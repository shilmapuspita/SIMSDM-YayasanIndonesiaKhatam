@extends('layouts.app')

@section('page-title', 'Profil Saya')

@section('content')

{{-- NOTIFIKASI --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="fw-semibold mb-4">Data Profil</h5>

            <form action="{{ route('employee.profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', auth()->user()->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', auth()->user()->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->role }}" disabled>
                </div>

                <button type="submit" class="btn btn-orange w-100">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
