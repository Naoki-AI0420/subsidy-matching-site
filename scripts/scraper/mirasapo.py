#!/usr/bin/env python3
"""
ミラサポplus (mirasapo-plus.go.jp) スクレイパー

主要補助金10種の詳細データを取得し data/mirasapo-subsidies.json に保存する。
rate limit: 1リクエスト/2秒
"""

import json
import os
import re
import sys
import time

import requests
from bs4 import BeautifulSoup

BASE_URL = "https://mirasapo-plus.go.jp"
OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "..", "..", "data")
OUTPUT_FILE = os.path.join(OUTPUT_DIR, "mirasapo-subsidies.json")
RATE_LIMIT = 2

HEADERS = {
    "User-Agent": "SubsidyMatchBot/1.0 (educational research)",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
    "Accept-Language": "ja,en;q=0.5",
}

# 主要補助金10種のURLとメタ情報
SUBSIDIES = [
    {
        "id": "shoryokuka",
        "name": "省力化投資補助金",
        "url": f"{BASE_URL}/subsidy/shoryokuka/",
        "max_amount": 100_000_000,
        "rate": "1/2～2/3",
        "target_challenges": ["equipment"],
        "target_industries": [],  # 全業種
        "summary": "人手不足解消のための省力化設備・システム投資を支援。カタログ注文型と一般型の2種類。",
    },
    {
        "id": "manufacturing",
        "name": "ものづくり補助金",
        "url": f"{BASE_URL}/subsidy/manufacturing",
        "max_amount": 40_000_000,
        "rate": "1/2～2/3",
        "target_challenges": ["equipment", "rnd"],
        "target_industries": ["manufacturing", "construction", "information_technology",
                               "professional_services"],
        "summary": "新製品・サービス開発や海外展開のための設備投資を支援。製品・サービス高付加価値化枠とグローバル枠。",
    },
    {
        "id": "ithojo",
        "name": "デジタル化・AI導入補助金（旧IT導入補助金）",
        "url": f"{BASE_URL}/subsidy/ithojo",
        "max_amount": 4_500_000,
        "rate": "1/2～4/5",
        "target_challenges": ["it_dx"],
        "target_industries": [],
        "summary": "ITツール・AI導入による生産性向上・DX推進を支援。通常枠、インボイス枠、セキュリティ枠等。",
    },
    {
        "id": "jizokuka",
        "name": "小規模事業者持続化補助金",
        "url": f"{BASE_URL}/subsidy/jizokuka/",
        "max_amount": 2_500_000,
        "rate": "2/3",
        "target_challenges": ["equipment", "it_dx"],
        "target_industries": [],
        "target_employee_size": ["1-5", "6-20"],
        "summary": "販路開拓・業務効率化を支援。経営計画を商工会議所等と策定して申請。一般型・災害支援枠・創業型。",
    },
    {
        "id": "syokei",
        "name": "事業承継・M&A補助金",
        "url": f"{BASE_URL}/subsidy/syokei/",
        "max_amount": 20_000_000,
        "rate": "1/3～2/3",
        "target_challenges": ["succession"],
        "target_industries": [],
        "summary": "事業承継・M&Aに伴う設備投資や専門家活用費用を支援。承継促進枠・専門家活用枠・PMI推進枠。",
    },
    {
        "id": "shinjigyou",
        "name": "新事業進出補助金",
        "url": f"{BASE_URL}/subsidy/shinjigyou/",
        "max_amount": 90_000_000,
        "rate": "1/2",
        "target_challenges": ["equipment", "rnd"],
        "target_industries": [],
        "summary": "既存事業と異なる新分野への大胆な挑戦を支援。設備・システム構築・建物費等が対象。",
    },
    {
        "id": "seityo",
        "name": "成長加速化補助金",
        "url": f"{BASE_URL}/subsidy/seityo/",
        "max_amount": 500_000_000,
        "rate": "1/2",
        "target_challenges": ["equipment", "overseas"],
        "target_industries": [],
        "summary": "100億企業を目指す成長志向の中小企業の大規模投資を支援。工場新設・設備増強等。",
    },
    {
        "id": "shoene",
        "name": "省エネ補助金",
        "url": "https://syouenehojyokin.sii.or.jp/",
        "max_amount": 100_000_000,
        "rate": "1/3～1/2",
        "target_challenges": ["equipment"],
        "target_industries": [],
        "summary": "省エネルギー設備への更新や運用改善を支援。高効率空調・照明LED・ボイラー等の設備導入。",
    },
    {
        "id": "koyou_chosei",
        "name": "雇用調整助成金",
        "url": f"{BASE_URL}/subsidy/koyouchousei/",
        "max_amount": 0,  # 日額制
        "rate": "2/3～3/4",
        "target_challenges": ["hiring"],
        "target_industries": [],
        "summary": "経済的理由で事業縮小した企業の休業手当・教育訓練費・出向費用を助成。日額上限あり。",
        "amount_note": "1人1日あたり上限8,490円",
    },
    {
        "id": "gyoumu_kaizen",
        "name": "業務改善助成金",
        "url": f"{BASE_URL}/subsidy/gyoumukaizen/",
        "max_amount": 6_000_000,
        "rate": "3/4～9/10",
        "target_challenges": ["equipment", "hiring"],
        "target_industries": [],
        "summary": "事業場内最低賃金引き上げと設備投資を一体的に支援。最低賃金引上げ額に応じて助成。",
    },
]


