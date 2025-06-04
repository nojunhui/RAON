// ===================== 학년·학과(2단 드롭다운), 필터, 하트 =====================

// ---- 광주대학교 단과대·학과 최신화 ----
const departmentsByCollege = {
  "공과대학": ["컴퓨터공학과", "전기공학과", "전자공학과", "기계공학과", "토목공학과", "건축학과", "화학공학과"],
  "인문사회과학대학": ["행정학과", "사회복지학과", "유아교육과", "경영학과", "영어영문학과", "법학과", "심리학과"],
  "보건복지대학": ["간호학과", "물리치료학과", "방사선학과", "치위생학과", "응급구조학과", "언어치료학과"],
  "자연과학대학": ["식품영양학과", "생명과학과", "화학과", "수학과"],
  "교육대학": ["초등교육과", "특수교육과", "유아교육과"],
  "예술체육대학": ["미술학과", "음악학과", "체육학과", "무용학과"],
  "문화산업대학": ["시각영상디자인학과", "산업디자인학과", "패션디자인학과", "스포츠과학부"]
};
// ---- 학년 선택 ----
const gradeSelect = document.getElementById('gradeSelect');
const searchGrade = document.getElementById('searchGrade');
if (gradeSelect && searchGrade) {
  searchGrade.value = gradeSelect.value;
  gradeSelect.onchange = function () {
    searchGrade.value = this.value;
  };
}
// ---- 2단 학과 드롭다운 (단과대/학과) ----
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

