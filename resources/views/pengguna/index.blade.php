@extends('layouts.app')
@section('judul','Manajemen Pengguna')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle">
                    <a class="btn btn-primary btn-sm" href="{{route('pengguna.create')}}" role="button">Tambah Pengguna Baru</a>
                </h6>
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP/NIK</th>
                                <th>Nama</th>
                                <th>Unit Bagian</th>
                                <th>Jabatan</th>
                                <th>Role</th>
                                <th>e-Mail</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengguna as $index => $k)
                            <tr>
                                <td class="text-center">{{$index+1}}</td>
                                <td>{{$k->nip}}</td>
                                <td>{{$k->name}}</td>
                                <td>{{$k->unitBagian->nama ?? ''}}</td>
                                <td>{{$k->jabatan->nama}}</td>
                                <td class="text-uppercase">{{$k->role}}</td>
                                <td>{{$k->email}}</td>
                                <td class="text-center">
                                    <a href="{{route('pengguna.edit', $k->id)}}" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')" class="d-inline" action="{{route('pengguna.destroy', $k->id)}}" method="POST" id="form-hapus{{$index+1}}" style="margin-left:6px;">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
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
    $(document).ready(function() {
        $('#table').DataTable({
            order: [[ 0, 'asc' ]]
        });
    });
    </script>
@endpush