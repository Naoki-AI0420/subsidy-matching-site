# 技術設計書 — 補助金・助成金マッチングサイト

## 1. システム構成図

```
┌─────────────────────────────────────────────┐
│                  Nginx (リバースプロキシ)       │
└──────────────────┬──────────────────────────┘
                   │
┌──────────────────▼──────────────────────────┐
│            WordPress (PHP 8.x)              │
│  ┌────────────────────────────────────────┐  │
│  │  テーマ: subsidy-match                  │  │
│  │  ├── フロントページ                      │  │
│  │  ├── 一問一答ページ（Vanilla JS）         │  │
│  │  ├── 結果ページ                          │  │
│  │  └── お問い合わせページ                   │  │
│  ├────────────────────────────────────────┤  │
│  │  カスタム投稿タイプ: subsidy              │  │
│  │  カスタムテーブル: wp_leads              │  │
│  ├────────────────────────────────────────┤  │
│  │  REST API                              │  │
│  │  ├── GET  /wp-json/subsidy/v1/match    │  │
│  │  ├── POST /wp-json/subsidy/v1/leads    │  │
│  │  └── GET  /wp-json/wp/v2/subsidy       │  │
│  └────────────────────────────────────────┘  │
└──────────────────┬──────────────────────────┘
                   │
┌──────────────────▼──────────────────────────┐
│              MySQL 8.0                       │
│  ├── wp_posts (補助金データ)                  │
│  ├── wp_postmeta (カスタムフィールド)          │
│  └── wp_leads (リードデータ)                  │
└─────────────────────────────────────────────┘
```

## 2. DB設計

### 2.1 補助金データ（wp_posts + wp_postmeta）

WordPress 標準の投稿テーブルを利用。カスタムフィールドは `wp_postmeta` に格納。

| メタキー | 型 | 説明 |
|---|---|---|
| `_subsidy_max_amount` | int | 最大補助金額（円） |
| `_subsidy_rate` | string | 補助率（例: "1/2", "2/3"） |
| `_subsidy_target_industries` | serialized array | 対象業種コード一覧 |
| `_subsidy_target_regions` | serialized array | 対象地域（都道府県コード） |
| `_subsidy_target_employee_size` | serialized array | 対象従業員規模 |
| `_subsidy_target_capital` | serialized array | 対象資本金規模 |
| `_subsidy_target_challenges` | serialized array | 対象課題カテゴリ |
| `_subsidy_deadline` | date | 申請期限（YYYY-MM-DD） |
| `_subsidy_official_url` | url | 公式サイトURL |
| `_subsidy_summary` | text | 概要（200文字程度） |
| `_subsidy_match_priority` | int | 表示優先度（1-100） |

### 2.2 リードデータ（カスタムテーブル: wp_leads）

```sql
CREATE TABLE wp_leads (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(255) NOT NULL,
    prefecture      VARCHAR(10) NOT NULL,
    industry        VARCHAR(100) NOT NULL,
    employee_size   VARCHAR(20) NOT NULL,
    capital         VARCHAR(20) NOT NULL,
    challenges      TEXT NOT NULL,          -- JSON配列
    annual_revenue  VARCHAR(20) NOT NULL,
    has_experience  TINYINT(1) NOT NULL DEFAULT 0,
    matched_ids     TEXT,                   -- マッチした補助金IDのJSON配列
    ip_address      VARCHAR(45),
    user_agent      VARCHAR(500),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 3. マッチングロジック

### 3.1 スコアリング方式

各補助金に対して回答データとの一致度をスコアリングし、降順でソートする。

```
スコア = Σ (条件一致 × 重み)

条件と重み:
  - 地域一致:     20点（全国対象の場合も20点）
  - 業種一致:     25点
  - 従業員規模:   15点
  - 資本金:       15点
  - 課題一致:     25点（複数一致の場合は最大25点）
```

### 3.2 適合度バッジ

| スコア | バッジ |
|---|---|
| 70点以上 | 高 |
| 40〜69点 | 中 |
| 39点以下 | 低（非表示 or 薄く表示） |

### 3.3 処理フロー

```
1. ユーザーが全質問に回答
2. JavaScript が回答データを JSON にまとめる
3. REST API (POST /wp-json/subsidy/v1/match) に送信
4. PHP 側で全補助金データを取得し、スコアリング
5. スコア降順でソートし、結果を JSON で返却
6. 同時にリードデータを wp_leads に保存
7. JavaScript が結果画面を描画
```

## 4. 画面遷移図

```
トップページ (front-page.php)
  │
  ├──→ 一問一答ページ (page-matching.php)
  │      │
  │      ├── Q1 → Q2 → Q3 → Q4 → Q5 → Q6 → Q7 → Q8
  │      │   (JavaScript でクライアントサイド遷移)
  │      │
  │      └──→ 結果ページ (JavaScript で描画)
  │             │
  │             └──→ お問い合わせページ (page-contact.php)
  │
  ├──→ お問い合わせページ (page-contact.php)
  │
  └──→ 補助金詳細（外部サイトへリンク）
