#!/usr/bin/env python3
"""
補助金ポータル (hojyokin-portal.jp) スクレイパー

公募中・公募予定の補助金データを収集し data/subsidies.json に保存する。
実際のサイトHTMLクラス構造に対応。
rate limit: 1リクエスト/2秒
"""

import json
import os
import re
import sys
import time

import requests
from bs4 import BeautifulSoup

BASE_URL = "https://hojyokin-portal.jp"
SEARCH_URL = f"{BASE_URL}/subsidies/search"
COUNTS_API = f"{BASE_URL}/api/subsidy/counts"
OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "..", "..", "data")
OUTPUT_FILE = os.path.join(OUTPUT_DIR, "subsidies.json")
CHECKPOINT_FILE = os.path.join(OUTPUT_DIR, ".scraper_checkpoint.json")
RATE_LIMIT = 2

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
                  "AppleWebKit/537.36 (KHTML, like Gecko) "
                  "Chrome/120.0.0.0 Safari/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Language": "ja,en-US;q=0.7,en;q=0.3",
}

ITEMS_PER_PAGE = 10


def get_total_count(session):
    """APIから総件数を取得"""
    try:
        r = session.post(COUNTS_API, headers=HEADERS, timeout=30)
        data = r.json()
        return data.get("data", 0)
    except Exception as e:
        print(f"[WARN] 総件数取得失敗: {e}")
        return 0


def parse_card(card):
    """
    .c-subsidy__card から補助金データを抽出

    サイトの実際のHTML構造:
    - .c-subsidy-head__state: 公募ステータス
    - .c-subsidy__title: 補助金名
    - .c-subsidy__meta p: 地域、申請期間
    - .c-subsidy__price .num: 上限金額（万円単位）
    - .c-subsidy__text: 説明文
    - .c-button a: 詳細リンク
    """
    item = {}

    # ステータス
    state_el = card.select_one(".c-subsidy-head__state")
    if state_el:
        state_text = state_el.get_text(strip=True)
        item["status_text"] = state_text
        if "公募中" in state_text:
            item["status"] = "active"
        elif "公募予定" in state_text:
            item["status"] = "upcoming"
        elif "公募終了" in state_text:
            item["status"] = "closed"
        else:
            item["status"] = "unknown"

    # タイトル
    title_el = card.select_one(".c-subsidy__title")
    if title_el:
        item["title"] = title_el.get_text(strip=True)

    # メタ情報
    meta_els = card.select(".c-subsidy__meta p")
    metas = [m.get_text(strip=True) for m in meta_els if m.get_text(strip=True)]
    if metas:
        item["region"] = metas[0]
    for m in metas:
        if "申請期間" in m:
            item["application_period"] = m.replace("申請期間：", "").strip()

    # 上限金額
    num_el = card.select_one(".c-subsidy__price .num")
    if num_el:
        try:
            amount_manyen = int(num_el.get_text(strip=True).replace(",", ""))
            item["max_amount"] = amount_manyen * 10000  # 円換算
            item["amount_text"] = f"{amount_manyen}万円"
        except ValueError:
            item["amount_text"] = num_el.get_text(strip=True)
    suffix_el = card.select_one(".c-subsidy__price .suffix")
    if suffix_el:
        item["amount_suffix"] = suffix_el.get_text(strip=True)

    # 説明文
    text_el = card.select_one(".c-subsidy__text")
    if text_el:
        item["summary"] = text_el.get_text(strip=True)

    # 詳細URL・ID
    link_el = card.select_one(".c-button a[href]")
    if link_el:
        href = link_el.get("href", "")
        item["detail_url"] = href
        match = re.search(r"/subsidies/(\d+)", href)
        if match:
            item["portal_id"] = match.group(1)

    return item


def scrape_page(session, page_num):
    """1ページ分の補助金データを取得"""
    url = f"{SEARCH_URL}?page={page_num}"
    time.sleep(RATE_LIMIT)
    r = session.get(url, headers=HEADERS, timeout=30)
    r.raise_for_status()
    soup = BeautifulSoup(r.text, "html.parser")
    cards = soup.select(".c-subsidy__card")
    return [parse_card(c) for c in cards if c]


def load_checkpoint():
    """チェックポイントから再開情報を読み込む"""
    if os.path.exists(CHECKPOINT_FILE):
        try:
            with open(CHECKPOINT_FILE, "r", encoding="utf-8") as f:
                data = json.load(f)
            return data.get("last_page", 0), data.get("items", [])
        except (json.JSONDecodeError, KeyError):
            pass
    return 0, []


def save_checkpoint(page, items):
    """中間保存"""
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    with open(CHECKPOINT_FILE, "w", encoding="utf-8") as f:
        json.dump({"last_page": page, "count": len(items), "items": items},
                  f, ensure_ascii=False)


