<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

$host = "localhost";
$user = "root";
$password = "";
$database = "women_safety_db";

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed. Check XAMPP MySQL and import database.sql.");
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function require_login() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function flash($message, $type = "success") {
    $_SESSION["flash"] = ["message" => $message, "type" => $type];
}

function show_flash() {
    if (!empty($_SESSION["flash"])) {
        $f = $_SESSION["flash"];
        echo '<div class="flash ' . e($f["type"]) . '">' . e($f["message"]) . '</div>';
        unset($_SESSION["flash"]);
    }
}

/* ---- CSRF protection: every POST must carry the session token; the token is
   injected automatically into every <form method="post"> on the page. ---- */
if (empty($_SESSION["csrf"])) { $_SESSION["csrf"] = bin2hex(random_bytes(32)); }
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sent = $_POST["csrf"] ?? "";
    if (!is_string($sent) || !hash_equals($_SESSION["csrf"], $sent)) {
        http_response_code(403);
        exit("Security check failed (invalid form token). Go back, refresh the page and try again.");
    }
}
ob_start(function ($html) {
    $field = '<input type="hidden" name="csrf" value="' . htmlspecialchars($_SESSION["csrf"] ?? "", ENT_QUOTES, "UTF-8") . '">';
    return preg_replace('/(<form\b[^>]*\bmethod=["\']?post["\']?[^>]*>)/i', '$1' . $field, $html);
});

function too_many_attempts($conn, $email) {
    $ip = $_SERVER["REMOTE_ADDR"] ?? "";
    $stmt = $conn->prepare("SELECT COUNT(*) n FROM login_attempts WHERE (email=? OR ip=?) AND attempted_at > (NOW() - INTERVAL 15 MINUTE)");
    $stmt->bind_param("ss", $email, $ip); $stmt->execute();
    return (int)$stmt->get_result()->fetch_assoc()["n"] >= 5;
}
function record_attempt($conn, $email) {
    $ip = $_SERVER["REMOTE_ADDR"] ?? "";
    $stmt = $conn->prepare("INSERT INTO login_attempts(email,ip) VALUES(?,?)");
    $stmt->bind_param("ss", $email, $ip); $stmt->execute();
}
?>