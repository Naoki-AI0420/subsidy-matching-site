<?php
/**
 * Template Name: トップページ
 *
 * @package SubsidyMatch
 */

get_header();
$matching_url = esc_url(home_url('/matching/'));
$contact_url  = esc_url(home_url('/contact/'));
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
                <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-large">
                    無料で補助金診断を始める
                </a>
                <p class="hero-note">※ 所要時間：約2分 ／ 登録不要でご利用いただけます</p>
                <!-- 社会的証明カウンター -->
                <div class="social-proof" id="social-proof">
                    <svg class="social-proof-icon" viewBox="0 0 24 24" fill="none" width="18" height="18">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" fill="currentColor"/>
                    </svg>
                    <span>本日 <strong id="proof-count">--</strong> 名が診断を完了しました</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 権威性バッジ -->
    <div class="authority-badges">
        <div class="container">
            <div class="authority-badges-inner">
                <span class="authority-badge">
                    <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><path d="M10 1l2.39 4.84L18 6.71l-4 3.9.94 5.5L10 13.77l-4.94 2.34.94-5.5-4-3.9 5.61-.87L10 1z" fill="#8B8B8B"/></svg>
                    経済産業省公開データ準拠
                </span>
                <span class="authority-badge">
                    <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><path d="M10 1l2.39 4.84L18 6.71l-4 3.9.94 5.5L10 13.77l-4.94 2.34.94-5.5-4-3.9 5.61-.87L10 1z" fill="#8B8B8B"/></svg>
                    中小企業庁情報活用
                </span>
                <span class="authority-badge">
                    <svg viewBox="0 0 20 20" fill="none" width="16" height="16"><path d="M10 1l2.39 4.84L18 6.71l-4 3.9.94 5.5L10 13.77l-4.94 2.34.94-5.5-4-3.9 5.61-.87L10 1z" fill="#8B8B8B"/></svg>
                    全国自治体補助金情報対応
                </span>
            </div>
        </div>
    </div>

    <!-- 申請期限の緊急性表示 -->
    <section class="urgency-banner" id="urgency-banner" style="display:none">
        <div class="container">
            <div class="urgency-inner">
                <span class="urgency-pulse"></span>
                <span class="urgency-text">今月申請期限の補助金が <strong id="deadline-count">0</strong> 件あります</span>
                <a href="<?php echo $matching_url; ?>" class="urgency-link">今すぐ診断する</a>
            </div>
        </div>
    </section>

    <!-- 知らないと損する、補助金の事実 -->
    <section class="section section-facts">
        <div class="container">
            <h2 class="section-title">知らないと損する、補助金の事実</h2>
            <div class="fact-card">
                <div class="fact-number">40%</div>
                <div class="fact-text">
                    <h3>補助金予算の約40%が毎年未使用のまま消えています</h3>
                    <p>令和8年度の補助金・助成金の年間予算は約3〜4兆円。しかし、その約40%にあたる1兆円以上が、申請者不足や情報不足により使われないまま消滅しています。あなたの会社が受け取れるはずだったお金が、毎年見逃されているかもしれません。</p>
                </div>
            </div>
            <div class="fact-card">
                <div class="fact-number">78%</div>
                <div class="fact-text">
                    <h3>中小企業の78%が「自社に合った補助金がわからない」と回答</h3>
                    <p>中小企業庁の調査によると、補助金制度の存在は知っていても、「自社が対象かどうかわからない」「申請方法がわからない」という理由で申請を諦めている企業が大多数です。年間20,000件以上公表される補助金情報の中から、自社に合ったものを見つけるのは容易ではありません。</p>
                </div>
            </div>
            <div class="fact-card">
                <div class="fact-number">62%</div>
                <div class="fact-text">
                    <h3>IT導入補助金の採択率は約62%</h3>
                    <p>「補助金は難しそう」と思われがちですが、IT導入補助金の採択率は約62%と、半数以上が採択されています。適切な事業計画と申請書類の準備があれば、決してハードルの高い制度ではありません。</p>
                </div>
            </div>
            <div class="text-center mt-32">
                <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-large">あなたの会社の補助金を調べる</a>
            </div>
        </div>
    </section>

    <!-- 補助金申請の流れ（5ステップ） -->
    <section class="section section-gray section-steps-detail">
        <div class="container">
            <h2 class="section-title">補助金申請の流れ</h2>
            <div class="steps-detail">
                <div class="step-detail step-detail-active">
                    <div class="step-detail-number">1</div>
                    <h3 class="step-detail-title">無料診断（1分）</h3>
                    <p class="step-detail-desc">14の質問に答えるだけ。会社の基本情報とDX状況をお聞きします。</p>
                </div>
                <div class="step-detail-line"></div>
                <div class="step-detail">
                    <div class="step-detail-number">2</div>
                    <h3 class="step-detail-title">結果確認</h3>
                    <p class="step-detail-desc">御社に該当する可能性のある補助金・助成金の一覧と、DX課題の分析レポートをその場でお渡しします。</p>
                </div>
                <div class="step-detail-line"></div>
                <div class="step-detail">
                    <div class="step-detail-number">3</div>
                    <h3 class="step-detail-title">無料相談</h3>
                    <p class="step-detail-desc">結果をもとに、専門スタッフが補助金の選び方や申請のポイントを丁寧にご説明します。</p>
                </div>
                <div class="step-detail-line"></div>
                <div class="step-detail">
                    <div class="step-detail-number">4</div>
                    <h3 class="step-detail-title">事業計画作成・申請</h3>
                    <p class="step-detail-desc">採択率を高める事業計画の作成を支援。申請書類の準備から提出までサポートします。</p>
                </div>
                <div class="step-detail-line"></div>
                <div class="step-detail">
                    <div class="step-detail-number">5</div>
                    <h3 class="step-detail-title">採択・システム導入</h3>
                    <p class="step-detail-desc">補助金が採択されたら、最適なシステムの選定・導入・運用までトータルで支援します。</p>
                </div>
            </div>
            <div class="text-center mt-32">
                <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-large">まずはSTEP1の無料診断から</a>
            </div>
        </div>
    </section>

    <!-- 中間CTA 1 -->
    <section class="section mid-cta-section">
        <div class="container text-center">
            <p class="mid-cta-text">まずは1分で、御社に合った補助金があるか確認してみませんか？</p>
            <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-mid-cta">無料で診断する</a>
        </div>
    </section>

    <!-- 悩みセクション -->
    <section class="section section-gray">
        <div class="container">
            <h2 class="section-title">このようなお悩みはありませんか？</h2>
            <div class="pain-points">
                <div class="pain-point card card-bordered"><h3>どの補助金が使えるかわからない</h3><p>補助金の種類が多すぎて、自社に該当するものがどれなのか判断がつかない。調べる時間もない。</p></div>
                <div class="pain-point card card-bordered"><h3>申請手続きが複雑で不安</h3><p>書類の書き方や申請のルールが難しそう。せっかく該当しても、申請で失敗したらもったいない。</p></div>
                <div class="pain-point card card-bordered"><h3>申請期限を逃してしまう</h3><p>気づいたときには募集が終了していた。補助金情報をタイムリーにキャッチできていない。</p></div>
            </div>
        </div>
    </section>

    <!-- 中間CTA 2 -->
    <section class="section mid-cta-section mid-cta-accent">
        <div class="container text-center">
            <p class="mid-cta-text">そのお悩み、無料の補助金診断で解決できます</p>
            <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-mid-cta">今すぐ無料診断を始める</a>
            <p class="mid-cta-sub">※ 約2分で完了。登録不要です。</p>
        </div>
    </section>

    <!-- 特徴 -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">本サービスの特徴</h2>
            <div class="features">
                <div class="feature card card-bordered"><h3>全国の補助金・助成金を網羅</h3><p>経済産業省、厚生労働省、各自治体が実施する補助金・助成金情報を幅広く掲載。定期的に情報を更新しております。</p></div>
                <div class="feature card card-bordered"><h3>簡単な質問で自動マッチング</h3><p>業種・地域・企業規模などの基本情報をご入力いただくだけで、該当する可能性のある補助金を自動的に抽出いたします。</p></div>
                <div class="feature card card-bordered"><h3>申請サポートも対応</h3><p>「申請の方法がわからない」「書類作成が不安」など、補助金申請に関するお悩みは専門スタッフが無料でサポートいたします。</p></div>
            </div>
        </div>
    </section>

    <!-- 中間CTA 3 -->
    <section class="section mid-cta-section">
        <div class="container text-center">
            <p class="mid-cta-text">活用できる補助金があるか、まずは無料で確認しましょう</p>
            <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-mid-cta">補助金診断を始める</a>
        </div>
    </section>

    <!-- 業種別 補助金活用事例 -->
    <section class="section section-gray section-industry-stories">
        <div class="container">
            <h2 class="section-title">業種別 補助金活用事例</h2>
            <p class="industry-stories-lead">あなたの業種をクリックして、補助金の活用イメージをご確認ください。</p>
            <div class="industry-tabs">
                <button class="industry-tab is-active" data-tab="manufacturing" type="button">製造業</button>
                <button class="industry-tab" data-tab="restaurant" type="button">飲食業</button>
                <button class="industry-tab" data-tab="retail" type="button">小売業</button>
                <button class="industry-tab" data-tab="construction" type="button">建設業</button>
                <button class="industry-tab" data-tab="medical" type="button">医療・介護</button>
            </div>
            <div class="industry-panels">
                <div class="industry-panel is-active" id="panel-manufacturing">
                    <div class="industry-flow">
                        <div class="industry-flow-step"><span class="industry-flow-label">課題</span><p>生産管理が手作業、在庫の無駄が多い</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">活用した補助金</span><p>ものづくり補助金（最大1,250万円）</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">導入システム</span><p>生産管理＋在庫管理システム</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">結果</span><p>生産効率<strong>30%向上</strong>、在庫コスト年間<strong>200万円削減</strong></p></div>
                    </div>
                    <div class="industry-cost">
                        <div class="industry-cost-row"><span class="industry-cost-label">開発費</span><span class="industry-cost-value">800万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-subsidy"><span class="industry-cost-label">補助金</span><span class="industry-cost-value">533万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-burden"><span class="industry-cost-label">実質負担</span><span class="industry-cost-value industry-cost-highlight">267万円</span></div>
                    </div>
                </div>
                <div class="industry-panel" id="panel-restaurant">
                    <div class="industry-flow">
                        <div class="industry-flow-step"><span class="industry-flow-label">課題</span><p>電話予約の取りこぼし、紙の顧客台帳</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">活用した補助金</span><p>IT導入補助金（最大450万円）</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">導入システム</span><p>予約管理＋POS＋顧客管理</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">結果</span><p>予約取りこぼし<strong>ゼロ</strong>、リピート率<strong>25%向上</strong></p></div>
                    </div>
                    <div class="industry-cost">
                        <div class="industry-cost-row"><span class="industry-cost-label">導入費</span><span class="industry-cost-value">200万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-subsidy"><span class="industry-cost-label">補助金</span><span class="industry-cost-value">150万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-burden"><span class="industry-cost-label">実質負担</span><span class="industry-cost-value industry-cost-highlight">50万円</span></div>
                    </div>
                </div>
                <div class="industry-panel" id="panel-retail">
                    <div class="industry-flow">
                        <div class="industry-flow-step"><span class="industry-flow-label">課題</span><p>実店舗のみで売上が頭打ち</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">活用した補助金</span><p>小規模事業者持続化補助金（最大200万円）</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">導入システム</span><p>ECサイト＋決済システム</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">結果</span><p>オンライン売上が月商の<strong>35%</strong>に成長</p></div>
                    </div>
                    <div class="industry-cost">
                        <div class="industry-cost-row"><span class="industry-cost-label">制作費</span><span class="industry-cost-value">150万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-subsidy"><span class="industry-cost-label">補助金</span><span class="industry-cost-value">100万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-burden"><span class="industry-cost-label">実質負担</span><span class="industry-cost-value industry-cost-highlight">50万円</span></div>
                    </div>
                </div>
                <div class="industry-panel" id="panel-construction">
                    <div class="industry-flow">
                        <div class="industry-flow-step"><span class="industry-flow-label">課題</span><p>現場写真の管理が煩雑、日報が手書き</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">活用した補助金</span><p>IT導入補助金（最大450万円）</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">導入システム</span><p>施工管理＋日報＋写真管理アプリ</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">結果</span><p>事務作業時間<strong>60%削減</strong>、書類作成の残業<strong>ゼロ</strong>に</p></div>
                    </div>
                    <div class="industry-cost">
                        <div class="industry-cost-row"><span class="industry-cost-label">導入費</span><span class="industry-cost-value">300万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-subsidy"><span class="industry-cost-label">補助金</span><span class="industry-cost-value">225万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-burden"><span class="industry-cost-label">実質負担</span><span class="industry-cost-value industry-cost-highlight">75万円</span></div>
                    </div>
                </div>
                <div class="industry-panel" id="panel-medical">
                    <div class="industry-flow">
                        <div class="industry-flow-step"><span class="industry-flow-label">課題</span><p>カルテが紙、予約が電話のみ</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">活用した補助金</span><p>IT導入補助金（最大450万円）</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">導入システム</span><p>電子カルテ＋Web予約＋レセプト連携</p></div>
                        <div class="industry-flow-arrow"></div>
                        <div class="industry-flow-step"><span class="industry-flow-label">結果</span><p>1日あたり診察可能患者数<strong>20%増加</strong></p></div>
                    </div>
                    <div class="industry-cost">
                        <div class="industry-cost-row"><span class="industry-cost-label">導入費</span><span class="industry-cost-value">400万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-subsidy"><span class="industry-cost-label">補助金</span><span class="industry-cost-value">300万円</span></div>
                        <div class="industry-cost-arrow">→</div>
                        <div class="industry-cost-row industry-cost-burden"><span class="industry-cost-label">実質負担</span><span class="industry-cost-value industry-cost-highlight">100万円</span></div>
                    </div>
                </div>
            </div>
            <p class="industry-stories-note">※ 上記は補助金活用のイメージです</p>
            <div class="text-center mt-32">
                <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-large">あなたの業種でも診断してみませんか？</a>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section section-gray">
        <div class="container">
            <h2 class="section-title">よくあるご質問</h2>
            <div class="faq-list">
                <div class="faq-item card card-bordered"><h3 class="faq-q">Q. 利用料金はかかりますか？</h3><p class="faq-a">A. 補助金診断は完全無料です。診断後の専門家への相談も無料で承っております。</p></div>
                <div class="faq-item card card-bordered"><h3 class="faq-q">Q. 個人事業主でも利用できますか？</h3><p class="faq-a">A. はい、個人事業主の方もご利用いただけます。小規模事業者向けの補助金も多数掲載しております。</p></div>
                <div class="faq-item card card-bordered"><h3 class="faq-q">Q. 診断結果はどのくらい正確ですか？</h3><p class="faq-a">A. 公開されている補助金の要件に基づいてマッチングを行っております。最終的な申請可否は専門家と一緒にご確認ください。</p></div>
            </div>
        </div>
    </section>

    <!-- CTA（最終） -->
    <section class="section section-gray">
        <div class="container text-center">
            <h2 class="section-title">まずは無料診断をお試しください</h2>
            <p class="text-muted mb-24">約2分の簡単な質問で、貴社に該当する補助金・助成金がわかります。</p>
            <a href="<?php echo $matching_url; ?>" class="btn btn-primary btn-large">補助金診断を始める</a>
        </div>
    </section>

    <!-- データの信頼性について -->
    <section class="section-data-sources">
        <div class="container">
            <div class="data-sources-box">
                <h3 class="data-sources-title">データの信頼性について</h3>
                <p class="data-sources-lead">本サービスで提供する補助金情報は、以下の公的機関の公開情報に基づいています。</p>
                <ul class="data-sources-list">
                    <li>経済産業省（meti.go.jp）</li>
                    <li>中小企業庁（chusho.meti.go.jp）</li>
                    <li>各都道府県・市区町村の公式ウェブサイト</li>
                    <li>独立行政法人 中小企業基盤整備機構</li>
                    <li>各種財団法人・公益法人</li>
                </ul>
                <p class="data-sources-note">※ 補助金の内容は変更される場合があります。最新情報は各公募要領をご確認ください。</p>
            </div>
        </div>
    </section>
