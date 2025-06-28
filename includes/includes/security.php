<?php
// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCSRFToken($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Input Sanitization
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Password Policy Enforcement
function validatePassword($password) {
    return preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password);
}
?>
