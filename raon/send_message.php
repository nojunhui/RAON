<?php
session_start();
if(!isset($_SESSION['student_id'])){echo json_encode(['success'=>false,'error'=>'로그인 필요']);exit;}
$student_id = $_SESSION['student_id'];
$chatroom_id = intval($_POST['chatroom_id']);
$message = trim($_POST['message'] ?? '');
$image_path = null;

// 이미지 업로드
if(isset($_FILES['image']) && $_FILES['image']['size']>0){
    $updir = "uploads/chats";
    if(!is_dir($updir)) mkdir($updir,0777,true);
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fname = uniqid().".".$ext;
    $target = "$updir/$fname";
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    $image_path = $target;
}

// DB 입력
$conn = new mysqli("localhost","root","1234","test");
$stmt = $conn->prepare("INSERT INTO ChatMessages(chatroom_id,sender_id,message,image_path) VALUES (?,?,?,?)");
$stmt->bind_param("isss", $chatroom_id, $student_id, $message, $image_path);
$stmt->execute();
echo json_encode(['success'=>true]);
