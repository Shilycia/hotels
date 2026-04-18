<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Admin Panel') – Hotel Neo</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Fraunces:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        /* Salin semua CSS kamu yang panjang di sini (Root, Sidebar, dll) */
        :root {
            --bg: #f0f2f8;
            --surface: #ffffff;
            --sidebar-bg: #0f172a;
            --sidebar-w: 260px;
            --blue: #2563eb;
            --blue-soft: #eff6ff;
            --teal: #0891b2;
            --teal-soft: #ecfeff;
            --amber: #d97706;
            --amber-soft: #fffbeb;
            --rose: #e11d48;
            --rose-soft: #fff1f2;
            --green: #059669;
            --green-soft: #ecfdf5;
            --text-dark: #0f172a;
            --text-mid: #475569;
            --text-light: #94a3b8;
            --border: #e2e8f0;
            --radius-sm: .5rem;
            --radius: .85rem;
            --transition: .22s cubic-bezier(.4,0,.2,1);
            --grad-blue: linear-gradient(135deg,#2563eb,#60a5fa);
            --grad-teal: linear-gradient(135deg,#0891b2,#22d3ee);
            --grad-amber: linear-gradient(135deg,#d97706,#fbbf24);
            --grad-rose: linear-gradient(135deg,#e11d48,#fb7185);
            --shadow-sm: 0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
            --shadow: 0 4px 16px rgba(0,0,0,.08);
        }
        *,::before,::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text-dark);min-height:100vh;display:flex;overflow-x:hidden}
        
        /* ─── Sidebar ─── */
        .sidebar{ width:var(--sidebar-w); min-height:100vh; background:var(--sidebar-bg); position:fixed;top:0;left:0;z-index:100; display:flex;flex-direction:column; transition:transform var(--transition); }
        .sidebar-brand{ padding:1.75rem 1.5rem 1.25rem; display:flex;align-items:center;gap:.85rem; border-bottom:1px solid rgba(255,255,255,.07); }
        .brand-icon{ width:40px;height:40px;border-radius:10px; background:var(--grad-blue); display:flex;align-items:center;justify-content:center; color:#fff;font-size:1.1rem;flex-shrink:0; box-shadow:0 4px 12px rgba(37,99,235,.4); }
        .brand-name{font-family:'Fraunces',serif;font-size:1.15rem;font-weight:700;color:#fff;line-height:1.1}
        .brand-sub{font-size:.7rem;color:rgba(255,255,255,.4);letter-spacing:.5px;text-transform:uppercase;margin-top:.15rem}
        .sidebar-section{padding:.85rem 1rem .25rem;font-size:.66rem;font-weight:600;color:rgba(255,255,255,.3);letter-spacing:1px;text-transform:uppercase}
        .nav-item{ display:flex;align-items:center;gap:.75rem; padding:.65rem 1.25rem;margin:.1rem .6rem; border-radius:var(--radius-sm); color:rgba(255,255,255,.55);font-size:.83rem;font-weight:500; text-decoration:none;cursor:pointer; transition:all var(--transition);position:relative; }
        .nav-item:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)}
        .nav-item.active{background:rgba(37,99,235,.22);color:#93c5fd}
        .nav-item.active::before{ content:'';position:absolute;left:0;top:50%;transform:translateY(-50%); height:60%;width:3px;background:var(--grad-blue);border-radius:0 3px 3px 0; }
        .nav-item .nav-icon{width:20px;text-align:center;font-size:.9rem;flex-shrink:0}
        .nav-item .nav-badge{ margin-left:auto;background:var(--rose);color:#fff; font-size:.65rem;font-weight:700;padding:.15rem .45rem;border-radius:50px; }
        .sidebar-footer{ margin-top:auto;padding:1.25rem 1.5rem; border-top:1px solid rgba(255,255,255,.07); }
        .user-chip{display:flex;align-items:center;gap:.75rem}
        .user-avatar{ width:36px;height:36px;border-radius:50%; background:var(--grad-teal); display:flex;align-items:center;justify-content:center; color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0; }
        .user-info-name{font-size:.82rem;font-weight:600;color:#fff}
        .user-info-role{font-size:.7rem;color:rgba(255,255,255,.4)}
        .logout-btn{ margin-left:auto;color:rgba(255,255,255,.35);cursor:pointer;font-size:.85rem; transition:color var(--transition); border:none; background:transparent; }
        .logout-btn:hover{color:var(--rose)}
        
        /* ─── Main & Components ─── */
        .main{ margin-left:var(--sidebar-w); flex:1;min-height:100vh; display:flex;flex-direction:column; }
        .topbar{ background:var(--surface); border-bottom:1px solid var(--border); padding:.9rem 2rem; display:flex;align-items:center;gap:1rem; position:sticky;top:0;z-index:50; box-shadow:var(--shadow-sm); }
        .topbar-title{font-family:'Fraunces',serif;font-size:1.25rem;font-weight:600;color:var(--text-dark)}
        .topbar-sub{font-size:.78rem;color:var(--text-light);margin-top:.1rem}
        .topbar-spacer{flex:1}
        .topbar-actions{display:flex;align-items:center;gap:.75rem}
        .topbar-btn{ width:38px;height:38px;border-radius:var(--radius-sm); border:1px solid var(--border);background:transparent; display:flex;align-items:center;justify-content:center; color:var(--text-mid);cursor:pointer;font-size:.85rem; transition:all var(--transition);position:relative; }
        .topbar-btn:hover{background:var(--bg);border-color:var(--blue);color:var(--blue)}
        .notif-dot{ position:absolute;top:7px;right:8px; width:7px;height:7px;border-radius:50%;background:var(--rose); border:2px solid var(--surface); }
        .page{padding:2rem;animation:fadeIn .3s ease} /* Dihapus display:none karena ditangani Blade */
        @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        
        /* ─── Stats, Charts, Cards ─── */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1.25rem;margin-bottom:1.75rem}
        .stat-card{ background:var(--surface);border-radius:var(--radius); padding:1.4rem;box-shadow:var(--shadow-sm); border:1px solid var(--border); display:flex;flex-direction:column;gap:.6rem; position:relative;overflow:hidden; transition:transform var(--transition),box-shadow var(--transition); }
        .stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow)}
        .stat-card::after{ content:'';position:absolute;top:-20px;right:-20px; width:80px;height:80px;border-radius:50%; opacity:.06; }
        .stat-card.blue::after{background:var(--blue)} .stat-card.teal::after{background:var(--teal)} .stat-card.amber::after{background:var(--amber)} .stat-card.rose::after{background:var(--rose)}
        .stat-icon{ width:44px;height:44px;border-radius:10px; display:flex;align-items:center;justify-content:center; font-size:1.1rem;color:#fff;flex-shrink:0; }
        .stat-icon.blue{background:var(--grad-blue);box-shadow:0 4px 12px rgba(37,99,235,.3)} .stat-icon.teal{background:var(--grad-teal);box-shadow:0 4px 12px rgba(8,145,178,.3)} .stat-icon.amber{background:var(--grad-amber);box-shadow:0 4px 12px rgba(217,119,6,.3)} .stat-icon.rose{background:var(--grad-rose);box-shadow:0 4px 12px rgba(225,29,72,.3)}
        .stat-label{font-size:.75rem;font-weight:500;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px}
        .stat-value{font-family:'Fraunces',serif;font-size:1.8rem;font-weight:700;color:var(--text-dark);line-height:1}
        .stat-footer{display:flex;align-items:center;gap:.35rem;font-size:.75rem;margin-top:.25rem}
        .stat-up{color:var(--green);font-weight:600} .stat-neutral{color:var(--text-light)}
        .charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.75rem}
        .chart-card{ background:var(--surface);border-radius:var(--radius); padding:1.5rem;box-shadow:var(--shadow-sm);border:1px solid var(--border); }
        .chart-card-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem}
        .chart-card-title{font-size:.9rem;font-weight:600;color:var(--text-dark)}
        .chart-card-sub{font-size:.75rem;color:var(--text-light);margin-top:.2rem}
        .table-card{ background:var(--surface);border-radius:var(--radius); box-shadow:var(--shadow-sm);border:1px solid var(--border); overflow:hidden; }
        .table-card-header{ padding:1.25rem 1.5rem;border-bottom:1px solid var(--border); display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem; }
        .table-card-title{font-size:.9rem;font-weight:600;color:var(--text-dark)}
        .table-card-actions{display:flex;gap:.5rem;align-items:center}
        .search-input{ font-family:inherit;font-size:.8rem;color:var(--text-dark); padding:.45rem .85rem;border:1px solid var(--border);border-radius:var(--radius-sm); outline:none;background:#fff;width:180px; transition:border-color var(--transition); }
        .search-input:focus{border-color:var(--blue);width:220px}
        table{width:100%;border-collapse:collapse}
        thead{background:#f8fafc}
        th{padding:.85rem 1.25rem;font-size:.72rem;font-weight:600;color:var(--text-light);text-transform:uppercase;letter-spacing:.5px;text-align:left;white-space:nowrap}
        td{padding:.9rem 1.25rem;font-size:.82rem;color:var(--text-mid);border-top:1px solid var(--border)}
        tr:hover td{background:#fafbfc}
        .badge{ display:inline-block;padding:.25rem .65rem;border-radius:50px; font-size:.7rem;font-weight:600; }
        .badge-paid{background:var(--green-soft);color:var(--green)}
        .badge-pending{background:var(--amber-soft);color:var(--amber)}
        .badge-cancelled{background:var(--rose-soft);color:var(--rose)}
        .btn{ padding:.6rem 1.25rem;border:none;border-radius:var(--radius-sm); font-family:inherit;font-size:.82rem;font-weight:600;cursor:pointer; display:inline-flex;align-items:center;gap:.5rem; transition:all var(--transition); }
        .btn-primary{background:var(--grad-blue);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.3)}
        .btn-sm{padding:.4rem .85rem;font-size:.76rem}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-hotel"></i></div>
            <div><div class="brand-name">Hotel Neo</div></div>
        </div>
        
        <div class="sidebar-section">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-gauge-high"></i></span> Dashboard
        </a>

        <div class="sidebar-section">Manajemen</div>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-users"></i></span> Manage User
        </a>
        <a href="{{ route('admin.roles') }}" class="nav-item {{ request()->is('admin/roles*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-user-shield"></i></span> Manage Role
        </a>
        <a href="{{ route('admin.rooms') }}" class="nav-item {{ request()->is('admin/rooms*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-bed"></i></span> Manage Room
        </a>
        <a href="{{ route('admin.room-types') }}" class="nav-item {{ request()->is('admin/room-types*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-tags"></i></span> Manage Tipe Kamar
        </a>
        <a href="{{ route('admin.bookings') }}" class="nav-item {{ request()->is('admin/bookings*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-calendar-check"></i></span> Manage Booking
        </a>
        <a href="{{ route('admin.menus') }}" class="nav-item {{ request()->is('admin/menus*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-utensils"></i></span> Restaurant Menu
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->is('admin/orders*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-receipt"></i></span> Manage Order
        </a>
        <a href="{{ route('admin.payments') }}" class="nav-item {{ request()->is('admin/payments*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-credit-card"></i></span> Manage Payment
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'A' }}</div>
                <div>
                    <div class="user-info-name">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</div>
                    <div class="user-info-role">Super Admin</div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-btn" title="Logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">@yield('title')</div>
                <div class="topbar-sub">Selamat datang kembali 👋, {{ Auth::check() ? Auth::user()->name : 'Admin' }}</div>
            </div>
            <div class="topbar-spacer"></div>
            <div class="topbar-actions">
                <button class="topbar-btn" title="Notifikasi"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
                <button class="topbar-btn" title="Pengaturan"><i class="fas fa-gear"></i></button>
            </div>
        </div>
        
        <div class="page">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>