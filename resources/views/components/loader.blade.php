<div id="splashScreen"
    style="
        position: fixed; inset: 0; z-index: -1; display: flex; opacity: 0; align-items: center; justify-content: center; overflow: hidden;
        background: url('{{ asset('images/splash-screen-transparent.png') }}') no-repeat center center / cover;
        background-color: #00000077;
        backdrop-filter: blur(2px);
    ">
    <div class="container"></div>

    <style>
        .container {
            --uib-size: 80px;
            --uib-color: white;
            --uib-speed: 1.4s;
            --uib-stroke: 5px;
            --uib-bg-opacity: .1;
            margin-top: 10rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: var(--uib-stroke);
            width: var(--uib-size);
            border-radius: calc(var(--uib-stroke) / 2);
            overflow: hidden;
            transform: translate3d(0, 0, 0);
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: var(--uib-color);
            opacity: var(--uib-bg-opacity);
            transition: background-color 0.3s ease;
        }

        .container::after {
            content: '';
            height: 100%;
            width: 100%;
            border-radius: calc(var(--uib-stroke) / 2);
            animation: zoom var(--uib-speed) ease-in-out infinite;
            transform: translateX(-100%);
            background-color: var(--uib-color);
            transition: background-color 0.3s ease;
        }

        @keyframes zoom {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }
    </style>


</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const splash = document.getElementById('splashScreen');

        window.triggerLoading = function(duration = 1) {
            splash.style.zIndex = "9999";
            splash.style.opacity = "1";
            splash.style.transition = `opacity ${duration}s ease`;
            return;
        }

        window.hideLoading = function(duration = 1) {
            splash.style.zIndex = "-1";
            splash.style.opacity = "0";
            splash.style.transition = `opacity ${duration} ease`;
            return;
        }

        document.querySelectorAll('form').forEach(defaultForm => {
            defaultForm.addEventListener("submit", function(e) {
                e.preventDefault();
                if (defaultForm.hasAttribute('data-no-loader')) return;
                triggerLoading(0.3);
                setTimeout(() => {
                    this.submit();
                }, 1000)
            })
        });

        document.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener("click", function(e) {
                const url = link.getAttribute('href');
                if (
                    !url ||
                    url.startsWith('#') ||
                    url.startsWith('javascript:') ||
                    link.target === '_blank' ||
                    (url.includes('://') && !url.includes(window.location.host)) ||
                    link.hasAttribute('data-no-loader')
                ) return;
                e.preventDefault();
                triggerLoading(0.3);

                setTimeout(() => {
                    window.location.href = url;
                }, 500)
            });
        });

    });
</script>
