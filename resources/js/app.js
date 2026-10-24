import './bootstrap';
import { createIcons, icons } from 'lucide';

// Export createIcons to window so Blade components can call it dynamically
window.createIcons = createIcons;
window.lucideIcons = icons;

export function initLucide() {
    createIcons({ icons });
}

window.initLucide = initLucide;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLucide);
} else {
    initLucide();
}
