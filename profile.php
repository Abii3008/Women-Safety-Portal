<?php
require_once "db.php"; require_login(); $uid=(int)$_SESSION["user_id"]; $error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  if(isset($_POST["save_profile"])){
    $name=trim($_POST["name"]??""); $phone=trim($_POST["phone"]??""); $msg=substr(trim($_POST["sos_message"]??""),0,300);
    if($name==="" || !preg_match('/^[0-9+\-\s()]{7,20}$/',$phone)) $error="Enter a valid name and phone number.";
    else { $stmt=$conn->prepare("UPDATE users SET name=?,phone=?,sos_message=? WHERE id=?"); $stmt->bind_param("sssi",$name,$phone,$msg,$uid); $stmt->execute(); $_SESSION["name"]=$name; flash("Profile updated."); header("Location: profile.php"); exit; }
  } elseif(isset($_POST["change_password"])){
    $cur=$_POST["current"]??""; $new=$_POST["new"]??"";
    $stmt=$conn->prepare("SELECT password FROM users WHERE id=?"); $stmt->bind_param("i",$uid); $stmt->execute(); $hash=$stmt->get_result()->fetch_assoc()["password"];
    if(!password_verify($cur,$hash)) $error="Current password is incorrect.";
    elseif(strlen($new)<8) $error="New password must contain at least 8 characters.";
    else { $h=password_hash($new,PASSWORD_DEFAULT); $stmt=$conn->prepare("UPDATE users SET password=? WHERE id=?"); $stmt->bind_param("si",$h,$uid); $stmt->execute(); flash("Password changed."); header("Location: profile.php"); exit; }
  } elseif(isset($_POST["delete_account"])){
    $stmt=$conn->prepare("SELECT password FROM users WHERE id=?"); $stmt->bind_param("i",$uid); $stmt->execute(); $hash=$stmt->get_result()->fetch_assoc()["password"];
    if(!password_verify($_POST["confirm_password"]??"",$hash)) $error="Password is incorrect. Account was not deleted.";
    else { $stmt=$conn->prepare("DELETE FROM users WHERE id=?"); $stmt->bind_param("i",$uid); $stmt->execute(); header("Location: logout.php"); exit; }
  }
}
$stmt=$conn->prepare("SELECT name,phone,email,sos_message,created_at FROM users WHERE id=?"); $stmt->bind_param("i",$uid); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
$stmt=$conn->prepare("SELECT COUNT(*) n FROM emergency_contacts WHERE user_id=?"); $stmt->bind_param("i",$uid); $stmt->execute(); $nc=(int)$stmt->get_result()->fetch_assoc()["n"];
$stmt=$conn->prepare("SELECT COUNT(*) n FROM emergency_contacts WHERE user_id=? AND is_primary=1"); $stmt->bind_param("i",$uid); $stmt->execute(); $np=(int)$stmt->get_result()->fetch_assoc()["n"];
$checks=["Add at least one trusted contact"=>$nc>0,"Add 3 or more contacts"=>$nc>=3,"Choose a primary contact"=>$np>0,"Write a custom SOS message"=>trim((string)$u["sos_message"])!==""];
$score=(int)round(100*count(array_filter($checks))/count($checks));
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My profile | SafeHer</title><link rel="stylesheet" href="style.css"></head><body class="app-page">
<aside class="sidebar"><a class="brand" href="dashboard.php"><span class="brand-icon">✦</span> SafeHer</a><div class="side-label">WORKSPACE</div><a class="side-link" href="dashboard.php">▦ <span>Dashboard</span></a><a class="side-link" href="contacts.php">♡ <span>Emergency contacts</span></a><a class="side-link" href="alert_history.php">◷ <span>Alert history</span></a><a class="side-link" href="tools.php">☎ <span>Safety tools</span></a><a class="side-link active" href="profile.php">☺ <span>My profile</span></a><?php if(($_SESSION["role"]??"")==="admin") echo '<a class="side-link" href="admin.php">♙ <span>Admin panel</span></a>'; ?><div class="side-bottom"><div class="avatar"><?=e(strtoupper(substr($_SESSION["name"],0,1)))?></div><div><b><?=e($_SESSION["name"])?></b><small>Personal account</small></div><a class="logout" href="logout.php">↗</a></div></aside>
<main class="app-main"><header class="topbar"><div><span class="eyebrow">YOUR ACCOUNT</span><h1>My profile <span>☺</span></h1><p class="muted">Keep your details and SOS message up to date.</p></div></header><?php show_flash(); ?><?php if($error) echo '<div class="flash error">'.e($error).'</div>'; ?>
<div class="content-grid">
<section class="panel"><div class="panel-head"><div><h3>Safety readiness: <?=$score?>%</h3><p class="muted">Complete these to be better prepared.</p></div></div><div class="pwd-meter" style="height:10px"><i style="width:<?=$score?>%;background:linear-gradient(90deg,#c83f91,#7955cb)"></i></div>
<?php foreach($checks as $label=>$ok): ?><div class="recent-row"><span class="recent-dot"><?=$ok?"✓":"○"?></span><div><b><?=e($label)?></b></div><span class="pill <?=$ok?"resolved":"pending"?>"><?=$ok?"Done":"To do"?></span></div><?php endforeach; ?></section>
<section class="panel"><div class="panel-head"><div><h3>Personal details</h3><p class="muted"><?=e($u["email"])?> · joined <?=e(substr($u["created_at"],0,10))?></p></div></div>
<form method="post" class="stack-form"><label>Full name<input name="name" maxlength="100" required value="<?=e($u["name"])?>"></label><label>Phone number<input name="phone" maxlength="20" required value="<?=e($u["phone"])?>"></label>
<label>Your SOS message <small>(shared with contacts after an SOS)</small><textarea name="sos_message" maxlength="300" rows="3" placeholder="I need help. Please call me."><?=e($u["sos_message"])?></textarea></label><button class="btn full" name="save_profile" value="1">Save profile</button></form></section>
<section class="panel"><div class="panel-head"><div><h3>Change password</h3></div></div><form method="post" class="stack-form"><label>Current password<input type="password" name="current" required></label><label>New password<input type="password" name="new" minlength="8" required></label><button class="btn full" name="change_password" value="1">Update password</button></form></section>
<section class="panel"><div class="panel-head"><div><h3>Delete account</h3><p class="muted">Permanently removes your profile, contacts and alerts.</p></div></div><form method="post" class="stack-form" onsubmit="return confirm('Delete your account permanently?')"><label>Confirm with your password<input type="password" name="confirm_password" required></label><button class="btn btn-danger full" name="delete_account" value="1">Delete my account</button></form></section></div></main></body></html>