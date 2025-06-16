<?php
header('Content-Type: application/json');

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => '이미지 업로드 실패']);
    exit;
}

$tmpName = $_FILES['image']['tmp_name'];

require_once __DIR__ . '/vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;


// OCR 엔진 경로 직접 지정
$ocr = new TesseractOCR($tmpName);
$ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
$ocr->lang('kor'); // ← 한글 지원 설정

$text = $ocr->run();

// 기본값
$title = '';
$author = '';

// 간단한 정규표현식 또는 추출 규칙 예시
// OCR 텍스트에서 줄별로 분석
$lines = explode("\n", $text);
foreach ($lines as $line) {
    $line = trim($line);
    if (mb_strlen($line) < 3) continue;

    // 저자 추정 (예: "홍길동 지음", "저자 홍길동")
    if (preg_match('/(지음|저자)/u', $line)) {
        $author = preg_replace('/(지음|저자)/u', '', $line);
    } elseif (!$title) {
        // 첫 번째 긴 줄을 제목으로 간주
        $title = $line;
    }
}

echo json_encode([
    'title' => $title,
    'author' => $author
]);

