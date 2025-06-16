<?php
header('Content-Type: application/json');

$title = $_POST['title'] ?? '';

if (!$title) {
    echo json_encode(['error' => '제목이 비어있습니다']);
    exit;
}

// 알라딘 API 설정
$ttbKey = 'ttbwnsgml5231444001'; // 예: ttbwnsgml5231444001
$query = urlencode($title); // ← author 제외하고 title만!
$url = "http://www.aladin.co.kr/ttb/api/ItemSearch.aspx?ttbkey={$ttbKey}&Query={$query}&QueryType=Keyword&MaxResults=1&start=1&SearchTarget=Book&output=js&Version=20131101";

// API 호출
$response = file_get_contents($url);
if (!$response) {
    echo json_encode(['error' => '알라딘 API 호출 실패']);
    exit;
}

$data = json_decode($response, true);
if (!isset($data['item'][0])) {
    echo json_encode(['error' => '도서 정보 없음']);
    exit;
}

$item = $data['item'][0];

echo json_encode([
    'title' => $item['title'] ?? '',
    'author' => $item['author'] ?? '',
    'publisher' => $item['publisher'] ?? '',
    'publish_date' => $item['pubDate'] ?? '',
    'original_price' => $item['priceStandard'] ?? ''
]);