// 드롭다운 생성
if (collegeList && deptList && selectedMajorInput && majorSelectBtn) {
  collegeList.innerHTML = '';
  // '전체' 버튼
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
    // 단과대 hover시 하위 학과 목록 출력
    cItem.onmouseenter = function() {
      deptList.innerHTML = "";
      // 단과대 전체
      let allDept = document.createElement('div');
      allDept.textContent = "전체";
      allDept.className = "dept-item";
      allDept.style.cursor = "pointer";
      allDept.onclick = function(e){
        setMajor(college + " 전체", college + " 전체");
        e.stopPropagation();
      };
      deptList.appendChild(allDept);
      // 학과
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

// ---- 교양 종류 ----
const liberalType = document.getElementById('liberalType');
const searchSubject = document.getElementById('searchSubject');
if (liberalType && searchSubject) {
  searchSubject.value = liberalType.value;
  liberalType.onchange = function () {
    searchSubject.value = this.value;
  };
}

// ---- 전공/교양 버튼 및 필터 UI ----
const btnMajor = document.getElementById('btn-major');
const btnLiberal = document.getElementById('btn-liberal');
const majorFilter = document.getElementById('major-filter');
const liberalFilter = document.getElementById('liberal-filter');
const searchCategory = document.getElementById('searchCategory');
const searchForm = document.getElementById('searchForm');

if (btnMajor && btnLiberal) {
  btnMajor.onclick = function () {
    if(btnMajor.classList.contains('active')) {
      btnMajor.classList.remove('active','selected');
      if (majorFilter) majorFilter.style.display = 'none';
      if (searchCategory) searchCategory.value = "";
      if (gradeSelect) gradeSelect.value = "";
      if (majorSelectBtn) { majorSelectBtn.textContent = "전체"; selectedMajorInput.value=""; }
      if (searchSubject) searchSubject.value = "";
      if (liberalType) liberalType.value = "";
    } else {
      btnMajor.classList.add('active','selected');
      btnLiberal.classList.remove('active','selected');
      if (majorFilter) majorFilter.style.display = '';
      if (liberalFilter) liberalFilter.style.display = 'none';
      if (searchCategory) searchCategory.value = "전공";
    }
  };
  btnLiberal.onclick = function () {
    if(btnLiberal.classList.contains('active')) {
      btnLiberal.classList.remove('active','selected');
      if (liberalFilter) liberalFilter.style.display = 'none';
      if (searchCategory) searchCategory.value = "";
      if (searchSubject) searchSubject.value = "";
      if (liberalType) liberalType.value = "";
      if (gradeSelect) gradeSelect.value = "";
      if (majorSelectBtn) { majorSelectBtn.textContent = "전체"; selectedMajorInput.value=""; }
    } else {
      btnLiberal.classList.add('active','selected');
      btnMajor.classList.remove('active','selected');
      if (liberalFilter) liberalFilter.style.display = '';
      if (majorFilter) majorFilter.style.display = 'none';
      if (searchCategory) searchCategory.value = "교양";
    }
  };
}

// ---- 하트(관심) 버튼 처리 ----
document.addEventListener('click', function(e) {
  if (e.target.closest('.heart-btn') || e.target.closest('.result-heart-btn')) {
    let btn = e.target.closest('button');
    let liked = btn.classList.contains('liked');
    let bookId = btn.dataset.bookId;
    if (!bookId) return;
    if (!btn.classList.contains('liked')) {
      if (confirm('이 책을 관심 목록에 추가할까요?')) {
        fetch('interest.php', {method:'POST', body:new URLSearchParams({action:'add', book_id:bookId})})
          .then(r=>r.json()).then(data=>{
            if (data.success) {
              btn.classList.add('liked');
              btn.querySelector('.interest-count').textContent = data.count;
            }
          });
      }
    } else {
      if (confirm('관심 목록에서 제거할까요?')) {
        fetch('interest.php', {method:'POST', body:new URLSearchParams({action:'remove', book_id:bookId})})
          .then(r=>r.json()).then(data=>{
            if (data.success) {
              btn.classList.remove('liked');
              btn.querySelector('.interest-count').textContent = data.count;
              // 관심목록 페이지면 행 삭제
              var row = document.getElementById('interest-row-' + bookId);
              if(row) row.parentNode.removeChild(row);
              alert('관심목록에서 제거하였습니다.');
            }
          });
      }
    }
  }
});

// ---- 책 카드/검색결과 전체 클릭시 상세로 ----
document.addEventListener('click', function(e){
  let bookCard = e.target.closest('.book-card,.result-item');
  if(bookCard){
    // 하트버튼이나 버튼 등 클릭이 아니면
    if(!e.target.closest('button') && !e.target.closest('.heart-btn') && !e.target.closest('.result-heart-btn')){
      let link = bookCard.querySelector('a[href*="book_detail.php"]');
      if(link){
        window.location.href = link.getAttribute('href');
      }
    }
  }
});

// ---- 파일 업로드 버튼 ----
const customFileBtn = document.getElementById('customFileBtn');
if(customFileBtn){
  customFileBtn.onclick = function() {
    const bookImages = document.getElementById('bookImages');
    if(bookImages) bookImages.click();
  };
}

// ---- 파일 업로드 목록 ----
const bookImages = document.getElementById('bookImages');
if(bookImages){
  bookImages.onchange = function() {
    const files = this.files;
    const list = document.getElementById('selectedFilesList');

    if (!files.length) {
      if(list) list.textContent = "선택된 파일이 없습니다.";
      return;
    }

    if (files.length > 5) {
      alert("최대 5개까지만 선택할 수 있습니다.");
      this.value = ""; // 선택 초기화
      if(list) list.textContent = "선택된 파일이 없습니다.";
      return;
    }

    let names = Array.from(files).map(file => file.name);
    if(list) list.textContent = names.join(', ');
  };
}

// ---- 등록 폼 제출 유효성 ----
const regForm = document.getElementById('regForm');
if(regForm){
  regForm.onsubmit = function(e) {
    const bookImages = document.getElementById('bookImages');
    if (bookImages && bookImages.files.length > 5) {
      alert("최대 5개까지만 선택할 수 있습니다.");
      e.preventDefault();
      return false;
    }
    // 기타 유효성 검사 ...
  };
}

// ---- 학생ID 입력란 플레이스홀더 ----
const studentInput = document.getElementById('student_id');
if(studentInput){
  const oriPlaceholder = studentInput.placeholder;
  studentInput.addEventListener('focus', function() {
    studentInput.placeholder = '';
  });
  studentInput.addEventListener('blur', function() {
    if (!studentInput.value) studentInput.placeholder = oriPlaceholder;
  });
}
