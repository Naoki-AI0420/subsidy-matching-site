<?php
/**
 * Template Name: トップページ
 *
 * UTM パラメータで LP 出し分け:
 * - utm_medium=cpc/ppc or utm_source=google_ads → リスティング用LP（PASONA型）
 * - それ以外（SNS広告/オーガニック）→ SNS広告用LP（信頼先行型）デフォルト
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main>
    <!-- 共通: 緊急性バー -->
    <div class="urgency-bar">
        <div class="container">
            <p class="urgency-bar-text">昨年度は予算上限到達により<strong>23件</strong>の補助金が早期終了しました。お早めの診断をお勧めいたします。</p>
        </div>
    </div>

    <!-- リスティング用ヒーロー -->
    <section class="hero lp-listing-only">
        <div class="container">
            <div class="hero-content">
                <p class="hero-label">中小企業・小規模事業者向け</p>
                <h1 class="hero-title">御社に合った補助金・助成金を<br>1分で無料診断</h1>
                <p class="hero-description">
                    簡単な質問に答えるだけで、貴社に該当する可能性のある<br>
                    補助金・助成金を自動マッチングいたします。
                </p>
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    無料で補助金診断を始める
                </a>
                <p class="hero-note">※ 所要時間：約1分 ／ 登録不要でご利用いただけます</p>
            </div>
        </div>
    </section>

    <!-- SNS広告用ヒーロー -->
    <section class="hero lp-sns-only">
        <div class="container">
            <div class="hero-content">
                <p class="hero-label">中小企業・小規模事業者向け 補助金・助成金マッチング</p>
                <h2 class="hero-title">もらえるはずだった補助金、<br>まだ見逃していませんか？</h2>
                <p class="hero-description">
                    全国49,000件以上の補助金データから、<br>
                    御社に合った制度を1分で無料診断。
                </p>
                <div class="hero-badges">
                    <span class="hero-badge">49,000件以上の補助金データ</span>
                    <span class="hero-badge">5,000社以上の利用実績</span>
                    <span class="hero-badge">47都道府県対応</span>
                </div>
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    無料で補助金診断を始める
                </a>
                <p class="hero-note">※ 所要時間：約1分 ／ 登録不要でご利用いただけます</p>
            </div>
        </div>
    </section>

    <!-- 共通: 信頼バッジ -->
    <section class="section-trust-badges">
        <div class="container">
            <div class="trust-badges-grid">
                <div class="trust-badge-item">
                    <span class="trust-badge-icon">&#x1F6E1;</span>
                    <span class="trust-badge-text">経済産業省公開データ準拠</span>
                </div>
                <div class="trust-badge-item">
                    <span class="trust-badge-icon">&#x1F3E2;</span>
                    <span class="trust-badge-text">中小企業庁情報活用</span>
                </div>
                <div class="trust-badge-item">
                    <span class="trust-badge-icon">&#x1F5FE;</span>
                    <span class="trust-badge-text">47都道府県対応</span>
                </div>
                <div class="trust-badge-item">
                    <span class="trust-badge-icon">&#x1F4CA;</span>
                    <span class="trust-badge-text">累計診断実績 <strong class="counter-up" data-target="5000">0</strong>社以上</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 共通: 数字で示す実績 -->
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

    <!-- SNS用: お客様の声（信頼を先に見せる） -->
    <section class="section section-gray section-testimonials lp-sns-only">
        <div class="container">
            <h2 class="section-title">お客様の声</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>自社に合う補助金が3件も見つかりました。IT導入補助金で会計ソフトを導入し、月20時間の事務作業を削減できました。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-man1.jpg" alt="田中様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">田中 裕介 様</span>
                            <span class="testimonial-detail">飲食業・東京都・従業員8名</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>補助金の存在は知っていましたが、種類が多すぎて諦めていました。診断で該当する制度がすぐにわかり、1,000万円の補助を受けられました。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-man2.jpg" alt="佐藤様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">佐藤 健一 様</span>
                            <span class="testimonial-detail">製造業・宮城県・従業員15名</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>持続化補助金でECサイトを構築。オンライン売上が月商の40%を占めるまでに成長しました。まずは診断してみることをお勧めします。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-man3.jpg" alt="山本様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">山本 誠 様</span>
                            <span class="testimonial-detail">小売業・大阪府・従業員3名</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>事務員2名の小さなサロンですが、IT導入補助金で150万円をいただき、予約システムを導入できました。お客様の予約がスムーズになり、リピート率が20%上がりました。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-woman1.jpg" alt="鈴木様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">鈴木 美咲 様</span>
                            <span class="testimonial-detail">サービス業・埼玉県・従業員2名</span>
                        </div>
                    </div>
                </div>
            </div>
            <p class="testimonials-note">※ 個人の感想です。効果には個人差があります。</p>
        </div>
    </section>

    <!-- SNS用: 専門家推薦（信頼先行） -->
    <section class="section section-expert lp-sns-only">
        <div class="container">
            <div class="expert-card">
                <div class="expert-header">
                    <div class="expert-avatar">
                        <span>中</span>
                    </div>
                    <div class="expert-info">
                        <span class="expert-name">中村 雅彦 氏</span>
                        <span class="expert-title">中小企業診断士 / 経営コンサルタント</span>
                    </div>
                </div>
                <div class="expert-bubble">
                    <p>中小企業の経営者にとって、自社に合った補助金を見つけることは非常に困難です。年間数万件もの補助金制度の中から、自社の業種・規模・課題に合致するものを探すには膨大な時間がかかります。このサービスは、その課題を的確に解決してくれます。まずは無料診断で、御社に該当する補助金があるか確認されることをお勧めします。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SNS用: 気づきセクション -->
    <section class="section section-awareness lp-sns-only">
        <div class="container text-center">
            <div class="awareness-box">
                <h2 class="awareness-title">あなたももらえるはずだった<br>補助金・助成金、見逃していませんか？</h2>
                <p class="awareness-lead">実は、年間約<strong>3兆円</strong>の補助金予算のうち<br>約<strong>40%</strong>が使われずに消えています。</p>
                <p class="awareness-sub">御社にも、まだ受け取っていない<br>補助金があるかもしれません。</p>
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    1分で無料診断してみる
                </a>
            </div>
        </div>
    </section>

    <!-- 共通: お悩み -->
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
                    <p>申請書類の作成が複雑で諦めた</p>
                </div>
                <div class="worry-item">
                    <p>システム導入したいけど予算がない</p>
                </div>
            </div>
        </div>
    </section>

    <!-- リスティング用: Affinity（PASONA の A） -->
    <section class="section section-gray section-affinity lp-listing-only">
        <div class="container text-center">
            <h2 class="section-title">毎年20,000件以上の補助金情報を<br>全て確認するのは不可能です</h2>
            <p class="affinity-text">国・自治体・各省庁が実施する補助金は年間数万件。<br>自社の業種・規模・課題にマッチする制度を自力で見つけるには、膨大な時間と専門知識が必要です。</p>
            <p class="affinity-highlight">だからこそ、<strong>自動マッチング</strong>が必要です。</p>
        </div>
    </section>

    <!-- リスティング用: Solution → CTA（PASONA の S） -->
    <section class="section section-solution lp-listing-only">
        <div class="container text-center">
            <h2 class="section-title">1分の無料診断で自動マッチング</h2>
            <p class="solution-text">業種・地域・企業規模などの基本情報を入力するだけで、<br>49,000件以上の補助金データから御社に該当する制度を自動的に抽出します。</p>
            <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                今すぐ無料診断する
            </a>
        </div>
    </section>

    <!-- 共通: Solution → CTA -->
    <section class="section section-solution">
        <div class="container text-center">
            <h2 class="section-title">1分の無料診断で自動マッチング</h2>
            <p class="solution-text">業種・地域・企業規模などの基本情報を入力するだけで、<br>49,000件以上の補助金データから御社に該当する制度を自動的に抽出します。</p>
            <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                今すぐ無料診断する
            </a>
        </div>
    </section>

    <!-- 共通: ご利用の流れ -->
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
            <div class="text-center mt-32">
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    今すぐ無料診断する
                </a>
            </div>
        </div>
    </section>

    <!-- リスティング用: 導入事例 -->
    <section class="section section-gray section-cases lp-listing-only">
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

    <!-- リスティング用: お客様の声 -->
    <section class="section section-testimonials lp-listing-only">
        <div class="container">
            <h2 class="section-title">お客様の声</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>自社に合う補助金が3件も見つかりました。IT導入補助金で会計ソフトを導入し、月20時間の事務作業を削減できました。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-man1.jpg" alt="田中様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">田中 裕介 様</span>
                            <span class="testimonial-detail">飲食業・東京都・従業員8名</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>補助金の存在は知っていましたが、種類が多すぎて諦めていました。診断で該当する制度がすぐにわかり、1,000万円の補助を受けられました。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-man2.jpg" alt="佐藤様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">佐藤 健一 様</span>
                            <span class="testimonial-detail">製造業・宮城県・従業員15名</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>持続化補助金でECサイトを構築。オンライン売上が月商の40%を占めるまでに成長しました。まずは診断してみることをお勧めします。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-man3.jpg" alt="山本様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">山本 誠 様</span>
                            <span class="testimonial-detail">小売業・大阪府・従業員3名</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-bubble">
                        <p>事務員2名の小さなサロンですが、IT導入補助金で150万円をいただき、予約システムを導入できました。リピート率が20%上がりました。</p>
                    </div>
                    <div class="testimonial-author">
                        <img class="testimonial-avatar-img" src="<?php echo get_template_directory_uri(); ?>/assets/images/testimonial-woman1.jpg" alt="鈴木様" loading="lazy">
                        <div class="testimonial-info">
                            <span class="testimonial-name">鈴木 美咲 様</span>
                            <span class="testimonial-detail">サービス業・埼玉県・従業員2名</span>
                        </div>
                    </div>
                </div>
            </div>
            <p class="testimonials-note">※ 個人の感想です。効果には個人差があります。</p>
        </div>
    </section>

    <!-- リスティング用: 専門家推薦 -->
    <section class="section section-expert lp-listing-only">
        <div class="container">
            <div class="expert-card">
                <div class="expert-header">
                    <div class="expert-avatar">
                        <span>中</span>
                    </div>
                    <div class="expert-info">
                        <span class="expert-name">中村 雅彦 氏</span>
                        <span class="expert-title">中小企業診断士 / 経営コンサルタント</span>
                    </div>
                </div>
                <div class="expert-bubble">
                    <p>中小企業の経営者にとって、自社に合った補助金を見つけることは非常に困難です。年間数万件もの補助金制度の中から、自社の業種・規模・課題に合致するものを探すには膨大な時間がかかります。このサービスは、その課題を的確に解決してくれます。まずは無料診断で、御社に該当する補助金があるか確認されることをお勧めします。</p>
                </div>
            </div>
            <div class="text-center mt-32">
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    専門家も推薦する無料診断を試す
                </a>
            </div>
        </div>
    </section>

    <!-- SNS用: 補助金の仕組み解説（アンカリング） -->
    <section class="section section-mechanism lp-sns-only">
        <div class="container">
            <h2 class="section-title">補助金の仕組み</h2>
            <p class="mechanism-lead">補助金とは、国や自治体から支給される<strong>返済不要</strong>の資金です。<br>事業に必要な費用の一部を補助金でまかなうことができます。</p>
            <div class="mechanism-chart">
                <div class="mechanism-example">
                    <p class="mechanism-anchor">主要な補助金の上限額: 最大<strong>1.5億円</strong></p>
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
            <div class="text-center mt-32">
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    あなたの補助金額を無料で調べる
                </a>
            </div>
        </div>
    </section>

    <!-- 共通: 本サービスの特徴 -->
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
            <div class="text-center mt-32">
                <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                    1分で結果がわかる無料診断
                </a>
            </div>
        </div>
    </section>

    <!-- 共通: 限定感 -->
    <section class="section section-limited">
        <div class="container text-center">
            <div class="limited-box">
                <p class="limited-label">無料相談について</p>
                <p class="limited-text">無料相談は<strong>月30社限定</strong>で承っております</p>
                <p class="limited-sub">お早めにお申し込みください。定員に達し次第、翌月のご案内となります。</p>
            </div>
        </div>
    </section>

    <!-- 共通: FAQ -->
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
                        <p>オンライン診断は約1分で完了します。診断後、より詳しいご相談をご希望の場合は、お問い合わせフォームよりご連絡ください。通常1〜2営業日以内にご返信いたします。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 共通: CTA（フッター前） -->
    <section class="section">
        <div class="container text-center">
            <h2 class="section-title">まずは無料診断をお試しください</h2>
            <p class="text-muted mb-24">約1分の簡単な質問で、貴社に該当する補助金・助成金がわかります。</p>
            <a href="<?php echo esc_url(home_url('/matching/')); ?>" class="btn btn-primary btn-large">
                補助金診断を始める
            </a>
        </div>
    </section>

    <!-- 共通: 運営情報 -->
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
/* LP 出し分け */
.lp-listing-only { display: none; }
.lp-sns-only { display: none; }
body.lp-listing .lp-listing-only { display: block; }
body.lp-listing .lp-sns-only { display: none !important; }
body.lp-sns .lp-listing-only { display: none !important; }
body.lp-sns .lp-sns-only { display: block; }

