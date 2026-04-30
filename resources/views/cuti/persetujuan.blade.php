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
                                <th>Jenis Cuti</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status L1</th>
                                <th>Status L2</th>
                                <th>Status Final</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuti as $index => $c)
                            <tr>
                                <td class="text-center">{{ $index+1 }}</td>
                                <td>{{ optional($c->user)->name }}</td>
                                <td>{{ $c->jenis_cuti }}</td>
                                <td>{{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d-m-Y') : '' }}</td>
                                <td>{{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d-m-Y') : '' }}</td>
                                <td class="text-center">{{ $c->status_level1 }}</td>
                                <td class="text-center">{{ $c->status_level2 }}</td>
                                <td class="text-center">{{ $c->status_pengajuan }}</td>
                                <td class="text-center">
                                    @if(Auth::user()->role == 'admin')
                                        <select class="form-control form-control-sm d-inline-block" style="width:120px;height:30px;padding:0 6px;">
                                            @if($c->status_level1 == 'Menunggu')
                                                <option value="1" selected>Level 1</option>
                                            @endif
                                            @if($c->status_level2 == 'Menunggu')
                                                <option value="2" {{ $c->status_level1 != 'Menunggu' ? 'selected' : '' }}>Level 2</option>
                                            @endif
                                        </select>
                                    @endif
                                    <form action="{{ route('cuti.persetujuan.approve', $c->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if(Auth::user()->role == 'admin')
                                            <input type="hidden" name="level" value="">
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('cuti.persetujuan.reject', $c->id) }}" method="POST" class="d-inline" style="margin-left:6px;" onsubmit="return confirm('Yakin ingin menolak pengajuan cuti ini?')">
                                        @csrf
                                        @if(Auth::user()->role == 'admin')
                                            <input type="hidden" name="level" value="">
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-times"></i>
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
@endsection

@push('js')
<script>
$(function() {
    $('#table').DataTable({
        "ordering": false
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
