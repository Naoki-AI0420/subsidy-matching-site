<?php
/**
 * Template Name: お問い合わせ
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main class="chat-contact-page">
    <div class="chat-contact-container">
        <div class="chat-contact-header">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/operator-akari.svg" alt="佐藤あかり" class="chat-contact-avatar">
            <div class="chat-contact-header-info">
                <span class="chat-contact-name">佐藤 あかり</span>
                <span class="chat-contact-role">補助金アドバイザー ／ 株式会社Growing up</span>
            </div>
            <span class="chat-contact-status">● オンライン</span>
        </div>

        <div class="chat-contact-messages" id="chat-messages">
            <!-- チャットメッセージがJSで挿入される -->
        </div>

        <div class="chat-contact-input-area" id="chat-input-area">
            <!-- 入力UIがJSで動的に変わる -->
        </div>
    </div>
</main>

<style>
.chat-contact-page {
    background: #E8ECF0;
    min-height: calc(100vh - 60px);
    padding: 0;
}

.chat-contact-container {
    max-width: 520px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    background: #F5F5F5;
}

.chat-contact-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    background: #003366;
    color: #FFFFFF;
    flex-shrink: 0;
}

.chat-contact-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.3);
}

.chat-contact-header-info {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.chat-contact-name {
    font-size: 14px;
    font-weight: 700;
}

.chat-contact-role {
    font-size: 11px;
    color: rgba(255,255,255,0.7);
}

.chat-contact-status {
    font-size: 11px;
    color: #4CAF50;
}

.chat-contact-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* オペレーターバブル */
.chat-bubble-op {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    max-width: 85%;
}

.chat-bubble-op-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    flex-shrink: 0;
}

