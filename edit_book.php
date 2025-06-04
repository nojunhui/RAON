<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html"); exit;
}
$student_id = $_SESSION['student_id'];
$book_id = intval($_GET['book_id'] ?? 0);
$conn = new mysqli("localhost", "root", "1234", "test");

$res = $conn->query("SELECT * FROM books WHERE book_id=$book_id AND seller_id='$student_id'");
$book = $res->fetch_assoc();
if (!$book) { exit("잘못된 접근입니다."); }

// 기존 이미지들 불러오기
$imgRes = $conn->query("SELECT * FROM BooksImages WHERE book_id=$book_id");
$images = [];
while ($row = $imgRes->fetch_assoc()) $images[] = $row;

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>교재 수정 | RAON</title>
    <link rel="stylesheet" href="style.css">
<style>
        .post-box { max-width:540px; margin:40px auto; background:#fff; border-radius:15px; box-shadow:0 1px 8px rgba(0,0,0,0.08); padding:34px 35px 34px 35px;}
        .post-title {font-size:1.29em;font-weight:bold;margin-bottom:25px;letter-spacing:2px;}
        .post-btn-row {display:flex;gap:12px;margin-bottom:23px;}
        .post-type-btn {
            width: 90px;
            height: 30px;
            background: #fff;
            color: #c87d2e;
            border: none;
            border-radius: 8px;
            font-size: 1.06em;
            cursor: pointer;
            font-weight: bold;
            transition: background .15s;
            border: 1.5px solid #ffc093;
            margin-bottom: 0;
        }
        .post-type-btn.active, .post-type-btn.selected {
            background: #FFC093 !important;
            color: #fff;
        }
        .post-filter { display:flex; gap:10px; margin-bottom:16px;}
        .input-row {margin-bottom:16px;}
        .input-row label {display:block;font-size:1em;margin-bottom:7px;font-weight:bold;color:#333;}
        .input-row input, .input-row textarea, .input-row select {width:100%;border-radius:7px;border:1px solid #ffc093;padding:8px;font-size:1.05em;}
        .input-row textarea {resize:vertical;min-height:80px;}
        .submit-btn {width:100%;margin-top:15px;height:44px;font-size:1.1em;font-weight:bold;background:#965426;color:#fff;border:none;border-radius:8px;cursor:pointer;}

        /* 파일 업로드 커스텀 */
        .custom-file-wrap {
          display: flex;
          align-items: center;
          gap: 12px;
          border-radius: 9px;
          padding: 12px 18px;
          width: 100%;
          max-width: 350px;
          margin: 0 auto 2px auto;  /* ← 이 부분! */
          justify-content: center;  /* ← 이 부분! */
        }
        .pretty-file-btn {
          background: #ffc093;
          color: #fff;
          font-weight: bold;
          border: none;
          border-radius: 7px;
          font-size: 1em;
          padding: 8px 22px;
          cursor: pointer;
          box-shadow: 0 1px 5px 0 #f8e0c1a9;
          transition: background 0.14s;
        }
        .pretty-file-btn:hover {
          background: #562502;
        }
        .file-info {
          color: #000000;
          font-size: 1.02em;
          font-weight: bold;
          margin-left: 2px;
          letter-spacing: 0.02em;
        }
        .input-row input[type="date"] {padding:6px;}
        .topnav {
            display: flex;
            align-items: center;
            background: #FFECC2;
            padding: 16px 24px 8px 24px;
            justify-content: space-between;
        }
        .logo {
            font-weight: bold;
            font-size: 2.2em;
            color: #222;
            margin-right: 36px;
            letter-spacing: 2px;
            cursor: pointer;
        }
        .auth-btns { display: flex; align-items: center; gap: 7px;}
        .auth-btns button { width:110px; height:40px; border-radius: 9px; background: #fff; border: 1px solid #ffc093; color:#c87d2e; font-weight: bold; font-size:1.05em; cursor:pointer;}
        .username { font-weight: bold; color: #222; margin-right: 13px; font-size:1.04em; }
        /* 드롭다운 스타일 */
        #majorDropdown {display:none;position:absolute;z-index:999;background:#fff;box-shadow:0 2px 7px rgba(0,0,0,0.14);border-radius:7px;padding:10px 0;min-width:370px;top:32px;}
        #collegeList { min-width:110px; border-right:1px solid #f0c6a7; padding:0 8px;}
        #deptList { min-width:150px; padding:0 8px;}
        .college-item, .dept-item {
            padding: 6px 11px; font-size: 1.01em; cursor: pointer; border-radius: 5px;
            margin-bottom: 2px; white-space: nowrap; transition: background 0.13s;
        }
        .college-item:hover, .dept-item:hover { background: #ffe4cc;}
        .college-item:first-child { font-weight:bold; color:#c87d2e; }

        
    </style>
</head>
<body>
<div class="topnav">
    <div class="logo" onclick="location.href='index.php'">RAON</div>
    <div class="auth-btns">
        <span class="username" id="username"></span>
        <a href="mypage.php"><button type="button">마이페이지</button></a>
        <a href="logout.php?goindex=1"><button type="button">로그아웃</button></a>
    </div>
</div>

<div class="post-box">
    <div class="post-title">교재 수정</div>
    <form action="update_book.php" method="post" enctype="multipart/form-data" id="editForm" autocomplete="off">
        <input type="hidden" name="book_id" value="<?=$book_id?>">
        <!-- 카테고리 -->
        <div class="post-btn-row">
            <button type="button" id="btnMajor" class="post-type-btn <?=$book['category']=='전공'?'active':''?>">전공</button>
            <button type="button" id="btnLiberal" class="post-type-btn <?=$book['category']=='교양'?'active':''?>">교양</button>
        </div>
        <input type="hidden" name="category" id="categoryInput" value="<?=htmlspecialchars($book['category'])?>">
        <!-- 전공 선택시 -->
        <div id="major-filter" class="post-filter" style="<?=($book['category']=='전공')?'':'display:none;'?>">
            <select id="gradeSelect" name="grade" class="filter-sel" style="width:90px;">
                <option value="">학년</option>
                <?php for($i=1;$i<=5;$i++): ?>
                    <option value="<?=$i?>" <?=$book['grade']==$i?'selected':''?>><?=$i?>학년</option>
                <?php endfor; ?>
            </select>
            <div id="majorSelectBtn" class="filter-sel" style="width:200px;position:relative;user-select:none;cursor:pointer;"><?=$book['major'] ? htmlspecialchars($book['major']) : '학과'?></div>
            <input type="hidden" id="selectedMajor" name="major" value="<?=htmlspecialchars($book['major'])?>">
            <div id="majorDropdown" style="display:none;">
                <div style="display:flex;">
                  <div id="collegeList"></div>
                  <div id="deptList"></div>
                </div>
            </div>
        </div>
        <!-- 교양 선택시 -->
        <div id="liberal-filter" class="post-filter" style="<?=($book['category']=='교양')?'':'display:none;'?>">
            <select id="liberalType" name="subject" class="filter-sel">
                <option value="">교양 종류</option>
                <option value="호심교양" <?=$book['subject']=='호심교양'?'selected':''?>>호심교양</option>
                <option value="균형교양" <?=$book['subject']=='균형교양'?'selected':''?>>균형교양</option>
            </select>
        </div>
        <!-- 이하 나머지 필드, 기존 value 값 넣어서 -->
        <div class="input-row"><label>책 제목</label>
            <input type="text" name="title" value="<?=htmlspecialchars($book['title'])?>" required>
        </div>
        <div class="input-row"><label>저자</label>
            <input type="text" name="author" value="<?=htmlspecialchars($book['author'])?>" required>
        </div>
        <div class="input-row"><label>출판사</label>
            <input type="text" name="publisher" value="<?=htmlspecialchars($book['publisher'])?>" required>
        </div>
        <div class="input-row"><label>출판일</label>
            <input type="date" name="publish_date" value="<?=$book['publish_date']?>">
        </div>
        <div class="input-row"><label>원가(정가)</label>
            <input type="number" name="original_price" min="0" value="<?=$book['original_price']?>">
        </div>
        <div class="input-row"><label>판매 가격</label>
            <input type="number" name="selling_price" min="0" value="<?=$book['selling_price']?>" required>
        </div>
        <div class="input-row"><label>상세 설명</label>
            <textarea name="description"><?=htmlspecialchars($book['description'])?></textarea>
        </div>

        <!-- 새 파일 첨부 -->
        <!-- 기존 이미지 표시 -->
        <div class="input-row">
            <label>책 사진 (최대 5장, 첫 번째가 대표사진)</label>
            <div id="existingFilesList"></div>
        </div>
        <!-- 새 파일 첨부 -->
        <div class="input-row">
            <div class="custom-file-wrap">
                <button type="button" id="customFileBtn" class="pretty-file-btn">파일 선택</button>
                <span id="selectedFilesText" class="file-info">선택된 파일 없음</span>
                <input type="file" id="bookImages" name="images[]" accept="image/*" multiple style="display:none;">
            </div>
            <div id="selectedFilesList"></div>
        </div>

        <div class="input-row" style="margin-top:10px;">
            <button type="submit" class="submit-btn">수정 완료</button>
        </div>
    </form>
</div>
<script>
window.onload = function() {
    const name = sessionStorage.getItem('name');
    if(name) document.getElementById('username').textContent = name + '님';
};
let deleteImgs = [];
function removeExistImg(path) {
    deleteImgs.push(path);
    document.getElementById('delete_exist').value = deleteImgs.join(',');
    event.target.parentElement.style.display = 'none';
}

// 전공/교양 토글 동작
const btnMajor = document.getElementById('btnMajor');
const btnLiberal = document.getElementById('btnLiberal');
const majorFilter = document.getElementById('major-filter');
const liberalFilter = document.getElementById('liberal-filter');
const categoryInput = document.getElementById('categoryInput');
btnMajor.onclick = function() {
    btnMajor.classList.add('active');
    btnLiberal.classList.remove('active');
    majorFilter.style.display = '';
    liberalFilter.style.display = 'none';
    categoryInput.value = '전공';
};
btnLiberal.onclick = function() {
    btnLiberal.classList.add('active');
    btnMajor.classList.remove('active');
    liberalFilter.style.display = '';
    majorFilter.style.display = 'none';
    categoryInput.value = '교양';
};
btnMajor.click();

// 학년/학과 2단 드롭다운
const departmentsByCollege = {
  "보건복지대학": [
    "간호학과",
    "작업치료학과",
    "사회복지학부",
    "언어치료학과",
    "식품영양학과",
    "보건행정학부",
    "응급구조학과",
    "건강기능식품학과",
    "반려동물보건산업학과"
  ],
  "문화산업대학": [
    "스포츠과학부",
    "시각영상디자인학과",
    "산업디자인학과",
    "인테리어디자인학과",
    "패션주얼리디자인학과",
    "패션ㆍ주얼리디자인학부",
    "뷰티미용학과",
    "사진영상학과",
    "문예창작과",
    "호텔관광경영학부",
    "항공서비스학과",
    "호텔조리제과제빵학과"
  ],
  "인문사회과학대학": [
    "유아교육과",
    "청소년상담ㆍ평생교육학과",
    "아동학과",
    "한국어교육과",
    "심리학과",
    "경찰행정학과",
    "소방행정학과",
    "사이버보안경찰학과",
    "문헌정보학과",
    "경영학과",
    "회계세무학과",
    "무역유통학과",
    "도시·부동산학과",
    "국방학과"
  ],
  "공과대학": [
    "건축학부",
    "건축공학과",
    "컴퓨터공학과",
    "AI소프트웨어학과",
    "전기공학과",
    "토목공학과",
    "기계자동차공학부",
    "국방기술학부",
    "AI자동차학과",
    "융합기계공학과",
    "기계자동차공학부(계약)"
  ]
};
const majorSelectBtn = document.getElementById('majorSelectBtn');
const majorDropdown = document.getElementById('majorDropdown');
const collegeList = document.getElementById('collegeList');
const deptList = document.getElementById('deptList');
const selectedMajorInput = document.getElementById('selectedMajor');
function setMajor(val, txt) {
  selectedMajorInput.value = val;
  majorSelectBtn.textContent = txt;
  majorDropdown.style.display = "none";
}
if (collegeList && deptList && selectedMajorInput && majorSelectBtn) {
  collegeList.innerHTML = '';
  let allItem = document.createElement('div');
  allItem.textContent = "전체";
  allItem.className = "college-item";
  allItem.style.cursor = "pointer";
  allItem.onclick = function(e){
    setMajor("", "전체");
    e.stopPropagation();
  };
  collegeList.appendChild(allItem);

  Object.keys(departmentsByCollege).forEach(college => {
    let cItem = document.createElement('div');
    cItem.textContent = college;
    cItem.className = "college-item";
    cItem.style.cursor = "pointer";
    cItem.onmouseenter = function() {
      deptList.innerHTML = "";
      let allDept = document.createElement('div');
      allDept.textContent = "전체";
      allDept.className = "dept-item";
      allDept.style.cursor = "pointer";
      allDept.onclick = function(e){
        setMajor(college + " 전체", college + " 전체");
        e.stopPropagation();
      };
      deptList.appendChild(allDept);
      departmentsByCollege[college].forEach(dept => {
        let dItem = document.createElement('div');
        dItem.textContent = dept;
        dItem.className = "dept-item";
        dItem.style.cursor = "pointer";
        dItem.onclick = function(e){
          setMajor(dept, dept);
          e.stopPropagation();
        };
        deptList.appendChild(dItem);
      });
    };
    collegeList.appendChild(cItem);
  });
}
if (majorSelectBtn && majorDropdown) {
  majorSelectBtn.onclick = function(e) {
    majorDropdown.style.display = majorDropdown.style.display === "block" ? "none" : "block";
    e.stopPropagation();
  };
  document.body.addEventListener('click', function() {
    if(majorDropdown) majorDropdown.style.display = 'none';
  });
}

// 기존 사진 목록을 자바스크립트로 넘겨서, post_book.html과 동일하게 동작하도록 구성
const existingImages = <?=json_encode(array_map(function($img) {
    return [
        'path' => $img['image_path'],
        'file' => basename($img['image_path']),
        'size' => @filesize($img['image_path']) ? round(filesize($img['image_path'])/1024,1) : '-'
    ];
}, $images))?>;

let fileList = [...existingImages];
const deletedExistingImages = new Set();

function showFiles() {
    const existingFilesList = document.getElementById('existingFilesList');
    existingFilesList.innerHTML = '';
    if (fileList.length === 0) {
        existingFilesList.textContent = "사진이 없습니다.";
        return;
    }
    fileList.forEach((f, i) => {
        if (!f) return;
        let isExisting = !!f.path; // 기존 이미지면 path 있음
        let fileLabel = `${i===0 ? '<b>[대표]</b> ' : ''}${f.file} (${f.size}KB)`;
        let delBtn = `<button type="button" onclick="removeFile(${i})">삭제</button>`;
        existingFilesList.innerHTML += `${fileLabel} ${delBtn}<br>`;
    });
}

window.removeFile = function(idx) {
    // 기존파일은 삭제 set에 추가
    if(fileList[idx] && fileList[idx].path) {
        deletedExistingImages.add(fileList[idx].path);
    }
    fileList.splice(idx, 1);
    showFiles();
}

showFiles();

// 새 파일 첨부 UI: post_book.html과 동일
const bookImages = document.getElementById('bookImages');
const customFileBtn = document.getElementById('customFileBtn');
const selectedFilesText = document.getElementById('selectedFilesText');
const selectedFilesList = document.getElementById('selectedFilesList');

let newFileList = [];

customFileBtn.onclick = () => bookImages.click();

bookImages.onchange = function(e) {
    const newFiles = Array.from(bookImages.files);
    for (let f of newFiles) {
        if (fileList.length + newFileList.length >= 5) break;
        if (!newFileList.some(x => x.name === f.name && x.size === f.size && x.lastModified === f.lastModified)) {
            newFileList.push(f);
        }
    }
    if (fileList.length + newFileList.length > 5) newFileList = newFileList.slice(0, 5 - fileList.length);

    bookImages.value = "";
    showNewFiles();
};

function showNewFiles() {
    selectedFilesList.innerHTML = '';
    if (newFileList.length === 0) {
        selectedFilesText.textContent = "선택된 파일 없음";
        return;
    }
    let names = newFileList.map(f => f.name);
    selectedFilesText.textContent = names.join(', ');
    newFileList.forEach((f, i) => {
        const div = document.createElement('div');
        div.style.marginBottom = '7px';
        div.innerHTML =
            `<b>${fileList.length + i === 0 ? '[대표]' : ''}</b> ${f.name} (${(f.size / 1024).toFixed(1)}KB)
             <button type="button" onclick="removeNewFile(${i})" style="margin-left:8px;">삭제</button>`;
        selectedFilesList.appendChild(div);
    });
}
window.removeNewFile = function(idx) {
    newFileList.splice(idx, 1);
    showNewFiles();
}

// 폼 제출 시 삭제/추가/대표순서 반영
document.getElementById('editForm').onsubmit = function(e){
    // 기존 파일 삭제 hidden input 생성
    deletedExistingImages.forEach(path => {
        let input = document.createElement('input');
        input.type = "hidden";
        input.name = "delete_exist[]";
        input.value = path;
        this.appendChild(input);
    });

    // 새 첨부파일(5장 제한)
    if (fileList.length + newFileList.length === 0) {
        alert('사진을 1장 이상 선택하세요.');
        e.preventDefault();
        return false;
    }
    if (fileList.length + newFileList.length > 5) {
        alert('사진은 최대 5장까지 업로드 가능합니다.');
        e.preventDefault();
        return false;
    }

    // 새 파일이 있으면, 대표는 맨 앞 파일(기존+새파일)
    if (newFileList.length > 0) {
        const dt = new DataTransfer();
        newFileList.forEach(f => dt.items.add(f));
        bookImages.files = dt.files;
    }
    // 대표 이미지는 무조건 첫번째! (서버에서 배열 맨 앞이 대표로 저장되도록)

    // 폼 동기 제출
};

</script>
</body>
</html>
