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

    <!-- CTA -->
    <section class="section section-gray">
        <div class="container text-center">
            <h2 class="section-title">まずは無料診断をお試しください</h2>
            <p class="text-muted mb-24">約2分の簡単な質問で、貴社に該当する補助金・助成金がわかります。</p>
            <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                補助金診断を始める
            </a>
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

@media (max-width: 768px) {
    .hero {
        padding: 48px 0;
    }

    .hero-title {
        font-size: 24px;
    }

    .hero-title br,
    .hero-description br {
        display: none;
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
}
</style>

<?php get_footer(); ?>
