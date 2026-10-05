const menuToggle = document.querySelector('.menu-toggle');
const headerInner = document.querySelector('.header-inner');
const navLinks = document.querySelectorAll('.main-nav a');
const offerForm = document.querySelector('#offerForm');
const toast = document.querySelector('#toast');

menuToggle?.addEventListener('click', () => {
  const open = headerInner.classList.toggle('mobile-open');
  menuToggle.setAttribute('aria-expanded', String(open));
  menuToggle.textContent = open ? '✕' : '☰';
});

navLinks.forEach(link => {
  link.addEventListener('click', () => {
    navLinks.forEach(item => item.classList.remove('active'));
    link.classList.add('active');
    headerInner.classList.remove('mobile-open');
    menuToggle?.setAttribute('aria-expanded', 'false');
    if (menuToggle) menuToggle.textContent = '☰';
  });
});

offerForm?.addEventListener('submit', (event) => {
  event.preventDefault();
  const email = document.querySelector('#offerEmail').value.trim();
  if (!email) return;

  toast.textContent = 'Oferta cadastrada! Verifique seu e-mail.';
  toast.classList.add('show');
  offerForm.reset();

  setTimeout(() => toast.classList.remove('show'), 3200);
});

document.querySelectorAll('[data-login-required]').forEach((button) => {
  button.addEventListener('click', () => {
    window.location.href = 'login.php';
  });
});

const emergencyButton = document.querySelector('#emergencyButton');
const emergencyOverlay = document.querySelector('#emergencyOverlay');
const emergencyClose = document.querySelector('#emergencyClose');

function openEmergency() {
  emergencyOverlay.classList.add('show');
  emergencyOverlay.setAttribute('aria-hidden', 'false');
}

function closeEmergency() {
  emergencyOverlay.classList.remove('show');
  emergencyOverlay.setAttribute('aria-hidden', 'true');
}

emergencyButton?.addEventListener('click', openEmergency);
emergencyClose?.addEventListener('click', closeEmergency);

emergencyOverlay?.addEventListener('click', (event) => {
  if (event.target === emergencyOverlay) closeEmergency();
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') closeEmergency();
});
