@if (auth()->check())
    @if (auth()->user()->roles()->count() == 0)
        <script nonce="{{ $nonce ?? '' }}">
            document.addEventListener('DOMContentLoaded', () => {
                fetch('/checksession', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => {
                        if (!r.ok) {
                            throw new Error(`Session check failed: ${r.status}`);
                        }
                        return r.json();
                    })
                    .catch(err => {
                        console.error('Session check error:', err);
                    });
            })
        </script>
    @endif
@endif
