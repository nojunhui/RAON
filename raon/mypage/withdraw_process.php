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

// 비밀번호(해시) 조회
$stmt = $conn->prepare("SELECT password FROM Users WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    if (password_verify($password_input, $row['password'])) {

        // 본인이 등록한 책들의 book_id 모아서 참조 삭제
        $book_ids = [];
        $res = $conn->query("SELECT book_id FROM Books WHERE seller_id='$student_id'");
        while($row2 = $res->fetch_assoc()) {
            $book_ids[] = $row2['book_id'];
        }
        if (!empty($book_ids)) {
            $id_list = implode(',', $book_ids);
            // 관심목록, 구매, 채팅, 리뷰 등 book_id 연관 row 먼저 삭제
            $conn->query("DELETE FROM Interests WHERE book_id IN ($id_list)");
            $conn->query("DELETE FROM Purchases WHERE book_id IN ($id_list)");
            $conn->query("DELETE FROM ChatMessages WHERE chatroom_id IN (SELECT chatroom_id FROM ChatRooms WHERE book_id IN ($id_list))");
            $conn->query("DELETE FROM ChatRooms WHERE book_id IN ($id_list)");
            $conn->query("DELETE FROM Reviews WHERE book_id IN ($id_list)"); // ★ 책에 달린 리뷰 삭제
        }
        // 내가 남긴 관심/구매/채팅/리뷰
        $conn->query("DELETE FROM Interests WHERE student_id='$student_id'");
        $conn->query("DELETE FROM Purchases WHERE buyer_id='$student_id'");
        $conn->query("DELETE FROM ChatMessages WHERE sender_id='$student_id'");
        $conn->query("DELETE FROM ChatRooms WHERE seller_id='$student_id' OR buyer_id='$student_id'");
        $conn->query("DELETE FROM Reviews WHERE student_id='$student_id'"); // ★ 내가 쓴 리뷰 삭제
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
