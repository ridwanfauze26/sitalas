@extends('layouts.app')
@section('judul','Daftar Formulir')
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
                    <a class="btn btn-primary m-1" href="{{route('formulir.create')}}" role="button">
                         Tambah Formulir
                    </a>
                    @endif
                </div>
                </h6>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('formulir.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bulan">Bulan</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    <option value="">Pilih Bulan</option>
                                    <option value="1" {{ request('bulan') == '1' ? 'selected' : '' }}>Januari</option>
                                    <option value="2" {{ request('bulan') == '2' ? 'selected' : '' }}>Februari</option>
                                    <option value="3" {{ request('bulan') == '3' ? 'selected' : '' }}>Maret</option>
                                    <option value="4" {{ request('bulan') == '4' ? 'selected' : '' }}>April</option>
                                    <option value="5" {{ request('bulan') == '5' ? 'selected' : '' }}>Mei</option>
                                    <option value="6" {{ request('bulan') == '6' ? 'selected' : '' }}>Juni</option>
                                    <option value="7" {{ request('bulan') == '7' ? 'selected' : '' }}>Juli</option>
                                    <option value="8" {{ request('bulan') == '8' ? 'selected' : '' }}>Agustus</option>
                                    <option value="9" {{ request('bulan') == '9' ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober</option>
                                    <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tahun">Tahun</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    <option value="">Pilih Tahun</option>
                                    @foreach($tahunList as $tahun)
                                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="{{ route('formulir.index') }}" class="btn btn-secondary">
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
                                <th>Judul Formulir</th>
                                <th>Tanggal</th>
                                <th>File</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Modal Formulir-->
                <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel">Detail Formulir</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <iframe height="700" id="berkas_scan" width="100%">
                            </iframe>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
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
    function getSurat(id){
        var berkas_scan = document.getElementById("berkas_scan");
        berkas_scan.setAttribute("src", "{{url('/formulir/view/')}}"+'/'+id+'?t='+new Date().getTime());
    }

    let userRole = '{{Auth::user()->role}}';

    $(document).ready(function() {
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('formulir.index') }}",
                type: "GET",
                data: function(d) {
                    d.bulan = $('#bulan').val();
                    d.tahun = $('#tahun').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'judul', name: 'judul'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'file_formulir', name: 'file_formulir', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            order: [[2, 'desc']] // Default sort by tanggal descending
        });

        // Reload table when filter changes
        $('#bulan, #tahun').change(function() {
            table.ajax.reload();
        });

        // Handle form submission
        $(document).on('submit', 'form[action*="destroy"]', function(e) {
            e.preventDefault();
            var form = $(this);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(() => {
                                table.ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
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
