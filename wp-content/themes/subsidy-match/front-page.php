<?php
/**
 * Template Name: トップページ
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main>
    <!-- ヒーローセクション -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <p class="hero-label">中小企業・小規模事業者向け</p>
                <h1 class="hero-title">あなたの会社で活用できる<br>補助金・助成金を探しましょう</h1>
                <p class="hero-description">
                    簡単な質問に答えるだけで、貴社に該当する可能性のある<br>
                    補助金・助成金をご案内いたします。
                </p>
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    無料で補助金診断を始める
                </a>
                <p class="hero-note">※ 所要時間：約2分 ／ 登録不要でご利用いただけます</p>
            </div>
        </div>
    </section>

    <!-- 数字で示す実績セクション -->
    <section class="section-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number" data-target="49000">0</span><span class="stat-unit">件以上</span>
                    <p class="stat-label">補助金データ収録数</p>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="47">0</span><span class="stat-unit">都道府県</span>
                    <p class="stat-label">全国すべての地域に対応</p>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="3">0</span><span class="stat-unit">〜4兆円</span>
                    <p class="stat-label">年間予算総額（国・自治体合計）</p>
                </div>
            </div>
        </div>
    </section>

    <!-- こんなお悩みありませんか？ -->
    <section class="section section-worries">
        <div class="container">
            <h2 class="section-title">こんなお悩みありませんか？</h2>
            <div class="worries-grid">
                <div class="worry-item">
                    <p>うちの会社でも補助金がもらえるの？</p>
                </div>
                <div class="worry-item">
                    <p>補助金の種類が多すぎて何が使えるかわからない</p>
                </div>
                <div class="worry-item">
                    <p>申請が面倒で諦めてしまった</p>
                </div>
                <div class="worry-item">
                    <p>システム導入したいけど予算がない</p>
                </div>
            </div>
            <div class="text-center mt-32">
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    そのお悩み、1分の無料診断で解決します
                </a>
            </div>
        </div>
    </section>

    <!-- ステップ説明 -->
    <section class="section section-gray">
        <div class="container">
            <h2 class="section-title">ご利用の流れ</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3 class="step-title">質問に回答</h3>
                    <p class="step-desc">会社の業種や規模など、<br>簡単な質問にお答えください。</p>
                </div>
                <div class="step-arrow">▶</div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3 class="step-title">診断結果を確認</h3>
                    <p class="step-desc">該当する補助金・助成金を<br>一覧でご確認いただけます。</p>
                </div>
                <div class="step-arrow">▶</div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3 class="step-title">専門家に相談</h3>
                    <p class="step-desc">申請に関するご相談も<br>無料で承っております。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 特徴 -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">本サービスの特徴</h2>
            <div class="features">
                <div class="feature card card-bordered">
                    <h3>全国の補助金・助成金を網羅</h3>
                    <p>経済産業省、厚生労働省、各自治体が実施する補助金・助成金情報を幅広く掲載。定期的に情報を更新しております。</p>
                </div>
                <div class="feature card card-bordered">
                    <h3>簡単な質問で自動マッチング</h3>
                    <p>業種・地域・企業規模などの基本情報をご入力いただくだけで、該当する可能性のある補助金を自動的に抽出いたします。</p>
                </div>
                <div class="feature card card-bordered">
                    <h3>申請サポートも対応</h3>
                    <p>「申請の方法がわからない」「書類作成が不安」など、補助金申請に関するお悩みは専門スタッフが無料でサポートいたします。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 導入事例セクション -->
    <section class="section section-gray section-cases">
        <div class="container">
            <h2 class="section-title">導入事例</h2>
            <div class="cases-grid">
                <div class="case-card">
                    <div class="case-header">
                        <span class="case-industry">製造業</span>
                        <span class="case-subsidy">ものづくり補助金</span>
                    </div>
                    <div class="case-company">A社（従業員15名・宮城県）</div>
                    <div class="case-amount"><span class="case-amount-number">1,000</span>万円</div>
                    <p class="case-desc">生産管理システムを導入し、生産効率<strong>30%向上</strong>を達成。手作業だった工程管理をデジタル化し、納期遵守率も大幅に改善。</p>
                </div>
                <div class="case-card">
                    <div class="case-header">
                        <span class="case-industry">飲食業</span>
                        <span class="case-subsidy">IT導入補助金</span>
                    </div>
                    <div class="case-company">B社（従業員8名・東京都）</div>
                    <div class="case-amount"><span class="case-amount-number">150</span>万円</div>
                    <p class="case-desc">予約管理＋POSシステムを導入し、売上<strong>15%向上</strong>。予約の取りこぼしが減り、顧客データの活用でリピート率も改善。</p>
                </div>
                <div class="case-card">
                    <div class="case-header">
                        <span class="case-industry">小売業</span>
                        <span class="case-subsidy">持続化補助金</span>
                    </div>
                    <div class="case-company">C社（従業員3名・大阪府）</div>
                    <div class="case-amount"><span class="case-amount-number">100</span>万円</div>
                    <p class="case-desc">ECサイトを構築し、オンライン売上が月商の<strong>40%</strong>を占めるまでに成長。店舗に来られない遠方のお客様にも販路を拡大。</p>
                </div>
            </div>
            <p class="cases-note">※ 導入事例はイメージです</p>
        </div>
    </section>

    <!-- 補助金の仕組み解説セクション -->
    <section class="section section-mechanism">
        <div class="container">
            <h2 class="section-title">補助金の仕組み</h2>
            <p class="mechanism-lead">補助金とは、国や自治体から支給される<strong>返済不要</strong>の資金です。<br>事業に必要な費用の一部を補助金でまかなうことができます。</p>
            <div class="mechanism-chart">
                <div class="mechanism-example">
                    <p class="mechanism-example-title">例：開発費 500万円の場合（補助率1/2）</p>
                    <div class="mechanism-bars">
                        <div class="mechanism-bar-row">
                            <span class="mechanism-bar-label">開発費総額</span>
                            <div class="mechanism-bar mechanism-bar-total">
                                <span>500万円</span>
                            </div>
                        </div>
                        <div class="mechanism-bar-row">
                            <span class="mechanism-bar-label">補助金</span>
                            <div class="mechanism-bar mechanism-bar-subsidy">
                                <span>250万円</span>
                            </div>
                        </div>
                        <div class="mechanism-bar-row">
                            <span class="mechanism-bar-label">実質負担</span>
                            <div class="mechanism-bar mechanism-bar-burden">
                                <span>250万円</span>
                            </div>
                        </div>
                    </div>
                    <p class="mechanism-highlight">半額の負担でシステム開発が可能に</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ セクション -->
    <section class="section section-gray section-faq">
        <div class="container">
            <h2 class="section-title">よくあるご質問</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question" type="button" aria-expanded="false">
                        <span>本当に無料で診断できますか？</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>はい、補助金診断は完全無料でご利用いただけます。会員登録やクレジットカードの登録も一切不要です。お気軽にお試しください。</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" type="button" aria-expanded="false">
                        <span>個人情報は安全ですか？</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>診断にあたって個人情報のご入力は不要です。業種や地域などの基本的な事業情報のみで診断が可能です。お問い合わせ時にご入力いただく情報は、SSL通信により暗号化して送信されます。</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" type="button" aria-expanded="false">
                        <span>診断結果は正確ですか？</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>当サービスは公的機関が公開する最新の補助金情報をもとにマッチングを行っております。ただし、最終的な申請要件の確認は各補助金の公募要領をご参照ください。</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" type="button" aria-expanded="false">
                        <span>申請のサポートもしてもらえますか？</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>はい、補助金申請に精通した専門スタッフが、申請書類の作成から提出までサポートいたします。まずはお気軽にお問い合わせください。</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" type="button" aria-expanded="false">
                        <span>どのくらいの時間がかかりますか？</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>オンライン診断は約2分で完了します。診断後、より詳しいご相談をご希望の場合は、お問い合わせフォームよりご連絡ください。通常1〜2営業日以内にご返信いたします。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section">
        <div class="container text-center">
            <h2 class="section-title">まずは無料診断をお試しください</h2>
            <p class="text-muted mb-24">約2分の簡単な質問で、貴社に該当する補助金・助成金がわかります。</p>
            <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                補助金診断を始める
            </a>
        </div>
    </section>

    <!-- 運営情報セクション -->
    <section class="section section-gray section-company">
        <div class="container">
            <h2 class="section-title">運営情報</h2>
            <table class="company-table">
                <tbody>
                    <tr>
                        <th>運営会社</th>
                        <td>Growing up AI Inc.</td>
                    </tr>
                    <tr>
                        <th>お問い合わせ</th>
                        <td><a href="mailto:info@yumeno-marketing.jp">info@yumeno-marketing.jp</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<style>
/* Hero */
.hero {
    background-color: #003366;
    color: #FFFFFF;
    padding: 80px 0;
    text-align: center;
}

