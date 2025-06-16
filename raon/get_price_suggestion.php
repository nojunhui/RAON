<?php
header("Content-Type: application/json; charset=UTF-8");
$conn = new mysqli("localhost", "root", "1234", "test");
$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
if(!$title || !$author){
    echo json_encode(['error'=>'noinput']); exit;
}
// 같은 제목, 저자 기준 평균가 구하기
$stmt = $conn->prepare("SELECT AVG(selling_price) as avg_price FROM books WHERE title=? AND author=?");
$stmt->bind_param("ss", $title, $author);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if($row && $row['avg_price']){
    // 정수 단위로 소수점 버림
    $avg = floor($row['avg_price']);
    echo json_encode(['avg_price'=>$avg]);
} else {
    echo json_encode(['avg_price'=>null]);
}
$stmt->close(); $conn->close();
?>
