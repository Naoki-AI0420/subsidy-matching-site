/**
 * チャット風一問一答マッチング — メインロジック
 *
 * @package SubsidyMatch
 */
(function () {
    'use strict';

    var TOTAL_STEPS = 6;
    var currentStep = 0;
    var answers = {};
    var matchResults = null; // API結果を保持

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

    var industryLabelMap = {
        manufacturing: '製造業', construction: '建設業', information_technology: '情報通信業',
        wholesale_retail: '卸売業・小売業', food_service: '飲食サービス業', accommodation: '宿泊業',
        medical_welfare: '医療・福祉', education: '教育・学習支援業',
        professional_services: '専門・技術サービス業', transportation: '運輸業・郵便業',
        real_estate: '不動産業', agriculture: '農業・林業・漁業', other: 'その他'
    };

    var industryMatchCount = {
        manufacturing: 15, construction: 12, information_technology: 18,
        wholesale_retail: 10, food_service: 8, accommodation: 9,
        medical_welfare: 14, education: 7, professional_services: 11,
        transportation: 8, real_estate: 6, agriculture: 9, other: 8
    };

    // 47都道府県
    var prefectures = [
        '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
        '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
        '新潟県','富山県','石川県','福井県','山梨県','長野県',
        '岐阜県','静岡県','愛知県','三重県',
        '滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
        '鳥取県','島根県','岡山県','広島県','山口県',
        '徳島県','香川県','愛媛県','高知県',
        '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
    ];

    // 主要都市サジェスト
    var majorCities = {
        '北海道': ['札幌市','旭川市','函館市','釧路市','帯広市','小樽市','北見市','苫小牧市'],
        '青森県': ['青森市','八戸市','弘前市'],
        '岩手県': ['盛岡市','一関市','奥州市'],
        '宮城県': ['仙台市','石巻市','大崎市'],
        '秋田県': ['秋田市','横手市','大仙市'],
        '山形県': ['山形市','鶴岡市','酒田市'],
        '福島県': ['福島市','郡山市','いわき市'],
        '茨城県': ['水戸市','つくば市','日立市','土浦市'],
        '栃木県': ['宇都宮市','小山市','栃木市'],
        '群馬県': ['前橋市','高崎市','太田市','伊勢崎市'],
        '埼玉県': ['さいたま市','川口市','川越市','所沢市','越谷市','草加市','春日部市','上尾市','熊谷市'],
        '千葉県': ['千葉市','船橋市','松戸市','市川市','柏市','市原市','八千代市','流山市'],
        '東京都': ['千代田区','中央区','港区','新宿区','文京区','台東区','墨田区','江東区','品川区','目黒区','大田区','世田谷区','渋谷区','中野区','杉並区','豊島区','北区','荒川区','板橋区','練馬区','足立区','葛飾区','江戸川区','八王子市','立川市','武蔵野市','三鷹市','府中市','調布市','町田市'],
        '神奈川県': ['横浜市','川崎市','相模原市','藤沢市','横須賀市','平塚市','茅ヶ崎市','厚木市','大和市'],
        '新潟県': ['新潟市','長岡市','上越市'],
        '富山県': ['富山市','高岡市'],
        '石川県': ['金沢市','白山市','小松市'],
        '福井県': ['福井市','坂井市'],
        '山梨県': ['甲府市','甲斐市'],
        '長野県': ['長野市','松本市','上田市'],
        '岐阜県': ['岐阜市','大垣市','各務原市'],
        '静岡県': ['静岡市','浜松市','富士市','沼津市'],
        '愛知県': ['名古屋市','豊田市','岡崎市','一宮市','豊橋市','春日井市','安城市'],
        '三重県': ['津市','四日市市','鈴鹿市'],
        '滋賀県': ['大津市','草津市','長浜市'],
        '京都府': ['京都市','宇治市','亀岡市'],
        '大阪府': ['大阪市','堺市','東大阪市','豊中市','枚方市','吹田市','高槻市','茨木市','八尾市'],
        '兵庫県': ['神戸市','姫路市','西宮市','尼崎市','明石市','加古川市','宝塚市'],
        '奈良県': ['奈良市','橿原市','生駒市'],
        '和歌山県': ['和歌山市','田辺市'],
        '鳥取県': ['鳥取市','米子市'],
        '島根県': ['松江市','出雲市'],
        '岡山県': ['岡山市','倉敷市','津山市'],
        '広島県': ['広島市','福山市','呉市','東広島市'],
        '山口県': ['下関市','山口市','周南市'],
        '徳島県': ['徳島市','阿南市'],
        '香川県': ['高松市','丸亀市'],
        '愛媛県': ['松山市','今治市','新居浜市'],
        '高知県': ['高知市','南国市'],
        '福岡県': ['福岡市','北九州市','久留米市','飯塚市','春日市','大野城市'],
        '佐賀県': ['佐賀市','唐津市'],
        '長崎県': ['長崎市','佐世保市'],
        '熊本県': ['熊本市','八代市'],
        '大分県': ['大分市','別府市'],
        '宮崎県': ['宮崎市','都城市','延岡市'],
        '鹿児島県': ['鹿児島市','霧島市'],
        '沖縄県': ['那覇市','沖縄市','うるま市','浦添市','宜野湾市']
    };

    // SVGアイコン（官公庁風）
    var avatarSvg = '<svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<circle cx="18" cy="14" r="6" fill="#FFFFFF"/>' +
        '<path d="M6 32c0-6.627 5.373-12 12-12s12 5.373 12 12" fill="#FFFFFF" opacity="0.7"/>' +
        '<path d="M18 2l2 4h4l-3.5 2.5L22 13l-4-3-4 3 1.5-4.5L12 6h4l2-4z" fill="#FFD54F"/>' +
        '</svg>';

    // DOM要素
    var progressBar = document.querySelector('.progress-bar');
    var progressPercent = document.querySelector('.progress-percent');
    var currentStepEl = document.querySelector('.current-step');
    var totalStepsEl = document.querySelector('.total-steps');
    var chatMessages = document.getElementById('chat-messages');
    var chatInputArea = document.getElementById('chat-input-area');
    var resultContainer = document.querySelector('.result-container');
    var progressContainer = document.querySelector('.progress-container');

    // 質問定義（6問）
    var questions = [
        {}, // index 0 unused
        {
            type: 'suggest',
            key: 'prefecture',
            question: '会社の所在地（都道府県）を教えてください',
            sub: '都道府県名を入力してください',
            placeholder: '例：東京都',
            candidates: prefectures
        },
        {
            type: 'text',
            key: 'city',
            question: '市区町村を教えてください',
            sub: '市区町村名を入力してください',
            placeholder: '例：渋谷区'
        },
        {
            type: 'select',
            key: 'industry',
            question: '業種を教えてください',
            sub: '該当する業種をお選びください',
            placeholder: '選択してください',
            options: [
                {v:'manufacturing',l:'製造業'},{v:'construction',l:'建設業'},
                {v:'information_technology',l:'情報通信業'},{v:'wholesale_retail',l:'卸売業・小売業'},
                {v:'food_service',l:'飲食サービス業'},{v:'accommodation',l:'宿泊業'},
                {v:'medical_welfare',l:'医療・福祉'},{v:'education',l:'教育・学習支援業'},
                {v:'professional_services',l:'専門・技術サービス業'},{v:'transportation',l:'運輸業・郵便業'},
                {v:'real_estate',l:'不動産業'},{v:'agriculture',l:'農業・林業・漁業'},
                {v:'other',l:'その他'}
            ]
        },
        {
            type: 'radio',
            key: 'capital',
            question: '資本金を教えてください',
            sub: '該当する範囲をお選びください',
            options: [
                {v:'under_3m',l:'300万円未満'},{v:'3m_10m',l:'300万〜1,000万円'},
                {v:'10m_50m',l:'1,000万〜5,000万円'},{v:'50m_100m',l:'5,000万〜1億円'},
                {v:'over_100m',l:'1億円以上'}
            ]
        },
        {
            type: 'radio',
            key: 'employee_size',
            question: '従業員数を教えてください',
            sub: 'パート・アルバイトを含む人数をお選びください',
            options: [
                {v:'1-5',l:'1〜5名'},{v:'6-20',l:'6〜20名'},{v:'21-50',l:'21〜50名'},
                {v:'51-100',l:'51〜100名'},{v:'101+',l:'101名以上'}
            ]
        },
        {
            type: 'radio',
            key: 'establishment_years',
            question: '設立からの年数を教えてください',
            sub: '該当する範囲をお選びください',
            options: [
                {v:'under_1',l:'1年未満'},{v:'1_3',l:'1〜3年'},
                {v:'3_5',l:'3〜5年'},{v:'5_10',l:'5〜10年'},
                {v:'over_10',l:'10年以上'}
            ]
        }
    ];

    // AIリアクションメッセージ生成
    function getReaction(step) {
        switch (step) {
            case 1:
                var pref = answers.prefecture || '';
                return pref + 'ですね！地域の補助金もチェックしますね。';
            case 2:
                return 'ありがとうございます！';
            case 3:
                var label = industryLabelMap[answers.industry] || '';
                var matchCount = industryMatchCount[answers.industry] || 8;
                return label + 'ですね！' + label + 'の企業では平均<strong>' + matchCount + '件</strong>の補助金に該当していますよ。';
            case 4:
                return 'ありがとうございます！続けてお聞きしますね。';
            case 5:
                return 'もう少しで完了です！あと<strong>1問</strong>です。';
            default:
                return 'ありがとうございます！';
        }
    }

    function init() {
        if (totalStepsEl) totalStepsEl.textContent = TOTAL_STEPS;
        startConversation();
    }

    function startConversation() {
        addAiMessage('こんにちは！補助金・助成金の<strong>無料診断</strong>を始めます。<strong>6つ</strong>の質問に答えるだけで、あなたに合った補助金がわかります！', function() {
            currentStep = 1;
            updateProgress();
            askQuestion(1);
        });
    }

    // AIメッセージ追加（タイピングインジケーター付き）
    function addAiMessage(html, callback) {
        var nameDiv = document.createElement('div');
        nameDiv.className = 'chat-ai-name';
        nameDiv.textContent = 'あかり｜補助金アドバイザー';
        chatMessages.appendChild(nameDiv);

        var typingRow = document.createElement('div');
        typingRow.className = 'chat-typing';
        typingRow.innerHTML = '<div class="chat-avatar">' + avatarSvg + '</div>' +
            '<div class="typing-dots"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
        chatMessages.appendChild(typingRow);
        scrollToBottom();

        var delay = 600 + Math.random() * 400;
        setTimeout(function() {
            chatMessages.removeChild(typingRow);

            var row = document.createElement('div');
            row.className = 'chat-row chat-row-ai';
            row.innerHTML = '<div class="chat-avatar">' + avatarSvg + '</div>' +
                '<div class="chat-bubble-ai">' + html + '</div>';
            chatMessages.appendChild(row);
            scrollToBottom();

            if (callback) {
                setTimeout(callback, 150);
            }
        }, delay);
    }

    // ユーザーメッセージ追加
    function addUserMessage(text) {
        var row = document.createElement('div');
        row.className = 'chat-row chat-row-user';
        row.innerHTML = '<div class="chat-bubble-user">' + escapeHtml(text) + '</div>';
        chatMessages.appendChild(row);
        scrollToBottom();
    }

    function scrollToBottom() {
        requestAnimationFrame(function() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    }

    // 質問表示
    function askQuestion(step) {
        var q = questions[step];
        if (!q) return;

        var qHtml = '<strong>' + escapeHtml(q.question) + '</strong>';
        if (q.sub) qHtml += '<br><span style="font-size:13px;color:#888">' + escapeHtml(q.sub) + '</span>';

        addAiMessage(qHtml, function() {
            renderInput(step, q);
        });
    }

    // 入力UI描画
    function renderInput(step, q) {
        chatInputArea.innerHTML = '';

        if (q.type === 'suggest') {
            renderSuggest(step, q);
        } else if (q.type === 'text') {
            renderTextInput(step, q);
        } else if (q.type === 'select') {
            renderSelect(step, q);
        } else if (q.type === 'radio') {
            renderRadio(step, q);
        }

        scrollToBottom();
    }

    // サジェスト付きテキスト入力（都道府県用）
    function renderSuggest(step, q) {
        var wrapper = document.createElement('div');
        wrapper.className = 'chat-suggest-wrapper';

        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'chat-text-input';
        input.placeholder = q.placeholder || '';
        input.setAttribute('autocomplete', 'off');

        var suggestList = document.createElement('div');
        suggestList.className = 'suggest-list';
        suggestList.style.display = 'none';

        var btn = document.createElement('button');
        btn.className = 'chat-select-btn';
        btn.textContent = '決定';
        btn.disabled = true;

        var inputRow = document.createElement('div');
        inputRow.className = 'chat-select-wrapper';
        inputRow.appendChild(input);
        inputRow.appendChild(btn);

        wrapper.appendChild(inputRow);
        wrapper.appendChild(suggestList);

        input.addEventListener('input', function() {
            var val = input.value.trim();
            suggestList.innerHTML = '';
            if (!val) {
                suggestList.style.display = 'none';
                btn.disabled = true;
                return;
            }

            var matches = q.candidates.filter(function(c) {
                return c.indexOf(val) === 0;
            });

            if (matches.length > 0) {
                matches.forEach(function(m) {
                    var item = document.createElement('div');
                    item.className = 'suggest-item';
                    item.textContent = m;
                    item.addEventListener('click', function() {
                        input.value = m;
                        suggestList.style.display = 'none';
                        btn.disabled = false;
                    });
                    suggestList.appendChild(item);
                });
                suggestList.style.display = 'block';
            } else {
                suggestList.style.display = 'none';
            }

            // 完全一致チェック
            btn.disabled = q.candidates.indexOf(val) === -1;
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !btn.disabled) {
                e.preventDefault();
                btn.click();
            }
        });

        // 外側クリックでサジェスト閉じる
        document.addEventListener('click', function closeSuggest(e) {
            if (!wrapper.contains(e.target)) {
                suggestList.style.display = 'none';
                document.removeEventListener('click', closeSuggest);
            }
        });

        btn.addEventListener('click', function() {
            var val = input.value.trim();
            if (q.candidates.indexOf(val) === -1) return;
            answers[q.key] = val;
            chatInputArea.innerHTML = '';
            addUserMessage(val);
            advanceStep(step);
        });

        chatInputArea.appendChild(wrapper);
        input.focus();
    }

    // テキスト入力（市区町村用、サジェスト付き）
    function renderTextInput(step, q) {
        var wrapper = document.createElement('div');
        wrapper.className = 'chat-suggest-wrapper';

        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'chat-text-input';
        input.placeholder = q.placeholder || '';
        input.setAttribute('autocomplete', 'off');

        var suggestList = document.createElement('div');
        suggestList.className = 'suggest-list';
        suggestList.style.display = 'none';

        var btn = document.createElement('button');
        btn.className = 'chat-select-btn';
        btn.textContent = '決定';
        btn.disabled = true;

        var inputRow = document.createElement('div');
        inputRow.className = 'chat-select-wrapper';
        inputRow.appendChild(input);
        inputRow.appendChild(btn);

        wrapper.appendChild(inputRow);
        wrapper.appendChild(suggestList);

        // 選択された都道府県に基づくサジェスト
        var cities = majorCities[answers.prefecture] || [];

        input.addEventListener('input', function() {
            var val = input.value.trim();
            suggestList.innerHTML = '';
            btn.disabled = !val;

            if (!val || cities.length === 0) {
                suggestList.style.display = 'none';
                return;
            }

            var matches = cities.filter(function(c) {
                return c.indexOf(val) === 0;
            });

            if (matches.length > 0) {
                matches.forEach(function(m) {
                    var item = document.createElement('div');
                    item.className = 'suggest-item';
                    item.textContent = m;
                    item.addEventListener('click', function() {
                        input.value = m;
                        suggestList.style.display = 'none';
                        btn.disabled = false;
                    });
                    suggestList.appendChild(item);
                });
                suggestList.style.display = 'block';
            } else {
                suggestList.style.display = 'none';
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !btn.disabled) {
                e.preventDefault();
                btn.click();
            }
        });

        document.addEventListener('click', function closeSuggest(e) {
            if (!wrapper.contains(e.target)) {
                suggestList.style.display = 'none';
                document.removeEventListener('click', closeSuggest);
            }
        });

        btn.addEventListener('click', function() {
            var val = input.value.trim();
            if (!val) return;
            answers[q.key] = val;
            chatInputArea.innerHTML = '';
            addUserMessage(val);
            advanceStep(step);
        });

        chatInputArea.appendChild(wrapper);
        input.focus();
    }

    // セレクトボックス型
    function renderSelect(step, q) {
        var wrapper = document.createElement('div');
        wrapper.className = 'chat-select-wrapper';

        var sel = document.createElement('select');
        sel.className = 'chat-select';
        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = q.placeholder || '選択してください';
        sel.appendChild(placeholder);

        q.options.forEach(function(opt) {
            var o = document.createElement('option');
            o.value = opt.v;
            o.textContent = opt.l;
            sel.appendChild(o);
        });

        var btn = document.createElement('button');
        btn.className = 'chat-select-btn';
        btn.textContent = '決定';
        btn.disabled = true;

        sel.addEventListener('change', function() {
            btn.disabled = !sel.value;
        });

        btn.addEventListener('click', function() {
            if (!sel.value) return;
            var selectedOpt = sel.options[sel.selectedIndex];
            var label = selectedOpt.textContent;
            answers[q.key] = sel.value;
            chatInputArea.innerHTML = '';
            addUserMessage(label);
            advanceStep(step);
        });

        wrapper.appendChild(sel);
        wrapper.appendChild(btn);
        chatInputArea.appendChild(wrapper);
    }

    // ラジオ型
    function renderRadio(step, q) {
        var container = document.createElement('div');
        container.className = 'chat-options';

        q.options.forEach(function(opt) {
            var btn = document.createElement('button');
            btn.className = 'chat-option-btn';
            btn.textContent = opt.l;
            btn.addEventListener('click', function() {
                answers[q.key] = opt.v;
                btn.classList.add('selected');
                chatInputArea.innerHTML = '';
                addUserMessage(opt.l);
                advanceStep(step);
            });
            container.appendChild(btn);
        });

        chatInputArea.appendChild(container);
    }

    // ステップ進行
    function advanceStep(completedStep) {
        if (completedStep < TOTAL_STEPS) {
            var reaction = getReaction(completedStep);
            addAiMessage(reaction, function() {
                currentStep = completedStep + 1;
                updateProgress();
                askQuestion(currentStep);
            });
        } else {
            // 最終ステップ完了 → 送信
            addAiMessage('ありがとうございます！診断結果を分析しています...', function() {
                submitMatching();
            });
        }
    }

    function updateProgress() {
        var percent = Math.round(((currentStep - 1) / TOTAL_STEPS) * 100);
        if (currentStep === TOTAL_STEPS) percent = 95;
        progressBar.style.width = percent + '%';
        progressPercent.textContent = percent + '%';
        currentStepEl.textContent = currentStep;

        if (currentStep >= 5) {
            progressBar.style.background = 'linear-gradient(90deg, #2E7D32, #43A047)';
        } else if (currentStep >= 3) {
            progressBar.style.background = 'linear-gradient(90deg, #003366, #1565C0)';
        } else {
            progressBar.style.background = 'linear-gradient(90deg, #003366, #0056b3)';
        }
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

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
     * マッチング送信
     */
    function submitMatching() {
        pushDataLayer('form_submit', { 'form_name': 'subsidy_matching' });

        var chatContainer = document.getElementById('chat-container');

        // 採択事例カルーセル（ローディング中）
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
        allCases['beauty'] = [
            { industry: '美容業', type: 'IT導入補助金', amount: '150万円', use: '予約管理・顧客管理システム導入', result: '予約率40%向上' },
            { industry: '美容業', type: '小規模事業者持続化補助金', amount: '50万円', use: 'Instagram集客・LINE予約連携', result: '新規客60%増' },
        ];

        var userIndustry = answers.industry || 'other';
        var allFlat = [];
        Object.keys(allCases).forEach(function(k) {
            allCases[k].forEach(function(c) {
                c._isUserIndustry = (k === userIndustry);
                c._amountNum = parseInt(c.amount.replace(/[^0-9]/g, ''), 10) || 0;
                allFlat.push(c);
            });
        });

        var userCases = allFlat.filter(function(c) { return c._isUserIndustry; });
        userCases.sort(function(a, b) { return b._amountNum - a._amountNum; });
        var otherCases = allFlat.filter(function(c) { return !c._isUserIndustry; });
        otherCases.sort(function(a, b) { return b._amountNum - a._amountNum; });
        var topThird = Math.ceil(otherCases.length / 3);
        var topCases = otherCases.slice(0, topThird);
        var restCases = otherCases.slice(topThird);
        for (var si = restCases.length - 1; si > 0; si--) {
            var sj = Math.floor(Math.random() * (si + 1));
            var tmp = restCases[si]; restCases[si] = restCases[sj]; restCases[sj] = tmp;
        }
        for (var ti = topCases.length - 1; ti > 0; ti--) {
            var tj = Math.floor(Math.random() * (ti + 1));
            var ttmp = topCases[ti]; topCases[ti] = topCases[tj]; topCases[tj] = ttmp;
        }
        var adoptionCases = userCases.concat(topCases).concat(restCases);
        var caseIndex = 0;

        // チャットを隠して結果コンテナを表示
        chatContainer.style.display = 'none';
        progressContainer.style.display = 'none';
        resultContainer.style.display = 'block';

        resultContainer.innerHTML =
            '<div class="result-loading">' +
            '  <div class="spinner"></div>' +
            '  <p class="loading-main-text">診断結果を分析しています...</p>' +
            '  <div class="loading-case-carousel">' +
            '    <p class="loading-case-label">\ud83d\udca1 採択事例</p>' +
            '    <div class="loading-case-card" id="loading-case">' +
            '      <span class="loading-case-industry">' + adoptionCases[0].industry + '</span>' +
            '      <span class="loading-case-type">' + adoptionCases[0].type + '</span>' +
            '      <span class="loading-case-amount">補助額 ' + adoptionCases[0].amount + '</span>' +
            '      <span class="loading-case-use">' + adoptionCases[0].use + '</span>' +
            '      <span class="loading-case-result">\u2192 ' + adoptionCases[0].result + '</span>' +
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
                        '<span class="loading-case-result">\u2192 ' + c.result + '</span>';
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
                    matchResults = data.results;
                    renderLeadGateResults(data.results);
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
     * リードゲート付き結果描画
     * 1件目のみ表示、2件目以降はすりガラス
     */
    function renderLeadGateResults(results) {
        pushDataLayer('matching_complete');

        matchResults = results;
        var html = '';

        // バナー
        html += '<div class="result-banner">';
        html += '  <div class="result-banner-icon">\ud83c\udf89</div>';
        html += '  <h2 class="result-banner-title"><strong>' + results.length + '件</strong>の補助金が見つかりました！</h2>';
        html += '  <p class="result-banner-sub">あなたの条件に合った補助金・助成金です</p>';
        html += '</div>';

        // 1件目（完全表示）
        if (results.length > 0) {
            html += renderSubsidyCard(results[0], 0);
        }

        // 2件目以降（すりガラス）
        if (results.length > 1) {
            html += '<div class="blurred-results-wrapper" id="blurred-results">';
            html += '  <div class="blurred-results-inner">';
            for (var i = 1; i < results.length; i++) {
                html += renderSubsidyCard(results[i], i);
            }
            html += '  </div>';
            html += '  <div class="blurred-overlay" id="blurred-overlay">';
            html += '    <div class="blurred-overlay-content">';
            html += '      <p class="blurred-overlay-text">他にも<strong>' + (results.length - 1) + '件</strong>の補助金が該当しています</p>';
            html += '      <button class="blurred-overlay-btn" id="show-all-btn">該当するすべての補助金情報を見る</button>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        }

        resultContainer.innerHTML = html;

        // CTAボタンのイベント
        var showAllBtn = document.getElementById('show-all-btn');
        if (showAllBtn) {
            showAllBtn.addEventListener('click', function() {
                startLeadForm();
            });
        }
    }

    /**
     * 補助金カード1件分のHTML
     */
    function renderSubsidyCard(item, index) {
        var matchClass = item.match_level || 'medium';
        var badgeLabel = matchClass === 'high' ? '適合度：高' : matchClass === 'medium' ? '適合度：中' : '適合度：低';
        var badgeClass = matchClass === 'high' ? 'badge-high' : matchClass === 'medium' ? 'badge-medium' : 'badge-low';

        var html = '<div class="subsidy-card" data-match="' + matchClass + '" data-index="' + index + '">';
        html += '  <div class="subsidy-card-header"><h3 class="subsidy-card-title">' + escapeHtml(item.title) + '</h3><span class="subsidy-card-badge badge ' + badgeClass + '">' + badgeLabel + '</span></div>';
        html += '  <div class="subsidy-card-details">';
        html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">補助上限額</span><span class="subsidy-detail-value">' + (item.amount_text || formatAmount(item.max_amount)) + '</span></div>';
        html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">補助率</span><span class="subsidy-detail-value">' + escapeHtml(item.rate || '-') + '</span></div>';
        if (item.adoption_rate) {
            html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">採択率</span><span class="subsidy-detail-value">' + Math.round(item.adoption_rate * 100) + '%</span></div>';
        }
        html += '  </div>';
        html += '  <p class="subsidy-card-summary">' + escapeHtml(item.summary || '') + '</p>';
        if (item.deadline) {
            html += '  <div class="subsidy-card-meta"><span class="subsidy-card-deadline">申請期間: ' + escapeHtml(item.deadline) + '</span></div>';
        }
        html += '</div>';
        return html;
    }

    /**
     * リード入力フォーム（チャット形式）
     */
    function startLeadForm() {
        // 結果コンテナを非表示に、チャットに戻す
        resultContainer.style.display = 'none';
        var chatContainer = document.getElementById('chat-container');
        chatContainer.style.display = 'flex';

        var leadAnswers = {};
        var leadFields = [
            { key: 'company_name', question: 'すべての結果をお見せしますね！まず、会社名を教えてください。', placeholder: '例：株式会社○○' },
            { key: 'contact_name', question: 'ご担当者様のお名前を教えてください。', placeholder: '例：山田 太郎' },
            { key: 'phone', question: 'お電話番号を教えてください。', placeholder: '例：03-1234-5678', validation: 'phone' },
            { key: 'email', question: 'メールアドレスを教えてください。', placeholder: '例：example@company.co.jp', validation: 'email' }
        ];
        var leadStep = 0;

        function askLeadQuestion() {
            if (leadStep >= leadFields.length) {
                // 同意チェック
                askConsent();
                return;
            }

            var field = leadFields[leadStep];
            addAiMessage('<strong>' + field.question + '</strong>', function() {
                renderLeadInput(field);
            });
        }

        function renderLeadInput(field) {
            chatInputArea.innerHTML = '';

            var wrapper = document.createElement('div');
            wrapper.className = 'chat-select-wrapper';

            var input = document.createElement('input');
            input.type = field.validation === 'email' ? 'email' : 'text';
            input.className = 'chat-text-input';
            input.placeholder = field.placeholder || '';

            var btn = document.createElement('button');
            btn.className = 'chat-select-btn';
            btn.textContent = '決定';
            btn.disabled = true;

            input.addEventListener('input', function() {
                btn.disabled = !input.value.trim();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !btn.disabled) {
                    e.preventDefault();
                    btn.click();
                }
            });

            btn.addEventListener('click', function() {
                var val = input.value.trim();
                if (!val) return;

                // バリデーション
                if (field.validation === 'email' && !isValidEmail(val)) {
                    input.style.borderColor = '#C62828';
                    return;
                }
                if (field.validation === 'phone' && !/^[\d\-+().\s]{8,}$/.test(val)) {
                    input.style.borderColor = '#C62828';
                    return;
                }

                leadAnswers[field.key] = val;
                chatInputArea.innerHTML = '';
                addUserMessage(val);
                leadStep++;

                setTimeout(function() {
                    askLeadQuestion();
                }, 300);
            });

            wrapper.appendChild(input);
            wrapper.appendChild(btn);
            chatInputArea.appendChild(wrapper);
            input.focus();
        }

        function askConsent() {
            addAiMessage('最後に、個人情報の取り扱いについてご確認ください。', function() {
                chatInputArea.innerHTML = '';

                var wrapper = document.createElement('div');
                wrapper.className = 'chat-email-wrapper';

                var consentDiv = document.createElement('div');
                consentDiv.className = 'chat-consent-group';
                consentDiv.innerHTML = '<input type="checkbox" id="lead-privacy-consent">' +
                    '<label for="lead-privacy-consent"><a href="/privacy/" target="_blank" rel="noopener">個人情報の取り扱い</a>に同意する</label>';

                var btn = document.createElement('button');
                btn.className = 'chat-email-submit';
                btn.textContent = 'すべての結果を見る';

                btn.addEventListener('click', function() {
                    var consentBox = document.getElementById('lead-privacy-consent');
                    if (!consentBox.checked) {
                        consentDiv.classList.add('consent-error');
                        return;
                    }
                    consentDiv.classList.remove('consent-error');

                    chatInputArea.innerHTML = '';
                    addUserMessage('同意します');

                    // リード保存
                    submitLead(leadAnswers);
                });

                wrapper.appendChild(consentDiv);
                wrapper.appendChild(btn);
                chatInputArea.appendChild(wrapper);
            });
        }

        askLeadQuestion();
    }

    /**
     * リード保存 API
     */
    function submitLead(leadData) {
        var apiUrl = (typeof subsidyMatchApi !== 'undefined')
            ? subsidyMatchApi.root + 'subsidy/v1/lead'
            : '/wp-json/subsidy/v1/lead';

        var headers = { 'Content-Type': 'application/json' };
        if (typeof subsidyMatchApi !== 'undefined' && subsidyMatchApi.nonce) {
            headers['X-WP-Nonce'] = subsidyMatchApi.nonce;
        }

        var payload = {
            company_name: leadData.company_name,
            contact_name: leadData.contact_name,
            phone: leadData.phone,
            email: leadData.email,
            diagnosis_answers: answers,
            results_count: matchResults ? matchResults.length : 0
        };

        addAiMessage('情報をお預かりしています...', function() {
            fetch(apiUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload)
            })
            .then(function(res) { return res.json(); })
            .then(function() {
                showFullResults();
            })
            .catch(function() {
                // APIエラーでも結果は表示
                showFullResults();
            });
        });
    }

    /**
     * すりガラス解除→全件表示→システム興味質問
     */
    function showFullResults() {
        var chatContainer = document.getElementById('chat-container');
        chatContainer.style.display = 'none';
        resultContainer.style.display = 'block';

        // すりガラス解除
        var blurredWrapper = document.getElementById('blurred-results');
        if (blurredWrapper) {
            var overlay = document.getElementById('blurred-overlay');
            if (overlay) overlay.remove();
            blurredWrapper.classList.add('revealed');
        }

        // 進捗を100%に
        if (progressContainer) {
            progressContainer.style.display = 'block';
            progressBar.style.width = '100%';
            progressPercent.textContent = '100%';
            progressBar.style.background = 'linear-gradient(90deg, #2E7D32, #43A047)';
        }

        addAiMessage('すべての結果を表示しました！', function() {
            // スクロールを一番上に
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // システム興味質問を結果の下に追加
            setTimeout(function() {
                appendSystemInterestQuestion();
            }, 1500);
        });
    }

    /**
     * システム開発興味質問 + 無料相談CTA
     */
    function appendSystemInterestQuestion() {
        var interestSection = document.createElement('div');
        interestSection.className = 'system-interest-section';
        interestSection.innerHTML =
            '<div class="system-interest-card">' +
            '  <h3 class="system-interest-title">補助金を使ってシステム開発・導入を検討していますか？</h3>' +
            '  <div class="system-interest-buttons">' +
            '    <button class="system-interest-btn" data-value="yes">はい</button>' +
            '    <button class="system-interest-btn" data-value="no">いいえ</button>' +
            '    <button class="system-interest-btn" data-value="undecided">まだわからない</button>' +
            '  </div>' +
            '</div>';

        resultContainer.appendChild(interestSection);

        // スクロールして見せる
        interestSection.scrollIntoView({ behavior: 'smooth', block: 'center' });

        var buttons = interestSection.querySelectorAll('.system-interest-btn');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var value = btn.getAttribute('data-value');

                // 選択状態を表示
                buttons.forEach(function(b) { b.classList.remove('selected'); });
                btn.classList.add('selected');

                // API でリードに追記
                updateLeadSystemInterest(value);

                // CTA表示
                setTimeout(function() {
                    showFinalCta();
                }, 500);
            });
        });
    }

    /**
     * リードにsystem_interestを追記
     */
    function updateLeadSystemInterest(value) {
        var apiUrl = (typeof subsidyMatchApi !== 'undefined')
            ? subsidyMatchApi.root + 'subsidy/v1/lead/update'
            : '/wp-json/subsidy/v1/lead/update';

        var headers = { 'Content-Type': 'application/json' };
        if (typeof subsidyMatchApi !== 'undefined' && subsidyMatchApi.nonce) {
            headers['X-WP-Nonce'] = subsidyMatchApi.nonce;
        }

        fetch(apiUrl, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ system_interest: value, email: answers.email || '' })
        }).catch(function() { /* ignore */ });
    }

    /**
     * 最終CTA（無料相談誘導）
     */
    function showFinalCta() {
        // 既にあれば追加しない
        if (document.getElementById('final-cta-section')) return;

        var ctaSection = document.createElement('div');
        ctaSection.id = 'final-cta-section';
        ctaSection.className = 'final-cta-section';
        ctaSection.innerHTML =
            '<div class="final-cta-card">' +
            '  <p class="final-cta-text">ただし、あなたがやりたいことに該当するかは<strong>個別の確認</strong>が必要です。<br>プロに<strong>無料</strong>で相談してみませんか？</p>' +
            '  <a href="' + getContactUrl() + '" class="final-cta-btn">無料相談する</a>' +
            '  <p class="final-cta-note">※ 相談は完全無料です。お気軽にお問い合わせください。</p>' +
            '</div>';

        resultContainer.appendChild(ctaSection);
        ctaSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
        renderLeadGateResults(sampleResults);
    }

    function formatAmount(amount) {
        if (!amount) return '-';
        if (amount >= 100000000) return (amount / 100000000) + '億円';
        else if (amount >= 10000) return (amount / 10000).toLocaleString() + '万円';
        return amount.toLocaleString() + '円';
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
