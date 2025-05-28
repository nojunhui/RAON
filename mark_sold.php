<?php
session_start();

if (!isset($_SESSION['student_id'])) exit('로그인 필요');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    $conn = new mysqli("localhost", "root", "1234", "test");
    $sql = "UPDATE Books SET status='판매완료' WHERE book_id = $book_id";
    $conn->query($sql);
    // 성공/실패 체크 가능
    header("Location: mypage.php");
    exit();
}
?>
