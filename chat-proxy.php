<?php
// chat-proxy.php
header('Content-Type: application/json');

// Get your key from https://aistudio.google.com/
$api_key = "AIzaSyClZmp8p1kzI0fDr8gi8GUhRNuVtHkTC7s"; 
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $api_key;

$system_prompt = "You are the AI Concierge for Casa De Manila. 
STRICT RULE: You only answer questions about the restaurant's menu, hours, and reservations. 
If someone asks an off-topic question, say: 'I can only assist with Casa De Manila inquiries.'";

$input = json_decode(file_get_contents('php://input'), true);
$history = $input['history'] ?? [];

$contents = [["role" => "user", "parts" => [["text" => "SYSTEM: " . $system_prompt]]]];
foreach ($history as $msg) {
    $contents[] = ["role" => ($msg['role'] === 'model' ? 'model' : 'user'), "parts" => [["text" => $msg['content']]]];
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["contents" => $contents]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 429) {
    echo json_encode(["error" => "limit_reached"]);
} else {
    echo $response;
}
?>