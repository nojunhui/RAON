<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "1234", "test");
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];

// 내가 등록한 책 목록 불러오기
$books = [];
$sql = "SELECT * FROM Books WHERE seller_id='$student_id' ORDER BY created_at DESC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) $books[] = $row;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>마이페이지 - 등록한 글 목록</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: #FFEDC7; margin: 0; font-family: 'Noto Sans KR', sans-serif; }
    .main-box { background: #fff; border-radius: 13px; max-width: 1000px; margin: 40px auto; padding: 34px 32px 38px 32px; min-height: 550px; }
    .mypage-menu { float: left; width: 200px; }
    .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding: 24px 20px; margin-bottom: 30px; }
    .mypage-menu .menu-box ul { list-style: none; padding: 0; margin: 0; }
    .mypage-menu .menu-box li { margin-bottom: 18px; }
    .mypage-menu .menu-box li:last-child { margin-bottom: 0; }
    .mypage-menu .menu-box a, .mypage-menu .menu-box b { color: #664317; text-decoration: none; font-size: 1.07em; }
    .mypage-main { margin-left: 240px; }
    .info-title { font-weight: bold; font-size: 1.23em; letter-spacing: 1px; margin-bottom: 12px; }
    .post-list { margin-top: 20px; }
    .post-list div { margin-bottom: 15px; }
    .footer { text-align: center; margin-top: 50px; color: #C1A06C; font-size: 14px; }
    .post-title { font-weight: bold; color: #444; }
    .post-meta { color: #999; font-size: 0.92em; margin-left: 7px; }
    @media (max-width: 900px) {
      .main-box { padding: 18px 5vw 22px 5vw; }
      .mypage-menu { float: none; width: auto; margin-bottom: 22px; }
      .mypage-main { margin-left: 0; }
    }
  </style>
</head>
<body>
<div class="main-box">
  <!-- 사이드 메뉴 -->
  <div class="mypage-menu">
    <div class="menu-box">
      <ul>
        <li><a href="edit_profile.php">회원정보 수정</a></li>
        <li><b>등록한 글 목록</b></li>
        <li><a href="my_interest.php">관심 책 목록</a></li>
        <li><a href="my_records.php">구매 / 판매 기록</a></li>
        <li><a href="my_chat.php">채팅</a></li>
      </ul>
    </div>
  </div>

  <!-- 본문 -->
  <div class="mypage-main">
    <div class="info-title">등록한 글 목록</div>
    <div class="post-list">
      <?php if (empty($books)): ?>
        <div style="color: #888;">등록한 책이 없습니다.</div>
      <?php else:
        $i = 1;
        foreach ($books as $book): ?>
          <div>
            <span class="post-title"><?= $i++ ?>. <?= htmlspecialchars($book['title']) ?></span>
            <span class="post-meta">| <?= htmlspecialchars($book['publisher']) ?> | <?= date('Y.m.d', strtotime($book['created_at'])) ?></span>
          </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <div style="clear:both;"></div>
</div>
<div class="footer">© RAON</div>
</body>
</html>
