<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "1234", "test");
$student_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8" />
  <title>RAON 채팅</title>
  <link rel="stylesheet" href="style.css" />
  <style>
  @font-face { font-family: 'RIDIBatang'; src: url('https://fastly.jsdelivr.net/gh/projectnoonnu/noonfonts_twelve@1.0/RIDIBatang.woff') format('woff'); font-weight: normal; font-style: normal; }
  body { background-color: #fff1cc; font-family: 'RIDIBatang', sans-serif; margin: 0; padding: 0; }
  .container.chat-page { max-width: 1200px; margin: 20px auto; background-color: #fff8e7; border-radius: 10px; padding: 20px; box-sizing: border-box; min-height: 80vh; display: flex; flex-direction: column; }
  .chat-box-wrapper { flex-grow: 1; display: flex; flex-direction: column; border: 1px solid #3a2d00; border-radius: 12px; background-color: white; margin-bottom: 20px; }
  .chat-header { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #562502; font-weight: bold; border-top-left-radius: 12px; border-top-right-radius: 12px; }
  .chat-header .chat-user span { font-weight: normal; font-size: 13px; color: #333; }
  .chat-status { align-self: center; font-size: 12px; }
  .chat-box { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 20px; max-height: 300px; }
  .chat-message { max-width: 60%; padding: 12px 16px; border-radius: 15px; font-size: 14px; line-height: 1.5; word-break: break-word; position: relative; background-color: #fff5d1; border: 1px solid #562502; }
  .chat-message.right { align-self: flex-end; background-color: #FFFFFF; border-top-right-radius: 0; }
  .chat-message.left { align-self: flex-start; border-top-left-radius: 0; }
  .chat-message img { max-width: 100%; border-radius: 10px; margin-top: 8px; }
  .read-status { display: block; margin-top: 6px; font-size: 12px; text-align: left; }
  .read-status.read { color: green; }
  .read-status.unread { color: red; }
  .chat-input-area { display: flex; align-items: center; padding: 10px 15px; border-top: 1px solid #3a2d00; background-color: white; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
  .chat-input-area input[type="text"] { flex: 1; padding: 10px; margin: 0 10px; border: 1px solid #ccc; border-radius: 6px; outline: none; font-size: 14px; }
  .chat-input-area .icon { cursor: pointer; font-size: 18px; margin: 0 5px; }
  .send-btn { background: none; border: none; cursor: pointer; font-size: 18px; }
  </style>
</head>
<body>

  <div class="container chat-page">
    <div class="chat-box-wrapper">
      <div class="chat-header">
        <div class="chat-user">
          <strong><?= htmlspecialchars($name) ?></strong><br>
          <span>[ 파이썬으로 경험하는 빅데이터 분석과 머신러닝 ]</span>
        </div>
        <div class="chat-status">[구매중]</div>
      </div>

      <div class="chat-box">
        <div class="chat-message right">
          안녕하세요! 책 거래 가능할까요?
          <span class="read-status read">읽음</span>
        </div>
        <div class="chat-message left">
          네, 가능합니다!
        </div>
      </div>

      <div class="chat-input-area">
        <!-- 이미지 첨부용 숨겨진 파일 입력 -->
        <input type="file" id="image-upload" accept="image/*" style="display:none" />
        <label class="icon" for="image-upload" title="이미지 첨부">
          <img src="https://img.icons8.com/ios-glyphs/30/image--v1.png" alt="이미지 첨부" />
        </label>

        <!-- 일반 파일 첨부용 숨겨진 파일 입력 -->
        <input type="file" id="file-upload" style="display:none" />
        <label class="icon" for="file-upload" title="첨부파일">
          <img src="https://img.icons8.com/ios-glyphs/30/attach.png" alt="첨부파일" />
        </label>

        <input type="text" placeholder="메시지를 입력하세요..." />
        <button class="send-btn" aria-label="보내기">
          <img src="https://img.icons8.com/ios-glyphs/30/filled-sent.png" alt="보내기" />
        </button>
      </div>
    </div>
  </div>

  <script>
    const chatBox = document.querySelector('.chat-box');
    const input = document.querySelector('.chat-input-area input[type="text"]');
    const sendBtn = document.querySelector('.send-btn');
    const imageInput = document.getElementById('image-upload');
    const fileInput = document.getElementById('file-upload');

    function addMessage(text, isRead = false, isRight = true, isImage = false, fileName = '') {
      if (!text.trim() && !isImage) return;  // 빈 메시지 무시 (이미지는 빈 텍스트 가능)

      const messageDiv = document.createElement('div');
      messageDiv.classList.add('chat-message');
      messageDiv.classList.add(isRight ? 'right' : 'left');

      if (isImage) {
        // 이미지일 때 텍스트는 파일명으로 대체 가능
        const msgText = document.createElement('div');
        msgText.textContent = text || fileName || '이미지';
        messageDiv.appendChild(msgText);

        const img = document.createElement('img');
        img.src = text;  // text가 이미지 데이터 URL임
        img.alt = fileName;
        messageDiv.appendChild(img);
      } else {
        messageDiv.textContent = text;
      }

      // 읽음 상태 스팬 추가
      const readStatusSpan = document.createElement('span');
      readStatusSpan.classList.add('read-status');
      readStatusSpan.classList.add(isRead ? 'read' : 'unread');
      readStatusSpan.textContent = isRead ? '읽음' : '안읽음';

      messageDiv.appendChild(readStatusSpan);
      chatBox.appendChild(messageDiv);

      // 스크롤 아래로 자동 이동
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    sendBtn.addEventListener('click', () => {
      const message = input.value;
      addMessage(message, false);
      input.value = '';
      input.focus();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        sendBtn.click();
        e.preventDefault();
      }
    });

    // 이미지 첨부 처리
    imageInput.addEventListener('change', () => {
      const file = imageInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          addMessage(e.target.result, false, true, true, file.name);
        }
        reader.readAsDataURL(file);
      }
      imageInput.value = ''; // 파일 재선택 가능하도록 초기화
    });

    // 일반 파일 첨부 처리
    fileInput.addEventListener('change', () => {
      const file = fileInput.files[0];
      if (file) {
        // 파일 이름만 메시지로 표시 (파일 전송 기능은 별도 구현 필요)
        addMessage(`📎 첨부파일: ${file.name}`, false);
      }
      fileInput.value = ''; // 파일 재선택 가능하도록 초기화
    });
  </script>

  <footer style="margin-top:32px;text-align:center;color:#C1A06C;">© RAON</footer>
</body>
</html>
