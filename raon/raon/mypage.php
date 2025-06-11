<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html"); exit;
}

$conn = new mysqli("localhost", "root", "1234", "test");
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];

// 카운트 쿼리
$count_sqls = [
    'book_count' => "SELECT COUNT(*) FROM Books WHERE seller_id='$student_id'",
    'interest_count' => "SELECT COUNT(*) FROM Interests WHERE student_id='$student_id'",
    'sold_count' => "SELECT COUNT(*) FROM Books WHERE seller_id='$student_id' AND status='판매완료'",
];
$counts = [];
foreach ($count_sqls as $k=>$sql) {
    $res = $conn->query($sql); $row = $res->fetch_row(); $counts[$k] = $row[0];
}

// 최근 등록글 3개
$books = [];
$sql = "SELECT book_id, title, status, created_at FROM Books WHERE seller_id='$student_id' ORDER BY created_at DESC LIMIT 3";
$res = $conn->query($sql);
while($row = $res->fetch_assoc()) $books[] = $row;
?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>마이페이지 | RAON</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #FFEDC7; }
        .main-box { background: #fff; border-radius: 13px; max-width: 1000px; margin: 40px auto 0 auto; padding: 34px 32px 38px 32px; min-height: 550px;}
        .mypage-menu { float:left; width:200px; }
        .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding:24px 20px; margin-bottom:30px;}
        .mypage-menu .menu-box ul {list-style:none;padding:0;margin:0;}
        .mypage-menu .menu-box li {margin-bottom:18px;}
        .mypage-menu .menu-box li:last-child {margin-bottom:0;}
        .mypage-menu .menu-box a {color:#664317;text-decoration:none;font-size:1.07em;}
        .mypage-main {margin-left:240px;}
        .info-title { font-weight:bold; font-size:1.23em; letter-spacing:1px; margin-bottom:12px;}
        .myinfo-box { border-left:3px solid #E2B575; padding-left:23px; margin-bottom:30px;}
        .myinfo-box .name { font-weight:bold; font-size:1.17em;}
        .myinfo-box .id { color:#73501E; font-size:.98em;}
        .myinfo-stats { margin-top:7px; }
        .myinfo-stats span { margin-right:28px; font-size:1.01em;}
        .myinfo-stats .num { color:#B77723; font-weight:bold; margin-right:2px;}
        .recent-title {font-weight:bold; font-size:1.11em; margin:30px 0 7px 0;}
        .recent-list {margin-bottom:33px;}
        .recent-list a { display:block; margin-bottom:8px; color:#222; text-decoration:none; transition:.1s;}
        .recent-list a:hover { text-decoration:underline; color:#be8c35;}
        .recent-status { color:#C79C5C; font-size: .95em; margin-left:9px; }
        .recent-date { color:#aaa; font-size:.93em; margin-left:8px;}
        .curriculum-box {margin-top:25px;}
        .curriculum-link {display:inline-block; margin-top:15px;}
        .curriculum-link img {width:110px;}
        .hr-line {border:0; border-top:1.3px solid #E2B575; margin:20px 0;}
        @media (max-width: 900px) {
            .main-box { padding:18px 5vw 22px 5vw; }
            .mypage-menu { float:none; width:auto; margin-bottom:22px;}
            .mypage-main { margin-left:0; }
        }
        /* 뱃지 스타일 */
        #chatBadge {
          display:none;
          margin-left:7px;
          background:#e74c3c;
          color:#fff;
          font-size:13px;
          border-radius:50%;
          padding:2px 8px;
          font-weight:bold;
          vertical-align:middle;
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
        <li><a href="my_books.php">등록한 글 목록</a></li>
        <li><a href="my_interest.php">관심 책 목록</a></li>
        <li><a href="my_records.php">구매 / 판매 기록</a></li>
        <li>
          <a href="my_chat.php">채팅</a>
          <span id="chatBadge"></span>
        </li>
      </ul>
    </div>
  </div>
  <!-- 본문 -->
  <div class="mypage-main">
      <div class="info-title">내 정보</div>
      <div class="myinfo-box">
          <span class="name"><?=$name?></span> <span class="id"><?=$student_id?></span>
          <div class="myinfo-stats">
              <span><span class="num"><?=$counts['book_count']?></span> - 등록한 책</span>
              <span><span class="num"><?=$counts['interest_count']?></span> - 관심 책</span>
              <span><span class="num"><?=$counts['sold_count']?></span> - 판매 완료</span>
          </div>
      </div>
      <div class="recent-title">최근 활동</div>
      <div class="recent-list">
          <?php if (!$books): ?>
              <div style="color:#a6a6a6;">최근 등록한 책이 없습니다.</div>
          <?php else:
              foreach ($books as $book): ?>
                  <a href="book_detail.php?id=<?=$book['book_id']?>">
                      <?=$book['title']?>
                      <span class="recent-status">[상태: <?=$book['status']?>]</span>
                      <span class="recent-date"><?=date('Y.m.d', strtotime($book['created_at']))?></span>
                  </a>
              <?php endforeach;
          endif; ?>
      </div>
      <div class="curriculum-box">
          <div class="recent-title" style="margin-bottom:7px;">교과과정 바로 가기</div>
          <hr class="hr-line">
          <a href="https://haksa.gwangju.ac.kr/~op/sugang/gyogwa/newgyogwa20251.php3" class="curriculum-link" target="_blank">
              <img src="/raon/uploads/logo.jpg" alt="GWANGJU UNIVERSITY">
          </a>
      </div>
  </div>
  <div style="clear:both;"></div>
</div>
<footer style="margin-top:32px;text-align:center;color:#C1A06C;">© RAON</footer>
<?php if (isset($_SESSION['student_id'])): ?>
<script>
function updateChatBadge() {
    fetch('/raon/unread_count.php')
      .then(res => res.text())
      .then(count => {
        const badge = document.getElementById('chatBadge');
        if (badge) {
          if (parseInt(count) > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
          } else {
            badge.style.display = 'none';
          }
        }
      });
}
setInterval(updateChatBadge, 3000);
updateChatBadge();
</script>
<?php endif; ?>
</body>
</html>
