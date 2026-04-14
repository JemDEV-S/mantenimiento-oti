import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);

window.Alpine = Alpine;

Alpine.store('sidebar', {
    open: false,
    activeModule: null,
    toggle(module) {
        if (this.activeModule === module && this.open) {
            this.open = false;
            this.activeModule = null;
        } else {
            this.activeModule = module;
            this.open = true;
        }
    },
    close() {
        this.open = false;
        this.activeModule = null;
    }
});

Alpine.store('commandPalette', {
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; }
});

Alpine.start();
