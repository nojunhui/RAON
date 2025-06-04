<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}
$student_id = $_SESSION['student_id'];
include 'header.php';
$conn = new mysqli("localhost", "root", "1234", "test");

// 구매/판매 모드 결정
$mode = $_GET['mode'] ?? 'buy'; // 'buy' 또는 'sell'

// 내 채팅방 리스트
if ($mode === 'sell') {
    // 내가 판매자
    $rooms_sql = "
        SELECT CR.*, U.name AS other_name, B.title, B.image_path,
               (SELECT message FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) AS last_msg,
               (SELECT sent_at FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) AS last_time
        FROM ChatRooms CR
        JOIN Users U ON CR.buyer_id = U.student_id
        JOIN Books B ON CR.book_id = B.book_id
        WHERE CR.seller_id = '$student_id'
        ORDER BY last_time DESC
    ";
} else {
    // 내가 구매자
    $rooms_sql = "
        SELECT CR.*, U.name AS other_name, B.title, B.image_path,
               (SELECT message FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) AS last_msg,
               (SELECT sent_at FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) AS last_time
        FROM ChatRooms CR
        JOIN Users U ON CR.seller_id = U.student_id
        JOIN Books B ON CR.book_id = B.book_id
        WHERE CR.buyer_id = '$student_id'
        ORDER BY last_time DESC
    ";
}
$res = $conn->query($rooms_sql);
$chatrooms = [];
while($row = $res->fetch_assoc()) $chatrooms[] = $row;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>채팅 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: #FFEDC7; margin:0; font-family: 'RIDIBatang', sans-serif; }
    .main-box { background: #fff; border-radius: 13px; max-width: 1000px; margin: 40px auto; padding: 34px 32px 38px 32px; min-height: 550px;}
    .mypage-menu { float: left; width: 200px;}
    .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding: 24px 20px;}
    .mypage-menu .menu-box ul { list-style:none; padding:0; margin:0;}
    .mypage-menu .menu-box li { margin-bottom:18px;}
    .mypage-menu .menu-box a, .mypage-menu .menu-box b { color:#664317; text-decoration:none; font-size:1.07em;}
    .chat-list-main { margin-left: 240px;}
    .chat-toggle-row { margin-bottom:16px;}
    .chat-toggle-btn {
        padding: 8px 26px; border-radius: 8px; border: 1.5px solid #8c6121;
        background: #ffe2b4; color: #6a3b07; font-weight: bold; font-size:1em;
        margin-right: 14px; cursor:pointer;
    }
    .chat-toggle-btn.active { background: #f7d19b; border-color: #bc8d46; }
    .chat-list { margin-top:0; }
    .chatroom-row { display: flex; align-items: center; gap:16px; border-bottom: 1px solid #efdeb7; padding: 15px 5px; cursor: pointer;}
    .chat-thumb { width: 54px; height: 66px; border-radius:7px; border:1.2px solid #efd6ab; object-fit:cover; background: #f6e7d0;}
    .chat-info { flex: 1; min-width:0;}
    .chat-book-title { font-weight:bold; font-size:1.05em; color:#5c3910;}
    .chat-other-name { color:#654418; font-size:1em; margin-bottom: 3px; }
    .chat-last-msg { color: #87775a; font-size: 0.97em; margin-top:3px;}
    .chat-last-time { color: #b3a083; font-size: 0.93em; margin-left: 9px;}
    .chat-empty { color:#b1a78e; text-align:center; margin:80px 0 60px 0; font-size:1.05em;}
    .chatroom-row:hover { background:#fff3e0; }

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
        <li><a href="my_records.php">구매 / 판매 기록</a></li>
        <li><b>채팅</b></li>
      </ul>
    </div>
  </div>
  <!-- 채팅방 리스트 본문 -->
  <div class="chat-list-main">
    <div class="chat-toggle-row">
      <button class="chat-toggle-btn<?= $mode=='buy'?' active':'' ?>" onclick="location.href='my_chat.php?mode=buy'">구매</button>
      <button class="chat-toggle-btn<?= $mode=='sell'?' active':'' ?>" onclick="location.href='my_chat.php?mode=sell'">판매</button>
    </div>
    <div class="chat-list">
      <?php if(empty($chatrooms)): ?>
        <div class="chat-empty">채팅 내역이 없습니다.</div>
      <?php else: foreach($chatrooms as $r): ?>
        <div class="chatroom-row" ondblclick="location.href='chat_room.php?chatroom_id=<?= $r['chatroom_id'] ?>'">
          <img class="chat-thumb" src="<?= htmlspecialchars($r['image_path']?:'noimage.png') ?>">
          <div class="chat-info">
            <div class="chat-other-name"><?= htmlspecialchars($r['other_name']) ?></div>
            <div class="chat-book-title">[<?= htmlspecialchars($r['title']) ?>]</div>
            <div class="chat-last-msg"><?= htmlspecialchars($r['last_msg']) ?></div>
          </div>
          <div class="chat-last-time">
            <?= $r['last_time'] ? date('Y.m.d H:i', strtotime($r['last_time'])) : '' ?>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
  <div style="clear:both"></div>
</div>
<footer style="text-align:center; margin-top:32px; color:#C1A06C;">© RAON</footer>
<script>
  // 클릭 or 더블클릭 모두 이동 지원하려면 아래 활성화
  document.querySelectorAll('.chatroom-row').forEach(function(row){
    row.onclick = function(){ location.href = row.getAttribute('ondblclick').replace("location.href=","").replace(/'/g,""); }
  });
</script>
</body>
</html>
