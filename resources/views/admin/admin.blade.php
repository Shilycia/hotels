<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Admin Panel') – Hotel Neo</title>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        :root {
            --sand: #f7f3ee;
            --sand2: #ede8e0;
            --sand3: #e4ddd3;
            --stone: #c8bfb0;
            --bark: #8b7355;
            --bark-soft: #f4ede4;
            --moss: #4a7c59;
            --moss-soft: #edf4ef;
            --clay: #c07850;
            --clay-soft: #fdf0e8;
            --slate: #5a6a7a;
            --slate-soft: #eef1f5;
            --ink: #2c2420;
            --ink2: #6b5e54;
            --ink3: #9e9088;
            --sidebar-bg: #2c2820;
            --sidebar-w: 220px;
            --surface: #ffffff;
            --radius: 10px;
            --radius-sm: 7px;
            --shadow-sm: 0 1px 3px rgba(44,36,32,0.06);
            --shadow: 0 4px 16px rgba(44,36,32,0.08);
            --transition: .2s ease;
        }

        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--sand);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            font-size: 13.5px;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed; top: 0; left: 0; z-index: 100;
            display: flex; flex-direction: column;
            transition: transform var(--transition);
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 22px 18px 18px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .brand-icon {
            width: 34px; height: 34px; border-radius: 8px;
            background: #c8a96e;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-icon i { font-size: 14px; color: #2c2820; }
        .brand-name { font-family: 'Lora', serif; font-size: 15px; color: #f0ebe3; font-style: italic; }
        .brand-sub { font-size: 10px; color: rgba(240,235,227,0.35); margin-top: 2px; letter-spacing: 0.8px; }

        .sidebar-section {
            padding: 16px 18px 5px;
            font-size: 9.5px; font-weight: 500;
            color: rgba(240,235,227,0.25);
            letter-spacing: 1.2px; text-transform: uppercase;
        }

        .nav-item {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 18px; margin: 1px 6px;
            border-radius: var(--radius-sm);
            color: rgba(240,235,227,0.45);
            font-size: 12.5px; font-weight: 400;
            text-decoration: none; cursor: pointer;
            transition: all var(--transition);
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: rgba(240,235,227,0.85); }
        .nav-item.active { background: rgba(200,169,110,0.14); color: #e2c98a; }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 50%;
            transform: translateY(-50%);
            width: 2.5px; height: 55%; background: #c8a96e;
            border-radius: 0 2px 2px 0;
        }
        .nav-icon { width: 16px; text-align: center; font-size: 12px; flex-shrink: 0; opacity: 0.75; }
        .nav-badge {
            margin-left: auto; background: rgba(192,120,80,0.7); color: #fde8d8;
            font-size: 9px; font-weight: 600; padding: 1px 6px; border-radius: 50px;
        }

        .sidebar-footer {
            margin-top: auto; padding: 14px 18px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .user-chip { display: flex; align-items: center; gap: 9px; }
        .user-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: rgba(200,169,110,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 500; color: #e2c98a; flex-shrink: 0;
        }
        .user-info-name { font-size: 12px; color: #f0ebe3; font-weight: 400; }
        .user-info-role { font-size: 10px; color: rgba(240,235,227,0.3); margin-top: 1px; }
        .logout-btn {
            margin-left: auto; background: none; border: none;
            color: rgba(240,235,227,0.2); cursor: pointer;
            font-size: 12px; padding: 4px; border-radius: 4px;
            transition: color var(--transition);
        }
        .logout-btn:hover { color: var(--clay); }

        /* ── Main ── */
        .main { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; display: flex; flex-direction: column; }

        .topbar {
            background: var(--sand);
            border-bottom: 1px solid var(--sand2);
            padding: 0 26px; height: 54px;
            display: flex; align-items: center; gap: 10px;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-family: 'Lora', serif; font-size: 18px; color: var(--ink); font-weight: 400; }
        .topbar-divider { color: var(--stone); font-size: 11px; margin: 0 2px; }
        .topbar-sub { font-size: 12px; color: var(--ink3); }
        .topbar-spacer { flex: 1; }
        .topbar-actions { display: flex; align-items: center; gap: 7px; }
        .topbar-btn {
            width: 32px; height: 32px; border-radius: var(--radius-sm);
            border: 1px solid var(--sand2); background: transparent;
            display: flex; align-items: center; justify-content: center;
            color: var(--ink3); cursor: pointer; font-size: 13px;
            transition: all var(--transition); position: relative;
        }
        .topbar-btn:hover { border-color: var(--bark); color: var(--bark); background: var(--sand2); }
        .notif-dot {
            position: absolute; top: 6px; right: 7px;
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--clay); border: 1.5px solid var(--sand);
        }

        .page { padding: 22px 26px; animation: fadeIn .25s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px) } to { opacity:1; transform:translateY(0) } }

        /* ── Components ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px,1fr)); gap: 12px; margin-bottom: 18px; }
        .stat-card {
            background: var(--surface); border: 1px solid var(--sand2);
            border-radius: var(--radius); padding: 16px 18px;
            transition: transform var(--transition), box-shadow var(--transition);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .stat-label { font-size: 11px; color: var(--ink3); font-weight: 500; letter-spacing: 0.3px; text-transform: uppercase; }
        .stat-icon {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0;
        }
        .stat-icon.moss { background: var(--moss-soft); color: var(--moss); }
        .stat-icon.clay { background: var(--clay-soft); color: var(--clay); }
        .stat-icon.bark { background: var(--bark-soft); color: var(--bark); }
        .stat-icon.slate { background: var(--slate-soft); color: var(--slate); }
        .stat-value { font-family: 'Lora', serif; font-size: 24px; color: var(--ink); font-weight: 400; line-height: 1; }
        .stat-value sub { font-family: 'DM Sans', sans-serif; font-size: 11px; color: var(--ink3); font-weight: 400; }
        .stat-value.sm { font-size: 18px; }
        .stat-footer { display: flex; align-items: center; gap: 5px; margin-top: 7px; font-size: 11px; }
        .stat-up { color: var(--moss); font-weight: 500; }
        .stat-neutral { color: var(--ink3); }

        .charts-grid { display: grid; grid-template-columns: 3fr 2fr; gap: 12px; margin-bottom: 18px; }
        .chart-card {
            background: var(--surface); border: 1px solid var(--sand2);
            border-radius: var(--radius); padding: 18px 20px;
        }
        .card-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
        .card-title { font-family: 'Lora', serif; font-size: 14px; color: var(--ink); font-weight: 400; }
        .card-sub { font-size: 11px; color: var(--ink3); margin-top: 2px; }

        .table-card {
            background: var(--surface); border: 1px solid var(--sand2);
            border-radius: var(--radius); overflow: hidden;
        }
        .table-card-header {
            padding: 14px 20px; border-bottom: 1px solid var(--sand2);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
        }
        .table-card-title { font-family: 'Lora', serif; font-size: 14px; color: var(--ink); font-weight: 400; }
        .table-card-actions { display: flex; gap: 8px; align-items: center; }

        .search-wrap {
            display: flex; align-items: center; gap: 7px;
            background: var(--sand); border: 1px solid var(--sand2);
            border-radius: var(--radius-sm); padding: 0 10px;
        }
        .search-wrap i { font-size: 11px; color: var(--ink3); }
        .search-input {
            border: none; background: transparent;
            font-family: 'DM Sans', sans-serif; font-size: 12px; color: var(--ink);
            outline: none; padding: 7px 0; width: 140px;
        }
        .search-input::placeholder { color: var(--ink3); }

        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--sand); }
        th {
            padding: 9px 20px; font-size: 10px; font-weight: 500; color: var(--ink3);
            text-align: left; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap;
        }
        td {
            padding: 11px 20px; font-size: 12.5px; color: var(--ink2);
            border-top: 1px solid var(--sand2);
        }
        tr:hover td { background: #fdfcfa; }

        .badge { display: inline-block; padding: 3px 9px; border-radius: 50px; font-size: 10px; font-weight: 500; }
        .badge-paid    { background: var(--moss-soft); color: #2e6644; }
        .badge-pending { background: #fdf3e8; color: #8a5a20; }
        .badge-cancelled { background: #fceee9; color: #8a3a24; }
        .badge-active  { background: var(--moss-soft); color: #2e6644; }
        .badge-inactive{ background: var(--sand2); color: var(--ink3); }
        .badge-info    { background: var(--slate-soft); color: var(--slate); }

        .btn {
            padding: 7px 14px; border: none; border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 500;
            cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
            transition: all var(--transition);
        }
        .btn-primary { background: var(--bark); color: #fff; }
        .btn-primary:hover { background: #7a6448; }
        .btn-outline { background: transparent; border: 1px solid var(--sand3); color: var(--ink2); }
        .btn-outline:hover { border-color: var(--bark); color: var(--bark); background: var(--bark-soft); }
        .btn-danger { background: transparent; border: 1px solid var(--sand3); color: var(--ink3); }
        .btn-danger:hover { border-color: var(--clay); color: var(--clay); background: var(--clay-soft); }
        .btn-sm { padding: 5px 10px; font-size: 11px; }

        /* ── Alerts ── */
        .alert {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 16px; border-radius: var(--radius-sm);
            font-size: 12.5px;
        }
        .alert-success { background: var(--moss-soft); color: #2e6644; border: 1px solid #b7d8c2; }
        .alert-error   { background: #fceee9; color: #8a3a24; border: 1px solid #f5c4b0; }

        /* ── Section header ── */
        .section-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; }
        .section-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 400; }
        .section-desc { font-size: 12px; color: var(--ink3); margin-top: 3px; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center; padding: 2.5rem 1rem;
            color: var(--ink3);
        }
        .empty-state i { font-size: 1.8rem; opacity: 0.35; margin-bottom: 0.5rem; display: block; }
        .empty-state p { font-size: 12.5px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-hotel"></i></div>
            <div>
                <div class="brand-name">Hotel Neo</div>
                <div class="brand-sub">Admin</div>
            </div>
        </div>

        <div class="sidebar-section">Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard
        </a>

        <div class="sidebar-section">Manajemen</div>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-user"></i></span> Users
        </a>
        <a href="{{ route('admin.roles') }}" class="nav-item {{ request()->is('admin/roles*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-shield-halved"></i></span> Roles
        </a>
        <a href="{{ route('admin.rooms') }}" class="nav-item {{ request()->is('admin/rooms*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-bed"></i></span> Rooms
        </a>
        <a href="{{ route('admin.room-types') }}" class="nav-item {{ request()->is('admin/room-types*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-tags"></i></span> Tipe Kamar
        </a>
        <a href="{{ route('admin.bookings') }}" class="nav-item {{ request()->is('admin/bookings*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-calendar-check"></i></span> Bookings
            {{-- <span class="nav-badge">4</span> --}}
        </a>
        <a href="{{ route('admin.menus') }}" class="nav-item {{ request()->is('admin/menus*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-utensils"></i></span> Restaurant
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->is('admin/orders*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-receipt"></i></span> Orders
        </a>
        <a href="{{ route('admin.payments') }}" class="nav-item {{ request()->is('admin/payments*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-credit-card"></i></span> Payments
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'A' }}</div>
                <div>
                    <div class="user-info-name">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</div>
                    <div class="user-info-role">Super Admin</div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-btn" title="Logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-title">@yield('title')</div>
            <span class="topbar-divider">·</span>
            <span class="topbar-sub">Selamat datang, {{ Auth::check() ? Auth::user()->name : 'Admin' }}</span>
            <div class="topbar-spacer"></div>
            <div class="topbar-actions">
                <button class="topbar-btn" title="Notifikasi">
                    <i class="fas fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <button class="topbar-btn" title="Pengaturan">
                    <i class="fas fa-gear"></i>
                </button>
            </div>
        </div>

        <div class="page">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>