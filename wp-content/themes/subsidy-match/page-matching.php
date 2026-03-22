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
            <p class="progress-step">質問 <span class="current-step">1</span> / <span class="total-steps">14</span></p>
        </div>

        <!-- チャットコンテナ -->
        <div class="chat-container" id="chat-container">
            <div class="chat-messages" id="chat-messages"></div>
            <div class="chat-input-area" id="chat-input-area"></div>
        </div>

        <!-- 結果表示エリア -->
        <div class="result-container" style="display:none"></div>

    </div>
</main>

<!-- 離脱防止ポップアップ（診断ページ用） -->
<div class="exit-modal-overlay" id="exit-modal-matching" style="display:none">
    <div class="exit-modal">
        <button class="exit-modal-close" id="exit-modal-matching-close" aria-label="閉じる">&times;</button>
        <h3 class="exit-modal-title">ここで離脱するともったいない！</h3>
        <p class="exit-modal-desc" id="exit-modal-matching-desc">あと少しで結果がわかります。<br>御社に合った補助金情報を見逃さないでください。</p>
        <button class="btn btn-primary exit-modal-btn" id="exit-modal-matching-resume">診断を続ける</button>
        <p class="exit-modal-note">※ あと数問で診断完了です</p>
    </div>
</div>

<style>
.exit-modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:10000;display:flex;align-items:center;justify-content:center;padding:20px;animation:fadeInOverlay .3s ease}
@keyframes fadeInOverlay{from{opacity:0}to{opacity:1}}
.exit-modal{background:#fff;border-radius:8px;padding:48px 40px 40px;max-width:460px;width:100%;text-align:center;position:relative;animation:slideUpModal .3s ease}
@keyframes slideUpModal{from{transform:translateY(24px);opacity:0}to{transform:translateY(0);opacity:1}}
.exit-modal-close{position:absolute;top:12px;right:16px;background:none;border:none;font-size:28px;color:#999;cursor:pointer;line-height:1;padding:4px}
.exit-modal-close:hover{color:#333}
.exit-modal-title{font-size:22px;font-weight:700;color:#003366;margin-bottom:12px}
.exit-modal-desc{font-size:15px;color:#555;line-height:1.7;margin-bottom:24px}
.exit-modal-btn{font-size:16px;font-weight:700;padding:16px 40px}
.exit-modal-note{font-size:12px;color:#999;margin-top:12px;margin-bottom:0}
@media(max-width:768px){.exit-modal{padding:36px 24px 32px}.exit-modal-title{font-size:19px}.exit-modal-desc br{display:none}}
</style>

<script>
(function(){
'use strict';
var modal=document.getElementById('exit-modal-matching'),closeBtn=document.getElementById('exit-modal-matching-close'),resumeBtn=document.getElementById('exit-modal-matching-resume'),descEl=document.getElementById('exit-modal-matching-desc');
if(!modal)return;
var CN='exit_popup_matching_shown',triggered=false;
if(document.cookie.indexOf(CN+'=1')!==-1)return;
function getRemaining(){var c=document.querySelector('.current-step'),t=document.querySelector('.total-steps');if(c&&t)return(parseInt(t.textContent,10)||14)-(parseInt(c.textContent,10)||1);return 10}
function show(){if(triggered)return;var r=document.querySelector('.result-container');if(r&&r.style.display!=='none')return;triggered=true;var rem=getRemaining();if(descEl)descEl.innerHTML='あと <strong>'+rem+'問</strong> で結果がわかります！<br>御社に合った補助金情報を見逃さないでください。';modal.style.display='flex';var d=new Date();d.setTime(d.getTime()+864e5);document.cookie=CN+'=1;expires='+d.toUTCString()+';path=/'}
function hide(){modal.style.display='none'}
document.addEventListener('mouseleave',function(e){if(e.clientY<10)show()});
if(closeBtn)closeBtn.addEventListener('click',hide);
if(resumeBtn)resumeBtn.addEventListener('click',hide);
modal.addEventListener('click',function(e){if(e.target===modal)hide()});
document.addEventListener('keydown',function(e){if(e.key==='Escape')hide()});
})();
</script>

<?php get_footer(); ?>
