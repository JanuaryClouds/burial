<style>
    @font-face {
        font-family: 'FuturaCyrillic';
        src: url('/assets/fonts/FuturaCyrillicBook.ttf') format('truetype');
        font-weight: 400;
        font-display: swap;
    }

    @font-face {
        font-family: 'FuturaCyrillicBold';
        src: url('/assets/fonts/FuturaCyrillicBold.ttf') format('truetype');
        font-weight: 600;
        font-display: swap;
    }

    @font-face {
        font-family: 'FuturaCyrillicExtraBold';
        src: url('/assets/fonts/FuturaCyrillicExtraBold.ttf') format('truetype');
        font-weight: 800;
        font-display: swap;
    }

    /* Sizing Classes */
    /* Mobile-first approach */
    /* Default sizes */
    :root {
        /* Shared Values */
        --padding: clamp(1rem, 2.5vw, 2.5rem);
        --gap: clamp(1rem, 2vw, 2.5rem);
        --border-radius: clamp(1.25rem, 1.6vw, 2rem);
        --panel-border: rgba(0, 0, 0, .08);
        --card-accent-h: clamp(0.5rem, .5rem + .6vw, 0.7rem);
        --poster-pad: clamp(1rem, .9rem + .8vw, 1.6rem);
        --h2-mb: .4rem;
        --divider-mt: .2rem;
        --divider-mb: .85rem;
        --table-font: .95rem;
        --shadow-soft: 0 10px 24px rgba(16, 24, 40, .08);
        --shadow-card: 0 14px 28px rgba(26, 71, 152, .16);
        --shadow-float: 0 18px 36px rgba(26, 71, 152, .22);

        --taguig-blue: #1a4798;
        --taguig-red: #ed1c24;
        --taguig-yellow: #f4c027;
        --ink: #101010;
        --bg-grey: #f4f6f9;
        --bg-blue: #eaf3ff;
        --bs-primary: var(--taguig-blue);
        --bs-danger: var(--taguig-red);
        --bs-warning: var(--taguig-yellow);
        --bs-body-color: var(--ink);
        --bs-link-color: var(--taguig-blue);

        /* Unique Values */
        /* Topbar / Navbar */
        --nav-h: 8vh;
        --brand-logo: calc(var(--nav-h) * .65);
        --brand-gap: calc(var(--nav-h) * .1);
        --brand-title: calc(var(--nav-h) * 0.3);
        --brand-subtitle: calc(var(--brand-title) * 0.8);

        /* Hero */
        --title: calc(var(--body-text) * 1.75);
        --eyebrow: calc(var(--title) * .4);
        --quote: calc(var(--title) * .5);
        --arrow: clamp(3rem, 4vw, 6rem);
        --skyline-h: 17vh;

        /* Content */
        --content-pad: clamp(2.25rem, 2.8vw, 3.75rem);
        --content-gap: clamp(.65rem, .55rem + .6vw, 1rem);
        --gap-lg: 1.15rem;
        --head-text: calc(var(--body-text) * 1.2);
        --sub-text: calc(var(--body-text) * 0.75);
        --body-text: clamp(1rem, 2vw, 1.4rem);
        --reading-width: 68ch;
        --list-padding: .45rem 0 .45rem 1.5rem;
        --list-bullet-top: .7rem;
        --ribbon-radius: 1rem;
        --ribbon-padding: clamp(.45rem, 1.5vw, .7rem) clamp(.65rem, 2.5vw, 1rem);

        /* Components */
        --to-top-d: clamp(3rem, 3.2vw, 4rem);
    }

    /* start::Document */
    * {
        box-sizing: border-box
    }

    html,
    body {
        height: 100%;
    }

    html {
        scroll-behavior: smooth
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto
        }
    }

    body {
        font-family: "FuturaCyrillic", system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
        color: var(--ink);
        line-height: 1.6;
        background-color: var(--bg-grey);
        background-image: radial-gradient(1200px 380px at 10% -20%, rgba(244, 192, 39, .16), transparent 60%),
            radial-gradient(1000px 360px at 100% -10%, rgba(26, 71, 152, .12), transparent 55%);
        padding-top: var(--nav-h);
        overflow-x: hidden
    }

    h1,
    h2,
    h3,
    h4,
    .display-5 {
        color: var(--taguig-blue);
        letter-spacing: .01em
    }

    /* end::Document */

    /* start::topbar / Navbar */
    .topbar {
        height: var(--nav-h);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, .92);
        border-bottom: 1px solid rgba(16, 16, 16, .06);
        box-shadow: 0 2px 12px rgba(0, 0, 0, .04);
        backdrop-filter: saturate(130%) blur(6px);
        z-index: 1030;
        transition: box-shadow .25s ease, background-color .25s ease, border-color .25s ease
    }

    .topbar.is-scrolled {
        background: rgba(255, 255, 255, .98);
        border-color: rgba(16, 16, 16, .08);
        box-shadow: 0 10px 26px rgba(0, 0, 0, .08)
    }

    .topbar.over-hero {
        background: transparent;
        border-color: transparent;
        box-shadow: none
    }

    .topbar.peek {
        background: rgba(255, 255, 255, .98);
        border-bottom: 1px solid rgba(16, 16, 16, .08);
        box-shadow: 0 10px 26px rgba(0, 0, 0, .08)
    }

    .brand {
        display: flex;
        align-items: center;
        gap: var(--brand-gap);
        min-width: 0;
        text-decoration: none
    }

    .logo {
        width: var(--brand-logo);
        height: var(--brand-logo);
        border-radius: 50%;
        object-fit: contain
    }

    .brandtext {
        display: none;
        flex-direction: column;
        line-height: 1.1;
        min-width: 0
    }

    .brand-title {
        font-weight: 600;
        color: #1b1b1b;
        font-size: var(--brand-title);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .brand-subtitle {
        color: #6b7280;
        font-size: var(--brand-subtitle);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .topbar.over-hero .brandtitle,
    .topbar.over-hero .brandsub {
        text-shadow: 0 1px 2px rgba(0, 0, 0, .08)
    }

    .right {
        display: flex;
        align-items: center;
        gap: .75rem
    }

    .timewrap {
        font-family: "FuturaCyrillicBold";
        display: flex;
        align-items: center;
        gap: .5rem;
        font-weight: 700;
        white-space: nowrap
    }

    .time-label {
        color: #374151;
        font-size: .9rem
    }

    .time {
        font-variant-numeric: tabular-nums;
        background: #eef3ff;
        border: 1px solid #d8e3ff;
        padding: .35rem .6rem;
        display: none;
        border-radius: 999px;
        color: #1a3f94;
        letter-spacing: .02em
    }

    .time-label {
        display: none
    }

    .topbar.over-hero .time {
        background: rgba(255, 255, 255, .55);
        border-color: transparent;
        color: #0f214a
    }

    .search {
        display: flex;
        align-items: center;
        gap: .4rem
    }

    .search .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 999px;
        border: 1px solid #d8e3ff;
        background: #fff;
        color: var(--taguig-blue)
    }

    .topbar.over-hero .search .btn-icon {
        background: rgba(255, 255, 255, .55);
        border-color: transparent;
        color: #0f214a
    }

    .search .form-control {
        width: 0;
        opacity: 0;
        padding-left: 0;
        padding-right: 0;
        border-color: transparent;
        transition: width .25s ease, opacity .2s ease, padding .2s ease, border-color .25s ease, background-color .25s ease
    }

    .search.open .form-control {
        width: min(260px, 58vw);
        opacity: 1;
        padding: 0 .75rem;
        border-color: #d8e3ff;
        background: #fff
    }

    .topbar.over-hero .search.open .form-control {
        background: rgba(255, 255, 255, .85);
        border-color: transparent;
        color: #0f214a
    }

    .search-meta {
        display: none;
        align-items: center;
        gap: .35rem;
        background: #eef3ff;
        border: 1px solid #d8e3ff;
        color: #1a3f94;
        border-radius: 999px;
        padding: .25rem .5rem;
        font-weight: 700;
        font-size: .85rem
    }

    .search.has-results .search-meta {
        display: inline-flex
    }

    .search-meta .navbtn {
        background: transparent;
        border: 0;
        padding: .1rem .25rem;
        font-weight: 900;
        line-height: 1;
        cursor: pointer
    }

    mark.search-hit {
        background: #ffe58a;
        padding: .05em .15em;
        border-radius: .2em
    }

    mark.search-current {
        background: #ffd24d;
        box-shadow: 0 0 0 2px rgba(255, 210, 77, .5)
    }

    .search.open .form-control {
        width: min(220px, 72vw)
    }

    /* end::Topbar */

    /* start::Hero */
    .hero {
        height: calc(100vh - var(--nav-h));
        padding-block: var(--nav-h);
        background-image: url("/images/footer-trim.png"), url("/images/sunray.jpg");
        background-repeat: repeat-x, no-repeat;
        background-position: bottom center, 50% 35%;
        background-size: auto var(--skyline-h), cover;
        background-color: #eaf3ff;
        display: grid;
        place-items: center;
        position: relative;
        isolation: isolate
    }

    @supports not (height:1svh) {
        .hero {
            min-height: calc(100vh - var(--nav-h))
        }
    }

    .hero>.container {
        position: relative;
        z-index: 1
    }

    .center {
        max-width: 95vw;
        margin-inline: auto;
        text-align: center
    }

    .title {
        color: var(--taguig-blue);
        font-family: "FuturaCyrillicExtraBold";
        font-weight: 800;
        line-height: 1.12;
        letter-spacing: .01em;
        font-size: var(--title);
        margin: .2rem 0 .4rem;
        text-wrap: balance
    }

    .quote {
        margin: 0 0 1rem;
        color: var(--taguig-red);
        font-weight: 600;
        font-style: italic;
        font-size: var(--quote);
        font-family: "Brush Script MT", "Segoe Script", "Lucida Handwriting", "Apple Chancery", "FuturaCyrillic",
            cursive
    }

    .actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-direction: column;
        align-items: stretch;
        margin-top: 3rem;
    }

    .actions .btn {
        border-radius: 999px;
        font-weight: 700;
        white-space: nowrap;
        width: 100%;
        font-size: var(--body-text);
    }

    .actions .btn-outline-primary {
        background: #fff
    }

    .hero-arrow {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: calc(var(--skyline-h) - 11vh);
        width: var(--arrow);
        height: var(--arrow);
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        color: var(--taguig-blue);
        border: 2px solid #d8e3ff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 26px rgba(16, 16, 16, .08);
        backdrop-filter: saturate(130%) blur(6px);
        cursor: pointer;
        z-index: 2;
        transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        animation: fas-bounce 1.6s ease-in-out infinite
    }

    .hero-arrow:hover {
        transform: translateX(-50%) translateY(2px)
    }

    .hero-arrow i {
        font-size: calc(var(--arrow) * .35);
    }

    @keyframes fas-bounce {

        0%,
        100% {
            transform: translateX(-50%) translateY(0)
        }

        50% {
            transform: translateX(-50%) translateY(6px)
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .hero-arrow {
            animation: none
        }
    }

    .hero-wrap.section-block {
        background: #fff;
        padding-block: calc(var(--content-pad) + .35rem)
    }

    .hero-img {
        max-width: 480px;
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: var(--shadow-card)
    }

    .eyebrow {
        color: var(--taguig-red);
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        font-style: italic;
        font-size: var(--eyebrow);
        font-family: "FuturaCyrillicExtraBold";
    }

    /* end::Hero */

    /* start::Section */
    .section {
        /* padding: var(--padding); */
        padding-block: calc(var(--padding) * 2.5);
        scroll-margin-top: var(--nav-h);
        font-size: var(--body-text);
    }

    .section-head {
        display: grid;
        gap: .35rem;
        margin-bottom: var(--content-gap)
    }

    .section-head h2 {
        font-weight: 600;
        letter-spacing: .02em;
        font-size: var(--head-text);
    }

    .section-body {
        display: grid;
        gap: var(--gap);
    }

    .section-body> :where(p, ul, ol) {
        max-width: var(--reading-width);
        justify-self: start
    }

    .rule {
        height: 3px;
        width: 64px;
        background: var(--taguig-blue);
        border-radius: 999px
    }

    .note {
        color: #545454
    }

    .motion-ready .js-reveal {
        opacity: 0;
        transform: translate3d(0, 18px, 0);
        transition: opacity .56s cubic-bezier(.22, 1, .36, 1), transform .56s cubic-bezier(.22, 1, .36, 1);
        transition-delay: var(--reveal-delay, 0ms);
        will-change: opacity, transform
    }

    .motion-ready .js-reveal.is-revealed {
        opacity: 1;
        transform: translate3d(0, 0, 0);
        will-change: auto
    }

    .motion-ready .divider.js-reveal {
        transform: scaleX(.72);
        transform-origin: left center
    }

    .motion-ready .divider.js-reveal.is-revealed {
        transform: scaleX(1)
    }

    .tint-grey {
        background: linear-gradient(180deg, #f7f9fc 0%, #f1f4f8 100%)
    }

    .tint-blue {
        background: linear-gradient(180deg, #eef5ff 0%, #e7f0ff 100%)
    }

    /* end::Section */

    /* start::Card */
    .card {
        background: #fff;
        border: 1px solid var(--panel-border);
        border-radius: var(--border-radius);
        /* padding: var(--padding); */
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease
    }

    .card-subtitle {
        text-align: center;
        color: var(--taguig-blue);
        font-weight: 800;
        font-size: clamp(1.05rem, .95rem + .6vw, 1.25rem);
        margin: 0 0 .35rem
    }

    :where(.card-body-dashed, .card-body) {
        padding: var(--padding);
        /* border-radius: 0 0 var(--border-radius) var(--border-radius); */
        /* background: #fff; */
        display: flex;
        flex-flow: column;
        flex-grow: 0;
        gap: var(--content-gap);
        font-size: var(--body-text);
    }

    .card-body-dashed {
        border: 2px dashed #9fb7ea;
        position: relative
    }

    .card-body {
        border: 1px solid #dbe7ff;
        border-top: 0
    }

    .card-title {
        font-family: "FuturaCyrillicExtraBold";
        text-align: center;
        color: var(--taguig-blue);
        font-weight: 800;
        font-size: var(--head-text);
        margin: .4rem 0 1rem
    }

    .card-body ol>li {
        font-size: var(--body-text);
    }

    .card-footer-wrapper {
        display: flex;
        gap: .75rem;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: .6rem
    }

    .card-footer-logo {
        height: 36px;
        width: auto;
        filter: saturate(1.1)
    }

    :where(.card-accent-a, .card-accent-b, .card-accent-c) {
        height: var(--card-accent-h);
        display: flex;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        overflow: hidden
    }

    .card-accent-a .r {
        background: var(--taguig-red);
        flex: 2
    }

    .card-accent-a .y {
        background: var(--taguig-yellow);
        flex: 1
    }

    .card-accent-a .b {
        background: var(--taguig-blue);
        flex: 3
    }

    .card-accent-b .b {
        background: var(--taguig-blue);
        flex: 3
    }

    .card-accent-b .r {
        background: var(--taguig-red);
        flex: 1
    }

    .card-accent-b .y {
        background: var(--taguig-yellow);
        flex: .8
    }

    .card-accent-c .r {
        background: var(--taguig-red);
        flex: 1
    }

    .card-accent-c .y {
        background: var(--taguig-yellow);
        flex: 1
    }

    .card-accent-c .b {
        background: var(--taguig-blue);
        flex: 2
    }

    /* start::Content */
    .list-number {
        list-style: none;
        counter-reset: n;
        padding-left: 0;
        margin: 0
    }

    .list-number li {
        counter-increment: n;
        position: relative;
        padding: var(--list-padding);
        line-height: 1.5
    }

    .list-number li::before {
        content: counter(n);
        position: absolute;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        left: 0;
        top: var(--list-bullet-top);
        width: var(--body-text);
        height: var(--body-text);
        line-height: 1.5rem;
        text-align: center;
        border-radius: 50%;
        background: var(--taguig-blue);
        color: #fff;
        font-weight: 800;
        font-size: calc(var(--body-text) * .5);
    }

    hr {
        height: 1px;
        background: #e5edf9;
        border: 0;
        margin: 1rem 0 .9rem
    }

    .chip {
        display: inline-block;
        background: var(--taguig-red);
        color: #fff;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
        border-radius: 8px;
        padding: .3rem .55rem;
        font-size: .78rem;
        margin-bottom: .35rem
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: var(--table-font);
        overflow: hidden;
        border: 1px solid #e9eef8
    }

    .table thead th {
        background: var(--taguig-red);
        color: #fff;
        text-transform: uppercase;
        letter-spacing: .02em;
        font-weight: 800;
        font-size: .85rem;
        border: 0
    }

    .table th,
    .table td {
        padding: .5rem .6rem
    }

    .table tbody td {
        border-top: 1px solid #f0f3fa
    }

    .table tbody tr:nth-child(even) {
        background: #fbfcff
    }

    .totals {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .65rem;
        margin-top: .55rem
    }

    .ribbon {
        font-family: "FuturaCyrillicExtraBold";
        display: inline-block;
        background: var(--taguig-red);
        color: #fff;
        text-transform: uppercase;
        padding: var(--ribbon-padding);
        border-radius: calc(var(--ribbon-radius) * .5);
        letter-spacing: .02em;
        font-size: var(--sub-text);
        margin-bottom: .5rem;
        max-width: fit-content;
        max-height: fit-content;
    }

    .box h3 {
        margin: 0 0 .35rem;
        color: #000;
        font-weight: 800;
        font-size: 1rem
    }

    .total-box {
        background: var(--taguig-blue);
        color: #fff;
        border-radius: 10px;
        padding: .5rem .65rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 800
    }

    .total-box .val {
        font-size: 1rem
    }

    .swa-eq {
        margin-top: .7rem;
        background: #fff;
        border: 2px solid #ffe4e4;
        border-radius: 12px;
        padding: .65rem .8rem;
        display: flex;
        flex-wrap: wrap;
        gap: .4rem .55rem;
        align-items: center;
        justify-content: center
    }

    .swa-eq .label {
        font-weight: 900;
        color: #000
    }

    .swa-eq .key {
        font-weight: 800;
        color: #b10e14
    }

    .swa-pill {
        display: inline-block;
        background: var(--taguig-red);
        color: #fff;
        font-weight: 900;
        border-radius: 10px;
        padding: .25rem .55rem;
        font-size: 1rem;
        min-width: 58px;
        text-align: center
    }

    .list-check {
        margin: 0 0 .75rem 0;
        padding: 0;
        list-style: none
    }

    .list-check li {
        position: relative;
        padding: var(--list-padding);
        line-height: 1.5
    }

    .list-check li::before {
        content: "\2713";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        left: 0;
        top: var(--list-bullet-top);
        width: var(--body-text);
        height: var(--body-text);
        border-radius: 50%;
        background: var(--taguig-red);
        color: #fff;
        font-weight: 900;
        font-size: calc(var(--body-text) * .5);
    }

    .dl-grid {
        display: grid;
        gap: var(--gap-lg);
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))
    }

    .dl-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: nowrap
    }

    .dl-ico {
        flex: 0 0 56px;
        height: 56px;
        width: 56px;
        border-radius: 14px;
        background: var(--bg-blue);
        border: 1px solid #dbe7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--taguig-blue);
        font-size: 24px
    }

    .dl-body {
        flex: 1 1 auto;
        min-width: 0
    }

    .dl-title {
        margin: 0;
        font-weight: 800;
        color: #000;
        font-size: 1rem
    }

    .dl-sub {
        margin: 0;
        color: #5b6575;
        font-size: .9rem
    }

    .btn-download {
        border-radius: 999px;
        font-weight: 800;
        white-space: nowrap;
        flex: 0 0 auto;
        margin-left: auto;
        padding: .8rem 1.2rem
    }

    .gallery-grid {
        display: grid;
        gap: .8rem;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr))
    }

    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        cursor: zoom-in;
        padding: 0;
        background: #f8fbff
    }

    .gallery-thumb {
        width: 100%;
        height: 0;
        padding-bottom: 66.66%;
        background-size: cover;
        background-position: center;
        display: block;
        transition: transform .3s ease
    }

    .gallery-cap {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .6) 100%);
        color: #fff;
        padding: .45rem .6rem;
        font-size: .85rem;
        transform: translateY(0);
        transition: opacity .2s ease;
        opacity: .92
    }

    .gallery-item:hover .gallery-cap,
    .gallery-item:focus-within .gallery-cap {
        opacity: 1
    }

    .gallery-item:focus-visible {
        outline: 3px solid var(--taguig-yellow);
        outline-offset: 2px
    }

    @media (hover: hover) {
        .panel:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-card);
            border-color: rgba(26, 71, 152, .2)
        }

        .gallery-item:hover .gallery-thumb {
            transform: scale(1.03)
        }
    }

    .lightbox-img {
        max-width: 100%;
        max-height: calc(100vh - 180px);
        border-radius: 12px
    }

    .lb-controls {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        pointer-events: none
    }

    .lb-btn {
        pointer-events: auto;
        border: 0;
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: rgba(0, 0, 0, .5);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center
    }

    .lb-btn:hover {
        background: rgba(0, 0, 0, .65)
    }

    .lb-meta {
        font-weight: 700
    }

    /* end::Content */

    /* start::Components */
    #requirements .stamp,
    #ceaa-requirements .stamp,
    #tlc-requirements .stamp {
        right: -48px;
        width: 170px
    }

    .totals {
        grid-template-columns: 1fr
    }

    /* start::Divider */
    .divider {
        display: flex;
        gap: 0;
        height: 6px;
        border-radius: 999px;
        overflow: hidden;
        margin: var(--divider-mt) 0 var(--divider-mb)
    }

    .divider .b {
        flex: 1;
        background: var(--taguig-blue)
    }

    .divider .y {
        flex: 1;
        background: var(--taguig-yellow)
    }

    .divider .r {
        flex: 1;
        background: var(--taguig-red)
    }

    /* end::Divider */

    /* end::Components */

    /* start::Footer */
    footer {
        border-top: 4px solid var(--taguig-blue);
        background: #fff;
    }

    .footer-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: center;
        gap: .75rem
    }

    .footer-bar {
        flex-direction: column;
        flex-wrap: wrap;
    }

    .footer-social {
        width: 100%;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
    }

    .footer-dot {
        display: none;
    }

    .footer-meta {
        display: flex;
        align-items: center;
        gap: .2rem .5rem;
        color: #223052;
        margin: 0.2rem 0 0.2rem 0;
    }

    .footer-meta .footer-dot {
        color: #94a7c6
    }

    .footer-meta-item {
        font-size: calc(var(--body-text) * .8);
    }

    .footer-social {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .45rem;
    }

    .social-link {
        font-family: "FuturaCyrillicBold";
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: #eef3ff;
        border: 1px solid #d8e3ff;
        border-radius: 999px;
        font-weight: 700;
        font-size: calc(var(--body-text) * .75);
        color: #0f214a;
        text-decoration: none;
        justify-content: center;
        padding: .75rem .6rem;
    }

    .social-link i {
        font-size: var(--body-text)
    }

    .social-link:hover {
        background: #fff;
        border-color: #c9d8ff;
        text-decoration: none;
        transform: translateY(-1px)
    }

    .social-link span {
        line-height: 1.1
    }

    /* end::Footer */

    /* start::Miscellaneous */
    #requirements .stamp,
    #ceaa-requirements .stamp,
    #tlc-requirements .stamp {
        position: absolute;
        top: 14px;
        right: -56px;
        width: 200px;
        height: 36px;
        background: var(--taguig-red);
        color: #fff;
        border: 0;
        border-radius: 0;
        box-shadow: 0 12px 24px rgba(237, 28, 36, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        transform: rotate(45deg);
        pointer-events: none;
        z-index: 3
    }

    #requirements .stamp::before,
    #requirements .stamp::after,
    #ceaa-requirements .stamp::before,
    #ceaa-requirements .stamp::after,
    #tlc-requirements .stamp::before,
    #tlc-requirements .stamp::after {
        content: "";
        position: absolute;
        top: 0;
        width: 0;
        height: 0;
        border-top: 18px solid transparent;
        border-bottom: 18px solid transparent
    }

    #requirements .stamp::before,
    #ceaa-requirements .stamp::before,
    #tlc-requirements .stamp::before {
        left: -18px;
        border-right: 18px solid var(--taguig-red)
    }

    #requirements .stamp::after,
    #ceaa-requirements .stamp::after,
    #tlc-requirements .stamp::after {
        right: -18px;
        border-left: 18px solid var(--taguig-red)
    }

    #requirements .stamp i,
    #ceaa-requirements .stamp i,
    #tlc-requirements .stamp i {
        font-size: 16px;
        color: #fff
    }

    :where(.callout, .callout-highlight) {
        padding: .9rem .9rem;
        border-radius: 12px;
        font-size: var(--body-text);
    }

    .callout {
        background: #f7faff;
        border: 1px solid #dbe7ff;
    }

    .callout-highlight {
        background: #fff9e4;
        border: 1px solid #f3d660;
    }

    .callout .dot {
        opacity: .5;
        margin: 0 .35rem
    }

    .tiles {
        display: grid;
        gap: var(--gap-lg);
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        align-items: start
    }

    .cards-2 {
        display: grid;
        gap: var(--gap)
    }

    .tile {
        display: flex;
        flex-direction: column;
        gap: .35rem
    }

    .tile-badge {
        display: inline-block;
        font-weight: 800;
        text-transform: uppercase;
        color: #fff;
        letter-spacing: .02em;
        border-radius: 999px;
        padding: .35rem .7rem;
        background: var(--taguig-blue);
        font-size: .85rem
    }

    .pill {
        display: inline-block;
        background: var(--taguig-red);
        color: #fff;
        font-weight: 800;
        padding: .35rem .65rem;
        border-radius: 999px;
        letter-spacing: .02em;
        font-size: .9rem
    }

    .table {
        font-size: var(--table-font)
    }

    .table-sm>:not(caption)>*>* {
        padding: .35rem .5rem
    }

    #toTop {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        z-index: 1030;
        width: var(--to-top-d);
        height: var(--to-top-d);
        border-radius: 999px;
        border: 0;
        padding: 0;
        background: transparent;
        color: var(--taguig-blue);
        display: none;
        align-items: center;
        justify-content: center;
        --progress: 0deg;
        transition: transform .2s ease
    }

    #toTop i {
        position: relative;
        z-index: 2;
        color: var(--taguig-blue);
        font-size: calc(var(--to-top-d) * .35);
        transition: transform .2s ease
    }

    #docQuick {
        position: fixed;
        right: 1rem;
        bottom: calc(1rem + 56px + 14px);
        z-index: 1030;
        width: 56px;
        height: 56px;
        border-radius: 999px;
        border: 0;
        padding: 0;
        background: var(--taguig-blue);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 24px rgba(26, 71, 152, .25);
        transition: transform .2s ease, box-shadow .2s ease
    }

    #docQuick .docquick-icon {
        position: relative;
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center
    }

    #docQuick .docquick-icon i:first-child {
        font-size: 22px
    }

    #docQuick .docquick-icon i:last-child {
        position: absolute;
        right: -2px;
        bottom: -2px;
        font-size: 11px
    }

    #docQuick.show {
        display: flex
    }

    #toTop::before,
    #toTop::after {
        content: "";
        position: absolute;
        border-radius: inherit;
        pointer-events: none
    }

    #toTop::before {
        inset: 0;
        background: conic-gradient(var(--taguig-blue) var(--progress), #dbe3f4 0deg);
        z-index: 0
    }

    #toTop::after {
        inset: 6px;
        background: #fff;
        border: 1px solid #d8e3ff;
        box-shadow: 0 12px 24px rgba(26, 71, 152, .18);
        z-index: 1;
        transition: box-shadow .2s ease
    }

    #toTop.show {
        display: flex
    }

    @media (hover: hover) {
        #toTop:hover {
            transform: translateY(-2px)
        }

        #toTop:hover::after {
            box-shadow: var(--shadow-float)
        }

        #docQuick:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-float)
        }
    }

    :focus-visible {
        outline: 3px solid var(--taguig-yellow);
        outline-offset: 3px;
        border-radius: .25rem
    }

    @media (prefers-reduced-motion: reduce) {

        .motion-ready .js-reveal,
        .motion-ready .js-reveal.is-revealed {
            opacity: 1;
            transform: none;
            transition: none
        }
    }

    /* end::Miscellaneous */

    /* large phones */
    @media (min-width: 576px) {
        .actions {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
        }

        .tiles {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr))
        }

        .dl-grid {
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr))
        }

        .gallery-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr))
        }

        .dl-card {
            flex-wrap: wrap;
            align-items: flex-start
        }

        .dl-body {
            order: 2;
            width: 100%
        }

        .btn-download {
            order: 3;
            width: 100%;
            margin-left: 0;
            margin-top: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center
        }
    }

    /* tablets */
    @media (min-width: 768px) {
        :root {
            --nav-h: 74px;
            --body-text: clamp(1.2rem, 2vw, 2rem);
            --title: calc(var(--body-text) * 2);
        }

        .brandtext {
            display: none;
        }

        .actions .btn {
            max-width: fit-content;
            font-size: calc(var(--body-text) * 1.25);
            padding: .75rem 1.5rem;
        }

        .time {
            display: block
        }
    }

    /* small desktops */
    @media (min-width: 1024px) {
        :root {
            --nav-h: 74px;
            --body-text: clamp(1rem, 1vw, 2rem);
            --skyline-h: 20vh;
            --card-accent-h: 0.65rem;
            --padding: clamp(1rem, 2vw, 2.25rem);
            --gap: clamp(1.2rem, 1.5vw, 2rem);
            --to-top-d: clamp(3.5rem, 4.5vw, 5rem);
            --border-radius: 0.75rem;
            --list-padding: 0.45rem 0 0.45rem 1.5rem;
            --list-bullet-top: 0.75rem;
            --ribbon-radius: 0.5rem;
            --ribbon-padding: 0.45rem 0.65rem;
            --title: calc(var(--body-text) * 3.5);
            --head-text: calc(var(--body-text) * 1.75);
            --arrow: clamp(3.5rem, 4vw, 6rem);
        }

        .brandtext {
            display: flex;
        }

        .actions .btn {
            width: auto
        }

        .footer-bar {
            flex-wrap: nowrap;
            flex-direction: row;
        }

        .footer-meta {
            flex-wrap: nowrap;
            width: 50%;
        }

        .footer-social {
            margin: 0;
            display: flex;
            justify-content: flex-end;
            flex-direction: row;
            gap: .5rem;
        }

        .footer-meta {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
        }

        .footer-dot {
            display: block;
        }

        .footer-meta-item {
            width: fit-content;
            font-size: 1rem;
        }

        .social-link {
            font-size: calc(var(--body-text));
            gap: .5rem;
        }

        .cards-2 {
            grid-template-columns: 1fr 1fr
        }
    }

    /* large desktops */
    @media (min-width: 1280px) {
        .time-label {
            display: block
        }
    }
</style>
