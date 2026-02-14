<?php
function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize_int($val) {
    return intval($val);
}

function sanitize_float($val) {
    return floatval($val);
}
