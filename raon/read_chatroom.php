<?php
session_start();
$student_id = $_SESSION['student_id'];
$chatroom_id = intval($_GET['chatroom_id'] ?? 0);

// 마지막 메시지 id를 찾는다
$res = $conn->query("SELECT MAX(message_id) AS max_id FROM ChatMessages WHERE chatroom_id=$chatroom_id");
$max_id = (int)($res->fetch_assoc()['max_id'] ?? 0);

// 내 역할 판별
$res2 = $conn->query("SELECT seller_id, buyer_id FROM ChatRooms WHERE chatroom_id=$chatroom_id");
$row = $res2->fetch_assoc();
if ($row['seller_id'] == $student_id) {
  $conn->query("UPDATE ChatRooms SET last_read_message_id_seller = $max_id WHERE chatroom_id=$chatroom_id");
} else if ($row['buyer_id'] == $student_id) {
  $conn->query("UPDATE ChatRooms SET last_read_message_id_buyer = $max_id WHERE chatroom_id=$chatroom_id");
}
echo "ok";

?>