.hero-label {
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 2px;
    margin-bottom: 16px;
    opacity: 0.9;
}

.hero-title {
    font-size: 32px;
    font-weight: 700;
    color: #FFFFFF;
    margin-bottom: 20px;
    line-height: 1.5;
}

.hero-description {
    font-size: 16px;
    margin-bottom: 32px;
    opacity: 0.9;
    line-height: 1.8;
}

.hero .btn-primary {
    background-color: #FFFFFF;
    color: #003366;
    font-weight: 700;
    font-size: 18px;
    padding: 16px 48px;
}

.hero .btn-primary:hover {
    background-color: #F5F5F5;
}

.hero-note {
    font-size: 13px;
    margin-top: 12px;
    opacity: 0.7;
    margin-bottom: 0;
}

/* Stats */
.section-stats {
    background-color: #1a3353;
    padding: 64px 0;
}

.stats-grid {
    display: flex;
    justify-content: center;
    gap: 60px;
}

.stat-item {
    text-align: center;
    color: #FFFFFF;
}

.stat-number {
    font-size: 56px;
    font-weight: 700;
    line-height: 1.2;
    display: inline;
}

.stat-unit {
    font-size: 18px;
    font-weight: 500;
    opacity: 0.9;
}

.stat-label {
    font-size: 14px;
    margin-top: 8px;
    margin-bottom: 0;
    opacity: 0.8;
}

