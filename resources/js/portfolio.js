import { createIcons, icons } from 'lucide';

// Lucide Icon Initialization Engine & Robust Fallback
export function initLucideIcons() {
  try {
    if (typeof createIcons === 'function') {
      createIcons({ icons });
    } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    }
  } catch (err) {
    if (process.env.NODE_ENV !== 'production') {
      console.warn('Lucide icon render notice:', err);
    }
  }
}

// Expose on global window scope for dynamic scripts & Blade components
if (typeof window !== 'undefined') {
  window.initLucideIcons = initLucideIcons;
  window.lucide = { createIcons: initLucideIcons, icons };
}

// Auto-run icon initialization on key lifecycle events
document.addEventListener('DOMContentLoaded', initLucideIcons);
window.addEventListener('load', initLucideIcons);
document.addEventListener('livewire:navigated', initLucideIcons);
document.addEventListener('alpine:initialized', initLucideIcons);

// Mobile Menu Navigation Controller
let savedScrollPosition = 0;

const burger = document.getElementById('burger');
const mmenu = document.getElementById('mobile-menu');
const mmClose = document.getElementById('mm-close');

function lockBodyScroll() {
  savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
  document.body.style.overflow = 'hidden';
  document.body.style.position = 'fixed';
  document.body.style.top = `-${savedScrollPosition}px`;
  document.body.style.width = '100%';
  document.body.classList.add('menu-open');
}

function unlockBodyScroll() {
  document.body.style.removeProperty('overflow');
  document.body.style.removeProperty('position');
  document.body.style.removeProperty('top');
  document.body.style.removeProperty('width');
  document.body.classList.remove('menu-open');
  window.scrollTo(0, savedScrollPosition);
}

function openMobileMenu() {
  if (!mmenu || !burger) return;
  burger.classList.add('open');
  burger.setAttribute('aria-expanded', 'true');
  mmenu.classList.add('open');
  mmenu.setAttribute('aria-hidden', 'false');
  lockBodyScroll();
  initLucideIcons();

  // Focus trap / auto-focus first link inside drawer
  setTimeout(() => {
    const focusable = mmenu.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable.length > 0) {
      focusable[0].focus();
    }
  }, 100);
}

function closeMobileMenu() {
  if (!mmenu || !burger) return;
  burger.classList.remove('open');
  burger.setAttribute('aria-expanded', 'false');
  mmenu.classList.remove('open');
  mmenu.setAttribute('aria-hidden', 'true');
  unlockBodyScroll();
  burger.focus();
}

function toggleMobileMenu() {
  const isOpen = mmenu?.classList.contains('open');
  if (isOpen) {
    closeMobileMenu();
  } else {
    openMobileMenu();
  }
}

if (burger) {
  burger.addEventListener('click', toggleMobileMenu);
}

if (mmClose) {
  mmClose.addEventListener('click', closeMobileMenu);
}

document.querySelectorAll('.mm-link').forEach(a => {
  a.addEventListener('click', closeMobileMenu);
});

// Close mobile menu on Escape key press
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && mmenu?.classList.contains('open')) {
    closeMobileMenu();
  }
});

// Trap Focus inside open mobile menu
document.addEventListener('keydown', e => {
  if (e.key !== 'Tab' || !mmenu?.classList.contains('open')) return;

  const focusables = Array.from(
    mmenu.querySelectorAll('button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')
  );
  if (!focusables.length) return;

  const firstEl = focusables[0];
  const lastEl = focusables[focusables.length - 1];

  if (e.shiftKey) {
    if (document.activeElement === firstEl) {
      lastEl.focus();
      e.preventDefault();
    }
  } else {
    if (document.activeElement === lastEl) {
      firstEl.focus();
      e.preventDefault();
    }
  }
});

// Scroll progress bar and active section highlight
const prog = document.getElementById('progress');
const nav = document.getElementById('nav');
const navAs = document.querySelectorAll('.nav-links a[data-section]');
const sectionIds = ['about', 'projects', 'skills', 'experience', 'beyond-code', 'certifications', 'contact'];

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

// Reveal on scroll elements
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

// Welcome Modal
const welcomeModal = document.getElementById('welcome-modal');
const welcomeSeenKey = 'portfolio_welcome_seen';

function closeWelcomeModal() {
  if (!welcomeModal) return;
  welcomeModal.classList.remove('open');
  welcomeModal.setAttribute('aria-hidden', 'true');
  try {
    localStorage.setItem(welcomeSeenKey, 'true');
  } catch (error) {
    // Ignore storage errors
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

// Prefetch case study pages on hover/touch for instant transitions
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

// Contact Form Handler
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
    
    toggles.forEach(btn => {
      btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
      btn.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} theme`);
    });

    // Re-initialize icons to ensure dark/light mode icons render properly
    initLucideIcons();
  };
  
  toggles.forEach(btn => {
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

// Typewriter effect
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
        delay = 40;
      } else {
        el.textContent = currentRole.substring(0, charIdx + 1);
        charIdx++;
        delay = 100;
      }

      if (!isDeleting && charIdx === currentRole.length) {
        isDeleting = true;
        delay = 2500;
      } else if (isDeleting && charIdx === 0) {
        isDeleting = false;
        roleIdx = (roleIdx + 1) % roles.length;
        delay = 500;
      }

      setTimeout(type, delay);
    };

    type();
  });
}