def fetch_page(url):
    """ページ取得（rate limit 付き）"""
    time.sleep(RATE_LIMIT)
    try:
        resp = requests.get(url, headers=HEADERS, timeout=30)
        resp.raise_for_status()
        return resp.text
    except requests.RequestException as e:
        print(f"  [ERROR] {url}: {e}", file=sys.stderr)
        return None


def parse_amount(text):
    """金額テキストを数値（円）に変換"""
    text = text.replace(",", "").replace(" ", "").replace("　", "")
    match = re.search(r"([\d.]+)\s*億円", text)
    if match:
        return int(float(match.group(1)) * 100_000_000)
    match = re.search(r"([\d.]+)\s*万円", text)
    if match:
        return int(float(match.group(1)) * 10_000)
    match = re.search(r"([\d,]+)\s*円", text)
    if match:
        return int(match.group(1).replace(",", ""))
    return 0


def scrape_detail(subsidy):
    """個別補助金の詳細ページをスクレイピング"""
    url = subsidy["url"]
    print(f"  取得中: {subsidy['name']} ({url})")

    html = fetch_page(url)
    if not html:
        return subsidy

    soup = BeautifulSoup(html, "lxml")
    body_text = soup.get_text(" ", strip=True)

    # 金額情報の更新
    amount_match = re.search(r"(?:最大|上限)[：:\s]*([\d,]+\s*[万億]?\s*円)", body_text)
    if amount_match:
        parsed = parse_amount(amount_match.group(1))
        if parsed > 0:
            subsidy["max_amount_scraped"] = parsed
            subsidy["amount_text"] = amount_match.group(1).strip()

    # 補助率
    rate_match = re.search(r"(?:補助率|助成率)[：:\s]*([0-9/～〜\-・]+)", body_text)
    if rate_match:
        subsidy["rate_scraped"] = rate_match.group(1)

    # 申請期間
    period_match = re.search(
        r"(?:公募期間|申請期間|受付期間)[：:\s]*(\d{4}[年/]\d{1,2}[月/]\d{1,2}日?)\s*[～〜\-]\s*(\d{4}[年/]?\d{1,2}[月/]\d{1,2}日?)",
        body_text
    )
    if period_match:
        subsidy["application_start"] = period_match.group(1)
        subsidy["application_end"] = period_match.group(2)

    # 対象者
    target_match = re.search(
        r"(?:対象者|対象となる方|申請対象)[：:\s]*(.{10,150}?)(?:[。\n])",
        body_text
    )
    if target_match:
        subsidy["target_description"] = target_match.group(1).strip()

    # 枠・類型情報
    categories = []
    for heading in soup.find_all(["h2", "h3", "h4"]):
        text = heading.get_text(strip=True)
        if "枠" in text or "型" in text or "類型" in text:
            categories.append(text)
    if categories:
        subsidy["categories"] = categories

    # ページのmeta description
    meta = soup.find("meta", attrs={"name": "description"})
    if meta and meta.get("content"):
        subsidy["meta_description"] = meta["content"][:300]

    return subsidy


def scrape_all():
    """全補助金の詳細を取得"""
    print("ミラサポplus スクレイパー開始")
    print(f"対象: {len(SUBSIDIES)} 件の主要補助金\n")

    results = []
    for subsidy in SUBSIDIES:
        result = scrape_detail(subsidy.copy())
        results.append(result)

    return results


def save_results(items):
    """結果をJSONファイルに保存"""
    os.makedirs(OUTPUT_DIR, exist_ok=True)

    output = {
        "source": "mirasapo-plus.go.jp",
        "scraped_at": time.strftime("%Y-%m-%dT%H:%M:%S+09:00"),
        "total_count": len(items),
        "items": items,
    }

    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(output, f, ensure_ascii=False, indent=2)

    print(f"\n結果を {OUTPUT_FILE} に保存しました（{len(items)} 件）")


def main():
    results = scrape_all()
    if results:
        save_results(results)
    else:
        print("データが取得できませんでした。")
        sys.exit(1)


if __name__ == "__main__":
    main()
