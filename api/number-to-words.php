<?php
/**
 * API: Convert number to Vietnamese words
 * File: /api/number-to-words.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/functions.php';

header('Content-Type: application/json; charset=utf-8');

$amount = isset($_GET['amount']) ? (int)$_GET['amount'] : 0;

$words = numberToVietnameseWords($amount);

echo json_encode([
    'success' => true,
    'amount' => $amount,
    'words' => $words
], JSON_UNESCAPED_UNICODE);