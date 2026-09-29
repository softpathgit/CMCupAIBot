<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

// Read input from frontend
$input = json_decode(file_get_contents('php://input'), true);
$query = $input['query'] ?? '';

if (!$query) {
    echo json_encode(['error' => 'Query is required']);
    exit;
}

$session_id = $input['session_id'] ?? null;

$data = ['query' => $query];
if ($session_id) {
    $data['session_id'] = $session_id;
}

$payload = json_encode($data);

$ch = curl_init('http://127.0.0.1:8000/chat');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'accept: application/json',
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}

curl_close($ch);

// Ensure ONLY JSON is returned
echo trim($response);
