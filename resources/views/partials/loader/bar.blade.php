<div class="loader-container"></div>

<style>
    .loader-container {
        --uib-size: 80px;
        --uib-color: white;
        --uib-speed: 1.4s;
        --uib-stroke: 5px;
        --uib-bg-opacity: .1;
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

    .loader-container::before {
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

    .loader-container::after {
        content: '';
        width: 100%;
        height: 100%;
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

    @media (prefers-reduced-motion: reduce) {
        .loader-container::after {
            animation: none;
            transform: translateX(0);
            opacity: 0.6;
        }
    }
</style>
