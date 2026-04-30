@extends('layouts.app')
@section('judul','Detail Surat Keputusan')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <a href="{{ route('surat-keputusan.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        @if(Auth::user()->role == 'admin')
                        <a href="{{ route('surat-keputusan.edit', $suratKeputusan->id) }}" class="btn btn-warning">
                            Edit
                        </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Nomor Surat</th>
                                <td>{{ $suratKeputusan->nomor_surat }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Surat</th>
                                <td>{{ \Carbon\Carbon::parse($suratKeputusan->tanggal_surat)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Judul Surat</th>
                                <td>{{ $suratKeputusan->judul_surat }}</td>
                            </tr>
                            <tr>
                                <th>Daftar Pegawai</th>
                                <td>
                                    <ol>
                                        @foreach($suratKeputusan->users as $user)
                                            @php
                                                $jabatan = json_decode($user->jabatan, true);
                                            @endphp
                                            <li><strong>{{ $jabatan['nama'] ?? '' }}</strong> - {{ $user->name }}</li>
                                        @endforeach
                                    </ol>
                                </td>
                            </tr>
                            @if($suratKeputusan->file)
                            <tr>
                                <th>Berkas Surat Keputusan</th>
                                <td>
                                    @php
                                        $ext = pathinfo($suratKeputusan->file, PATHINFO_EXTENSION);
                                        $icon = '';
                                        switch($ext) {
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
                                            default:
                                                $icon = 'far fa-file text-secondary';
                                        }
                                    @endphp

                                    <p>
                                        <i class="{{ $icon }} fa-2x"></i>
                                        <a href="{{ asset('berkas/suratkeputusan/'.$suratKeputusan->file) }}" target="_blank">{{ $suratKeputusan->file }}</a>
                                    </p>

                                    @if(in_array($ext, ['pdf']))
                                        <iframe src="{{ asset('berkas/suratkeputusan/'.$suratKeputusan->file) }}?t={{ time() }}" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
                                    @else
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i> File ini tidak dapat ditampilkan sebagai preview. Silakan <a href="{{ asset('berkas/suratkeputusan/'.$suratKeputusan->file) }}" target="_blank">unduh atau buka file</a> untuk melihat isinya.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ \Carbon\Carbon::parse($suratKeputusan->created_at)->translatedFormat('d F Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diperbarui</th>
                                <td>{{ \Carbon\Carbon::parse($suratKeputusan->updated_at)->translatedFormat('d F Y H:i:s') }}</td>
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
