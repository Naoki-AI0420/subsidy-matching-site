#!/usr/bin/env python3
"""金額欠損データの詳細ページから金額・補助率を補完するスクリプト"""

import json
import os
import re
import sys
import time

import requests
from bs4 import BeautifulSoup

DATA_DIR = os.path.join(os.path.dirname(__file__), "..", "..", "data")
INPUT_FILE = os.path.join(DATA_DIR, "subsidies.json")
OUTPUT_FILE = os.path.join(DATA_DIR, "subsidies.json")
CHECKPOINT = os.path.join(DATA_DIR, ".fill_checkpoint.json")
RATE_LIMIT = 2

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
}


def extract_amount_from_detail(session, url):
    """詳細ページから金額と補助率を抽出"""
    try:
        r = session.get(url, headers=HEADERS, timeout=30)
        r.raise_for_status()
        soup = BeautifulSoup(r.text, "html.parser")

        result = {}

        # テーブルから補助率を取得
        for row in soup.select("tr"):
            text = row.get_text(strip=True)
            if "補助率" in text and "上限" not in text:
                dd = row.select_one("td")
                if dd:
                    result["subsidy_rate"] = dd.get_text(strip=True)

        # 本文から金額パターンを抽出
        body = soup.get_text()
        # 大きい金額を優先的に探す
        amounts_yen = re.findall(r'([\d,]+)万円', body)
        if amounts_yen:
            # 最大金額を上限と推定
            max_amount = max(int(a.replace(',', '')) for a in amounts_yen)
            result["max_amount"] = max_amount * 10000
            result["amount_text"] = f"{max_amount}万円"

        return result
    except Exception as e:
        print(f"  [ERROR] {url}: {e}", flush=True)
        return {}


def main():
    print("[INFO] 金額欠損データ補完開始", flush=True)

    with open(INPUT_FILE, "r", encoding="utf-8") as f:
        data = json.load(f)

    items = data["items"]
    missing = [(idx, item) for idx, item in enumerate(items) if not item.get("max_amount")]
    print(f"[INFO] 金額欠損: {len(missing):,}件", flush=True)

    # チェックポイント読み込み
    start_idx = 0
    if os.path.exists(CHECKPOINT):
        try:
            with open(CHECKPOINT, "r") as f:
                cp = json.load(f)
            start_idx = cp.get("processed", 0)
            print(f"[INFO] チェックポイントから再開: {start_idx}件処理済み", flush=True)
        except:
            pass

    session = requests.Session()
    filled = 0
    errors = 0

    for i, (idx, item) in enumerate(missing):
        if i < start_idx:
            continue

        url = item.get("detail_url", "")
        if not url:
            continue

        time.sleep(RATE_LIMIT)
        result = extract_amount_from_detail(session, url)

        if result.get("max_amount"):
            items[idx]["max_amount"] = result["max_amount"]
            items[idx]["amount_text"] = result.get("amount_text", "")
            filled += 1
        if result.get("subsidy_rate"):
            items[idx]["subsidy_rate"] = result["subsidy_rate"]

        if not result:
            errors += 1

        if (i + 1) % 10 == 0 or i < 5:
            print(f"  [{i+1:,}/{len(missing):,}] 補完: {filled:,}件 / エラー: {errors:,}件", flush=True)

        # 100件ごとにチェックポイント
        if (i + 1) % 100 == 0:
            with open(CHECKPOINT, "w") as f:
                json.dump({"processed": i + 1, "filled": filled}, f)
            # 中間保存
            data["items"] = items
            data["total_count"] = len(items)
            data["last_fill_update"] = time.strftime("%Y-%m-%dT%H:%M:%S+09:00")
            with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
                json.dump(data, f, ensure_ascii=False, indent=2)
            print(f"  [SAVE] 中間保存完了", flush=True)

    # 最終保存
    data["items"] = items
    data["total_count"] = len(items)
    data["last_fill_update"] = time.strftime("%Y-%m-%dT%H:%M:%S+09:00")
    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    # チェックポイント削除
    if os.path.exists(CHECKPOINT):
        os.remove(CHECKPOINT)

    print(f"\n[完了] {filled:,}/{len(missing):,}件の金額を補完しました", flush=True)


if __name__ == "__main__":
    main()
