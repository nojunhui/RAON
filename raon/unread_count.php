<?php
session_start();
if (!isset($_SESSION['student_id'])) exit('0');
$student_id = $_SESSION['student_id'];
$conn = new mysqli("localhost", "root", "1234", "test");

// 내가 속한 채팅방 찾고, 그 방에서 '상대방'이 보낸 안 읽은 메시지
$sql = "
  SELECT COUNT(*) FROM ChatMessages
  WHERE chatroom_id IN (
    SELECT chatroom_id FROM ChatRooms
    WHERE buyer_id = '$student_id' OR seller_id = '$student_id'
  )
  AND sender_id != '$student_id'
  AND is_read = 0
";
$res = $conn->query($sql);
$row = $res->fetch_row();
echo $row[0];
?>