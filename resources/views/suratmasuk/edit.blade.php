@extends('layouts.app')
@section('judul','Edit Surat Masuk')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0 text-white">
                    <a class="btn btn-sm btn-danger" href="{{route('surat-masuk.index')}}" role="button">Kembali ke Laporan Surat Masuk</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('surat-masuk.update', $suratmasuk->id)}}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="form-body">
                        @if(isset($suratmasuk->nomor_agenda))
                            @if($suratmasuk->nomor_agenda != NULL)
                            @php 
                                    $nomoragenda = $suratmasuk->nomor_agenda; 
                            @endphp
                            @endif
                        @else
                            @php $temp = explode('-',$sm->nomor_agenda);
                             $nomoragenda = ++$temp[0].'-'.date('Y');  @endphp
                        @endif
                        @if(Auth::user()->role!='pegawai')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Nomor Agenda</label>
                            <div class="col-md-9">
                            <input type="text" name="nomor_agenda" value="{{ old('nomor_agenda',$nomoragenda) }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tanggal Penerimaan</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="tanggal_penerimaan" value="{{ $suratmasuk->tanggal_penerimaan }}" placeholder="hh/bb/tttt" required>
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->role=='admin' || Auth::user()->role=='pegawai')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Berkas Scan Surat Masuk</label>
                            <div class="col-md-9">
                                <input type="file" id="input-file-now" class="dropify" name="berkas_scan" placeholder="Masukkan berkas PDF" accept=".pdf" data-default-file="{{url('/berkas/suratmasuk/'.$suratmasuk->berkas_scan)}}">
                            </div>
                        </div>
                        @endif
                        @if(Auth::user()->role!='pegawai')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Kode/Klasifikasi Surat</label>
                            <div class="col-md-9">
                                <select class="form-control custom-select select2" name="kode_surat" required>
                                    @foreach($klasifikasi as $k)
                                    <option value="{{$k->kode}}" @if($suratmasuk->kode_surat == $k->kode) selected="selected" @endif>{{$k->kode}} - {{$k->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Nomor Surat</label>
                            <div class="col-md-9">
                            <input type="text" name="nomor_surat" value="{{ $suratmasuk->nomor_surat }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tanggal Surat</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" value="{{ $suratmasuk->tanggal_surat }}" required name="tanggal_surat" placeholder="hh/bb/tttt">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Pengirim</label>
                            <div class="col-md-9">
                            <input type="text" name="pengirim" value="{{ $suratmasuk->pengirim }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Isi Singkat/Perihal</label>
                            <div class="col-md-9">
                            <textarea name="isi_singkat" class="form-control" required>{{ $suratmasuk->isi_singkat }}</textarea>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Surat Masuk</label>
                            <div class="col-md-9">
                                <iframe class="col-12" height="500" src="{{url('/berkas/suratmasuk/'.$suratmasuk->berkas_scan)}}">
                                </iframe>
                            </div>
                        </div>
                        @if(Auth::user()->role == 'verifikator' || Auth::user()->role == 'admin')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Verifikasi</label>
                            <div class="col-md-9">
                                <div class="demo-checkbox">
                                    <input type="checkbox"  class="filled-in" name="verifikasi" value="1" {{ $suratmasuk->verifikasi ? 'checked' : '' }}/>
                                    <label class="mr-3">Sesuai</label><br/>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->role == 'verifikator')
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Catatan Pemeriksaan</label>
                            <div class="col-md-9">
                            <textarea name="catatan" class="form-control">{{ $suratmasuk->catatan }}</textarea>
                            </div>
                        </div>
                        @endif
                        @endif
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala')
                        @if($suratmasuk->verifikasi)
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Sifat Surat</label>
                            <div class="col-md-9">
                            <input name="sifat" list="sifat" value="{{ $suratmasuk->sifat }}" class="form-control" required>
                            <datalist id="sifat">
                                <option value="SEGERA">
                                <option value="PENTING">
                                <option value="RAHASIA">
                                <option value="BIASA">
                                <option value="UNDANGAN">
                              </datalist>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Disposisi Kepada</label>
                            <div class="col-md-9">
                            <a href="javascript:checkAll()" class="btn btn-dark">Pilih Semua</a>&nbsp;&nbsp;
                            <a href="javascript:unCheckAll()" class="btn btn-light">Hapus Pilih Semua</a><br><br>
                                <div class="demo-checkbox overflow-auto" style="max-height:400px;">
                                    @foreach($pamong as $index => $p)
                                    <input type="checkbox" id="disposisi{{$p->id}}" class="filled-in" name="disposisi_ke[]" value="{{$p->id}}" @if(in_array($p->id, json_decode($suratmasuk->disposisi_ke == NULL ? '[]' : $suratmasuk->disposisi_ke))) checked @endif />
                                    <label for="disposisi{{$p->id}}" class="mr-3">{{$index+1}}. <b>{{$p->jabatan->nama}}</b> - {{$p->name}}</label><br/>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Disposisi</label>
                            <div class="col-md-9">
                                <select class="form-control custom-select select2" name="disposisi" required>
                                    <option value="0" {{ $suratmasuk->disposisi == '0'? 'selected' : '' }}>==Pilih Disposisi==</option>
                                    <option value="1" {{ $suratmasuk->disposisi == '1'? 'selected' : '' }}>Diketahui dan dipergunakan seperlunya</option>
                                    <option value="2" {{ $suratmasuk->disposisi == '2'? 'selected' : '' }}>Untuk mendapatkan penyelesaian</option>
                                    <option value="3" {{ $suratmasuk->disposisi == '3'? 'selected' : '' }}>Konsultasikan dengan kepala balai</option>
                                    <option value="4" {{ $suratmasuk->disposisi == '4'? 'selected' : '' }}>Edaran ke seluruh staf</option>
                                    <option value="5" {{ $suratmasuk->disposisi == '5'? 'selected' : '' }}>Untuk dilakukan pengujian</option>
                                    <option value="6" {{ $suratmasuk->disposisi == '6'? 'selected' : '' }}>Diketahui dan dilaksanakan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Isi Disposisi</label>
                            <div class="col-md-9">
                            <textarea name="isi_disposisi" class="form-control">{{ $suratmasuk->isi_disposisi }}</textarea>
                            </div>
                        </div>
                        @else
                        <div class="form-group row">
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                            <h3 class="bg-info text-white text-center">Surat dalam proses pemeriksaan dan belum dapat didisposisikan</h3>
                            </div>
                        </div>
                        @endif
                        @endif
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

    function checkAll()
{
    let list = document.getElementsByName("disposisi_ke[]");
    let b=0;
    for (b=0;b<list.length;b++)
    {
        list[b].checked=true;
        
    }
}

function unCheckAll()
{
    let list = document.getElementsByName("disposisi_ke[]");
    let b=0;
    for (b=0;b<list.length;b++)
    {
        list[b].checked=false;
        
    }
}
    </script>
@endpush