@extends('layouts.app')
@section('judul','Daftar Cuti')
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
                                <td>{{ optional($c->user)->name }}</td>
                                <td>{{ optional($c->user->unitBagian)->nama }}</td>
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
                                     @elseif($c->status_pengajuan == 'Ditangguhkan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #fd7e14; font-size: 11px; border-radius: 12px;">Ditangguhkan</span>
                                     @elseif($c->status_pengajuan == 'Perubahan')
                                         <span class="badge text-white px-2 py-1" style="background-color: #6f42c1; font-size: 11px; border-radius: 12px;">Perubahan</span>
                                     @else
                                         <span class="badge badge-secondary px-2 py-1" style="font-size: 11px; border-radius: 12px;">Menunggu</span>
                                     @endif
                                 </td>
                                <td class="text-center">
                                    <a href="{{ route('cuti.admin.edit', $c->id) }}" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')" class="d-inline" action="{{ route('cuti.admin.destroy', $c->id) }}" method="POST" style="margin-left:6px;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('cuti.admin.show', $c->id) }}" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
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
