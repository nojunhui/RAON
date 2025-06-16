<?php
session_start();
$student_id = $_SESSION['student_id'];
$conn = new mysqli("localhost", "root", "1234", "test");

$result = [];
// 내가 참가자인 모든 채팅방
$res = $conn->query("SELECT * FROM ChatRooms WHERE seller_id='$student_id' OR buyer_id='$student_id'");
while($row = $res->fetch_assoc()) {
  $chatroom_id = $row['chatroom_id'];
  if ($row['seller_id'] == $student_id) {
    // 판매자라면
    $last_read = (int)$row['last_read_message_id_seller'];
    $q = $conn->query("SELECT COUNT(*) FROM ChatMessages WHERE chatroom_id=$chatroom_id AND message_id > $last_read AND sender_id != '$student_id'");
  } else if ($row['buyer_id'] == $student_id) {
    // 구매자라면
    $last_read = (int)$row['last_read_message_id_buyer'];
    $q = $conn->query("SELECT COUNT(*) FROM ChatMessages WHERE chatroom_id=$chatroom_id AND message_id > $last_read AND sender_id != '$student_id'");
  } else {
    continue;
  }
  $cnt = $q ? (int)$q->fetch_row()[0] : 0;
  $result[$chatroom_id] = $cnt;
}
header("Content-Type: application/json");
echo json_encode($result);
?>
