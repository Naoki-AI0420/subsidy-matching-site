<?php
/**
 * Template Name: プライバシーポリシー
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main class="privacy-page">
    <div class="container">
        <h1 class="privacy-title">プライバシーポリシー（個人情報保護方針）</h1>

        <div class="privacy-content">
            <p class="privacy-lead">
                補助金・助成金マッチングサービス（以下「本サービス」といいます。）を運営する Growing Up AI Inc.（以下「当社」といいます。）は、
                お客様の個人情報の保護を重要な責務と認識し、以下のとおり個人情報保護方針を定め、適切な管理・保護に努めてまいります。
            </p>

            <section class="privacy-section">
                <h2>1. 個人情報の定義</h2>
                <p>
                    本ポリシーにおいて「個人情報」とは、個人情報保護法に定める個人情報を指し、
                    氏名、メールアドレス、電話番号、その他の記述等により特定の個人を識別できる情報をいいます。
                </p>
            </section>

            <section class="privacy-section">
                <h2>2. 個人情報の収集目的</h2>
                <p>当社は、以下の目的のために個人情報を収集いたします。</p>
                <ul>
                    <li>補助金・助成金の診断結果のご提供</li>
                    <li>診断結果に基づく補助金申請サポートのご案内</li>
                    <li>お問い合わせへの回答およびご連絡</li>
                    <li>サービスの改善および新サービスの開発</li>
                    <li>利用状況の統計・分析（個人を特定しない形での利用）</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>3. 個人情報の利用目的</h2>
                <p>
                    当社は、収集した個人情報を前項に定める目的の範囲内でのみ利用いたします。
                    利用目的を変更する場合は、変更後の目的について本人の同意を得た上で行います。
                </p>
            </section>

            <section class="privacy-section">
                <h2>4. 第三者への提供</h2>
                <p>
                    当社は、以下の場合を除き、お客様の個人情報を第三者に提供することはありません。
                </p>
                <ul>
                    <li>お客様ご本人の同意がある場合</li>
                    <li>法令に基づく場合</li>
                    <li>人の生命、身体または財産の保護のために必要がある場合であって、本人の同意を得ることが困難であるとき</li>
                    <li>国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>5. 個人情報の管理体制</h2>
                <p>当社は、個人情報の正確性および安全性を確保するため、以下の措置を講じます。</p>
                <ul>
                    <li>SSL（Secure Sockets Layer）通信による情報の暗号化</li>
                    <li>不正アクセス、紛失、破損、改ざんおよび漏洩の防止のための適切なセキュリティ対策の実施</li>
                    <li>個人情報を取り扱う従業者への教育および監督</li>
                    <li>個人情報の取り扱いに関する規程の整備および運用</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>6. Cookie（クッキー）の使用について</h2>
                <p>
                    本サービスでは、サービスの利便性向上およびアクセス解析のためにCookieを使用する場合があります。
                    Cookieの使用により個人を特定する情報を収集することはありません。
                    お客様のブラウザ設定によりCookieの受け取りを拒否することも可能ですが、
                    一部のサービスが正常に機能しない場合がございます。
                </p>
            </section>

            <section class="privacy-section">
                <h2>7. アクセス解析ツールについて</h2>
                <p>
                    本サービスでは、Googleによるアクセス解析ツール「Google Analytics」を使用する場合があります。
                    Google Analyticsはデータの収集のためにCookieを使用しますが、このデータは匿名で収集されており、
                    個人を特定するものではありません。
                    詳細については<a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener">Google のポリシーと規約</a>をご確認ください。
                </p>
            </section>

            <section class="privacy-section">
                <h2>8. 個人情報の開示・訂正・削除</h2>
                <p>
                    お客様ご本人から個人情報の開示、訂正、追加、削除、利用停止のご請求があった場合は、
                    ご本人であることを確認の上、速やかに対応いたします。
                </p>
            </section>

            <section class="privacy-section">
                <h2>9. プライバシーポリシーの変更</h2>
                <p>
                    当社は、法令の改正やサービス内容の変更等に伴い、本ポリシーを予告なく改定する場合があります。
                    改定後のプライバシーポリシーは、本ページに掲載した時点から効力を生じるものとします。
                </p>
            </section>

            <section class="privacy-section">
                <h2>10. お問い合わせ先</h2>
                <p>個人情報の取り扱いに関するお問い合わせは、下記までご連絡ください。</p>
                <div class="privacy-contact">
                    <p>
                        <strong>Growing Up AI Inc.</strong><br>
                        所在地: 東京都<br>
                        メール: <a href="mailto:info@yumeno-marketing.jp">info@yumeno-marketing.jp</a><br>
                        お問い合わせフォーム: <a href="<?php echo esc_url(home_url('/contact/')); ?>">こちら</a>
                    </p>
                </div>
            </section>

            <p class="privacy-date">制定日: 2026年3月21日</p>
        </div>
    </div>
</main>

<style>
.privacy-page {
    padding: 60px 0 80px;
    min-height: 70vh;
}
.privacy-title {
    font-size: 24px;
    font-weight: 700;
    color: #003366;
    text-align: center;
    margin-bottom: 48px;
    padding-bottom: 16px;
    border-bottom: 3px solid #003366;
}
.privacy-content {
    max-width: 800px;
    margin: 0 auto;
}
.privacy-lead {
    font-size: 15px;
    line-height: 1.8;
    color: #333333;
    margin-bottom: 40px;
    padding: 20px 24px;
    background-color: #F8F9FA;
    border-left: 4px solid #003366;
    border-radius: 2px;
}
.privacy-section {
    margin-bottom: 32px;
}
.privacy-section h2 {
    font-size: 18px;
    font-weight: 700;
    color: #003366;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #E0E0E0;
}
.privacy-section p {
    font-size: 14px;
    line-height: 1.8;
    color: #333333;
}
.privacy-section ul {
    list-style: none;
    padding: 0;
    margin: 12px 0;
}
.privacy-section ul li {
    font-size: 14px;
    line-height: 1.8;
    color: #333333;
    padding: 4px 0 4px 20px;
    position: relative;
}
.privacy-section ul li::before {
    content: '\25CF';
    position: absolute;
    left: 0;
    color: #003366;
    font-size: 8px;
    top: 10px;
}
.privacy-contact {
    background-color: #F8F9FA;
    border: 1px solid #E0E0E0;
    border-radius: 4px;
    padding: 20px 24px;
    margin-top: 12px;
}
.privacy-contact p {
    margin: 0;
}
.privacy-date {
    text-align: right;
    font-size: 13px;
    color: #888888;
    margin-top: 40px;
}
@media (max-width: 768px) {
    .privacy-page { padding: 40px 0 60px; }
    .privacy-title { font-size: 20px; }
    .privacy-lead { padding: 16px; font-size: 14px; }
}
</style>

<?php get_footer(); ?>
