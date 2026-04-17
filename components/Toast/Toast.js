// 🔥 FIXED PREMIUM TOAST v6 - Anti-Dupe + Visible Guaranteed
// High contrast, dedupe logic, stacking, accessibility

let toastId = 0;
const recentToasts = new Map(); // [key] = timestamp

function showToast(message, type = 'success', duration = 5000, position = 'top-right') {
  const key = `${type}:${message}`;
  const now = Date.now();
  
  // Dedupe: skip if identical toast <2s ago
  if (recentToasts.has(key) && now - recentToasts.get(key) < 2000) {
    console.log('⏭️ Duplicate toast skipped:', message);
    return;
  }
  recentToasts.set(key, now);
  
  // Cleanup old
  for (let [k, time] of recentToasts) {
    if (now - time > 10000) recentToasts.delete(k);
  }

  const id = `toast-${++toastId}`;
  const toast = document.createElement('div');
  toast.id = id;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'polite');
  toast.setAttribute('tabindex', '-1');
  toast.style.zIndex = '1000000';

  const positions = {
    'top-right': 'top: 20px; right: 20px;',
    'top-left': 'top: 20px; left: 20px;',
    'bottom-right': 'bottom: 20px; right: 20px;',
    'bottom-left': 'bottom: 20px; left: 20px;',
    'top-center': 'top: 20px; left: 50%; transform: translateX(-50%);',
    'bottom-center': 'bottom: 20px; left: 50%; transform: translateX(-50%);'
  };
  toast.style.position = 'fixed';
  toast.style.cssText += positions[position] || positions['top-right'];

  const themes = {
    success: { bg: '#10B981', icon: '#ECFDF5', title: '#FFFFFF', text: '#FFFFFF', border: '#059669', progress: '#34D399' },
    error: { bg: '#EF4444', icon: '#FEE2E2', title: '#FFFFFF', text: '#FFFFFF', border: '#DC2626', progress: '#F87171' },
    warning: { bg: '#F59E0B', icon: '#FEF3C7', title: '#FFFFFF', text: '#FFFFFF', border: '#D97706', progress: '#FCD34D' },
    info: { bg: '#3B82F6', icon: '#DBEAFE', title: '#FFFFFF', text: '#FFFFFF', border: '#2563EB', progress: '#60A5FA' }
  };
  const theme = themes[type] || themes.success;

  toast.style.cssText += `
    max-width: 420px; width: 90vw; padding: 20px 24px 16px; margin-bottom: 12px;
    border-radius: 16px; border: 3px solid ${theme.border};
    background: ${theme.bg} !important; color: ${theme.text} !important;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
    opacity: 0; transform: translateY(-20px); transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
    font-family: system-ui,-apple-system,sans-serif;
  `;

  const icons = {
    success: 'M9 12l2 2 4-4m-7 4l2 2 4-4',
    error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    warning: 'M12 9v2m0 4h.01',
    info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 0 11-18 0 9 9 0 0118 0z'
  };

  toast.innerHTML = `
    <div style="display: flex; align-items: flex-start; gap: 16px;">
      <div style="width: 56px; height: 56px; border-radius: 16px; background: ${theme.icon}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
        <svg style="width: 28px; height: 28px; stroke: ${theme.bg}; stroke-width: 2;" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="${icons[type]}"></path>
        </svg>
      </div>
      <div style="flex: 1; min-width: 0; padding-top: 4px;">
        <h3 style="font-size: 20px; font-weight: 800; color: ${theme.title} !important; margin: 0 0 8px 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">${type.charAt(0).toUpperCase() + type.slice(1)}</h3>
        <p style="font-size: 16px; font-weight: 600; color: ${theme.text} !important; margin: 0; line-height: 1.5; text-shadow: 0 1px 3px rgba(0,0,0,0.4); word-break: break-word;">${message}</p>
      </div>
      <button onclick="document.getElementById('${id}').remove()" style="margin-left: 12px; padding: 8px; border: none; background: none; color: ${theme.icon}; cursor: pointer; border-radius: 12px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; opacity: 0.9; transition: all 0.2s;" aria-label="Close">
        <svg style="width: 20px; height: 20px; stroke: currentColor; stroke-width: 2.5;" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <div style="height: 4px; background: rgba(255,255,255,0.5); border-radius: 2px; margin-top: 16px; overflow: hidden;">
      <div id="${id}-progress" style="height: 100%; background: linear-gradient(90deg, ${theme.progress}, #ffffff22); transform: scaleX(0); transform-origin: left; transition: transform ${duration}ms cubic-bezier(0.4,0,0.2,1);"></div>
    </div>
  `;

  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0) scale(1)';
  });

  toast.focus();

  setTimeout(() => {
    const progress = document.getElementById(`${id}-progress`);
    if (progress) progress.style.transform = 'scaleX(1)';
  }, 400);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-20px) scale(0.95)';
    setTimeout(() => toast.remove(), 400);
  }, duration);

  const handleKey = (e) => {
    if (e.key === 'Escape' && document.activeElement === toast) {
      toast.remove();
      document.removeEventListener('keydown', handleKey);
    }
  };
  document.addEventListener('keydown', handleKey);

  return toast;
}

window.showToast = showToast;

