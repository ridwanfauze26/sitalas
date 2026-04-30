@extends('layouts.app')
@section('judul','Data Unit Bagian')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle">
                    <div class="row">
                        <button type="button" class="btn btn-primary m-1" data-toggle="modal" data-target="#tambahUnitBagianModal">Tambah Unit Bagian</button>
                    </div>
                </h6>
                <div class="table-responsive pb-3">
                    <table id="table" class="display table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Unit Bagian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unitBagian as $index => $u)
                            <tr>
                                <td class="text-center">{{$index+1}}</td>
                                <td>{{$u->nama}}</td>
                                <td class="text-center">
                                    <a href="{{route('unit-bagian.edit', $u->id)}}" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')" class="d-inline" action="{{route('unit-bagian.destroy', $u->id)}}" method="POST" id="form-hapus" style="margin-left:6px;">
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

                <div class="modal fade" id="tambahUnitBagianModal" tabindex="-1" role="dialog" aria-labelledby="tambahUnitBagianModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahUnitBagianModalLabel">Tambah Unit Bagian</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{route('unit-bagian.store')}}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Unit Bagian</label>
                                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required>
                                        @error('nama')
                                            <div class="alert alert-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
