<?php
function getTranslation($key) {
    global $lang;
    return $lang[$key] ?? $key;
}

function formatPrice($amount) {
    $config = include '../config/settings.php';
    return $config['currency'] . number_format($amount, 2);
}

function isAdmin() {
    return $_SESSION['role'] === 'admin';
}
?>
