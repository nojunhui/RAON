<?php
session_start();
if (!isset($_SESSION['student_id'])) exit('로그인 필요');

$conn = new mysqli("localhost", "root", "1234", "test");
$student_id = $_SESSION['student_id'];
$book_id = intval($_POST['book_id'] ?? 0);

// (1) 이미 판매된 책인지 체크
$res = $conn->query("SELECT * FROM Books WHERE book_id=$book_id AND status='판매중'");
if (!$res->fetch_assoc()) {
    echo "<script>alert('이미 판매된 책입니다!');history.back();</script>"; exit;
}

// (2) 거래 기록 저장
$conn->query("INSERT INTO Purchases (book_id, buyer_id, purchased_at) VALUES ($book_id, '$student_id', NOW())");

// (3) 책 상태, 구매자 정보 갱신
$conn->query("UPDATE Books SET status='판매완료', buyer_id='$student_id' WHERE book_id=$book_id");

// (4) 사용자 거래 카운트 갱신 (선택)
$conn->query("UPDATE Users SET buy_count=buy_count+1 WHERE student_id='$student_id'");
$res = $conn->query("SELECT seller_id FROM Books WHERE book_id=$book_id");
$seller_id = $res->fetch_assoc()['seller_id'] ?? null;
if ($seller_id) {
    $conn->query("UPDATE Users SET sell_count=sell_count+1 WHERE student_id='$seller_id'");
}

echo "<script>alert('구매가 완료되었습니다!');location.href='my_records.php';</script>";
?>