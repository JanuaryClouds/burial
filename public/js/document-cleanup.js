(() => {
    let pending = false;
    const forms = document.querySelectorAll('[data-cleanup-form]');

    const reset = () => {
        pending = false;
        forms.forEach(form => {
            form.removeAttribute('aria-busy');
            form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = false; });
            form.querySelectorAll('[data-cleanup-idle]').forEach(label => { label.hidden = false; });
            form.querySelectorAll('[data-cleanup-busy]').forEach(label => { label.hidden = true; });
        });
    };

    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (pending) {
                event.preventDefault();
                return;
            }
            pending = true;
            form.setAttribute('aria-busy', 'true');
            forms.forEach(item => item.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = true; }));
            form.querySelectorAll('[data-cleanup-idle]').forEach(label => { label.hidden = true; });
            form.querySelectorAll('[data-cleanup-busy]').forEach(label => { label.hidden = false; });
        });
    });

    window.addEventListener('pageshow', reset);
})();
