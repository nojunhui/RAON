<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
include 'header.php';

$conn = new mysqli("localhost", "root", "1234", "test");

// ---- 페이징 처리 ----
$buy_page = intval($_GET['buy_page'] ?? 1);
$sell_page = intval($_GET['sell_page'] ?? 1);
$per_page = 5;
$buy_offset = ($buy_page - 1) * $per_page;
$sell_offset = ($sell_page - 1) * $per_page;

// ---- 구매 목록: Purchases + Books 조인 ----
$buy_sql = "SELECT B.*, P.purchase_id, P.purchased_at
    FROM Purchases P
    JOIN Books B ON P.book_id = B.book_id
    WHERE P.buyer_id = '$student_id'
    ORDER BY P.purchased_at DESC
    LIMIT $per_page OFFSET $buy_offset";
$buy_result = $conn->query($buy_sql);
$buy_books = [];
while ($row = $buy_result->fetch_assoc()) $buy_books[] = $row;

// ---- 구매 총 개수 ----
$buy_total = 0;
$res = $conn->query("SELECT COUNT(*) FROM Purchases WHERE buyer_id='$student_id'");
if ($res) { $buy_total = $res->fetch_row()[0]; }

// ---- 판매 목록: Books 테이블에서 seller_id == 내 학번, status = '판매완료' ----
$sell_sql = "SELECT * FROM Books WHERE seller_id = '$student_id' AND status = '판매완료' ORDER BY created_at DESC LIMIT $per_page OFFSET $sell_offset";
$sell_result = $conn->query($sell_sql);
$sell_books = [];
while ($row = $sell_result->fetch_assoc()) $sell_books[] = $row;

// ---- 판매 총 개수 (status = '판매완료'만 카운트) ----
$sell_total = 0;
$res = $conn->query("SELECT COUNT(*) FROM Books WHERE seller_id='$student_id' AND status='판매완료'");
if ($res) { $sell_total = $res->fetch_row()[0]; }

