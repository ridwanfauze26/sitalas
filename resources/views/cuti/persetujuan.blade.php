@extends('layouts.app')
@section('judul','PERSETUJUAN CUTI')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Unit Bagian</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Lama Cuti</th>
                                @if(Auth::user()->role == 'admin')
                                <th>Status L1</th>
                                <th>Status L2</th>
                                <th>Status Final</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuti as $index => $c)
                            <tr>
                                <td class="text-center">{{ $index+1 }}</td>
                                <td>{{ optional($c->user)->name }}</td>
                                <td>{{ optional($c->user->unitBagian)->nama }}</td>
                                <td>{{ $c->jenis_cuti }}</td>
                                <td>{{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') : '' }}</td>
                                <td>{{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') : '' }}</td>
                                <td>{{ $c->lama_cuti }}</td>
                                @if(Auth::user()->role == 'admin')
                                 <td class="text-center">
                                     @if($c->status_level1 == 'Disetujui')
                                         <span class="badge badge-success px-2 py-1" style="font-size: 11px; border-radius: 12px;">Disetujui</span>
                                     @elseif($c->status_level1 == 'Ditolak')
                                         <span class="badge badge-danger px-2 py-1" style="font-size: 11px; border-radius: 12px;">Ditolak</span>
                                     @elseif($c->status_level1 == 'Ditangguhkan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #fd7e14; font-size: 11px; border-radius: 12px;">Ditangguhkan</span>
                                     @elseif($c->status_level1 == 'Perubahan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #6f42c1; font-size: 11px; border-radius: 12px;">Perubahan</span>
                                     @else
                                         <span class="badge badge-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;">Menunggu</span>
                                     @endif
                                 </td>
                                 <td class="text-center">
                                     @if($c->status_level2 == 'Disetujui')
                                         <span class="badge badge-success px-2 py-1" style="font-size: 11px; border-radius: 12px;">Disetujui</span>
                                     @elseif($c->status_level2 == 'Ditolak')
                                         <span class="badge badge-danger px-2 py-1" style="font-size: 11px; border-radius: 12px;">Ditolak</span>
                                     @elseif($c->status_level2 == 'Ditangguhkan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #fd7e14; font-size: 11px; border-radius: 12px;">Ditangguhkan</span>
                                     @elseif($c->status_level2 == 'Perubahan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #6f42c1; font-size: 11px; border-radius: 12px;">Perubahan</span>
                                     @elseif($c->status_level2 == 'Tidak Perlu')
                                         <span class="badge badge-light px-2 py-1 text-muted" style="font-size: 11px; border-radius: 12px; border: 1px solid #e5e7eb;">Tidak Perlu</span>
                                     @else
                                         <span class="badge badge-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;">Menunggu</span>
                                     @endif
                                 </td>
                                 <td class="text-center">
                                     @if($c->status_pengajuan == 'Disetujui')
                                         <span class="badge badge-success px-2 py-1" style="font-size: 11px; border-radius: 12px;">Disetujui</span>
                                     @elseif($c->status_pengajuan == 'Ditolak')
                                         <span class="badge badge-danger px-2 py-1" style="font-size: 11px; border-radius: 12px;">Ditolak</span>
                                     @elseif($c->status_pengajuan == 'Ditangguhkan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #fd7e14; font-size: 11px; border-radius: 12px;">Ditangguhkan</span>
                                     @elseif($c->status_pengajuan == 'Perubahan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #6f42c1; font-size: 11px; border-radius: 12px;">Perubahan</span>
                                     @else
                                         <span class="badge badge-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;">Menunggu</span>
                                     @endif
                                 </td>
                                 @endif
                                <td class="text-center">
                                    @if(Auth::user()->role == 'admin')
                                        <select class="form-control form-control-sm d-inline-block" style="width:120px;height:30px;padding:0 6px;">
                                            @if($c->status_level1 == 'Menunggu')
                                                <option value="1">Level 1</option>
                                            @endif
                                            @if($c->status_level2 == 'Menunggu')
                                                <option value="2" {{ $c->status_level1 == 'Menunggu' ? 'selected' : '' }}>Level 2</option>
                                            @endif
                                        </select>
                                    @endif
                                    <form action="{{ route('cuti.persetujuan.approve', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menyetujui pengajuan cuti ini?')">
                                        @csrf
                                        @if(Auth::user()->role == 'admin')
                                            <input type="hidden" name="level" value="">
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('cuti.persetujuan.reject', $c->id) }}" method="POST" class="d-inline" style="margin-left:6px;" onsubmit="var r = prompt('Masukkan alasan penolakan (opsional):'); if(r === null) return false; $(this).append('<input type=\'hidden\' name=\'rejected_reason\' value=\'' + r + '\'>'); return true;">
                                        @csrf
                                        @if(Auth::user()->role == 'admin')
                                            <input type="hidden" name="level" value="">
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('cuti.persetujuan.postpone', $c->id) }}" method="POST" class="d-inline" style="margin-left:6px;" onsubmit="var r = prompt('Masukkan alasan penangguhan (opsional):'); if(r === null) return false; $(this).append('<input type=\'hidden\' name=\'postponed_reason\' value=\'' + r + '\'>'); return true;">
                                        @csrf
                                        @if(Auth::user()->role == 'admin')
                                            <input type="hidden" name="level" value="">
                                        @endif
                                        <button type="submit" class="btn btn-sm text-white" title="Tangguhkan" style="background-color:#fd7e14;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-pause"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('cuti.persetujuan.change', $c->id) }}" method="POST" class="d-inline" style="margin-left:6px;" onsubmit="var r = prompt('Masukkan rincian perubahan yang diminta (opsional):'); if(r === null) return false; $(this).append('<input type=\'hidden\' name=\'changed_reason\' value=\'' + r + '\'>'); return true;">
                                        @csrf
                                        @if(Auth::user()->role == 'admin')
                                            <input type="hidden" name="level" value="">
                                        @endif
                                        <button type="submit" class="btn btn-sm text-white" title="Perubahan" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </form>

                                    <a href="{{ Auth::user()->role == 'admin' ? route('cuti.admin.show', $c->id) : route('cuti.show', $c->id) }}" class="btn btn-sm btn-primary" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
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

@if(Auth::user()->role!='admin')
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-primary font-weight-bold" style="margin-bottom: 20px;">RIWAYAT CUTI PEGAWAI (DISETUJUI)</h4>
                <div class="table-responsive pb-3">
                    <table id="historyTable" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th>Unit Bagian</th>
                                <th>Jenis Cuti</th>
                                <th class="text-center">Tanggal Mulai</th>
                                <th class="text-center">Tanggal Selesai</th>
                                <th class="text-center">Lama Cuti</th>
                                <th>Alasan Cuti</th>
                                <th class="text-center">Status Final</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyCuti as $index => $c)
                            <tr>
                                <td class="text-center">{{ $index+1 }}</td>
                                <td><strong>{{ optional($c->user)->name }}</strong></td>
                                <td><strong>{{ optional($c->user->unitBagian)->nama }}</strong></td>
                                <td>{{ ucwords(str_replace('_', ' ', $c->jenis_cuti)) }}</td>
                                <td class="text-center">{{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') : '' }}</td>
                                <td class="text-center">{{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') : '' }}</td>
                                <td class="text-center">{{ $c->lama_cuti }} {{ $c->alasan_mode ?: 'Hari' }}</td>
                                <td>{{ $c->alasan_cuti ?: '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-success px-3 py-2" style="font-size: 11px; border-radius: 20px;">Disetujui</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ Auth::user()->role == 'admin' ? route('cuti.admin.show', $c->id) : route('cuti.show', $c->id) }}" class="btn btn-sm btn-primary" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
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
@endif
@endsection

@push('js')
<script>
$(function() {
    $('#table').DataTable({
        "ordering": true
    });
    $('#historyTable').DataTable({
        "ordering": true
    });
});

$(document).on('submit', 'form', function() {
    var select = $(this).closest('td').find('select');
    if(select.length) {
        $(this).find('input[name="level"]').val(select.val());
    }
});
</script>
@endpush