/* Worries */
.section-worries {
    padding: 80px 0;
}

.worries-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    max-width: 800px;
    margin: 0 auto;
}

.worry-item {
    background: #FFFFFF;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 24px 24px 24px 52px;
    position: relative;
}

.worry-item::before {
    content: '\2713';
    position: absolute;
    left: 20px;
    top: 24px;
    width: 22px;
    height: 22px;
    background-color: #003366;
    color: #FFFFFF;
    border-radius: 50%;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.worry-item p {
    margin: 0;
    font-size: 15px;
    font-weight: 500;
    color: #333333;
}

/* Steps */
.steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
}

.step {
    text-align: center;
    flex: 1;
    max-width: 260px;
    background: #FFFFFF;
    border: 1px solid #CCCCCC;
    border-radius: 4px;
    padding: 32px 24px;
}

.step-number {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background-color: #003366;
    color: #FFFFFF;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.step-title {
    font-size: 18px;
    color: #003366;
    margin-bottom: 8px;
}

.step-desc {
    font-size: 14px;
    color: #666666;
    margin-bottom: 0;
}

.step-arrow {
    color: #CCCCCC;
    font-size: 20px;
}

/* Features */
.features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.feature h3 {
    font-size: 18px;
    margin-bottom: 12px;
}

.feature p {
    font-size: 14px;
    color: #666666;
    margin-bottom: 0;
}

/* Cases */
.section-cases {
    padding: 80px 0;
}

.cases-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.case-card {
    background: #FFFFFF;
    border: 1px solid #E0E0E0;
    border-left: 4px solid #003366;
    border-radius: 4px;
    padding: 28px 24px;
}

.case-header {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}

.case-industry {
    display: inline-block;
    background-color: #003366;
    color: #FFFFFF;
    font-size: 12px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 2px;
}

.case-subsidy {
    display: inline-block;
    background-color: #E8F0F8;
    color: #003366;
    font-size: 12px;
    font-weight: 500;
    padding: 2px 10px;
    border-radius: 2px;
}

.case-company {
    font-size: 13px;
    color: #666666;
    margin-bottom: 8px;
}

.case-amount {
    font-size: 16px;
    font-weight: 700;
    color: #003366;
    margin-bottom: 12px;
}

.case-amount-number {
    font-size: 36px;
    line-height: 1;
}

.case-desc {
    font-size: 14px;
    color: #555555;
    line-height: 1.7;
    margin-bottom: 0;
}

.cases-note {
    text-align: center;
    font-size: 12px;
    color: #999999;
    margin-top: 20px;
    margin-bottom: 0;
}

