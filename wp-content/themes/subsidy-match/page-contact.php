<?php
/**
 * Template Name: お問い合わせ
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main class="section">
    <div class="container">
        <div class="contact-page">
            <h1 class="section-title">お問い合わせ</h1>
            <p class="text-center text-muted mb-24">
                補助金・助成金の活用に関するご相談、サービスに関するお問い合わせは<br>
                下記フォームよりお気軽にお問い合わせください。
            </p>

            <div class="contact-staff-box">
                <div class="staff-avatar-row">
                    <div class="staff-avatar">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                    </div>
                    <div class="staff-speech-bubble">
                        専門スタッフが丁寧にご案内いたします。お気軽にご相談ください。
                    </div>
                </div>
            </div>

            <form class="contact-form" id="contact-form">
                <div class="form-group">
                    <label for="company_name">会社名 <span class="required">必須</span></label>
                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="contact_name">ご担当者名 <span class="required">必須</span></label>
                    <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                </div>

                <div class="form-group">
                    <label for="contact_email">メールアドレス <span class="required">必須</span></label>
                    <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                </div>

                <div class="form-group">
                    <label for="contact_phone">電話番号</label>
                    <input type="tel" class="form-control" id="contact_phone" name="contact_phone">
                </div>

                <div class="form-group">
                    <label for="contact_message">ご相談内容 <span class="required">必須</span></label>
                    <textarea class="form-control" id="contact_message" name="contact_message" rows="6" required
                              placeholder="補助金の活用方法、申請に関するご相談など、お気軽にお書きください。"></textarea>
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-large">送信する</button>
                </div>

                <div class="form-message" id="form-message" style="display:none"></div>
            </form>
        </div>
    </div>
</main>

<style>
.contact-page {
    max-width: 640px;
    margin: 0 auto;
}

.required {
    font-size: 12px;
    color: #C62828;
    margin-left: 4px;
}

.form-message {
    text-align: center;
    padding: 16px;
    border-radius: 4px;
    margin-top: 20px;
    font-size: 14px;
}

.form-message.success {
    background-color: #E8F5E9;
    color: #2E7D32;
    border: 1px solid #A5D6A7;
}

.form-message.error {
    background-color: #FFEBEE;
    color: #C62828;
    border: 1px solid #EF9A9A;
}

.contact-staff-box {
    max-width: 480px;
    margin: 0 auto 32px;
}
</style>

<?php get_footer(); ?>
