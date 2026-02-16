<nav class="topbar" role="navigation" aria-label="Primary">
    <div class="container-fluid d-flex align-items-center justify-content-between h-100 gap-3 px-3">
        <a href="#" class="brand">
            <img src="{{ asset('images/ito-logo.png') }}" alt="Taguig IT logo" class="logo">
            <img src="{{ asset('images/CSWDO.webp') }}" alt="Taguig scholar logo" class="logo">
            <span class="brandtext">
                <span class="brand-title">City Social Welfare & Development Office</span>
                <span class="brand-subtitle">City Government of Taguig</span>
            </span>
        </a>

        <div class="right">
            <div class="search" id="navSearch">
                <button class="btn-icon" id="searchToggle" aria-label="Open search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <form id="searchForm" class="d-flex align-items-center" role="search" aria-label="Site search"
                    autocomplete="off">
                    <input id="searchInput" class="form-control form-control-sm" type="search"
                        placeholder="Search this page…" aria-label="Search input">
                </form>
                <div class="search-meta" id="searchMeta" aria-live="polite">
                    <button class="navbtn" id="prevHit" title="Previous result"
                        aria-label="Previous result">‹</button>
                    <span id="hitCount">0/0</span>
                    <button class="navbtn" id="nextHit" title="Next result" aria-label="Next result">›</button>
                    <button class="navbtn" id="clearSearch" title="Clear" aria-label="Clear search">×</button>
                </div>
            </div>
            <div class="timewrap" aria-live="polite">
                <span class="time-label">Philippine Time</span>
                <span id="ph-time" class="time">Loading…</span>
            </div>
        </div>
    </div>
</nav>
