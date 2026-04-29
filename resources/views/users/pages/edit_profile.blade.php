@extends('users.layouts.app') 

{{-- [P-02] FIX: Menambahkan Judul Tab Browser --}}
@section('title', 'Edit Profil - Hotel Neo')

@section('content')

{{-- [P-02] FIX: Menambahkan Header Halaman agar konsisten dengan halaman lain --}}
@include('users.components.page-header', [
    'title' => 'Edit Profil',
    'breadcrumb' => 'Edit Profil'
])

<div class="container mt-5 mb-5">
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-white shadow rounded p-5">
                <form action="{{ route('guest.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $guest->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $guest->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $guest->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label>Nomor Identitas (KTP/Passport)</label>
                        <input type="text" name="identity_number" class="form-control" value="{{ old('identity_number', $guest->identity_number) }}">
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control">{{ old('address', $guest->address) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label>Foto Profil Baru (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4 me-2">Simpan Perubahan</button>
                        <a href="{{ route('guest.profile') }}" class="btn btn-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection