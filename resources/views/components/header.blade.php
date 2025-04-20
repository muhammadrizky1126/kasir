@auth
<div class="navbar-bg" style="background: linear-gradient(to right, #ffff 60%, #4a1d6b 100%);"></div>
<nav class="navbar navbar-expand-lg main-navbar" style="background: linear-gradient(to right, #fffff 0%, #4a1d6b 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <!-- Sidebar Toggle Button on the Left -->

    <!-- Right Side Navbar Items - Simplified Version -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="nav-link nav-link-lg nav-link-user" style="color: #ffffff; font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 14px; display: flex; align-items: center;">
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-2" style="border: 2px solid #ffffff; width: 32px; height: 32px;">
                <span>{{ auth()->user()->name }}</span>
            </div>
        </li>
        <li class="nav-item ml-3">
            <a href="{{ route('logout') }}" class="nav-link nav-link-lg" style="color: #6a3093 100%; font-family: 'Poppins', sans-serif;"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');
.navbar {
    font-family: 'Poppins', sans-serif;
}
</style>
@endauth
