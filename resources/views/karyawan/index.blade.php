@extends('layouts.app')

@section('page-title', 'Data Pegawai')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">Data Pegawai</h5>

        <a href="{{ route('karyawan.create') }}"
            class="btn btn-add btn-sm rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Tambah Pegawai
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Posisi</th>
                    <th>Status</th>
                    <th width="20%" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($employees as $karyawan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $karyawan->employee_code }}</td>
                    <td>{{ $karyawan->full_name }}</td>
                    <td>{{ $karyawan->position }}</td>
                    <td>
                        <span class="badge rounded-pill bg-{{ $karyawan->employment_status == 'permanent' ? 'success' : 'warning' }}">
                            {{ ucfirst($karyawan->employment_status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">

                            <a href="{{ route('karyawan.show', $karyawan->id) }}"
                                class="btn btn-sm btn-outline-info rounded-circle"
                                title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>

                            <a href="{{ route('karyawan.edit', $karyawan->id) }}"
                                class="btn btn-sm btn-outline-warning rounded-circle"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('karyawan.destroy', $karyawan->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin hapus data pegawai ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-circle"
                                    title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Belum ada data pegawai
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection