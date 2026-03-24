<?php
/**
 * AI チャット（Anthropic Claude Haiku）
 *
 * @package SubsidyMatch
 */

/**
 * .env ファイルから環境変数を読み込む
 */
function subsidy_match_load_env() {
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;

    // 複数パスを試行
    $paths = array(
        dirname(get_template_directory()) . '/../../.env',        // wp-content/themes/../../.env
        ABSPATH . '.env',                                          // /var/www/html/.env
        ABSPATH . '../.env',                                       // /var/www/.env
        dirname(ABSPATH) . '/.env',
        '/var/www/html/.env',
    );

    foreach ($paths as $path) {
        $real = realpath($path);
        if ($real && is_readable($real)) {
            $lines = file($real, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') continue;
                if (strpos($line, '=') === false) continue;
                list($key, $value) = explode('=', $line, 2);
                $key   = trim($key);
                $value = trim($value);
                // クォート除去
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                if (!defined($key)) {
                    define($key, $value);
                }
            }
            break;
        }
    }
}

/**
 * Anthropic API キーを取得
 */
function subsidy_match_get_anthropic_key() {
    subsidy_match_load_env();

    if (defined('ANTHROPIC_API_KEY')) {
        return ANTHROPIC_API_KEY;
    }

    // wp-config.php で定義されている場合
    $key = getenv('ANTHROPIC_API_KEY');
    if ($key) return $key;

    return '';
}

/**
 * 通知用メールアドレスを取得
 */
function subsidy_match_get_notification_email() {
    subsidy_match_load_env();
    if (defined('NOTIFICATION_EMAIL')) return NOTIFICATION_EMAIL;
    return get_option('admin_email');
}

/**
 * Discord Webhook URL を取得
 */
function subsidy_match_get_discord_webhook() {
    subsidy_match_load_env();
    if (defined('DISCORD_WEBHOOK_URL')) return DISCORD_WEBHOOK_URL;
    return '';
}

/**
 * AI チャット API ルート登録
 */
function subsidy_match_register_chat_route() {
    register_rest_route('subsidy/v1', '/chat', array(
        'methods'             => 'POST',
        'callback'            => 'subsidy_match_handle_chat',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'subsidy_match_register_chat_route');

/**
 * AI チャット処理
 */
function subsidy_match_handle_chat($request) {
    $params  = $request->get_json_params();
    $message = sanitize_text_field($params['message'] ?? '');
    $context = $params['context'] ?? array();
    $history = $params['history'] ?? array();

    if (empty($message)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'メッセージを入力してください。',
        ), 400);
    }

    $api_key = subsidy_match_get_anthropic_key();
    if (empty($api_key)) {
        // APIキーなしでもフォールバック応答を返す
        $fallback = subsidy_match_ai_fallback($message, $context);
        return new WP_REST_Response(array(
            'success' => true,
            'reply'   => $fallback,
        ), 200);
    }

    // コンテキストからシステムプロンプトを構築
    $prefecture = sanitize_text_field($context['prefecture'] ?? '');
    $city       = sanitize_text_field($context['city'] ?? '');
    $industry   = sanitize_text_field($context['industry'] ?? '');
    $employee   = sanitize_text_field($context['employee_size'] ?? '');
    $subsidies  = isset($context['matched_subsidies']) && is_array($context['matched_subsidies'])
                    ? $context['matched_subsidies'] : array();

    $subsidy_info = '';
    foreach (array_slice($subsidies, 0, 5) as $s) {
        $title  = sanitize_text_field($s['title'] ?? '');
        $amount = sanitize_text_field($s['max_amount'] ?? '');
        $rate   = sanitize_text_field($s['rate'] ?? '');
        $subsidy_info .= "- {$title}（最大{$amount}、補助率{$rate}）\n";
    }

    $system_prompt = <<<PROMPT
あなたは「佐藤あかり」、株式会社Growing upの補助金アドバイザーです。23歳、明るく親しみやすい話し方をします。

役割:
- ユーザーの「やりたいこと」を聞いて、最適な補助金を提案する
- 補助金の具体的な金額、補助率、対象経費を説明する
- 最終的に「15分の無料面談」に誘導する

コンテキスト:
- 所在地: {$prefecture}{$city}
- 業種: {$industry}
- 従業員規模: {$employee}
- マッチした補助金:
{$subsidy_info}

ルール:
- 返答は200文字以内で簡潔に
- 必ず具体的な補助金名と金額を含める
- 3往復以内に「無料面談のご予約はいかがですか？」に着地する
- 「詳しくは面談で」は使わない。チャット内でも具体的に答える
- 敬語だが堅すぎない。親しみやすく
PROMPT;

    // メッセージ履歴を構築
    $messages = array();
    if (is_array($history)) {
        foreach ($history as $h) {
            $role = ($h['role'] ?? '') === 'user' ? 'user' : 'assistant';
            $content = sanitize_text_field($h['content'] ?? '');
            if ($content) {
                $messages[] = array('role' => $role, 'content' => $content);
            }
        }
    }
    $messages[] = array('role' => 'user', 'content' => $message);

    // Anthropic API 呼び出し
    $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
        'timeout' => 30,
        'headers' => array(
            'Content-Type'      => 'application/json',
            'x-api-key'         => $api_key,
            'anthropic-version' => '2023-06-01',
        ),
        'body' => wp_json_encode(array(
            'model'      => 'claude-3-5-haiku-20241022',
            'max_tokens' => 512,
            'system'     => $system_prompt,
            'messages'   => $messages,
        )),
    ));

    if (is_wp_error($response)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => '通信エラーが発生しました。',
        ), 500);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($body['content'][0]['text'])) {
        // エラー詳細をログ
        $error_detail = isset($body['error']) ? $body['error']['message'] : wp_json_encode($body);
        error_log('[SubsidyMatch AI Chat] API Error: ' . $error_detail);
        
        // ユーザーにはフレンドリーなメッセージ + フォールバック
        $fallback = subsidy_match_ai_fallback($message, $context);
        if ($fallback) {
            return new WP_REST_Response(array(
                'success' => true,
                'reply'   => $fallback,
            ), 200);
        }
        
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'ただいま混み合っております。お電話（03-XXXX-XXXX）または下のお問い合わせフォームからもご相談いただけます。',
        ), 500);
    }

    return new WP_REST_Response(array(
        'success' => true,
        'reply'   => $body['content'][0]['text'],
    ), 200);
}

