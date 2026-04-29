@extends('layouts.app') @section('content')
<div class="container mt-5">
    <h2>Edit Profil</h2>
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    
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
        
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('guest.profile') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection