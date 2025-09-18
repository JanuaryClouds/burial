<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar justify-content-between px-4">
    <span class="d-flex align-items-baseline">
        <ul class="navbar-nav mr-3">
            <li><a href="#"
                    @click.prevent="sidebarMini = !sidebarMini"
                    class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
        <!-- <nav aria-label="Page breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach (Request::segments() as $url)
                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                        <a href="">{{ $url }}</a>
                    </li>
                @endforeach
            </ol>
        </nav> -->
    </span>
    <ul class="navbar-nav">
        <li class="dropdown">
            <a href="" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('images/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
            <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit"
                        class="btn w-100">
                        <span class="fw-medium">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </span>
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
