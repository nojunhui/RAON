document.addEventListener('DOMContentLoaded', () => {
  // 사이드바 항목 클릭 시 페이지 이동
  const sidebarItems = document.querySelectorAll('.sidebar ul li');

  sidebarItems.forEach((item, index) => {
    item.addEventListener('click', () => {
      switch(index) {
        case 0:
          window.location.href = '../mypage-profile-update/index.html';  // 회원정보 수정
          break;
        case 1:
          window.location.href = '../mypage-post-history/index.html';    // 등록한 글 목록
          break;
        case 2:
          window.location.href = '../mypage-favorite-books/index.html';  // 관심 책 목록
          break;
        case 3:
          window.location.href = '../mypage-transaction-history/index.html'; // 구매/판매 기록
          break;
        case 4:
          window.location.href = '../mypage-chat/index.html';            // 채팅
          break;
        default:
          break;
      }
    });
  });

  // 마이페이지 버튼 클릭 시 이동
  const mypageButton = document.getElementById('goToMypage');
  if (mypageButton) {
    mypageButton.addEventListener('click', () => {
      window.location.href = '../mypage/index.html';
    });
  }

  //  회원탈퇴 팝업 관련 기능 추가
  const withdrawBtn = document.querySelector('.withdraw');
  const modal = document.getElementById('withdrawModal');

  if (withdrawBtn && modal) {
    withdrawBtn.addEventListener('click', () => {
      modal.style.display = 'flex';
    });

    // 팝업 바깥 클릭 시 닫기
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }
});