?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>구매 / 판매 기록 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    @font-face {
      font-family: 'RIDIBatang';
      src: url('https://fastly.jsdelivr.net/gh/projectnoonnu/noonfonts_twelve@1.0/RIDIBatang.woff') format('woff');
      font-weight: normal;
      font-style: normal;
    }
    body { background: #FFEDC7; margin: 0; font-family: 'RIDIBatang', sans-serif; }
    .main-box { background: #fff; border-radius: 13px; max-width: 1000px; margin: 40px auto; padding: 34px 32px 38px 32px; min-height: 550px; }
    .mypage-menu { float: left; width: 200px; }
    .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding: 24px 20px; margin-bottom: 30px; }
    .mypage-menu .menu-box ul { list-style: none; padding: 0; margin: 0; }
    .mypage-menu .menu-box li { margin-bottom: 18px; }
    .mypage-menu .menu-box li:last-child { margin-bottom: 0; }
    .mypage-menu .menu-box a, .mypage-menu .menu-box b { color: #664317; text-decoration: none; font-size: 1.07em; }
    .mypage-main { margin-left: 240px; min-height: 500px;}
    .info-title { font-weight: bold; font-size: 1.23em; letter-spacing: 1px; margin-bottom: 18px; border-bottom: 1px solid #8b5a2b; padding-bottom: 4px;}
    .records-wrap { display: flex; flex-direction: column; gap: 26px; }
    .record-box { background: #FFF8EC; border-radius: 11px; padding: 24px 24px 14px 24px; }
    .record-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .record-title { font-size: 1.13em; font-weight: bold; color: #4b2c06; }
    .record-list { }
    .record-row { padding: 7px 0 7px 0; border-bottom: 1px dashed #ead5b4; font-size: 1.02em; display: flex; align-items: center; gap: 8px;}
    .record-row:last-child { border-bottom: none;}
    .record-row a { color: #413006; text-decoration: none; font-weight: bold;}
    .record-row a:hover { text-decoration: underline; }
    .record-meta { color: #6a5e50; font-size: 0.96em; margin-left: 8px;}
    .record-empty { color: #aaa; font-size: 0.99em; padding: 13px 0; }
    .arrow-btn { background: none; border: none; font-size: 1.16em; color: #987444; cursor: pointer; padding: 2px 10px; }
    .arrow-btn:disabled { color: #e2c598; cursor: not-allowed; }
    @media (max-width: 900px) {
      .main-box { padding: 18px 5vw 22px 5vw; }
      .mypage-menu { float: none; width: auto; margin-bottom: 22px; }
      .mypage-main { margin-left: 0; }
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
        <li><a href="my_interest.php">관심 책 목록</a></li>
        <li><b>구매 / 판매 기록</b></li>
        <li><a href="my_chat.php">채팅</a></li>
      </ul>
    </div>
  </div>
  <!-- 본문 -->
  <div class="mypage-main">
    <div class="info-title">구매 / 판매 기록</div>
    <div class="records-wrap">
      <!-- 구매 목록 -->
      <div class="record-box">
        <div class="record-head">
          <div class="record-title">구매 목록</div>
          <div>
            <button class="arrow-btn" onclick="location.href='my_records.php?buy_page=<?= max($buy_page-1,1) ?>&sell_page=<?= $sell_page ?>'" <?= $buy_page<=1?'disabled':'' ?>>
              <i class="fa fa-chevron-left"></i>
            </button>
            <span style="font-size:0.99em;"><?= $buy_page ?></span>
            <button class="arrow-btn" onclick="location.href='my_records.php?buy_page=<?= $buy_page+1 ?>&sell_page=<?= $sell_page ?>'" <?= $buy_offset+$per_page >= $buy_total?'disabled':'' ?>>
              <i class="fa fa-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="record-list">
          <?php if(empty($buy_books)): ?>
            <div class="record-empty">구매한 책이 없습니다.</div>
          <?php else: foreach($buy_books as $book): ?>
            <div class="record-row">
              <a href="book_detail.php?id=<?= $book['book_id'] ?>">[<?= htmlspecialchars($book['title']) ?>]</a>
              <span class="record-meta"><?= htmlspecialchars($book['author']) ?> ｜ <?= htmlspecialchars($book['publisher']) ?> ｜ <?= $book['category']=='전공' ? htmlspecialchars($book['major']) : htmlspecialchars($book['subject']) ?></span>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
      <!-- 판매 목록 -->
      <div class="record-box">
        <div class="record-head">
          <div class="record-title">판매 목록</div>
          <div>
            <button class="arrow-btn" onclick="location.href='my_records.php?buy_page=<?= $buy_page ?>&sell_page=<?= max($sell_page-1,1) ?>'" <?= $sell_page<=1?'disabled':'' ?>>
              <i class="fa fa-chevron-left"></i>
            </button>
            <span style="font-size:0.99em;"><?= $sell_page ?></span>
            <button class="arrow-btn" onclick="location.href='my_records.php?buy_page=<?= $buy_page ?>&sell_page=<?= $sell_page+1 ?>'" <?= $sell_offset+$per_page >= $sell_total?'disabled':'' ?>>
              <i class="fa fa-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="record-list">
          <?php if(empty($sell_books)): ?>
            <div class="record-empty">판매한 책이 없습니다.</div>
          <?php else: foreach($sell_books as $book): ?>
            <div class="record-row">
              <a href="book_detail.php?id=<?= $book['book_id'] ?>">[<?= htmlspecialchars($book['title']) ?>]</a>
              <span class="record-meta"><?= htmlspecialchars($book['author']) ?> ｜ <?= htmlspecialchars($book['publisher']) ?> ｜ <?= $book['category']=='전공' ? htmlspecialchars($book['major']) : htmlspecialchars($book['subject']) ?></span>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div style="clear:both;"></div>
</div>
<footer style="text-align:center; margin-top:32px; color:#C1A06C;">© RAON</footer>
<script src="script.js"></script>
</body>
</html>
