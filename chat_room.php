<?php
session_start();
if (!isset($_SESSION['student_id'])) { header("Location: login.html"); exit; }
$student_id = $_SESSION['student_id'];

$chatroom_id = intval($_GET['chatroom_id'] ?? 0);
if (!$chatroom_id) { echo "잘못된 접근입니다."; exit; }

$conn = new mysqli("localhost", "root", "1234", "test");

// (1) 이 채팅방의 마지막 메시지 id 구하기
$res = $conn->query("SELECT MAX(message_id) AS max_id FROM ChatMessages WHERE chatroom_id=$chatroom_id");
$max_id = (int)($res->fetch_assoc()['max_id'] ?? 0);

// (2) 내 역할(판매자/구매자) 판별해서 last_read_message_id 갱신
$res2 = $conn->query("SELECT seller_id, buyer_id FROM ChatRooms WHERE chatroom_id=$chatroom_id");
$row = $res2->fetch_assoc();
if ($row['seller_id'] == $student_id) {
  $conn->query("UPDATE ChatRooms SET last_read_message_id_seller = $max_id WHERE chatroom_id=$chatroom_id");
} else if ($row['buyer_id'] == $student_id) {
  $conn->query("UPDATE ChatRooms SET last_read_message_id_buyer = $max_id WHERE chatroom_id=$chatroom_id");
}

// ====================== 아래 기존 코드 이어서 ======================
$room_sql = "SELECT CR.*, B.title, B.image_path, B.status, B.book_id,
    S.name as seller_name, U.name as buyer_name
 FROM ChatRooms CR
 JOIN Books B ON CR.book_id = B.book_id
 JOIN Users S ON CR.seller_id = S.student_id
 JOIN Users U ON CR.buyer_id = U.student_id
 WHERE CR.chatroom_id = $chatroom_id";
$room = $conn->query($room_sql)->fetch_assoc();
if(!$room) { echo "채팅방을 찾을 수 없습니다."; exit; }

$isSeller = ($student_id == $room['seller_id']);
$isBuyer = ($student_id == $room['buyer_id']);
$book_id = $room['book_id'];
$other_name = $isSeller ? $room['buyer_name'] : $room['seller_name'];

