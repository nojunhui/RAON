<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.html';</script>";
    exit;
}

$student_id = $_SESSION['student_id'];
$password_input = $_POST['password'];

$conn = new mysqli("localhost", "root", "1234", "test");
if ($conn->connect_error) die("DB 연결 실패: " . $conn->connect_error);

// 현재 비밀번호(해시) 조회
$stmt = $conn->prepare("SELECT password FROM Users WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    // 🔴 평문 비교가 아니라 password_verify로 비교해야 함!
    if (password_verify($password_input, $row['password'])) {
        // (아래 삭제코드는 그대로 사용)
        $conn->query("DELETE FROM Interests WHERE student_id='$student_id'");
        $conn->query("DELETE FROM Purchases WHERE buyer_id='$student_id'");
        $conn->query("DELETE FROM ChatMessages WHERE sender_id='$student_id'");
        $conn->query("DELETE FROM ChatRooms WHERE seller_id='$student_id' OR buyer_id='$student_id'");
        $conn->query("DELETE FROM Books WHERE seller_id='$student_id'");
        $conn->query("DELETE FROM Users WHERE student_id='$student_id'");
        session_destroy();
        echo "<script>
            alert('회원 탈퇴가 완료되었습니다.');
            location.href = '../index.php';
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
