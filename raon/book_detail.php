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

// 책 정보 불러오기 (Users와 조인, 대소문자 일치)
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

// 대표사진 + 추가이미지
$images = [];
// 대표사진(Books 테이블)
if ($row['image_path']) $images[] = $row['image_path'];
// 추가이미지(BooksImages 테이블)
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
function isLogin() {
    return isset($_SESSION['student_id']);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?> 상세정보 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    .detail-wrap { max-width:740px; margin:40px auto 60px auto; background:#fff; border-radius:17px; box-shadow:0 2px 15px rgba(222,182,123,0.07); padding:36px 38px; }
    .detail-row { display: flex; gap: 32px; }
    .detail-thumb { width:170px; height:210px; object-fit:cover; border-radius:8px; border:1px solid #ffc093;}
    .thumb-list { display:flex; gap:7px; margin-top:11px; }
    .thumb-list img { width:40px; height:46px; border-radius:5px; border:1px solid #eee; object-fit:cover; cursor:pointer;}
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
    .detail-action-row { margin-top:21px; }
  </style>
</head>
<body>
<!-- ==== index.php와 동일한 상단바 + 필터, 검색, 버튼 ==== -->
<div class="topnav">
  <div class="logo" onclick="location.href='index.php'">RAON</div>
  <div class="category-bar">
    <button id="btn-major" class="category-btn">전공</button>
    <button id="btn-liberal" class="category-btn">교양</button>
  </div>
  <form id="searchForm" class="search-bar" method="get" action="search.php" autocomplete="off">
    <input type="hidden" name="category" id="searchCategory">
    <div id="major-filter" class="filter-group" style="display:none;position:relative;">
      <select id="gradeSelect" class="filter-sel">
        <option value="">전체</option>
        <option value="1">1학년</option>
        <option value="2">2학년</option>
        <option value="3">3학년</option>
        <option value="4">4학년</option>
        <option value="5">5학년</option>
      </select>
      <input type="hidden" id="searchGrade" name="grade">
      <div id="majorSelectBtn" class="filter-sel" style="width:170px;position:relative;user-select:none;cursor:pointer;">전체</div>
      <input type="hidden" id="selectedMajor" name="major">
      <div id="majorDropdown" style="display:none;position:absolute;z-index:999;background:#fff;box-shadow:0 2px 7px rgba(0,0,0,0.14);border-radius:7px;padding:10px 0;min-width:350px;top:36px;">
        <div style="display:flex;">
          <div id="collegeList" style="min-width:120px;border-right:1px solid #f0c6a7;padding:0 8px;"></div>
          <div id="deptList" style="min-width:180px;padding:0 8px;"></div>
        </div>
      </div>
    </div>
    <div id="liberal-filter" class="filter-group" style="display:none;">
      <select id="liberalType" class="filter-sel">
        <option value="">전체</option>
        <option value="호심교양">호심교양</option>
        <option value="균형교양">균형교양</option>
      </select>
      <input type="hidden" id="searchSubject" name="subject">
    </div>
    <input type="text" name="search" class="search-input" placeholder="검색어를 입력해 주세요.">
    <button type="submit" class="search-btn">검색</button>
  </form>
  <div class="auth-btns">
    <?php if ($isLogin): ?>
      <span class="username"><?=$seller_name?>님</span>
      <a href="post_book.html"><button>교재 판매</button></a>
      <a href="mypage.php"><button>마이페이지</button></a>
      <a href="logout.php?goindex=1"><button>로그아웃</button></a>
    <?php else: ?>
      <a href="register.html"><button>회원가입</button></a>
      <a href="login.html"><button>로그인</button></a>
    <?php endif; ?>
  </div>
</div>
<!-- ==== 상세 정보 ==== -->
<div class="detail-wrap">
  <div class="detail-row">
    <div>
      <img src="<?= htmlspecialchars($images[0]) ?>" id="mainImage" class="detail-thumb" alt="대표사진">
      <?php if(count($images)>1): ?>
      <div class="thumb-list">
        <?php foreach($images as $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" onclick="document.getElementById('mainImage').src='<?= htmlspecialchars($img) ?>'">
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
      <div class="detail-seller"><i class="fa fa-user"></i> <?= $seller_name ?></div>
      <div class="detail-interest">
        <button class="heart-btn<?= ($isLogin && isLiked($my_id, $book_id)) ? ' liked' : '' ?>" data-book-id="<?= $book_id ?>">
          <i class="fa fa-heart"></i>
          <span class="interest-count"><?= $interest_count ?></span>
        </button>
      </div>
      <div class="detail-date">등록일: <?= $created_at ?></div>
      <div class="detail-action-row">
        <?php if($isLogin && $my_id == $seller_id): ?>
          <a href="edit_book.php?id=<?= $book_id ?>"><button class="book-edit-btn">글 수정</button></a>
          <a href="delete_book.php?id=<?= $book_id ?>" onclick="return confirm('정말 삭제하시겠습니까?')"><button class="book-delete-btn">글 삭제</button></a>
        <?php elseif($isLogin && $my_id != $seller_id): ?>
          <a href="chat.php?book_id=<?= $book_id ?>&seller_id=<?= $seller_id ?>">
            <button class="chat-btn">채팅하기</button>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="detail-desc"><?= $detail ?></div>
</div>
<script src="script.js"></script>
</body>
</html>
