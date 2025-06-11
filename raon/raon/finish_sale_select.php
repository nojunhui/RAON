<?php
session_start();
if (!isset($_SESSION['student_id'])) exit;
$seller_id = $_SESSION['student_id'];
$book_id = intval($_GET['book_id'] ?? 0);

$conn = new mysqli("localhost","root","1234","test");

// 구매자별 마지막 메시지, 시간, 썸네일, 책제목
$res = $conn->query("
  SELECT 
    CR.chatroom_id, U.student_id, U.name,
    B.title, B.image_path,
    (SELECT message FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) as last_msg,
    (SELECT sent_at FROM ChatMessages WHERE chatroom_id=CR.chatroom_id ORDER BY sent_at DESC LIMIT 1) as last_time
  FROM ChatRooms CR
  JOIN Users U ON CR.buyer_id = U.student_id
  JOIN Books B ON CR.book_id = B.book_id
  WHERE CR.book_id = $book_id AND CR.seller_id = '$seller_id'
  ORDER BY last_time DESC
");
$buyers = [];
while ($row = $res->fetch_assoc()) $buyers[] = $row;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <title>판매완료 구매자 선택 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { background: #FFEDC7; font-family: 'RIDIBatang', sans-serif; }
    .sale-select-box { background:#fff; border-radius:13px; max-width:560px; margin: 40px auto; padding:32px 26px; }
    .buyer-row { display:flex; align-items:center; border-bottom:1px solid #f4e4bc; padding:16px 0; }
    .buyer-radio { margin-right: 15px; }
    .buyer-thumb { width:54px; height:66px; border-radius:7px; object-fit:cover; background:#f6e7d0; border:1.2px solid #efd6ab;}
    .buyer-info { flex:1; min-width:0; margin-left: 13px;}
    .buyer-name { color:#654418; font-size:1.04em; font-weight:bold;}
    .buyer-title { color:#80674c; font-size:1em; margin-top:2px; }
    .buyer-msg { color:#a1917a; font-size:0.97em; margin-top:4px;}
    .buyer-time { color:#baa484; font-size:0.94em; margin-left:8px;}
    .submit-btn {
      margin-top:20px; background:#ff7e1b; color:#fff; border:none; border-radius:8px; 
      font-size:1em; padding:9px 23px; font-family:inherit; cursor:pointer; 
      transition: background 0.17s; box-shadow:0 1px 3px #ffe3ba3a;
    }
    .submit-btn:hover { background:#ff6000; }

    
  </style>
</head>
<body>
  <div class="topnav">
    <div class="logo" onclick="location.href='index.php'">RAON</div>
  </div>
  <div class="sale-select-box">
    <h3 style="margin-top:0;">판매완료로 처리할 구매자를 선택하세요</h3>
    <form method="post" action="finish_sale.php" onsubmit="return checkBuyerSelected();">
      <input type="hidden" name="book_id" value="<?= $book_id ?>">
      <?php if($buyers): foreach($buyers as $b): ?>
        <div class="buyer-row">
          <input class="buyer-radio" type="radio" name="buyer_id" value="<?= $b['student_id'] ?>" required>
          <img class="buyer-thumb" src="<?= htmlspecialchars($b['image_path']?:'noimage.png') ?>">
          <div class="buyer-info">
            <div class="buyer-name"><?= htmlspecialchars($b['name']) ?></div>
            <div class="buyer-title"><?= htmlspecialchars($b['title']) ?></div>
            <div class="buyer-msg">
              <?= htmlspecialchars($b['last_msg']) ?>
              <?php if($b['last_time']): ?>
                <span class="buyer-time">(<?= date('Y.m.d H:i', strtotime($b['last_time'])) ?>)</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; else: ?>
        <div style="color:#b79c68;">이 책에 대해 대화한 구매자가 없습니다.</div>
      <?php endif; ?>
      <button type="submit" class="submit-btn">확인</button>
    </form>
    <script>
function checkBuyerSelected() {
    // radio가 한 개 이상 있고, 체크된 게 있는지 검사
    const checked = document.querySelector('input[name="buyer_id"]:checked');
    if (!checked) {
        alert('구매자를 선택하세요.');
        return false;
    }
    return confirm('이 구매자로 판매완료 처리할까요?');
}
</script>
  </div>
</body>
</html>
