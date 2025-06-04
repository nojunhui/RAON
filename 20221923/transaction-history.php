<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html"); exit;
}
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <title>마이페이지 | RAON</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=SUIT:wght@400;700;900&family=Noto+Sans+KR&display=swap" rel="stylesheet" />
  <style>
    @font-face {
      font-family: 'RIDIBatang';
      src: url('https://fastly.jsdelivr.net/gh/projectnoonnu/noonfonts_twelve@1.0/RIDIBatang.woff') format('woff');
      font-weight: normal;
      font-style: normal;
    }

    body { background: #FFEDC7; font-family: 'RIDIBatang', 'SUIT', 'Noto Sans KR', sans-serif; margin: 0; padding: 0; color: #664317; font-weight: 400; }
    .main-box { background: #fff; border-radius: 13px; max-width: 1000px; margin: 40px auto; padding: 34px 32px; min-height: 550px; box-sizing: border-box; display: flex; }
    .mypage-menu { width: 200px; margin-right: 40px; }
    .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding: 24px 20px; margin-bottom: 30px; }
    .mypage-menu ul { list-style: none; padding: 0; margin: 0; font-size: 1.07em; font-weight: 400; color: #664317; }
    .mypage-menu li { margin-bottom: 18px; }
    .mypage-menu a { color: #664317; text-decoration: none; display: block; padding-left: 5px; transition: 0.3s; font-weight: 400; }
    .mypage-menu a.active { font-weight: 900; }

    .mypage-main { flex: 1; }

    section.content { background: transparent; border-radius: 0; box-shadow: none; padding: 0; }
    section.content h2 { font-weight: bold; font-size: 1.23em; letter-spacing: 1px; margin-bottom: 18px; border-bottom: 1px solid #8b5a2b; padding-bottom: 4px; color: #000;}
    section.content ol { list-style: decimal inside; padding-left: 20px; margin: 0 0 40px 0; font-size: 1.05em; }
    section.content li { margin-bottom: 15px; }
    section.content li a { text-decoration: none; color: #664317; transition: color 0.2s ease; }
    section.content li a:hover { color: #be8c35; font-weight: 900; }
    section.content li span { margin-left: 12px; font-size: 0.95em; color: #c79c5c; }

    @media (max-width: 900px) {
      .main-box { flex-direction: column; padding: 18px 5vw; max-width: 95vw; }
      .mypage-menu { width: 100%; margin: 0 0 22px 0; }
    }
  </style>
</head>
<body>

<div class="main-box">
  <!-- 좌측 메뉴 -->
  <div class="mypage-menu">
    <div class="menu-box">
      <ul>
        <li><a href="edit_profile.php">회원정보 수정</a></li>
        <li><a href="mypage/my_books.php">등록한 글 목록</a></li>
        <li><a href="my_interest.php">관심 책 목록</a></li>
        <li><a class="active" href="transaction-history.php">구매 / 판매 기록</a></li>
        <li><a href="chat-list.php">채팅</a></li>
      </ul>
    </div>
  </div>

  <!-- 본문 -->
  <section class="content" role="main" tabindex="0">
    <h2>구매 / 판매 기록</h2>
    <ol>
      <li><a href="#">[파이썬으로 경험하는 빅데이터 분석과 머신러닝]<span>[구매]</span> 2025.x.x</a></li>
      <li><a href="#">[젊은이를 위한 인간관계의 심리학(제3판)]<span>[구매]</span> 2025.x.x</a></li>
      <li><a href="#">[쉽게 배우는 데이터 통신과 컴퓨터 네트워크]<span>[판매]</span> 2025.x.x</a></li>
      <li><a href="#">[리눅스시스템 원리와 실제]<span>[판매]</span> 2025.x.x</a></li>
      <li><a href="#">[데이터베이스 배움터]<span>[판매]</span> 2025.x.x</a></li>
    </ol>
  </section>
</div>

<footer style="margin-top:32px;text-align:center;color:#C1A06C;">© RAON</footer>
<script src="script.js"></script>
</body>
</html>