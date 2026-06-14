<?php
// api/stream.php

// Disable session write locking to prevent concurrent request blocking during streaming
session_write_close();

$file = isset($_GET['file']) ? $_GET['file'] : '';

if (empty($file)) {
    header("HTTP/1.1 400 Bad Request");
    echo "File parameter is required.";
    exit;
}

// Security: Prevent directory traversal. Resolve paths and ensure they are under /uploads
$baseDir = realpath(__DIR__ . '/../uploads');
$filePath = realpath(__DIR__ . '/../' . $file);

if ($baseDir === false || $filePath === false || strpos($filePath, $baseDir) !== 0) {
    header("HTTP/1.1 403 Forbidden");
    echo "Access denied.";
    exit;
}

if (!file_exists($filePath)) {
    header("HTTP/1.1 404 Not Found");
    echo "File not found.";
    exit;
}

// Get file info
$size = filesize($filePath);
$type = mime_content_type($filePath);

header("Accept-Ranges: bytes");

$start = 0;
$end = $size - 1;

if (isset($_SERVER['HTTP_RANGE'])) {
    $c_start = $start;
    $c_end = $end;

    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    if ($range == '-') {
        $c_start = 0;
        $c_end = $size - 1;
    } else {
        $range = explode('-', $range);
        $c_start = $range[0];
        $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size - 1;
    }
    
    $c_end = ($c_end > $end) ? $end : $c_end;
    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    $start = $c_start;
    $end = $c_end;
    $length = $end - $start + 1;
    
    header('HTTP/1.1 206 Partial Content');
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: $length");
} else {
    header("Content-Length: $size");
}

header("Content-Type: $type");

// Stream the file in chunks
$buffer = 1024 * 8; // 8KB chunks
$fp = fopen($filePath, 'rb');
fseek($fp, $start);

while (!feof($fp) && ($pos = ftell($fp)) <= $end) {
    if ($pos + $buffer > $end) {
        $buffer = $end - $pos + 1;
    }
    set_time_limit(0);
    echo fread($fp, $buffer);
    flush();
}
fclose($fp);
exit;
?>
