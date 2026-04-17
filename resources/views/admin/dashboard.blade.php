<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Dashboard Admin – Hotel Neo</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,300;9..144,600;9..144,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<style>

</style>
</head>
<body>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="fas fa-hotel"></i></div>
    <div>
      <div class="brand-name">Hotel Neo</div>
      <div class="brand-sub">Admin Panel</div>
    </div>
  </div>

  <div class="sidebar-section">Main Menu</div>
  <a class="nav-item active" data-page="dashboard" onclick="navigate('dashboard',this)"><span class="nav-icon"><i class="fas fa-gauge-high"></i></span> Dashboard</a>
  <a class="nav-item" data-page="reporting" onclick="navigate('reporting',this)"><span class="nav-icon"><i class="fas fa-chart-line"></i></span> Reporting</a>

  <div class="sidebar-section">Manajemen</div>
  <a class="nav-item" data-page="users" onclick="navigate('users',this)"><span class="nav-icon"><i class="fas fa-users"></i></span> Manage User</a>
  <a class="nav-item" data-page="rooms" onclick="navigate('rooms',this)"><span class="nav-icon"><i class="fas fa-bed"></i></span> Manage Room</a>
  <a class="nav-item" data-page="bookings" onclick="navigate('bookings',this)"><span class="nav-icon"><i class="fas fa-calendar-check"></i></span> Manage Booking <span class="nav-badge">New</span></a>

  <div class="sidebar-footer">
    <div class="user-chip">
      <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
      <div>
        <div class="user-info-name">{{ Auth::user()->name }}</div>
        <div class="user-info-role">Super Admin</div>
      </div>
      
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
      </form>
      <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-btn" title="Logout">
          <i class="fas fa-arrow-right-from-bracket"></i>
      </button>

    </div>
  </div>
</aside>

