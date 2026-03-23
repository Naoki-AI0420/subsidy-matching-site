/**
 * チャット風一問一答マッチング — メインロジック（6問版）
 *
 * @package SubsidyMatch
 */
(function () {
    'use strict';

    var TOTAL_STEPS = 6;
    var currentStep = 0;
    var answers = {};
    var matchResults = null;
    var leadId = 0;

    // 業種→最大金額マッピング
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

    // 47都道府県
    var prefectures = [
        '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
        '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
        '新潟県','富山県','石川県','福井県','山梨県','長野県',
        '岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
        '鳥取県','島根県','岡山県','広島県','山口県',
        '徳島県','香川県','愛媛県','高知県',
        '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
    ];

    // 主要市区町村マッピング
    var cityMap = {
        '北海道': ['札幌市','旭川市','函館市','釧路市','帯広市','小樽市','北見市','江別市','苫小牧市','千歳市'],
        '青森県': ['青森市','八戸市','弘前市','十和田市','むつ市','五所川原市'],
        '岩手県': ['盛岡市','一関市','奥州市','花巻市','北上市','宮古市'],
        '宮城県': ['仙台市','石巻市','大崎市','名取市','登米市','栗原市','多賀城市'],
        '秋田県': ['秋田市','横手市','大仙市','由利本荘市','大館市'],
        '山形県': ['山形市','鶴岡市','酒田市','米沢市','天童市','東根市'],
        '福島県': ['福島市','郡山市','いわき市','会津若松市','須賀川市','白河市'],
        '茨城県': ['水戸市','つくば市','日立市','ひたちなか市','土浦市','古河市','取手市','筑西市'],
        '栃木県': ['宇都宮市','小山市','栃木市','足利市','佐野市','那須塩原市','鹿沼市'],
        '群馬県': ['前橋市','高崎市','太田市','伊勢崎市','桐生市','館林市'],
        '埼玉県': ['さいたま市','川口市','川越市','所沢市','越谷市','草加市','春日部市','上尾市','熊谷市','新座市','狭山市','久喜市','入間市','深谷市','三郷市','朝霞市','戸田市','富士見市','坂戸市','東松山市'],
        '千葉県': ['千葉市','船橋市','松戸市','市川市','柏市','市原市','八千代市','流山市','佐倉市','習志野市','浦安市','野田市','木更津市','我孫子市','成田市','鎌ケ谷市','印西市'],
        '東京都': ['千代田区','中央区','港区','新宿区','文京区','台東区','墨田区','江東区','品川区','目黒区','大田区','世田谷区','渋谷区','中野区','杉並区','豊島区','北区','荒川区','板橋区','練馬区','足立区','葛飾区','江戸川区','八王子市','立川市','武蔵野市','三鷹市','府中市','調布市','町田市','小平市','日野市','西東京市','国分寺市','国立市','多摩市'],
        '神奈川県': ['横浜市','川崎市','相模原市','藤沢市','横須賀市','平塚市','茅ヶ崎市','大和市','厚木市','小田原市','鎌倉市','秦野市','海老名市','座間市','伊勢原市','綾瀬市'],
        '新潟県': ['新潟市','長岡市','上越市','三条市','新発田市','柏崎市','燕市'],
        '富山県': ['富山市','高岡市','射水市','南砺市','氷見市'],
        '石川県': ['金沢市','白山市','小松市','加賀市','七尾市','野々市市'],
        '福井県': ['福井市','坂井市','越前市','鯖江市','敦賀市'],
        '山梨県': ['甲府市','甲斐市','南アルプス市','笛吹市','富士吉田市'],
        '長野県': ['長野市','松本市','上田市','飯田市','佐久市','安曇野市','伊那市','塩尻市'],
        '岐阜県': ['岐阜市','大垣市','各務原市','多治見市','可児市','高山市','関市'],
        '静岡県': ['静岡市','浜松市','富士市','沼津市','磐田市','藤枝市','焼津市','掛川市','三島市','富士宮市'],
        '愛知県': ['名古屋市','豊田市','岡崎市','一宮市','豊橋市','春日井市','安城市','豊川市','西尾市','刈谷市','小牧市','稲沢市','瀬戸市','半田市','東海市','江南市','大府市','日進市','あま市'],
        '三重県': ['津市','四日市市','鈴鹿市','松阪市','桑名市','伊勢市','名張市'],
        '滋賀県': ['大津市','草津市','長浜市','東近江市','彦根市','甲賀市','守山市'],
        '京都府': ['京都市','宇治市','亀岡市','舞鶴市','城陽市','長岡京市','福知山市','木津川市'],
        '大阪府': ['大阪市','堺市','東大阪市','枚方市','豊中市','吹田市','高槻市','茨木市','八尾市','寝屋川市','岸和田市','和泉市','守口市','箕面市','門真市','大東市','松原市','富田林市','羽曳野市','河内長野市'],
        '兵庫県': ['神戸市','姫路市','西宮市','尼崎市','明石市','加古川市','宝塚市','伊丹市','川西市','三田市','芦屋市','高砂市','豊岡市'],
        '奈良県': ['奈良市','橿原市','生駒市','大和郡山市','香芝市','天理市','大和高田市'],
        '和歌山県': ['和歌山市','田辺市','橋本市','紀の川市','岩出市','海南市'],
        '鳥取県': ['鳥取市','米子市','倉吉市','境港市'],
        '島根県': ['松江市','出雲市','浜田市','益田市','安来市'],
        '岡山県': ['岡山市','倉敷市','津山市','総社市','笠岡市','玉野市'],
        '広島県': ['広島市','福山市','呉市','東広島市','尾道市','廿日市市','三原市'],
        '山口県': ['下関市','山口市','宇部市','周南市','岩国市','防府市'],
        '徳島県': ['徳島市','阿南市','鳴門市','吉野川市','小松島市'],
        '香川県': ['高松市','丸亀市','三豊市','観音寺市','坂出市','さぬき市'],
        '愛媛県': ['松山市','今治市','新居浜市','西条市','四国中央市','宇和島市'],
        '高知県': ['高知市','南国市','四万十市','香南市','土佐市'],
        '福岡県': ['福岡市','北九州市','久留米市','飯塚市','大牟田市','春日市','筑紫野市','大野城市','太宰府市','糸島市','宗像市','古賀市'],
        '佐賀県': ['佐賀市','唐津市','鳥栖市','伊万里市','武雄市'],
        '長崎県': ['長崎市','佐世保市','諫早市','大村市','島原市'],
        '熊本県': ['熊本市','八代市','天草市','玉名市','宇城市','合志市','菊池市'],
        '大分県': ['大分市','別府市','中津市','佐伯市','日田市','宇佐市'],
        '宮崎県': ['宮崎市','都城市','延岡市','日南市','日向市','小林市'],
        '鹿児島県': ['鹿児島市','霧島市','鹿屋市','薩摩川内市','姶良市','奄美市','出水市'],
        '沖縄県': ['那覇市','沖縄市','うるま市','浦添市','宜野湾市','名護市','豊見城市','糸満市','南城市']
    };

    // オペレーターアバターSVG
    var avatarSvg = '<svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<circle cx="40" cy="40" r="40" fill="#003366"/>' +
        '<circle cx="40" cy="32" r="12" fill="#F5DEB3"/>' +
        '<ellipse cx="40" cy="24" rx="14" ry="10" fill="#4A3728"/>' +
        '<circle cx="35" cy="33" r="1.5" fill="#333"/><circle cx="45" cy="33" r="1.5" fill="#333"/>' +
        '<path d="M36 38 Q40 41 44 38" stroke="#E08080" stroke-width="1.5" fill="none" stroke-linecap="round"/>' +
        '<path d="M28 48 Q40 42 52 48 L54 70 Q40 66 26 70 Z" fill="#1a2744"/>' +
        '<path d="M36 48 L40 52 L44 48" fill="white"/>' +
        '<ellipse cx="22" cy="34" rx="4" ry="5" fill="none" stroke="#999" stroke-width="1.5"/>' +
        '<line x1="22" y1="39" x2="28" y2="44" stroke="#999" stroke-width="1.5"/>' +
        '<circle cx="22" cy="34" r="2" fill="#666"/>' +
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
            sub: '入力すると候補が表示されます',
            placeholder: '例: 東京都',
            candidates: prefectures
        },
        {
            type: 'suggest',
            key: 'city',
            question: '市区町村を教えてください',
            sub: '入力すると候補が表示されます',
            placeholder: '例: 渋谷区',
            getCandidates: function() { return cityMap[answers.prefecture] || []; }
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
                {v:'under_1y',l:'1年未満'},{v:'1_3y',l:'1〜3年'},{v:'3_5y',l:'3〜5年'},
                {v:'5_10y',l:'5〜10年'},{v:'over_10y',l:'10年以上'}
            ]
        }
    ];

    // AIリアクション
    function getReaction(step) {
        switch (step) {
            case 1:
                return answers.prefecture + 'ですね！';
            case 2:
                return 'ありがとうございます！続けてお聞きします。';
            case 3:
                var info = industryAmountMap[answers.industry];
                if (info) {
                    return industryLabelMap[answers.industry] + 'ですね！最大<strong>' + info.amount + '</strong>の補助金が活用できる可能性があります。';
                }
                return 'ありがとうございます！';
            case 4:
                return '承知しました！あと少しです。';
            case 5:
                return 'ありがとうございます！最後の質問です。';
            default:
                return 'ありがとうございます！';
        }
    }

    function init() {
        if (totalStepsEl) totalStepsEl.textContent = TOTAL_STEPS;
        startConversation();
    }

    function startConversation() {
        addAiMessage('こんにちは！補助金・助成金の<strong>無料診断</strong>を始めます。<br>社長様が即答できる<strong>6つの質問</strong>だけで完了します！', function() {
            currentStep = 1;
            updateProgress();
            askQuestion(1);
        });
    }

    // AIメッセージ追加
    function addAiMessage(html, callback) {
        var nameDiv = document.createElement('div');
        nameDiv.className = 'chat-ai-name';
        nameDiv.textContent = '佐藤あかり（補助金アドバイザー）';
        chatMessages.appendChild(nameDiv);

        var typingRow = document.createElement('div');
        typingRow.className = 'chat-typing';
        typingRow.innerHTML = '<div class="chat-avatar">' + avatarSvg + '</div>' +
            '<div class="typing-dots"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
        chatMessages.appendChild(typingRow);
        scrollToBottom();

        var delay = 500 + Math.random() * 300;
        setTimeout(function() {
            chatMessages.removeChild(typingRow);
            var row = document.createElement('div');
            row.className = 'chat-row chat-row-ai';
            row.innerHTML = '<div class="chat-avatar">' + avatarSvg + '</div>' +
                '<div class="chat-bubble-ai">' + html + '</div>';
            chatMessages.appendChild(row);
            scrollToBottom();
            if (callback) setTimeout(callback, 150);
        }, delay);
    }

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

    function askQuestion(step) {
        var q = questions[step];
        if (!q) return;
        var qHtml = '<strong>' + escapeHtml(q.question) + '</strong>';
        if (q.sub) qHtml += '<br><span style="font-size:13px;color:#888">' + escapeHtml(q.sub) + '</span>';
        addAiMessage(qHtml, function() { renderInput(step, q); });
    }

    function renderInput(step, q) {
        chatInputArea.innerHTML = '';
        if (q.type === 'suggest') renderSuggest(step, q);
        else if (q.type === 'select') renderSelect(step, q);
        else if (q.type === 'radio') renderRadio(step, q);
        scrollToBottom();
    }

    // サジェスト型入力
    function renderSuggest(step, q) {
        var wrapper = document.createElement('div');
        wrapper.className = 'chat-suggest-wrapper';

        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'chat-suggest-input';
        input.placeholder = q.placeholder || '';
        input.autocomplete = 'off';

        var dropdown = document.createElement('div');
        dropdown.className = 'chat-suggest-dropdown';
        dropdown.style.display = 'none';
        dropdown.style.position = 'absolute';
        dropdown.style.bottom = '100%';
        dropdown.style.top = 'auto';
        dropdown.style.left = '0';
        dropdown.style.right = '0';
        dropdown.style.zIndex = '200';
        dropdown.style.maxHeight = '250px';
        dropdown.style.overflowY = 'auto';
        dropdown.style.background = '#FFFFFF';
        dropdown.style.border = '1px solid #CCCCCC';
        dropdown.style.borderRadius = '8px';
        dropdown.style.boxShadow = '0 -4px 12px rgba(0,0,0,0.15)';
        dropdown.style.marginBottom = '4px';

        var candidates = q.candidates || (q.getCandidates ? q.getCandidates() : []);

        function updateDropdown() {
            var val = input.value.trim();
            dropdown.innerHTML = '';
            var filtered;
            if (!val) {
                filtered = candidates;
            } else {
                filtered = candidates.filter(function(c) {
                    return c.indexOf(val) === 0 || c.indexOf(val) !== -1;
                });
            }
            filtered.forEach(function(c) {
                var item = document.createElement('div');
                item.className = 'chat-suggest-item';
                item.textContent = c;
                item.addEventListener('click', function() { selectItem(c); });
                dropdown.appendChild(item);
            });
            dropdown.style.display = filtered.length > 0 ? 'block' : 'none';
        }

        function selectItem(value) {
            answers[q.key] = value;
            chatInputArea.innerHTML = '';
            addUserMessage(value);
            advanceStep(step);
        }

        input.addEventListener('input', updateDropdown);

        var btn = document.createElement('button');
        btn.className = 'chat-select-btn';
        btn.textContent = '決定';
        btn.addEventListener('click', function() {
            var val = input.value.trim();
            if (!val) return;
            selectItem(val);
        });

        // IME変換中フラグ（日本語入力の確定Enterで送信しない）
        var isComposing = false;
        input.addEventListener('compositionstart', function() { isComposing = true; });
        input.addEventListener('compositionend', function() { isComposing = false; });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !isComposing) {
                e.preventDefault();
                var val = input.value.trim();
                if (val) selectItem(val);
            }
        });

        wrapper.appendChild(input);
        wrapper.appendChild(dropdown);

        var row = document.createElement('div');
        row.className = 'chat-select-wrapper';
        row.appendChild(wrapper);
        row.appendChild(btn);
        chatInputArea.appendChild(row);

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

        sel.addEventListener('change', function() { btn.disabled = !sel.value; });

        btn.addEventListener('click', function() {
            if (!sel.value) return;
            var label = sel.options[sel.selectedIndex].textContent;
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

    function pushDataLayer(event, params) {
        window.dataLayer = window.dataLayer || [];
        var obj = { 'event': event };
        if (params) { for (var key in params) { if (params.hasOwnProperty(key)) obj[key] = params[key]; } }
        window.dataLayer.push(obj);
    }

    /**
     * マッチング送信
     */
    function submitMatching() {
        pushDataLayer('form_submit', { 'form_name': 'subsidy_matching' });

        var chatContainer = document.getElementById('chat-container');
        chatContainer.style.display = 'none';
        progressContainer.style.display = 'none';
        resultContainer.style.display = 'block';

        resultContainer.innerHTML =
            '<div class="result-loading">' +
            '  <div class="spinner"></div>' +
            '  <p class="loading-main-text">診断結果を分析しています...</p>' +
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
                    matchResults = data.results;
                    renderResultsWithGate(data.results);
                } else {
                    matchResults = getSampleResults();
                    renderResultsWithGate(matchResults);
                }
            })
            .catch(function () {
                matchResults = getSampleResults();
                renderResultsWithGate(matchResults);
            });
    }

    /**
     * 結果画面（リードゲート付き）
     */
    function renderResultsWithGate(results) {
        pushDataLayer('matching_complete');
        var html = '';

        // ヘッダー
        html += '<div class="proposal-header">';
        html += '  <div class="proposal-header-badge">診断結果レポート</div>';
        html += '  <h2>' + results.length + '件の補助金が見つかりました！</h2>';
        html += '  <p>' + escapeHtml(answers.prefecture + (answers.city || '')) + 'の' + escapeHtml(industryLabelMap[answers.industry] || answers.industry) + '企業様向け</p>';
        html += '</div>';

        // 最初の1件を完全表示
        if (results.length > 0) {
            html += '<section class="proposal-section">';
            html += '  <div class="proposal-section-header"><h3>該当する補助金・助成金</h3><span class="proposal-section-count">' + results.length + '件</span></div>';
            html += renderSubsidyCard(results[0]);
            html += '</section>';
        }

        // 2件目以降はすりガラス + フォームを2件目に重ねる
        if (results.length > 1) {
            html += '<div id="blurred-section" style="position:relative;">';
            // 2件目以降のカード（ぼかし表示）
            html += '<div class="blurred-cards">';
            for (var i = 1; i < Math.min(results.length, 4); i++) {
                html += '<div class="subsidy-card-blurred">' + renderSubsidyCard(results[i]) + '</div>';
            }
            html += '</div>';
            // フォームを2件目の上に重ねて表示
            html += '<div class="blur-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;display:flex;align-items:flex-start;justify-content:center;padding-top:20px;background:linear-gradient(180deg,rgba(255,255,255,0.7) 0%,rgba(255,255,255,0.95) 30%,rgba(255,255,255,1) 60%);z-index:10;border-radius:8px;">';
            html += '  <div style="width:100%;max-width:480px;padding:0 20px;">';
            html += '  <h3 style="text-align:center;margin-bottom:8px;">該当するすべての補助金情報を見る</h3>';
            html += '  <p class="blur-overlay-desc" style="text-align:center;font-size:13px;color:#666;margin-bottom:16px;">会社情報をご入力いただくと、すべての診断結果をご覧いただけます。</p>';
            html += '  <div class="lead-gate-form" id="lead-gate-form">';
            html += '    <div class="lead-gate-field"><input type="text" id="gate-company" placeholder="会社名（必須）" class="lead-gate-input" required></div>';
            html += '    <div class="lead-gate-field"><input type="text" id="gate-name" placeholder="担当者名（必須）" class="lead-gate-input" required></div>';
            html += '    <div class="lead-gate-field"><input type="tel" id="gate-phone" placeholder="電話番号（必須）" class="lead-gate-input" required></div>';
            html += '    <div class="lead-gate-field"><input type="email" id="gate-email" placeholder="メールアドレス（必須）" class="lead-gate-input" required></div>';
            html += '    <div class="lead-gate-consent"><input type="checkbox" id="gate-consent"><label for="gate-consent"><a href="/privacy/" target="_blank" rel="noopener">個人情報の取り扱い</a>に同意する</label></div>';
            html += '    <button id="gate-submit-btn" class="btn btn-primary btn-large lead-gate-submit">すべての補助金を見る</button>';
            html += '    <p class="lead-gate-error" id="gate-error" style="display:none"></p>';
            html += '  </div>';
            html += '  </div>';
            html += '</div>';
            html += '</div>';
        }

        html += '<div id="full-results-section" style="display:none"></div>';
        resultContainer.innerHTML = html;

        var gateBtn = document.getElementById('gate-submit-btn');
        if (gateBtn) gateBtn.addEventListener('click', handleLeadGate);
    }

    function renderSubsidyCard(item) {
        var matchClass = item.match_level || 'medium';
        var badgeLabel = matchClass === 'high' ? '適合度：高' : matchClass === 'medium' ? '適合度：中' : '適合度：低';
        var badgeClass = matchClass === 'high' ? 'badge-high' : matchClass === 'medium' ? 'badge-medium' : 'badge-low';

        var html = '<div class="subsidy-card" data-match="' + matchClass + '">';
        html += '  <div class="subsidy-card-header"><h3 class="subsidy-card-title">' + escapeHtml(item.title) + '</h3><span class="subsidy-card-badge badge ' + badgeClass + '">' + badgeLabel + '</span></div>';
        html += '  <div class="subsidy-card-details">';
        html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">補助上限額</span><span class="subsidy-detail-value">' + (item.amount_text || formatAmount(item.max_amount)) + '</span></div>';
        html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">補助率</span><span class="subsidy-detail-value">' + escapeHtml(item.rate || '-') + '</span></div>';
        if (item.adoption_rate) {
            html += '    <div class="subsidy-detail-item"><span class="subsidy-detail-label">採択率</span><span class="subsidy-detail-value">' + Math.round(item.adoption_rate * 100) + '%</span></div>';
        }
        html += '  </div>';
        html += '  <p class="subsidy-card-summary">' + escapeHtml(item.summary || '') + '</p>';
        /* 公募要領リンク廃止 — 大半が404のため */
        html += '</div>';
        return html;
    }

    /**
     * リードゲート処理
     */
    function handleLeadGate() {
        var company = document.getElementById('gate-company').value.trim();
        var name    = document.getElementById('gate-name').value.trim();
        var phone   = document.getElementById('gate-phone').value.trim();
        var email   = document.getElementById('gate-email').value.trim();
        var consent = document.getElementById('gate-consent').checked;
        var errorEl = document.getElementById('gate-error');

        if (!company || !name || !phone || !email) {
            errorEl.textContent = '全ての項目を入力してください。';
            errorEl.style.display = 'block';
            return;
        }
        if (!isValidEmail(email)) {
            errorEl.textContent = '正しいメールアドレスを入力してください。';
            errorEl.style.display = 'block';
            return;
        }
        if (!consent) {
            errorEl.textContent = '個人情報の取り扱いに同意してください。';
            errorEl.style.display = 'block';
            return;
        }

        errorEl.style.display = 'none';
        var btn = document.getElementById('gate-submit-btn');
        btn.textContent = '送信中...';
        btn.disabled = true;

        var apiUrl = (typeof subsidyMatchApi !== 'undefined')
            ? subsidyMatchApi.root + 'subsidy/v1/register-lead'
            : '/wp-json/subsidy/v1/register-lead';
        var headers = { 'Content-Type': 'application/json' };
        if (typeof subsidyMatchApi !== 'undefined' && subsidyMatchApi.nonce) {
            headers['X-WP-Nonce'] = subsidyMatchApi.nonce;
        }

        var topSubsidies = matchResults ? matchResults.slice(0, 3).map(function(s) {
            return { title: s.title, max_amount: s.max_amount, amount_text: s.amount_text, rate: s.rate };
        }) : [];

        fetch(apiUrl, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                company_name: company,
                contact_name: name,
                phone: phone,
                email: email,
                prefecture: answers.prefecture,
                city: answers.city,
                industry: answers.industry,
                capital: answers.capital,
                employee_size: answers.employee_size,
                establishment_years: answers.establishment_years,
                matched_count: matchResults ? matchResults.length : 0,
                matched_ids: matchResults ? matchResults.map(function(r) { return r.id; }) : [],
                matched_subsidies: topSubsidies
            })
        })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    leadId = data.lead_id || 0;
                    unlockFullResults();
                } else {
                    btn.textContent = 'すべての補助金を見る';
                    btn.disabled = false;
                    errorEl.textContent = data.message || 'エラーが発生しました。';
                    errorEl.style.display = 'block';
                }
            })
            .catch(function() {
                unlockFullResults();
            });
    }

    /**
     * ゲート解除 → 全件表示
     */
    function unlockFullResults() {
        var blurred = document.getElementById('blurred-section');
        if (blurred) blurred.style.display = 'none';

        var fullSection = document.getElementById('full-results-section');
        fullSection.style.display = 'block';

        var html = '';

        html += '<section class="proposal-section">';
        for (var i = 1; i < matchResults.length; i++) {
            html += renderSubsidyCard(matchResults[i]);
        }
        html += '</section>';

        // システムニーズ質問
        html += '<section class="proposal-section system-interest-section" id="system-interest-section">';
        html += '  <div class="system-interest-card">';
        html += '    <h3>補助金を使ってシステム開発・導入を検討していますか？</h3>';
        html += '    <div class="system-interest-options">';
        html += '      <button class="btn btn-primary system-interest-btn" data-value="yes">はい、検討している</button>';
        html += '      <button class="btn btn-secondary system-interest-btn" data-value="no">いいえ、他の目的です</button>';
        html += '      <button class="btn btn-secondary system-interest-btn" data-value="undecided">まだわからない</button>';
        html += '    </div>';
        html += '  </div>';
        html += '</section>';

        // 無料相談CTA
        html += '<section class="proposal-section proposal-consult-section" id="consult-section" style="display:none">';
        html += '  <div class="consult-card">';
        html += '    <p class="consult-lead">ただし、あなたがやりたいことに該当するかは個別の確認が必要です。<br>プロに<strong>無料</strong>で相談してみませんか？</p>';
        html += '    <button class="btn btn-primary btn-large consult-open-btn" id="open-ai-chat-btn">無料相談する</button>';
        html += '  </div>';
        html += '</section>';

        // AIチャット
        html += '<section class="proposal-section ai-chat-section" id="ai-chat-section" style="display:none">';
        html += '  <div class="ai-chat-container">';
        html += '    <div class="ai-chat-header">';
        html += '      <div class="ai-chat-header-avatar">' + avatarSvg + '</div>';
        html += '      <div><strong>佐藤あかり</strong><br><span style="font-size:12px;color:#888">補助金アドバイザー</span></div>';
        html += '    </div>';
        html += '    <div class="ai-chat-messages" id="ai-chat-messages"></div>';
        html += '    <div class="ai-chat-input-row">';
        html += '      <input type="text" id="ai-chat-input" class="ai-chat-input" placeholder="質問を入力してください...">';
        html += '      <button id="ai-chat-send" class="btn btn-primary ai-chat-send-btn">送信</button>';
        html += '    </div>';
        html += '  </div>';
        html += '</section>';

        fullSection.innerHTML = html;

        // システムニーズボタン
        var siBtns = fullSection.querySelectorAll('.system-interest-btn');
        siBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var val = btn.getAttribute('data-value');
                handleSystemInterest(val);
                siBtns.forEach(function(b) { b.classList.remove('selected'); });
                btn.classList.add('selected');
            });
        });

        showToast('すべての補助金情報が表示されました');
        fullSection.scrollIntoView({ behavior: 'smooth' });
    }

    function handleSystemInterest(value) {
        if (leadId) {
            var apiUrl = (typeof subsidyMatchApi !== 'undefined')
                ? subsidyMatchApi.root + 'subsidy/v1/system-interest'
                : '/wp-json/subsidy/v1/system-interest';
            var headers = { 'Content-Type': 'application/json' };
            if (typeof subsidyMatchApi !== 'undefined' && subsidyMatchApi.nonce) {
                headers['X-WP-Nonce'] = subsidyMatchApi.nonce;
            }
            fetch(apiUrl, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ lead_id: leadId, system_interest: value })
            });
        }

        var consultSection = document.getElementById('consult-section');
        if (consultSection) {
            consultSection.style.display = 'block';
            consultSection.scrollIntoView({ behavior: 'smooth' });

            var openBtn = document.getElementById('open-ai-chat-btn');
            if (openBtn) {
                openBtn.addEventListener('click', function() { openAiChat(); });
            }
        }
    }

    var chatHistory = [];

    function openAiChat() {
        var chatSection = document.getElementById('ai-chat-section');
        chatSection.style.display = 'block';
        chatSection.scrollIntoView({ behavior: 'smooth' });

        var topSubsidy = matchResults && matchResults[0] ? matchResults[0] : null;
        var greeting = 'こんにちは！佐藤あかりです。';
        if (topSubsidy) {
            greeting += escapeHtml(topSubsidy.title) + '（最大' + (topSubsidy.amount_text || formatAmount(topSubsidy.max_amount)) + '）など、' + matchResults.length + '件の補助金が見つかりましたね。';
        }
        greeting += '具体的にどんなことに活用したいですか？';
        addAiChatMessage(greeting);

        var sendBtn = document.getElementById('ai-chat-send');
        var input = document.getElementById('ai-chat-input');
        sendBtn.addEventListener('click', function() { sendAiChat(); });
        var aiComposing = false;
        input.addEventListener('compositionstart', function() { aiComposing = true; });
        input.addEventListener('compositionend', function() { aiComposing = false; });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !aiComposing) { e.preventDefault(); sendAiChat(); }
        });
    }

    function addAiChatMessage(text) {
        var msgs = document.getElementById('ai-chat-messages');
        var row = document.createElement('div');
        row.className = 'ai-chat-row ai-chat-row-ai';
        row.innerHTML = '<div class="ai-chat-avatar">' + avatarSvg + '</div><div class="ai-chat-bubble ai-chat-bubble-ai">' + text + '</div>';
        msgs.appendChild(row);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function addUserChatMessage(text) {
        var msgs = document.getElementById('ai-chat-messages');
        var row = document.createElement('div');
        row.className = 'ai-chat-row ai-chat-row-user';
        row.innerHTML = '<div class="ai-chat-bubble ai-chat-bubble-user">' + escapeHtml(text) + '</div>';
        msgs.appendChild(row);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function addAiTypingIndicator() {
        var msgs = document.getElementById('ai-chat-messages');
        var row = document.createElement('div');
        row.className = 'ai-chat-row ai-chat-row-ai ai-chat-typing-row';
        row.innerHTML = '<div class="ai-chat-avatar">' + avatarSvg + '</div><div class="typing-dots"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
        msgs.appendChild(row);
        msgs.scrollTop = msgs.scrollHeight;
        return row;
    }

    function sendAiChat() {
        var input = document.getElementById('ai-chat-input');
        var message = input.value.trim();
        if (!message) return;

        input.value = '';
        addUserChatMessage(message);
        chatHistory.push({ role: 'user', content: message });

        var typingEl = addAiTypingIndicator();

        var apiUrl = (typeof subsidyMatchApi !== 'undefined')
            ? subsidyMatchApi.root + 'subsidy/v1/chat'
            : '/wp-json/subsidy/v1/chat';
        var headers = { 'Content-Type': 'application/json' };
        if (typeof subsidyMatchApi !== 'undefined' && subsidyMatchApi.nonce) {
            headers['X-WP-Nonce'] = subsidyMatchApi.nonce;
        }

        var topSubsidies = matchResults ? matchResults.slice(0, 5).map(function(s) {
            return { title: s.title, max_amount: formatAmount(s.max_amount), rate: s.rate || '' };
        }) : [];

        fetch(apiUrl, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                message: message,
                history: chatHistory,
                context: {
                    prefecture: answers.prefecture,
                    city: answers.city,
                    industry: industryLabelMap[answers.industry] || answers.industry,
                    employee_size: answers.employee_size,
                    matched_subsidies: topSubsidies
                }
            })
        })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                typingEl.remove();
                if (data.success && data.reply) {
                    chatHistory.push({ role: 'assistant', content: data.reply });
                    addAiChatMessage(escapeHtml(data.reply));
                    var userMessages = chatHistory.filter(function(h) { return h.role === 'user'; });
                    if (userMessages.length >= 3) showBookingButton();
                } else {
                    addAiChatMessage('申し訳ございません、うまく応答できませんでした。直接お電話でもご相談いただけます。');
                }
            })
            .catch(function() {
                typingEl.remove();
                addAiChatMessage('通信エラーが発生しました。恐れ入りますが、少し時間をおいてお試しください。');
            });
    }

    function showBookingButton() {
        var msgs = document.getElementById('ai-chat-messages');
        if (msgs.querySelector('.ai-chat-booking')) return;

        var div = document.createElement('div');
        div.className = 'ai-chat-booking';
        div.innerHTML = '<p>15分の無料面談で、最適な補助金活用プランをご提案します。</p>' +
            '<a href="' + getContactUrl() + '" class="btn btn-primary btn-large ai-chat-booking-btn">無料面談を予約する</a>';
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showToast(text) {
        var toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = text;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.classList.add('toast-fade-out');
            setTimeout(function() { toast.remove(); }, 400);
        }, 3000);
    }

    function getSampleResults() {
        return [
            { id: 0, title: 'IT導入補助金', max_amount: 4500000, rate: '1/2〜3/4', summary: '中小企業・小規模事業者がITツールを導入する際の経費の一部を補助する制度です。', deadline: '2026年6月30日', official_url: '', match_level: 'high', adoption_rate: 0.62 },
            { id: 0, title: 'ものづくり・商業・サービス補助金', max_amount: 12500000, rate: '1/2〜2/3', summary: '革新的サービス開発・試作品開発・生産プロセスの改善を行う際の設備投資等を支援します。', deadline: '2026年9月30日', official_url: '', match_level: 'medium', adoption_rate: 0.45 },
            { id: 0, title: '小規模事業者持続化補助金', max_amount: 2000000, rate: '2/3', summary: '小規模事業者が経営計画を策定して取り組む販路開拓等の取組を支援する制度です。', deadline: '2026年5月31日', official_url: '', match_level: 'medium', adoption_rate: 0.55 },
            { id: 0, title: '事業再構築補助金', max_amount: 150000000, rate: '1/2〜3/4', summary: '中小企業等の思い切った事業再構築を支援します。', deadline: '2026年7月31日', official_url: '', match_level: 'low', adoption_rate: 0.38 }
        ];
    }

    function formatAmount(amount) {
        if (!amount) return '-';
        if (amount >= 100000000) return (amount / 100000000) + '億円';
        else if (amount >= 10000) return Math.round(amount / 10000).toLocaleString() + '万円';
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
