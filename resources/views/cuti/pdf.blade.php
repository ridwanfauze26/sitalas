<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Cuti</title>
    <style>
        @page { margin: 12px 14px 12px 14px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #444; }
        .header { margin-bottom: 10px; }
        .header-title { font-weight: bold; font-size: 11px; color: #1e4fa3; }
        .header-sub { font-size: 9px; color: #1e4fa3; }
        .header-text { position: relative; left: -12px; }
        .lampiran { font-size: 9px; }
        .small { font-size: 10px; }
        .rule-blue { border-top: 2px solid #1e4fa3; margin: 6px 0 10px 0; }
        .header-table td { vertical-align: middle; padding: 0; }
        .logo-cell { padding-right: 4px; }
        .logo-img { width: 72px; height: auto; display: block; }
        table { border-collapse: collapse; width: 100%; }
        .table-border td, .table-border th { border: 0.6pt solid #666; padding: 3px; vertical-align: top; }
        .no-border { border: 0 !important; }
        .section-title { font-weight: bold; }
        .box { border: 1px solid #000; height: 95px; }
        .box-lg { border: 1px solid #000; height: 80px; }
        .pad { padding: 4px; }
        .checkbox { display: inline-block; width: 10px; height: 10px; border: 0.6pt solid #666; margin-right: 6px; vertical-align: middle; }
        .checked { background: #000; }
        .mt-6 { margin-top: 6px; }
        .mt-10 { margin-top: 10px; }
        .qr-wrap { text-align: center; margin-bottom: 4px; }
        .qr-img { width: 50px; height: 50px; }
        .decision-head { text-align: center; font-weight: bold; }
        .approve-cell { text-align: center; }
        .approve-name { margin-top: 2px; font-weight: bold; }
        .no-break { page-break-inside: avoid; break-inside: avoid; }
        tr { page-break-inside: avoid; break-inside: avoid; }
        .dots { display: inline-block; border-bottom: 0.6pt dotted #666; width: 240px; height: 10px; vertical-align: bottom; }
        .jenis-grid { width: 100%; }
        .jenis-grid td { border: 0; padding: 2px 4px; vertical-align: top; }
        .jenis-grid .jenis-left { border-right: 0.6pt solid #666; }
    </style>
</head>
<body>
    <div class="header">
        <table class="no-border header-table" style="width:100%;">
            <tr>
                <td class="no-border logo-cell" style="width:14%; text-align:left;">
                    <img class="logo-img" src="{{ public_path('admin/images/logo-header.png') }}" alt="Logo">
                </td>
                <td class="no-border text-center" style="width:86%;">
                    <div class="header-text">
                        <div class="header-title">KEMENTERIAN PERTANIAN</div>
                        <div class="header-title">DIREKTORAT JENDERAL PETERNAKAN DAN KESEHATAN HEWAN</div>
                        <div class="header-title">BALAI PENGUJIAN MUTU DAN SERTIFIKASI PRODUK HEWAN</div>
                        <div class="header-sub">JL. PEMUDA NOMOR 29 A BOGOR 16161</div>
                        <div class="header-sub">Telepon (0251) 8377111,8353712 Faksimili (0251) 8353712</div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="rule-blue"></div>

        <table class="no-border" style="width:100%; margin-top:2px;">
            <tr>
                <td class="no-border" style="width:65%;"></td>
                <td class="no-border lampiran" style="width:35%;">
                    <div><b>ANAK LAMPIRAN 1.b</b></div>
                    <div>PERATURAN BADAN KEPEGAWAIAN NEGARA</div>
                    <div>REPUBLIK INDONESIA</div>
                    <div>NOMOR 24 TAHUN 2017</div>
                    <div>TENTANG</div>
                    <div>TATA CARA PEMBERIAN CUTI PEGAWAI NEGERI SIPIL</div>

                    <div style="height:10px;"></div>
                    <div style="font-weight:bold;">BOGOR,</div>
                    <div style="height:10px;"></div>
                    <div>Yth. Kepala BPMSPH Bogor</span></div>
                    <div>di Tempat</span></div>
                    <!-- <div>di. <span class="dots"></span></div> -->
                </td>
            </tr>
        </table>
    </div>

    <div class="text-center" style="font-weight:bold; margin-bottom:6px;">FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</div>

    <table class="table-border">
        <tr>
            <td colspan="2" class="section-title">I. DATA PEGAWAI</td>
        </tr>
        <tr>
            <td style="width:50%;">
                <table style="width:100%;" class="no-border">
                    <tr>
                        <td class="no-border" style="width:90px;">Nama</td>
                        <td class="no-border">: {{ optional($cuti->user)->name }}</td>
                    </tr>
                    <tr>
                        <td class="no-border">Jabatan</td>
                        <td class="no-border">: {{ optional(optional($cuti->user)->jabatan)->nama }}</td>
                    </tr>
                    <tr>
                        <td class="no-border">Unit Kerja</td>
                        <td class="no-border">: {{ optional(optional($cuti->user)->unitBagian)->nama ?? optional($cuti->user)->unit_bagian_nama }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table style="width:100%;" class="no-border">
                    <tr>
                        <td class="no-border" style="width:90px;">NIP</td>
                        <td class="no-border">: {{ optional($cuti->user)->nip }}</td>
                    </tr>
                    <tr>
                        <td class="no-border">Masa Kerja</td>
                        <td class="no-border">: {{ $masaKerja }}
                            <!-- {{ data_get($cuti->user, 'masa_kerja') ?? (data_get($cuti->user, 'tmt') || data_get($cuti->user, 'tanggal_masuk') ? (function () use ($cuti) {
                            $tmt = data_get($cuti->user, 'tmt') ?? data_get($cuti->user, 'tanggal_masuk');
                            try {
                                $start = \Carbon\Carbon::parse($tmt);
                                $diff = $start->diff(\Carbon\Carbon::now());
                                return $diff->y . ' th ' . $diff->m . ' bl';
                            } catch (\Throwable $e) {
                                return '-';
                            }
                        })() : '-') }} -->
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">II. JENIS CUTI YANG DIAMBIL</td>
        </tr>
        <tr>
            <td colspan="2">
                @php
                    $jenisList = [
                        'cuti_tahunan' => 'Cuti Tahunan',
                        'cuti_besar' => 'Cuti Besar',
                        'cuti_sakit' => 'Cuti Sakit',
                        'cuti_melahirkan' => 'Cuti Melahirkan',
                        'cuti_alasan_penting' => 'Cuti Karena Alasan Penting',
                        'cuti_luar_tanggungan' => 'Cuti di Luar Tanggungan Negara',
                    ];
                @endphp
                <table class="jenis-grid">
                    <tr>
                        <td class="jenis-left" style="width:50%;">
                            <div><span class="checkbox {{ $cuti->jenis_cuti === 'cuti_tahunan' ? 'checked' : '' }}"></span>1. {{ $jenisList['cuti_tahunan'] }}</div>
                            <div><span class="checkbox {{ $cuti->jenis_cuti === 'cuti_sakit' ? 'checked' : '' }}"></span>3. {{ $jenisList['cuti_sakit'] }}</div>
                            <div><span class="checkbox {{ $cuti->jenis_cuti === 'cuti_alasan_penting' ? 'checked' : '' }}"></span>5. {{ $jenisList['cuti_alasan_penting'] }}</div>
                        </td>
                        <td style="width:50%;">
                            <div><span class="checkbox {{ $cuti->jenis_cuti === 'cuti_besar' ? 'checked' : '' }}"></span>2. {{ $jenisList['cuti_besar'] }}</div>
                            <div><span class="checkbox {{ $cuti->jenis_cuti === 'cuti_melahirkan' ? 'checked' : '' }}"></span>4. {{ $jenisList['cuti_melahirkan'] }}</div>
                            <div><span class="checkbox {{ $cuti->jenis_cuti === 'cuti_luar_tanggungan' ? 'checked' : '' }}"></span>6. {{ $jenisList['cuti_luar_tanggungan'] }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">III. ALASAN CUTI</td>
        </tr>
        <tr>
            <td colspan="2" style="height:40px;">{{ $cuti->alasan_cuti }}</td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">IV. LAMANYA CUTI</td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width:100%;" class="no-border">
                    <tr>
                        <td class="no-border" style="width:45%;">Selama : {{ $cuti->lama_cuti }} {{ ($cuti->jenis_cuti === 'cuti_luar_tanggungan' || $cuti->jenis_cuti === 'cuti_besar') ? '(bulan)' : '(hari)' }}</td>
                        <td class="no-border" style="width:25%;">Mulai Tanggal : {{ $cuti->tanggal_mulai ? \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                        <td class="no-border" style="width:30%;">s/d : {{ $cuti->tanggal_selesai ? \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">V. CATATAN CUTI</td>
        </tr>
        <tr>
            <td colspan="2" class="pad">
                @php
                    $tahunCuti = (int) ($cuti->tahun_cuti ?: ($cuti->tanggal_mulai ? (int) \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('Y') : (int) date('Y')));
                    $potN = (int) ($cuti->potong_n ?? 0);
                    $potN1 = (int) ($cuti->potong_n1 ?? 0);
                    $potN2 = (int) ($cuti->potong_n2 ?? 0);
                @endphp
                <table style="width:100%;" class="table-border">
                    <tr>
                        <td style="width:50%; padding:0;">
                            <table style="width:100%;" class="table-border">
                                <tr>
                                    <td colspan="3" class="section-title">1. CUTI TAHUNAN</td>
                                </tr>
                                <tr>
                                    <td style="width:20%;">Tahun</td>
                                    <td style="width:20%;">Sisa</td>
                                    <td style="width:60%;">Keterangan</td>
                                </tr>
                                <tr>
                                    <td>N-2</td>
                                    <td></td>
                                    <td>{{ $potN2 > 0 ? 'Dipakai ' . $potN2 . ' hari' : '' }}</td>
                                </tr>
                                <tr>
                                    <td>N-1</td>
                                    <td></td>
                                    <td>{{ $potN1 > 0 ? 'Dipakai ' . $potN1 . ' hari' : '' }}</td>
                                </tr>
                                <tr>
                                    <td>N</td>
                                    <td></td>
                                    <td>{{ $potN > 0 ? 'Dipakai ' . $potN . ' hari (Tahun ' . $tahunCuti . ')' : ($cuti->jenis_cuti === 'cuti_tahunan' ? 'Tahun ' . $tahunCuti : '') }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:50%; padding:0;">
                            <table style="width:100%;" class="table-border">
                                <tr><td class="section-title">2. CUTI BESAR</td></tr>
                                <tr><td class="section-title">3. CUTI SAKIT</td></tr>
                                <tr><td class="section-title">4. CUTI MELAHIRKAN</td></tr>
                                <tr><td class="section-title">5. CUTI KARENA ALASAN PENTING</td></tr>
                                <tr><td class="section-title">6. CUTI DI LUAR TANGGUNGAN NEGARA</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="section-title">VI. ALAMAT SELAMA MENJALANKAN CUTI</td>
        </tr>
        <tr>
            <td style="width:65%; height:55px;">{{ $cuti->alamat }}</td>
            <td style="width:35%;">
                <table style="width:100%;" class="no-border">
                    <tr>
                        <td class="no-border" style="width:40%;">TELP</td>
                        <td class="no-border" style="width:60%;">: {{ $cuti->no_telepon }}</td>
                    </tr>
                </table>
                <div class="mt-10">Hormat saya,</div>
                <div style="height:28px;"></div>
                <div>( {{ optional($cuti->user)->name }} )</div>
                <div>NIP {{ optional($cuti->user)->nip }}</div>
            </td>
        </tr>

        <tr class="no-break">
            <td colspan="2" class="section-title">VII. PERTIMBANGAN ATASAN LANGSUNG</td>
        </tr>
        <tr class="no-break">
            <td colspan="2" style="padding:0;">
                <table class="table-border" style="width:100%;">
                    <tr>
                        <td class="decision-head" style="width:25%;">DISETUJUI</td>
                        <td class="decision-head" style="width:25%;">PERUBAHAN</td>
                        <td class="decision-head" style="width:25%;">DITANGGUHKAN</td>
                        <td class="decision-head" style="width:25%;">TIDAK DISETUJUI</td>
                    </tr>
                    <tr>
                        <td class="approve-cell" style="height:85px;">
                            <div class="qr-wrap">
                                @if($qrDataUri)
                                    <img class="qr-img" src="{{ $qrDataUri }}" alt="QR">
                                @endif
                            </div>
                            <div class="approve-name">{{ optional($approverLevel2)->name }}</div>
                            <div>NIP {{ optional($approverLevel2)->nip }}</div>
                        </td>
                        <td style="height:85px;"></td>
                        <td style="height:85px;"></td>
                        <td style="height:85px;"></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="no-break">
            <td colspan="2" class="section-title">VIII. KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI</td>
        </tr>
        <tr class="no-break">
            <td colspan="2" style="padding:0;">
                <table class="table-border" style="width:100%;">
                    <tr>
                        <td class="decision-head" style="width:25%;">DISETUJUI</td>
                        <td class="decision-head" style="width:25%;">PERUBAHAN</td>
                        <td class="decision-head" style="width:25%;">DITANGGUHKAN</td>
                        <td class="decision-head" style="width:25%;">TIDAK DISETUJUI</td>
                    </tr>
                    <tr>
                        <td class="approve-cell" style="height:85px;">
                            <div class="qr-wrap">
                                @if($qrDataUri)
                                    <img class="qr-img" src="{{ $qrDataUri }}" alt="QR">
                                @endif
                            </div>
                            <div class="approve-name">{{ optional($approverLevel1)->name }}</div>
                            <div>NIP {{ optional($approverLevel1)->nip }}</div>
                        </td>
                        <td style="height:85px;"></td>
                        <td style="height:85px;"></td>
                        <td style="height:85px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
