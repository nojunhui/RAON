<?php
session_start();
if (!isset($_SESSION['student_id'])) exit(json_encode(['success'=>false]));
$conn = new mysqli("localhost", "root", "1234", "test");
$student_id = $_SESSION['student_id'];
$book_id = intval($_POST['book_id'] ?? 0);
$action = $_POST['action'] ?? '';
if (!$book_id || !in_array($action, ['add','remove'])) exit(json_encode(['success'=>false]));

if ($action=='add') {
  $conn->query("INSERT IGNORE INTO Interests (student_id, book_id) VALUES ('$student_id', $book_id)");
  $conn->query("UPDATE Books SET interest_count = (SELECT COUNT(*) FROM Interests WHERE book_id=$book_id) WHERE book_id=$book_id");
} else {
  $conn->query("DELETE FROM Interests WHERE student_id='$student_id' AND book_id=$book_id");
  $conn->query("UPDATE Books SET interest_count = (SELECT COUNT(*) FROM Interests WHERE book_id=$book_id) WHERE book_id=$book_id");
}
$res = $conn->query("SELECT interest_count FROM Books WHERE book_id=$book_id");
$count = $res ? ($res->fetch_row()[0] ?? 0) : 0;
echo json_encode(['success'=>true, 'count'=>$count]);
?>
