@extends('layouts.app')
@section('judul','Hubungkan Telegram')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    1) Klik tombol <strong>Start Bot</strong><br>
                    2) Di Telegram tekan <strong>Start</strong><br>
                    3) Setelah itu jalankan command: <code>php artisan telegram:sync-updates</code>
                </div>

                @if($startLink)
                    <a href="{{ $startLink }}" target="_blank" class="btn btn-primary">Start Bot</a>
                @else
                    <div class="alert alert-danger">
                        TELEGRAM_BOT_USERNAME belum diset. Isi di file <code>.env</code>.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
