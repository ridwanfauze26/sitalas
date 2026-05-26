@extends('layouts.app')
@section('judul', Auth::user()->role == 'admin' ? 'Edit Cuti' : 'HALAMAN USER')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="m-b-0 text-white">
                    <a class="btn btn-sm btn-danger" href="{{ Auth::user()->role == 'admin' ? route('cuti.admin.index') : route('cuti.index') }}" role="button">Kembali</a>
                </h4>
            </div>
            <div class="card-body">
                @if(($cuti->status_pengajuan === 'Ditangguhkan' || $cuti->status_pengajuan === 'Perubahan') && $cuti->rejected_reason)
                    @if($cuti->status_pengajuan === 'Ditangguhkan')
                        <div class="alert alert-warning border-left-warning shadow-sm" role="alert" style="border-radius: 12px; border-left: 5px solid #fd7e14; background-color: #fff3cd; color: #856404; margin-bottom: 24px; padding: 15px 20px;">
                            <h5 class="alert-heading font-weight-bold mb-2">
                                <i class="fa fa-info-circle mr-2"></i> Pengajuan Ditangguhkan oleh Atasan
                            </h5>
                            <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;">
                                <strong>Catatan Penangguhan:</strong> "{{ $cuti->rejected_reason }}"
                            </p>
                            <hr style="border-top-color: #ffe8a1; margin: 10px 0;">
                            <span style="font-size: 12px; opacity: 0.85;">Pengajuan Anda ditangguhkan oleh Atasan. Namun, jika Anda ingin menyesuaikan data dan mengajukannya ulang kembali, Anda tetap dapat mengedit data di bawah lalu klik tombol <strong>Kirim Kembali Pengajuan</strong>.</span>
                        </div>
                    @else
                        <div class="alert alert-warning border-left-warning shadow-sm" role="alert" style="border-radius: 12px; border-left: 5px solid #6f42c1; background-color: #f3ebff; color: #4e2b8c; margin-bottom: 24px; padding: 15px 20px;">
                            <h5 class="alert-heading font-weight-bold mb-2">
                                <i class="fa fa-pencil mr-2"></i> Permintaan Perubahan Data Cuti
                            </h5>
                            <p class="mb-0" style="font-size: 13.5px; line-height: 1.5;">
                                <strong>Catatan Perubahan:</strong> "{{ $cuti->rejected_reason }}"
                            </p>
                            <hr style="border-top-color: #d8c3f7; margin: 10px 0;">
                            <span style="font-size: 12px; opacity: 0.85;">Silakan perbaiki data di bawah sesuai dengan catatan di atas, kemudian klik tombol <strong>Kirim Kembali Pengajuan</strong> untuk diajukan ulang.</span>
                        </div>
                    @endif
                @endif
                <form action="{{ Auth::user()->role == 'admin' ? route('cuti.admin.update', $cuti->id) : route('cuti.update', $cuti->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Jenis Cuti</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="jenis_cuti" required>
                                    <option value="cuti_tahunan" {{ $cuti->jenis_cuti == 'cuti_tahunan' ? 'selected' : '' }}>1. Cuti Tahunan</option>
                                    <option value="cuti_besar" {{ $cuti->jenis_cuti == 'cuti_besar' ? 'selected' : '' }}>2. Cuti Besar</option>
                                    <option value="cuti_sakit" {{ $cuti->jenis_cuti == 'cuti_sakit' ? 'selected' : '' }}>3. Cuti Sakit</option>
                                    <option value="cuti_melahirkan" {{ $cuti->jenis_cuti == 'cuti_melahirkan' ? 'selected' : '' }}>4. Cuti Melahirkan</option>
                                    <option value="cuti_penting" {{ $cuti->jenis_cuti == 'cuti_penting' ? 'selected' : '' }}>5. Cuti Karena Alasan Penting</option>
                                    <option value="cuti_luar_tanggungan" {{ $cuti->jenis_cuti == 'cuti_luar_tanggungan' ? 'selected' : '' }}>6. Cuti di Luar Tanggungan Negara</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Alasan Cuti</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="alasan_cuti" value="{{ $cuti->alasan_cuti }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Lama Cuti</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="lama_cuti" id="lama_cuti" value="{{ $cuti->lama_cuti }}" min="1" step="1">
                            </div>
                            <div class="col-sm-2">
                                <select class="form-control" name="alasan_mode" id="alasan_mode">
                                    <option value="hari" selected>Hari</option>
                                    <option value="bulan">Bulan</option>
                                    <option value="tahun">Tahun</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="infoCutiBesar" style="display:none;">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <div class="alert alert-warning" style="margin-bottom:0;">
                                    Cuti besar maksimal 3 bulan.
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="dokumenCutiSakit" style="display:none;">
                            <label class="col-sm-3 col-form-label">Surat Dokter/Bidan</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="dokumen_sakit" accept=".pdf,.jpg,.jpeg,.png" id="suratdokter">
                            </div>
                        </div>

                        <div class="form-group row" id="dokumenCutiLuar" style="display:none;">
                            <label class="col-sm-3 col-form-label">Surat Keputusan PPK</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="dokumen_ppk" accept=".pdf" id="SKPPK">
                            </div>
                        </div>

                        <div class="form-group row" id="infoCutiLuar" style="display:none;">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <div class="alert alert-warning" style="margin-bottom:0;">
                                    Cuti di luar tanggungan negara maksimal 3 tahun (36 bulan).
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Mulai</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control flatpickr" name="tanggal_mulai" value="{{ $cuti->tanggal_mulai ? \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') : '' }}" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tanggal Selesai</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control flatpickr" name="tanggal_selesai" value="{{ $cuti->tanggal_selesai ? \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') : '' }}" placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Alamat Selama Cuti</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="alamat" value="{{ $cuti->alamat }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">No. Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_telepon" value="{{ $cuti->no_telepon }}">
                            </div>
                        </div>
                        @if(Auth::user()->role == 'admin')
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Status Pengajuan</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_pengajuan">
                                    <option value="Diajukan" {{ $cuti->status_pengajuan == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                                    <option value="Disetujui" {{ $cuti->status_pengajuan == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="Ditolak" {{ $cuti->status_pengajuan == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    @if($cuti->status_pengajuan === 'Ditangguhkan' || $cuti->status_pengajuan === 'Perubahan')
                                        Kirim Kembali Pengajuan
                                    @else
                                        Simpan
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var jenis = document.querySelector('select[name="jenis_cuti"]');
        var infoBesar = document.getElementById('infoCutiBesar');
        var alasanMode = document.getElementById('alasan_mode');
        var lamaCuti = document.getElementById('lama_cuti');
        if (!jenis) return;

        function refreshInfo() {
            var isBesar = jenis.value === 'cuti_besar';
            if (infoBesar) {
                var v = lamaCuti ? parseInt(lamaCuti.value || '0', 10) : 0;
                var show = isBesar && v > 3;
                infoBesar.style.display = show ? 'flex' : 'none';
            }
            if (isBesar && alasanMode) {
                alasanMode.value = 'bulan';
            }

            var dokSakit = document.getElementById('dokumenCutiSakit');
            var isSakit = jenis.value === 'cuti_sakit';
            if (dokSakit) {
                dokSakit.style.display = isSakit ? 'flex' : 'none';
                document.getElementById('suratdokter').required = dokSakit.style.display == 'flex' ?  true : false;
            }

            var dokLuar = document.getElementById('dokumenCutiLuar');
            var infoLuar = document.getElementById('infoCutiLuar');
            var isLuar = jenis.value === 'cuti_luar_tanggungan';
            if (dokLuar) {
                dokLuar.style.display = isLuar ? 'flex' : 'none';
                document.getElementById('SKPPK').required = dokLuar.style.display == 'flex' ?  true : false;
            }
            if (isLuar && alasanMode) {
                alasanMode.value = 'bulan';
            }
            if (infoLuar) {
                var vLuar = lamaCuti ? parseInt(lamaCuti.value || '0', 10) : 0;
                var showLuar = isLuar && vLuar > 36;
                infoLuar.style.display = showLuar ? 'flex' : 'none';
            }
        }

        jenis.addEventListener('change', refreshInfo);
        if (lamaCuti) {
            lamaCuti.addEventListener('input', refreshInfo);
            lamaCuti.addEventListener('change', refreshInfo);
        }
        $(document).ready(function() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".flatpickr", {
                    locale: "id",
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    position: "above",
                    onReady: function(selectedDates, dateStr, instance) {
                        const footer = document.createElement("div");
                        footer.className = "flatpickr-custom-footer";
                        footer.innerHTML = `
                            <div class="today-label">TODAY</div>
                            <div class="divider">|</div>
                            <div class="time-container">
                                <div class="time-clock"></div>
                                <div class="time-date"></div>
                            </div>
                        `;
                        instance.calendarContainer.appendChild(footer);
                        
                        function updateTime() {
                            const now = new Date();
                            const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
                            const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                            const clockEl = footer.querySelector(".time-clock");
                            const dateEl = footer.querySelector(".time-date");
                            if (clockEl) clockEl.textContent = timeStr;
                            if (dateEl) dateEl.textContent = dateStr;
                        }
                        updateTime();
                        setInterval(updateTime, 1000);
                    }
                });
            }
        });
        refreshInfo();
    })();
</script>
@endsection
