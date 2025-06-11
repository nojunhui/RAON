<?php
session_start();
if (!isset($_SESSION['student_id'])) exit;

$student_id = $_SESSION['student_id'];
$book_id = $_POST['book_id'];

$conn = new mysqli("localhost", "root", "1234", "test");
$sql = "DELETE FROM Interests WHERE student_id='$student_id' AND book_id='$book_id'";
$conn->query($sql);
$conn->close();

echo "success";
?>