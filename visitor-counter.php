<?php
// visitor-counter.php — increments and returns the count stored in visitor-count.txt
// Upload next to index.html. The folder/file must be writable by PHP (chmod 664 or 666 on visitor-count.txt).
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$file = __DIR__ . '/visitor-count.txt';

// Only count real page visits: GET with ?hit=1, and not search-engine / link-preview bots.
$hit = ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && ($_GET['hit'] ?? '') === '1';
$ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/bot|crawl|spider|slurp|preview|headless|facebookexternalhit/i', $ua)) {
    $hit = false;
}

$fp = @fopen($file, 'c+');   // creates the file if it does not exist
if (!$fp) {
    http_response_code(500);
    echo json_encode(['error' => 'Cannot open visitor-count.txt — check file permissions']);
    exit;
}

flock($fp, LOCK_EX);         // safe when several visitors arrive at once
$count = (int) trim((string) stream_get_contents($fp));
if ($hit) {
    $count++;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $count);
    fflush($fp);
}
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['count' => $count]);
