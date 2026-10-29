// One authorized request per image. Missing documents never become visible;
// service failures remain distinguishable from documents that were not uploaded.
const requests = new WeakMap();

function updateEmptyState(gallery) {
    if (!gallery) return;
    const slots = [...gallery.querySelectorAll('[data-document-slot]')];
    const empty = gallery.querySelector('[data-document-empty]');
    if (empty) empty.hidden = !slots.every(slot => slot.dataset.documentState === 'missing');
}

function release(state) {
    state.controller.abort();
    if (state.objectUrl) URL.revokeObjectURL(state.objectUrl);
}

async function loadImage(image) {
    const source = image.dataset.documentImageSrc;
    const previous = requests.get(image);
    if (previous?.source === source) return;
    if (previous) release(previous);

    const viewer = image.closest('[data-document-viewer]');
    const slot = image.closest('[data-document-slot]') || viewer;
    if (!viewer || !slot) return;
    const gallery = slot.closest('[data-document-gallery]');
    const button = viewer.querySelector('[data-image-viewer-url]');
    const message = viewer.querySelector('[data-document-message]');
    const state = { source, controller: new AbortController(), objectUrl: null };
    requests.set(image, state);
    slot.hidden = true;
    slot.dataset.documentState = 'pending';
    image.classList.add('d-none');
    button?.classList.add('d-none');
    if (message) message.hidden = true;
    updateEmptyState(gallery);

    try {
        const url = new URL(source, window.location.origin);
        if (url.origin !== window.location.origin) throw new Error('Invalid image origin');
        const response = await fetch(url.href, {
            credentials: 'same-origin', cache: 'no-store', redirect: 'error', signal: state.controller.signal,
        });
        if (!image.isConnected || requests.get(image) !== state) return;
        if (response.status === 404) {
            slot.dataset.documentState = 'missing';
            return;
        }
        if (!response.ok || response.headers.get('Content-Type')?.split(';')[0].trim() !== 'image/jpeg') {
            throw new Error('Image unavailable');
        }
        const blob = await response.blob();
        if (!image.isConnected || requests.get(image) !== state) return;
        state.objectUrl = URL.createObjectURL(blob);
        await new Promise((resolve, reject) => {
            image.onload = resolve;
            image.onerror = () => reject(new Error('Invalid image'));
            image.src = state.objectUrl;
        });
        if (!image.isConnected || requests.get(image) !== state) return;
        image.classList.remove('d-none');
        button?.classList.remove('d-none');
        slot.dataset.documentState = 'ready';
        slot.hidden = false;
    } catch {
        if (!image.isConnected || requests.get(image) !== state || state.controller.signal.aborted) return;
        slot.dataset.documentState = 'unavailable';
        if (message) {
            message.textContent = 'Image temporarily unavailable. Reload the page to try again.';
            message.hidden = false;
        }
        slot.hidden = false;
    } finally {
        if (state.objectUrl) {
            URL.revokeObjectURL(state.objectUrl);
            state.objectUrl = null;
        }
        if (requests.get(image) === state) {
            image.onload = null;
            image.onerror = null;
        }
        updateEmptyState(gallery);
    }
}

export function initImageViewers(root = document) {
    if (root.matches?.('img[data-document-image-src]')) loadImage(root);
    root.querySelectorAll?.('img[data-document-image-src]').forEach(loadImage);
    if (root.matches?.('[data-document-gallery]')) updateEmptyState(root);
    root.querySelectorAll?.('[data-document-gallery]').forEach(updateEmptyState);
}

// Handle initial markup, Livewire navigation and newly morphed document slots.
initImageViewers();
document.addEventListener('livewire:navigated', () => initImageViewers());
new MutationObserver(records => {
    for (const record of records) {
        for (const node of record.removedNodes) {
            if (node.isConnected) continue;
            const images = node.matches?.('img[data-document-image-src]') ? [node] : [...(node.querySelectorAll?.('img[data-document-image-src]') || [])];
            for (const image of images) {
                const state = requests.get(image);
                if (state) release(state);
                requests.delete(image);
            }
        }
        record.addedNodes.forEach(node => initImageViewers(node));
        updateEmptyState(record.target.closest?.('[data-document-gallery]'));
    }
}).observe(document.documentElement, { childList: true, subtree: true });

// Delegation also handles viewers inserted by a Livewire morph.
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-image-viewer-url]');
    if (!button) return;
    const url = new URL(button.dataset.imageViewerUrl, window.location.origin);
    if (url.origin !== window.location.origin) return;
    event.preventDefault();
    window.open(url.href, 'imageViewer', 'width=1000,height=800,resizable=yes,scrollbars=yes');
});
