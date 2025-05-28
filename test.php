<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>로그인</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>로그인</h2>
  <form action="login.php" method="post">
    학번: <input type="text" name="student_id" maxlength="8" required><br>
    비밀번호: <input type="password" name="password" required><br>
    <input type="submit" value="로그인">
  </form>
  <a href="register.html">회원가입</a>

</body>
<footer>© RAON</footer>
<script src="script.js"></script>
</html>
