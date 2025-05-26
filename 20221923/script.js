document.addEventListener('DOMContentLoaded', () => {
  // 사이드바 항목 클릭 시 페이지 이동
  const sidebarItems = document.querySelectorAll('.sidebar ul li');

  sidebarItems.forEach((item, index) => {
    item.addEventListener('click', () => {
      switch(index) {
        case 0:
          window.location.href = '../mypage-profile-update/profile-update.html';
          break;
          case 1:
            window.location.href = '../mypage-post-history/post-history.html';
            break;
            case 2:
              window.location.href = '../mypage-favorite-books/favorite-books.html';
              break;
              case 3:
                window.location.href = '../mypage-transaction-history/transaction-history.html';
                break;
                case 4:
                  window.location.href = '../mypage-chat/chat.html';
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
    window.location.href = '../mypage/mypage.html';
  });
}

  // 회원탈퇴 팝업 관련 기능
  const withdrawBtn = document.querySelector('.withdraw');
  const modal = document.getElementById('withdrawModal');

  if (withdrawBtn && modal) {
    withdrawBtn.addEventListener('click', () => {
      modal.style.display = 'flex';
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }

  // 채팅 전송 + 읽음/안읽음 처리
  const input = document.querySelector('.chat-input-area input[type="text"]');
  const sendBtn = document.querySelector('.send-btn');
  const chatBox = document.querySelector('.chat-box');

  function appendMyMessage(text) {
    // 이전 메시지의 read-status 제거
    const prevStatus = chatBox.querySelectorAll('.chat-message.right .read-status');
    prevStatus.forEach(el => el.remove());

    // 메시지 요소 생성
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chat-message', 'right');
    messageDiv.textContent = text;

    // 읽음 상태 span 생성
    const statusSpan = document.createElement('span');
    statusSpan.classList.add('read-status', 'unread');
    statusSpan.textContent = '안읽음';

    messageDiv.appendChild(statusSpan);
    chatBox.appendChild(messageDiv);

    // 스크롤 아래로 자동 이동
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  sendBtn.addEventListener('click', () => {
    const message = input.value.trim();
    if (message === '') return;

    appendMyMessage(message);
    input.value = '';
  });

  // 엔터키로 전송
  input.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      sendBtn.click();
    }
  });
});
