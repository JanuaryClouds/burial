document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-password-visibility]');
    if (!button) return;
    const input = button.closest('.input-group')?.querySelector('input');
    if (!input) return;
    const visible = input.type === 'password';
    input.type = visible ? 'text' : 'password';
    button.textContent = visible ? 'Hide' : 'Show';
    button.setAttribute('aria-pressed', String(visible));
    button.setAttribute('aria-label', `${visible ? 'Hide' : 'Show'} ${button.dataset.passwordLabel}`);
});
