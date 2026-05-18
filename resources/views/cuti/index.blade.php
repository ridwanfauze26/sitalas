@extends('layouts.app')
@section('judul','HALAMAN USER')
@section('content')
@php
    $needsAttention = $cuti->filter(function($c) {
        return $c->status_pengajuan === 'Perubahan';
    });
@endphp

@if($needsAttention->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 5px solid #6f42c1; background-color: #f3ebff; color: #4e2b8c; margin-bottom: 20px; padding: 15px 20px;">
        <h5 class="alert-heading font-weight-bold mb-2"><i class="fa fa-pencil mr-2"></i> Perlu Perbaikan Data Cuti</h5>
        <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;">
            Terdapat {{ $needsAttention->count() }} pengajuan cuti Anda yang membutuhkan **Perubahan/Revisi** sesuai petunjuk Atasan. Silakan klik tombol **Edit** (ikon pensil <i class="fa fa-pencil"></i>) pada tabel di bawah untuk menyesuaikan data dan **mengirimkan kembali** pengajuan Anda.
        </p>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a class="btn btn-primary mb-3" href="{{ route('cuti.ajukan') }}" role="button">Ajukan Cuti</a>
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal Cuti</th>
                                <th>Tanggal Selesai</th>
                                <th>Lama Cuti</th>
                                <th>Alasan</th>
                                <th>Status Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuti as $index => $c)
                            <tr>
                                <td class="text-center">{{$index+1}}</td>
                                <td>{{$c->jenis_cuti}}</td>
                                <td>{{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') : '' }}</td>
                                <td>{{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') : '' }}</td>
                                <td>{{$c->lama_cuti}}</td>
                                <td>{{$c->alasan_cuti}}</td>
                                  <td class="text-center">
                                      @if($c->status_pengajuan == 'Disetujui')
                                          <span class="badge badge-success px-2 py-1" style="font-size: 11px; border-radius: 12px;">Disetujui</span>
                                      @elseif($c->status_pengajuan == 'Ditolak')
                                          <span class="badge badge-danger px-2 py-1" style="font-size: 11px; border-radius: 12px;">Ditolak</span>
                                          @if($c->rejected_reason)
                                              <div class="mt-1 font-weight-bold" style="font-size: 10px; color: #dc3545; line-height: 1.2;">
                                                  Alasan: {{ $c->rejected_reason }}
                                              </div>
                                          @endif
                                      @elseif($c->status_pengajuan == 'Ditangguhkan')
                                          <span class="badge text-white px-2 py-1" style="background-color: #fd7e14; font-size: 11px; border-radius: 12px;">Ditangguhkan</span>
                                          @if($c->rejected_reason)
                                              <div class="mt-1 font-weight-bold" style="font-size: 10px; color: #fd7e14; line-height: 1.2;">
                                                  Catatan: {{ $c->rejected_reason }}
                                              </div>
                                          @endif
                                      @elseif($c->status_pengajuan == 'Perubahan')
                                          <span class="badge text-white px-2 py-1" style="background-color: #6f42c1; font-size: 11px; border-radius: 12px;">Perubahan</span>
                                          @if($c->rejected_reason)
                                              <div class="mt-1 font-weight-bold" style="font-size: 10px; color: #6f42c1; line-height: 1.2;">
                                                  Revisi: {{ $c->rejected_reason }}
                                              </div>
                                          @endif
                                      @else
                                          <span class="badge badge-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;">Menunggu</span>
                                      @endif
                                  </td>
                                <td class="text-center">
                                    @if($c->status_pengajuan !== 'Disetujui' || Auth::user()->role === 'admin')
                                    <a href="{{ route('cuti.edit', $c->id) }}" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')" class="d-inline" action="{{ route('cuti.destroy', $c->id) }}" method="POST" style="margin-left:6px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('cuti.show', $c->id) }}" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function() {
    $('#table').DataTable({
        "ordering": false
    });
});
</script>
@endpush
