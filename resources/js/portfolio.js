

const prog = document.getElementById('progress');
const nav = document.getElementById('nav');
const navAs = document.querySelectorAll('.nav-links a[data-section]');
const sectionIds = ['about', 'projects', 'skills', 'certifications', 'experience', 'contact'];

let scrollScheduled = false;

window.addEventListener('scroll', () => {
  if (scrollScheduled) return;
  scrollScheduled = true;
  
  requestAnimationFrame(() => {
    if (prog) {
      const h = document.documentElement.scrollHeight - innerHeight;
      prog.style.width = `${h > 0 ? (scrollY / h) * 100 : 0}%`;
    }

    nav?.classList.toggle('scrolled', scrollY > 30);

    let current = '';
    sectionIds.forEach(id => {
      const sec = document.getElementById(id);
      if (sec && sec.getBoundingClientRect().top <= 120) current = id;
    });

    navAs.forEach(a => {
      a.classList.toggle('active', a.dataset.section === current);
    });

    scrollScheduled = false;
  });
}, { passive: true });

const burger = document.getElementById('burger');
const mmenu = document.getElementById('mobile-menu');
const mmClose = document.getElementById('mm-close');

burger?.addEventListener('click', () => {
  const isOpen = burger.classList.toggle('open');
  mmenu?.classList.toggle('open');
  document.body.classList.toggle('menu-open', isOpen);
});

mmClose?.addEventListener('click', () => {
  burger?.classList.remove('open');
  mmenu?.classList.remove('open');
  document.body.classList.remove('menu-open');
});

document.querySelectorAll('.mm-link').forEach(a => {
  a.addEventListener('click', () => {
    burger?.classList.remove('open');
    mmenu?.classList.remove('open');
    document.body.classList.remove('menu-open');
  });
});

const revEls = document.querySelectorAll('.r');
const ro = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) e.target.classList.add('in');
  });
}, { threshold: 0.07 });
revEls.forEach(el => ro.observe(el));

document.querySelectorAll('.sk-fill').forEach(fill => {
  fill.style.width = '0';
});

const so = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      setTimeout(() => {
        e.target.style.width = `${e.target.dataset.w}%`;
      }, 150);
      so.unobserve(e.target);
    }
  });
}, { threshold: 0.3 });
document.querySelectorAll('.sk-fill').forEach(fill => so.observe(fill));

const welcomeModal = document.getElementById('welcome-modal');
const welcomeSeenKey = 'portfolio_welcome_seen';

function closeWelcomeModal() {
  if (!welcomeModal) return;

  welcomeModal.classList.remove('open');
  welcomeModal.setAttribute('aria-hidden', 'true');

  try {
    localStorage.setItem(welcomeSeenKey, 'true');
  } catch (error) {
    // Ignore storage failures so the modal never blocks browsing.
  }
}

function openWelcomeModal() {
  if (!welcomeModal) return;

  welcomeModal.classList.add('open');
  welcomeModal.setAttribute('aria-hidden', 'false');
}

if (welcomeModal) {
  let hasSeenWelcome = false;

  try {
    hasSeenWelcome = localStorage.getItem(welcomeSeenKey) === 'true';
  } catch (error) {
    hasSeenWelcome = false;
  }

  if (!hasSeenWelcome) {
    const triggerWelcome = () => window.setTimeout(openWelcomeModal, 1500);
    if ('requestIdleCallback' in window) {
      requestIdleCallback(triggerWelcome);
    } else {
      window.addEventListener('load', triggerWelcome);
    }
  }

  welcomeModal.querySelectorAll('[data-welcome-close]').forEach(element => {
    element.addEventListener('click', closeWelcomeModal);
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && welcomeModal.classList.contains('open')) {
      closeWelcomeModal();
    }
  });
}

