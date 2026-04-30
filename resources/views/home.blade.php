@extends('layouts.app')
@section('judul','Beranda')
@section('content')
<!-- <div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex justify-content-between flex-wrap">
      <div class="d-flex align-items-end flex-wrap">
        <div class="mr-md-3 mr-xl-5">
          <h2>Beranda</h2>
        </div>
      </div>
    </div>
  </div>
</div> -->
@if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'verifikator')
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body dashboard-tabs p-0">
        <div class="tab-content py-0 px-0">
          <div class="d-flex flex-wrap justify-content-xl-between">
            <div class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <a href="{{ route('surat-masuk.index') }}"><i class="mdi mdi-download icon-lg mr-3 text-primary"></i></a>
              <div class="d-flex flex-column justify-content-around">
                <small class="mb-1 text-muted">Jumlah Surat Masuk</small>
                <h5 class="mb-0 d-inline-block">{{ $suratmasuk }}</h5>
                <small class="mb-1 text-muted">Belum Diposisi</small>
                <h5 class="mb-0 d-inline-block">{{ $belumDisposisi }}</h5>
              </div>
            </div>
            <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <a href="{{ route('surat-keluar.index') }}"><i class="mdi mdi-upload mr-3 icon-lg text-danger"></i></a>
              <div class="d-flex flex-column justify-content-around">
                <small class="mb-1 text-muted">Jumlah Surat Keluar</small>
                <h5 class="mr-2 mb-0">{{ $suratkeluar }}</h5>
                <small class="mb-1 text-muted">Surat Keluar Belum Bernomor</small>
                <h5 class="mr-2 mb-0">{{ $belumBernomor }}</h5>
              </div>
            </div>
            @if(Auth::user()->role == 'admin')
            <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <a href="{{ route('klasifikasi-surat.index') }}"><i class="mdi mdi-note-text mr-3 icon-lg text-success"></i></a>
              <div class="d-flex flex-column justify-content-around">
                <small class="mb-1 text-muted">Jumlah Klasifikasi Surat</small>
                <h5 class="mr-2 mb-0">{{ $klasifikasi }}</h5>
              </div>
            </div>
            <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <a href="{{ route('pengguna.index') }}"><i class="mdi mdi-account-check mr-3 icon-lg text-warning"></i></a>
              <div class="d-flex flex-column justify-content-around">
                <small class="mb-1 text-muted">Jumlah Pengguna</small>
                <h5 class="mr-2 mb-0">{{ $user }}</h5>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || (int) Auth::user()->cuti_level === 1 || (int) Auth::user()->cuti_level === 2)
