<?php require_once "db.php"; require_login(); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Safety tools | SafeHer</title><link rel="stylesheet" href="style.css"></head><body class="app-page">
<aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">✦</span> SafeHer</a><div class="side-label">WORKSPACE</div><a class="side-link" href="dashboard.php">▦ <span>Dashboard</span></a><a class="side-link" href="contacts.php">♡ <span>Emergency contacts</span></a><a class="side-link" href="alert_history.php">◷ <span>Alert history</span></a><a class="side-link active" href="tools.php">☎ <span>Safety tools</span></a><a class="side-link" href="profile.php">☺ <span>My profile</span></a><?php if(($_SESSION["role"]??"")==="admin") echo '<a class="side-link" href="admin.php">♙ <span>Admin panel</span></a>'; ?><div class="side-bottom"><div class="avatar"><?=e(strtoupper(substr($_SESSION["name"],0,1)))?></div><div><b><?=e($_SESSION["name"])?></b><small>Personal account</small></div><a class="logout" href="logout.php">↗</a></div></aside>
<main class="app-main"><header class="topbar"><div><span class="eyebrow">SAFETY TOOLKIT</span><h1>Safety tools <span>☎</span></h1><p class="muted">Quick tools that run in your browser. Keep this page open when you use the timer.</p></div><a class="btn btn-small" href="sos.php">! SOS</a></header>
<div class="tools-grid">
<section class="tool-card"><div class="stat-icon pink">☎</div><h3>Fake incoming call</h3><p>Schedule a realistic ringing call to give yourself a reason to leave.</p><div class="row"><input id="fc-name" value="Amma" maxlength="30" aria-label="Caller name"><select id="fc-delay" aria-label="Delay"><option value="0">Now</option><option value="10">In 10 sec</option><option value="30">In 30 sec</option><option value="60">In 1 min</option></select><button class="btn btn-small" id="fc-start" type="button">Start</button></div></section>
<section class="tool-card"><div class="stat-icon violet">◔</div><h3>Check-in timer</h3><p>If you do not tap "I'm safe" before time runs out, a loud siren starts and you can save an SOS and notify contacts.</p><div class="row"><input id="ci-min" type="number" min="1" max="240" value="15" aria-label="Minutes"><button class="btn btn-small" id="ci-start" type="button">Start timer</button><button class="btn btn-small btn-ghost" id="ci-stop" type="button">Stop</button></div><p id="ci-status" style="margin:12px 0 0"></p></section>
<section class="tool-card"><div class="stat-icon peach">♪</div><h3>Loud siren</h3><p>Draws attention in public. Lower your volume before testing.</p><div class="row"><button class="btn btn-small btn-danger" id="siren-btn" type="button">Start siren</button></div></section>
<section class="tool-card"><div class="stat-icon pink">☀</div><h3>Bright screen light</h3><p>Turns your screen fully white as an emergency light. Tap to close.</p><div class="row"><button class="btn btn-small" id="light-btn" type="button">Turn on</button></div></section>
</div>
<section class="panel spaced-panel"><div class="panel-head"><div><h3>Emergency helplines (India)</h3><p class="muted">Tap to call. Please verify numbers for your state.</p></div></div><div class="helpline-list">
<a class="helpline" href="tel:112"><strong>112</strong><small>All-in-one emergency</small></a><a class="helpline" href="tel:100"><strong>100</strong><small>Police</small></a><a class="helpline" href="tel:1091"><strong>1091</strong><small>Women helpline</small></a><a class="helpline" href="tel:181"><strong>181</strong><small>Mahila helpline</small></a><a class="helpline" href="tel:108"><strong>108</strong><small>Ambulance</small></a><a class="helpline" href="tel:1930"><strong>1930</strong><small>Cyber crime</small></a></div></section>
<div class="overlay fake-call" id="call-ui"><div class="box"><div class="avatar-lg" id="call-av">A</div><h2 id="call-name">Amma</h2><p id="call-state">Incoming call…</p><div id="call-btns"><button class="btn btn-danger" id="call-decline" type="button">Decline</button><button class="btn" id="call-accept" type="button">Accept</button></div></div></div>
<div class="overlay" id="alarm-ui"><div class="box"><h2>Are you safe?</h2><p>Your check-in timer ran out.</p><button class="btn" id="al-safe" type="button">I'm safe</button><form method="post" action="sos.php" style="display:inline"><input type="hidden" name="notes" value="Check-in timer expired"><button class="btn btn-danger" type="submit">Save SOS &amp; notify contacts</button></form></div></div>
<div class="overlay" id="light-ui" style="background:#fff" onclick="this.classList.remove('show')"><p style="color:#999">Tap anywhere to close</p></div></main><script>
(function(){
var $=function(i){return document.getElementById(i)},AC=null,osc=null,gain=null,ring=null;
function ctx(){if(!AC){AC=new (window.AudioContext||window.webkitAudioContext)();}if(AC.state==="suspended")AC.resume();return AC;}
function sirenOn(){if(osc)return;var a=ctx();osc=a.createOscillator();gain=a.createGain();osc.type="square";gain.gain.value=.25;osc.connect(gain);gain.connect(a.destination);osc.start();
var up=true;osc._t=setInterval(function(){var t=a.currentTime;osc.frequency.setValueAtTime(up?600:1100,t);up=!up;},450);}
function sirenOff(){if(!osc)return;clearInterval(osc._t);osc.stop();osc=null;}
$("siren-btn").onclick=function(){if(osc){sirenOff();this.textContent="Start siren";}else{sirenOn();this.textContent="Stop siren";}};
$("light-btn").onclick=function(){$("light-ui").classList.add("show");};
// fake call
function ringStart(){var a=ctx();ring=setInterval(function(){var o=a.createOscillator(),g=a.createGain();o.frequency.value=480;g.gain.value=.15;o.connect(g);g.connect(a.destination);o.start();o.stop(a.currentTime+.6);},1600);if(navigator.vibrate)navigator.vibrate([400,200,400]);}
function ringStop(){clearInterval(ring);ring=null;}
var callTimer=null;
$("fc-start").onclick=function(){var n=($("fc-name").value||"Mom").trim(),d=parseInt($("fc-delay").value,10)*1000;ctx();$("call-name").textContent=n;$("call-av").textContent=n.charAt(0).toUpperCase();
setTimeout(function(){$("call-state").textContent="Incoming call…";$("call-btns").style.display="";$("call-ui").classList.add("show");ringStart();},d);};
$("call-decline").onclick=function(){ringStop();$("call-ui").classList.remove("show");clearInterval(callTimer);};
$("call-accept").onclick=function(){ringStop();$("call-btns").innerHTML='<button class="btn btn-danger" id="call-end" type="button">End call</button>';var s=0;$("call-state").textContent="00:00";
callTimer=setInterval(function(){s++;$("call-state").textContent=("0"+Math.floor(s/60)).slice(-2)+":"+("0"+s%60).slice(-2);},1000);
$("call-end").onclick=function(){clearInterval(callTimer);$("call-ui").classList.remove("show");$("call-btns").innerHTML='<button class="btn btn-danger" id="call-decline" type="button">Decline</button><button class="btn" id="call-accept" type="button">Accept</button>';location.reload();};};
// check-in timer (survives page reload via localStorage)
var K="safeher_checkin_end",tick=null;
function render(){var end=parseInt(localStorage.getItem(K)||"0",10);if(!end){$("ci-status").textContent="No timer running.";clearInterval(tick);return;}
var left=Math.round((end-Date.now())/1000);if(left<=0){localStorage.removeItem(K);clearInterval(tick);$("ci-status").textContent="Timer finished.";sirenOn();$("alarm-ui").classList.add("show");return;}
$("ci-status").textContent="Time left: "+Math.floor(left/60)+" min "+("0"+left%60).slice(-2)+" sec";}
function begin(){clearInterval(tick);render();tick=setInterval(render,1000);}
$("ci-start").onclick=function(){var m=Math.max(1,Math.min(240,parseInt($("ci-min").value,10)||15));ctx();localStorage.setItem(K,String(Date.now()+m*60000));begin();};
$("ci-stop").onclick=function(){localStorage.removeItem(K);render();};
$("al-safe").onclick=function(){sirenOff();$("siren-btn").textContent="Start siren";$("alarm-ui").classList.remove("show");};
if(localStorage.getItem(K))begin();else render();
})();
</script></body></html>