</main>

<!-- スクロール追従CTA -->
<div class="sticky-cta" id="sticky-cta" style="display:none">
    <div class="sticky-cta-inner">
        <span class="sticky-cta-text">御社に合った補助金を無料で診断</span>
        <a href="<?php echo $matching_url; ?>" class="btn btn-primary sticky-cta-btn">無料で補助金診断する →</a>
    </div>
</div>

<!-- 離脱防止ポップアップ -->
<div class="exit-modal-overlay" id="exit-modal" style="display:none">
    <div class="exit-modal">
        <button class="exit-modal-close" id="exit-modal-close" aria-label="閉じる">&times;</button>
        <h3 class="exit-modal-title">まだ診断していませんか？</h3>
        <p class="exit-modal-desc">1分で御社に合った補助金がわかります。<br>申請期限が迫っている補助金もあります。</p>
        <a href="<?php echo $matching_url; ?>" class="btn btn-primary exit-modal-btn">無料で補助金診断を始める</a>
        <p class="exit-modal-note">※ 登録不要・完全無料でご利用いただけます</p>
    </div>
</div>

<style>
.hero{background-color:#003366;color:#fff;padding:80px 0 60px;text-align:center}
.hero-label{font-size:14px;font-weight:500;letter-spacing:2px;margin-bottom:16px;opacity:.9}
.hero-title{font-size:32px;font-weight:700;color:#fff;margin-bottom:20px;line-height:1.5}
.hero-description{font-size:16px;margin-bottom:32px;opacity:.9;line-height:1.8}
.hero .btn-primary{background-color:#fff;color:#003366;font-weight:700;font-size:18px;padding:16px 48px}
.hero .btn-primary:hover{background-color:#F5F5F5}
.hero-note{font-size:13px;margin-top:12px;opacity:.7;margin-bottom:16px}
.social-proof{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:rgba(255,255,255,.8);margin-top:4px}
.social-proof-icon{color:rgba(255,255,255,.7);flex-shrink:0}
.social-proof strong{color:#fff;font-weight:700}
.authority-badges{background:#F5F5F5;padding:14px 0;border-bottom:1px solid #E0E0E0}
.authority-badges-inner{display:flex;justify-content:center;align-items:center;gap:24px;flex-wrap:wrap}
.authority-badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#666;font-weight:500}
.authority-badge svg{flex-shrink:0}
.urgency-banner{background:#FFF3F3;border-bottom:1px solid #FFCDD2;padding:12px 0}
.urgency-inner{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap}
.urgency-pulse{display:inline-block;width:10px;height:10px;background:#C62828;border-radius:50%;animation:urgencyPulse 1.5s ease-in-out infinite;flex-shrink:0}
@keyframes urgencyPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.3)}}
.urgency-text{font-size:14px;color:#333;font-weight:500}
.urgency-text strong{color:#C62828;font-weight:700;font-size:18px}
.urgency-link{font-size:13px;font-weight:600;color:#C62828;text-decoration:underline}
.pain-points{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.pain-point h3{font-size:17px;margin-bottom:10px;color:#003366}
.pain-point p{font-size:14px;color:#666;margin-bottom:0;line-height:1.7}
.mid-cta-section{padding:40px 0}
.mid-cta-text{font-size:18px;font-weight:600;color:#003366;margin-bottom:16px}
.btn-mid-cta{font-size:16px;padding:14px 40px}
.mid-cta-sub{font-size:12px;color:#999;margin-top:8px;margin-bottom:0}
.mid-cta-accent{background:#EBF0F5}
.faq-list{max-width:700px;margin:0 auto}
.faq-item{margin-bottom:12px}
.faq-q{font-size:15px;font-weight:600;color:#003366;margin-bottom:6px}
.faq-a{font-size:14px;color:#555;margin-bottom:0;line-height:1.7}
.steps{display:flex;align-items:center;justify-content:center;gap:24px}
.step{text-align:center;flex:1;max-width:260px;background:#fff;border:1px solid #ccc;border-radius:4px;padding:32px 24px}
.step-number{width:48px;height:48px;border-radius:50%;background-color:#003366;color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.step-title{font-size:18px;color:#003366;margin-bottom:8px}
.step-desc{font-size:14px;color:#666;margin-bottom:0}
.step-arrow{color:#ccc;font-size:20px}
.features{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.feature h3{font-size:18px;margin-bottom:12px}
.feature p{font-size:14px;color:#666;margin-bottom:0}
.sticky-cta{position:fixed;z-index:999;left:0;right:0;background:#003366;box-shadow:0 -2px 8px rgba(0,0,0,.15);padding:10px 0;transition:transform .3s ease}
.sticky-cta-inner{max-width:1100px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:center;gap:20px}
.sticky-cta-text{color:#fff;font-size:14px;font-weight:500}
.sticky-cta-btn{font-size:14px;padding:10px 28px;white-space:nowrap;background:#fff;color:#003366;font-weight:700;border-radius:4px;text-decoration:none}
.sticky-cta-btn:hover{background:#F0F0F0}
.exit-modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:10000;display:flex;align-items:center;justify-content:center;padding:20px;animation:fadeInOverlay .3s ease}
@keyframes fadeInOverlay{from{opacity:0}to{opacity:1}}
.exit-modal{background:#fff;border-radius:8px;padding:48px 40px 40px;max-width:460px;width:100%;text-align:center;position:relative;animation:slideUp .3s ease}
@keyframes slideUp{from{transform:translateY(24px);opacity:0}to{transform:translateY(0);opacity:1}}
.exit-modal-close{position:absolute;top:12px;right:16px;background:none;border:none;font-size:28px;color:#999;cursor:pointer;line-height:1;padding:4px}
.exit-modal-close:hover{color:#333}
.exit-modal-title{font-size:22px;font-weight:700;color:#003366;margin-bottom:12px}
.exit-modal-desc{font-size:15px;color:#555;line-height:1.7;margin-bottom:24px}
.exit-modal-btn{display:inline-block;font-size:16px;font-weight:700;padding:16px 40px}
.exit-modal-note{font-size:12px;color:#999;margin-top:12px;margin-bottom:0}
/* Facts section */
.section-facts{padding:80px 0;background:#fff}
.fact-card{display:flex;align-items:flex-start;gap:28px;background:#fff;border:1px solid #E0E0E0;border-left:4px solid #003366;border-radius:4px;padding:32px;margin-bottom:24px;max-width:860px;margin-left:auto;margin-right:auto}
.fact-card:last-of-type{margin-bottom:0}
.fact-number{font-size:64px;font-weight:700;color:#003366;line-height:1;flex-shrink:0;min-width:120px;text-align:center}
.fact-text h3{font-size:18px;color:#003366;margin-bottom:8px}
.fact-text p{font-size:15px;color:#555;line-height:1.8;margin-bottom:0}
/* Steps detail 5-step */
.section-steps-detail{padding:80px 0}
.steps-detail{display:flex;align-items:flex-start;justify-content:center;gap:0;max-width:1000px;margin:0 auto}
.step-detail{text-align:center;flex:1;max-width:180px;padding:0 8px}
.step-detail-number{width:48px;height:48px;border-radius:50%;background-color:#003366;color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 12px}
.step-detail-active .step-detail-number{background-color:#E8594F;box-shadow:0 0 0 4px rgba(232,89,79,.2)}
.step-detail-title{font-size:15px;color:#003366;margin-bottom:8px}
.step-detail-desc{font-size:13px;color:#666;line-height:1.7;margin-bottom:0}
.step-detail-line{width:32px;height:2px;background-color:#B0BEC5;flex-shrink:0;margin-top:24px}
/* Industry stories */
.section-industry-stories{padding:80px 0}
.industry-stories-lead{text-align:center;font-size:15px;color:#666;margin-bottom:32px}
.industry-tabs{display:flex;justify-content:center;gap:8px;margin-bottom:32px;flex-wrap:wrap}
.industry-tab{padding:10px 24px;font-size:14px;font-weight:700;font-family:inherit;color:#003366;background:#fff;border:2px solid #003366;border-radius:4px;cursor:pointer;transition:all .2s}
.industry-tab:hover{background-color:#E8F0F8}
.industry-tab.is-active{background-color:#003366;color:#fff}
.industry-panel{display:none;max-width:860px;margin:0 auto}
.industry-panel.is-active{display:block}
.industry-flow{display:flex;align-items:flex-start;gap:0;margin-bottom:28px}
.industry-flow-step{flex:1;background:#fff;border:1px solid #E0E0E0;border-radius:4px;padding:20px 16px;text-align:center}
.industry-flow-label{display:inline-block;font-size:11px;font-weight:700;color:#fff;background-color:#003366;padding:2px 10px;border-radius:2px;margin-bottom:8px}
.industry-flow-step p{font-size:14px;color:#333;margin-bottom:0;line-height:1.6}
.industry-flow-arrow{width:24px;flex-shrink:0;display:flex;align-items:center;justify-content:center;margin-top:36px}
.industry-flow-arrow::after{content:'▶';color:#B0BEC5;font-size:14px}
.industry-cost{display:flex;align-items:center;justify-content:center;gap:12px;background:#F0F4F8;border:1px solid #D0DAE5;border-radius:4px;padding:24px}
.industry-cost-row{text-align:center}
.industry-cost-label{display:block;font-size:12px;color:#666;margin-bottom:4px}
.industry-cost-value{font-size:20px;font-weight:700;color:#333}
.industry-cost-subsidy .industry-cost-value{color:#2E7D32}
.industry-cost-highlight{font-size:28px;color:#C62828!important}
.industry-cost-arrow{font-size:20px;color:#B0BEC5;flex-shrink:0}
.industry-stories-note{text-align:center;font-size:12px;color:#999;margin-top:20px;margin-bottom:0}
/* Data sources */
.section-data-sources{padding:40px 0;background-color:#F5F5F5}
.data-sources-box{max-width:760px;margin:0 auto;padding:28px 32px;background-color:#FAFAFA;border:1px solid #E0E0E0;border-radius:4px}
.data-sources-title{font-size:14px;font-weight:700;color:#666;margin-bottom:8px}
.data-sources-lead{font-size:13px;color:#666;margin-bottom:12px}
.data-sources-list{list-style:none;padding:0;margin:0 0 12px}
.data-sources-list li{font-size:13px;color:#666;padding:2px 0 2px 16px;position:relative}
.data-sources-list li::before{content:'・';position:absolute;left:0}
.data-sources-note{font-size:12px;color:#999;margin-bottom:0}
@media(max-width:768px){
.hero{padding:48px 0 40px}.hero-title{font-size:24px}.hero-title br,.hero-description br{display:none}
.steps{flex-direction:column}.step{max-width:100%;width:100%}.step-arrow{transform:rotate(90deg)}
.features,.pain-points{grid-template-columns:1fr}
.authority-badges-inner{gap:12px}.authority-badge{font-size:11px}
.sticky-cta{bottom:0;top:auto}.sticky-cta-text{display:none}
.sticky-cta-btn{width:100%;text-align:center;padding:14px 20px;font-size:15px}
.mid-cta-text{font-size:16px}
.exit-modal{padding:36px 24px 32px}.exit-modal-title{font-size:19px}.exit-modal-desc br{display:none}
.fact-card{flex-direction:column;gap:12px;padding:24px}.fact-number{font-size:48px;min-width:auto}
.steps-detail{flex-direction:column;align-items:center}.step-detail{max-width:100%;width:100%}.step-detail-line{width:2px;height:24px;margin:8px auto}
.industry-tabs{gap:6px}.industry-tab{padding:8px 14px;font-size:13px}
.industry-flow{flex-direction:column}.industry-flow-arrow{margin-top:0;height:24px;width:100%}.industry-flow-arrow::after{content:'▼'}
.industry-cost{flex-direction:column;gap:8px;padding:20px 16px}.industry-cost-arrow{transform:rotate(90deg)}
}
@media(min-width:769px){.sticky-cta{top:0;bottom:auto}}
</style>

<script>
(function(){
'use strict';
// Social proof counter
(function(){var el=document.getElementById('proof-count');if(!el)return;var h=new Date().getHours(),b=80;if(h>=9&&h<=12)b=140;else if(h>=13&&h<=18)b=170;else if(h>=19&&h<=22)b=120;var d=new Date().getDate(),s=(h*7+d*13)%50;el.textContent=Math.max(50,Math.min(200,b+s))})();
// Deadline urgency
(function(){var bn=document.getElementById('urgency-banner'),ct=document.getElementById('deadline-count');if(!bn||!ct)return;fetch('<?php echo esc_url_raw(rest_url("subsidy/v1/deadline-count")); ?>').then(function(r){return r.json()}).then(function(d){if(d.success&&d.count>0){ct.textContent=d.count;bn.style.display='block'}}).catch(function(){ct.textContent=3+(new Date().getMonth()%5);bn.style.display='block'})})();
// Sticky CTA
(function(){var sc=document.getElementById('sticky-cta'),hr=document.querySelector('.hero');if(!sc||!hr)return;var hb=hr.offsetTop+hr.offsetHeight,sh=false;function ck(){var sy=window.pageYOffset||document.documentElement.scrollTop;if(sy>hb&&!sh){sc.style.display='block';sh=true}else if(sy<=hb&&sh){sc.style.display='none';sh=false}}window.addEventListener('scroll',ck,{passive:true});ck()})();
// Industry tabs
(function(){var tabs=document.querySelectorAll('.industry-tab');if(!tabs.length)return;tabs.forEach(function(tab){tab.addEventListener('click',function(){tabs.forEach(function(t){t.classList.remove('is-active')});tab.classList.add('is-active');var panels=document.querySelectorAll('.industry-panel');panels.forEach(function(p){p.classList.remove('is-active')});var target=document.getElementById('panel-'+tab.getAttribute('data-tab'));if(target)target.classList.add('is-active')})})})();
// Exit popup
(function(){var m=document.getElementById('exit-modal'),c=document.getElementById('exit-modal-close');if(!m)return;var CN='exit_popup_shown',t=false;if(document.cookie.indexOf(CN+'=1')!==-1)return;function show(){if(t)return;t=true;m.style.display='flex';var d=new Date();d.setTime(d.getTime()+864e5);document.cookie=CN+'=1;expires='+d.toUTCString()+';path=/'}function hide(){m.style.display='none'}document.addEventListener('mouseleave',function(e){if(e.clientY<10)show()});if(c)c.addEventListener('click',hide);m.addEventListener('click',function(e){if(e.target===m)hide()});document.addEventListener('keydown',function(e){if(e.key==='Escape')hide()})})();
})();
</script>

<?php get_footer(); ?>
