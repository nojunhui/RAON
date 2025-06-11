<?php
session_start();
if (!isset($_SESSION['student_id'])) exit('로그인 필요');

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "test");
$student_id = $_SESSION['student_id'];

// ① 구매 목록 불러오기 (내가 산 책들)
$sql_buy = "SELECT B.*, P.purchased_at
            FROM Purchases P
            JOIN Books B ON P.book_id = B.book_id
            WHERE P.buyer_id='$student_id'
            ORDER BY P.purchased_at DESC";
$res_buy = $conn->query($sql_buy);

// ② 판매 목록 불러오기 (내가 판 책들)
$sql_sell = "SELECT * FROM Books WHERE seller_id='$student_id'";
$res_sell = $conn->query($sql_sell);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>구매 / 판매 기록</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="main-box">
    <div class="mypage-menu">
        <div class="menu-box">
            <ul>
                <li><a href="edit_profile.php">회원정보 수정</a></li>
                <li><a href="my_books.php">등록한 글 목록</a></li>
                <li><a href="my_interest.php">관심 책 목록</a></li>
                <li><b>구매 / 판매 기록</b></li>
                <li><a href="my_chat.php">채팅</a></li>
            </ul>
        </div>
    </div>
    <div class="mypage-main">
        <div class="info-title">구매 / 판매 기록</div>

        <!-- 구매 목록 -->
        <div style="background:#fff8ed; border-radius:15px; padding:20px 25px; margin-bottom:25px;">
            <b>구매 목록</b>
            <ul>
                <?php if ($res_buy->num_rows == 0): ?>
                    <li style="color:#b79c78;">구매한 책이 없습니다.</li>
                <?php else: ?>
                    <?php while($row = $res_buy->fetch_assoc()): ?>
                        
                        <li>
                            [<?= htmlspecialchars($row['title']) ?>] 
                            <?= htmlspecialchars($row['author']) ?> | 
                            <?= htmlspecialchars($row['publisher']) ?> | 
                            <?= date('Y-m-d', strtotime($row['purchased_at'])) ?>
                            

                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- 판매 목록 -->
        <div style="background:#fff8ed; border-radius:15px; padding:20px 25px;">
            <b>판매 목록</b>
            <ul>
                <?php if ($res_sell->num_rows == 0): ?>
                    <li style="color:#b79c78;">등록/판매한 책이 없습니다.</li>
                <?php else: ?>
                    <?php while($row = $res_sell->fetch_assoc()): ?>
                        <li>
                            [<?= htmlspecialchars($row['title']) ?>]
                            <?= htmlspecialchars($row['author']) ?> | 
                            <?= htmlspecialchars($row['publisher']) ?> | 
                            <?= htmlspecialchars($row['status']) ?>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>
<footer style="text-align:center; margin-top:32px; color:#C1A06C;">© RAON</footer>
</body>
</html>
