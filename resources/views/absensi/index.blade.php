@extends('layouts.app')

@section('page-title', 'Data Absensi')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">Data Absensi</h5>

        <a href="{{ route('absensi.create') }}" class="btn btn-add btn-sm rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Tambah Absensi
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Pegawai</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Status</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $attendance->employee->full_name }}</td>
                    <td>{{ $attendance->attendance_date?->format('d-m-Y') }}</td>
                    <td>{{ $attendance->check_in ?? '-' }}</td>
                    <td>{{ $attendance->check_out ?? '-' }}</td>
                    <td>
                        <span class="badge rounded-pill 
                            bg-{{ 
                                $attendance->status === 'present' ? 'success' :
                                ($attendance->status === 'sick' ? 'warning' :
                                ($attendance->status === 'leave' ? 'info' : 'danger'))
                            }}">
                            {{ ucfirst($attendance->status) }}
                        </span>

                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">

                            {{-- DETAIL --}}
                            <a href="{{ route('absensi.show', $attendance->id) }}"
                                class="btn btn-sm btn-outline-info rounded-circle"
                                title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- EDIT --}}
                            <a href="{{ route('absensi.edit', $attendance->id) }}"
                                class="btn btn-sm btn-outline-warning rounded-circle"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- DELETE --}}
                            <form action="{{ route('absensi.destroy', $attendance->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus data ini?');"
                                style="display: inline-block;">

                                @csrf {{-- Token keamanan WAJIB --}}
                                @method('DELETE') {{-- Mengubah POST menjadi DELETE --}}

                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Belum ada data absensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection