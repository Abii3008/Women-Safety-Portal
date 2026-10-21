<?php
require_once "db.php"; require_login(); $uid=(int)$_SESSION["user_id"];
$id=(int)($_GET["id"] ?? 0);
$stmt=$conn->prepare("SELECT id,latitude,longitude,notes,alert_time FROM alerts WHERE id=? AND user_id=?"); $stmt->bind_param("ii",$id,$uid); $stmt->execute(); $alert=$stmt->get_result()->fetch_assoc();
if(!$alert){ header("Location: alert_history.php"); exit; }
$stmt=$conn->prepare("SELECT sos_message FROM users WHERE id=?"); $stmt->bind_param("i",$uid); $stmt->execute(); $custom=trim($stmt->get_result()->fetch_assoc()["sos_message"] ?? "");
$msg=($custom!=="" ? $custom : "I need help. Please call me or contact emergency services.")." - ".$_SESSION["name"];
if($alert["latitude"]!==null) $msg.=" My location: https://maps.google.com/?q=".$alert["latitude"].",".$alert["longitude"];
if(trim($alert["notes"])!=="") $msg.=" Note: ".$alert["notes"];
$stmt=$conn->prepare("SELECT contact_name,phone,relationship,is_primary FROM emergency_contacts WHERE user_id=? ORDER BY is_primary DESC,id DESC"); $stmt->bind_param("i",$uid); $stmt->execute(); $contacts=$stmt->get_result();
function wa_number($p){ $d=preg_replace('/\D+/','',$p); if(strlen($d)===10) $d="91".$d; return $d; }
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Share alert | SafeHer</title><link rel="stylesheet" href="style.css"></head><body class="app-page">
<aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">✦</span> SafeHer</a><div class="side-label">WORKSPACE</div><a class="side-link" href="dashboard.php">▦ <span>Dashboard</span></a><a class="side-link" href="contacts.php">♡ <span>Emergency contacts</span></a><a class="side-link active" href="alert_history.php">◷ <span>Alert history</span></a><a class="side-link" href="tools.php">☎ <span>Safety tools</span></a><a class="side-link" href="profile.php">☺ <span>My profile</span></a><?php if(($_SESSION["role"]??"")==="admin") echo '<a class="side-link" href="admin.php">♙ <span>Admin panel</span></a>'; ?><div class="side-bottom"><div class="avatar"><?=e(strtoupper(substr($_SESSION["name"],0,1)))?></div><div><b><?=e($_SESSION["name"])?></b><small>Personal account</small></div><a class="logout" href="logout.php">↗</a></div></aside>
<main class="app-main"><header class="topbar"><div><span class="eyebrow">SOS RECORD SAVED</span><h1>Now tell your people <span>✉</span></h1><p class="muted">Your record is saved. Tap a button to open WhatsApp or SMS with the message already written - then press send on your phone.</p></div><a class="btn btn-small" href="alert_history.php">Alert history</a></header>
<div class="flash warning">This portal cannot send messages by itself. If you are in danger, call emergency services now.</div>
<div class="tools-grid" style="margin-bottom:20px"><a class="helpline" href="tel:112"><strong>112</strong><small>National emergency number</small></a><a class="helpline" href="tel:1091"><strong>1091</strong><small>Women helpline</small></a></div>
<section class="panel"><div class="panel-head"><div><h3>Message that will be shared</h3></div></div><p class="muted" style="background:#faf6ff;padding:14px;border-radius:12px;word-break:break-word"><?=e($msg)?></p>
<div class="share-list"><?php if($contacts->num_rows): while($r=$contacts->fetch_assoc()): $n=wa_number($r["phone"]); ?>
<div class="share-row"><div class="contact-avatar"><?=e(strtoupper(substr($r["contact_name"],0,1)))?></div><div class="contact-info"><b><?=e($r["contact_name"])?> <?=$r["is_primary"]?"★":""?></b><small><?=e($r["relationship"] ?: "Trusted contact")?></small></div>
<a class="btn btn-small" target="_blank" rel="noopener" href="https://wa.me/<?=e($n)?>?text=<?=e(rawurlencode($msg))?>">WhatsApp</a>
<a class="btn btn-small btn-ghost" href="sms:<?=e(preg_replace('/[^0-9+]/','',$r["phone"]))?>?body=<?=e(rawurlencode($msg))?>">SMS</a>
<a class="btn btn-small btn-ghost" href="tel:<?=e($r["phone"])?>">Call</a></div>
<?php endwhile; else: ?><div class="empty-state">♡<p>No trusted contacts yet.</p><a class="btn btn-small" href="contacts.php">Add a contact</a></div><?php endif; ?></div></section></main></body></html>