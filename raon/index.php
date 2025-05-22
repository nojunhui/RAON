<?php
session_start();
if (!isset($_SESSION['student_id'])) {
  header("Location: login.html"); exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>교재 거래 메인</title>
  <style>
    .dropdown-multilevel { position: relative; display: inline-block; }
    .dropdown-btn {
      padding: 8px 16px;
      border: 1px solid #bbb; background: #fff; cursor: pointer;
      min-width: 140px;
    }
    .dropdown-content, .sub-menu, .sub-sub-menu {
      display: none;
      position: absolute; background: #fff; border: 1px solid #ccc; min-width: 160px; z-index: 1000;
    }
    .dropdown-content { left: 0; top: 100%; }
    .sub-menu { left: 100%; top: 0; }
    .sub-sub-menu { left: 100%; top: 0; }
    .dropdown-multilevel:hover .dropdown-content { display: block; }
    .dropdown-content > .menu-item:hover > .sub-menu { display: block; }
    .sub-menu > .sub-item:hover > .sub-sub-menu { display: block; }
    .menu-item, .sub-item, .sub-sub-item {
      padding: 8px 16px; cursor: pointer; white-space: nowrap;
    }
    .menu-item:hover, .sub-item:hover, .sub-sub-item:hover { background: #f0f0f0; }
    .book-row {
      display: flex; align-items: center; justify-content: space-between;
      border-bottom: 1px solid #ddd; padding: 10px 0;
    }
    .book-info {
      display: flex; align-items: center;
    }
    .book-img {
      height: 80px; width: 80px; object-fit: cover;
      margin-right: 10px; border: 1px solid #eee; background: #fafafa;
    }
    .book-title {
      font-weight: bold; font-size: 1.15em;
      color: #272957;
      text-decoration: none;
    }
    .interest-btn {
      background: #f7e8da; border: 1px solid #e36c09; color: #e36c09;
      font-weight: bold; padding: 4px 10px; border-radius: 16px; cursor: pointer;
      margin-right: 5px;
    }
    .seller-name { font-size: 0.97em; color: #444; margin-right: 15px;}
    .price-tag { font-weight: bold; color: #e36c09; font-size: 1.1em;}
    #gradeSection { display:none; margin: 10px 0 0 0; }
    #gradeSelect { padding:3px 6px; }
  </style>
</head>
<body>
  <h2>안녕하세요, <?=$_SESSION['name']?>님</h2>
  <a href="post_book.html">교재 등록</a> | 
  <a href="mypage.php">마이페이지</a> | 
  <a href="logout.php">로그아웃</a>
  <form action="index.php" method="get" id="searchForm" style="margin-top:20px;">
    <div class="dropdown-multilevel" id="categoryDropdown">
      <div class="dropdown-btn" id="categoryBtn">카테고리 선택</div>
      <div class="dropdown-content" id="dropdownMenu">
        <div class="menu-item" data-cat="전공">전공
          <div class="sub-menu">
            <div class="sub-item" data-college="공과대학">공과대학
              <div class="sub-sub-menu">
                <div class="sub-sub-item" data-major="컴퓨터공학과">컴퓨터공학과</div>
                <div class="sub-sub-item" data-major="건축학과">건축학과</div>
              </div>
            </div>
            <div class="sub-item" data-college="보건복지대학">보건복지대학
              <div class="sub-sub-menu">
                <div class="sub-sub-item" data-major="간호학과">간호학과</div>
                <div class="sub-sub-item" data-major="언어치료학과">언어치료학과</div>
              </div>
            </div>
          </div>
        </div>
        <div class="menu-item" data-cat="교양">교양
          <div class="sub-menu">
            <div class="sub-item" data-subject="호심교양">호심교양</div>
            <div class="sub-item" data-subject="균형교양">균형교양</div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="category" id="categoryInput">
    <input type="hidden" name="college" id="collegeInput">
    <input type="hidden" name="major" id="majorInput">
    <input type="hidden" name="subject" id="subjectInput">
    <input type="hidden" name="grade" id="gradeInput">
    <input type="text" name="search" placeholder="책 제목, 저자" value="<?=htmlspecialchars($_GET['search']??'')?>">
    <span id="gradeSection">
      <select name="grade" id="gradeSelect">
        <option value="">학년 선택</option>
        <option value="1">1학년</option>
        <option value="2">2학년</option>
        <option value="3">3학년</option>
        <option value="4">4학년</option>
      </select>
    </span>
    <input type="submit" value="검색">
  </form>
  <script>
  // 전공-학과 선택
  document.querySelectorAll('.sub-sub-item').forEach(item => {
    item.onclick = function(e) {
      e.stopPropagation();
      document.getElementById('categoryInput').value = '전공';
      document.getElementById('collegeInput').value = this.parentNode.parentNode.dataset.college;
      document.getElementById('majorInput').value = this.dataset.major;
      document.getElementById('subjectInput').value = '';
      document.getElementById('categoryBtn').textContent = `전공 > ${this.parentNode.parentNode.dataset.college} > ${this.dataset.major}`;
      document.getElementById('gradeSection').style.display = 'inline-block';
    }
  });
  // 교양-분야 선택
  document.querySelectorAll('.sub-item[data-subject]').forEach(item => {
    item.onclick = function(e) {
      e.stopPropagation();
      document.getElementById('categoryInput').value = '교양';
      document.getElementById('collegeInput').value = '';
      document.getElementById('majorInput').value = '';
      document.getElementById('subjectInput').value = this.dataset.subject;
      document.getElementById('categoryBtn').textContent = `교양 > ${this.dataset.subject}`;
      document.getElementById('gradeSection').style.display = 'none';
      document.getElementById('gradeSelect').value = '';
      document.getElementById('gradeInput').value = '';
    }
  });
  // grade 선택하면 hidden에도 반영
  document.getElementById('gradeSelect').onchange = function() {
    document.getElementById('gradeInput').value = this.value;
  };
  // 페이지 새로고침시 기존 선택값 유지(gradeSection)
  window.onload = function() {
    if(document.getElementById('categoryInput').value === '전공') {
      document.getElementById('gradeSection').style.display = 'inline-block';
    }
  };
  </script>
  <hr>
  <?php
  $category = $_GET['category'] ?? '';
  $college  = $_GET['college'] ?? '';
  $major    = $_GET['major'] ?? '';
  $subject  = $_GET['subject'] ?? '';
  $grade    = $_GET['grade'] ?? '';
  $search   = $_GET['search'] ?? '';

  $conn = new mysqli("localhost", "root", "1234", "test");
  $where = "1=1";
  if ($category) $where .= " AND category='$category'";

  // 전공: major/college/grade, 교양: subject
  if ($category == "전공") {
    if ($major) $where .= " AND major='$major'";
    if ($college) $where .= " AND college='$college'";
    if ($grade) $where .= " AND grade='$grade'";
  }
  if ($category == "교양") {
    if ($subject) $where .= " AND subject='$subject'";
  }
  // 검색(모든 책 공통)
  if ($search) $where .= " AND (title LIKE '%$search%' OR author LIKE '%$search%')";

  $sql = "SELECT * FROM Books WHERE $where ORDER BY created_at DESC";
  $result = $conn->query($sql);

  echo "<hr>";
  if ($result->num_rows == 0) {
    echo "<div>검색 결과가 없습니다.</div>";
  }
  while ($row = $result->fetch_assoc()) {
    // 판매자 이름 조회
    $seller_id = $row['seller_id'];
    $user_result = $conn->query("SELECT name FROM Users WHERE student_id='$seller_id'");
    $user = $user_result->fetch_assoc();
    $seller_name = $user['name'] ?? '알 수 없음';

    // 대표 이미지 (없으면 기본 이미지 표시)
    $img_tag = $row['image_path'] && file_exists($row['image_path']) ?
      "<img src='{$row['image_path']}' alt='책사진' class='book-img'>" :
      "<div style='display:inline-block;width:80px;height:80px;background:#eee;margin-right:10px;vertical-align:middle;'></div>";

    echo "<div class='book-row'>";
    echo "<div class='book-info'>";
    echo $img_tag;
    echo "<div>";
    echo "<a href='book_detail.php?id={$row['book_id']}' class='book-title'>{$row['title']}</a><br>";
    echo "{$row['author']}";
    echo "</div></div>";
    echo "<div style='display:flex;align-items:center;gap:10px;'>";
    echo "<span class='seller-name'>{$seller_name}</span>";
    echo "<form action='interest.php' method='post' style='display:inline; margin:0;'>
            <input type='hidden' name='book_id' value='{$row['book_id']}'>
            <button type='submit' class='interest-btn'>♥ 관심</button>
          </form>";
    echo "<span class='price-tag'>{$row['selling_price']}원</span>";
    echo "</div>";
    echo "</div>";
  }
  $conn->close();
  ?>
</body>
</html>
