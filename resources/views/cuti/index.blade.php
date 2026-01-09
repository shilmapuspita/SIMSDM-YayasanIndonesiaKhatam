@extends('layouts.app')

@section('page-title', 'Data Pengajuan Cuti')

@section('content')

{{-- Alert Sukses (Tetap kita pasang biar ada feedback kalau habis approve/reject) --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3 rounded-3 border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card p-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">Data Pengajuan Cuti</h5>

        <a href="{{ route('cuti.create') }}" class="btn btn-add btn-sm rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Buat Pengajuan
        </a>

    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">Nama Pegawai</th>
                    <th width="15%">Jenis Cuti</th>
                    <th width="20%">Tanggal & Durasi</th>
                    <th>Keperluan</th>
                    <th width="10%">Status</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($leaves as $leave)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    {{-- 1. NAMA PEGAWAI --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ $leave->user->employee->full_name ?? $leave->user->name }}</span>
                            <small class="text-muted" style="font-size: 12px;">
                                NIY: {{ $leave->user->employee->employee_code ?? '-' }}
                            </small>
                        </div>
                    </td>

                    {{-- 2. JENIS CUTI (Badge Style) --}}
                    <td>
                        @if($leave->leave_type == 'annual')
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Tahunan</span>
                        @elseif($leave->leave_type == 'sick')
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Sakit</span>
                        @elseif($leave->leave_type == 'maternity')
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Melahirkan</span>
                        @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 text-uppercase">{{ $leave->leave_type }}</span>
                        @endif
                    </td>

                    {{-- 3. TANGGAL & DURASI --}}
                    <td>
                        <div class="d-flex flex-column">
                            <span>{{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}</span>
                            <small class="text-muted fw-bold">
                                {{ $leave->start_date->diffInDays($leave->end_date) + 1 }} Hari
                            </small>
                        </div>
                    </td>

                    {{-- 4. KEPERLUAN --}}
                    <td>
                        <span class="d-inline-block text-truncate text-secondary" style="max-width: 150px;">
                            {{ $leave->reason }}
                        </span>
                    </td>

                    {{-- 5. STATUS (Pill Badge) --}}
                    <td>
                        <span class="badge rounded-pill 
                            bg-{{ 
                                $leave->status === 'approved' ? 'success' : 
                                ($leave->status === 'rejected' ? 'danger' : 'warning') 
                            }}">
                            {{-- Ubah teks status jadi bahasa Indonesia biar rapi --}}
                            {{ $leave->status === 'approved' ? 'Disetujui' : ($leave->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                        </span>
                    </td>

                    {{-- 6. AKSI (Icon Only - Sama persis Absensi) --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">

                            {{-- DETAIL --}}
                            <a href="{{ route('cuti.show', $leave->id) }}"
                                class="btn btn-sm btn-outline-info rounded-circle"
                                title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- EDIT (Hanya jika status masih pending biar aman) --}}
                            @if($leave->status === 'pending')
                            <a href="{{ route('cuti.edit', $leave->id) }}"
                                class="btn btn-sm btn-outline-warning rounded-circle"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @else
                            {{-- Button disabled kalau sudah diapprove/reject --}}
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" disabled>
                                <i class="bi bi-pencil"></i>
                            </button>
                            @endif

                            {{-- DELETE --}}
                            <form action="{{ route('cuti.destroy', $leave->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus pengajuan cuti ini?');"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada data pengajuan cuti
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (Tetap dipertahankan) --}}
    @if($leaves->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $leaves->links() }}
    </div>
    @endif
</div>
@endsection