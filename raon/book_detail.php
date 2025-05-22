<?php
session_start();
if (!isset($_SESSION['student_id'])) {
  header("Location: login.html"); exit;
}
if (!isset($_GET['id'])) {
  echo "잘못된 접근입니다."; exit;
}
$conn = new mysqli("localhost", "root", "1234", "test");

$book_id = intval($_GET['id']);
$sql = "SELECT B.*, U.name as seller_name FROM Books B
        LEFT JOIN Users U ON B.seller_id = U.student_id
        WHERE B.book_id = $book_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
  echo "책 정보를 찾을 수 없습니다."; exit;
}
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title><?=$row['title']?> - 상세보기</title>
  <style>
    .detail-img {height:180px; margin: 8px; border:1px solid #eee;}
  </style>
</head>
<body>
  <h2><?=$row['title']?> (<?=$row['category']?><?=$row['category']=='전공' ? " / ".$row['college']." / ".$row['major'] : " / ".$row['subject']?>)</h2>
  <div><b>저자:</b> <?=$row['author']?></div>
  <div><b>출판사:</b> <?=$row['publisher']?></div>
  <div><b>출판일:</b> <?=$row['publish_date']?></div>
  <div><b>원가:</b> <?=$row['original_price']?>원</div>
  <div><b>판매가:</b> <span style="color:#e36c09;font-weight:bold;"><?=$row['selling_price']?>원</span></div>
  <div><b>판매자:</b> <?=$row['seller_name']?></div>
  <div><b>설명:</b> <?=$row['description']?></div>
  <div><b>등록일:</b> <?=$row['created_at']?></div>
  <hr>
  <div>
    <b>책 사진:</b><br>
    <?php
      // 여러 장 이미지 모두 출력
      $img_sql = "SELECT image_path FROM BooksImages WHERE book_id=$book_id";
      $img_result = $conn->query($img_sql);
      if ($img_result->num_rows == 0 && $row['image_path']) {
        // 옛날 데이터/실패 대비
        echo "<img src='{$row['image_path']}' class='detail-img'>";
      } else {
        while ($img = $img_result->fetch_assoc()) {
          echo "<img src='{$img['image_path']}' class='detail-img'>";
        }
      }
    ?>
  </div>
  <p><a href="index.php">목록으로</a></p>
</body>
</html>
<?php $conn->close(); ?>
