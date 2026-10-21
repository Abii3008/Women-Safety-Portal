<?php
require_once "db.php";
if (!empty($_SESSION["user_id"])) { header("Location: dashboard.php"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $pass = $_POST["password"] ?? "";
    if ($name === "" || !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 8) {
        $error = "Enter valid details. Password must contain at least 8 characters.";
    } else {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users(name,phone,email,password) VALUES(?,?,?,?)");
            $stmt->bind_param("ssss", $name, $phone, $email, $hash);
            $stmt->execute();
            flash("Account created. Please log in.");
            header("Location: login.php"); exit;
        } catch (mysqli_sql_exception $ex) {
            $error = $ex->getCode() === 1062 ? "This email is already registered." : "Could not create account. Please try again.";
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create Account | SafeHer</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-page"><a class="brand auth-brand" href="index.php"><span class="brand-icon">✦</span> SafeHer</a>
<div class="auth-wrap"><div class="auth-intro"><span class="eyebrow">YOUR SAFETY SPACE</span><h1>Start with a<br><span>trusted circle.</span></h1><p>Create your private account to manage contacts and safety alert records.</p><div class="auth-badge">♡ Your safety matters</div><img class="auth-woman" src="assets/woman-banner.svg" alt="Smiling woman illustration"></div>
<form class="form-card" method="post"><a class="back-link" href="index.php">← Home</a><h2>Create account</h2><p class="muted">A few details to get started.</p>
<?php if ($error) echo '<div class="flash error">'.e($error).'</div>'; ?>
<label>Full name<input name="name" maxlength="100" required value="<?=e($_POST['name'] ?? '')?>" placeholder="Your name"></label>
<label>Phone number<input name="phone" maxlength="20" required value="<?=e($_POST['phone'] ?? '')?>" placeholder="+91 98765 43210"></label>
<label>Email address<input type="email" name="email" required value="<?=e($_POST['email'] ?? '')?>" placeholder="you@example.com"></label>
<label>Password <small>(minimum 8 characters)</small><input type="password" name="password" minlength="8" required placeholder="Create a strong password" id="pwd"><div class="pwd-meter"><i id="pwd-bar"></i></div></label>
<button class="btn full" type="submit">Create account →</button><p class="form-bottom">Already registered? <a href="login.php">Login</a></p></form></div><script>
var p=document.getElementById("pwd"),b=document.getElementById("pwd-bar");
if(p){p.addEventListener("input",function(){var v=p.value,n=0;if(v.length>=8)n++;if(/[A-Z]/.test(v)&&/[a-z]/.test(v))n++;if(/\d/.test(v))n++;if(/[^A-Za-z0-9]/.test(v))n++;
b.style.width=(n*25)+"%";b.style.background=["#e34d7f","#e34d7f","#e8a33d","#7ac27a","#2b8753"][n];});}
</script></body></html>