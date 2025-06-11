<?php
session_start();
$isLogin = isset($_SESSION['student_id']);
$my_id = $isLogin ? $_SESSION['student_id'] : null;

$conn = new mysqli("localhost", "root", "1234", "test");
$book_id = intval($_GET['id'] ?? 0);
if (!$book_id) {
    echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
    exit;
}

// 책 정보 불러오기 (Users와 조인)
$q = $conn->query("SELECT B.*, U.name as seller_name, U.student_id as seller_id
                   FROM Books B
                   JOIN Users U ON B.seller_id = U.student_id
                   WHERE B.book_id = $book_id");
$row = $q->fetch_assoc();
if (!$row) {
    echo "<script>alert('존재하지 않는 책입니다.');history.back();</script>";
    exit;
}

$title = htmlspecialchars($row['title']);
$author = htmlspecialchars($row['author']);
$publisher = htmlspecialchars($row['publisher']);
$desc = $row['category']=='전공' ? htmlspecialchars($row['major']) : htmlspecialchars($row['subject']);
$price = number_format($row['selling_price']);
$origin_price = $row['original_price'] ? number_format($row['original_price']) : "-";
$publish_date = $row['publish_date'] ? htmlspecialchars($row['publish_date']) : "-";
$status = $row['status'];
$seller_name = htmlspecialchars($row['seller_name']);
$seller_id = $row['seller_id'];
$category = $row['category'];
$created_at = $row['created_at'];
$detail = nl2br(htmlspecialchars($row['description']));
$interest_count = $row['interest_count'];
// 판매자 정보
$seller_info = $conn->query("SELECT name, sell_count, buy_count FROM Users WHERE student_id='$seller_id'")->fetch_assoc();

// 대표사진 + 추가이미지
$images = [];
if ($row['image_path']) $images[] = $row['image_path'];
$res_img = $conn->query("SELECT image_path FROM BooksImages WHERE book_id = $book_id ORDER BY image_id ASC");
while($img = $res_img->fetch_assoc()) {
    if ($img['image_path'] && $img['image_path'] != $row['image_path']) $images[] = $img['image_path'];
}

// 좋아요(관심) 여부
function isLiked($user_id, $book_id){
    if(!$user_id || !$book_id) return false;
    $c = new mysqli("localhost", "root", "1234", "test");
    $q = $c->query("SELECT 1 FROM Interests WHERE student_id='$user_id' AND book_id=$book_id");
    return $q && $q->num_rows > 0;
}
function isLogin() { return isset($_SESSION['student_id']); }
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?> 상세정보 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    .detail-wrap { max-width:740px; margin:40px auto 60px auto; background:#fff; border-radius:17px; box-shadow:0 2px 15px rgba(222,182,123,0.07); padding:36px 38px; }
.detail-thumb {
  width: 270px;      /* 기존 170px → 270px, 더 키우고 싶으면 320px도 가능 */
  height: 350px;     /* 기존 210px → 350px, 380px까지도 가능 */
  object-fit: cover;
  border-radius: 12px;
  border: 2px solid #ffc093;
  background: #f7f4e5;
  cursor: pointer;   /* 클릭 가능해 보이게 */
  transition: box-shadow 0.18s;
}

.detail-thumb:hover {
  box-shadow: 0 4px 32px rgba(230,170,90,0.15);
}

.thumb-list {
  display: flex;
  gap: 10px;
  margin-top: 14px;
  overflow-x: auto;
  max-width: 320px;
  padding-bottom: 4px;
}
.thumb-list::-webkit-scrollbar { height: 7px; background: #ffeec4;}
.thumb-list::-webkit-scrollbar-thumb { background: #ffd699; border-radius: 7px;}

.thumb-list img {
  width: 62px;      /* 기존 40px → 62px */
  height: 78px;     /* 기존 46px → 78px */
  border-radius: 7px;
  border: 1.5px solid #ffd7b3;
  object-fit: cover;
  cursor: pointer;
  background: #f7f4e5;
  transition: box-shadow 0.18s, border 0.15s;
}
.thumb-list img:hover {
  border: 2px solid #ffb964;
  box-shadow: 0 2px 10px #ffe4bc5a;
}
.detail-row {
  display: flex;
  gap: 54px;
  flex-direction: row;  /* ★ 반드시 row! */
}

@media (max-width:900px){
  .detail-row{ flex-direction:column; gap:12px;}
}


    .detail-info { flex:1; min-width:0; }
    .detail-title-row { display:flex; align-items:center; gap:11px; margin-bottom:7px;}
    .detail-title { font-size:1.28em; font-weight:bold; color:#222;}
    .book-status { margin-left:12px;}
    .detail-meta { color:#5c5148; font-size:1.07em; margin-bottom:5px; }
    .detail-seller { font-size:1.03em; color:#565656; margin-bottom:5px; }
    .detail-price { color:#e36c09; font-size:1.13em; font-weight:bold; margin-bottom:7px;}
    .detail-origin-price { color:#555; font-size:1em; margin-bottom:7px; }
    .detail-publish-date { color:#555; font-size:1em; margin-bottom:7px;}
    .detail-interest { margin-bottom:13px; display:flex; align-items:center; gap:12px;}
    .heart-btn { font-size:21px;}
    .detail-desc { background:#FFF8EC; border-radius:9px; padding:18px 17px; color:#6b5122; font-size:1.05em; margin-top:19px; }
    .detail-date { margin-top:12px; color:#b7a085; font-size:0.97em;}

.detail-action-row { 
  margin-top: 21px;
  display: flex; 
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}
    /* =============== 후기/다른글 추가 박스 =============== */
    .info-section { max-width:740px; margin:40px auto 0 auto; }
    .review-box, .other-books-box {
      background:#FFF8EC; border-radius:9px; padding:18px 17px; margin-bottom:36px; color:#6b5122; box-shadow:0 2px 12px rgba(222,182,123,0.04);
    }
    .review-title, .other-books-title {
      font-weight:bold; color:#ac831b; font-size:1.13em; margin-bottom:12px;
      border-bottom: 1px solid #f2e4c5; padding-bottom:3px;
    }
    .review-item { border-bottom:1px solid #ebebeb; margin-bottom:10px; padding-bottom:8px;}
    .review-user { font-weight:bold; color:#ac831b;}
    .review-date { color:#b1a078; font-size:0.97em; margin-left:4px;}
    .review-content { color:#62431d; font-size:1.03em; margin-top:2px; display:inline-block;}
    /* ===== 판매자 다른글 ===== */
    .other-books-list { display:flex; gap:18px; align-items:flex-end; min-height:210px; }
    .other-book-thumb { width:162px; height:200px; border-radius:11px; box-shadow:0 1px 6px #e6d6bb; object-fit:cover; background:#f7f4e5; }
    .other-books-arrow { background:none; border:none; color:#c6ae85; font-size:26px; cursor:pointer; margin:0 5px; }
    /* ===== 판매완료하기 버튼 스타일 ===== */
    .book-sold-btn {
      background: #ff7e1b;
      color: #fff;
      border: none;
      border-radius: 7px;
      font-size: 1em;
      padding: 8px 15px;
      margin-left: 4px;
      font-family: inherit;
      cursor: pointer;
      transition: background 0.15s;
      vertical-align: middle;
      box-shadow:0 1px 3px #ffe3ba3a;
    }
    .book-sold-btn:hover { background: #ff6000; }
    @media (max-width:900px){
      .detail-wrap,.info-section{padding:14px 2vw;}
      .detail-row{flex-direction:column;gap:12px;}
      .other-book-thumb{width:98px;height:120px;}
    }
  </style>
</head>
<body>
<div class="detail-wrap">
<div class="detail-row">
  <!-- 왼쪽: 대표사진+썸네일 -->
  <div style="max-width:320px; min-width:230px; flex-shrink:0;">
    <img src="<?= htmlspecialchars($images[0]) ?>" id="mainImage" class="detail-thumb" alt="대표사진">
    <?php if(count($images)>1): ?>
      <div class="thumb-list">
        <?php foreach($images as $img): if (!$img) continue; ?>
          <img src="<?= htmlspecialchars($img) ?>" onclick="document.getElementById('mainImage').src='<?= htmlspecialchars($img) ?>'" alt="추가사진">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>


    <div class="detail-info">
      <div class="detail-title-row">
        <div class="detail-title"><?= $title ?></div>
        <span class="book-status <?= $status=='판매중'?'sale':'soldout' ?>">
          <?= $status=='판매중'?'판매중':'판매완료' ?>
        </span>
      </div>
      <div class="detail-meta"><?= $author ?> 지음 | <?= $publisher ?></div>
      <div class="detail-meta"><?= $category=='전공' ? $desc : htmlspecialchars($row['subject']) ?></div>
      <div class="detail-origin-price">정가: <?= $origin_price ?>원</div>
      <div class="detail-price">판매가 <?= $price ?>원</div>
      <div class="detail-publish-date">출판일: <?= $publish_date ?></div>
      <div class="detail-seller">
        <i class="fa fa-user"></i>
        <?= $seller_name ?>
        <span style="font-size:0.97em;color:#b19050;">(판매 <?= $seller_info['sell_count'] ?>, 구매 <?= $seller_info['buy_count'] ?>)</span>
      </div>
      <div class="detail-interest">
        <button class="heart-btn<?= ($isLogin && isLiked($my_id, $book_id)) ? ' liked' : '' ?>" data-book-id="<?= $book_id ?>">
          <i class="fa fa-heart"></i>
          <span class="interest-count"><?= $interest_count ?></span>
        </button>
      </div>
      <div class="detail-date">등록일: <?= $created_at ?></div>
      <div class="detail-action-row">
        <?php if($isLogin && $my_id == $seller_id): ?>
          <a href="edit_book.php?book_id=<?= $book_id ?>"><button class="book-edit-btn">글 수정</button></a>
          <a href="delete_book.php?id=<?= $book_id ?>" onclick="return confirm('정말 삭제하시겠습니까?')"><button class="book-delete-btn">글 삭제</button></a>
          <?php if($status=='판매중'): ?>
            <a href="finish_sale_select.php?book_id=<?= $book_id ?>"><button class="book-sold-btn">판매완료하기</button></a>
          <?php else: ?>
            <span class="book-sold-label" style="color:#c68a26;font-weight:bold;margin-left:6px;">[판매완료]</span>
          <?php endif; ?>
        <?php elseif($isLogin && $my_id != $seller_id && $status=='판매중'): ?>
          <a href="chat.php?book_id=<?= $book_id ?>&seller_id=<?= $seller_id ?>">
            <button class="chat-btn">채팅하기</button>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="detail-desc"><?= $detail ?></div>
</div>

<!-- 후기 박스 (하얀 박스) -->
<div class="info-section">
  <div class="review-box">
    <div class="review-title"><i class="fa fa-comment-dots"></i> 후기</div>
    <?php
    $res = $conn->query("
      SELECT R.*, U.name 
      FROM Reviews R JOIN Users U ON R.buyer_id=U.student_id
      WHERE R.book_id = $book_id
      ORDER BY R.created_at DESC
    ");
    if ($res->num_rows) {
      while($row = $res->fetch_assoc()){
        echo "<div class='review-item'>";
        echo "<span class='review-user'>".htmlspecialchars($row['name'])."</span>";
        echo "<span class='review-date'>(".date('Y.m.d H:i',strtotime($row['created_at'])).")</span><br>";
        echo "<span class='review-content'>".htmlspecialchars($row['content'])."</span>";
        echo "</div>";
      }
    } else {
      echo "<div style='color:#c6ae85;'>아직 후기가 없습니다.</div>";
    }
    ?>
  </div>
</div>

<!-- 판매자의 다른 글 박스 (하얀 박스 + 화살표) -->
<div class="info-section">
  <div class="other-books-box">
    <div class="other-books-title"><i class="fa fa-book"></i> 판매자의 다른 글</div>
    <div class="other-books-list" id="otherBooksList">
      <!-- 썸네일 JS로 출력 -->
    </div>
    <div style="text-align:center; margin-top:12px;">
      <button class="other-books-arrow" id="prevArrow"><i class="fa fa-chevron-left"></i></button>
      <button class="other-books-arrow" id="nextArrow"><i class="fa fa-chevron-right"></i></button>
    </div>
  </div>
</div>

<script src="script.js"></script>
<script>
// ===== 판매자의 다른 글 JS (최대 4개, 화살표 슬라이드) =====
let books = [
<?php
$res = $conn->query("SELECT * FROM Books WHERE seller_id='$seller_id' AND book_id<>$book_id AND status='판매중' ORDER BY created_at DESC");
$allBooks = [];
while($b = $res->fetch_assoc()) $allBooks[] = $b;
foreach($allBooks as $b) {
    echo "{id:".$b['book_id'].",img:'".htmlspecialchars($b['image_path']?:'noimage.png')."'},";
}
?>
];
let currentPage = 0;
const pageSize = 4;

function renderOtherBooks() {
  let list = document.getElementById('otherBooksList');
  list.innerHTML = '';
  if(books.length === 0) {
    list.innerHTML = "<span style='color:#c6ae85;'>해당 판매자의 다른 판매글이 없습니다.</span>";
    document.getElementById('prevArrow').style.display = "none";
    document.getElementById('nextArrow').style.display = "none";
    return;
  }
  let start = currentPage * pageSize;
  let end = Math.min(start + pageSize, books.length);
  for(let i=start;i<end;i++) {
    let b = books[i];
    let a = document.createElement('a');
    a.href = 'book_detail.php?id='+b.id;
    let img = document.createElement('img');
    img.src = b.img;
    img.className = 'other-book-thumb';
    img.alt = '썸네일';
    a.appendChild(img);
    list.appendChild(a);
  }
  document.getElementById('prevArrow').disabled = currentPage === 0;
  document.getElementById('nextArrow').disabled = end >= books.length;
}
if(document.getElementById('otherBooksList')) renderOtherBooks();
document.getElementById('prevArrow').onclick = function(){ if(currentPage>0){ currentPage--; renderOtherBooks(); }};
document.getElementById('nextArrow').onclick = function(){
  if((currentPage+1)*pageSize < books.length){ currentPage++; renderOtherBooks(); }
};
</script>
</body>
</html>
