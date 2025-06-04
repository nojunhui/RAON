<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
    exit;
}

$student_id = $_SESSION['student_id'];

// 값 받아오기
$current_pw = $_POST['current_password'];
$new_pw = $_POST['new_password'];
$confirm_pw = $_POST['confirm_password'];
$phone1 = $_POST['phone1'];
$phone2 = $_POST['phone2'];
$phone3 = $_POST['phone3'];

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패: " . $conn->connect_error);

// 1. 현재 비밀번호 체크 (비교는 password_verify로!)
$stmt = $conn->prepare("SELECT password FROM Users WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $db_pw = $row['password'];
    if (!password_verify($current_pw, $db_pw)) {
        echo "<script>alert('현재 비밀번호가 올바르지 않습니다.'); history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('회원 정보를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}

// 2. 새 비밀번호 처리 (해시화!)
if (!empty($new_pw)) {
    $set_pw = password_hash($new_pw, PASSWORD_DEFAULT);
} else {
    $set_pw = $db_pw; // 변경 안 하면 기존 해시값 유지
}

// 3. 전화번호 조립 (010-1234-5678 형태)
$phone_full = $phone1 . '-' . $phone2 . '-' . $phone3;

// 4. DB 업데이트
$update_sql = "UPDATE Users SET password = ?, phone = ? WHERE student_id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("sss", $set_pw, $phone_full, $student_id);

if ($update_stmt->execute()) {
    echo "<script>alert('수정이 완료되었습니다.'); location.href='mypage.php';</script>";
    exit;
} else {
    echo "<script>alert('수정에 실패했습니다. 다시 시도해 주세요.'); history.back();</script>";
    exit;
}
?>
