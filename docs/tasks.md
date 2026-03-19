# 実装タスク分解 — 補助金・助成金マッチングサイト

## Phase 1: 仕様策定 ✅

- [x] requirements.md（機能要件・非機能要件）
- [x] design.md（技術設計・DB設計・画面遷移）
- [x] tasks.md（本ファイル）

## Phase 2: WordPress テーマ実装

### 2.1 テーマ基盤

- [ ] `style.css` — テーマ情報 + 基本スタイル
- [ ] `functions.php` — テーマ設定、スクリプト/スタイル読み込み
- [ ] `header.php` — 共通ヘッダー（紺色背景、ロゴ、ナビ）
- [ ] `footer.php` — 共通フッター（グレー背景、官公庁風）
- [ ] `assets/css/common.css` — 共通スタイル（カラー、タイポグラフィ、レスポンシブ）

### 2.2 トップページ

- [ ] `front-page.php` — ヒーローセクション + サービス説明 + CTA

### 2.3 一問一答マッチング

- [ ] `page-matching.php` — マッチングページテンプレート
- [ ] `assets/css/matching.css` — 一問一答スタイル
- [ ] `assets/js/matching.js` — 質問遷移、進捗バー、回答保持、API呼び出し

### 2.4 結果画面

- [ ] `assets/css/result.css` — 結果画面スタイル
- [ ] 結果描画ロジック（matching.js 内）

### 2.5 お問い合わせページ

- [ ] `page-contact.php` — フォームテンプレート
- [ ] `assets/js/contact.js` — バリデーション

### 2.6 カスタム投稿タイプ + カスタムフィールド

- [ ] `inc/custom-post-types.php` — CPT「subsidy」登録、カスタムフィールド（メタボックス）

### 2.7 REST API

- [ ] `inc/rest-api.php` — マッチングエンドポイント、スコアリングロジック

### 2.8 リード管理

- [ ] `inc/lead-manager.php` — wp_leads テーブル作成、保存処理
- [ ] `inc/admin-menu.php` — 管理画面にリード一覧ページ追加

### 2.9 その他

- [ ] `404.php` — 404ページ
- [ ] `screenshot.png` — テーマスクリーンショット（プレースホルダー）

## Phase 3: Docker 構成

- [ ] `docker-compose.yml` — WordPress + MySQL + phpMyAdmin
- [ ] `.env.example` — 環境変数テンプレート
- [ ] 動作確認

## Phase 4: Git

- [ ] git add → commit → push origin main
