<?php
require_once "db.php"; require_login(); $uid=(int)$_SESSION["user_id"]; $error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $lat=trim($_POST["latitude"]??""); $lng=trim($_POST["longitude"]??""); $notes=trim($_POST["notes"]??"");
    $latVal=($lat!=="" && is_numeric($lat) && abs((float)$lat)<=90)?(float)$lat:null;
    $lngVal=($lng!=="" && is_numeric($lng) && abs((float)$lng)<=180)?(float)$lng:null;
    if($latVal===null || $lngVal===null){ $latVal=null; $lngVal=null; }
    if(strlen($notes)>500) $notes=substr($notes,0,500);
    $location=($latVal!==null && $lngVal!==null) ? $lat.", ".$lng : "Location not shared";
    $stmt=$conn->prepare("INSERT INTO alerts(user_id,alert_type,location_text,latitude,longitude,notes) VALUES(?,'SOS',?,?,?,?)");
    $stmt->bind_param("isdds",$uid,$location,$latVal,$lngVal,$notes); $stmt->execute();
    flash("SOS record saved. Remember: no SMS or emergency call was sent.", "warning"); header("Location: alert_share.php?id=".$conn->insert_id); exit;
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SOS Alert | SafeHer</title><link rel="stylesheet" href="style.css"><script src="script.js" defer></script></head><body class="app-page">
<aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">✦</span> SafeHer</a><div class="side-label">WORKSPACE</div><a class="side-link" href="dashboard.php">▦ <span>Dashboard</span></a><a class="side-link" href="contacts.php">♡ <span>Emergency contacts</span></a><a class="side-link active" href="sos.php">! <span>Create SOS</span></a><a class="side-link" href="alert_history.php">◷ <span>Alert history</span></a><a class="side-link" href="tools.php">☎ <span>Safety tools</span></a><a class="side-link" href="profile.php">☺ <span>My profile</span></a><div class="side-bottom"><div class="avatar"><?=e(strtoupper(substr($_SESSION["name"],0,1)))?></div><div><b><?=e($_SESSION["name"])?></b><small>Personal account</small></div><a class="logout" href="logout.php">↗</a></div></aside>
<main class="app-main"><header class="topbar"><div><span class="eyebrow">SAFETY ALERT</span><h1>Create an SOS record <span>⌁</span></h1><p class="muted">Add a record and optionally share your current browser location.</p></div><a class="btn btn-small" href="dashboard.php">← Dashboard</a></header>
<div class="sos-layout"><section class="sos-card"><div class="sos-heart">!</div><span class="eyebrow">SOS ALERT RECORD</span><h2>Need help?</h2><p>Press the button below to prepare an alert record. Review the confirmation before saving.</p><div class="location-box"><span class="location-icon">⌖</span><div><b>Optional location</b><small id="location-status">Location has not been requested.</small></div><button type="button" class="btn btn-ghost btn-small" id="get-location">Get location</button></div>
<form method="post" id="sos-form"><input type="hidden" name="latitude" id="latitude"><input type="hidden" name="longitude" id="longitude"><label>Optional note<textarea name="notes" maxlength="500" rows="3" placeholder="Add a short note (optional)"></textarea></label><button class="sos-button" type="button" id="sos-go"><span>!</span> Start SOS (5-second countdown)</button></form><p class="safety-disclaimer">If you are in immediate danger, contact local emergency services directly. This demo does not send an alert to anyone.</p></section>
<section class="panel sos-side"><h3>Before you continue</h3><div class="safety-tip"><span>01</span><div><b>Check your trusted contacts</b><p>Keep phone numbers current on the contacts page.</p></div></div><div class="safety-tip"><span>02</span><div><b>Share location only if comfortable</b><p>Your browser may ask for location permission. It is optional.</p></div></div><div class="safety-tip"><span>03</span><div><b>Know what this button does</b><p>It saves a record, then lets you open WhatsApp/SMS for each contact. It does not send anything by itself.</p></div></div></section></div></main>
<div class="overlay" id="sos-overlay"><div class="box"><h2>Sending SOS record in…</h2><div class="big" id="sos-count">5</div><p>Your location will be attached if you allowed it.</p><button class="btn btn-ghost" type="button" id="sos-cancel">Cancel — I'm okay</button><button class="btn btn-danger" type="button" id="sos-now">Save now</button></div></div>
<script>
(function(){var go=document.getElementById("sos-go"),ov=document.getElementById("sos-overlay"),c=document.getElementById("sos-count"),f=document.getElementById("sos-form"),t,n;
function stop(){clearInterval(t);ov.classList.remove("show");}
function submit(){stop();f.submit();}
go.onclick=function(){n=5;c.textContent=n;ov.classList.add("show");var gl=document.getElementById("get-location");if(gl&&!document.getElementById("latitude").value)gl.click();
t=setInterval(function(){n--;c.textContent=n;if(n<=0)submit();},1000);};
document.getElementById("sos-cancel").onclick=stop;document.getElementById("sos-now").onclick=submit;})();
</script></body></html>