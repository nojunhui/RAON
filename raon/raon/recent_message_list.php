<?php
session_start();
$student_id = $_SESSION['student_id'] ?? '';
if (!$student_id) exit('{}');

$conn = new mysqli("localhost", "root", "1234", "test");

// mode는 buy/sell 구분할 필요 없이 모두 찾으면 됨
$sql = "
SELECT CR.chatroom_id,
    (SELECT message FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) AS last_msg,
    (SELECT sent_at FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) AS last_time
FROM ChatRooms CR
WHERE CR.buyer_id='$student_id' OR CR.seller_id='$student_id'
";
$res = $conn->query($sql);
$out = [];
while($row = $res->fetch_assoc()) {
  $out[$row['chatroom_id']] = [
    'last_msg' => $row['last_msg'],
    'last_time' => $row['last_time'] ? date('Y.m.d H:i', strtotime($row['last_time'])) : '',
  ];
}
header('Content-Type: application/json');
echo json_encode($out);
?>