```

## 5. テーマ構成（subsidy-match）

```
subsidy-match/
├── style.css                  # テーマ情報 + 基本スタイル
├── functions.php              # テーマ設定・CPT登録・REST API
├── header.php                 # ヘッダー（紺色背景）
├── footer.php                 # フッター（グレー背景）
├── front-page.php             # トップページ
├── page-matching.php          # 一問一答ページ
├── page-contact.php           # お問い合わせページ
├── single-subsidy.php         # 補助金個別（将来用）
├── 404.php                    # 404ページ
├── screenshot.png             # テーマスクリーンショット
├── assets/
│   ├── css/
│   │   ├── common.css         # 共通スタイル
│   │   ├── matching.css       # 一問一答スタイル
│   │   └── result.css         # 結果画面スタイル
│   ├── js/
│   │   ├── matching.js        # 一問一答ロジック
│   │   └── contact.js         # フォームバリデーション
│   └── images/
│       └── logo.svg           # ロゴ
└── inc/
    ├── custom-post-types.php  # カスタム投稿タイプ定義
    ├── rest-api.php           # REST API エンドポイント
    ├── lead-manager.php       # リード管理
    └── admin-menu.php         # 管理画面カスタマイズ
```

## 6. デザインシステム

### 6.1 カラーパレット

| 用途 | カラー | コード |
|---|---|---|
| プライマリ（紺） | ██ | `#003366` |
| プライマリ（濃紺） | ██ | `#002244` |
| アクセント | ██ | `#0056b3` |
| テキスト | ██ | `#333333` |
| サブテキスト | ██ | `#666666` |
| 背景（白） | ██ | `#FFFFFF` |
| 背景（薄灰） | ██ | `#F5F5F5` |
| 背景（灰） | ██ | `#E8E8E8` |
| ボーダー | ██ | `#CCCCCC` |
| 適合度・高 | ██ | `#2E7D32` |
| 適合度・中 | ██ | `#F9A825` |
| 適合度・低 | ██ | `#757575` |

### 6.2 タイポグラフィ

```css
font-family: 'Noto Sans JP', '游ゴシック', 'Yu Gothic', sans-serif;

/* 見出し */
h1: 28px / bold
h2: 24px / bold
h3: 20px / medium

/* 本文 */
body: 16px / regular / line-height: 1.8
small: 14px / regular

/* 一問一答 質問文 */
.question-text: 22px / medium
```

### 6.3 コンポーネント

```css
/* ボタン */
.btn-primary {
    background: #003366;
    color: #FFFFFF;
    border-radius: 4px;
    padding: 12px 32px;
    font-size: 16px;
}

/* カード */
.card {
    background: #FFFFFF;
    border: 1px solid #CCCCCC;
    border-radius: 4px;
    padding: 24px;
}

/* 進捗バー */
.progress-bar {
    background: linear-gradient(90deg, #003366, #0056b3);
    height: 8px;
    border-radius: 4px;
}
```

## 7. REST API 設計

### GET /wp-json/subsidy/v1/match

マッチング実行。クエリパラメータで回答データを受け取り、スコアリング結果を返す。

**Request:**
```json
POST /wp-json/subsidy/v1/match
{
    "prefecture": "13",
    "industry": "information_technology",
    "employee_size": "6-20",
    "capital": "1000-3000",
    "challenges": ["it_dx", "equipment"],
    "annual_revenue": "5000-10000",
    "has_experience": false,
    "email": "tanaka@example.com"
}
```

**Response:**
```json
{
    "success": true,
    "results": [
        {
            "id": 123,
            "title": "IT導入補助金",
            "max_amount": 4500000,
            "rate": "1/2〜3/4",
            "summary": "ITツール導入費用を補助...",
            "deadline": "2026-06-30",
            "official_url": "https://...",
            "score": 85,
            "match_level": "high"
        }
    ],
    "lead_id": 456
}
```

### POST /wp-json/subsidy/v1/leads

リード保存（マッチング時に自動保存されるため、通常は直接呼ばない）。

### GET /wp-json/wp/v2/subsidy

WordPress 標準の REST API で補助金一覧取得。
