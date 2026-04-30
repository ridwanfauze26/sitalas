@extends('layouts.app')
@section('judul','Laporan Surat Masuk')
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
                    @if (Auth::user()->role == 'admin'|| Auth::user()->role == 'verifikator' || Auth::user()->role == 'pegawai')
                    <a class="btn btn-primary m-1" href="{{route('surat-masuk.create')}}" role="button">Tambah Surat Masuk</a>
                    @endif
                </div>
                </h6>
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="text-wrap">Tanggal Penerimaan</th>
                                @if(Auth::user()->role != 'pegawai')
                                <th class="text-wrap">Nomor Agenda</th>
                                <th>Nomor Surat</th>
                                <th>Pengirim</th>
                                <th>Isi Singkat</th>
                                <th>Penerima</th>
                                <th>Status</th>@endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        
                    </table>
                    <!-- Modal Surat-->
                    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel">Detail Surat</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <iframe height="700" id="berkas_scan">
                                            </iframe>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    
                                    </div>
                                </div>
                                </div>
                            </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection  

@push('js')
    <script>
    function getSurat(namaFile){
    var berkas_scan=document.getElementById("berkas_scan");
    berkas_scan.setAttribute("src","{{url('/berkas/suratmasuk/')}}"+'/'+namaFile);
    console.log("src","{{url('/berkas/suratmasuk/')}}"+'/'+namaFile);
}
    let userRole = '{{Auth::user()->role}}';
    
    const columnFormat = [
            { data: null, orderable: false },
            { data: 'date', name: 'date' },
            { data: 'nomor_agenda', name: 'nomor_agenda'},
            { data: 'nomor_surat', name: 'nomor_surat' },
            { data: 'pengirim', render:function(data)
            {
                if(data){
                    return '<div class="text-wrap" style="line-height:1.5">'+data+'</div>'
                }
                
                return '';
            } },
            { data: 'isi_singkat', render:function(data)
            {
                if(data){
                    return '<div class="text-wrap" style="line-height:1.5">'+data+'</div>'
                }
                
                return '';
            } },
            { data: 'name', name: 'name' },
            { data: 'verifikasi',
            render: function(data, type, row){
                switch(data){
                    case 1 :
                        if(row.disposisi_ke != '[]')
                            return '<div class="p-2 text-center bg-success">Sudah Disposisi</div>' ;
                        else
                            return '<div class="p-2 text-center bg-warning">Disposisi</div>' ;
                        break;
                    default :
                        return '<div class="p-2 text-center bg-danger">Belum Disposisi</div>';
                }
            } },
            { data: 'action', name: 'action', orderable: false, searchable: false}
        ];
    if(userRole == 'pegawai'){
       columnFormat.splice(2,6);
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
        ajax: '{{route("surat_masuk")}}',
        columns: columnFormat,
        columnDefs: [{ className: "text-wrap", "targets": [] }
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
function getSurat(namaFile){
    var berkas_scan=document.getElementById("berkas_scan");
    berkas_scan.setAttribute("src","{{url('/berkas/suratmasuk/')}}"+'/'+namaFile);
}

    </script>
@endpush