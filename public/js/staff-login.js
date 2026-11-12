document.addEventListener('alpine:init', () => {
    Alpine.data('staffLogin', () => ({
        remaining: 0, recovery: false, visible: false, timer: null,
        failed(detail) {
            this.resetFeedback();
            this.recovery = detail.recovery;
            this.remaining = detail.retryAfter;
            if (this.remaining > 0) {
                const until = Date.now() + this.remaining * 1000;
                this.timer = setInterval(() => {
                    this.remaining = Math.max(0, Math.ceil((until - Date.now()) / 1000));
                    if (this.remaining === 0) clearInterval(this.timer);
                }, 250);
            }
        },
        resetFeedback() {
            clearInterval(this.timer);
            this.remaining = 0;
            this.recovery = false;
        },
        destroy() { clearInterval(this.timer); }
    }));
});
