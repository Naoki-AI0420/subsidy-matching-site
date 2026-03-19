/**
 * お問い合わせフォーム — バリデーション & 送信
 *
 * @package SubsidyMatch
 */
(function () {
    'use strict';

    var form = document.getElementById('contact-form');
    var messageEl = document.getElementById('form-message');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var data = {
            company_name: form.company_name.value.trim(),
            contact_name: form.contact_name.value.trim(),
            email: form.contact_email.value.trim(),
            phone: form.contact_phone.value.trim(),
            message: form.contact_message.value.trim()
        };

        // バリデーション
        if (!data.company_name || !data.contact_name || !data.email || !data.message) {
            showMessage('必須項目をすべてご入力ください。', 'error');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
            showMessage('正しいメールアドレスをご入力ください。', 'error');
            return;
        }

        var submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = '送信中...';

        var apiUrl = (typeof subsidyContactApi !== 'undefined')
            ? subsidyContactApi.root + 'subsidy/v1/contact'
            : '/wp-json/subsidy/v1/contact';

        var headers = {
            'Content-Type': 'application/json'
        };
        if (typeof subsidyContactApi !== 'undefined' && subsidyContactApi.nonce) {
            headers['X-WP-Nonce'] = subsidyContactApi.nonce;
        }

        fetch(apiUrl, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(data)
        })
            .then(function (res) { return res.json(); })
            .then(function (result) {
                if (result.success) {
                    form.style.display = 'none';
                    showMessage('お問い合わせありがとうございます。担当者より折り返しご連絡いたします。', 'success');
                } else {
                    showMessage('送信に失敗しました。お手数ですが、もう一度お試しください。', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = '送信する';
                }
            })
            .catch(function () {
                showMessage('送信に失敗しました。お手数ですが、もう一度お試しください。', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = '送信する';
            });
    });

    function showMessage(text, type) {
        messageEl.textContent = text;
        messageEl.className = 'form-message ' + type;
        messageEl.style.display = 'block';
    }
})();
