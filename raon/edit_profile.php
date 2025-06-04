<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
?>
  <?php include 'header.php'; ?>

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
      background-color: rgba(0,0,0,0.36);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 999;
      font-family: 'RIDIBatang', sans-serif;
    }
    .modal-content {
      background: #fff;
      padding: 40px 30px 35px 30px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 6px 24px rgba(0,0,0,0.20);
      max-width: 420px;
      width: 94vw;
      position: relative;
    }
    .modal-content h2 {
      margin-bottom: 20px;
      font-size: 1.48em;
      font-weight: bold;
    }
    .modal-content input[type="password"] {
      width: 80%;
      padding: 10px;
      margin: 20px 0 16px 0;
      border: 1px solid #a5753f;
      border-radius: 6px;
      font-size: 1em;
    }
    .modal-content button {
      padding: 9px 24px;
      background-color: #f3c97b;
      border: 1.3px solid #a5753f;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.15s;
      font-size: 1.05em;
    }
    .modal-content button:hover { background-color: #e3b95f; }
    .modal-x-btn {
      position: absolute;
      right: 19px; top: 18px;
      background: none;
      border: none;
      font-size: 1.48em;
      color: #ad854c;
      cursor: pointer;
      z-index: 1001;
    }
  </style>
</head>
<body>
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

  
  <!-- 🔵 회원탈퇴 팝업 모달(숨겨진 상태로 미리 삽입) -->
  <div id="withdrawModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
      <button class="modal-x-btn" onclick="closeWithdrawModal()" title="닫기">&times;</button>
      <h2>회원탈퇴</h2>
      <div style="font-size:1.15em; font-weight:bold; margin-bottom:12px;">정말로 탈퇴하시겠습니까!?</div>
      <div style="margin-bottom:12px; color:#8b6619; font-size:0.97em;">
        계정을 삭제하시려면 현재 사용중인 비밀번호를 입력하세요.
      </div>
      <form id="withdrawForm" action="mypage/withdraw_process.php" method="post">
        <input type="password" name="password" placeholder="비밀번호 입력" required>
        <br>
        <button type="submit">회원 탈퇴</button>
      </form>
    </div>
  </div>

   <script>
    // 회원탈퇴 모달 열기
    function openWithdrawModal() {
      document.getElementById("withdrawModal").style.display = "flex";
      // 비밀번호 input에 자동포커스
      setTimeout(function(){
        let pw = document.querySelector('#withdrawModal input[type="password"]');
        if(pw) pw.focus();
      }, 100);
    }
    // 회원탈퇴 모달 닫기
    function closeWithdrawModal() {
      document.getElementById("withdrawModal").style.display = "none";
      // 입력값도 지워주기(UX)
      let pw = document.querySelector('#withdrawModal input[type="password"]');
      if(pw) pw.value = '';
    }
    // x버튼 외에도 배경 클릭 시 닫기
    document.getElementById("withdrawModal").addEventListener("click", function(e){
      if(e.target === this) closeWithdrawModal();
    });

    // 회원탈퇴 a태그 이벤트
    document.getElementById('withdrawBtn').onclick = function(e) {
      e.preventDefault();
      openWithdrawModal();
      return false;
    };
    // 회원정보 수정 폼 새 비번 일치 확인
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
