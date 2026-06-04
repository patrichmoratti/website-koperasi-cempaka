import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart  = Chart;

// Global toast helper
window.showToast = function(message, type = 'success', duration = 3500) {
    const toastId = 'toast-' + Date.now();
    const icons = {
        success: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
        danger:  `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        warning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
        info:    `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    };
    const div = document.createElement('div');
    div.id = toastId;
    div.className = `toast toast-${type}`;
    div.innerHTML = `${icons[type] || icons.info}<span>${message}</span>`;
    document.body.appendChild(div);
    setTimeout(() => { div.style.opacity = '0'; div.style.transition = 'opacity .3s'; setTimeout(() => div.remove(), 300); }, duration);
};

// Global currency formatter
window.formatRupiah = function(amount) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

// Chart.js defaults
Chart.defaults.font.family = 'DM Sans, sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.plugins.legend.labels.boxWidth = 12;
Chart.defaults.plugins.legend.labels.padding = 16;
Chart.defaults.plugins.tooltip.callbacks.label = function(context) {
    if (typeof context.raw === 'number') {
        return ' ' + new Intl.NumberFormat('id-ID').format(context.raw);
    }
    return context.formattedValue;
};

Alpine.start();
