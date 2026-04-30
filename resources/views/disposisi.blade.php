@extends('layouts.app')
@section('judul','Laporan Disposisi')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu Disposisi</th>
                                <th>Sifat</th>
                                <th>Isi Singkat Surat</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                    <!-- Modal Response-->
                    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="responseModalLabel">Tanggapi Disposisi</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data" id="form-reaction">
                                            @csrf
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                    <input type="text" name="hasil_disposisi" class="form-control" autofocus placeholder="Masukkan laporan progres disini" required>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                     </form>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <!-- Modal Detail Disposisi-->
                    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel">Detail Disposisi</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Nomor Agenda</label>
                                        <div class="col-md-3">
                                        <input type="text" name="nomor_agenda" id="nomor_agenda" class="form-control" disabled>
                                        </div>
                                        <label class="control-label text-center col-md-3">Nomor Surat</label>
                                        <div class="col-md-3">
                                        <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Tanggal Penerimaan</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="tanggal_penerimaan" id="tanggal_penerimaan"  disabled>
                                        </div>
                                        <label class="control-label text-center col-md-3">Tanggal Surat</label>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" id="tanggal_surat" name="tanggal_surat" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Sifat Surat</label>
                                        <div class="col-md-3">
                                            <input type="text" name="sifat" id="sifat" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Pengirim</label>
                                        <div class="col-md-9">
                                        <input type="text" name="pengirim" id="pengirim" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Isi Singkat/Perihal</label>
                                        <div class="col-md-9">
                                        <textarea name="isi_singkat" class="form-control" disabled id="isi"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Disposisi</label>
                                        <div class="col-md-9">
                                            <input type="text" name="disposisi" id="disposisi" class="form-control" disabled>
                                            
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Isi Disposisi</label>
                                        <div class="col-md-9">
                                        <textarea name="isi_disposisi" class="form-control" disabled id="isi_disposisi"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="control-label text-left col-md-3">Berkas Scan Surat Masuk</label>
                                        <div class="col-md-9">
                                            <iframe class="col-12" height="700" id="berkas_scan">
                                            </iframe>
                                        </div>
                                    </div>
                                                        
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    const columnData = [
            { data: null, orderable: false },
            { data: 'date',name: 'date'},
            { data: 'sifat', name: 'sifat' },
            { data: 'isi_singkat', render:function(data)
            {
                if(data){
                    return '<div class="text-wrap" style="line-height:1.5">'+data+'</div>'
                }
                
                return '';
            } },
            { data: 'status', render:function(data){
                if(data){
                    return '<div class="p-2 text-center bg-success">Sudah Ditanggapi</div>' ;
                }
                return '<div class="p-2 text-center bg-danger">Belum Ditanggapi</div>' ;
            } },
            { data: 'action', name: 'action', orderable: false, searchable: false}
        ];
    
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
        ajax: '{{route("get_disposisi")}}',
        columns: columnData,
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
function setLink(link){
        let x = "{{route('respon',':link')}}";
        x = x.replace(':link',link);
        let form = document.getElementById('form-reaction');
        form.setAttribute('action',x);
    }

    var nomor_agenda = document.getElementById("nomor_agenda");
    var nomor_surat = document.getElementById("nomor_surat");
    var tanggal_penerimaan = document.getElementById("tanggal_penerimaan");
    var tanggal_surat = document.getElementById("tanggal_surat");
    var sifat=document.getElementById("sifat");
    var pengirim=document.getElementById("pengirim");
    var isi=document.getElementById("isi");
    var disposisi=document.getElementById("disposisi");
    var isi_disposisi=document.getElementById("isi_disposisi");
    var berkas_scan=document.getElementById("berkas_scan");
    async function getDisposisi(id) {
    let response = await fetch('{{route("get_disposisi")}}');
    let data = await response.text();
    let json = JSON.parse(data);
    const disposisiValue = ['',
                            'Diketahui dan dipergunakan seperlunya',
                            'Untuk mendapatkan penyelesaian',
                            'Konsultasikan dengan kepala balai',
                            'Edaran ke seluruh staf',
                            'Untuk dilakukan pengujian',
                            'Diketahui dan dilaksanakan']

        var results = [];
        var searchField = "id";
        var searchVal = id;
        for (var i=0 ; i < json.data.length ; i++)
        {
            if (json.data[i][searchField] == searchVal) {
                results.push(json.data[i]);
            }
        }
        nomor_agenda.value = results[0].noAgenda;
        nomor_surat.value = results[0].nomor_surat;
        tanggal_penerimaan.value = results[0].tanggal_penerimaan;
        tanggal_surat.value = results[0].tanggal_surat;
        sifat.value = results[0].sifat_surat;
        pengirim.value = results[0].pengirim;
        isi.value = results[0].isi_singkat;
        disposisi.value = disposisiValue[results[0].disposisi];
        isi_disposisi.value = results[0].isi_disposisi;
        berkas_scan.setAttribute("src","{{url('/berkas/suratmasuk/')}}"+'/'+results[0].berkas_scan);
    }
   
    </script>
@endpush