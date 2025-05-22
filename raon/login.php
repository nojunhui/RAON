<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패");

$student_id = $_POST['student_id'];
$password = $_POST['password'];

$sql = "SELECT * FROM Users WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
  $_SESSION['student_id'] = $user['student_id'];
  $_SESSION['name'] = $user['name'];
  header("Location: index.php");
  exit;
} else {
  echo "로그인 실패. <a href='login.html'>다시 시도</a>";
}
$stmt->close(); $conn->close();
?>
