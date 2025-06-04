<?php
session_start();
if (!isset($_SESSION['student_id'])) exit;
$seller_id = $_SESSION['student_id'];
$book_id = intval($_POST['book_id'] ?? 0);
$buyer_id = $_POST['buyer_id'] ?? '';

$conn = new mysqli("localhost","root","1234","test");

// 1. 판매자인지 체크 + 판매중 상태
$res = $conn->query("SELECT * FROM Books WHERE book_id=$book_id AND seller_id='$seller_id' AND status='판매중'");
if (!$res->fetch_assoc()) {
    echo "<script>alert('잘못된 요청(판매권한X, 이미 판매됨, 존재X)');history.back();</script>"; exit;
}

// 2. 중복 거래 방지: 이미 구매내역이 있으면 추가로 저장하지 않음
$res = $conn->query("SELECT * FROM Purchases WHERE book_id=$book_id");
if ($res->num_rows > 0) {
    echo "<script>alert('이미 판매완료된 책입니다!');location.href='my_books.php';</script>"; exit;
}

// 3. Books 테이블 상태 변경 (판매완료/구매자 저장)
$conn->query("UPDATE Books SET status='판매완료', buyer_id='$buyer_id' WHERE book_id=$book_id");

// 4. 거래내역 기록 (Purchases에 저장)
$conn->query("INSERT INTO Purchases (book_id, buyer_id, purchased_at) VALUES ($book_id, '$buyer_id', NOW())");

// 5. 판매자/구매자 거래횟수 증가
$conn->query("UPDATE Users SET sell_count = sell_count+1 WHERE student_id='$seller_id'");
$conn->query("UPDATE Users SET buy_count = buy_count+1 WHERE student_id='$buyer_id'");

// 6. 완료 안내
echo "<script>alert('판매완료 처리되었습니다.');location.href='my_books.php';</script>";
?>
