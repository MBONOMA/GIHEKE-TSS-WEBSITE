(function() {
  const STORAGE_KEY = 'giheke-theme';
  const current = localStorage.getItem(STORAGE_KEY) || 'light';
  document.documentElement.setAttribute('data-theme', current);
  window.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('themeToggle');
    const icon = btn ? btn.querySelector('i') : null;
    if (icon) {
      icon.className = current === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
  });
  window.toggleTheme = function() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const next = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem(STORAGE_KEY, next);
    const icon = document.querySelector('#themeToggle i');
    if (icon) {
      icon.className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
  };
})();
