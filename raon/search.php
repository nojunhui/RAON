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
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>검색결과 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
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
<!-- ===================== 상단바 (index와 100% 동일하게) ======================= -->


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
