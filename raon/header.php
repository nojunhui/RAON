<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isLogin = isset($_SESSION['student_id']);
$name = $isLogin ? $_SESSION['name'] : null;
?>
<!-- 헤더 스타일 바로 여기서 선언 (모든 페이지 공통적용 가능) -->
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

<div class="topnav">
  <div class="logo" onclick="location.href='index.php'">RAON</div>
  <div class="category-bar" style="flex-shrink:0;">
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
    <input type="text" class="search-input raon-input" placeholder="검색어를 입력해 주세요.">
    <button type="submit" class="search-btn">검색</button>
  </form>
  <div class="auth-btns">
    <?php if ($isLogin): ?>
      <span class="username"><?=$name?> 님</span>
      <a href="post_book.html"><button type="button">교재 판매</button></a>
      <a href="mypage.php"><button type="button">마이페이지</button></a>
      <a href="logout.php?goindex=1"><button type="button">로그아웃</button></a>
    <?php else: ?>
      <a href="register.html"><button type="button">회원가입</button></a>
      <a href="login.html"><button type="button">로그인</button></a>
    <?php endif; ?>
  </div>
</div>

<script>
  // 검색 input에 placeholder 포커스 시 사라지고, blur 시 내용 없으면 복귀
  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    const oriPlaceholder = searchInput.placeholder;
    searchInput.addEventListener('focus', () => searchInput.placeholder = '');
    searchInput.addEventListener('blur', () => {
      if (!searchInput.value) searchInput.placeholder = oriPlaceholder;
    });
  }
</script>