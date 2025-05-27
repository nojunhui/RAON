<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패");

$student_id = $_POST['student_id'] ?? '';
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';

if(empty($student_id) || empty($name) || empty($phone)) {
    die("<script>alert('모든 필드를 채워주세요.'); history.back();</script>");
}

// 학번, 이름, 전화번호 일치 여부 확인
$sql = "SELECT COUNT(*) FROM Users WHERE student_id = ? AND name = ? AND phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $student_id, $name, $phone);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if($count == 1){
    // 정보 일치 -> 새 비밀번호 페이지로 이동, student_id GET 파라미터 전달
    header("Location: reset_pw.html?student_id=".urlencode($student_id));
    exit;
} else {
    echo "<script>alert('정보가 일치하지 않습니다.'); history.back();</script>";
}
?>
