<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
    exit;
}

$student_id = $_SESSION['student_id'];
$password_input = $_POST['password'];

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 현재 비밀번호 조회
$sql = "SELECT password FROM Users WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    // 평문 비밀번호 비교
    if ($row['password'] === $password_input) {
        // 탈퇴 처리
        $delete_sql = "DELETE FROM Users WHERE student_id = ?";
        $del_stmt = $conn->prepare($delete_sql);
        $del_stmt->bind_param("s", $student_id);
        $del_stmt->execute();

        // 세션 종료
        session_destroy();

        // 리디렉션
        echo "<script>
            alert('회원 탈퇴가 완료되었습니다.');
            if (window.opener) {
                window.opener.location.href = 'index.php';
                window.close();
            } else {
                location.href = 'index.php';
            }
        </script>";
        exit;
    } else {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('회원 정보를 찾을 수 없습니다.'); history.back();</script>";
    exit;
}
?>