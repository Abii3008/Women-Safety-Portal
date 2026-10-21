<?php
require_once "db.php";
if (!empty($_SESSION["user_id"])) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $pass = $_POST["password"] ?? "";
    if (too_many_attempts($conn, $email)) { $error = "Too many failed attempts. Please wait 15 minutes and try again."; }
    else {
    $stmt = $conn->prepare("SELECT id,name,email,password,role FROM users WHERE email=?");
    $stmt->bind_param("s", $email); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        header("Location: dashboard.php"); exit;
    } else { record_attempt($conn, $email); $error = "Email or password is incorrect."; }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | SafeHer</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-page"><a class="brand auth-brand" href="index.php"><span class="brand-icon">✦</span> SafeHer</a>
<div class="auth-wrap"><div class="auth-intro"><span class="eyebrow">WELCOME BACK</span><h1>Your trusted<br><span>safety space.</span></h1><p>Sign in to check your contacts, create alerts and review your alert history.</p><div class="auth-badge">✦ Private and personal</div><img class="auth-woman" src="assets/woman-auth.svg" alt="Smiling woman illustration"></div>
<form class="form-card" method="post"><a class="back-link" href="index.php">← Home</a><h2>Welcome back</h2><p class="muted">Login to continue.</p><?php show_flash(); ?><?php if ($error) echo '<div class="flash error">'.e($error).'</div>'; ?>
<label>Email address<input type="email" name="email" required value="<?=e($_POST['email'] ?? '')?>" placeholder="you@example.com"></label>
<label>Password<input type="password" name="password" required placeholder="Your password"></label>
<button class="btn full" type="submit">Login securely →</button><p class="form-bottom">New here? <a href="register.php">Create an account</a></p></form></div></body></html>