/**
 * APIエラー時のフォールバック応答
 */
function subsidy_match_ai_fallback($message, $context) {
    $msg = $message;
    $subsidies = isset($context['matched_subsidies']) ? $context['matched_subsidies'] : array();
    $top = !empty($subsidies) ? $subsidies[0] : null;
    
    // キーワードベースの応答
    if (mb_strpos($msg, '予約') !== false || mb_strpos($msg, 'システム') !== false || mb_strpos($msg, 'IT') !== false) {
        return 'システム導入をお考えですね！IT導入補助金（補助率1/2〜3/4、最大450万円）がぴったりです。予約システムや顧客管理など、幅広いITツールが対象になります。詳しい要件や申請のコツを15分の無料面談でご説明できますが、ご都合いかがですか？';
    }
    if (mb_strpos($msg, 'EC') !== false || mb_strpos($msg, 'ec') !== false || mb_strpos($msg, 'ネット') !== false || mb_strpos($msg, 'オンライン') !== false) {
        return 'ECサイトの構築ですね！IT導入補助金やIT小規模事業者持続化補助金が活用できます。補助率2/3で最大50万円、IT導入補助金なら最大450万円です。具体的な申請方法を無料面談でご説明できますが、いかがですか？';
    }
    if (mb_strpos($msg, '設備') !== false || mb_strpos($msg, '機械') !== false || mb_strpos($msg, '工場') !== false) {
        return '設備投資をお考えですね！ものづくり補助金（最大1,250万円、補助率1/2〜2/3）が最適です。生産設備、検査装置、工作機械などが対象です。申請のポイントを無料面談でお伝えできますが、ご都合いかがですか？';
    }
    if (mb_strpos($msg, '人') !== false || mb_strpos($msg, '採用') !== false || mb_strpos($msg, '雇用') !== false) {
        return '人材確保をお考えですね！雇用関連の助成金（キャリアアップ助成金、トライアル雇用助成金など）が活用できます。採用計画に合わせた最適な助成金を無料面談でご提案できますが、いかがですか？';
    }
    if ($top) {
        return $top['title'] . '（最大' . ($top['amount_text'] ?: '') . '）についてのご質問ですね。この補助金の詳しい申請要件や採択のコツを、15分の無料面談でご説明できます。ご都合の良い日時はありますか？';
    }
    return '気になる補助金はありましたか？具体的にどんなことに活用したいか教えていただければ、最適な補助金をご提案できます。また、15分の無料面談で詳しくご説明することもできますよ。';
}
