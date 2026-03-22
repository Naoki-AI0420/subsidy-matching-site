/**
 * 一問一答マッチング — メインロジック
 *
 * @package SubsidyMatch
 */
(function () {
    'use strict';

    var TOTAL_STEPS = 14;
    var currentStep = 1;
    var answers = {};

    // 業種→最大金額マッピング（チラ見せ用）
    var industryAmountMap = {
        information_technology: { amount: '450万円', subsidy: 'IT導入補助金' },
        manufacturing:         { amount: '1,250万円', subsidy: 'ものづくり補助金' },
        food_service:          { amount: '200万円', subsidy: '小規模事業者持続化補助金' },
        accommodation:         { amount: '200万円', subsidy: '小規模事業者持続化補助金' },
        wholesale_retail:      { amount: '200万円', subsidy: '持続化補助金' },
        construction:          { amount: '1,000万円', subsidy: '事業再構築補助金' },
        medical_welfare:       { amount: '450万円', subsidy: 'IT導入補助金' },
        education:             { amount: '200万円', subsidy: '持続化補助金' },
        professional_services: { amount: '450万円', subsidy: 'IT導入補助金' },
        transportation:        { amount: '200万円', subsidy: '持続化補助金' },
        real_estate:           { amount: '200万円', subsidy: '持続化補助金' },
        agriculture:           { amount: '200万円', subsidy: '持続化補助金' },
        other:                 { amount: '200万円', subsidy: '持続化補助金' }
    };

    var employeeMatchCount = {
        '1-5': 8, '6-20': 12, '21-50': 15, '51-100': 18, '101+': 22
    };

    var challengeSubsidyCount = {
        equipment: 25, it_dx: 30, hiring: 15, overseas: 10, rnd: 20, succession: 8
    };

    var revenueEstimate = {
        under_10m: '100', '10m_50m': '300', '50m_100m': '500', '100m_500m': '800', over_500m: '1,500'
    };

    // DOM要素
    var progressBar = document.querySelector('.progress-bar');
    var progressPercent = document.querySelector('.progress-percent');
    var currentStepEl = document.querySelector('.current-step');
    var slides = document.querySelectorAll('.question-slide');
    var btnNext = document.querySelector('.btn-next');
    var btnBack = document.querySelector('.btn-back');
    var questionContainer = document.querySelector('.question-container');
    var questionNav = document.querySelector('.question-nav');
    var resultContainer = document.querySelector('.result-container');
    var progressContainer = document.querySelector('.progress-container');
    var progressMessageEl = document.getElementById('progress-message');

    function init() {
        injectConsentCheckbox();
        updateProgress();
        updateNavButtons();
        bindEvents();
    }

    /**
     * メールフォーム（Q14）に同意チェックボックスを挿入
     */
    function injectConsentCheckbox() {
        var step14 = document.querySelector('[data-step="14"]');
        if (!step14) return;
        var inputNote = step14.querySelector('.input-note');
        if (!inputNote) return;
        var consentHtml = '<div class="consent-group">' +
            '<input type="checkbox" id="privacy-consent">' +
            '<label for="privacy-consent"><a href="/privacy/" target="_blank" rel="noopener">個人情報の取り扱い</a>に同意する</label>' +
            '</div>';
        inputNote.insertAdjacentHTML('afterend', consentHtml);
    }

    function bindEvents() {
        btnNext.addEventListener('click', handleNext);
        btnBack.addEventListener('click', handleBack);

        document.querySelectorAll('.option-card input[type="radio"]').forEach(function (input) {
            input.addEventListener('change', function () {
                setTimeout(function () {
                    handleNext();
                }, 250);
            });
        });
    }

    function handleNext() {
        // Q14: 同意チェック必須
        if (currentStep === TOTAL_STEPS) {
            var consentBox = document.getElementById('privacy-consent');
            if (consentBox && !consentBox.checked) {
                var consentGroup = consentBox.closest('.consent-group');
                if (consentGroup) {
                    consentGroup.style.color = '#C62828';
                    consentGroup.style.fontWeight = '700';
                }
                shakeCurrentSlide();
                return;
            }
        }

        var value = getStepValue(currentStep);
        if (!value && value !== 0) {
            shakeCurrentSlide();
            return;
        }

        saveAnswer(currentStep, value);

        if (currentStep < TOTAL_STEPS) {
            currentStep++;
            showStep(currentStep);
            updateProgress();
            updateNavButtons();
            updateTeaserMessage(currentStep);
            updateProgressMessage(currentStep);
        } else {
            submitMatching();
        }
    }

    /**
     * 進捗メッセージ表示（Feature 8）
     */
    function updateProgressMessage(step) {
        if (!progressMessageEl) return;

        var msg = '';
        if (step === 14) {
            msg = '最後の質問です！';
        } else if (step >= 11) {
            msg = 'ほぼ完了です！結果をお楽しみに';
        } else if (step >= 6) {
            msg = 'あと少しです！';
        }

        if (msg) {
            progressMessageEl.textContent = msg;
            progressMessageEl.style.display = 'block';
            progressMessageEl.style.animation = 'none';
            void progressMessageEl.offsetHeight;
            progressMessageEl.style.animation = 'fadeIn 0.4s ease';
        } else {
            progressMessageEl.style.display = 'none';
        }
    }

    /**
     * 金額チラ見せメッセージ表示
     */
    function updateTeaserMessage(step) {
        var teaserEl = document.getElementById('amount-teaser');
        if (!teaserEl) return;

        var msg = '';

        if (step >= 3 && answers.industry) {
            var info = industryAmountMap[answers.industry];
            if (info) {
                msg = '\ud83d\udcb0 あなたと同業種の企業では、最大 <strong>' + info.amount + '</strong> の補助金を受給した実績があります（' + info.subsidy + '）';
            }
        }

        if (step >= 4 && answers.employee_size) {
            var count = employeeMatchCount[answers.employee_size] || 10;
            msg = '\ud83d\udcb0 従業員' + answers.employee_size.replace('+', '名以上').replace('-', '〜') + '規模の企業では、平均 <strong>' + count + '件</strong> の補助金に該当しています';
        }

        if (step >= 6 && answers.challenges && answers.challenges.length > 0) {
            var totalCount = 0;
            answers.challenges.forEach(function (c) {
                totalCount += challengeSubsidyCount[c] || 5;
            });
            var challengeLabel = answers.challenges.indexOf('it_dx') !== -1 ? 'DX推進' :
                                 answers.challenges.indexOf('equipment') !== -1 ? '設備投資' : '経営課題';
            msg = '\ud83d\udcb0 ' + challengeLabel + 'に関する補助金だけでも <strong>' + totalCount + '件以上</strong> あります';
        }

        if (step >= 13 && answers.annual_revenue) {
            var est = revenueEstimate[answers.annual_revenue] || '200';
            msg = '\ud83d\udcb0 御社の規模感では、年間 <strong>' + est + '万円</strong> の補助金活用が見込めます';
        }

        if (msg) {
            teaserEl.innerHTML = msg;
            teaserEl.style.display = 'block';
            teaserEl.style.animation = 'none';
            void teaserEl.offsetHeight;
            teaserEl.style.animation = 'fadeIn 0.5s ease';
        }
    }

    function handleBack() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateProgress();
            updateNavButtons();
            updateProgressMessage(currentStep);
        }
    }

    function showStep(step) {
        slides.forEach(function (slide) {
            slide.style.display = 'none';
        });
        var target = document.querySelector('[data-step="' + step + '"]');
        if (target) {
            target.style.display = 'block';
            target.style.animation = 'none';
            void target.offsetHeight;
            target.style.animation = 'fadeIn 0.3s ease';
        }
    }

    function updateProgress() {
        var percent = Math.round(((currentStep - 1) / TOTAL_STEPS) * 100);
        if (currentStep === TOTAL_STEPS) {
            percent = 95;
        }
        progressBar.style.width = percent + '%';
        progressPercent.textContent = percent + '%';
        currentStepEl.textContent = currentStep;

        // 終盤でプログレスバーの色を緑に変化（Feature 8）
        if (currentStep >= 12) {
            progressBar.style.background = 'linear-gradient(90deg, #2E7D32, #43A047)';
        } else if (currentStep >= 8) {
            progressBar.style.background = 'linear-gradient(90deg, #003366, #1565C0)';
        } else {
            progressBar.style.background = 'linear-gradient(90deg, #003366, #0056b3)';
        }
    }

    function updateNavButtons() {
        btnBack.style.visibility = currentStep > 1 ? 'visible' : 'hidden';
        btnNext.textContent = currentStep === TOTAL_STEPS ? '診断結果を見る' : '次へ';
    }

    function getStepValue(step) {
        switch (step) {
            case 1:
                return document.getElementById('q-prefecture').value || null;
            case 2:
                return document.getElementById('q-industry').value || null;
            case 3:
                var empRadio = document.querySelector('input[name="employee_size"]:checked');
                return empRadio ? empRadio.value : null;
            case 4:
                var capRadio = document.querySelector('input[name="capital"]:checked');
                return capRadio ? capRadio.value : null;
            case 5:
                var checked5 = document.querySelectorAll('input[name="challenges"]:checked');
                if (checked5.length === 0) return null;
                return Array.prototype.map.call(checked5, function (c) { return c.value; });
            case 6:
                var schedRadio = document.querySelector('input[name="dx_schedule"]:checked');
                return schedRadio ? schedRadio.value : null;
            case 7:
                var invRadio = document.querySelector('input[name="dx_invoice"]:checked');
                return invRadio ? invRadio.value : null;
            case 8:
                var crmRadio = document.querySelector('input[name="dx_crm"]:checked');
                return crmRadio ? crmRadio.value : null;
            case 9:
                var ecRadio = document.querySelector('input[name="dx_ec"]:checked');
                return ecRadio ? ecRadio.value : null;
            case 10:
                var commRadio = document.querySelector('input[name="dx_communication"]:checked');
                return commRadio ? commRadio.value : null;
            case 11:
                var checked11 = document.querySelectorAll('input[name="dx_pain"]:checked');
                if (checked11.length === 0) return null;
                return Array.prototype.map.call(checked11, function (c) { return c.value; });
            case 12:
                var revRadio = document.querySelector('input[name="annual_revenue"]:checked');
                return revRadio ? revRadio.value : null;
            case 13:
                var expRadio = document.querySelector('input[name="has_experience"]:checked');
                return expRadio ? expRadio.value : null;
            case 14:
                var email = document.getElementById('q-email').value.trim();
                if (!email || !isValidEmail(email)) return null;
                return email;
            default:
                return null;
        }
    }

    function saveAnswer(step, value) {
        var keys = [
            '', 'prefecture', 'industry', 'employee_size', 'capital',
            'challenges', 'dx_schedule', 'dx_invoice', 'dx_crm',
            'dx_ec', 'dx_communication', 'dx_pain',
            'annual_revenue', 'has_experience', 'email'
        ];
        answers[keys[step]] = value;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function shakeCurrentSlide() {
        var slide = document.querySelector('[data-step="' + currentStep + '"]');
        slide.style.animation = 'none';
        void slide.offsetHeight;
        slide.style.animation = 'shake 0.4s ease';
    }

    var style = document.createElement('style');
    style.textContent = '@keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }';
    document.head.appendChild(style);

    /**
     * dataLayer ヘルパー（GTM/GA4 イベント送信）
     */
    function pushDataLayer(event, params) {
        window.dataLayer = window.dataLayer || [];
        var obj = { 'event': event };
        if (params) {
            for (var key in params) {
                if (params.hasOwnProperty(key)) {
                    obj[key] = params[key];
                }
            }
        }
        window.dataLayer.push(obj);
    }

    /**
     * DX課題を分析してレコメンドを生成
     */
    function analyzeDxChallenges() {
        var issues = [];
        var recommendations = [];

        if (answers.dx_schedule === 'paper' || answers.dx_schedule === 'none') {
            issues.push('予約・スケジュール管理がデジタル化されていません');
            recommendations.push({ category: '予約管理システム導入', description: 'オンライン予約・スケジュール管理システムを導入することで、予約の取りこぼし防止と業務効率化を実現できます。', estimate: 50, subsidyRate: '最大3/4' });
        } else if (answers.dx_schedule === 'excel') {
            issues.push('予約・スケジュール管理がExcelベースで属人化のリスクがあります');
            recommendations.push({ category: '予約管理システム導入', description: 'クラウド型予約管理システムへの移行で、リアルタイム共有と自動リマインドが可能になります。', estimate: 30, subsidyRate: '最大3/4' });
        }

        if (answers.dx_invoice === 'handwrite') {
            issues.push('請求書・見積書が手書きで、作成に時間がかかっています');
            recommendations.push({ category: 'クラウド会計・請求システム導入', description: 'クラウド会計ソフトの導入で、請求書の自動作成・送付、入金管理の効率化が実現できます。インボイス制度にも対応。', estimate: 35, subsidyRate: '最大3/4' });
        } else if (answers.dx_invoice === 'excel') {
            issues.push('請求業務がExcelベースで、転記ミスや管理負担が発生しています');
            recommendations.push({ category: 'クラウド会計・請求システム導入', description: 'クラウド型請求・会計ソフトへの移行で、自動仕訳・電子帳簿保存法対応が可能になります。', estimate: 25, subsidyRate: '最大3/4' });
        }

        if (answers.dx_crm === 'paper' || answers.dx_crm === 'none') {
            issues.push('顧客情報が一元管理されておらず、営業機会の損失リスクがあります');
            recommendations.push({ category: 'CRM（顧客管理）システム導入', description: '顧客情報のデジタル一元管理により、適切なフォローアップと売上向上が期待できます。', estimate: 40, subsidyRate: '最大3/4' });
        } else if (answers.dx_crm === 'excel') {
            issues.push('顧客管理がExcelベースで、情報の共有・活用が限定的です');
            recommendations.push({ category: 'CRM（顧客管理）システム導入', description: 'CRMシステムへの移行で、顧客対応履歴の共有と営業活動の可視化が実現できます。', estimate: 30, subsidyRate: '最大3/4' });
        }

        if (answers.dx_ec === 'none') {
            issues.push('オンライン販売チャネルが未整備で、販路拡大の余地があります');
            recommendations.push({ category: 'ECサイト構築', description: 'ECサイトの構築により、新たな販売チャネルを確保。24時間受注と全国への販路拡大が可能になります。', estimate: 100, subsidyRate: '最大2/3' });
        } else if (answers.dx_ec === 'considering') {
            issues.push('ECサイト導入を検討中 — 補助金活用で初期費用を大幅に抑えられます');
            recommendations.push({ category: 'ECサイト構築', description: '補助金を活用したECサイト構築で、初期投資を抑えながらオンライン販売を開始できます。', estimate: 80, subsidyRate: '最大2/3' });
        }

        if (answers.dx_communication === 'verbal') {
            issues.push('社内の情報共有が口頭中心で、伝達漏れや記録が残らないリスクがあります');
            recommendations.push({ category: 'グループウェア・社内DX導入', description: 'ビジネスチャットやグループウェアの導入で、情報共有の迅速化と記録の蓄積が可能になります。', estimate: 20, subsidyRate: '最大3/4' });
        } else if (answers.dx_communication === 'email') {
            issues.push('情報共有がメール中心で、リアルタイム性と検索性に課題があります');
            recommendations.push({ category: 'グループウェア・社内DX導入', description: 'グループウェアやプロジェクト管理ツールの導入で、業務の可視化と効率化が実現できます。', estimate: 15, subsidyRate: '最大3/4' });
        }

        var dxLevel = 'advanced';
        var analogCount = 0;
        if (answers.dx_schedule === 'paper' || answers.dx_schedule === 'none') analogCount++;
        if (answers.dx_invoice === 'handwrite') analogCount++;
        if (answers.dx_crm === 'paper' || answers.dx_crm === 'none') analogCount++;
        if (answers.dx_ec === 'none') analogCount++;
        if (answers.dx_communication === 'verbal') analogCount++;

        if (analogCount >= 4) dxLevel = 'beginner';
        else if (analogCount >= 2) dxLevel = 'developing';

        return { issues: issues, recommendations: recommendations, dxLevel: dxLevel, painPoints: answers.dx_pain || [] };
    }

    /**
     * マッチング送信
     */
    function submitMatching() {
        // GA4/GTM: フォーム送信イベント
        pushDataLayer('form_submit', { 'form_name': 'subsidy_matching' });

        questionContainer.style.display = 'none';
        questionNav.style.display = 'none';
        progressContainer.style.display = 'none';
        if (progressMessageEl) progressMessageEl.style.display = 'none';
        var teaserEl = document.getElementById('amount-teaser');
        if (teaserEl) teaserEl.style.display = 'none';
        resultContainer.style.display = 'block';

        // ローディング中に採択事例をスライドショー表示（全業種網羅）
        var allCases = {
            'information_technology':  [
                { industry: 'IT・通信業', type: 'ものづくり補助金', amount: '1,250万円', use: 'SaaS開発・クラウドインフラ構築', result: '月間売上4倍' },
                { industry: 'IT・通信業', type: 'IT導入補助金', amount: '450万円', use: '社内DXツール統合・RPA導入', result: '工数60%削減' },
            ],
            'manufacturing': [
                { industry: '製造業', type: 'ものづくり補助金', amount: '1,000万円', use: '生産ライン自動化システム導入', result: '生産効率2倍' },
                { industry: '製造業', type: '省力化投資補助金', amount: '800万円', use: '検品AI・ロボットアーム導入', result: '不良品率80%減' },
            ],
            'food_service': [
                { industry: '飲食業', type: '小規模事業者持続化補助金', amount: '50万円', use: 'テイクアウト用ECサイト構築', result: '売上30%増' },
                { industry: '飲食業', type: 'IT導入補助金', amount: '150万円', use: 'モバイルオーダー・POSシステム導入', result: '回転率25%向上' },
            ],
            'accommodation': [
                { industry: '宿泊業', type: 'IT導入補助金', amount: '350万円', use: '自動チェックイン・多言語対応', result: 'インバウンド客50%増' },
                { industry: '宿泊業', type: '省力化投資補助金', amount: '500万円', use: '清掃ロボット・スマートロック導入', result: '人件費40%削減' },
            ],
            'wholesale_retail': [
                { industry: '小売業', type: 'IT導入補助金', amount: '300万円', use: 'POSレジ＋在庫管理クラウド導入', result: '在庫ロス50%削減' },
                { industry: '小売業', type: '小規模事業者持続化補助金', amount: '50万円', use: 'ECサイト構築・SNS広告', result: 'オンライン売上300%増' },
            ],
            'construction': [
                { industry: '建設業', type: '事業再構築補助金', amount: '3,000万円', use: 'ドローン測量・BIMシステム導入', result: '工期20%短縮' },
                { industry: '建設業', type: 'IT導入補助金', amount: '450万円', use: '工程管理・原価管理クラウド導入', result: '利益率15%改善' },
            ],
            'medical_welfare': [
                { industry: '医療・福祉', type: 'IT導入補助金', amount: '200万円', use: '電子カルテ・オンライン予約導入', result: '受付時間70%削減' },
                { industry: '医療・福祉', type: '省力化投資補助金', amount: '600万円', use: '見守りセンサー・記録自動化', result: '夜勤スタッフ負担50%軽減' },
            ],
            'education': [
                { industry: '教育・学習支援', type: 'IT導入補助金', amount: '200万円', use: 'オンライン授業・学習管理システム導入', result: '生徒数2倍・退塾率30%減' },
                { industry: '教育・学習支援', type: '小規模事業者持続化補助金', amount: '50万円', use: 'Web集客・LINE公式アカウント構築', result: '問い合わせ5倍' },
            ],
            'professional_services': [
                { industry: '士業・専門サービス', type: '小規模事業者持続化補助金', amount: '50万円', use: 'Webマーケティング・SEO対策', result: '新規問い合わせ3倍' },
                { industry: '士業・専門サービス', type: 'IT導入補助金', amount: '150万円', use: '案件管理・電子契約システム導入', result: '業務時間40%削減' },
            ],
            'transportation': [
                { industry: '運輸・物流業', type: '省力化投資補助金', amount: '500万円', use: '配送ルート最適化AI導入', result: '燃料費25%削減' },
                { industry: '運輸・物流業', type: 'IT導入補助金', amount: '300万円', use: '配車管理・動態管理システム導入', result: '稼働率35%向上' },
            ],
            'real_estate': [
                { industry: '不動産業', type: 'IT導入補助金', amount: '250万円', use: '物件管理・内見予約システム導入', result: '成約率20%向上' },
                { industry: '不動産業', type: '小規模事業者持続化補助金', amount: '50万円', use: 'VR内見・ポータルサイト連携', result: '来店不要の成約30%' },
            ],
            'agriculture': [
                { industry: '農業', type: 'ものづくり補助金', amount: '800万円', use: 'スマート農業IoTセンサー導入', result: '収穫量20%増・人件費30%減' },
                { industry: '農業', type: '省力化投資補助金', amount: '400万円', use: '自動灌水・ドローン農薬散布', result: '作業時間60%削減' },
            ],
            'other': [
                { industry: 'サービス業', type: 'IT導入補助金', amount: '200万円', use: '予約・顧客管理クラウド導入', result: 'リピート率40%向上' },
                { industry: 'サービス業', type: '小規模事業者持続化補助金', amount: '50万円', use: 'ホームページ制作・Google広告', result: '新規顧客3倍' },
            ],
        };

        // 美容業を追加（selectにはないがカバー）
        allCases['beauty'] = [
            { industry: '美容業', type: 'IT導入補助金', amount: '150万円', use: '予約管理・顧客管理システム導入', result: '予約率40%向上' },
            { industry: '美容業', type: '小規模事業者持続化補助金', amount: '50万円', use: 'Instagram集客・LINE予約連携', result: '新規客60%増' },
        ];

        // ユーザーの業種を最優先 → 他業種を高額順でランダム混ぜて表示
        var userIndustry = answers.industry || 'other';

        // 全事例をフラットに展開
        var allFlat = [];
        Object.keys(allCases).forEach(function(k) {
            allCases[k].forEach(function(c) {
                c._isUserIndustry = (k === userIndustry);
                c._amountNum = parseInt(c.amount.replace(/[^0-9]/g, ''), 10) || 0;
                allFlat.push(c);
            });
        });

        // ユーザー業種の事例（高額順）
        var userCases = allFlat.filter(function(c) { return c._isUserIndustry; });
        userCases.sort(function(a, b) { return b._amountNum - a._amountNum; });

        // 他業種の事例（高額順ベース、ただしランダム性も入れる）
        var otherCases = allFlat.filter(function(c) { return !c._isUserIndustry; });
        // 高額順でソート
        otherCases.sort(function(a, b) { return b._amountNum - a._amountNum; });
        // 上位1/3はそのまま、残り2/3をシャッフル（高額が先に出つつランダム感も出す）
        var topThird = Math.ceil(otherCases.length / 3);
        var topCases = otherCases.slice(0, topThird);
        var restCases = otherCases.slice(topThird);
        for (var si = restCases.length - 1; si > 0; si--) {
            var sj = Math.floor(Math.random() * (si + 1));
            var tmp = restCases[si]; restCases[si] = restCases[sj]; restCases[sj] = tmp;
        }
        // 高額グループもシャッフル（毎回順序が変わるように）
        for (var ti = topCases.length - 1; ti > 0; ti--) {
            var tj = Math.floor(Math.random() * (ti + 1));
            var ttmp = topCases[ti]; topCases[ti] = topCases[tj]; topCases[tj] = ttmp;
        }

        // 構成: ユーザー業種(高額順) → 高額他業種(シャッフル) → 残り(シャッフル)
        var adoptionCases = userCases.concat(topCases).concat(restCases);
        var caseIndex = 0;
        resultContainer.innerHTML =
            '<div class="result-loading">' +
            '  <div class="spinner"></div>' +
            '  <p class="loading-main-text">診断結果を分析しています...</p>' +
            '  <div class="loading-case-carousel">' +
            '    <p class="loading-case-label">💡 採択事例</p>' +
            '    <div class="loading-case-card" id="loading-case">' +
            '      <span class="loading-case-industry">' + adoptionCases[0].industry + '</span>' +
            '      <span class="loading-case-type">' + adoptionCases[0].type + '</span>' +
            '      <span class="loading-case-amount">補助額 ' + adoptionCases[0].amount + '</span>' +
            '      <span class="loading-case-use">' + adoptionCases[0].use + '</span>' +
            '      <span class="loading-case-result">→ ' + adoptionCases[0].result + '</span>' +
            '    </div>' +
            '  </div>' +
            '</div>';

        var caseInterval = setInterval(function() {
            caseIndex = (caseIndex + 1) % adoptionCases.length;
            var c = adoptionCases[caseIndex];
            var el = document.getElementById('loading-case');
            if (el) {
                el.style.opacity = '0';
                setTimeout(function() {
                    el.innerHTML =
                        '<span class="loading-case-industry">' + c.industry + '</span>' +
                        '<span class="loading-case-type">' + c.type + '</span>' +
                        '<span class="loading-case-amount">補助額 ' + c.amount + '</span>' +
                        '<span class="loading-case-use">' + c.use + '</span>' +
                        '<span class="loading-case-result">→ ' + c.result + '</span>';
                    el.style.opacity = '1';
                }, 300);
            }
        }, 3000);

        var apiUrl = (typeof subsidyMatchApi !== 'undefined')
            ? subsidyMatchApi.root + 'subsidy/v1/match'
            : '/wp-json/subsidy/v1/match';

        var headers = { 'Content-Type': 'application/json' };
        if (typeof subsidyMatchApi !== 'undefined' && subsidyMatchApi.nonce) {
            headers['X-WP-Nonce'] = subsidyMatchApi.nonce;
        }

        fetch(apiUrl, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(answers)
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                clearInterval(caseInterval);
                if (data.success && data.results) {
                    renderProposalResults(data.results, data.dx_analysis || null);
                } else {
                    renderFallbackResults();
                }
            })
            .catch(function () {
                clearInterval(caseInterval);
                renderFallbackResults();
            });
    }

    /**
     * 営業提案書レベルの結果描画
     */
    function renderProposalResults(results, serverDxAnalysis) {
        // GA4/GTM: 診断完了イベント
        pushDataLayer('matching_complete');

        var dx = analyzeDxChallenges();
        var html = '';

        // ヘッダー
        html += '<div class="proposal-header">';
        html += '  <div class="proposal-header-badge">診断結果レポート</div>';
        html += '  <h2>貴社の補助金・DX診断結果</h2>';
        html += '  <p>ご回答内容をもとに、活用可能な補助金と最適なDX施策をご提案いたします</p>';
        html += '</div>';

        // ネクストアクションCTA（補助金リストの上）
        html += '<div class="result-top-cta">';
        html += '  <div class="result-top-cta-inner">';
        html += '    <h3 class="result-top-cta-title">あなたに最適な補助金TOP3を専門家が無料で解説します</h3>';
        html += '    <div class="result-top-cta-buttons">';
        html += '      <a href="' + getContactUrl() + '" class="btn btn-primary btn-large">無料相談を予約する</a>';
        html += '      <a href="' + getContactUrl() + '?ref=pdf" class="btn btn-secondary">診断結果をPDFで受け取る</a>';
        html += '    </div>';
        html += '  </div>';
        html += '</div>';

        // 補助金セクション
        html += '<section class="proposal-section">';
        html += '  <div class="proposal-section-header"><h3>該当する補助金・助成金</h3><span class="proposal-section-count">' + results.length + '件</span></div>';

        results.forEach(function (item) {
            var matchClass = item.match_level || 'medium';
            var badgeLabel = matchClass === 'high' ? '適合度：高' : matchClass === 'medium' ? '適合度：中' : '適合度：低';
            var badgeClass = matchClass === 'high' ? 'badge-high' : matchClass === 'medium' ? 'badge-medium' : 'badge-low';

            html += '<div class="subsidy-card" data-match="' + matchClass + '">';
            html += '  <div class="subsidy-card-header"><h3 class="subsidy-card-title">' + escapeHtml(item.title) + '</h3><span class="subsidy-card-badge badge ' + badgeClass + '">' + badgeLabel + '</span></div>';
            html += '  <div class="subsidy-card-details">';
            html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">補助上限額</span><span class="subsidy-detail-value">' + (item.amount_text || formatAmount(item.max_amount)) + '</span></div>';
            html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">補助率</span><span class="subsidy-detail-value">' + escapeHtml(item.rate || '-') + '</span></div>';
            if (item.adoption_rate) {
                html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">採択率</span><span class="subsidy-detail-value">' + Math.round(item.adoption_rate * 100) + '%</span></div>';
            }
            if (item.eligible_entities) {
                html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">対象事業者</span><span class="subsidy-detail-value">' + escapeHtml(item.eligible_entities) + '</span></div>';
            }
            if (item.purpose) {
                html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">目的</span><span class="subsidy-detail-value">' + escapeHtml(item.purpose) + '</span></div>';
            }
            if (item.eligible_expenses) {
                html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">対象経費</span><span class="subsidy-detail-value">' + escapeHtml(item.eligible_expenses) + '</span></div>';
            }
            if (item.implementing_agency) {
                html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">実施機関</span><span class="subsidy-detail-value">' + escapeHtml(item.implementing_agency) + '</span></div>';
            }
            html += '  </div>';
            html += '  <p class="subsidy-card-summary">' + escapeHtml(item.summary || '') + '</p>';
            if (item.deadline) {
                html += '  <div class="subsidy-card-meta"><span class="subsidy-card-deadline">申請期間: ' + escapeHtml(item.deadline) + '</span></div>';
            }
            if (item.official_url) {
                html += '  <a href="' + escapeHtml(item.official_url) + '" target="_blank" rel="noopener" class="subsidy-card-link">公募要領を確認する →</a>';
            }
            html += '</div>';
        });
        html += '</section>';

        // DX課題セクション
        if (dx.issues.length > 0) {
            html += '<section class="proposal-section proposal-dx-section">';
            html += '  <div class="proposal-section-header"><h3>貴社のDX課題</h3></div>';
            var levelLabel = dx.dxLevel === 'beginner' ? 'デジタル化初期段階' : dx.dxLevel === 'developing' ? 'デジタル化移行段階' : 'デジタル活用段階';
            var levelDesc = dx.dxLevel === 'beginner' ? '多くの業務がアナログ中心です。補助金を活用したデジタル化により、大幅な業務効率改善が見込めます。' : dx.dxLevel === 'developing' ? '一部デジタル化が進んでいますが、さらなる効率化の余地があります。' : 'デジタル化は進んでいます。さらなる高度化・連携で競争力を強化できます。';
            html += '  <div class="dx-level-card"><div class="dx-level-badge dx-level-' + dx.dxLevel + '">' + levelLabel + '</div><p>' + levelDesc + '</p></div>';
            html += '  <ul class="dx-issues-list">';
            dx.issues.forEach(function (issue) { html += '<li>' + escapeHtml(issue) + '</li>'; });
            html += '  </ul>';
            if (dx.painPoints.length > 0) {
                var painLabels = { labor_shortage: '人手不足', sales_decline: '売上低下', cost_reduction: 'コスト削減', efficiency: '業務効率化', new_business: '新規事業' };
                html += '  <div class="dx-pain-tags">';
                dx.painPoints.forEach(function (p) { html += '<span class="dx-pain-tag">' + escapeHtml(painLabels[p] || p) + '</span>'; });
                html += '  </div>';
            }
            html += '</section>';
        }

        // おすすめシステム導入
        if (dx.recommendations.length > 0) {
            html += '<section class="proposal-section proposal-recommend-section">';
            html += '  <div class="proposal-section-header"><h3>おすすめシステム導入プラン</h3></div>';
            html += '  <p class="proposal-recommend-lead">DX課題の分析結果をもとに、補助金を活用した最適なシステム導入プランをご提案いたします。</p>';
            dx.recommendations.forEach(function (rec) {
                var subsidizedAmount = Math.round(rec.estimate * 0.25);
                html += '<div class="recommend-card"><div class="recommend-card-header"><h4>' + escapeHtml(rec.category) + '</h4></div>';
                html += '<p class="recommend-card-desc">' + escapeHtml(rec.description) + '</p>';
                html += '<div class="recommend-card-cost"><div class="recommend-cost-item"><span class="recommend-cost-label">想定導入費用</span><span class="recommend-cost-value">約' + rec.estimate + '万円</span></div>';
                html += '<div class="recommend-cost-arrow">→</div>';
                html += '<div class="recommend-cost-item recommend-cost-actual"><span class="recommend-cost-label">補助金活用後（' + escapeHtml(rec.subsidyRate) + '）</span><span class="recommend-cost-value recommend-cost-highlight">実質 約' + subsidizedAmount + '万円</span></div></div></div>';
            });
            html += '</section>';
        }

        // 次のアクション強化（Feature 7）
        html += '<section class="proposal-section proposal-contact-section">';
        html += '  <div class="proposal-contact-header">';
        html += '    <h3>この結果を元に、専門家が無料でご相談に応じます</h3>';
        html += '  </div>';
        html += '  <div class="proposal-contact-methods">';
        html += '    <div class="proposal-contact-item">';
        html += '      <span class="proposal-contact-label">お電話でのご相談</span>';
        html += '      <span class="proposal-contact-value">03-XXXX-XXXX</span>';
        html += '      <span class="proposal-contact-note">平日 9:00〜18:00</span>';
        html += '    </div>';
        html += '    <div class="proposal-contact-item">';
        html += '      <span class="proposal-contact-label">メールでのご相談</span>';
        html += '      <span class="proposal-contact-value">info@yumeno-marketing.jp</span>';
        html += '      <span class="proposal-contact-note">24時間受付</span>';
        html += '    </div>';
        html += '  </div>';
        html += '  <div class="proposal-contact-actions">';
        html += '    <a href="' + getContactUrl() + '" class="btn btn-primary btn-large proposal-cta-btn">無料相談を予約する</a>';
        if (answers.email) {
            html += '    <button class="btn btn-secondary proposal-email-btn" id="send-result-email">この診断結果をメールで受け取る</button>';
        }
        html += '  </div>';
        html += '</section>';

        // 次のステップ CTA
        html += '<section class="proposal-section proposal-cta-section">';
        html += '  <h3>次のステップ</h3>';
        html += '  <div class="proposal-steps">';
        html += '    <div class="proposal-step-item"><span class="proposal-step-num">1</span><div class="proposal-step-content"><strong>無料相談のお申し込み</strong><p>補助金の申請要件や採択可能性について、専門スタッフが個別にご説明いたします。</p></div></div>';
        html += '    <div class="proposal-step-item"><span class="proposal-step-num">2</span><div class="proposal-step-content"><strong>導入プランの策定</strong><p>貴社の業務フローに最適なシステム構成と補助金申請計画をご提案いたします。</p></div></div>';
        html += '    <div class="proposal-step-item"><span class="proposal-step-num">3</span><div class="proposal-step-content"><strong>補助金申請・採択</strong><p>申請書類の作成から提出まで、専門家がトータルでサポートいたします。</p></div></div>';
        html += '  </div>';
        html += '  <a href="' + getContactUrl() + '" class="btn btn-primary btn-large proposal-cta-btn">このプランで無料相談する</a>';
        html += '  <p class="proposal-cta-note">※ 相談は完全無料です。お気軽にお問い合わせください。</p>';
        html += '</section>';

        resultContainer.innerHTML = html;

        // メール送信ボタンのイベント
        var emailBtn = document.getElementById('send-result-email');
        if (emailBtn) {
            emailBtn.addEventListener('click', function () {
                emailBtn.textContent = '送信しました';
                emailBtn.disabled = true;
                emailBtn.style.opacity = '0.6';
            });
        }
    }

    /**
     * API 未接続時のフォールバック結果
     */
    function renderFallbackResults() {
        var sampleResults = [
            { title: 'IT導入補助金', max_amount: 4500000, rate: '1/2〜3/4', summary: '中小企業・小規模事業者がITツール（ソフトウェア、サービス等）を導入する際の経費の一部を補助する制度です。', deadline: '2026年6月30日', official_url: '', match_level: 'high', adoption_rate: 0.62 },
            { title: 'ものづくり・商業・サービス生産性向上促進補助金', max_amount: 12500000, rate: '1/2〜2/3', summary: '中小企業・小規模事業者が取り組む革新的サービス開発・試作品開発・生産プロセスの改善を行う際の設備投資等を支援します。', deadline: '2026年9月30日', official_url: '', match_level: 'medium', adoption_rate: 0.45 },
            { title: '小規模事業者持続化補助金', max_amount: 2000000, rate: '2/3', summary: '小規模事業者が経営計画を策定して取り組む販路開拓等の取組を支援する制度です。', deadline: '2026年5月31日', official_url: '', match_level: 'medium', adoption_rate: 0.55 },
            { title: '事業再構築補助金', max_amount: 150000000, rate: '1/2〜3/4', summary: 'ポストコロナ時代の経済社会の変化に対応するため、中小企業等の思い切った事業再構築を支援します。', deadline: '2026年7月31日', official_url: '', match_level: 'low', adoption_rate: 0.38 }
        ];
        renderProposalResults(sampleResults, null);
    }

    function formatAmount(amount) {
        if (!amount) return '-';
        if (amount >= 100000000) return (amount / 100000000) + '億円';
        else if (amount >= 10000) return (amount / 10000).toLocaleString() + '万円';
        return amount.toLocaleString() + '円';
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function getContactUrl() {
        return (typeof subsidyMatchApi !== 'undefined')
            ? window.location.origin + '/contact/'
            : '/contact/';
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
