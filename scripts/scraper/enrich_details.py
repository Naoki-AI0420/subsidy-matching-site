#!/usr/bin/env python3
"""詳細ページから対象事業者・目的・対象経費・関連タグを取得して構造化"""

import json
import os
import time
import requests
from bs4 import BeautifulSoup

DATA_DIR = os.path.join(os.path.dirname(__file__), "..", "..", "data")
INPUT_FILE = os.path.join(DATA_DIR, "subsidies.json")
CHECKPOINT = os.path.join(DATA_DIR, ".enrich_checkpoint.json")
RATE_LIMIT = 2

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
}


def extract_details(session, url):
    """詳細ページからテーブル情報を抽出"""
    try:
        r = session.get(url, headers=HEADERS, timeout=30)
        r.raise_for_status()
        soup = BeautifulSoup(r.text, "html.parser")

        result = {}
        for row in soup.select("tr"):
            th = row.select_one("th")
            td = row.select_one("td")
            if not th or not td:
                continue
            label = th.get_text(strip=True)
            value = td.get_text(strip=True)

            if label == "対象事業者":
                result["eligible_entities"] = value
            elif label == "目的":
                result["purpose"] = value
            elif label == "対象経費":
                result["eligible_expenses"] = value
            elif label == "補助率":
                result["subsidy_rate_detail"] = value
            elif label == "実施機関":
                result["implementing_agency"] = value
            elif label == "関連タグ":
                # #タグ1#タグ2 → リストに変換
                tags = [t.strip() for t in value.split("#") if t.strip()]
                result["tags"] = tags
            elif label == "公式公募ページ":
                official = value.replace("※終了している可能性がありますので、実施機関にご確認ください。", "").strip()
                result["official_url"] = official

        return result
    except Exception as e:
        return {"error": str(e)}


def main():
    print("[INFO] 詳細ページ構造化スクレイピング開始", flush=True)

    with open(INPUT_FILE, "r", encoding="utf-8") as f:
        data = json.load(f)

    items = data["items"]

    # 公募中+公募予定だけ対象
    targets = [(idx, item) for idx, item in enumerate(items)
               if item.get("status") in ("active", "upcoming")]
    print(f"[INFO] 対象: {len(targets):,}件 (公募中+公募予定)", flush=True)

    # チェックポイント
    start_idx = 0
    if os.path.exists(CHECKPOINT):
        try:
            with open(CHECKPOINT, "r") as f:
                cp = json.load(f)
            start_idx = cp.get("processed", 0)
            print(f"[INFO] チェックポイント再開: {start_idx}件処理済み", flush=True)
        except:
            pass

    session = requests.Session()
    enriched = 0
    errors = 0

    for i, (idx, item) in enumerate(targets):
        if i < start_idx:
            continue

        url = item.get("detail_url", "")
        if not url:
            continue

        time.sleep(RATE_LIMIT)
        result = extract_details(session, url)

        if "error" in result:
            errors += 1
        else:
            for key, value in result.items():
                items[idx][key] = value
            if result:
                enriched += 1

        if (i + 1) % 10 == 0 or i < 5:
            print(f"  [{i+1:,}/{len(targets):,}] 構造化: {enriched:,}件 / エラー: {errors:,}件", flush=True)

        # 100件ごとに保存
        if (i + 1) % 100 == 0:
            with open(CHECKPOINT, "w") as f:
                json.dump({"processed": i + 1, "enriched": enriched}, f)
            data["items"] = items
            data["enrich_updated"] = time.strftime("%Y-%m-%dT%H:%M:%S+09:00")
            with open(INPUT_FILE, "w", encoding="utf-8") as f:
                json.dump(data, f, ensure_ascii=False, indent=2)
            print(f"  [SAVE] 中間保存完了", flush=True)

    # 最終保存
    data["items"] = items
    data["enrich_updated"] = time.strftime("%Y-%m-%dT%H:%M:%S+09:00")
    with open(INPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    if os.path.exists(CHECKPOINT):
        os.remove(CHECKPOINT)

    print(f"\n[完了] {enriched:,}/{len(targets):,}件の詳細データを取得しました", flush=True)


if __name__ == "__main__":
    main()