.chat-bubble-op-text {
    background: #FFFFFF;
    border-radius: 0 12px 12px 12px;
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.6;
    color: #333333;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

/* ユーザーバブル */
.chat-bubble-user {
    align-self: flex-end;
    max-width: 75%;
}

.chat-bubble-user-text {
    background: #003366;
    color: #FFFFFF;
    border-radius: 12px 0 12px 12px;
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.6;
}

/* タイピングインジケーター */
.chat-typing {
    display: flex;
    gap: 8px;
    align-items: flex-end;
}

.chat-typing-dots {
    background: #FFFFFF;
    border-radius: 0 12px 12px 12px;
    padding: 12px 20px;
    display: flex;
    gap: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

.chat-typing-dot {
    width: 6px;
    height: 6px;
    background: #999;
    border-radius: 50%;
    animation: chatTyping 1.2s infinite;
}

.chat-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.chat-typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes chatTyping {
    0%, 100% { opacity: 0.3; transform: translateY(0); }
    50% { opacity: 1; transform: translateY(-3px); }
}

/* 選択肢ボタン */
.chat-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 8px 16px 16px;
}

.chat-option-btn {
    background: #FFFFFF;
    border: 2px solid #003366;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    color: #003366;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}

.chat-option-btn:hover {
    background: #003366;
    color: #FFFFFF;
}

/* テキスト入力 */
.chat-input-row {
    display: flex;
    gap: 8px;
    padding: 12px 16px;
    background: #FFFFFF;
    border-top: 1px solid #E0E0E0;
}

.chat-input-field {
    flex: 1;
    border: 1px solid #CCCCCC;
    border-radius: 20px;
    padding: 10px 16px;
    font-size: 14px;
    outline: none;
}

.chat-input-field:focus {
    border-color: #003366;
}

.chat-send-btn {
    background: #003366;
    color: #FFFFFF;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* 完了メッセージ */
.chat-complete-card {
    background: #E8F5E9;
    border: 1px solid #A5D6A7;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin: 8px 0;
}

.chat-complete-card h3 {
    color: #2E7D32;
    font-size: 16px;
    margin-bottom: 8px;
}

.chat-complete-card p {
    font-size: 13px;
    color: #555555;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .chat-contact-container {
        max-width: 100%;
    }

    .chat-bubble-op {
        max-width: 90%;
    }
}
</style>

<script>
(function() {
    var operatorIcon = '<?php echo get_template_directory_uri(); ?>/assets/images/operator-akari.svg';
    var messagesEl = document.getElementById('chat-messages');
    var inputArea = document.getElementById('chat-input-area');
    var contactData = {};
    var step = 0;

    var flow = [
        { type: 'op', text: 'こんにちは！補助金アドバイザーの佐藤あかりです。' },
        { type: 'op', text: 'ご相談内容をお聞かせください。どのようなことでもお気軽にどうぞ。', delay: 800 },
        { type: 'options', key: 'inquiry_type', options: [
            { label: '補助金の申請について相談したい', value: 'application' },
            { label: '診断結果について詳しく聞きたい', value: 'result' },
            { label: 'システム導入の見積もりが欲しい', value: 'estimate' },
            { label: 'その他のご相談', value: 'other' },
        ]},
        { type: 'op', text: 'ありがとうございます！専門スタッフが対応いたしますので、ご連絡先を教えてください。' },
        { type: 'input', key: 'company_name', placeholder: '会社名を入力', label: '会社名を教えてください。' },
        { type: 'input', key: 'contact_name', placeholder: 'お名前を入力', label: 'ご担当者のお名前は？' },
        { type: 'input', key: 'email', placeholder: 'メールアドレスを入力', label: 'メールアドレスをお願いします。' },
        { type: 'input', key: 'phone', placeholder: '電話番号を入力（任意）', label: 'お電話番号もいただけますか？（任意です）', optional: true },
        { type: 'op', text: 'ありがとうございます！最後に、具体的なご相談内容があればお聞かせください。' },
        { type: 'textarea', key: 'message', placeholder: 'ご相談内容を入力' },
        { type: 'submit' },
    ];

    function addOpBubble(text, callback) {
        // タイピングインジケーター
        var typing = document.createElement('div');
        typing.className = 'chat-typing';
        typing.innerHTML = '<img src="' + operatorIcon + '" class="chat-bubble-op-icon" alt="">' +
            '<div class="chat-typing-dots"><span class="chat-typing-dot"></span><span class="chat-typing-dot"></span><span class="chat-typing-dot"></span></div>';
        messagesEl.appendChild(typing);
        messagesEl.scrollTop = messagesEl.scrollHeight;

        setTimeout(function() {
            messagesEl.removeChild(typing);
            var bubble = document.createElement('div');
            bubble.className = 'chat-bubble-op';
            bubble.innerHTML = '<img src="' + operatorIcon + '" class="chat-bubble-op-icon" alt="あかり">' +
                '<div class="chat-bubble-op-text">' + text + '</div>';
            messagesEl.appendChild(bubble);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            if (callback) callback();
        }, 800);
    }

    function addUserBubble(text) {
        var bubble = document.createElement('div');
        bubble.className = 'chat-bubble-user';
        bubble.innerHTML = '<div class="chat-bubble-user-text">' + escapeHtml(text) + '</div>';
        messagesEl.appendChild(bubble);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showOptions(options, callback) {
        inputArea.innerHTML = '';
        var wrap = document.createElement('div');
        wrap.className = 'chat-options';
        options.forEach(function(opt) {
            var btn = document.createElement('button');
            btn.className = 'chat-option-btn';
            btn.textContent = opt.label;
            btn.onclick = function() {
                addUserBubble(opt.label);
                inputArea.innerHTML = '';
                callback(opt.value);
            };
            wrap.appendChild(btn);
        });
        inputArea.appendChild(wrap);
    }

    function showTextInput(placeholder, callback, isTextarea) {
        inputArea.innerHTML = '';
        var row = document.createElement('div');
        row.className = 'chat-input-row';
        var input;
        if (isTextarea) {
            input = document.createElement('textarea');
            input.rows = 3;
            input.style.borderRadius = '12px';
        } else {
            input = document.createElement('input');
            input.type = 'text';
        }
        input.className = 'chat-input-field';
        input.placeholder = placeholder;

        var btn = document.createElement('button');
        btn.className = 'chat-send-btn';
        btn.innerHTML = '➤';
        btn.onclick = function() { submit(); };
        if (!isTextarea) {
            input.onkeydown = function(e) { if (e.key === 'Enter') { e.preventDefault(); submit(); } };
        }

        function submit() {
            var val = input.value.trim();
            if (!val && !flow[step - 1].optional) return;
            addUserBubble(val || '（スキップ）');
            inputArea.innerHTML = '';
            callback(val);
        }

        row.appendChild(input);
        row.appendChild(btn);
        inputArea.appendChild(row);
        input.focus();
    }

    function processStep() {
        if (step >= flow.length) return;
        var s = flow[step];

        if (s.type === 'op') {
            addOpBubble(s.text, function() {
                step++;
                setTimeout(function() { processStep(); }, s.delay || 300);
            });
        } else if (s.type === 'options') {
            addOpBubble(flow[step - 1] ? '' : '', function() {});
            showOptions(s.options, function(val) {
                contactData[s.key] = val;
                step++;
                processStep();
            });
        } else if (s.type === 'input') {
            addOpBubble(s.label, function() {
                showTextInput(s.placeholder, function(val) {
                    contactData[s.key] = val;
                    step++;
                    processStep();
                });
            });
        } else if (s.type === 'textarea') {
            showTextInput(s.placeholder, function(val) {
                contactData.message = val;
                step++;
                processStep();
            }, true);
        } else if (s.type === 'submit') {
            submitContact();
        }
    }

    function submitContact() {
        addOpBubble('送信しています...', function() {
            var apiUrl = (typeof subsidyMatchApi !== 'undefined')
                ? subsidyMatchApi.root + 'subsidy/v1/contact'
                : '/wp-json/subsidy/v1/contact';

            fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    company_name: contactData.company_name || '',
                    contact_name: contactData.contact_name || '',
                    email: contactData.email || '',
                    phone: contactData.phone || '',
                    message: (contactData.inquiry_type || '') + '\n' + (contactData.message || ''),
                })
            }).then(function() {
                showComplete();
            }).catch(function() {
                showComplete();
            });
        });
    }

    function showComplete() {
        var card = document.createElement('div');
        card.className = 'chat-complete-card';
        card.innerHTML = '<h3>✓ 送信完了</h3>' +
            '<p><strong>' + escapeHtml(contactData.contact_name || '') + '</strong> 様<br>' +
            'お問い合わせありがとうございます。<br>1〜2営業日以内にご連絡いたします。</p>';
        messagesEl.appendChild(card);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        inputArea.innerHTML = '';

        addOpBubble('お問い合わせありがとうございます！専門スタッフより折り返しご連絡いたしますので、少々お待ちください。', function() {});

        if (typeof window.dataLayer !== 'undefined') {
            window.dataLayer.push({ event: 'contact_submit', inquiry_type: contactData.inquiry_type });
        }
    }

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // 開始
    processStep();
})();
</script>

<?php get_footer(); ?>
