import './bootstrap';

import Alpine from 'alpinejs';
import NProgress from 'nprogress';

NProgress.configure({
    showSpinner: false,
    speed: 300,
    minimum: 0.1,
    trickleSpeed: 200,
});
window.Alpine = Alpine;

Alpine.start();
/* ==========================================================
   NProgress — top loading bar on navigation
   ========================================================== */

// Start on internal link click
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (! link) return;

    const href = link.getAttribute('href');
    if (! href) return;
    if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
    if (link.target === '_blank') return;
    if (link.hasAttribute('download')) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if (link.hostname && link.hostname !== window.location.hostname) return;

    NProgress.start();
});

// Start on any form submit
document.addEventListener('submit', () => {
    NProgress.start();
});

// Finish when page loads
window.addEventListener('load', () => NProgress.done());

// Finish when page restored from bfcache (browser back/forward)
window.addEventListener('pageshow', (e) => {
    if (e.persisted) NProgress.done();
});