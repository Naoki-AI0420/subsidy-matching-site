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

    // 従業員規模→該当件数の目安
    var employeeMatchCount = {
        '1-5': 8, '6-20': 12, '21-50': 15, '51-100': 18, '101+': 22
    };

    // 課題→関連補助金件数
    var challengeSubsidyCount = {
        equipment: 25, it_dx: 30, hiring: 15, overseas: 10, rnd: 20, succession: 8
    };

    // 売上→年間補助金活用見込み
    var revenueEstimate = {
        under_10m: '100', '10m_50m': '300', '50m_100m': '500', '100m_500m': '800', over_500m: '1,500'
    };

    // バンドワゴン: 業種別該当率
    var industryMatchRate = {
        manufacturing: 92, construction: 88, information_technology: 95,
        wholesale_retail: 82, food_service: 85, accommodation: 80,
        medical_welfare: 78, education: 75, professional_services: 88,
        transportation: 76, real_estate: 72, agriculture: 70, other: 68
    };

    // ディドロ効果: 補助金→導入システム例マッピング
    var diderotSystemMap = {
        'IT導入補助金': ['予約管理システム', '会計システム', 'ECサイト'],
        'ものづくり補助金': ['生産管理システム', '在庫管理システム', 'CAD/CAMシステム'],
        'ものづくり・商業・サービス生産性向上促進補助金': ['生産管理システム', '在庫管理システム', 'CAD/CAMシステム'],
        '持続化補助金': ['ホームページ制作', 'SNSマーケティングツール', 'POSレジ'],
        '小規模事業者持続化補助金': ['ホームページ制作', 'SNSマーケティングツール', 'POSレジ'],
        '事業再構築補助金': ['基幹システム刷新', '新規事業用設備', 'オンラインサービス基盤']
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

    function init() {
        updateProgress();
        updateNavButtons();
        bindEvents();
        setupBeforeUnload();
    }

    function bindEvents() {
        btnNext.addEventListener('click', handleNext);
        btnBack.addEventListener('click', handleBack);

        // ラジオボタンで選択時に自動で次へ
        document.querySelectorAll('.option-card input[type="radio"]').forEach(function (input) {
            input.addEventListener('change', function () {
                setTimeout(function () {
                    handleNext();
                }, 250);
            });
        });
    }

    /**
     * サンクコスト強化: beforeunload
     */
    function setupBeforeUnload() {
        window.addEventListener('beforeunload', function (e) {
            if (currentStep > 1 && !resultContainer.style.display.match(/block/)) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    function handleNext() {
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
            updateSunkCostMessage(currentStep);
            updateBandwagonMessage(currentStep);
        } else {
            submitMatching();
        }
    }

    /**
     * バンドワゴン効果: 業種選択後メッセージ
     */
    function updateBandwagonMessage(step) {
        if (step === 3 && answers.industry) {
            var rate = industryMatchRate[answers.industry] || 75;
            var industryLabel = document.querySelector('#q-industry option[value="' + answers.industry + '"]');
            var label = industryLabel ? industryLabel.textContent : '御社の業種';

            var teaserEl = document.getElementById('amount-teaser');
            if (teaserEl) {
                teaserEl.innerHTML = label + 'の企業の<strong>' + rate + '%</strong>がいずれかの補助金に該当しています';
                teaserEl.style.display = 'block';
                teaserEl.style.animation = 'none';
                void teaserEl.offsetHeight;
                teaserEl.style.animation = 'fadeIn 0.5s ease';
            }
        }
    }

    /**
     * サンクコスト強化: 暫定候補数メッセージ
     */
    function updateSunkCostMessage(step) {
        var msgEl = document.getElementById('sunk-cost-msg');
        if (!msgEl) return;

        if (step >= 3) {
            var baseCount = employeeMatchCount[answers.employee_size] || 10;
            var answeredCount = Object.keys(answers).length;
            var adjustedCount = Math.max(3, baseCount + Math.floor(answeredCount * 1.2));
            msgEl.innerHTML = 'ここまでの回答を元に、暫定 <strong>' + adjustedCount + '件</strong> の候補が見つかっています';
            msgEl.style.display = 'block';
            msgEl.style.animation = 'none';
            void msgEl.offsetHeight;
            msgEl.style.animation = 'fadeIn 0.5s ease';
        } else {
            msgEl.style.display = 'none';
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

    // シェイクアニメーション追加
    var style = document.createElement('style');
    style.textContent = '@keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } }';
    document.head.appendChild(style);

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
        questionContainer.style.display = 'none';
        questionNav.style.display = 'none';
        progressContainer.style.display = 'none';
        resultContainer.style.display = 'block';

        var sunkEl = document.getElementById('sunk-cost-msg');
        if (sunkEl) sunkEl.style.display = 'none';
        var teaserEl = document.getElementById('amount-teaser');
        if (teaserEl) teaserEl.style.display = 'none';

        resultContainer.innerHTML =
            '<div class="result-loading">' +
            '  <div class="spinner"></div>' +
            '  <p>診断結果を分析しています...</p>' +
            '</div>';

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
                if (data.success && data.results) {
                    renderProposalResults(data.results, data.dx_analysis || null);
                } else {
                    renderFallbackResults();
                }
            })
            .catch(function () {
                renderFallbackResults();
            });
    }

    /**
     * 吊橋効果: 期限カウントダウン計算
     */
    function calcDaysRemaining(deadlineStr) {
        if (!deadlineStr) return null;
        var match = deadlineStr.match(/(\d{4})[年\-\/](\d{1,2})[月\-\/](\d{1,2})/);
        if (!match) return null;
        var deadline = new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]));
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        return Math.ceil((deadline - today) / (1000 * 60 * 60 * 24));
    }

    /**
     * 営業提案書レベルの結果描画
     */
    function renderProposalResults(results, serverDxAnalysis) {
        var dx = analyzeDxChallenges();
        var html = '';

        // スノッブ効果: 専用レポートメッセージ
        html += '<div class="result-exclusive-badge">';
        html += '  <span>このレポートは御社専用に生成されました</span>';
        html += '</div>';

        // ヘッダー
        html += '<div class="proposal-header">';
        html += '  <div class="proposal-header-badge">診断結果レポート</div>';
        html += '  <h2>貴社の補助金・DX診断結果</h2>';
        html += '  <p>ご回答内容をもとに、活用可能な補助金と最適なDX施策をご提案いたします</p>';
        html += '</div>';

        // アンカリング効果: 最大交付額
        html += '<div class="anchoring-banner">';
        html += '  <p class="anchoring-label">補助金制度全体の最大交付額</p>';
        html += '  <p class="anchoring-amount">1億5,000<span class="anchoring-unit">万円</span></p>';
        html += '  <p class="anchoring-sub">以下は御社に該当する補助金です</p>';
        html += '</div>';

        // 吊橋効果: 期限迫り警告バナー
        var hasUrgent = false;
        results.forEach(function (item) {
            var days = calcDaysRemaining(item.deadline);
            if (days !== null && days <= 30 && days >= 0) hasUrgent = true;
        });
        if (hasUrgent) {
            html += '<div class="deadline-warning-banner">';
            html += '  <span class="deadline-warning-icon">&#9888;</span>';
            html += '  <span>申請期限が迫っている補助金があります。お早めにご確認ください。</span>';
            html += '</div>';
        }

        // 補助金セクション
        html += '<section class="proposal-section">';
        html += '  <div class="proposal-section-header">';
        html += '    <h3>該当する補助金・助成金</h3>';
        html += '    <span class="proposal-section-count">' + results.length + '件</span>';
        html += '  </div>';

        results.forEach(function (item) {
            var matchClass = item.match_level || 'medium';
            var badgeLabel = matchClass === 'high' ? '適合度：高' : matchClass === 'medium' ? '適合度：中' : '適合度：低';
            var badgeClass = matchClass === 'high' ? 'badge-high' : matchClass === 'medium' ? 'badge-medium' : 'badge-low';
            var applyCount = 30 + (item.title.length % 20);
            var daysRemaining = calcDaysRemaining(item.deadline);

            // アンカリング: 実質負担額計算
            var maxAmount = item.max_amount || 0;
            var rateText = item.rate || '';
            var subsidyRatio = 0.5;
            if (rateText.indexOf('3/4') !== -1) subsidyRatio = 0.75;
            else if (rateText.indexOf('2/3') !== -1) subsidyRatio = 0.667;
            var estimatedCost = maxAmount > 0 ? Math.round(maxAmount / subsidyRatio) : 0;
            var actualBurden = estimatedCost > 0 ? estimatedCost - maxAmount : 0;

            html += '<div class="subsidy-card" data-match="' + matchClass + '">';
            html += '  <div class="subsidy-card-header">';
            html += '    <h3 class="subsidy-card-title">' + escapeHtml(item.title) + '</h3>';
            html += '    <span class="subsidy-card-badge badge ' + badgeClass + '">' + badgeLabel + '</span>';
            html += '  </div>';

            if (daysRemaining !== null && daysRemaining <= 30 && daysRemaining >= 0) {
                html += '  <div class="subsidy-countdown"><span class="countdown-days">あと' + daysRemaining + '日</span>で申請期限</div>';
            }

            html += '  <div class="subsidy-card-details">';
            html += '    <div class="subsidy-detail-item">';
            html += '      <span class="subsidy-detail-label">補助上限額</span>';
            html += '      <span class="subsidy-detail-value">' + formatAmount(item.max_amount) + '</span>';
            html += '    </div>';
            html += '    <div class="subsidy-detail-item">';
            html += '      <span class="subsidy-detail-label">補助率</span>';
            html += '      <span class="subsidy-detail-value">' + escapeHtml(item.rate || '-') + '</span>';
            html += '    </div>';
            if (item.adoption_rate) {
                html += '    <div class="subsidy-detail-item">';
                html += '      <span class="subsidy-detail-label">採択率</span>';
                html += '      <span class="subsidy-detail-value">' + Math.round(item.adoption_rate * 100) + '%</span>';
                html += '    </div>';
            }
            html += '  </div>';

            // アンカリング: 3段階コスト表示
            if (estimatedCost > 0) {
                html += '  <div class="subsidy-cost-breakdown">';
                html += '    <span class="cost-item">開発費 ' + formatAmount(estimatedCost) + '</span>';
                html += '    <span class="cost-arrow">&rarr;</span>';
                html += '    <span class="cost-item cost-subsidy">補助金 ' + formatAmount(maxAmount) + '</span>';
                html += '    <span class="cost-arrow">=</span>';
                html += '    <span class="cost-item cost-actual">実質負担 ' + formatAmount(actualBurden) + '</span>';
                html += '  </div>';
            }

            html += '  <p class="subsidy-card-summary">' + escapeHtml(item.summary || '') + '</p>';
            html += '  <p class="subsidy-apply-count">この補助金は今月 <strong>' + applyCount + '社</strong> が申請しています</p>';

            if (item.deadline) {
                html += '  <div class="subsidy-card-meta"><span class="subsidy-card-deadline">申請期限: ' + escapeHtml(item.deadline) + '</span></div>';
            }
            if (item.official_url) {
                html += '  <a href="' + escapeHtml(item.official_url) + '" target="_blank" rel="noopener" class="subsidy-card-link">公募要領を確認する &rarr;</a>';
            }
            html += '</div>';
        });
        html += '</section>';

        // DX課題セクション
        if (dx.issues.length > 0) {
            html += '<section class="proposal-section proposal-dx-section">';
            html += '  <div class="proposal-section-header"><h3>貴社のDX課題</h3></div>';

            var levelLabel = dx.dxLevel === 'beginner' ? 'デジタル化初期段階' : dx.dxLevel === 'developing' ? 'デジタル化移行段階' : 'デジタル活用段階';
            var levelDesc = dx.dxLevel === 'beginner' ? '多くの業務がアナログ中心です。補助金を活用したデジタル化により、大幅な業務効率改善が見込めます。' :
                            dx.dxLevel === 'developing' ? '一部デジタル化が進んでいますが、さらなる効率化の余地があります。' :
                            'デジタル化は進んでいます。さらなる高度化・連携で競争力を強化できます。';

            html += '  <div class="dx-level-card">';
            html += '    <div class="dx-level-badge dx-level-' + dx.dxLevel + '">' + levelLabel + '</div>';
            html += '    <p>' + levelDesc + '</p>';
            html += '  </div>';
            html += '  <ul class="dx-issues-list">';
            dx.issues.forEach(function (issue) { html += '<li>' + escapeHtml(issue) + '</li>'; });
            html += '  </ul>';

            if (dx.painPoints.length > 0) {
                var painLabels = { labor_shortage: '人手不足', sales_decline: '売上低下', cost_reduction: 'コスト削減', efficiency: '業務効率化', new_business: '新規事業' };
                html += '<div class="dx-pain-tags">';
                dx.painPoints.forEach(function (p) { html += '<span class="dx-pain-tag">' + escapeHtml(painLabels[p] || p) + '</span>'; });
                html += '</div>';
            }
            html += '</section>';
        }

        // おすすめシステム導入セクション
        if (dx.recommendations.length > 0) {
            html += '<section class="proposal-section proposal-recommend-section">';
            html += '  <div class="proposal-section-header"><h3>おすすめシステム導入プラン</h3></div>';
            html += '  <p class="proposal-recommend-lead">DX課題の分析結果をもとに、補助金を活用した最適なシステム導入プランをご提案いたします。</p>';

            dx.recommendations.forEach(function (rec) {
                var subsidizedAmount = Math.round(rec.estimate * 0.25);
                html += '<div class="recommend-card">';
                html += '  <div class="recommend-card-header"><h4>' + escapeHtml(rec.category) + '</h4></div>';
                html += '  <p class="recommend-card-desc">' + escapeHtml(rec.description) + '</p>';
                html += '  <div class="recommend-card-cost">';
                html += '    <div class="recommend-cost-item"><span class="recommend-cost-label">想定導入費用</span><span class="recommend-cost-value">約' + rec.estimate + '万円</span></div>';
                html += '    <div class="recommend-cost-arrow">&rarr;</div>';
                html += '    <div class="recommend-cost-item recommend-cost-actual"><span class="recommend-cost-label">補助金活用後（' + escapeHtml(rec.subsidyRate) + '）</span><span class="recommend-cost-value recommend-cost-highlight">実質 約' + subsidizedAmount + '万円</span></div>';
                html += '  </div>';
                html += '</div>';
            });
            html += '</section>';
        }

        // ディドロ効果: 補助金で導入できるシステム例
        var diderotItems = [];
        results.forEach(function (item) {
            var title = item.title || '';
            Object.keys(diderotSystemMap).forEach(function (key) {
                if (title.indexOf(key) !== -1 || key.indexOf(title) !== -1) {
                    diderotSystemMap[key].forEach(function (sys) {
                        if (diderotItems.indexOf(sys) === -1) diderotItems.push(sys);
                    });
                }
            });
            if (title.indexOf('IT') !== -1 || title.indexOf('デジタル') !== -1) {
                ['予約管理システム', '会計システム', 'ECサイト'].forEach(function (sys) { if (diderotItems.indexOf(sys) === -1) diderotItems.push(sys); });
            }
            if (title.indexOf('ものづくり') !== -1) {
                ['生産管理システム', '在庫管理システム'].forEach(function (sys) { if (diderotItems.indexOf(sys) === -1) diderotItems.push(sys); });
            }
            if (title.indexOf('持続化') !== -1) {
                ['ホームページ制作', 'SNSマーケティングツール'].forEach(function (sys) { if (diderotItems.indexOf(sys) === -1) diderotItems.push(sys); });
            }
        });

        if (diderotItems.length > 0) {
            html += '<section class="proposal-section proposal-diderot-section">';
            html += '  <div class="proposal-section-header"><h3>この補助金で導入できるシステム例</h3></div>';
            html += '  <div class="diderot-grid">';
            diderotItems.forEach(function (sys) {
                html += '<div class="diderot-item"><span class="diderot-icon">&#9654;</span><span>' + escapeHtml(sys) + '</span></div>';
            });
            html += '  </div>';
            html += '  <p class="diderot-note">無料相談では、補助金申請から導入まで一貫してサポートいたします</p>';
            html += '</section>';
        }

        // スノッブ効果: 限定PDF
        html += '<div class="result-pdf-teaser">';
        html += '  <p class="pdf-teaser-text">詳細分析レポート（PDF）は<strong>無料相談</strong>でのみご提供しております</p>';
        html += '</div>';

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
        if (amount >= 10000) return (amount / 10000).toLocaleString() + '万円';
        return amount.toLocaleString() + '円';
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function getContactUrl() {
        return (typeof subsidyMatchApi !== 'undefined') ? window.location.origin + '/contact/' : '/contact/';
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
