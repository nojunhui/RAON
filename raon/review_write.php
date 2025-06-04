<?php
session_start();
if(!isset($_SESSION['student_id'])) exit;
$buyer_id = $_SESSION['student_id'];
$book_id = intval($_POST['book_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if(mb_strlen($content)>100) $content = mb_substr($content,0,100);
$conn = new mysqli("localhost","root","1234","test");

// 중복방지
$check = $conn->query("SELECT * FROM Reviews WHERE book_id=$book_id AND buyer_id='$buyer_id'");
if($check->fetch_assoc()) { echo json_encode(['success'=>false,'msg'=>'이미 작성']); exit; }

$book = $conn->query("SELECT * FROM Books WHERE book_id=$book_id AND buyer_id='$buyer_id' AND status='판매완료'")->fetch_assoc();
if(!$book){ echo json_encode(['success'=>false,'msg'=>'권한 없음']); exit; }

$stmt = $conn->prepare("INSERT INTO Reviews (book_id, seller_id, buyer_id, content) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $book_id, $book['seller_id'], $buyer_id, $content);
$stmt->execute();
echo json_encode(['success'=>true]);
?>
