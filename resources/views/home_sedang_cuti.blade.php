@extends('layouts.app')
@section('judul','Sedang Cuti')
@section('content')
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
          <div>
            <h4 class="card-title mb-1">Sedang Cuti Hari Ini</h4>
            <small class="text-muted">{{ $today ? \Carbon\Carbon::parse($today)->format('d/m/Y') : '' }}</small>
          </div>
          <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <hr>

        @if(isset($cutiSedang) && $cutiSedang->count())
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Jabatan</th>
                  <th>Jenis</th>
                  <th>Mulai</th>
                  <th>Selesai</th>
                  <th>Lama</th>
                </tr>
              </thead>
              <tbody>
                @foreach($cutiSedang as $c)
                  <tr>
                    <td>{{ optional($c->user)->name }}</td>
                    <td>{{ optional(optional($c->user)->jabatan)->nama }}</td>
                    <td>{{ $c->jenis_cuti }}</td>
                    <td>{{ $c->tanggal_mulai ? \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $c->tanggal_selesai ? \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $c->lama_cuti ? $c->lama_cuti : '-' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div>Belum ada</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
