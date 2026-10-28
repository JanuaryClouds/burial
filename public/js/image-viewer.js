// Delegation also handles viewers inserted by a Livewire morph.
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-image-viewer-url]');
    if (!button) return;
    const url = new URL(button.dataset.imageViewerUrl, window.location.origin);
    if (url.origin !== window.location.origin) return;
    event.preventDefault();
    window.open(url.href, 'imageViewer', 'width=1000,height=800,resizable=yes,scrollbars=yes');
});
