<?php
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패");

$student_id = $_POST['student_id'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$name = $_POST['name'];
$phone = $_POST['phone'];

$sql = "INSERT INTO Users (student_id, password, name, phone) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $student_id, $password, $name, $phone);
if ($stmt->execute()) {
  echo "회원가입 성공! <a href='login.html'>로그인하기</a>";
} else {
  echo "회원가입 실패: ".$conn->error;
}
$stmt->close(); $conn->close();
?>
