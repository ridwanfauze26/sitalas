@extends('layouts.app')
@section('judul','Edit Surat Keluar')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0 text-white">
                    <a class="btn btn-sm btn-danger" href="{{route('surat-keluar.index')}}" role="button">Kembali ke Laporan Surat Keluar</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('surat-keluar.update', $suratkeluar->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Berkas Scan Surat Keluar</label>
                            <div class="col-md-9">
                                <input type="file" id="input-file-now" class="dropify" name="berkas_scan" placeholder="Masukkan berkas PDF" accept=".pdf" data-default-file="{{url('/berkas/suratkeluar/'.$suratkeluar->berkas_scan)}}">
                            </div>
                        </div>
                        @if($suratkeluar->berkas_scan!='Belum Upload')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Surat Keluar</label>
                            <div class="col-md-9">
                                <iframe class="col-12" height="500" src="{{url('/berkas/suratkeluar/'.$suratkeluar->berkas_scan)}}">
                                </iframe>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tanggal Surat</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" value="{{$suratkeluar->tanggal_surat}}" required name="tanggal_surat" id="tanggal_surat" onchange="genNumber()" placeholder="hh/bb/tttt">
                            </div>
                        </div>
                        @if(Auth::user()->role  == 'admin')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Permintaan Surat</label>
                            <div class="col-md-9">
                                <select class="form-control custom-select select2" name="user" id="user" required>
                                    @foreach($user as $u)
                                    <option value="{{$u->id}}"  @if($u->id == $suratkeluar->user_id) selected="selected" @endif>{{$u->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Kode/Klasifikasi Surat</label>
                            <div class="col-md-9">
                                <select class="form-control custom-select select2" name="kode_surat" id="kode_surat" onchange="genNumber()">
                                    <option value="">==Pilih==</option>
                                    @foreach($klasifikasi as $k)
                                    <option value="{{$k->kode}}" @if($suratkeluar->kode_surat == $k->kode) selected="selected" @endif>{{$k->kode}} - {{$k->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Sifat Surat</label>
                            <div class="col-md-9">
                            <select class="form-control custom-select select2" name="sifat_surat" id="sifat_surat" onchange="genNumber()" required >
                                <option value="" {{ $suratkeluar->sifat_surat == ''? 'selected' : '' }}>==Pilih==</option>                                   
                                <option value="Biasa" {{ $suratkeluar->sifat_surat == 'Biasa'? 'selected' : '' }}>Biasa</option>
                                <option value="Terbatas" {{ $suratkeluar->sifat_surat == 'Terbatas'? 'selected' : '' }}>Terbatas</option>
                                <option value="Rahasia" {{ $suratkeluar->sifat_surat == 'Rahasia'? 'selected' : '' }}>Rahasia</option>
                                <option value="Segera" {{ $suratkeluar->sifat_surat == 'Segera'? 'selected' : '' }}>Segera</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Nomor Surat</label>
                            <div class="col-md-9">
                            <input type="text" name="nomor_surat" id="nomor_surat" value="{{$suratkeluar->nomor_surat}}" class="form-control" onchange="genNumber()" required>
                            </div>
                        </div>
                        @endif
                      
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tujuan</label>
                            <div class="col-md-9">
                            <input type="text" name="tujuan" value="{{$suratkeluar->tujuan}}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Isi Singkat/Perihal</label>
                            <div class="col-md-9">
                            <textarea name="isi_singkat" class="form-control" required>{{$suratkeluar->isi_singkat}}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tanggal Pengiriman</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="tanggal_pengiriman" value="{{ $suratkeluar->tanggal_pengiriman }}" placeholder="hh/bb/tttt" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Media Pengiriman</label>
                            <div class="col-md-9">
                                <select class="form-control" name="media_pengiriman" required>
                                    <option value="" {{ $suratkeluar->media_pengiriman == ''? 'selected' : '' }}>==Pilih media==</option>
                                    <option value="whatsapp" {{ $suratkeluar->media_pengiriman == 'whatsapp'? 'selected' : '' }}>Whatsapp</option>
                                    <option value="email" {{ $suratkeluar->media_pengiriman == 'email'? 'selected' : '' }}>Email</option>
                                    <option value="whatsapp" {{ $suratkeluar->media_pengiriman == 'post'? 'selected' : '' }}>Post</option>
                                    <option value="email" {{ $suratkeluar->media_pengiriman == 'jne'? 'selected' : '' }}>JNE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Simpan</button>
                        <!-- <button type="reset" class="btn btn-inverse">Reset</button> -->
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
<script>
    $(document).ready(function() {
        // Basic
        $('.dropify').dropify();

        // Used events
        var drEvent = $('#input-file-events').dropify();

        drEvent.on('dropify.beforeClear', function(event, element) {
            return confirm("Yakin ingin menghapus file ini : \"" + element.file.name + "\" ?");
        });

        drEvent.on('dropify.afterClear', function(event, element) {
            alert('File telah dihapus');
        });

        drEvent.on('dropify.errors', function(event, element) {
            console.log('Terjadi kesalahan');
        });

        var drDestroy = $('#input-file-to-destroy').dropify();
        drDestroy = drDestroy.data('dropify')
        $('#toggleDropify').on('click', function(e) {
            e.preventDefault();
            if (drDestroy.isDropified()) {
                drDestroy.destroy();
            } else {
                drDestroy.init();
            }
        })
    });
    function genNumber() {
        let klasifikasi = document.getElementById("kode_surat").value;
        let sifat = document.getElementById("sifat_surat").value;
        let tanggal = document.getElementById("tanggal_surat").value;
        let x = document.getElementById("nomor_surat");
        
        if (tanggal.length == 0 || klasifikasi.length == 0 || sifat.length == 0) {
            x.setAttribute("value", "");
            return;
        }
        
        let tglArr = tanggal.split("-");
       
        let url = "{{route('no_surat', ':date')}}";
        url = url.replace(':date',tanggal);
        const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
             if(xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                var temp = JSON.parse(this.responseText);
                var number = 1;
                if(temp.data != null){
                    number = temp.data.substring(4,7);
                    ++number;
                }

                no = number.toString();
                switch(no.length){
                 case 1:
                    no = "00"+no;
                    break;
                 case 2:
                    no = "0"+no;
                    break;
                 default:
                    no;
                    break;
                }
                
                var nomor =  sifat.substring(0,1)+"-"+tglArr[2]+no+"/"+klasifikasi+"/F5.A/"+tglArr[1]+"/"+tglArr[0];
                x.setAttribute("value", nomor);
                }
            }
        xmlhttp.open("GET", url);
        xmlhttp.send();
    }
    </script>
@endpush