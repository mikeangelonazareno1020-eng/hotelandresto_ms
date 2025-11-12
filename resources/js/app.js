// Main app bootstrap
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

// Lucide icon initialization
function renderLucideIcons() {
    try {
        requestAnimationFrame(() => createIcons({ icons }));
    } catch (e) {
        console.error('Lucide render failed:', e);
    }
}

// Run on DOM ready and on window load (covers HMR/navigation)
document.addEventListener('DOMContentLoaded', renderLucideIcons);
window.addEventListener('load', renderLucideIcons);

// Re-render when Alpine mutates the DOM
document.addEventListener('alpine:init', () => {
    Alpine.effect(() => {
        renderLucideIcons();
    });
});

// Optional: manual refresh if you ever inject HTML via AJAX
window.refreshLucide = renderLucideIcons;

// Helpers
window.SwalPopup = function(icon = 'success', title = 'Done!', text = '') {
    Swal.fire({
        icon,
        title,
        text,
        confirmButtonColor: '#D92332',
    });
};

window.toggleLoading = function(btn, loading = true, text = 'Loading...') {
    if (loading) {
        btn.dataset.originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            ${text}
        `;
    } else {
        btn.disabled = false;
        btn.innerHTML = btn.dataset.originalText || 'Submit';
    }
};

window.toast = ({ icon = 'success', title = 'Done!' } = {}) => {
    const colors = { success: '#22C55E', error: '#EF4444', warning: '#F59E0B' };
    const hasHeader = !!document.getElementById('main-header');
    const hasSidebar = !!document.getElementById('sidebar');
    const position = hasSidebar ? 'top-end' : 'top';
    const containerClass = hasHeader && !hasSidebar ? 'mt-16' : '';

    Swal.fire({
        toast: true,
        position,
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        padding: 6,
        icon,
        title,
        background: '#111',
        color: '#FFD600',
        iconColor: colors[icon] || '#F59E0B',
        customClass: {
            container: containerClass,
            popup: 'rounded-lg shadow-md text-center p-1.5 text-[11px]',
            title: 'text-[11px] font-semibold',
            icon: 'scale-75',
        },
    });
};