def save_results(items):
    """最終結果をJSONファイルに保存"""
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    output = {
        "source": "hojyokin-portal.jp",
        "scraped_at": time.strftime("%Y-%m-%dT%H:%M:%S+09:00"),
        "total_count": len(items),
        "items": items,
    }
    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(output, f, ensure_ascii=False, indent=2)
    print(f"\n[OK] {len(items):,}件を {OUTPUT_FILE} に保存しました")


def main():
    import argparse
    parser = argparse.ArgumentParser(description="補助金ポータル スクレイパー")
    parser.add_argument("--max-pages", type=int, default=0,
                        help="最大取得ページ数 (0=全ページ)")
    parser.add_argument("--resume", action="store_true",
                        help="チェックポイントから再開")
    args = parser.parse_args()

    session = requests.Session()

    # 総件数はサイト情報から約51,000件 → 5,200ページ
    total_pages = 5200
    print(f"[INFO] 補助金ポータル スクレイパー開始")
    print(f"[INFO] 推定 {total_pages:,} ページ", flush=True)

    if args.max_pages > 0:
        total_pages = min(total_pages, args.max_pages)
        print(f"[INFO] {args.max_pages} ページまで取得")

    # チェックポイント再開
    start_page = 0
    all_items = []
    if args.resume:
        start_page, all_items = load_checkpoint()
        if start_page > 0:
            print(f"[INFO] チェックポイントから再開: ページ {start_page + 1}, "
                  f"{len(all_items):,}件取得済み")

    seen_ids = {item.get("portal_id") for item in all_items if item.get("portal_id")}
    consecutive_errors = 0
    empty_pages = 0

    for page in range(start_page + 1, total_pages + 1):
        try:
            items = scrape_page(session, page)

            if not items:
                empty_pages += 1
                if empty_pages >= 3:
                    print(f"[INFO] 3ページ連続で結果なし — 終了")
                    break
                continue

            empty_pages = 0
            new_count = 0
            for item in items:
                pid = item.get("portal_id")
                if pid and pid not in seen_ids:
                    all_items.append(item)
                    seen_ids.add(pid)
                    new_count += 1
                elif not pid:
                    all_items.append(item)
                    new_count += 1

            if page % 10 == 0 or page <= 5:
                print(f"  [{page:,}/{total_pages:,}] +{new_count}件 "
                      f"(合計 {len(all_items):,}件)", flush=True)

            # 50ページごとにチェックポイント
            if page % 50 == 0:
                save_checkpoint(page, all_items)

            consecutive_errors = 0

        except KeyboardInterrupt:
            print(f"\n[中断] ページ {page} で中断。保存中...")
            save_checkpoint(page - 1, all_items)
            save_results(all_items)
            return

        except Exception as e:
            consecutive_errors += 1
            print(f"[ERROR] ページ {page}: {e}")
            if consecutive_errors >= 10:
                print(f"[ABORT] 10回連続エラー — 中断")
                break
            time.sleep(5)

    # タイトルが「詳細はこちら」のアイテムは詳細ページからタイトルを取得
    fix_count = 0
    needs_fix = [i for i, item in enumerate(all_items)
                 if item.get("title", "") in ("詳細はこちら", "")]
    if needs_fix:
        print(f"\n[INFO] {len(needs_fix)}件のタイトルを詳細ページから取得中...")
        for idx in needs_fix:
            item = all_items[idx]
            url = item.get("detail_url", "")
            if not url:
                continue
            try:
                time.sleep(RATE_LIMIT)
                r = session.get(url, headers=HEADERS, timeout=30)
                r.raise_for_status()
                soup = BeautifulSoup(r.text, "html.parser")
                # <title> タグまたは h1 からタイトルを取得
                h1 = soup.select_one("h1")
                if h1:
                    title = h1.get_text(strip=True)
                else:
                    title_tag = soup.find("title")
                    title = title_tag.get_text(strip=True) if title_tag else ""
                    # " | 補助金ポータル" を除去
                    title = re.sub(r"\s*[|｜]\s*補助金ポータル.*", "", title)
                if title and title != "詳細はこちら":
                    item["title"] = title
                    fix_count += 1
                if fix_count % 10 == 0 and fix_count > 0:
                    print(f"  タイトル取得: {fix_count}/{len(needs_fix)}")
            except Exception as e:
                print(f"  [WARN] タイトル取得失敗 {url}: {e}")

        print(f"  タイトル修正: {fix_count}/{len(needs_fix)}件")

    save_results(all_items)

    # チェックポイント削除
    if os.path.exists(CHECKPOINT_FILE):
        os.remove(CHECKPOINT_FILE)


if __name__ == "__main__":
    main()
