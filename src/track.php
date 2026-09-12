<?php

// --- 1. Read or create visitor_id cookie ---

$visitorId = $_COOKIE['ttrk_visitor_id'] ?? null;

if ($visitorId === null) {
    $visitorId = bin2hex(random_bytes(16)); // 32-char random hex string
    // Cookie valid for 1 year, available side-wide, sent on cross-site requests
    // (SameSite=None is required since the tracker is embedded on third-party sites)
    setcookie('ttrk_visitor_id', $visitorId, [
        'expires' => time() + (365 * 24 * 60 * 60),
        'path' => '/',
        'samesite' => 'None',
        'secure' => true, // required by browsers when SameSite=None
    ]);
}

// --- 2. Read request data ---

$rawBody = file_get_contents('php://input');
$data = json_decode($rawBody, true);

// takes from tracker.js
$pageUrl = $data['page_url'] ?? null;
$referrer = $data['referrer'] ?? null;
// takes from $_SERVER
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// page_url is the only field we truly can't proceed without
if ($pageUrl === null) {
    http_response_code(400);
    exit;
}

// --- 3. Save to database ---

$pdo = new PDO(
    'mysql:host=db;dbname=traffic_tracker;charset=utf8mb4',
    'tracker_user',
    'trackerpass'
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare(
    'INSERT INTO page_views (page_url, referrer, visitor_id, user_agent)
     VALUES (:page_url, :referrer, :visitor_id, :user_agent)'
);

$stmt->execute([
    'page_url' => $pageUrl,
    'referrer' => $referrer,
    'visitor_id' => $visitorId,
    'user_agent' => $userAgent,
]);

http_response_code(204); // success, no content to return