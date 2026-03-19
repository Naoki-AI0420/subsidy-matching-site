/**
 * 一問一答マッチング — メインロジック
 *
 * @package SubsidyMatch
 */
(function () {
    'use strict';

    var TOTAL_STEPS = 8;
    var currentStep = 1;
    var answers = {};

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
    }

    function bindEvents() {
        btnNext.addEventListener('click', handleNext);
        btnBack.addEventListener('click', handleBack);

        // ラジオボタンで選択時に自動で次へ
        document.querySelectorAll('.option-card input[type="radio"]').forEach(function (input) {
            input.addEventListener('change', function () {
                // 少し待ってから次へ（選択のフィードバックを見せる）
                setTimeout(function () {
                    handleNext();
                }, 250);
            });
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
        } else {
            submitMatching();
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
            // reflow
            void target.offsetHeight;
            target.style.animation = 'fadeIn 0.3s ease';
        }
    }

    function updateProgress() {
        var percent = Math.round(((currentStep - 1) / TOTAL_STEPS) * 100);
        if (currentStep === TOTAL_STEPS) {
            percent = 90; // 最後の質問で90%にして「あと少し」感を出す
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
                var checked = document.querySelectorAll('input[name="challenges"]:checked');
                if (checked.length === 0) return null;
                return Array.prototype.map.call(checked, function (c) { return c.value; });
            case 6:
                var revRadio = document.querySelector('input[name="annual_revenue"]:checked');
                return revRadio ? revRadio.value : null;
            case 7:
                var expRadio = document.querySelector('input[name="has_experience"]:checked');
                return expRadio ? expRadio.value : null;
            case 8:
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
            'challenges', 'annual_revenue', 'has_experience', 'email'
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
     * マッチング送信
     */
    function submitMatching() {
        // UI切り替え
        questionContainer.style.display = 'none';
        questionNav.style.display = 'none';
        progressContainer.style.display = 'none';
        resultContainer.style.display = 'block';

        // ローディング表示
        resultContainer.innerHTML =
            '<div class="result-loading">' +
            '  <div class="spinner"></div>' +
            '  <p>診断結果を分析しています...</p>' +
            '</div>';

        // API 呼び出し
        var apiUrl = (typeof subsidyMatchApi !== 'undefined')
            ? subsidyMatchApi.root + 'subsidy/v1/match'
            : '/wp-json/subsidy/v1/match';

        var headers = {
            'Content-Type': 'application/json'
        };
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
                    renderResults(data.results);
                } else {
                    renderFallbackResults();
                }
            })
            .catch(function () {
                // API未設定の場合はフォールバック
                renderFallbackResults();
            });
    }

    /**
     * 結果描画
     */
    function renderResults(results) {
        var html = '';

        // ヘッダー
        html += '<div class="result-header">';
        html += '  <h2>診断結果</h2>';
        html += '  <p>あなたの会社に該当する可能性がある補助金・助成金</p>';
        html += '  <span class="result-count">' + results.length + '件</span>';
        html += '</div>';

        // カード
        results.forEach(function (item) {
            var matchClass = item.match_level || 'medium';
            var badgeLabel = matchClass === 'high' ? '適合度：高' : matchClass === 'medium' ? '適合度：中' : '適合度：低';
            var badgeClass = matchClass === 'high' ? 'badge-high' : matchClass === 'medium' ? 'badge-medium' : 'badge-low';

            html += '<div class="subsidy-card" data-match="' + matchClass + '">';
            html += '  <div class="subsidy-card-header">';
            html += '    <h3 class="subsidy-card-title">' + escapeHtml(item.title) + '</h3>';
            html += '    <span class="subsidy-card-badge badge ' + badgeClass + '">' + badgeLabel + '</span>';
            html += '  </div>';
            html += '  <div class="subsidy-card-amount">最大 ' + formatAmount(item.max_amount) + '<span>（補助率 ' + escapeHtml(item.rate || '-') + '）</span></div>';
            html += '  <p class="subsidy-card-summary">' + escapeHtml(item.summary || '') + '</p>';
            html += '  <div class="subsidy-card-meta">';
            if (item.deadline) {
                html += '    <span class="subsidy-card-deadline">申請期限: ' + escapeHtml(item.deadline) + '</span>';
            }
            html += '  </div>';
            if (item.official_url) {
                html += '  <a href="' + escapeHtml(item.official_url) + '" target="_blank" rel="noopener" class="subsidy-card-link">詳細を見る →</a>';
            }
            html += '</div>';
        });

        // CTA
        html += '<div class="result-cta">';
        html += '  <h3>補助金を活用したシステム開発をご検討ですか？</h3>';
        html += '  <p>補助金の申請から活用方法まで、専門スタッフが無料でご相談を承ります。</p>';
        html += '  <a href="' + getContactUrl() + '" class="btn btn-primary btn-large">無料相談はこちら</a>';
        html += '</div>';

        resultContainer.innerHTML = html;
    }

    /**
     * API 未接続時のフォールバック結果
     */
    function renderFallbackResults() {
        var sampleResults = [
            {
                title: 'IT導入補助金',
                max_amount: 4500000,
                rate: '1/2〜3/4',
                summary: '中小企業・小規模事業者がITツール（ソフトウェア、サービス等）を導入する際の経費の一部を補助する制度です。',
                deadline: '2026年6月30日',
                official_url: '',
                match_level: 'high'
            },
            {
                title: 'ものづくり・商業・サービス生産性向上促進補助金',
                max_amount: 12500000,
                rate: '1/2〜2/3',
                summary: '中小企業・小規模事業者が取り組む革新的サービス開発・試作品開発・生産プロセスの改善を行う際の設備投資等を支援します。',
                deadline: '2026年9月30日',
                official_url: '',
                match_level: 'medium'
            },
            {
                title: '小規模事業者持続化補助金',
                max_amount: 2000000,
                rate: '2/3',
                summary: '小規模事業者が経営計画を策定して取り組む販路開拓等の取組を支援する制度です。',
                deadline: '2026年5月31日',
                official_url: '',
                match_level: 'medium'
            },
            {
                title: '事業再構築補助金',
                max_amount: 150000000,
                rate: '1/2〜3/4',
                summary: 'ポストコロナ時代の経済社会の変化に対応するため、中小企業等の思い切った事業再構築を支援します。',
                deadline: '2026年7月31日',
                official_url: '',
                match_level: 'low'
            }
        ];

        renderResults(sampleResults);
    }

    function formatAmount(amount) {
        if (!amount) return '-';
        if (amount >= 100000000) {
            return (amount / 100000000) + '億円';
        } else if (amount >= 10000) {
            return (amount / 10000).toLocaleString() + '万円';
        }
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

    // DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
