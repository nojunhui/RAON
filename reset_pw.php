<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패");

$student_id = $_POST['student_id'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if(empty($student_id) || empty($new_password)) {
    die("<script>alert('잘못된 접근입니다.'); history.back();</script>");
}

// 비밀번호 해시화
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE Users SET password = ? WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $student_id);

if($stmt->execute()) {
    echo "<script>
        alert('비밀번호가 성공적으로 변경되었습니다.');
        window.location.href = 'login.html';
    </script>";
} else {
    echo "비밀번호 변경 실패: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
