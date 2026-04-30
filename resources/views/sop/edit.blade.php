@extends('layouts.app')

@section('judul', 'Edit SOP')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0 text-white">
                    <a class="btn btn-sm btn-danger" href="{{ route('sop.index') }}" role="button">Kembali ke Daftar SOP</a>
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

                <form method="POST" action="{{ route('sop.update', $sop->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Judul SOP</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                       name="judul" value="{{ old('judul', $sop->judul) }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Tanggal</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                       name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::parse($sop->tanggal)->format('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">File SOP</label>
                            <div class="col-md-9">
                                <input type="file" id="input-file-now" class="dropify" name="file_sop" placeholder="Masukkan berkas PDF" accept=".pdf" data-default-file="{{ asset($sop->file_sop) }}">
                            </div>
                        </div>

                        @if($sop->file_sop)
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">File Saat Ini</label>
                            <div class="col-md-9">
                                <iframe class="col-12" height="500" src="{{ route('sop.view', $sop->id) }}?t={{ time() }}">
                                </iframe>
                            </div>
                        </div>
                        @endif

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw==" crossorigin="anonymous" />
<style>
    .dropify-wrapper .dropify-message span.file-icon {
        color: #1e88e5;
    }
    .dropify-wrapper .dropify-preview .dropify-render i {
        color: #1e88e5;
    }
    .dropify-wrapper .dropify-clear {
        color: #1e88e5;
    }
    .dropify-wrapper .dropify-preview .dropify-infos .dropify-infos-inner p {
        color: #1e88e5;
    }
</style>
@endpush

@push('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew==" crossorigin="anonymous"></script>
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
    });
</script>
@endpush 