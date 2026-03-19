<?php
/**
 * Template Name: 補助金診断
 *
 * @package SubsidyMatch
 */

get_header();
?>

<main class="matching-page">
    <div class="container">

        <!-- 進捗バー -->
        <div class="progress-container">
            <div class="progress-info">
                <span class="progress-label">診断の進捗</span>
                <span class="progress-percent">0%</span>
            </div>
            <div class="progress-track">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
            <p class="progress-step">質問 <span class="current-step">1</span> / <span class="total-steps">8</span></p>
        </div>

        <!-- 質問コンテナ -->
        <div class="question-container">

            <!-- Q1: 所在地 -->
            <div class="question-slide" data-step="1">
                <h2 class="question-text">会社の所在地を教えてください</h2>
                <p class="question-sub">都道府県をお選びください</p>
                <div class="question-input">
                    <select class="form-control select-large" id="q-prefecture">
                        <option value="">選択してください</option>
                        <option value="01">北海道</option><option value="02">青森県</option>
                        <option value="03">岩手県</option><option value="04">宮城県</option>
                        <option value="05">秋田県</option><option value="06">山形県</option>
                        <option value="07">福島県</option><option value="08">茨城県</option>
                        <option value="09">栃木県</option><option value="10">群馬県</option>
                        <option value="11">埼玉県</option><option value="12">千葉県</option>
                        <option value="13">東京都</option><option value="14">神奈川県</option>
                        <option value="15">新潟県</option><option value="16">富山県</option>
                        <option value="17">石川県</option><option value="18">福井県</option>
                        <option value="19">山梨県</option><option value="20">長野県</option>
                        <option value="21">岐阜県</option><option value="22">静岡県</option>
                        <option value="23">愛知県</option><option value="24">三重県</option>
                        <option value="25">滋賀県</option><option value="26">京都府</option>
                        <option value="27">大阪府</option><option value="28">兵庫県</option>
                        <option value="29">奈良県</option><option value="30">和歌山県</option>
                        <option value="31">鳥取県</option><option value="32">島根県</option>
                        <option value="33">岡山県</option><option value="34">広島県</option>
                        <option value="35">山口県</option><option value="36">徳島県</option>
                        <option value="37">香川県</option><option value="38">愛媛県</option>
                        <option value="39">高知県</option><option value="40">福岡県</option>
                        <option value="41">佐賀県</option><option value="42">長崎県</option>
                        <option value="43">熊本県</option><option value="44">大分県</option>
                        <option value="45">宮崎県</option><option value="46">鹿児島県</option>
                        <option value="47">沖縄県</option>
                    </select>
                </div>
            </div>

            <!-- Q2: 業種 -->
            <div class="question-slide" data-step="2" style="display:none">
                <h2 class="question-text">業種を教えてください</h2>
                <p class="question-sub">該当する業種をお選びください</p>
                <div class="question-input">
                    <select class="form-control select-large" id="q-industry">
                        <option value="">選択してください</option>
                        <option value="manufacturing">製造業</option>
                        <option value="construction">建設業</option>
                        <option value="information_technology">情報通信業</option>
                        <option value="wholesale_retail">卸売業・小売業</option>
                        <option value="food_service">飲食サービス業</option>
                        <option value="accommodation">宿泊業</option>
                        <option value="medical_welfare">医療・福祉</option>
                        <option value="education">教育・学習支援業</option>
                        <option value="professional_services">専門・技術サービス業</option>
                        <option value="transportation">運輸業・郵便業</option>
                        <option value="real_estate">不動産業</option>
                        <option value="agriculture">農業・林業・漁業</option>
                        <option value="other">その他</option>
                    </select>
                </div>
            </div>

            <!-- Q3: 従業員数 -->
            <div class="question-slide" data-step="3" style="display:none">
                <h2 class="question-text">従業員数を教えてください</h2>
                <p class="question-sub">パート・アルバイトを含む人数をお選びください</p>
                <div class="question-input">
                    <div class="option-cards">
                        <label class="option-card">
                            <input type="radio" name="employee_size" value="1-5">
                            <span class="option-card-inner">
                                <span class="option-card-text">1〜5名</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="employee_size" value="6-20">
                            <span class="option-card-inner">
                                <span class="option-card-text">6〜20名</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="employee_size" value="21-50">
                            <span class="option-card-inner">
                                <span class="option-card-text">21〜50名</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="employee_size" value="51-100">
                            <span class="option-card-inner">
                                <span class="option-card-text">51〜100名</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="employee_size" value="101+">
                            <span class="option-card-inner">
                                <span class="option-card-text">101名以上</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Q4: 資本金 -->
            <div class="question-slide" data-step="4" style="display:none">
                <h2 class="question-text">資本金を教えてください</h2>
                <p class="question-sub">該当する範囲をお選びください</p>
                <div class="question-input">
                    <div class="option-cards">
                        <label class="option-card">
                            <input type="radio" name="capital" value="under_3m">
                            <span class="option-card-inner">
                                <span class="option-card-text">300万円未満</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="capital" value="3m_10m">
                            <span class="option-card-inner">
                                <span class="option-card-text">300万〜1,000万円</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="capital" value="10m_30m">
                            <span class="option-card-inner">
                                <span class="option-card-text">1,000万〜3,000万円</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="capital" value="30m_100m">
                            <span class="option-card-inner">
                                <span class="option-card-text">3,000万〜1億円</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="capital" value="over_100m">
                            <span class="option-card-inner">
                                <span class="option-card-text">1億円以上</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Q5: 課題（複数選択） -->
            <div class="question-slide" data-step="5" style="display:none">
                <h2 class="question-text">現在の経営課題を教えてください</h2>
                <p class="question-sub">該当するものを全てお選びください（複数選択可）</p>
                <div class="question-input">
                    <div class="option-cards option-cards-multi">
                        <label class="option-card option-card-checkbox">
                            <input type="checkbox" name="challenges" value="equipment">
                            <span class="option-card-inner">
                                <span class="option-card-text">設備投資がしたい</span>
                            </span>
                        </label>
                        <label class="option-card option-card-checkbox">
                            <input type="checkbox" name="challenges" value="it_dx">
                            <span class="option-card-inner">
                                <span class="option-card-text">IT化・DXを進めたい</span>
                            </span>
                        </label>
                        <label class="option-card option-card-checkbox">
                            <input type="checkbox" name="challenges" value="hiring">
                            <span class="option-card-inner">
                                <span class="option-card-text">人材を採用したい</span>
                            </span>
                        </label>
                        <label class="option-card option-card-checkbox">
                            <input type="checkbox" name="challenges" value="overseas">
                            <span class="option-card-inner">
                                <span class="option-card-text">海外展開を考えている</span>
                            </span>
                        </label>
                        <label class="option-card option-card-checkbox">
                            <input type="checkbox" name="challenges" value="rnd">
                            <span class="option-card-inner">
                                <span class="option-card-text">研究開発をしたい</span>
                            </span>
                        </label>
                        <label class="option-card option-card-checkbox">
                            <input type="checkbox" name="challenges" value="succession">
                            <span class="option-card-inner">
                                <span class="option-card-text">事業承継を考えている</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Q6: 売上規模 -->
            <div class="question-slide" data-step="6" style="display:none">
                <h2 class="question-text">年間売上規模を教えてください</h2>
                <p class="question-sub">直近の事業年度の売上高をお選びください</p>
                <div class="question-input">
                    <div class="option-cards">
                        <label class="option-card">
                            <input type="radio" name="annual_revenue" value="under_10m">
                            <span class="option-card-inner">
                                <span class="option-card-text">1,000万円未満</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="annual_revenue" value="10m_50m">
                            <span class="option-card-inner">
                                <span class="option-card-text">1,000万〜5,000万円</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="annual_revenue" value="50m_100m">
                            <span class="option-card-inner">
                                <span class="option-card-text">5,000万〜1億円</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="annual_revenue" value="100m_500m">
                            <span class="option-card-inner">
                                <span class="option-card-text">1億〜5億円</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="annual_revenue" value="over_500m">
                            <span class="option-card-inner">
                                <span class="option-card-text">5億円以上</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Q7: 補助金申請経験 -->
            <div class="question-slide" data-step="7" style="display:none">
                <h2 class="question-text">これまでに補助金を申請されたことはありますか？</h2>
                <p class="question-sub">過去の申請経験の有無をお選びください</p>
                <div class="question-input">
                    <div class="option-cards option-cards-two">
                        <label class="option-card">
                            <input type="radio" name="has_experience" value="1">
                            <span class="option-card-inner">
                                <span class="option-card-text">はい、申請したことがある</span>
                            </span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="has_experience" value="0">
                            <span class="option-card-inner">
                                <span class="option-card-text">いいえ、初めて</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Q8: メールアドレス -->
            <div class="question-slide" data-step="8" style="display:none">
                <h2 class="question-text">メールアドレスを入力してください</h2>
                <p class="question-sub">診断結果をお送りいたします</p>
                <div class="question-input">
                    <input type="email" class="form-control input-large" id="q-email"
                           placeholder="example@company.co.jp">
                    <p class="input-note">※ 入力いただいた情報は診断結果のご案内のみに使用いたします</p>
                </div>
            </div>
        </div>

        <!-- ナビゲーションボタン -->
        <div class="question-nav">
            <button class="btn btn-secondary btn-back" style="visibility:hidden">戻る</button>
            <button class="btn btn-primary btn-next">次へ</button>
        </div>

        <!-- 結果表示エリア -->
        <div class="result-container" style="display:none">
            <!-- matching.js で動的に描画 -->
        </div>

    </div>
</main>

<?php get_footer(); ?>
