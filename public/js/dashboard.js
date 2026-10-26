// Save the scroll offset before Livewire reconciles the activity list.
export default function initDashboard() {
    const scrollOffsets = new WeakMap();
    Livewire.hook('morph', ({ el }) => {
        const list = el.querySelector('[data-dashboard-activity-scroll]');
        if (list) scrollOffsets.set(el, list.scrollTop);
    });

    Livewire.hook('morphed', ({ el }) => {
        const list = el.querySelector('[data-dashboard-activity-scroll]');
        if (list) list.scrollTop = scrollOffsets.get(el) || 0;
    });
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-back-to-top]')) return;

    window.scrollTo({
        top: 0,
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
    });
});
