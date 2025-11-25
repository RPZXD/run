<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

if (!isset($_GET['file'])) {
    header('HTTP/1.0 400 Bad Request');
    exit;
}

$file = basename($_GET['file']);
$path = '../uploads/' . $file;

if (file_exists($path)) {
    $mime = mime_content_type($path);
    header('Content-Type: ' . $mime);
    readfile($path);
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
