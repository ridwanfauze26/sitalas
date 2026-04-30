@extends('layouts.app')
@section('judul','Laporan Surat Keluar')
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
                    @if(Auth::user()->role  == 'admin' || Auth::user()->role == 'pegawai' || Auth::user()->role == 'verifikator')
                    <a class="btn btn-primary my-3" href="{{route('surat-keluar.create')}}" role="button">Tambah Surat Keluar</a>
                    @endif
                    @if (Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'verifikator')
                    
                    @endif
                </div>
                </h6>
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                        <tr>
                                <th>No</th>
                                <th>Nomor Surat</th>
                                <th class="text-wrap">Ditujukan Kepada</th>
                                <th class="text-wrap">Isi Singkat</th>
                                @if(Auth::user()->role != 'pegawai')<th>Ajuan</th>@endif
                                <th class="text-wrap">Tanggal Surat</th>
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
<style>
    .line-space{
        line-height: 2;
    }
    </style>
@push('js')
    <script>
    let userRole = '{{Auth::user()->role}}';
    
    const columnFormat = [
            { data: null, orderable: false },
            { data: 'nomor_surat', name: 'nomor_surat' },
            { data: 'tujuan', name: 'tujuan' },
            { data: 'isi_singkat', name: 'isi_singkat' },
            { data: 'name', name: 'name' },
            { data: 'date', name: 'date', render: function(data,type,row)
            {
                if(data){
                    return '<div class="text-wrap">'+data+'</div>'
                }
                
                return '';
            } },
            { data: 'action', name: 'action', orderable: false, searchable: false, render: function(data,type,row)
            {
                if(row.berkas_scan=='Belum Upload'){
                    return '<div class="p-2 bg-warning text-center">'+data+'</div>';
                }
                return '<div class="p-2 text-center">'+data+'</div>';
            }}
        ];
    if(userRole == 'pegawai'){
       columnFormat.splice(4,1);
    }
    $(function() {
    $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings)
                {
                    return {
                        "iStart": oSettings._iDisplayStart,
                        "iEnd": oSettings.fnDisplayEnd(),
                        "iLength": oSettings._iDisplayLength,
                        "iTotal": oSettings.fnRecordsTotal(),
                        "iFilteredTotal": oSettings.fnRecordsDisplay(),
                        "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                        "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
                    };
                };
    $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{route("surat_keluar")}}',
        columns: columnFormat,
        columnDefs: [{ className: "text-wrap", "targets": [2,3] }
                     ],
        "order": [[0, 'asc']],
        "rowCallback": function (row, data, iDisplayIndex) {
                        var info = this.fnPagingInfo();
                        var page = info.iPage;
                        var length = info.iLength;
                        var index = page * length + (iDisplayIndex + 1);
                        $('td:eq(0)', row).html(index);
        }
    });
});
    </script>
@endpush