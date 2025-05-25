<?php
session_start();
$isLogin = isset($_SESSION['student_id']);
$name = $isLogin ? $_SESSION['name'] : null;
$category = $_GET['category'] ?? '';
$grade = $_GET['grade'] ?? '';
$major = $_GET['major'] ?? '';
$subject = $_GET['subject'] ?? '';
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 5;
$start = ($page-1)*$perPage;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>검색결과 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<!-- ===================== 상단바 (index와 100% 동일하게) ======================= -->
<div class="topnav">
  <div class="logo" onclick="location.href='index.php'">RAON</div>
  <div class="category-bar">
    <button id="btn-major" class="category-btn">전공</button>
    <button id="btn-liberal" class="category-btn">교양</button>
  </div>
  <form id="searchForm" class="search-bar" method="get" action="search.php" autocomplete="off">
    <input type="hidden" name="category" id="searchCategory" value="<?=htmlspecialchars($category)?>">
    <div id="major-filter" class="filter-group" style="display:none;position:relative;">
      <select id="gradeSelect" class="filter-sel">
        <option value="">전체</option>
        <option value="1" <?=($grade=="1"?"selected":"")?>>1학년</option>
        <option value="2" <?=($grade=="2"?"selected":"")?>>2학년</option>
        <option value="3" <?=($grade=="3"?"selected":"")?>>3학년</option>
        <option value="4" <?=($grade=="4"?"selected":"")?>>4학년</option>
        <option value="5" <?=($grade=="5"?"selected":"")?>>5학년</option>
      </select>
      <input type="hidden" id="searchGrade" name="grade" value="<?=htmlspecialchars($grade)?>">
      <!-- 2단 학과 드롭다운 -->
      <div id="majorSelectBtn" class="filter-sel" style="width:170px;position:relative;user-select:none;cursor:pointer;">
        <?= $major ? htmlspecialchars($major) : "전체" ?>
      </div>
      <input type="hidden" id="selectedMajor" name="major" value="<?=htmlspecialchars($major)?>">
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
        <option value="호심교양" <?=($subject=="호심교양"?"selected":"")?>>호심교양</option>
        <option value="균형교양" <?=($subject=="균형교양"?"selected":"")?>>균형교양</option>
      </select>
      <input type="hidden" id="searchSubject" name="subject" value="<?=htmlspecialchars($subject)?>">
    </div>
    <input type="text" name="search" class="search-input" value="<?=htmlspecialchars($search)?>" placeholder="검색어를 입력해 주세요.">
    <button type="submit" class="search-btn">검색</button>
  </form>
  <div class="auth-btns">
    <?php if ($isLogin): ?>
      <span class="username"><?=$name?>님</span>
      <a href="post_book.html"><button>교재 판매</button></a>
      <a href="mypage.php"><button>마이페이지</button></a>
      <a href="logout.php?goindex=1"><button>로그아웃</button></a>
    <?php else: ?>
      <a href="register.html"><button>회원가입</button></a>
      <a href="login.html"><button>로그인</button></a>
    <?php endif; ?>
  </div>
</div>

<!-- ===================== 검색 결과 ======================= -->
<div class="result-wrapper">
  <div class="result-container">
    <div class="result-title">'<?=htmlspecialchars($search)?>' 검색 결과</div>
    <div class="search-results-list">
<?php
$conn = new mysqli("localhost", "root", "1234", "test");
$where = "1=1";
if ($category == "전공")       $where .= " AND category='전공'";
else if ($category == "교양") $where .= " AND category='교양'";
if ($category=="전공" && $grade) $where .= " AND grade='". $conn->real_escape_string($grade) ."'";
if ($category=="전공" && $major && $major != "" && strpos($major,"전체")===false) $where .= " AND major='". $conn->real_escape_string($major) ."'";
if ($category=="교양" && $subject && $subject != "") $where .= " AND subject='". $conn->real_escape_string($subject) ."'";
if ($search) $where .= " AND (title LIKE '%".$conn->real_escape_string($search)."%' OR author LIKE '%".$conn->real_escape_string($search)."%' OR publisher LIKE '%".$conn->real_escape_string($search)."%')";
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM Books WHERE $where ORDER BY created_at DESC LIMIT $start,$perPage";
$result = $conn->query($sql);
$total = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
if ($result->num_rows == 0) {
  echo "<div>검색 결과가 없습니다.</div>";
} else {
  $rank = $start+1;
  while($row = $result->fetch_assoc()) {
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
?>
      <div class="result-item">
        <div class="result-rank"><?=$rank++?>.</div>
        <a href="book_detail.php?id=<?=$book_id?>"><img src="<?=$img?>" class="result-thumb"></a>
        <div class="result-info">
          <div class="result-title-txt"><?=htmlspecialchars($row['title'])?>
            <?php if ($status=='판매중'): ?>
              <span class="book-status sale">판매중</span>
            <?php else: ?>
              <span class="book-status soldout">판매완료</span>
            <?php endif; ?>
          </div>
          <div class="result-meta"><?=htmlspecialchars($row['author'])?> 지음 | <?=htmlspecialchars($row['publisher'])?></div>
          <div class="result-major"><?=htmlspecialchars($desc)?></div>
        </div>
        <div style="text-align:right;">
          <div class="result-interest">
            <button class="result-heart-btn<?=$is_liked?' liked':''?>" data-book-id="<?=$book_id?>">
              <i class="fa fa-heart"></i>
              <span class="interest-count"><?=$interest_count?></span>
            </button>
          </div>
          <div class="result-price">판매가 <b><?=number_format($row['selling_price'])?>원</b></div>
        </div>
      </div>
<?php } } ?>
    </div>
    <!-- 페이지네이션 -->
    <div class="result-pagenation">
      <?php
        $qstr = "&category=".urlencode($category)."&grade=".urlencode($grade)."&major=".urlencode($major)."&subject=".urlencode($subject)."&search=".urlencode($search);
        $pages = ceil($total/$perPage);
        for($i=1; $i<=$pages; $i++){
          $cls = $i==$page ? "result-page-btn active" : "result-page-btn";
          echo "<a href='search.php?page=$i$qstr'><button class='$cls'>$i</button></a>";
        }
      ?>
    </div>
  </div>
</div>
<script src="script.js"></script>
</body>
</html>
