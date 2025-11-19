<?php
// Serve small postal-data chunks by prefix to reduce client payload.
// Usage: assets/data/postal.php?prefix=54
header('Content-Type: application/json; charset=utf-8');

$prefix = isset($_GET['prefix']) ? preg_replace('/\D/', '', $_GET['prefix']) : '';
if ($prefix === '') {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

$prefix = substr($prefix, 0, 3); // limit length

$dir = __DIR__;
$chunksDir = $dir . '/chunks';
if (!is_dir($chunksDir)) {
    @mkdir($chunksDir, 0755, true);
}

$chunkFile = $chunksDir . '/postal_' . $prefix . '.json';
if (file_exists($chunkFile)) {
    // serve cached chunk
    readfile($chunkFile);
    exit;
}

$source = $dir . '/th_postal_codes.json';
if (!file_exists($source)) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$raw = @file_get_contents($source);
$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode([]);
    exit;
}

$out = [];
foreach ($data as $item) {
    if (!isset($item['zipcode'])) continue;
    $zip = str_pad((string)$item['zipcode'], 5, '0', STR_PAD_LEFT);
    if (strpos($zip, $prefix) === 0) {
        $out[] = $item;
    }
}

// cache chunk for future requests
@file_put_contents($chunkFile, json_encode($out));

echo json_encode($out);

?>