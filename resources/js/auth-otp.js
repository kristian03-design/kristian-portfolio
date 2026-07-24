const boxes = Array.from(document.querySelectorAll('.otp-box'));
  const hiddenOtp = document.getElementById('otp');
  const submitBtn = document.getElementById('submitBtn');

  function updateState() {
    const val = boxes.map(b => b.value).join('');
    hiddenOtp.value = val;
    boxes.forEach(b => b.classList.toggle('filled', b.value !== ''));
    submitBtn.disabled = val.length < 6;
  }

  boxes.forEach((box, idx) => {
    box.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace') {
        if (box.value === '' && idx > 0) {
          boxes[idx - 1].value = '';
          boxes[idx - 1].focus();
        } else {
          box.value = '';
        }
        updateState();
        e.preventDefault();
      } else if (e.key === 'ArrowLeft' && idx > 0) {
        boxes[idx - 1].focus();
      } else if (e.key === 'ArrowRight' && idx < 5) {
        boxes[idx + 1].focus();
      }
    });

    box.addEventListener('input', (e) => {
      const char = e.data || box.value;
      if (char && char.length > 1) {
        const digits = char.replace(/\D/g, '').slice(0, 6).split('');
        digits.forEach((d, i) => { if (boxes[i]) boxes[i].value = d; });
        const next = Math.min(digits.length, 5);
        boxes[next].focus();
        updateState();
        return;
      }
      if (!/^\d$/.test(char)) { box.value = ''; return; }
      box.value = char.slice(-1);
      if (idx < 5) boxes[idx + 1].focus();
      updateState();
    });

    box.addEventListener('paste', (e) => {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData).getData('text');
      const digits = pasted.replace(/\D/g, '').slice(0, 6).split('');
      digits.forEach((d, i) => { if (boxes[i]) boxes[i].value = d; });
      const next = Math.min(digits.length, 5);
      if (boxes[next]) boxes[next].focus();
      updateState();
    });

    box.addEventListener('focus', () => box.select());
  });

  boxes[0].focus();

  // Timer countdown
  const countdownEl = document.getElementById('countdown');
  const resendBtn = document.getElementById('resendBtn');
  let seconds = 600;

  const tick = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
      clearInterval(tick);
      countdownEl.textContent = 'Expired';
      countdownEl.classList.add('expired');
      resendBtn.classList.add('visible');
      return;
    }
    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    countdownEl.textContent = `${m}:${s}`;
  }, 1000);

  function loadLucide() {
    if (typeof window.initLucideIcons === 'function') {
      window.initLucideIcons();
    } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }
  }

  document.addEventListener('DOMContentLoaded', loadLucide);
  loadLucide();
