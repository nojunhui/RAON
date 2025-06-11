<?php
session_start();
if (!isset($_SESSION['student_id'])) exit('로그인 필요');
$student_id = $_SESSION['student_id'];
$book_id = intval($_POST['book_id'] ?? 0);
$conn = new mysqli("localhost", "root", "1234", "test");

// 값 받기
$category = $_POST['category'] ?? null;
$grade = ($_POST['grade'] !== '') ? intval($_POST['grade']) : null;
$major = $_POST['major'] ?? null;
$subject = ($_POST['subject'] ?? null);

// 전공이면 subject를 null로 설정
if ($category === "전공") $subject = null;

// 나머지 값
$title = $_POST['title'] ?? null;
$author = $_POST['author'] ?? null;
$publisher = $_POST['publisher'] ?? null;
$publish_date = $_POST['publish_date'] ?? null;
$original_price = ($_POST['original_price'] !== '') ? intval($_POST['original_price']) : null;
$selling_price = ($_POST['selling_price'] !== '') ? intval($_POST['selling_price']) : null;
$description = $_POST['description'] ?? null;

// 대표 사진/이미지 업데이트
$image_paths = [];
if (!empty($_FILES['images']['name'][0])) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir);
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

// 기존 이미지 삭제
if (!empty($_POST['delete_exist']) && is_array($_POST['delete_exist'])) {
    foreach ($_POST['delete_exist'] as $del_img) {
        $conn->query("DELETE FROM BooksImages WHERE book_id=$book_id AND image_path='". $conn->real_escape_string($del_img) ."'");
        if (file_exists($del_img)) unlink($del_img);
    }
}

// 새 이미지 추가
if (!empty($image_paths)) {
    $img_stmt = $conn->prepare("INSERT INTO BooksImages (book_id, image_path) VALUES (?, ?)");
    foreach ($image_paths as $img_path) {
        $img_stmt->bind_param("is", $book_id, $img_path);
        $img_stmt->execute();
    }
    $img_stmt->close();
}

// 대표사진 갱신 (삭제 또는 새 이미지 추가시)
$res = $conn->query("SELECT image_path FROM BooksImages WHERE book_id=$book_id LIMIT 1");
$main_image = ($row = $res->fetch_assoc()) ? $row['image_path'] : '';

// UPDATE books 테이블
$sql = "UPDATE books SET 
    category=?, grade=?, major=?, subject=?, 
    title=?, author=?, publisher=?, publish_date=?, 
    original_price=?, selling_price=?, description=?, image_path=?
    WHERE book_id=? AND seller_id=?";

// 타입: s = string, i = int
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sissssssddssis",
    $category,        // s
    $grade,           // i
    $major,           // s
    $subject,         // s
    $title,           // s
    $author,          // s
    $publisher,       // s
    $publish_date,    // s
    $original_price,  // d (사실 INT지만 i, d 모두 OK)
    $selling_price,   // d
    $description,     // s
    $main_image,      // s
    $book_id,         // i
    $student_id       // s (숫자면 i, 문자면 s)
);
// → 타입을 실제 데이터 형태와 일치시킵니다.

if ($stmt->execute()) {
    echo "<script>alert('수정이 완료되었습니다.'); location.href='mypage.php';</script>";
} else {
    echo "수정 실패: ".$conn->error;
}
$stmt->close();
$conn->close();
?>
