<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isLogin = isset($_SESSION['student_id']);
$name = $isLogin ? $_SESSION['name'] : null;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>RAON</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* 헤더 레이아웃 핵심 수정 */
    .topnav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #FFECC2;
      padding: 12px 24px 7px 24px;
      width: 100%;
      min-width: 0;
      box-sizing: border-box;
    }
    .logo {
      font-size: 2.2em;
      font-weight: bold;
      color: #a5753f;
      margin-right: 18px;
      letter-spacing: 2px;
      cursor: pointer;
      flex-shrink: 0;
    }
    .topnav-center-wrap {
      display: flex;
      align-items: center;
      flex: 1 1 0%;
      min-width: 0;
      gap: 14px;
    }
    .category-bar {
      display: flex;
      gap: 6px;
      flex-shrink: 0;
    }
    .search-bar {
      flex: 1 1 0;
      max-width: 420px;
      min-width: 120px;
      display: flex;
      align-items: center;
      margin-left: 16px;
    }
    .search-input {
      flex: 1 1 0;
      min-width: 0;
      max-width: 240px;
      border: 1.5px solid #a5753f;
      border-radius: 12px;
      font-size: 1em;
      padding: 10px 16px;
      background: #fff;
      height: 38px;
      box-sizing: border-box;
      outline: none;
    }
    .search-btn {
      background: #ffcd99;
      color: #fff;
      border-radius: 12px;
      font-size: 1.08em;
      font-weight: bold;
      padding: 0 231px;
      height: 38px;
      border: none;
      white-space: nowrap;
      display: flex;
      align-items: center;
      justify-content: center;
      box-sizing: border-box;
      margin-left: 7px;
      outline: none;
      transition: background 0.15s;
      cursor: pointer;
    }
    .auth-btns {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-left: 18px;
      flex-shrink: 0;
      font-size : 15px;
    }
    .auth-btns button {
      background: #fff;
      border: 1.3px solid #ffd7b3;
      color: #ad854c;
      border-radius: 8px;
      font-size: 1.01em;
      font-weight: bold;
      padding: 7px 15px;
      cursor: pointer;
      transition: background 0.12s, color 0.12s;
    }
    .auth-btns button:hover {
      background: #FFE4CC;
      color: #c87d2e;
    }
    .username {
      font-weight: bold;
      color: #222;
      margin-right: 8px;
      font-size: 1.04em;
      padding-top: 7px;
      display: inline-block;
    }
    /* 반응형 */
    @media (max-width: 950px) {
      .topnav { flex-wrap: wrap; padding: 7px 2vw; }
      .topnav-center-wrap { flex-basis: 100%; justify-content: center; margin: 7px 0; }
      .auth-btns { margin-left: 0; }
    }
  </style>
</head>
<body>
<div class="topnav">
  <div class="logo" onclick="location.href='index.php'">RAON</div>
  <div class="topnav-center-wrap">
    <div class="category-bar">
      <button id="btn-major" class="category-btn">전공</button>
      <button id="btn-liberal" class="category-btn">교양</button>
    </div>
    <form id="searchForm" class="search-bar" method="get" action="search.php" autocomplete="off">
      <input type="hidden" name="category" id="searchCategory">
      <!-- 전공필터 -->
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
        <div id="majorSelectBtn" class="filter-sel" style="width:120px;position:relative;user-select:none;cursor:pointer;">전체</div>
        <input type="hidden" id="selectedMajor" name="major">
        <div id="majorDropdown" style="display:none;position:absolute;z-index:999;background:#fff;box-shadow:0 2px 7px rgba(0,0,0,0.14);border-radius:7px;padding:10px 0;min-width:240px;top:36px;">
          <div style="display:flex;">
            <div id="collegeList" style="min-width:90px;border-right:1px solid #f0c6a7;padding:0 8px;"></div>
            <div id="deptList" style="min-width:120px;padding:0 8px;"></div>
          </div>
        </div>
      </div>
      <!-- 교양필터 -->
      <div id="liberal-filter" class="filter-group" style="display:none;">
        <select id="liberalType" class="filter-sel">
          <option value="">전체</option>
          <option value="호심교양">호심교양</option>
          <option value="균형교양">균형교양</option>
        </select>
        <input type="hidden" id="searchSubject" name="subject">
      </div>
      <input type="text" class="search-input raon-input" name="keyword" id="keywordInput" placeholder="검색어를 입력해 주세요.">
      <button type="submit" class="search-btn">검색</button>
    </form>
  </div>
  <div class="auth-btns">
    <?php if ($isLogin): ?>
      <span class="username"><?=$name?> 님</span>
      <a href="post_book.html"><button type="button">교재 등록</button></a>
      <a href="mypage.php"><button type="button">마이페이지</button></a>
      <a href="logout.php?goindex=1"><button type="button">로그아웃</button></a>
    <?php else: ?>
      <a href="register.html"><button type="button">회원가입</button></a>
      <a href="login.html"><button type="button">로그인</button></a>
    <?php endif; ?>
  </div>
</div>
<!-- 핵심 검색 바 동작(공백 검색 방지) -->
<script>
  // 검색 input에 placeholder 포커스 시 사라지고, blur 시 내용 없으면 복귀
  const searchInput = document.getElementById('keywordInput');
  if (searchInput) {
    const oriPlaceholder = searchInput.placeholder;
    searchInput.addEventListener('focus', () => searchInput.placeholder = '');
    searchInput.addEventListener('blur', () => {
      if (!searchInput.value) searchInput.placeholder = oriPlaceholder;
    });
  }
  // 공백 검색 방지 + 실제 값 제대로 전송
  document.getElementById('searchForm').onsubmit = function(e) {
    const val = searchInput.value.trim();
    if (!val) {
      alert('검색어를 입력해 주세요.');
      searchInput.value = '';
      searchInput.focus();
      e.preventDefault();
      return false;
    }
    searchInput.value = val; // 앞뒤 공백 잘라서 검색
    return true;
  }
</script>
<!-- script.js 연동 -->
<script src="script.js"></script>
</body>
</html>
