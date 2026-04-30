@extends('layouts.app')
@section('judul','Data Jabatan')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Level</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jabatan as $index => $k)
                            <tr>
                                <td class="text-center">{{$index+1}}</td>
                                <td>{{$k->nama}}</td>
                                <td class="text-center">{{$k->level ?? '-'}}</td>
                                <td class="text-center">
                                    <a href="{{route('jabatan.edit', $k->id)}}" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')" class="d-inline" action="{{route('jabatan.destroy', $k->id)}}" method="POST" id="form-hapus" style="margin-left:6px;">
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0">
                    Tambah Data Jabatan
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('jabatan.store')}}" method="POST">
                    @csrf
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Nama Jabatan</label>
                            <div class="col-md-9">
                            <input type="text" name="nama" value="{{ old('nama') }}" value="" class="form-control @error('nama') is-invalid @enderror" required>
                            @error('nama')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-left col-md-3">Level</label>
                            <div class="col-md-9">
                                <select name="level" class="form-control @error('level') is-invalid @enderror">
                                    <option value="">-</option>
                                    <option value="1" @if(old('level') == '1') selected @endif>1</option>
                                    <option value="2" @if(old('level') == '2') selected @endif>2</option>
                                    <option value="3" @if(old('level') == '3') selected @endif>3</option>
                                </select>
                                @error('level')
                                    <div class="alert alert-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Simpan</button>
                    </div>
                </form>
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