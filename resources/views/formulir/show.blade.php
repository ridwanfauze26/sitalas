@extends('layouts.app')
@section('judul','Detail Formulir')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <a href="{{ route('formulir.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        @if(Auth::user()->role != 'kepala' && Auth::user()->role != 'pegawai')
                        <a href="{{ route('formulir.edit', $formulir->id) }}" class="btn btn-warning">
                            Edit
                        </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Judul Formulir</th>
                                <td>{{ $formulir->judul }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($formulir->tanggal)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>File Formulir</th>
                                <td>
                                    @php
                                        $extension = pathinfo($formulir->file_formulir, PATHINFO_EXTENSION);
                                        $icon = '';
                                        switch($extension) {
                                            case 'pdf':
                                                $icon = 'far fa-file-pdf text-danger';
                                                break;
                                            case 'docx':
                                            case 'doc':
                                                $icon = 'far fa-file-word text-primary';
                                                break;
                                            case 'xlsx':
                                            case 'xls':
                                                $icon = 'far fa-file-excel text-success';
                                                break;
                                        }
                                        $fileUrl = route('formulir.view', $formulir->id);
                                    @endphp

                                    <p>
                                        <i class="{{ $icon }} fa-2x"></i>
                                        <a href="{{ $fileUrl }}" target="_blank">{{ $formulir->file_formulir }}</a>
                                    </p>

                                    @if ($extension === 'pdf')
                                        <iframe src="{{ $fileUrl }}?t={{ time() }}" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
                                    @else
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i> File ini tidak dapat ditampilkan sebagai preview. Silakan <a href="{{ $fileUrl }}" target="_blank">unduh atau buka file</a> untuk melihat isinya.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ \Carbon\Carbon::parse($formulir->created_at)->translatedFormat('d F Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td>{{ \Carbon\Carbon::parse($formulir->updated_at)->translatedFormat('d F Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush
