@extends('layouts.app')
@section('judul','Daftar Surat Keputusan')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle">
                <div class="row">
                    @if (Auth::user()->role == 'admin')
                    <a class="btn btn-primary m-1" href="{{route('surat-keputusan.create')}}" role="button">Tambah Surat Keputusan</a>
                    @endif
                </div>
                </h6>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('surat-keputusan.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bulan">Bulan</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    <option value="">Pilih Bulan</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tahun">Tahun</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    <option value="">Pilih Tahun</option>
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="{{ route('surat-keputusan.index') }}" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive pb-3">
    <table id="table" class="display table table-bordered" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Surat</th>
                <th>Tanggal Surat</th>
                <th>Judul Surat</th>
                <th>Daftar Pegawai</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('surat-keputusan.data') }}",
            type: "GET",
            data: function(d) {
                d.bulan = $('#bulan').val();
                d.tahun = $('#tahun').val();
            },
            error: function(xhr, error, thrown) {
                console.log('Error:', error);
                console.log('XHR:', xhr);

                let errorMessage = 'Terjadi kesalahan saat memuat data. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nomor_surat', name: 'nomor_surat'},
            {data: 'tanggal_surat', name: 'tanggal_surat'},
            {data: 'judul_surat', name: 'judul_surat'},
            {data: 'pengguna', name: 'pengguna'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
            processing: "Memproses data...",
            loadingRecords: "Memuat data...",
            emptyTable: "Tidak ada data yang tersedia",
            zeroRecords: "Tidak ada data yang cocok dengan pencarian"
        },
        order: [[2, 'desc']], // Default sort by tanggal_surat descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    });

    // Reload table when filter changes
    $('#bulan, #tahun').change(function() {
        table.ajax.reload();
    });
});

$(document).ready(function() {
    $(document).on('click', '.btn-hapus', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Yakin mau hapus?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/surat-keputusan/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Berhasil!',
                            'Data berhasil dihapus.',
                            'success'
                        ).then(() => {
                            $('#table').DataTable().ajax.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus data.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush
