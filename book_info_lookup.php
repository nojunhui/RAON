<?php
header('Content-Type: application/json');

$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
if (!$title || !$author) {
    echo json_encode(['error' => '책 제목과 저자를 입력하세요.']);
    exit;
}

// GPT 프롬프트 구성
$prompt = "책 제목이 '{$title}', 저자가 '{$author}'인 책의 출판사, 출판일(연-월-일), 정가(원)를 알려줘. 각각 따로 JSON 형식으로 출력해줘. 
예) {\"publisher\":\"\", \"publish_date\":\"\", \"original_price\":\"\"}";

// OpenAI API 키 (본인 키로 교체!)
$apiKey = "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";

// GPT API 요청
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0.3
]));

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$content = $data['choices'][0]['message']['content'] ?? '';

// GPT 응답에서 JSON 부분만 추출
if (preg_match('/\{.*\}/s', $content, $match)) {
    $jsonPart = json_decode($match[0], true);
    echo json_encode($jsonPart);
} else {
    echo json_encode(['error' => '정보를 찾을 수 없습니다.', 'raw' => $content]);
}
