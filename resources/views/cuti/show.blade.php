@extends('layouts.app')
@section('judul', Auth::user()->role == 'admin' ? 'Detail Cuti' : 'HALAMAN USER')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0 text-white">
                    <a class="btn btn-sm btn-danger" href="{{ Auth::user()->role == 'admin' ? route('cuti.admin.index') : route('cuti.index') }}" role="button">Kembali</a>
                    @if($cuti->status_pengajuan === 'Disetujui')
                        <a class="btn btn-sm btn-success" href="{{ route('cuti.pdf', ['id'=>$cuti->id, 'qr'=>0]) }}" target="_blank" rel="noopener" role="button" style="margin-left:8px;">Cetak PDF</a>
                        <a class="btn btn-sm btn-primary" href="{{ route('cuti.pdf', ['id'=>$cuti->id, 'qr'=>1]) }}" target="_blank" rel="noopener" role="button" style="margin-left:8px;">Cetak PDF QRCode</a>
                    @endif
                </h4>
            </div>
            <div class="card-body">
                <div class="form-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ optional($cuti->user)->name }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Jenis Cuti</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->jenis_cuti }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alasan Cuti</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->alasan_cuti }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Lama Cuti</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->lama_cuti }}" readonly>
                        </div>
                    </div>
                    @if($cuti->jenis_cuti === 'cuti_tahunan')
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Lama Cuti (Hari Kerja)</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->lama_cuti_hari_kerja }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Potong Saldo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="N: {{ (int) $cuti->potong_n }} | N-1: {{ (int) $cuti->potong_n1 }} | N-2: {{ (int) $cuti->potong_n2 }}" readonly>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tanggal Mulai</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" value="{{ $cuti->tanggal_mulai }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tanggal Selesai</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" value="{{ $cuti->tanggal_selesai }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alamat Selama Cuti</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->alamat }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">No. Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->no_telepon }}" readonly>
                        </div>
                    </div>
                    @if($cuti->dokumen_sakit)
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Dokumen Cuti Sakit</label>
                        <div class="col-sm-9">
                            <a class="btn btn-sm btn-info" href="{{ asset('storage/' . $cuti->dokumen_sakit) }}" target="_blank" rel="noopener">Lihat Dokumen</a>
                        </div>
                    </div>
                    @endif
                    @if($cuti->dokumen_ppk)
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Surat Keputusan PPK</label>
                        <div class="col-sm-9">
                            <a class="btn btn-sm btn-info" href="{{ asset('storage/' . $cuti->dokumen_ppk) }}" target="_blank" rel="noopener">Lihat Dokumen</a>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Status Persetujuan Level 1</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->status_level1 }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Status Persetujuan Level 2</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->status_level2 }}" readonly>
                        </div>
                    </div>
                    @if($cuti->rejected_reason)
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alasan Ditolak</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->rejected_reason }}" readonly>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Status Pengajuan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="{{ $cuti->status_pengajuan }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