// 후기를 이미 썼는지?
$canWriteReview = false;
if ($room['status']=='판매완료' && $isBuyer) {
    $chk = $conn->query("SELECT 1 FROM Reviews WHERE book_id=$book_id AND buyer_id='$student_id'");
    $canWriteReview = !$chk->fetch_row();
}
?>

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>채팅 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: #FFEDC7; margin:0; font-family: 'RIDIBatang', sans-serif; }
    .main-box { background:#fff; border-radius:13px; max-width:900px; margin:40px auto; padding:26px 18px 18px 18px;}
    .chat-head { display: flex; align-items: center; justify-content: space-between; margin-bottom:10px; }
    .chat-title { font-size: 1.15em; font-weight: bold; color:#432700; }
    .chat-status { font-size:1em; color:#7b5b27; font-weight:bold; margin-left:12px;}
    .chat-book-title { font-size:0.98em; color:#6c5b3a;}
    .chat-right-area { display:flex; align-items:center; gap:10px;}
    .chat-book-thumb { width:54px; height:66px; border-radius:7px; border:1px solid #e4c48a; background:#f3e5c7; cursor:pointer; }
    .sell-btn {
      background:#ff7e1b; color:#fff; border:none; border-radius:7px;
      font-size:0.99em; font-family:inherit; padding:7px 13px; font-weight:bold; margin-left:7px; cursor:pointer;
      transition: background 0.18s;
    }
    .sell-btn:hover { background:#ff6000; }
    .sold-label {
      color: #c68a26; font-weight: bold; margin-left: 10px; font-size:0.99em;
      background: #fff6df; padding: 5px 12px; border-radius: 7px; border:1px solid #f9e2b0;
    }
    .chat-body { background: #fff9ee; border-radius:8px; padding:18px 16px 11px 16px; height:400px; overflow-y:auto; border:1.2px solid #f2debb;}
    .msg-row { display: flex; margin-bottom:11px;}
    .msg-row.mine { justify-content:flex-end; }
    .msg-bubble { max-width:350px; padding:13px 18px; border-radius:13px; font-size:1.05em; background:#f7e1b4; color:#59370e; position:relative;}
    .msg-row.mine .msg-bubble { background:#fff; color:#222; border:1.2px solid #ead59d;}
    .msg-time { font-size:0.93em; color:#b3a083; margin-top:6px; text-align:right;}
    .chat-footer { display: flex; align-items: center; gap:10px; margin-top:11px;}
    .chat-input { flex:1; padding:11px 12px; border-radius:8px; border:1.2px solid #e7c187; font-size:1em;}
    .chat-btn { background:#ffd37a; border:1.5px solid #d7b36b; border-radius:8px; padding:8px 15px; color:#764d12; font-weight:bold; cursor:pointer; }
    .file-btn { background:none; border:none; font-size:1.16em; color:#baa079; cursor:pointer; }
    .file-btn:hover { color:#cfab54; }
    .chat-images { margin-top:7px;}
    .chat-image { max-width:120px; max-height:90px; border-radius:7px; margin-right:7px;}
    /* 후기 폼 스타일 */
    .review-form-wrap { max-width:900px; margin:30px auto 0 auto; }
    .review-form-box {
      background:#FFF8EC; padding:18px 17px; border-radius:9px;
      box-shadow:0 2px 12px rgba(222,182,123,0.06); margin-top:13px;
    }
    .review-title { font-weight:bold; color:#ac831b; margin-bottom:7px; }
    .review-textarea { width:100%; min-height:56px; font-size:1.02em; padding:7px 9px;
      border-radius:7px; border:1.2px solid #e7c187; }
    .review-submit-btn {
      margin-top:8px;background:#ff7e1b;color:#fff;border:none;
      border-radius:7px;font-size:1em;padding:7px 19px;cursor:pointer;
      font-family:inherit;
    }
    .review-submit-btn:hover { background:#ff6000; }
    @media (max-width: 900px) { .main-box {padding: 8px 1vw;} }
  </style>
</head>
<body>
<div class="main-box">
  <div class="chat-head">
    <div>
      <span class="chat-title"><?= htmlspecialchars($other_name) ?></span>
      <span class="chat-status"><?= $room['status']=='판매완료' ? '판매완료' : '판매중' ?></span>
      <div class="chat-book-title">[<?= htmlspecialchars($room['title']) ?>]</div>
    </div>
    <div class="chat-right-area">
      <img class="chat-book-thumb" src="<?= htmlspecialchars($room['image_path']?:'noimage.png') ?>"
           alt="책 썸네일"
           onclick="location.href='book_detail.php?id=<?= $book_id ?>'">
      <?php if($isSeller && $room['status']=='판매중'): ?>
        <button class="sell-btn" onclick="location.href='finish_sale_select.php?book_id=<?= $book_id ?>'">판매완료하기</button>
      <?php elseif($room['status']=='판매완료'): ?>
        <span class="sold-label">판매완료</span>
      <?php endif; ?>
    </div>
  </div>
  <div id="chat-body" class="chat-body"></div>
  <form id="msgForm" class="chat-footer" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="chatroom_id" value="<?= $chatroom_id ?>">
    <input type="text" name="message" class="chat-input" maxlength="100" placeholder="메시지 입력(최대 100자)">
    <input type="file" name="image" id="imgInput" style="display:none;" accept="image/*">
    <button type="button" class="file-btn" onclick="document.getElementById('imgInput').click();"><i class="fa fa-image"></i></button>
    <button type="submit" class="chat-btn">전송</button>
  </form>
</div>

<!-- ================= 후기 남기기 폼(구매자+판매완료상태) ================= -->
<?php if($canWriteReview): ?>
  <div class="review-form-wrap">
    <form id="reviewForm" class="review-form-box">
      <div class="review-title"><i class="fa fa-pen"></i> 후기 남기기</div>
      <textarea name="content" maxlength="100" class="review-textarea" placeholder="100자 이내로 입력"></textarea>
      <input type="hidden" name="book_id" value="<?= $book_id ?>">
      <button type="submit" class="review-submit-btn">후기 등록</button>
    </form>
  </div>
  <script>
    document.getElementById('reviewForm').onsubmit = function(e){
      e.preventDefault();
      const fd = new FormData(this);
      fetch('review_write.php', {method:'POST',body:fd})
        .then(r=>r.json()).then(res=>{
          if(res.success) { alert("후기가 등록되었습니다."); location.reload(); }
          else alert(res.msg||"이미 작성했거나 권한 없음");
        });
    }
  </script>
<?php endif; ?>

<footer style="text-align:center; margin-top:18px; color:#C1A06C;">© RAON</footer>
<script>
// 메시지 자동 새로고침
function fetchMessages(scrollToBottom=false) {
  fetch('fetch_messages.php?chatroom_id=<?= $chatroom_id ?>')
  .then(r=>r.json())
  .then(data=>{
    const chatBody = document.getElementById('chat-body');
    chatBody.innerHTML = '';
    data.forEach(function(msg){
      let row = document.createElement('div');
      row.className = 'msg-row' + (msg.sender_id == '<?= $student_id ?>' ? ' mine':'');
      let bubble = document.createElement('div');
      bubble.className = 'msg-bubble';
      // 이미지 메시지 처리
      if(msg.image_path){
        bubble.innerHTML = '<img src="' + msg.image_path + '" class="chat-image"><br>' +
          (msg.message ? msg.message : '');
      } else {
        bubble.textContent = msg.message;
      }
      let time = document.createElement('div');
      time.className = 'msg-time';
      time.textContent = msg.time;
      bubble.appendChild(time);
      row.appendChild(bubble);
      chatBody.appendChild(row);
    });
    if(scrollToBottom) chatBody.scrollTop = chatBody.scrollHeight;
  });
}
fetchMessages(true);
setInterval(fetchMessages, 1200); // 1.2초마다 새로고침

// 메시지 전송
document.getElementById('msgForm').onsubmit = function(e){
  e.preventDefault();
  let fd = new FormData(this);
  fetch('send_message.php', {method:'POST', body:fd})
    .then(r=>r.json())
    .then(res=>{
      if(res.success){
        this.message.value = '';
        this.image.value = '';
        fetchMessages(true);
      }else{
        alert(res.error||'전송 실패');
      }
    });
};
</script>
<script src="script.js"></script>
</body>
</html>
