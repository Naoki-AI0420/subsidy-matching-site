# 実装タスク分解 — 補助金・助成金マッチングサイト

## Phase 1: 仕様策定 ✅

- [x] requirements.md（機能要件・非機能要件）
- [x] design.md（技術設計・DB設計・画面遷移）
- [x] tasks.md（本ファイル）

## Phase 2: WordPress テーマ実装 ✅

### 2.1 テーマ基盤

- [x] `style.css` — テーマ情報 + 基本スタイル
- [x] `functions.php` — テーマ設定、スクリプト/スタイル読み込み
- [x] `header.php` — 共通ヘッダー（紺色背景、ロゴ、ナビ）
- [x] `footer.php` — 共通フッター（グレー背景、官公庁風）
- [x] `assets/css/common.css` — 共通スタイル（カラー、タイポグラフィ、レスポンシブ）

### 2.2 トップページ

- [x] `front-page.php` — ヒーローセクション + サービス説明 + CTA

### 2.3 一問一答マッチング

- [x] `page-matching.php` — マッチングページテンプレート
- [x] `assets/css/matching.css` — 一問一答スタイル
- [x] `assets/js/matching.js` — 質問遷移、進捗バー、回答保持、API呼び出し

### 2.4 結果画面

- [x] `assets/css/result.css` — 結果画面スタイル
- [x] 結果描画ロジック（matching.js 内）

### 2.5 お問い合わせページ

- [x] `page-contact.php` — フォームテンプレート
- [x] `assets/js/contact.js` — バリデーション

### 2.6 カスタム投稿タイプ + カスタムフィールド

- [x] `inc/custom-post-types.php` — CPT「subsidy」登録、カスタムフィールド（メタボックス）

### 2.7 REST API

- [x] `inc/rest-api.php` — マッチングエンドポイント、スコアリングロジック

### 2.8 リード管理

- [x] `inc/lead-manager.php` — wp_leads テーブル作成、保存処理
- [x] `inc/admin-menu.php` — 管理画面にリード一覧ページ追加

### 2.9 その他

- [x] `404.php` — 404ページ
- [x] `screenshot.png` — テーマスクリーンショット（プレースホルダー）

## Phase 3: Docker 構成 ✅

- [x] `docker-compose.yml` — WordPress + MySQL + phpMyAdmin
- [x] `.env.example` — 環境変数テンプレート
- [x] 動作確認

## Phase 4: Git ✅

- [x] git add → commit → push origin main