/* Hero */
.hero { background: linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.90)), url('<?php echo get_template_directory_uri(); ?>/assets/images/hero-japanese-office.jpg') center/cover no-repeat; color: #FFFFFF; padding: 80px 0; text-align: center; }
.hero-label { font-size: 14px; font-weight: 500; letter-spacing: 2px; margin-bottom: 16px; opacity: 0.9; }
.hero-title { font-size: 32px; font-weight: 700; color: #FFFFFF; margin-bottom: 20px; line-height: 1.5; }
.hero-description { font-size: 16px; margin-bottom: 32px; opacity: 0.9; line-height: 1.8; }
.hero .btn-primary { background-color: #FFFFFF; color: #003366; font-weight: 700; font-size: 18px; padding: 16px 48px; }
.hero .btn-primary:hover { background-color: #F5F5F5; }
.hero-note { font-size: 13px; margin-top: 12px; opacity: 0.7; margin-bottom: 0; }
.hero-badges { display: flex; justify-content: center; gap: 16px; margin-bottom: 32px; flex-wrap: wrap; }
.hero-badge { display: inline-block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 4px; padding: 6px 16px; font-size: 13px; font-weight: 600; color: #FFFFFF; }

/* 緊急性バー */
.urgency-bar { background-color: #FFF3E0; border-bottom: 1px solid #FFE0B2; padding: 10px 0; }
.urgency-bar-text { font-size: 13px; color: #E65100; text-align: center; margin: 0; line-height: 1.5; }
.urgency-bar-text strong { font-weight: 700; }

/* 信頼バッジ */
.section-trust-badges { background-color: #F0F4F8; padding: 20px 0; border-bottom: 1px solid #D0DAE5; }
.trust-badges-grid { display: flex; justify-content: center; align-items: center; gap: 32px; flex-wrap: wrap; }
.trust-badge-item { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: #1a3353; background: #FFFFFF; border: 1px solid #D0DAE5; border-radius: 2px; padding: 6px 14px; }
.trust-badge-icon { font-size: 14px; line-height: 1; }
.trust-badge-text strong { color: #003366; font-weight: 700; }

/* Stats */
.section-stats { background-color: #1a3353; padding: 64px 0; }
.stats-grid { display: flex; justify-content: center; gap: 60px; }
.stat-item { text-align: center; color: #FFFFFF; }
.stat-number { font-size: 56px; font-weight: 700; line-height: 1.2; display: inline; }
.stat-unit { font-size: 18px; font-weight: 500; opacity: 0.9; }
.stat-label { font-size: 14px; margin-top: 8px; margin-bottom: 0; opacity: 0.8; }

/* 気づきセクション */
.section-awareness { padding: 80px 0; background: linear-gradient(135deg, #003366 0%, #1a4d80 100%); }
.awareness-box { max-width: 680px; margin: 0 auto; color: #FFFFFF; }
.awareness-title { font-size: 28px; font-weight: 700; color: #FFFFFF; margin-bottom: 24px; line-height: 1.6; }
.awareness-lead { font-size: 18px; line-height: 1.8; margin-bottom: 16px; opacity: 0.95; }
.awareness-lead strong { color: #FFD54F; font-size: 24px; }
.awareness-sub { font-size: 16px; line-height: 1.8; margin-bottom: 32px; opacity: 0.85; }
.section-awareness .btn-primary { background-color: #FFFFFF; color: #003366; font-weight: 700; font-size: 18px; padding: 16px 48px; }
.section-awareness .btn-primary:hover { background-color: #F5F5F5; }

/* Affinity */
.section-affinity { padding: 64px 0; }
.affinity-text { font-size: 16px; line-height: 1.8; color: #555555; margin-bottom: 24px; }
.affinity-highlight { font-size: 20px; font-weight: 700; color: #003366; margin-bottom: 0; }
.affinity-highlight strong { color: #C62828; }

/* Solution */
.section-solution { padding: 64px 0; }
.solution-text { font-size: 16px; line-height: 1.8; color: #555555; margin-bottom: 32px; }

/* Worries */
.section-worries { padding: 80px 0; }
.worries-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 800px; margin: 0 auto; }
.worry-item { background: #FFFFFF; border: 1px solid #E0E0E0; border-radius: 4px; padding: 24px 24px 24px 52px; position: relative; }
.worry-item::before { content: '\2713'; position: absolute; left: 20px; top: 24px; width: 22px; height: 22px; background-color: #003366; color: #FFFFFF; border-radius: 50%; font-size: 12px; display: flex; align-items: center; justify-content: center; line-height: 1; }
.worry-item p { margin: 0; font-size: 15px; font-weight: 500; color: #333333; }

/* Steps */
.steps { display: flex; align-items: center; justify-content: center; gap: 24px; }
.step { text-align: center; flex: 1; max-width: 260px; background: #FFFFFF; border: 1px solid #CCCCCC; border-radius: 4px; padding: 32px 24px; }
.step-number { width: 48px; height: 48px; border-radius: 50%; background-color: #003366; color: #FFFFFF; font-size: 20px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.step-title { font-size: 18px; color: #003366; margin-bottom: 8px; }
.step-desc { font-size: 14px; color: #666666; margin-bottom: 0; }
.step-arrow { color: #CCCCCC; font-size: 20px; }

/* Features */
.features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.feature h3 { font-size: 18px; margin-bottom: 12px; }
.feature p { font-size: 14px; color: #666666; margin-bottom: 0; }

/* お客様の声 */
.section-testimonials { padding: 80px 0; }
.testimonials-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; max-width: 960px; margin: 0 auto; }
.testimonial-card { display: flex; flex-direction: column; }
.testimonial-bubble { background: #FFFFFF; border: 1px solid #E0E0E0; border-radius: 8px; padding: 24px; position: relative; margin-bottom: 16px; flex: 1; }
.testimonial-bubble::after { content: ''; position: absolute; bottom: -8px; left: 32px; width: 16px; height: 16px; background: #FFFFFF; border-right: 1px solid #E0E0E0; border-bottom: 1px solid #E0E0E0; transform: rotate(45deg); }
.testimonial-bubble p { font-size: 14px; color: #333333; line-height: 1.8; margin: 0; }
.testimonial-author { display: flex; align-items: center; gap: 12px; padding-left: 8px; }
.microcopy { font-size: 13px; color: #888; margin-top: 8px; text-align: center; }
.hero .microcopy { color: rgba(255,255,255,0.75); }
.testimonial-avatar-img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #e0e0e0; }
.testimonial-avatar { width: 44px; height: 44px; border-radius: 50%; background-color: #003366; color: #FFFFFF; font-size: 16px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.testimonial-info { display: flex; flex-direction: column; }
.testimonial-name { font-size: 13px; font-weight: 700; color: #333333; }
.testimonial-detail { font-size: 12px; color: #888888; }
.testimonials-note { text-align: center; font-size: 11px; color: #999999; margin-top: 20px; margin-bottom: 0; }

/* 専門家推薦 */
.section-expert { padding: 60px 0; }
.expert-card { max-width: 720px; margin: 0 auto; background: #FFFFFF; border: 1px solid #D0DAE5; border-left: 4px solid #003366; border-radius: 4px; padding: 32px; }
.expert-header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
.expert-avatar { width: 56px; height: 56px; border-radius: 50%; background-color: #1a3353; color: #FFFFFF; font-size: 20px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.expert-info { display: flex; flex-direction: column; }
.expert-name { font-size: 16px; font-weight: 700; color: #003366; }
.expert-title { font-size: 13px; color: #666666; }
.expert-bubble p { font-size: 15px; color: #333333; line-height: 1.8; margin: 0; padding: 20px; background: #F8F9FA; border-radius: 4px; border-left: 3px solid #B0BEC5; }

/* Cases */
.section-cases { padding: 80px 0; }
.cases-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.case-card { background: #FFFFFF; border: 1px solid #E0E0E0; border-left: 4px solid #003366; border-radius: 4px; padding: 28px 24px; }
.case-header { display: flex; gap: 8px; margin-bottom: 12px; }
.case-industry { display: inline-block; background-color: #003366; color: #FFFFFF; font-size: 12px; font-weight: 700; padding: 2px 10px; border-radius: 2px; }
.case-subsidy { display: inline-block; background-color: #E8F0F8; color: #003366; font-size: 12px; font-weight: 500; padding: 2px 10px; border-radius: 2px; }
.case-company { font-size: 13px; color: #666666; margin-bottom: 8px; }
.case-amount { font-size: 16px; font-weight: 700; color: #003366; margin-bottom: 12px; }
.case-amount-number { font-size: 36px; line-height: 1; }
.case-desc { font-size: 14px; color: #555555; line-height: 1.7; margin-bottom: 0; }
.cases-note { text-align: center; font-size: 12px; color: #999999; margin-top: 20px; margin-bottom: 0; }

/* Mechanism */
.section-mechanism { padding: 80px 0; background-color: #F8F9FA; }
.mechanism-lead { text-align: center; font-size: 16px; line-height: 1.8; margin-bottom: 40px; }
.mechanism-chart { max-width: 640px; margin: 0 auto; }
.mechanism-example { background: #FFFFFF; border: 1px solid #E0E0E0; border-radius: 4px; padding: 32px; }
.mechanism-anchor { text-align: center; font-size: 16px; color: #C62828; font-weight: 700; margin-bottom: 20px; padding: 12px; background: #FFF3E0; border-radius: 4px; border: 1px solid #FFE0B2; }
.mechanism-anchor strong { font-size: 24px; }
.mechanism-example-title { font-size: 15px; font-weight: 700; color: #003366; margin-bottom: 24px; text-align: center; }
.mechanism-bars { display: flex; flex-direction: column; gap: 16px; }
.mechanism-bar-row { display: flex; align-items: center; gap: 16px; }
.mechanism-bar-label { width: 80px; font-size: 13px; font-weight: 700; color: #333333; text-align: right; flex-shrink: 0; }
.mechanism-bar { height: 44px; border-radius: 4px; display: flex; align-items: center; padding: 0 16px; font-size: 15px; font-weight: 700; color: #FFFFFF; }
.mechanism-bar-total { width: 100%; background-color: #003366; }
.mechanism-bar-subsidy { width: 50%; background-color: #2E7D32; }
.mechanism-bar-burden { width: 50%; background-color: #B0BEC5; color: #333333; }
.mechanism-highlight { text-align: center; font-size: 18px; font-weight: 700; color: #2E7D32; margin-top: 24px; margin-bottom: 0; padding: 12px; background-color: #E8F5E9; border-radius: 4px; }

/* 限定ボックス */
.section-limited { padding: 40px 0; background: #FFFFFF; }
.limited-box { max-width: 560px; margin: 0 auto; padding: 28px 32px; border: 2px solid #D0DAE5; border-radius: 4px; background: #FAFBFC; }
.limited-label { font-size: 12px; font-weight: 700; color: #666666; letter-spacing: 0.1em; margin-bottom: 8px; }
.limited-text { font-size: 18px; font-weight: 700; color: #003366; margin-bottom: 8px; }
.limited-text strong { color: #C62828; }
.limited-sub { font-size: 13px; color: #888888; margin: 0; }

/* FAQ */
.section-faq { padding: 80px 0; }
.faq-list { max-width: 760px; margin: 0 auto; }
.faq-item { border: 1px solid #E0E0E0; border-radius: 4px; margin-bottom: 12px; overflow: hidden; background: #FFFFFF; }
.faq-question { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; background: #FFFFFF; border: none; cursor: pointer; font-size: 15px; font-weight: 500; color: #003366; text-align: left; font-family: inherit; line-height: 1.5; transition: background-color 0.2s; }
.faq-question:hover { background-color: #F8F9FA; }
.faq-icon { font-size: 22px; font-weight: 700; color: #003366; flex-shrink: 0; margin-left: 16px; width: 24px; text-align: center; transition: transform 0.2s; }
.faq-item.is-open .faq-icon { transform: rotate(45deg); }
.faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
.faq-answer p { padding: 0 24px 20px; font-size: 14px; color: #555555; line-height: 1.8; margin: 0; }
.faq-item.is-open .faq-answer { max-height: 300px; }

/* Company */
.section-company { padding: 60px 0; }
.company-table { max-width: 560px; margin: 0 auto; width: 100%; border-collapse: collapse; }
.company-table th, .company-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #E0E0E0; text-align: left; }
.company-table th { width: 140px; font-weight: 700; color: #003366; background-color: #F0F4F8; }
.company-table td a { color: #0056b3; }

/* Responsive */
@media (max-width: 768px) {
    .hero { padding: 48px 0; }
    .hero-title { font-size: 24px; }
    .hero-title br, .hero-description br, .mechanism-lead br, .awareness-title br, .awareness-lead br, .awareness-sub br, .affinity-text br, .solution-text br { display: none; }
    .hero-badges { gap: 8px; }
    .hero-badge { font-size: 11px; padding: 4px 10px; }
    .trust-badges-grid { gap: 8px; }
    .trust-badge-item { font-size: 11px; padding: 4px 10px; }
    .stats-grid { flex-direction: column; gap: 32px; align-items: center; }
    .stat-number { font-size: 44px; }
    .section-stats { padding: 48px 0; }
    .worries-grid { grid-template-columns: 1fr; }
    .section-worries, .section-cases, .section-mechanism, .section-faq, .section-testimonials { padding: 56px 0; }
    .section-awareness { padding: 56px 0; }
    .awareness-title { font-size: 22px; }
    .awareness-lead { font-size: 16px; }
    .awareness-lead strong { font-size: 20px; }
    .steps { flex-direction: column; }
    .step { max-width: 100%; width: 100%; }
    .step-arrow { transform: rotate(90deg); }
    .features { grid-template-columns: 1fr; }
    .testimonials-grid { grid-template-columns: 1fr; }
    .cases-grid { grid-template-columns: 1fr; }
    .mechanism-example { padding: 24px 16px; }
    .mechanism-bar-label { width: 60px; font-size: 12px; }
    .mechanism-bar { font-size: 13px; height: 38px; }
    .company-table th { width: 100px; }
    .expert-card { padding: 24px 16px; }
    .expert-bubble p { padding: 16px; font-size: 14px; }
    .section-affinity, .section-solution { padding: 48px 0; }
    .affinity-highlight { font-size: 18px; }
}
</style>

<script>
// UTM パラメータ判定 & LP 出し分け（即時実行）
(function() {
    var params = new URLSearchParams(window.location.search);
    var source = (params.get('utm_source') || '').toLowerCase();
    var medium = (params.get('utm_medium') || '').toLowerCase();

    var isListing = (medium === 'cpc' || medium === 'ppc' || source === 'google_ads');

    document.body.classList.add(isListing ? 'lp-listing' : 'lp-sns');

    // UTM パラメータを Cookie に保存（診断ページでも維持）
    if (source || medium) {
        var utmData = {
            utm_source: params.get('utm_source') || '',
            utm_medium: params.get('utm_medium') || '',
            utm_campaign: params.get('utm_campaign') || '',
            utm_term: params.get('utm_term') || '',
            utm_content: params.get('utm_content') || ''
        };
        var expires = new Date();
        expires.setDate(expires.getDate() + 30);
        document.cookie = 'utm_params=' + encodeURIComponent(JSON.stringify(utmData)) + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
    }

    // GA イベント送信
    if (typeof gtag === 'function') {
        gtag('event', 'lp_view', {
            lp_type: isListing ? 'listing' : 'sns',
            utm_source: source,
            utm_medium: medium
        });
    }
})();

// カウントアップアニメーション
(function() {
    function animateCounters() {
        var counters = document.querySelectorAll('.counter-up, .stat-number[data-target]');
        counters.forEach(function(el) {
            var target = parseInt(el.getAttribute('data-target'));
            if (!target || el.dataset.animated) return;

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !el.dataset.animated) {
                        el.dataset.animated = '1';
                        var duration = 1500;
                        var startTime = null;

                        function step(timestamp) {
                            if (!startTime) startTime = timestamp;
                            var progress = Math.min((timestamp - startTime) / duration, 1);
                            var eased = 1 - Math.pow(1 - progress, 3);
                            var current = Math.floor(eased * target);
                            el.textContent = current.toLocaleString();
                            if (progress < 1) {
                                requestAnimationFrame(step);
                            } else {
                                el.textContent = target.toLocaleString();
                            }
                        }
                        requestAnimationFrame(step);
                    }
                });
            }, { threshold: 0.3 });
            observer.observe(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', animateCounters);
    } else {
        animateCounters();
    }
})();
</script>

<?php get_footer(); ?>
