<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];

include 'header.php';

// 관심 책 불러오기 (Interests + Books + Users)
$conn = new mysqli("localhost", "root", "1234", "test");
$interests = [];
$sql = "SELECT B.*, I.book_id as iid, U.name AS seller_name
        FROM Interests I
        JOIN Books B ON I.book_id = B.book_id
        JOIN Users U ON B.seller_id = U.student_id
        WHERE I.student_id = '$student_id'
        ORDER BY I.interest_id DESC";
$res = $conn->query($sql);
while($row = $res->fetch_assoc()) $interests[] = $row;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>관심 책 목록 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    @font-face {
      font-family: 'RIDIBatang';
      src: url('https://fastly.jsdelivr.net/gh/projectnoonnu/noonfonts_twelve@1.0/RIDIBatang.woff') format('woff');
      font-weight: normal;
      font-style: normal;
    }
    body {
      background: #FFEDC7;
      margin: 0;
      font-family: 'RIDIBatang', sans-serif;
    }
    .main-box {
      background: #fff;
      border-radius: 13px;
      max-width: 1000px;
      margin: 40px auto;
      padding: 34px 32px 38px 32px;
      min-height: 550px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .mypage-menu { float: left; width: 200px; }
    .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding: 24px 20px; margin-bottom: 30px; }
    .mypage-menu .menu-box ul { list-style: none; padding: 0; margin: 0; }
    .mypage-menu .menu-box li { margin-bottom: 18px; }
    .mypage-menu .menu-box li:last-child { margin-bottom: 0; }
    .mypage-menu .menu-box a, .mypage-menu .menu-box b { color: #664317; text-decoration: none; font-size: 1.07em; }
    .mypage-main { margin-left: 240px; }
    .info-title { font-weight: bold; font-size: 1.23em; letter-spacing: 1px; margin-bottom: 18px; border-bottom: 1px solid #8b5a2b; padding-bottom: 4px;}
    .interest-list { margin-top: 14px; }
    .interest-row {
      display: flex;
      align-items: flex-start;
      gap: 19px;
      margin-bottom: 26px;
      padding-bottom: 7px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .interest-thumb {
      width: 68px; height: 82px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #e0c294;
      background: #f8e6c6;
      flex-shrink: 0;
      font-family: 'RIDIBatang', sans-serif;
    }
    .interest-book-info { flex: 1; min-width: 0; }
    .interest-title {
      font-size: 1.07em;
      font-weight: bold;
      color: #222;
      margin-bottom: 6px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .interest-meta {
      color: #58534f;
      font-size: 0.98em;
      margin-bottom: 2px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .interest-publisher {
      color: #767676; font-size: 0.97em;
      margin-bottom: 2px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .interest-detail-row {
      display: flex; align-items: center; gap: 22px;
      margin-top: 5px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .interest-price {
      color: #c82323; font-weight: bold; font-size: 1.11em;
      margin-left: 3px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .heart-btn {
      color: #c82323; background: none; border: none; outline: none;
      cursor: pointer; font-size: 19px; display: inline-flex; align-items: center; gap: 4px;
      transition: color 0.13s;
    }
    .heart-btn.liked { color: #e44141; }
    .interest-count { font-size: 1em; color: #c82323; margin-left: 2px;}
    @media (max-width: 900px) {
      .main-box { padding: 18px 5vw 22px 5vw; }
      .mypage-menu { float: none; width: auto; margin-bottom: 22px; }
      .mypage-main { margin-left: 0; }
      .interest-row { flex-direction: column; align-items: flex-start; gap: 6px; }
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
  <!-- 마이페이지 메뉴 -->
  <div class="mypage-menu">
    <div class="menu-box">
      <ul>
        <li><a href="edit_profile.php">회원정보 수정</a></li>
        <li><a href="my_books.php">등록한 글 목록</a></li>
        <li><b>관심 책 목록</b></li>
        <li><a href="my_records.php">구매 / 판매 기록</a></li>
        <li><a href="my_chat.php">채팅</a></li>
      </ul>
    </div>
  </div>
  <!-- 본문 -->
  <div class="mypage-main">
    <div class="info-title">관심 책 목록</div>
    <div class="interest-list">
      <?php if (empty($interests)): ?>
        <div style="color: #888;">관심 책이 없습니다.</div>
      <?php else: $i=1; foreach($interests as $book): ?>
        <div class="interest-row" id="interest-row-<?= $book['book_id'] ?>">
          <div><?= $i++ ?>.</div>
          <a href="book_detail.php?id=<?= $book['book_id'] ?>">
            <img src="<?= htmlspecialchars($book['image_path'] ?: 'noimage.png') ?>" class="interest-thumb" alt="썸네일">
          </a>
          <div class="interest-book-info">
            <div class="interest-title">
              <a href="book_detail.php?id=<?= $book['book_id'] ?>" style="color:inherit; text-decoration:none;">
                <?= htmlspecialchars($book['title']) ?>
              </a>
            </div>
            <div class="interest-meta"><?= htmlspecialchars($book['author']) ?> 지음 ｜ <?= htmlspecialchars($book['publisher']) ?></div>
            <div class="interest-meta"><?= $book['category']=='전공' ? htmlspecialchars($book['major']) : htmlspecialchars($book['subject']) ?></div>
            <div class="interest-detail-row">
              <div class="interest-price">판매가 <?= number_format($book['selling_price']) ?>원</div>
             <button class="heart-btn liked remove-interest-btn" data-book-id="<?= $book['book_id'] ?>">
  <i class="fa fa-heart"></i>
  <span class="interest-count"><?= $book['interest_count'] ?></span>
</button>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  <div style="clear:both;"></div>
</div>
<footer style="text-align:center; margin-top:32px; color:#C1A06C;">© RAON</footer>
<script src="script.js"></script>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.remove-interest-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const bookId = this.getAttribute('data-book-id');
      fetch('remove_interest.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'book_id=' + encodeURIComponent(bookId)
      })
      .then(res => res.text())
      .then(msg => {
        if (msg.trim() === 'success') {
          document.getElementById('interest-row-' + bookId).remove();
        } else {
          alert('제거 실패!');
        }
      });
    });
  });
});
</script>
</html>