<div class="main">

  <div class="topbar">
    <div>
      <div class="topbar-title" id="pageTitle">Dashboard</div>
      <div class="topbar-sub" id="pageSub">Selamat datang kembali 👋, {{ Auth::user()->name }}</div>
    </div>
    <div class="topbar-spacer"></div>
    <div class="topbar-actions">
      <button class="topbar-btn" title="Notifikasi"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
      <button class="topbar-btn" title="Pengaturan"><i class="fas fa-gear"></i></button>
    </div>
  </div>

  <div class="page active" id="page-dashboard">

    <div class="stats-grid" style="padding:2rem 2rem 0">
      
      <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-label">Total Pengguna</div>
        <div class="stat-value">{{ number_format($widgets['total_users'] ?? 0) }}</div>
        <div class="stat-footer"><span class="stat-neutral">Akun terdaftar</span></div>
      </div>

      <div class="stat-card teal">
        <div class="stat-icon teal"><i class="fas fa-bed"></i></div>
        <div class="stat-label">Kamar Terisi</div>
        <div class="stat-value">{{ $widgets['occupied_rooms'] ?? 0 }}<span style="font-size:1rem;color:var(--text-light)">/{{ $widgets['total_rooms'] ?? 0 }}</span></div>
        <div class="stat-footer"><span class="stat-neutral">Occupancy hari ini</span></div>
      </div>

      <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-utensils"></i></div>
        <div class="stat-label">Order Restoran</div>
        <div class="stat-value">328</div>
        <div class="stat-footer"><span class="stat-up"><i class="fas fa-arrow-trend-up"></i> +12.5%</span><span class="stat-neutral">bulan ini</span></div>
      </div>

      <div class="stat-card rose">
        <div class="stat-icon rose"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value" style="font-size:1.35rem">Rp {{ number_format($widgets['total_revenue'] ?? 0, 0, ',', '.') }}</div>
        <div class="stat-footer"><span class="stat-up"><i class="fas fa-arrow-trend-up"></i> Lunas</span></div>
      </div>
    </div>

    <div class="charts-grid" style="padding:1.25rem 2rem 0">
      <div class="chart-card">
        <div class="chart-card-header">
          <div>
            <div class="chart-card-title">Tren Pendapatan</div>
            <div class="chart-card-sub">Booking & Restoran per bulan</div>
          </div>
        </div>
        <canvas id="incomeChart" height="90"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-card-header">
          <div><div class="chart-card-title">Komposisi Pendapatan</div></div>
        </div>
        <canvas id="donutChart" height="170"></canvas>
      </div>
    </div>

    <div style="padding:1.25rem 2rem 2rem">
      <div class="table-card">
        <div class="table-card-header">
          <div class="table-card-title">Booking Terbaru</div>
          <div class="table-card-actions">
            <input class="search-input" placeholder="🔍  Cari...">
            <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</button>
          </div>
        </div>
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>#ID</th>
                <th>Tamu</th>
                <th>Kamar</th>
                <th>Status Pembayaran</th>
                <th>Total Nominal</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentBookings as $booking)
              <tr>
                <td>#B-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $booking->user->name ?? 'Tamu Tidak Diketahui' }}</td>
                <td>{{ $booking->room->name ?? 'Kamar Tidak Diketahui' }}</td>
                <td>
                    @if($booking->payment_status == 'paid')
                        <span class="badge badge-paid">Lunas</span>
                    @elseif($booking->payment_status == 'pending')
                        <span class="badge badge-pending">Pending</span>
                    @else
                        <span class="badge badge-cancelled">Batal</span>
                    @endif
                </td>
                <td>Rp {{ number_format($booking->amount ?? 0, 0, ',', '.') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada data booking terbaru.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="page" id="page-reporting"><div style="padding:2rem"><div class="coming-soon"><h2>Fitur Report Sedang Disiapkan</h2></div></div></div>
  <div class="page" id="page-users"><div style="padding:2rem"><div class="coming-soon"><h2>Manage User</h2></div></div></div>
  <div class="page" id="page-rooms"><div style="padding:2rem"><div class="coming-soon"><h2>Manage Room</h2></div></div></div>
  <div class="page" id="page-bookings"><div style="padding:2rem"><div class="coming-soon"><h2>Manage Booking</h2></div></div></div>

</div><script>
/* ─── Navigation Script ─── */
const pageMeta = {
  dashboard: { title: 'Dashboard', sub: 'Selamat datang kembali 👋, {{ Auth::user()->name }}' },
  reporting:  { title: 'Income Report', sub: 'Laporan pendapatan Hotel Neo' },
  users:      { title: 'Manage User', sub: 'Kelola akun pengguna sistem' },
  rooms:      { title: 'Manage Room', sub: 'Inventaris kamar hotel' },
  bookings:   { title: 'Manage Booking', sub: 'Reservasi dan pemesanan kamar' }
};

function navigate(page, el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  const pg = document.getElementById('page-' + page);
  if (pg) pg.classList.add('active');
  const meta = pageMeta[page] || { title: page, sub: '' };
  document.getElementById('pageTitle').textContent = meta.title;
  document.getElementById('pageSub').textContent = meta.sub;
}

/* ─── Dashboard Charts Dummy Data ─── */
function initDashboardCharts() {
  const ctxLine = document.getElementById('incomeChart').getContext('2d');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: ['Nov','Des','Jan','Feb','Mar','Apr'],
      datasets: [
        {
          label: 'Booking',
          data: [32, 28, 45, 38, 52, 57],
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37,99,235,.08)',
          tension: .4, fill: true, pointRadius: 4,
          pointBackgroundColor: '#2563eb',
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display:false } },
      scales: { y: { beginAtZero:true } }
    }
  });

  const ctxDonut = document.getElementById('donutChart').getContext('2d');
  new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
      labels: ['Booking', 'Restoran'],
      datasets: [{
        data: [68, 32],
        backgroundColor: ['#2563eb', '#d97706'],
        borderWidth: 0, hoverOffset: 6
      }]
    },
    options: { responsive:true, cutout:'72%', plugins: { legend:{display:false} } }
  });
}

initDashboardCharts();
</script>
</body>
</html>