/* Mechanism */
.section-mechanism {
    padding: 80px 0;
    background-color: #F8F9FA;
}

.mechanism-lead {
    text-align: center;
    font-size: 16px;
    line-height: 1.8;
    margin-bottom: 40px;
}

.mechanism-chart {
    max-width: 640px;
    margin: 0 auto;
}

.mechanism-example {
    background: #FFFFFF;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 32px;
}

.mechanism-example-title {
    font-size: 15px;
    font-weight: 700;
    color: #003366;
    margin-bottom: 24px;
    text-align: center;
}

.mechanism-bars {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.mechanism-bar-row {
    display: flex;
    align-items: center;
    gap: 16px;
}

.mechanism-bar-label {
    width: 80px;
    font-size: 13px;
    font-weight: 700;
    color: #333333;
    text-align: right;
    flex-shrink: 0;
}

.mechanism-bar {
    height: 44px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 0 16px;
    font-size: 15px;
    font-weight: 700;
    color: #FFFFFF;
}

.mechanism-bar-total {
    width: 100%;
    background-color: #003366;
}

.mechanism-bar-subsidy {
    width: 50%;
    background-color: #2E7D32;
}

.mechanism-bar-burden {
    width: 50%;
    background-color: #B0BEC5;
    color: #333333;
}

.mechanism-highlight {
    text-align: center;
    font-size: 18px;
    font-weight: 700;
    color: #2E7D32;
    margin-top: 24px;
    margin-bottom: 0;
    padding: 12px;
    background-color: #E8F5E9;
    border-radius: 4px;
}

/* FAQ */
.section-faq {
    padding: 80px 0;
}

.faq-list {
    max-width: 760px;
    margin: 0 auto;
}

.faq-item {
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    margin-bottom: 12px;
    overflow: hidden;
    background: #FFFFFF;
}

.faq-question {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: #FFFFFF;
    border: none;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    color: #003366;
    text-align: left;
    font-family: inherit;
    line-height: 1.5;
    transition: background-color 0.2s;
}

.faq-question:hover {
    background-color: #F8F9FA;
}

.faq-icon {
    font-size: 22px;
    font-weight: 700;
    color: #003366;
    flex-shrink: 0;
    margin-left: 16px;
    width: 24px;
    text-align: center;
    transition: transform 0.2s;
}

.faq-item.is-open .faq-icon {
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-answer p {
    padding: 0 24px 20px;
    font-size: 14px;
    color: #555555;
    line-height: 1.8;
    margin: 0;
}

.faq-item.is-open .faq-answer {
    max-height: 300px;
}

/* Company */
.section-company {
    padding: 60px 0;
}

.company-table {
    max-width: 560px;
    margin: 0 auto;
    width: 100%;
    border-collapse: collapse;
}

.company-table th,
.company-table td {
    padding: 16px 20px;
    font-size: 14px;
    border-bottom: 1px solid #E0E0E0;
    text-align: left;
}

.company-table th {
    width: 140px;
    font-weight: 700;
    color: #003366;
    background-color: #F0F4F8;
}

.company-table td a {
    color: #0056b3;
}

/* Responsive */
@media (max-width: 768px) {
    .hero {
        padding: 48px 0;
    }

    .hero-title {
        font-size: 24px;
    }

    .hero-title br,
    .hero-description br,
    .mechanism-lead br {
        display: none;
    }

    .stats-grid {
        flex-direction: column;
        gap: 32px;
        align-items: center;
    }

    .stat-number {
        font-size: 44px;
    }

    .section-stats {
        padding: 48px 0;
    }

    .worries-grid {
        grid-template-columns: 1fr;
    }

    .section-worries,
    .section-cases,
    .section-mechanism,
    .section-faq {
        padding: 56px 0;
    }

    .steps {
        flex-direction: column;
    }

    .step {
        max-width: 100%;
        width: 100%;
    }

    .step-arrow {
        transform: rotate(90deg);
    }

    .features {
        grid-template-columns: 1fr;
    }

    .cases-grid {
        grid-template-columns: 1fr;
    }

    .mechanism-example {
        padding: 24px 16px;
    }

    .mechanism-bar-label {
        width: 60px;
        font-size: 12px;
    }

    .mechanism-bar {
        font-size: 13px;
        height: 38px;
    }

    .company-table th {
        width: 100px;
    }
}
</style>

<?php get_footer(); ?>
