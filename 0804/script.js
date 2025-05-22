document.addEventListener("DOMContentLoaded", function () {
  const departmentsByCollege = {
    "보건복지대학": [
      "간호학과",
      "물리치료학과",
      "작업치료학과",
      "사회복지학부",
      "언어치료학과",
      "식품영양학과",
      "보건행정학부",
      "응급구조학과",
      "반려동물보건산업학과",
    ],
    "문화산업대학": [
      "스포츠과학부",
      "시각영상디자인학과",
      "산업디자인학과",
      "인테리어디자인학과",
      "패션주얼리디자인학과",
      "뷰티미용학과",
      "사진영상학과",
      "문예창작과",
      "호텔관광경영학부",
    ],
    "인문사회과학대학": [
      "심리학과",
      "영어영문학과",
      "법학과",
      "항공서비스학과",
      "호텔조리제과제빵학과",
    ],
    "공과대학": ["컴퓨터공학과", "전자공학과", "기계공학과"],
  };

  const collegeSelect = document.getElementById("college");
  const departmentSelect = document.getElementById("department");

  collegeSelect.addEventListener("change", function () {
    const selectedCollege = this.value;
    const departments = departmentsByCollege[selectedCollege] || [];

    departmentSelect.innerHTML = '<option value="">학과 선택</option>';

    departments.forEach((dept) => {
      const option = document.createElement("option");
      option.value = dept;
      option.textContent = dept;
      departmentSelect.appendChild(option);
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // 전공/교양 버튼 전환 및 페이지 이동 기능
  const majorBtn = document.getElementById("majorBtn");
  const liberalBtn = document.getElementById("liberalBtn");

  if (majorBtn && liberalBtn) {
    majorBtn.addEventListener("click", function () {
      majorBtn.classList.add("active");
      liberalBtn.classList.remove("active");
      // 현재 페이지 유지
    });

    liberalBtn.addEventListener("click", function () {
      majorBtn.classList.remove("active");
      liberalBtn.classList.add("active");
      // 교양 페이지로 이동
      window.location.href = "liberal.html";
    });
  }
});

heart.addEventListener('click', () => {
  heart.classList.toggle('fa-regular');
  heart.classList.toggle('fa-solid');
  heart.classList.toggle('red');

  // 1초 후에 찜 목록 페이지로 이동
  setTimeout(() => {
    window.location.href = '/찜목록페이지URL';
  }, 1000);
});