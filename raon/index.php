<?php
session_start();
$isLogin = isset($_SESSION['student_id']);
$name = $isLogin ? $_SESSION['name'] : null;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>RAON 교재 거래</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="topnav">
  <div class="logo" onclick="location.href='index.php'">RAON</div>
<div class="lion-group">
  <img src="/raon/uploads/lion_group.png" alt="사자들" />
</div>
  <!-- 가운데 정렬용 wrapper -->
  <div class="topnav-center-wrap">
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
    <!-- 여기에 사용자 이름 및 버튼들 -->
    <div class="auth-btns">
      <?php if ($isLogin): ?>
        <span class="username"><?=$name?> 님</span>
        <a href="post_book.html"><button>교재 판매</button></a>
        <a href="mypage.php"><button>마이페이지</button></a>
        <a href="logout.php?goindex=1"><button>로그아웃</button></a>
      <?php else: ?>
        <a href="register.html"><button>회원가입</button></a>
        <a href="login.html"><button>로그인</button></a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="main-content">
  <!-- 오늘 새로 올라온 책 -->
  <div class="new-books-box">
    <div class="new-books-title"><i class="fa fa-clock"></i> 오늘 새로 올라온 책</div>
    <div class="book-list-row">
    <?php
      $conn = new mysqli("localhost", "root", "1234", "test");
      $new_sql = "SELECT * FROM Books ORDER BY created_at DESC LIMIT 5";
      $new_result = $conn->query($new_sql);
      while($row = $new_result->fetch_assoc()) {
        $book_id = $row['book_id'];
        $img = $row['image_path'];
        $interest_count = $row['interest_count'];
        $desc = $row['category']=='전공' ? $row['major'] : $row['subject'];
        $status = $row['status'];
        $is_liked = false;
        if ($isLogin) {
          $sid = $_SESSION['student_id'];
          $q = $conn->query("SELECT 1 FROM Interests WHERE student_id='$sid' AND book_id=$book_id");
          if ($q && $q->num_rows>0) $is_liked = true;
        }
echo "<div class='book-card'>";
echo "<a href='book_detail.php?id=$book_id'><img class='book-thumb' src='$img'></a>";

// ★ 여기 book-title-row 내에만 제목 + 상태 박스를!
echo "<div class='book-title-row'>";
echo "<div class='book-title'>" .
     (mb_strlen($row['title']) > 5
         ? htmlspecialchars(mb_substr($row['title'],0,5)) . '...'
         : htmlspecialchars($row['title'])) .
     "</div>";
if ($status == '판매중') {
  echo '<span class="book-status sale">판매중</span>';
} else {
  echo '<span class="book-status soldout">판매완료</span>';
}
echo "</div>"; // book-title-row

echo "<div class='book-meta'>".htmlspecialchars($desc)."</div>";
echo "<div class='book-footer'>";
echo "<button class='heart-btn".($is_liked?' liked':'')."' data-book-id='$book_id'>";
echo "<i class='fa fa-heart'></i> <span class='interest-count'>$interest_count</span>";
echo "</button>";
echo "<span class='book-price'>".number_format($row['selling_price'])."원</span>";
echo "</div></div>";
      }
    ?>
    </div>
  </div>

<div class="major-book-top-box">
  <div class="major-book-top-title">
    <i class="fa-solid fa-book"></i>
    <b>지금 많이 거래되는 전공책 TOP</b>
  </div>
  <div class="major-book-top-list">
    <?php
      // PHP 코드는 기존처럼 사용!
    $rank_sql = "
  SELECT title, COUNT(*) AS cnt 
  FROM Books 
  WHERE category = '전공' AND status = '판매완료'
  GROUP BY title
  ORDER BY cnt DESC
  LIMIT 5
";
$rank_result = $conn->query($rank_sql);
$rankings = [];
while ($row = $rank_result->fetch_assoc()) $rankings[] = $row;
for ($i = 0; $i < 5; $i++):
  $rank = $i + 1;
  $book = $rankings[$i] ?? null;
  $itemClass = $book ? ($i < 3 ? 'highlight' : 'subtle') : 'empty';
?>
  <div class="major-book-top-item <?= $itemClass ?>">
    <span class="rank-label">TOP <?= $rank ?></span>
    <span class="book-title"><?= $book ? htmlspecialchars($book['title']) : '' ?></span>
    <span class="book-major"><?= $book ? ($book['cnt'] . "회") : '' ?></span>
  </div>
<?php endfor; ?>
  </div>
</div>

<div class="bottom-row">
  <!-- 중고 거래 팁 박스 -->
  <div class="tip-box">
    <div class="tip-title">💡 중고 거래 판매 TIP!</div>
    <div class="tips-area">
      <ol class="tips">
        <li>사진은 3장 이상! ( 표지 · 옆면 · 사용감 보여 주기 ) * 실제 사진만 사용하기 </span></li>
        <li>설명에 학기 · 상태 · 거래 방식 적기 [ 예: “2025-1학기 / 필기 없음 / 직거래” ] </span>
        </li>
        <li>적정 가격은 신간가의 50 ~ 70%</li>
        <li>거래는 학교 인증 사용자와! ( 사기 예방 및 안전한 거래 )</span>
        <li>책 내 이름 / 학번 등 개인정보 가리기 ( 사진에 개인정보가 노출되지 않도록 주의! )</li></span>


        </li>
      </ol>
    </div>
  </div>
  <!-- 교과과정 바로가기 박스 -->
  <div class="go-curriculum">
    <b>교과과정 바로 가기</b><br>
    <a href="https://haksa.gwangju.ac.kr/~op/sugang/gyogwa/newgyogwa20251.php3" target="_blank">
      <img src="/raon/uploads/logo.jpg" style="margin-top:12px;width:120px;">
    </a>
  </div>
</div>
  </div>
</div>
<footer>© RAON</footer>
<script src="script.js"></script>
</body>
</html>
