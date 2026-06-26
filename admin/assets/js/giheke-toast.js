(function() {
  'use strict';

  if (window.GihekeToast) return;

  const container = document.createElement('div');
  container.className = 'toast-container';
  container.id = 'gihekeToastContainer';
  document.body.appendChild(container);

  function showToast(options) {
    const { title, message, type = 'success', duration = 4000 } = options;
    const el = document.createElement('div');
    el.className = `giheke-toast toast-${type}`;

    const icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-x-circle-fill',
      warning: 'bi-exclamation-circle-fill',
      info: 'bi-info-circle-fill'
    };

    el.innerHTML = `
      <div class="toast-icon"><i class="bi ${icons[type] || icons.success}"></i></div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close"><i class="bi bi-x"></i></button>
    `;

    const closeBtn = el.querySelector('.toast-close');
    closeBtn.addEventListener('click', function() { hideToast(el); });

    container.appendChild(el);
    el.offsetHeight;

    if (duration > 0) {
      setTimeout(function() { hideToast(el); }, duration);
    }
  }

  function hideToast(el) {
    if (!el || el.classList.contains('toast-hiding')) return;
    el.classList.add('toast-hiding');
    setTimeout(function() {
      if (el.parentNode) el.parentNode.removeChild(el);
    }, 300);
  }

  function showModal(options) {
    const { title, message, type = 'success', buttonText = 'OK', callback } = options;

    const existing = document.querySelector('.modern-modal-overlay');
    if (existing) existing.remove();

    const icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-x-circle-fill',
      warning: 'bi-exclamation-circle-fill',
      info: 'bi-info-circle-fill'
    };

    const overlay = document.createElement('div');
    overlay.className = 'modern-modal-overlay active';
    overlay.innerHTML = `
      <div class="modern-modal">
        <div class="modal-icon ${type}"><i class="bi ${icons[type] || icons.success}"></i></div>
        <h3>${title}</h3>
        <p>${message}</p>
        <button class="btn-modal">${buttonText}</button>
      </div>
    `;

    document.body.appendChild(overlay);

    const btn = overlay.querySelector('.btn-modal');
    btn.addEventListener('click', function() {
      overlay.classList.remove('active');
      setTimeout(function() { overlay.remove(); }, 200);
      if (typeof callback === 'function') callback();
    });

    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) {
        overlay.classList.remove('active');
        setTimeout(function() { overlay.remove(); }, 200);
      }
    });
  }

  window.GihekeToast = { showToast, showModal };
})();