<div class="row">
  <div class="col-12 col-md-3 col-lg-3 grid-margin stretch-card mx-auto">
    <div class="card">
      <div class="card-body dashboard-tabs p-0">
        <div class="tab-content py-0 px-0">
          <div class="d-flex flex-wrap justify-content-xl-between">
            <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
              <i class="mdi mdi-calendar-check icon-lg mr-3 text-primary"></i>
              <div class="d-flex flex-column justify-content-around">
                <small class="mb-1 text-muted">Sedang Cuti Hari Ini</small>
                <h5 class="mb-0 d-inline-block">{{ isset($cutiSedang) ? $cutiSedang->count() : 0 }}</h5>
                @if(isset($cutiSedang) && $cutiSedang->count())
                  <a href="{{ route('home.sedang_cuti') }}" class="text-primary mt-1 d-inline-block" style="font-size: 12px;">Lihat detail</a>
                @else
                  <small class="mb-1 text-muted">Belum ada</small>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row">
        <div class="col-md-6  col-md-offset-1">
        <div class="card">
            <div class="panel panel-default">
                <div class="panel-body">
                    <canvas id="suratmasuk" height="400" width="600"></canvas>
                </div>
            </div>
        </div>
        </div>
        <div class="col-md-6  col-md-offset-1">
        <div class="card">
            <div class="panel panel-default">
                <div class="panel-body">
                    <canvas id="suratkeluar" height="400" width="600"></canvas>
                </div>
            </div>
        </div>
  </div>
    </div>
    <script>

    function barChart2D(id,label,bulansuratmasuk, jumlahsuratmasukperbulan, bgcolor, bordercolor, max) {
        var ctx = document.getElementById(id).getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: bulansuratmasuk,
                datasets: [{
                    label: label,
                    backgroundColor: bgcolor,
                    data: jumlahsuratmasukperbulan
                }]
            },
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 1,
                        borderColor: bordercolor,
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'Tahun {{date("Y")}}'
                },
                scales: {
                  yAxes: [{
                    ticks: {
                      min: 0,
                      max: max,
                      beginAtZero:true,
                      callback: function(value, index, values) {
                        if (Math.floor(value) === value) {
                        return value;
                        }
                      }
                    }
                  }],
                  xAxes: [{
                    gridLines: {
                        display:false
                      },
                    // barPercentage: 0.5
                  }]
                }
            }
        });
    };
    window.onload = function(){
        let data = @json($jumlahsuratmasukperbulan).concat(@json($jumlahsuratkeluarperbulan));
        let max= Math.max(...data);
        if(max%100!=0){
            max = max - max%100 + 100;
        }
      barChart2D('suratmasuk','Surat Masuk',@json($bulansuratmasuk),@json($jumlahsuratmasukperbulan), 'rgba(54, 162, 235, 0.2)','rgba(54, 162, 235, 1)',max);
      barChart2D('suratkeluar','Surat Keluar',@json($bulansuratkeluar),@json($jumlahsuratkeluarperbulan), 'rgba(255, 99, 132, 0.2)', 'rgba(255, 99, 132, 1)',max);
    };
</script>
@else

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body dashboard-tabs p-0">
        <div class="tab-content py-0 px-0">
          <!-- <div class="d-flex flex-wrap justify-content-xl-between">
            <div class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <h3>Selamat datang {{Auth::user()->name}}</h3>
            </div>
          </div> -->
          <div class="d-flex flex-wrap justify-content-xl-between" style="gap:18px;">
          <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <a href="{{ route('surat-keluar.index') }}"><i class="mdi mdi-upload mr-3 icon-lg text-danger"></i></a>
              <div class="d-flex flex-column justify-content-around">
                  @php $nomor_terisi=0;  $nomor_kosong=0@endphp
                  @foreach($suratkeluar as $sk)
                    @php
                    if($sk==NULL)
                      $nomor_kosong++;
                    else
                      $nomor_terisi++;
                    @endphp
                  @endforeach
                <div class="d-flex flex-wrap" style="gap:18px;">
                  <div class="text-center" style="min-width:140px;">
                    <small class="mb-1 text-muted d-block">Total Surat Keluar Ajuan</small>
                    <h5 class="mb-0">{{ count($suratkeluar) }}</h5>
                  </div>
                  <div class="text-center" style="min-width:120px;">
                    <small class="mb-1 text-muted d-block">Belum Bernomor</small>
                    <h5 class="mb-0">{{ $nomor_kosong }}</h5>
                  </div>
                  <div class="text-center" style="min-width:100px;">
                    <small class="mb-1 text-muted d-block">Bernomor</small>
                    <h5 class="mb-0">{{ $nomor_terisi }}</h5>
                  </div>
                </div>
              </div>
            </div>
          <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-center p-3 item">
            <a href="{{ route('disposisi') }}"><i class="mdi mdi-human-greeting menu-icon mr-3 icon-lg text-warning"></i></a>
              <div class="d-flex flex-column justify-content-around">
                <div class="text-center" style="min-width:140px;">
                  <small class="mb-1 text-muted d-block">Jumlah Disposisi</small>
                  <h5 class="mb-0">{{ $disposisi }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
