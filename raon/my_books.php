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
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>마이페이지 - 등록한 글 목록</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: #FFEDC7; margin: 0; font-family: 'RIDIBatang', sans-serif; }
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
    .post-item {
      display: flex; align-items: center; justify-content: space-between;
      background: #FFF8EC; border-radius: 11px; padding: 16px 22px; margin-bottom: 15px;
      box-shadow:0 2px 12px rgba(222,182,123,0.06);
      font-family: 'RIDIBatang', sans-serif;
    }
    .post-title { font-weight: bold; color: #444; font-size:1.08em; }
    .post-title a { color: #3c2c12; text-decoration: none; transition: color 0.15s; }
    .post-title a:hover { text-decoration: underline; color: #c68a26; }
    .post-meta { color: #999; font-size: 0.92em; margin-left: 7px; }
    .post-btn-area { display:flex; align-items:center; gap:10px; }
    .sell-btn {
      background:#ff7e1b; color:#fff; border:none; border-radius:7px;
      font-size:0.99em; font-family:inherit; padding:7px 13px; font-weight:bold; cursor:pointer;
      transition: background 0.18s;
    }
    .sell-btn:hover { background:#ff6000; }
    .sold-label {
      color: #c68a26; font-weight: bold; font-size:0.99em;
      background: #fff6df; padding: 5px 14px; border-radius: 7px; border:1px solid #f9e2b0;
    }
    @media (max-width: 900px) {
      .main-box { padding: 18px 5vw 22px 5vw; }
      .mypage-menu { float: none; width: auto; margin-bottom: 22px; }
      .mypage-main { margin-left: 0; }
      .post-item { flex-direction: column; align-items: flex-start; }
      .post-btn-area { margin-top:9px; }
    }
.search-bar {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

.search-input {
  flex: 1 1 auto;
  min-width: 0;
  border: 1.5px solid #a5753f;
  border-radius: 12px 12px 12px 12px;
  font-size: 1em;
  padding: 10px 18px;
  background: #fff;
  height: 42px;
  box-sizing: border-box;
  outline: none;
}

.search-btn {
  background: #ffcd99;
  color: #fff;
  border-radius: 12px 12px 12px 12px;
  font-size: 1.1em;
  font-weight: bold;
  padding: 0 28px;
  height: 42px;
  white-space: nowrap;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
  margin-left: 0;
  outline: none;
  transition: background 0.15s;
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
    <div class="post-item">
      <div>
        <span class="post-title">
          <a href="book_detail.php?id=<?= $book['book_id'] ?>">
            <?= $i++ ?>. <?= htmlspecialchars($book['title']) ?>
          </a>
        </span>
        <span class="post-meta">| <?= htmlspecialchars($book['publisher']) ?> | <?= date('Y.m.d', strtotime($book['created_at'])) ?></span>
      </div>
      <div class="post-btn-area">
        <?php if($book['status'] == '판매중'): ?>
          <button class="sell-btn" onclick="location.href='finish_sale_select.php?book_id=<?= $book['book_id'] ?>'">판매완료하기</button>
        <?php else: ?>
          <span class="sold-label">판매완료</span>
        <?php endif; ?>
      </div>
    </div>
<?php endforeach; endif; ?>
    </div>
  </div>

  <div style="clear:both;"></div>
</div>
<div class="footer">© RAON</div>
<script src="script.js"></script>
</body>
</html>
