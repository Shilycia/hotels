@extends('admin.admin')

@section('title', 'Restaurant Menu')

@section('content')
<style>
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(44,36,32,0.5); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; transition: opacity 0.25s ease; }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-content { background: #fff; border-radius: 8px; width: 100%; max-width: 550px; padding: 24px; border: 1px solid var(--sand2); transform: translateY(16px); transition: transform 0.25s ease; max-height: 90vh; overflow-y: auto; }
    .modal-overlay.show .modal-content { transform: translateY(0); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-family: 'Lora', serif; font-size: 16px; color: var(--ink); font-weight: 600; }
    .btn-close { background: transparent; border: none; color: var(--ink3); cursor: pointer; font-size: 14px; padding: 4px; transition: color 0.3s ease; }
    .btn-close:hover { color: var(--clay); }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-size: 11px; font-weight: 600; color: var(--ink2); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.4px; }
    .form-control { width: 100%; padding: 8px 12px; border: 1px solid var(--sand3); border-radius: 4px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); background: var(--sand); outline: none; transition: border-color 0.3s ease; }
    select.form-control { cursor: pointer; }
    textarea.form-control { resize: vertical; min-height: 70px; }
    .form-control:focus { border-color: var(--bark); background: #fff; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--sand2); }
    .menu-thumb { width: 40px; height: 40px; border-radius: 7px; background: var(--clay-soft); display: flex; align-items: center; justify-content: center; color: var(--clay); font-size: 15px; flex-shrink: 0; overflow: hidden; border: 1px solid var(--sand2); }
    .menu-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .menu-cell { display: flex; align-items: center; gap: 10px; }
    .badge-status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .status-available { background: #d1e7dd; color: #0f5132; }
    .status-soldout { background: #f8d7da; color: #842029; }
    .badge-cat { background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
</style>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px">
        <div style="font-weight: bold; margin-bottom: 5px;"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan input:</div>
        <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="section-header">
    <div>
        <div class="section-title">Menu Restoran</div>
        <div class="section-desc">Kelola daftar menu makanan dan minuman restoran.</div>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i> Tambah Menu
    </button>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Daftar Menu</div>
        <div class="table-card-actions">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input class="search-input" id="searchInput" placeholder="Cari nama menu...">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody id="menuTableBody">
                @forelse($menus as $menu)
                <tr>
                    <td>
                        <div class="menu-cell">
                            <div class="menu-thumb">
                                @if($menu->foto_url)
                                    <img src="{{ asset('storage/' . $menu->foto_url) }}" alt="{{ $menu->name }}">
                                @else
                                    <i class="fas fa-utensils"></i>
                                @endif
                            </div>
                            <div>
                                <span style="font-weight:600;color:var(--ink);display:block;">{{ $menu->name }}</span>
                                <span style="font-size:11px;color:var(--ink3)">{{ \Illuminate\Support\Str::limit($menu->description, 30) ?: 'Tidak ada deskripsi' }}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-cat">{{ $menu->category }}</span></td>
                    <td style="font-weight:600;color:var(--clay)">Rp {{ number_format($menu->price ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($menu->is_available)
                            <span class="badge-status status-available"><i class="fas fa-check-circle"></i> Tersedia</span>
                        @else
                            <span class="badge-status status-soldout"><i class="fas fa-times-circle"></i> Habis</span>
                        @endif
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <button class="btn btn-outline btn-sm" onclick="openEditModal({{ json_encode($menu) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form id="delete-form-{{ $menu->id }}" action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $menu->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state" style="text-align: center; padding: 30px;">
                            <i class="fas fa-utensils" style="font-size: 32px; color: var(--sand3); margin-bottom: 10px;"></i>
                            <p>Belum ada menu terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalAdd">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tambah Menu</div>
            <button class="btn-close" type="button" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Menu</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Nasi Goreng Spesial" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" id="add_category" class="form-control" onchange="togglePaketItems('add')" required>
                        <option value="">-- Pilih --</option>
                        <option value="food">Makanan (Food)</option>
                        <option value="drink">Minuman (Drink)</option>
                        <option value="dessert">Dessert</option>
                        <option value="snack">Snack</option>
                        <option value="paket">Paket (Bundle)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control" placeholder="Contoh: 45000" min="0" required>
                </div>
            </div>

            {{-- Dinamis: Muncul hanya jika kategori = paket --}}
            <div class="form-group" id="add_paket_items_container" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px dashed #ced4da;">
                <label class="form-label" style="color: var(--clay)">Pilih Isi Paket</label>
                <span style="font-size: 10px; color: var(--ink3); display: block; margin-bottom: 5px;">Tahan tombol CTRL (Windows) / CMD (Mac) untuk memilih lebih dari 1 menu.</span>
                <select name="paket_items[]" id="add_paket_items" class="form-control" multiple style="height: 120px;">
                    @foreach($foodItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} (Rp {{ number_format($item->price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Bisa Bundling Kamar?</label>
                    <select name="can_bundle_with_room" class="form-control" required>
                        <option value="0">Tidak (Hanya Beli Biasa)</option>
                        <option value="1">Ya (Bisa Jadi Layanan Kamar)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Ketersediaan</label>
                    <select name="is_available" class="form-control" required>
                        <option value="1">Tersedia</option>
                        <option value="0">Habis (Sold Out)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Porsi</label>
                    <input type="text" name="serving" class="form-control" placeholder="Contoh: 1 Orang">
                </div>
                <div class="form-group">
                    <label class="form-label">Kalori (kcal)</label>
                    <input type="number" name="calories" class="form-control" placeholder="Opsional" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" placeholder="Deskripsi singkat menu..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto Menu (Opsional)</label>
                <input type="file" name="foto_url" class="form-control" accept="image/*">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAdd')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Edit Menu</div>
            <button class="btn-close" type="button" onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
        </div>
        <form id="formEditMenu" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Menu</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" id="edit_category" class="form-control" onchange="togglePaketItems('edit')" required>
                        <option value="food">Makanan (Food)</option>
                        <option value="drink">Minuman (Drink)</option>
                        <option value="dessert">Dessert</option>
                        <option value="snack">Snack</option>
                        <option value="paket">Paket (Bundle)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
                </div>
            </div>

            {{-- Dinamis Edit Paket --}}
            <div class="form-group" id="edit_paket_items_container" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px dashed #ced4da;">
                <label class="form-label" style="color: var(--clay)">Pilih Isi Paket</label>
                <span style="font-size: 10px; color: var(--ink3); display: block; margin-bottom: 5px;">Tahan CTRL/CMD untuk memilih > 1 menu.</span>
                <select name="paket_items[]" id="edit_paket_items" class="form-control" multiple style="height: 120px;">
                    @foreach($foodItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Bisa Bundling Kamar?</label>
                    <select name="can_bundle_with_room" id="edit_can_bundle" class="form-control" required>
                        <option value="0">Tidak (Hanya Beli Biasa)</option>
                        <option value="1">Ya (Bisa Jadi Layanan Kamar)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Ketersediaan</label>
                    <select name="is_available" id="edit_is_available" class="form-control" required>
                        <option value="1">Tersedia</option>
                        <option value="0">Habis (Sold Out)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Porsi</label>
                    <input type="text" name="serving" id="edit_serving" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Kalori (kcal)</label>
                    <input type="number" name="calories" id="edit_calories" class="form-control" min="0">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" id="edit_description" class="form-control"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" name="foto_url" class="form-control" accept="image/*">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-rotate-right"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal(id)  { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    // Logika memunculkan Multi-Select Paket jika kategori = 'paket'
    function togglePaketItems(type) {
        let categoryVal = document.getElementById(type + '_category').value;
        let container = document.getElementById(type + '_paket_items_container');
        if(categoryVal === 'paket') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function openEditModal(menu) {
        document.getElementById('edit_name').value = menu.name;
        document.getElementById('edit_category').value = menu.category;
        document.getElementById('edit_price').value = menu.price;
        document.getElementById('edit_serving').value = menu.serving || '';
        document.getElementById('edit_calories').value = menu.calories || '';
        document.getElementById('edit_is_available').value = menu.is_available ? "1" : "0";
        document.getElementById('edit_can_bundle').value = menu.can_bundle_with_room ? "1" : "0";
        document.getElementById('edit_description').value = menu.description || '';
        
        // Panggil untuk nge-trigger show/hide box paket
        togglePaketItems('edit');

        // Jika ini paket, pilih otomatis item di kotak multi-selectnya
        if(menu.category === 'paket' && menu.paket_items) {
            let selectedIds = menu.paket_items.map(item => item.id.toString());
            let options = document.getElementById('edit_paket_items').options;
            for(let i=0; i < options.length; i++) {
                options[i].selected = selectedIds.includes(options[i].value);
            }
        }

        document.getElementById('formEditMenu').action = '/admin/menus/' + menu.id;
        openModal('modalEdit');
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Menu?',
            text: 'Item menu ini akan dihapus dari daftar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c07850',
            cancelButtonColor: '#8b7355',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            backdrop: 'rgba(44,36,32,0.5)'
        }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#menuTableBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush