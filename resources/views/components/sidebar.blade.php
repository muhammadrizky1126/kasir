@auth
<div class="main-sidebar sidebar-style-2" style="background: linear-gradient(to bottom, #ffffff 0%, #f8f1ff 60%, #6a3093 100%);">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand" style="padding: 20px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <a href="" style="color: #6a3093; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.5rem; text-transform: uppercase; letter-spacing: 1px;">TAKASIR</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="" style="color: #6a3093; font-weight: 700;">TS</a>
        </div>

        <ul class="sidebar-menu" style="margin-top: 20px;">

            <li class="{{ Request::is('home') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ url('home') }}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-tachometer-alt" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- superadmin --}}
            @if (Auth::user()->role == 'superadmin')
            {{-- produk master --}}

            <li class="{{ Request::is('product') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('products.index') }}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-box-open" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Produk</span>
                </a>
            </li>
            <li class="{{ Request::is('sales') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('sales.index') }}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-cash-register" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Penjualan</span>
                </a>
            </li>

            {{-- user master --}}

            <li class="{{ Request::is('user') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('user.index')}}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-users-cog" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>User</span>
                </a>
            </li>
            <li class="{{ Request::is('members') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('members.index')}}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-id-card" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Member</span>
                </a>
            </li>
            @endif

            @if (Auth::user()->role == 'user')
            <li class="menu-header" style="color: #6a3093; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 15px;">Menu</li>
            <li class="{{ Request::is('product') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('products.index') }}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-box-open" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Produk</span>
                </a>
            </li>
            <li class="{{ Request::is('sales') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('sales.index') }}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-cash-register" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Penjualan</span>
                </a>
            </li>

            {{-- user master --}}
            <li class="menu-header" style="color: #6a3093; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-top: 15px;">Member</li>
            <li class="{{ Request::is('members') ? 'active' : '' }}" style="border-left: 3px solid transparent;">
                <a class="nav-link" href="{{ route('members.index')}}" style="color: #4a4a4a; font-family: 'Poppins', sans-serif; font-size: 0.9rem;">
                    <i class="fas fa-id-card" style="color: #6a3093; width: 20px; text-align: center;"></i>
                    <span>Member</span>
                </a>
            </li>
            @endif
        </ul>
    </aside>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
.sidebar-menu li.active {
    border-left: 3px solid #6a3093 !important;
    background-color: rgba(106, 48, 147, 0.1);
}
.sidebar-menu li.active a {
    color: #6a3093 !important;
    font-weight: 500;
}
.sidebar-menu li:hover:not(.menu-header) {
    background-color: rgba(106, 48, 147, 0.1);
}
</style>
@endauth
