<?php
session_start();
// 로그인 확인
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}
$student_id = $_SESSION['student_id'];
$conn = new mysqli("localhost", "root", "1234", "test");

// 내가 등록한 책 조회
$sql = "SELECT * FROM Books WHERE seller_id = '$student_id'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>마이페이지 | RAON</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 위에서 안내한 CSS를 직접 넣어도 됨 */
    </style>
</head>
<body>
<div class="topnav">
    <div class="logo" onclick="location.href='index.php'">RAON</div>
    <div style="flex:1;"></div>
    <a href="post_book.html"><button>교재 등록</button></a>
    <a href="logout.php"><button>로그아웃</button></a>
</div>
<div class="main-content">
    <div class="my-books-list">
        <div class="my-books-title">내가 등록한 책</div>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="my-book-row">
                <span class="my-book-title"><?=htmlspecialchars($row['title'])?></span>
                <?php if ($row['status'] == '판매중'): ?>
                    <form method="post" action="mark_sold.php" style="display:inline;">
                        <input type="hidden" name="book_id" value="<?=$row['book_id']?>">
                        <button type="submit" class="mark-sold-btn">판매완료</button>
                    </form>
                <?php else: ?>
                    <span class="sold-label">[판매완료]</span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>