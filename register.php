<?php
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패");

// POST 값 받기
$student_id = $_POST['student_id'] ?? '';
$password = $_POST['password'] ?? '';
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';

// 간단한 입력값 체크 (필요에 따라 확장 가능)
if (empty($student_id) || empty($password) || empty($name) || empty($phone)) {
    die("모든 필드를 채워주세요. <a href='register.html'>돌아가기</a>");
}

// 중복 학번 체크
$sql = "SELECT COUNT(*) FROM Users WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    die("<script>alert('이미 회원가입되어 있는 학번입니다.'); window.history.back();</script>");
}

// 비밀번호 해시화
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 회원가입 처리
$sql = "INSERT INTO Users (student_id, password, name, phone) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $student_id, $hashed_password, $name, $phone);

if ($stmt->execute()) {
    echo "<script>
        alert('회원가입을 완료하였습니다.');
        window.location.href = 'login.html';
    </script>";
} else {
    echo "회원가입 실패: " . $conn->error;
}


$stmt->close();
$conn->close();
?>