// Prefetch case study pages on hover for instant transitions
document.addEventListener('DOMContentLoaded', () => {
  const prefetchedUrls = new Set();
  
  const prefetchLink = (url) => {
    if (!url || url === '#' || prefetchedUrls.has(url)) return;
    prefetchedUrls.add(url);
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    document.head.appendChild(link);
  };

  document.querySelectorAll('a[href*="/projects/"]').forEach(a => {
    a.addEventListener('mouseenter', () => prefetchLink(a.href), { passive: true });
    a.addEventListener('touchstart', () => prefetchLink(a.href), { passive: true });
  });
});

const contactForm = document.getElementById('contactForm');
const formMessage = document.getElementById('formMessage');

function showFormMessage(type, text) {
  if (!formMessage) return;
  formMessage.className = `form-message ${type}`;
  formMessage.textContent = text;
}

contactForm?.addEventListener('submit', async event => {
  event.preventDefault();

  const submit = contactForm.querySelector('.cf-submit');
  const formData = new FormData(contactForm);
  const subject = formData.get('subject')?.toString().trim();

  if (subject) {
    const message = formData.get('message')?.toString().trim() || '';
    formData.set('message', `Subject: ${subject}\n\n${message}`);
  }

  formData.delete('subject');
  submit.disabled = true;
  showFormMessage('success', 'Sending your message...');

  try {
    const response = await fetch(contactForm.action, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      const fallback = data.message || 'Please check the form and try again.';
      throw new Error(fallback);
    }

    contactForm.reset();
    showFormMessage('success', data.message || 'Your message has been sent successfully.');
  } catch (error) {
    showFormMessage('error', error.message || 'Something went wrong. Please try again.');
  } finally {
    submit.disabled = false;
  }
});

// Theme Toggle Functionality
const initThemeToggle = () => {
  const toggles = document.querySelectorAll('#theme-toggle, #mobile-theme-toggle');
  if (!toggles.length) return;
  
  const getTheme = () => document.documentElement.getAttribute('data-theme') || 'light';
  
  const setTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.classList.remove('dark', 'light');
    document.documentElement.classList.add(theme);
    localStorage.setItem('theme', theme);
    
    // Update ARIA attributes
    toggles.forEach(btn => {
      btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      btn.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} theme`);
    });
  };
  
  toggles.forEach(btn => {
    // Set initial state
    const currentTheme = getTheme();
    btn.setAttribute('aria-pressed', currentTheme === 'dark' ? 'true' : 'false');
    btn.setAttribute('aria-label', `Switch to ${currentTheme === 'dark' ? 'light' : 'dark'} theme`);
    
    btn.addEventListener('click', () => {
      const nextTheme = getTheme() === 'dark' ? 'light' : 'dark';
      setTheme(nextTheme);
    });
  });
};

document.addEventListener('DOMContentLoaded', () => {
  initThemeToggle();
  initTypewriter();
});

// Typewriter effect for elements with class .typewriter-target
function initTypewriter() {
  const elements = document.querySelectorAll('.typewriter-target');
  elements.forEach(el => {
    const roles = JSON.parse(el.getAttribute('data-roles') || '[]');
    if (!roles.length) return;

    let roleIdx = 0;
    let charIdx = 0;
    let isDeleting = false;
    let delay = 150;

    const type = () => {
      const currentRole = roles[roleIdx];
      
      if (isDeleting) {
        el.textContent = currentRole.substring(0, charIdx - 1);
        charIdx--;
        delay = 40; // Deleting is faster
      } else {
        el.textContent = currentRole.substring(0, charIdx + 1);
        charIdx++;
        delay = 100; // Typing speed
      }

      if (!isDeleting && charIdx === currentRole.length) {
        isDeleting = true;
        delay = 2500; // Pause at end of word
      } else if (isDeleting && charIdx === 0) {
        isDeleting = false;
        roleIdx = (roleIdx + 1) % roles.length;
        delay = 500; // Pause before next word
      }

      setTimeout(type, delay);
    };

    type();
  });
}



