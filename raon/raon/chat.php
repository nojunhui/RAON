<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html"); exit;
}
$my_id = $_SESSION['student_id'];

$book_id = intval($_GET['book_id'] ?? 0);
$seller_id = $_GET['seller_id'] ?? '';

if (!$book_id || !$seller_id || $my_id == $seller_id) {
    echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
    exit;
}


$conn = new mysqli("localhost", "root", "1234", "test");

// 1. 이미 채팅방이 있으면 가져오기
$sql = "SELECT chatroom_id FROM ChatRooms WHERE book_id = $book_id AND buyer_id = '$my_id'";
$res = $conn->query($sql);
if ($row = $res->fetch_assoc()) {
    $chatroom_id = $row['chatroom_id'];
} else {
    // 2. 없으면 생성
    $stmt = $conn->prepare("INSERT INTO ChatRooms (book_id, seller_id, buyer_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $book_id, $seller_id, $my_id);
    $stmt->execute();
    $chatroom_id = $conn->insert_id;
}
// 3. 바로 채팅룸 페이지로 이동
header("Location: chat_room.php?chatroom_id=$chatroom_id");
exit;
?>
