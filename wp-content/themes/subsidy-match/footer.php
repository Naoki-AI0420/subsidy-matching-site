<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-section">
                <h4>補助金・助成金マッチングサービスについて</h4>
                <p>
                    本サービスは、中小企業・小規模事業者の皆様が活用可能な補助金・助成金を
                    簡単に検索できるマッチングサービスです。経済産業省、厚生労働省、
                    各自治体等が実施する補助金・助成金情報を掲載しております。
                </p>
            </div>
            <div class="footer-section">
                <h4>サイトメニュー</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">トップページ</a></li>
                    <li><a href="<?php echo esc_url(home_url('/matching/')); ?>">補助金診断</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>運営情報</h4>
                <ul>
                    <li>運営: Growing Up</li>
                    <li>所在地: 東京都</li>
                    <li>お問い合わせ: <a href="<?php echo esc_url(home_url('/contact/')); ?>">こちら</a></li>
                    <li><a href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-disclaimer">
            <p>※本サイトの情報は各省庁・自治体の公開情報に基づいています。補助金の詳細・最新情報は必ず各実施機関の公式サイトをご確認ください。</p>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
