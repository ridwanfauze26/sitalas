@extends('layouts.app')

@section('judul', 'Detail SOP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <a href="{{ route('sop.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        @if(Auth::user()->role != 'kepala' && Auth::user()->role != 'pegawai')
                        <a href="{{ route('sop.edit', $sop->id) }}" class="btn btn-warning">
                            Edit
                        </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Judul SOP</th>
                                <td>{{ $sop->judul }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($sop->tanggal)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>File SOP</th>
                                <td>
                                    @php
                                        $extension = pathinfo($sop->file_sop, PATHINFO_EXTENSION);
                                        $fileUrl = route('sop.view', $sop->id);
                                    @endphp

                                    @if ($extension === 'pdf')
                                        <p>
                                            <i class="far fa-file-pdf text-danger fa-2x"></i>
                                            <a href="{{ $fileUrl }}" target="_blank">{{ $sop->file_sop }}</a>
                                        </p>
                                        <iframe src="{{ $fileUrl }}?t={{ time() }}" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> File ini bukan format PDF. Silakan <a href="{{ $fileUrl }}" target="_blank">unduh file</a> untuk melihat isinya.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ \Carbon\Carbon::parse($sop->created_at)->format('d F Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td>{{ \Carbon\Carbon::parse($sop->updated_at)->format('d F Y H:i:s') }}</td>
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
