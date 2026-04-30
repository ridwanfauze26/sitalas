@extends('layouts.app')

@section('judul', 'Buat Surat Keputusan')

@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0 text-white">
                    <a class="btn btn-sm btn-danger" href="{{route('surat-keputusan.index')}}" role="button">Kembali ke Daftar Surat Keputusan</a>
                </h4>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{route('surat-keputusan.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Nomor Surat</label>
                            <div class="col-md-9">
                                <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tanggal Surat</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="tanggal_surat" value="{{ old('tanggal_surat', Carbon\Carbon::now()->format('Y-m-d'))}}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Judul Surat</label>
                            <div class="col-md-9">
                                <input type="text" name="judul_surat" value="{{ old('judul_surat') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Daftar Pegawai</label>
                            <div class="col-md-9">
                                <a href="javascript:checkAll()" class="btn btn-dark">Pilih Semua</a>&nbsp;&nbsp;
                                <a href="javascript:unCheckAll()" class="btn btn-light">Hapus Pilih Semua</a><br><br>
                                <div class="demo-checkbox overflow-auto" style="max-height:400px;">
                                    @foreach($users as $index => $user)
                                        @php
                                            $jabatan = json_decode($user->jabatan, true);
                                        @endphp
                                        <input type="checkbox" id="user{{$user->id}}" class="filled-in" name="user_id[]" value="{{$user->id}}" {{ in_array($user->id, old('user_id', [])) ? 'checked' : '' }}/>
                                        <label for="user{{$user->id}}" class="mr-3">{{$index+1}}. <strong>{{ $jabatan['nama'] ?? '' }}</strong> - {{$user->name}}</label><br/>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Berkas Scan Surat</label>
                            <div class="col-md-9">
                                <input type="file" id="input-file-now" class="dropify" name="file" accept=".pdf,.doc,.docx" data-allowed-file-extensions="pdf doc docx" data-max-file-size="10M" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew==" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();

        // Used events
        var drEvent = $('#input-file-now').dropify();

        drEvent.on('dropify.beforeClear', function(event, element) {
            return confirm("Yakin ingin menghapus file ini : \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element) {
            alert('File telah dihapus');
        });

        drEvent.on('dropify.errors', function(event, element) {
            console.log('Terjadi kesalahan');
        });

        // Select2
        $('.select2').select2({
            placeholder: 'Pilih satu atau lebih pengguna',
            allowClear: true,
            width: '100%'
        });
    });

    function checkAll()
    {
        let list = document.getElementsByName("user_id[]");
        let b=0;
        for (b=0;b<list.length;b++)
        {
            list[b].checked=true;
        }
    }

    function unCheckAll()
    {
        let list = document.getElementsByName("user_id[]");
        let b=0;
        for (b=0;b<list.length;b++)
        {
            list[b].checked=false;
        }
    }
</script>
@endpush
