@extends('layouts.app')
@section('judul','HALAMAN USER')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Jenis Cuti</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="jenis_cuti" id="jenis_cuti" required>
                                        <option value="cuti_tahunan">1. Cuti Tahunan</option>
                                        <option value="cuti_besar">2. Cuti Besar</option>
                                        <option value="cuti_sakit">3. Cuti Sakit</option>
                                        <option value="cuti_melahirkan">4. Cuti Melahirkan</option>
                                        <option value="cuti_penting">5. Cuti Karena Alasan Penting</option>
                                        <option value="cuti_luar_tanggungan">6. Cuti di Luar Tanggungan Negara</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Lama Cuti</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="lama_cuti" id="lama_cuti" placeholder="" min="1" step="1" required>
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
                                <label class="col-sm-3 col-form-label">Alasan Cuti</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="alasan_cuti" placeholder="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tanggal Mulai Cuti</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control flatpickr" name="tanggal_mulai" placeholder="dd/mm/yyyy" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tanggal Selesai Cuti</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control flatpickr" name="tanggal_selesai" placeholder="dd/mm/yyyy" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Alamat Selama Cuti</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="alamat" placeholder="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">No. Telepon</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="no_telepon" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div id="saldoCutiTahunan" style="display:none;">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Sisa Cuti Tahunan</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Tahun</th>
                                                        <th>Sisa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>N</td>
                                                        <td id="saldoN">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-end justify-content-center" style="margin-top: 12px;">
                                <button type="submit" class="btn btn-primary" id="ajukan">Ajukan Cuti</button>
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
        
        function setSaldoVisible(isTahunan) {
            var el = document.getElementById('saldoCutiTahunan');
            if (!el) return;
            el.style.display = isTahunan ? 'block' : 'none';
        }

        function setSaldo(data) {
            document.getElementById('saldoN').innerText = (data && data['N'] !== undefined) ? data['N'] : '-';
            
            if(data['N'] == 0 && document.getElementById('jenis_cuti').value == 'cuti_tahunan')
                document.getElementById('ajukan').disabled = true;
            
        }

        function fetchSaldo() {
            return fetch("{{ route('cuti.tahunan.saldo') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    setSaldo(data);
                })
                .catch(function () {
                    setSaldo(null);
                });
        }

        var jenis = document.getElementById('jenis_cuti');
        var lamaCuti = document.getElementById('lama_cuti');
        if (!jenis) return;

        function refreshInfo() {
            document.getElementById('ajukan').disabled = false;
            var isTahunan = jenis.value === 'cuti_tahunan';
            setSaldoVisible(isTahunan);
            if (isTahunan) {
                fetchSaldo();
            }

            var infoBesar = document.getElementById('infoCutiBesar');
            var alasanMode = document.getElementById('alasan_mode');
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
