<?php
session_start();
$chatroom_id = intval($_GET['chatroom_id']??0);
$conn = new mysqli("localhost","root","1234","test");
$sql = "SELECT * FROM ChatMessages WHERE chatroom_id=$chatroom_id ORDER BY sent_at ASC";
$res = $conn->query($sql);
$messages = [];
while($row = $res->fetch_assoc()){
    $messages[] = [
        "sender_id"=>$row['sender_id'],
        "message"=>$row['message'],
        "image_path"=>!empty($row['image_path']) ? $row['image_path'] : null,
        "time"=>date('H:i',strtotime($row['sent_at']))
    ];
}
header('Content-Type: application/json');
echo json_encode($messages);
