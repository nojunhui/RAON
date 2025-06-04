<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
    exit;
}
$student_id = $_SESSION['student_id'];
$book_id = intval($_GET['id'] ?? 0);

if (!$book_id) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$conn = new mysqli("localhost", "root", "1234", "test");

// 본인 글인지 체크
$res = $conn->query("SELECT seller_id FROM Books WHERE book_id = $book_id");
if (!$res || $res->num_rows == 0) {
    echo "<script>alert('존재하지 않는 책입니다.'); history.back();</script>";
    exit;
}
$row = $res->fetch_assoc();
if ($row['seller_id'] != $student_id) {
    echo "<script>alert('본인 글만 삭제할 수 있습니다.'); history.back();</script>";
    exit;
}

// ========== 연관 데이터 먼저 삭제 ==========
$conn->query("DELETE FROM BooksImages WHERE book_id = $book_id");
$conn->query("DELETE FROM Interests WHERE book_id = $book_id");
$conn->query("DELETE FROM Purchases WHERE book_id = $book_id");
$conn->query("DELETE FROM ChatMessages WHERE chatroom_id IN (SELECT chatroom_id FROM ChatRooms WHERE book_id = $book_id)");
$conn->query("DELETE FROM ChatRooms WHERE book_id = $book_id");
$conn->query("DELETE FROM Reviews WHERE book_id = $book_id");

// ========== 책 데이터 마지막에 삭제 ==========
$conn->query("DELETE FROM Books WHERE book_id = $book_id");

$conn->close();

echo "<script>
    alert('삭제가 완료되었습니다.');
    window.location.href = 'index.php';
</script>";
exit;
?>
