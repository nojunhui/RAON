<?php
session_start();
if (!isset($_SESSION['student_id'])) {
  header("Location: login.html"); exit;
}
$conn = new mysqli("localhost", "root", "1234", "test");

// 업로드 폴더
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) mkdir($upload_dir);

// 여러 장 사진 저장
$image_paths = [];
if (!empty($_FILES['images']['name'][0])) {
  foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
    if ($_FILES['images']['error'][$key] == 0 && is_uploaded_file($tmp_name)) {
      $file_name = basename($_FILES['images']['name'][$key]);
      $target_path = $upload_dir . time() . '_' . uniqid() . '_' . $file_name;
      if (move_uploaded_file($tmp_name, $target_path)) {
        $image_paths[] = $target_path;
      }
    }
  }
}
$main_image = $image_paths[0] ?? '';

// ... 파일 업로드 부분 생략 ...

$seller_id = $_SESSION['student_id'];
$category = (isset($_POST['category']) && in_array($_POST['category'], ['전공', '교양'])) ? $_POST['category'] : null;
if ($category === null) {
  exit("카테고리를 선택해 주세요. <a href='post_book.html'>돌아가기</a>");
}
$subject = (isset($_POST['subject']) && $_POST['subject'] !== '') ? $_POST['subject'] : null;
$grade   = (isset($_POST['grade']) && $_POST['grade'] !== '') ? $_POST['grade'] : null;
$college = (isset($_POST['college']) && $_POST['college'] !== '') ? $_POST['college'] : null;
$major   = (isset($_POST['major']) && $_POST['major'] !== '') ? $_POST['major'] : null;
$title   = $_POST['title'] ?? null;
$author  = $_POST['author'] ?? null;
$publisher = $_POST['publisher'] ?? null;
$publish_date = $_POST['publish_date'] ?? null;
$original_price = (isset($_POST['original_price']) && $_POST['original_price'] !== '') ? $_POST['original_price'] : null;
$selling_price  = (isset($_POST['selling_price']) && $_POST['selling_price'] !== '') ? $_POST['selling_price'] : null;
$description    = $_POST['description'] ?? null;
$image_path     = $main_image;


$sql = "INSERT INTO Books (
  seller_id, category, subject, grade, college, major,
  title, author, publisher, publish_date, original_price, selling_price, description, image_path
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "ssssssssssddss",
  $seller_id, $category, $subject, $grade, $college, $major,
  $title, $author, $publisher, $publish_date, $original_price, $selling_price, $description, $image_path
);

if ($stmt->execute()) {
  $book_id = $stmt->insert_id;
  // 여러 장 BooksImages 테이블에 저장
  if (!empty($image_paths)) {
    $img_stmt = $conn->prepare("INSERT INTO BooksImages (book_id, image_path) VALUES (?, ?)");
    foreach ($image_paths as $img_path) {
      $img_stmt->bind_param("is", $book_id, $img_path);
      $img_stmt->execute();
    }
    $img_stmt->close();
  }
  // 대표사진/첨부파일 미리보기 출력
  echo "등록 성공! <a href='index.php'>메인으로</a><br><br>";
  echo "<b>책 대표사진 미리보기:</b><br>";
  if ($main_image) echo "<img src='$main_image' style='max-width:150px; max-height:200px; border:1px solid #ccc;'><br>";
  if (count($image_paths) > 1) {
      echo "<b>첨부된 모든 이미지:</b><br>";
      foreach ($image_paths as $img_path) {
        echo "<img src='$img_path' style='max-width:90px; max-height:120px; border:1px solid #eee; margin:2px;'>";
      }
  }
} else {
  echo "등록 실패: ".$conn->error;
}
$stmt->close(); $conn->close();
?>
