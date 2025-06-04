<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>회원정보 수정 | RAON</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    @font-face {
      font-family: 'RIDIBatang';
      src: url('https://fastly.jsdelivr.net/gh/projectnoonnu/noonfonts_twelve@1.0/RIDIBatang.woff') format('woff');
      font-weight: normal;
      font-style: normal;
    }
    body {
      background: #FFEDC7;
      margin: 0;
      padding-top: 0;
      font-family: 'RIDIBatang', sans-serif;
    }

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
    .main-box { background: #fff; border-radius: 13px; max-width: 1000px; margin: 40px auto; padding: 34px 32px 38px 32px; min-height: 550px; }
    .mypage-menu { float: left; width: 200px; }
    .mypage-menu .menu-box { border: 1.5px solid #e7c195; border-radius: 11px; padding: 24px 20px; margin-bottom: 30px; }
    .mypage-menu .menu-box ul { list-style: none; padding: 0; margin: 0; }
    .mypage-menu .menu-box li { margin-bottom: 18px; }
    .mypage-menu .menu-box li:last-child { margin-bottom: 0; }
    .mypage-menu .menu-box a, .mypage-menu .menu-box b { color: #664317; text-decoration: none; font-size: 1.07em; }
    .mypage-main { margin-left: 240px; }
    .info-title { font-weight: bold; font-size: 1.23em; letter-spacing: 1px; margin-bottom: 18px; border-bottom: 1px solid #8b5a2b; padding-bottom: 4px; }
    form label { display: block; font-weight: bold; margin-top: 16px; color: #42210b; }
    form input[type="password"], form input[type="text"] {
      width: 250px;
      padding: 7px 10px;
      margin-top: 6px;
      border: 1px solid #a5753f;
      border-radius: 5px;
    }
    .phone-box {
      display: flex;
      gap: 6px;
      align-items: center;
      margin-top: 6px;
    }
    .phone-box select, .phone-box input {
      padding: 7px 8px;
      border: 1px solid #a5753f;
      border-radius: 5px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .phone-box input { width: 60px; }
    .action-row {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-top: 30px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .withdraw-link {
      color: red;
      font-size: 0.9em;
      text-decoration: underline;
      cursor: pointer;
      font-family: 'RIDIBatang', sans-serif;
    }
    .submit-btn {
      padding: 8px 16px;
      background-color: #fff;
      border: 1.5px solid #a5753f;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
      font-family: 'RIDIBatang', sans-serif;
    }
    .submit-btn:hover { background-color: #f9e0b8; }
    .modal-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background-color: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 999;
      font-family: 'RIDIBatang', sans-serif;
    }
    .modal-content {
      background-color: white;
      padding: 40px 30px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
      max-width: 400px;
      width: 90%;
      font-family: 'RIDIBatang', sans-serif;
    }
    .modal-content h2 {
      margin-bottom: 15px;
      font-size: 1.5em;
      font-family: 'RIDIBatang', sans-serif;
    }
    .modal-content input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 15px 0;
      border: 1px solid #a5753f;
      border-radius: 6px;
      font-family: 'RIDIBatang', sans-serif;
    }
    .modal-content button {
      padding: 8px 16px;
      background-color: #f3c97b;
      border: 1px solid #a5753f;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.2s;
      font-family: 'RIDIBatang', sans-serif;
    }
    .modal-content button:hover { background-color: #e3b95f; }
    @media (max-width: 900px) {
      .main-box { padding: 18px 5vw 22px 5vw; }
      .mypage-menu { float: none; width: auto; margin-bottom: 22px; }
      .mypage-main { margin-left: 0; }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <div class="main-box">
    <!-- 사이드 메뉴 -->
    <div class="mypage-menu">
      <div class="menu-box">
        <ul>
          <li><b>회원정보 수정</b></li>
          <li><a href="my_books.php">등록한 글 목록</a></li>
          <li><a href="my_interest.php">관심 책 목록</a></li>
          <li><a href="my_records.php">구매 / 판매 기록</a></li>
          <li><a href="my_chat.php">채팅</a></li>
        </ul>
      </div>
    </div>

    <!-- 본문 -->
    <div class="mypage-main">
      <div class="info-title">회원정보 수정</div>
      <form id="editForm" action="update_profile.php" method="post">
        <label>비밀번호</label>
        <input type="password" class="raon-input" name="current_password" id="current_password" placeholder="현재 비밀번호 입력">
       <input type="password" class="raon-input" name="new_password" id="new_password" placeholder="새 비밀번호 입력">
        <input type="password" class="raon-input" name="confirm_password" id="confirm_password" placeholder="새 비밀번호 재입력">
        <label>전화번호</label>
        <div class="phone-box">
          <select name="phone1">
            <option>010</option>
            <option>011</option>
            <option>016</option>
          </select>
          <input type="text" class="raon-input" name="phone2" maxlength="4">
          <input type="text" class="raon-input" name="phone3" maxlength="4">
        </div>
        <!-- 회원탈퇴 + 수정 버튼 한 줄 정렬 -->
        <div class="action-row">
          <a href="#" onclick="openWithdrawModal(); return false;" class="withdraw-link">회원탈퇴</a>
          <button type="submit" class="submit-btn">회원정보 수정</button>
        </div>
      </form>
    </div>
    <div style="clear:both;"></div>
  </div>
  <footer style="text-align:center; margin-top:32px; color:#C1A06C;">© RAON</footer>

  <!-- 탈퇴 모달 스크립트 -->
  <script>
    function openWithdrawModal() {
      document.getElementById("withdrawModal").style.display = "flex";
    }
    document.getElementById('editForm').onsubmit = function(e) {
      var newpw = document.getElementById('new_password').value;
      var confirmpw = document.getElementById('confirm_password').value;
      if(newpw !== confirmpw) {
        alert('새 비밀번호와 재입력 값이 다릅니다. 다시 입력해 주세요.');
        e.preventDefault();
        return false;
      }
    }
  </script>
  <script src="script.js"></script>
</body>
</html>
