<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gate;
use Auth;
use App\Services\TelegramService;
use App\User;
use App\CutiTahunanBalance;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class CutiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
            if(Gate::any(['admin','kepala','verifikator','pegawai'])) return $next($request);
            abort(403, 'Anda tidak memiliki hak akses untuk mengakses halaman ini');
        });
    }

    public function index()
    {
        if(Auth::user()->role == 'admin') {
            return redirect()->route('cuti.admin.index');
        }

        $cuti = \App\Cuti::where('user_id', Auth::user()->id)->latest()->get();
        return view('cuti.index', compact('cuti'));
    }

    public function adminIndex()
    {
        if(Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        
        $cuti = \App\Cuti::with('user')->latest()->get();
        return view('cuti.admin', compact('cuti'));
    }

    public function create()
    {
        if(Auth::user()->role == 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        return view('cuti.ajukan');
    }

    public function tahunanSaldo()
    {
        if (!Auth::check()) {
            abort(403, 'Anda tidak memiliki akses');
        }

        $year = (int) date('Y');
        $userId = (int) Auth::user()->id;

        $jenisCuti = "cuti_tahunan";
        $totalCuti = \App\Cuti::where('user_id', $userId)
            ->where('jenis_cuti', $jenisCuti)
            ->where('tahun_cuti', $year)
            ->where('status_pengajuan','Disetujui')
            ->sum('lama_cuti');
        
        
        // $n = CutiTahunanBalance::firstOrCreate(
        //     ['user_id' => $userId, 'tahun' => $year],
        //     ['jatah' => 12, 'dipakai' => 0]
        // );
        // $n1 = CutiTahunanBalance::firstOrCreate(
        //     ['user_id' => $userId, 'tahun' => $year - 1],
        //     ['jatah' => 12, 'dipakai' => 0]
        // );
        // $n2 = CutiTahunanBalance::firstOrCreate(
        //     ['user_id' => $userId, 'tahun' => $year - 2],
        //     ['jatah' => 12, 'dipakai' => 0]
        // );

        // $sisaN = max(0, (int) $n->jatah - (int) $n->dipakai);
        $sisaN = 12-$totalCuti;
        // $sisaN = max(0, (int) $n->jatah - (int) $n->dipakai);
        // $sisaN1Raw = max(0, (int) $n1->jatah - (int) $n1->dipakai);
        // $sisaN2Raw = max(0, (int) $n2->jatah - (int) $n2->dipakai);

        // $carryCap = 6;
        // $carryTotal = min($carryCap, $sisaN1Raw + $sisaN2Raw);
        // $sisaN1 = min($sisaN1Raw, $carryTotal);
        // $sisaN2 = max(0, $carryTotal - $sisaN1);

        return response()->json([
            'tahun' => $year,
            'N' => $sisaN,
            // 'N-1' => $sisaN1,
            // 'N-2' => $sisaN2,
        ]);
    }

    private function hitungHariKerja(?string $tanggalMulai, ?string $tanggalSelesai): ?int
    {
        if (!$tanggalMulai || !$tanggalSelesai) {
            return null;
        }

        $start = Carbon::parse($tanggalMulai)->startOfDay();
        $end = Carbon::parse($tanggalSelesai)->startOfDay();
        if ($end->lt($start)) {
            return null;
        }

        $days = 0;
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            if (!$cursor->isWeekend()) {
                $days++;
            }
            $cursor->addDay();
        }

        return $days;
    }

    public function store(Request $request)
    {
        if(Auth::user()->role == 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $request->validate([
            'jenis_cuti' => 'required|string|max:100',
            'alasan_cuti' => 'nullable|string|max:200',
            'lama_cuti' => 'nullable|integer|min:1',
            'alasan_mode' => 'nullable|string|max:20',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'alamat' => 'nullable|string|max:200',
            'no_telepon' => 'nullable|string|max:50',
        ]);

        if ($request->jenis_cuti === 'cuti_besar') {
            $request->validate([
                'alasan_mode' => 'required|in:bulan',
                'lama_cuti' => 'required|integer|min:1|max:3',
            ], [
                'lama_cuti.max' => 'Cuti besar maksimal 3 bulan',
            ]);
        }

        $hasDokumenSakitCol = Schema::hasColumn('cuti', 'dokumen_sakit');
        $hasDokumenPpkCol = Schema::hasColumn('cuti', 'dokumen_ppk');

        if ($request->jenis_cuti === 'cuti_sakit') {
            $request->validate([
                'dokumen_sakit' => $hasDokumenSakitCol ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'nullable',
                'alasan_mode' => 'required|in:hari',
                'lama_cuti' => 'required|integer|min:1|max:548',
            ], [
                'dokumen_sakit.required' => 'Surat keterangan dokter/bidan wajib dilampirkan',
            ]);

            if ((int) $request->lama_cuti <= 14) {
                $request->validate([
                    'lama_cuti' => 'required|integer|min:1|max:14',
                ], [
                    'lama_cuti.max' => 'Cuti sakit maksimal 14 hari untuk kategori sakit 1-14 hari',
                ]);
            }
        }

        if ($request->jenis_cuti === 'cuti_luar_tanggungan') {
            $request->validate([
                'dokumen_ppk' => $hasDokumenPpkCol ? 'required|file|mimes:pdf|max:2048' : 'nullable',
                'alasan_mode' => 'required|in:bulan',
                'lama_cuti' => 'required|integer|min:1|max:36',
            ], [
                'dokumen_ppk.required' => 'Surat keputusan PPK wajib dilampirkan',
                'lama_cuti.max' => 'Cuti di luar tanggungan negara maksimal 3 tahun (36 bulan)',
            ]);
        }

        $levelPengaju = (int) Auth::user()->cuti_level;
        $statusPengajuan = 'Menunggu Persetujuan';
        $statusLevel1 = 'Tidak Perlu';
        $statusLevel2 = 'Tidak Perlu';

        if ($levelPengaju === 3) {
            $statusLevel1 = 'Menunggu';
            $statusLevel2 = 'Menunggu';
        } elseif ($levelPengaju === 2) {
            $statusLevel1 = 'Menunggu';
        } else {
            $levelPengaju = 1;
            $statusPengajuan = 'Disetujui';
        }

        $dokumenSakitPath = null;
        if ($hasDokumenSakitCol && $request->jenis_cuti === 'cuti_sakit' && $request->hasFile('dokumen_sakit')) {
            $dokumenSakitPath = $request->file('dokumen_sakit')->store('dokumen_cuti', 'public');
        }

        $dokumenPpkPath = null;
        if ($hasDokumenPpkCol && $request->jenis_cuti === 'cuti_luar_tanggungan' && $request->hasFile('dokumen_ppk')) {
            $dokumenPpkPath = $request->file('dokumen_ppk')->store('dokumen_cuti', 'public');
        }

        $data = [
            'user_id' => Auth::user()->id,
            'level_pengaju' => $levelPengaju,
            'tahun_cuti' => $request->tanggal_mulai ? (int) Carbon::parse($request->tanggal_mulai)->format('Y') : (int) date('Y'),
            'jenis_cuti' => $request->jenis_cuti,
            'alasan_cuti' => $request->alasan_cuti,
            'lama_cuti' => $request->lama_cuti,
            'lama_cuti_hari_kerja' => $request->jenis_cuti === 'cuti_tahunan' ? $this->hitungHariKerja($request->tanggal_mulai, $request->tanggal_selesai) : null,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'status_pengajuan' => $statusPengajuan,
            'status_level1' => $statusLevel1,
            'status_level2' => $statusLevel2,
        ];

        if ($hasDokumenSakitCol) {
            $data['dokumen_sakit'] = $dokumenSakitPath;
        }
        if ($hasDokumenPpkCol) {
            $data['dokumen_ppk'] = $dokumenPpkPath;
        }

        $cuti = \App\Cuti::create($data);

        $telegram = app(TelegramService::class);

        $targetLevels = [];
        if ((int) $cuti->level_pengaju === 3) {
            $targetLevels = [1, 2];
        } elseif ((int) $cuti->level_pengaju === 2) {
            $targetLevels = [1];
        }

        if ($targetLevels) {
            $users = User::with('jabatan')
                ->get()
                ->filter(function ($u) use ($targetLevels) {
                    return in_array((int) $u->cuti_level, $targetLevels, true);
                });

            foreach ($users as $u) {
                if ($u->active_telegram_chat_id) {
                    $telegram->sendMessage(
                        $u->active_telegram_chat_id,
                        'Pengajuan cuti baru dari ' . e(Auth::user()->name) . "\n" .
                        'Jenis: ' . e((string) $cuti->jenis_cuti) . "\n" .
                        'Status: Menunggu Persetujuan'
                    );
                }
            }
        }

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil diajukan');
    }

    public function persetujuanIndex()
    {
        if (!Auth::user()->isCutiApprover()) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $query = \App\Cuti::with('user')->latest();
        $unitBagian = \App\UnitBagian::find(Auth::user()->unit_bagian_id);
        // $unitBagian = \App\UnitBagian::where('jabatan_id',Auth::user()->jabatan_id)->get();
        // foreach($unitBagian as $ub)
        dd($unitBagian->nama);
        if (Auth::user()->role === 'admin') {
            $query->where(function ($q) {
                $q->where('status_level1', 'Menunggu')
                    ->orWhere('status_level2', 'Menunggu');
            });
        } elseif (Auth::user()->isCutiApproverLevel1()) {
            $query->where('status_level1', 'Menunggu');
        } elseif (Auth::user()->isCutiApproverLevel2() && Auth::user()->isVerifikator()) {
            $query->where('status_level2', 'Menunggu');
        } else {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $cuti = $query->get();

        return view('cuti.persetujuan', compact('cuti'));
    }

    private function determineApprovalLevel(\App\Cuti $cuti, Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $level = (int) $request->input('level');
            if ($level === 1 || $level === 2) {
                return $level;
            }
            if ($cuti->status_level1 === 'Menunggu') {
                return 1;
            }
            if ($cuti->status_level2 === 'Menunggu') {
                return 2;
            }
            abort(400, 'Tidak ada level persetujuan yang menunggu');
        }

        if (Auth::user()->isCutiApproverLevel1()) {
            return 1;
        }

        if (Auth::user()->isCutiApproverLevel2()) {
            return 2;
        }

        abort(403, 'Anda tidak memiliki akses untuk menyetujui pengajuan ini');
    }

    private function refreshFinalStatus(\App\Cuti $cuti)
    {
        if ($cuti->status_level1 === 'Ditolak' || $cuti->status_level2 === 'Ditolak') {
            $cuti->status_pengajuan = 'Ditolak';
            return;
        }

        $levelPengaju = (int) $cuti->level_pengaju;

        if ($levelPengaju === 1) {
            $cuti->status_pengajuan = 'Disetujui';
            return;
        }

        if ($levelPengaju === 2) {
            $cuti->status_pengajuan = ($cuti->status_level1 === 'Disetujui') ? 'Disetujui' : 'Menunggu Persetujuan';
            return;
        }

        $cuti->status_pengajuan = ($cuti->status_level1 === 'Disetujui' && $cuti->status_level2 === 'Disetujui') ? 'Disetujui' : 'Menunggu Persetujuan';
    }

    private function applyCutiTahunanDeduction(\App\Cuti $cuti)
    {
        if ($cuti->jenis_cuti !== 'cuti_tahunan') {
            return;
        }

        if ($cuti->status_pengajuan !== 'Disetujui') {
            return;
        }

        if ((int) $cuti->potong_n > 0 || (int) $cuti->potong_n1 > 0 || (int) $cuti->potong_n2 > 0) {
            return;
        }

        $hariKerja = (int) $cuti->lama_cuti_hari_kerja;
        if ($hariKerja <= 0) {
            $hariKerja = (int) $this->hitungHariKerja($cuti->tanggal_mulai, $cuti->tanggal_selesai);
            $cuti->lama_cuti_hari_kerja = $hariKerja > 0 ? $hariKerja : null;
        }

        if ($hariKerja <= 0) {
            return;
        }

        $tahun = (int) ($cuti->tahun_cuti ?: ($cuti->tanggal_mulai ? (int) Carbon::parse($cuti->tanggal_mulai)->format('Y') : (int) date('Y')));
        $userId = (int) $cuti->user_id;

        $n = CutiTahunanBalance::firstOrCreate(
            ['user_id' => $userId, 'tahun' => $tahun],
            ['jatah' => 12, 'dipakai' => 0]
        );
        $n1 = CutiTahunanBalance::firstOrCreate(
            ['user_id' => $userId, 'tahun' => $tahun - 1],
            ['jatah' => 12, 'dipakai' => 0]
        );
        $n2 = CutiTahunanBalance::firstOrCreate(
            ['user_id' => $userId, 'tahun' => $tahun - 2],
            ['jatah' => 12, 'dipakai' => 0]
        );

        $sisaN = max(0, (int) $n->jatah - (int) $n->dipakai);
        $sisaN1Raw = max(0, (int) $n1->jatah - (int) $n1->dipakai);
        $sisaN2Raw = max(0, (int) $n2->jatah - (int) $n2->dipakai);

        $carryCap = 6;
        $carryTotal = min($carryCap, $sisaN1Raw + $sisaN2Raw);
        $carryN1 = min($sisaN1Raw, $carryTotal);
        $carryN2 = max(0, $carryTotal - $carryN1);

        $totalAvailable = $sisaN + $carryTotal;
        // if ($hariKerja > $totalAvailable) {
        //     abort(400, 'Saldo cuti tahunan tidak mencukupi');
        // }

        $potongN = min($hariKerja, $sisaN);
        // $remaining = $hariKerja - $potongN;

        $potongN1 = 0;
        $potongN2 = 0;
        // if ($remaining > 0) {
        //     $potongN1 = min($remaining, $carryN1);
        //     $remaining -= $potongN1;
        // }
        // if ($remaining > 0) {
        //     $potongN2 = min($remaining, $carryN2);
        //     $remaining -= $potongN2;
        // }

        if((int)$n->dipakai==0){
            $cuti->potong_n = (int) $n->jatah - (int) $cuti->lama_cuti;
        }else{
            $cuti->potong_n = (int) $n->jatah - ((int) $cuti->lama_cuti + (int)$n->dipakai);
        }

        $n->dipakai = (int) $n->dipakai + (int) $potongN;
        $n->save();
        if ($potongN1 > 0) {
            $n1->dipakai = (int) $n1->dipakai + (int) $potongN1;
            $n1->save();
        }
        if ($potongN2 > 0) {
            $n2->dipakai = (int) $n2->dipakai + (int) $potongN2;
            $n2->save();
        }

        

        
        $cuti->potong_n1 = $potongN1;
        $cuti->potong_n2 = $potongN2;
    }

    public function approve(Request $request, $id)
    {
        if (!Auth::user()->isCutiApprover()) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $cuti = \App\Cuti::with('user')->findOrFail($id);
        $level = $this->determineApprovalLevel($cuti, $request);

        if ($level === 1) {
            if ($cuti->status_level1 !== 'Menunggu') {
                return back()->with('error', 'Status persetujuan Level 1 tidak dalam kondisi menunggu');
            }
            $cuti->status_level1 = 'Disetujui';
            $cuti->approved_level1_by = Auth::user()->id;
            $cuti->approved_level1_at = now();
        } else {
            if ($cuti->status_level2 !== 'Menunggu') {
                return back()->with('error', 'Status persetujuan Level 2 tidak dalam kondisi menunggu');
            }
            $cuti->status_level2 = 'Disetujui';
            $cuti->approved_level2_by = Auth::user()->id;
            $cuti->approved_level2_at = now();
        }

        $this->refreshFinalStatus($cuti);
        $this->applyCutiTahunanDeduction($cuti);
        $cuti->save();

        if ($cuti->status_pengajuan === 'Disetujui' && $cuti->user && $cuti->user->active_telegram_chat_id) {
            $telegram = app(TelegramService::class);
            $telegram->sendMessage(
                $cuti->user->active_telegram_chat_id,
                'Pengajuan cuti kamu sudah DISSETUJUI.\n' .
                'Jenis: ' . e((string) $cuti->jenis_cuti)
            );
        }

        return back()->with('success', 'Pengajuan cuti berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        if (!Auth::user()->isCutiApprover()) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $request->validate([
            'rejected_reason' => 'nullable|string|max:200',
        ]);

        $cuti = \App\Cuti::with('user')->findOrFail($id);
        $level = $this->determineApprovalLevel($cuti, $request);

        if ($level === 1) {
            if ($cuti->status_level1 !== 'Menunggu') {
                return back()->with('error', 'Status persetujuan Level 1 tidak dalam kondisi menunggu');
            }
            $cuti->status_level1 = 'Ditolak';
            $cuti->approved_level1_by = Auth::user()->id;
            $cuti->approved_level1_at = now();
        } else {
            if ($cuti->status_level2 !== 'Menunggu') {
                return back()->with('error', 'Status persetujuan Level 2 tidak dalam kondisi menunggu');
            }
            $cuti->status_level2 = 'Ditolak';
            $cuti->approved_level2_by = Auth::user()->id;
            $cuti->approved_level2_at = now();
        }

        $cuti->rejected_reason = $request->rejected_reason;
        $cuti->status_pengajuan = 'Ditolak';
        $cuti->save();

        if ($cuti->user && $cuti->user->active_telegram_chat_id) {
            $telegram = app(TelegramService::class);
            $reason = $cuti->rejected_reason ? (string) $cuti->rejected_reason : '-';
            $telegram->sendMessage(
                $cuti->user->active_telegram_chat_id,
                'Pengajuan cuti kamu DITOLAK.\n' .
                'Jenis: ' . e((string) $cuti->jenis_cuti) . "\n" .
                'Alasan: ' . e($reason)
            );
        }

        return back()->with('success', 'Pengajuan cuti berhasil ditolak');
    }

    private function findCutiOrFail($id)
    {
        $cuti = \App\Cuti::with('user')->findOrFail($id);

        if(Auth::user()->role != 'admin' && (int) $cuti->user_id !== (int) Auth::user()->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        return $cuti;
    }

    private function findCutiOrFailForShow($id)
    {
        $cuti = \App\Cuti::with('user')->findOrFail($id);

        if (
            Auth::user()->role !== 'admin' &&
            (int) $cuti->user_id !== (int) Auth::user()->id &&
            !Auth::user()->isCutiApprover()
        ) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        return $cuti;
    }

    public function show($id)
    {
        $cuti = $this->findCutiOrFailForShow($id);
        return view('cuti.show', compact('cuti'));
    }

    public function pdf($id,$qr)
    {
        $cuti = $this->findCutiOrFail($id);
        $tahunBulan = substr($cuti->user->nip, 8, 6);

        // Pisahkan tahun dan bulan
        $tahun = substr($tahunBulan, 0, 4);
        $bulan = substr($tahunBulan, 4, 2);

        $masaKerja = "";
        $year = (int) date('Y');
        
        if($bulan=='21'){
            $p3kmasaKerja = $year-(int)$tahun-1;
            $masaKerja = "{$p3kmasaKerja} tahun";
        }else{
            $tanggalMasuk = Carbon::createFromDate($tahun, $bulan, 1);
            $sekarang = Carbon::now();
            $diff = $tanggalMasuk->diff($sekarang);
            
            
            if($diff->y>0 && $diff->m>0 ){
                $masaKerja = "{$diff->y} tahun {$diff->y} bulan";
            }else
            {
                $masaKerja = "{$diff->y} tahun";
            }
        }
        

        if ($cuti->status_pengajuan !== 'Disetujui') {
            abort(403, 'PDF hanya tersedia jika cuti sudah disetujui');
        }

        $cuti->loadMissing(['user', 'user.jabatan', 'user.unitBagian']);

        $approverLevel1 = $cuti->approved_level1_by ? User::find($cuti->approved_level1_by) : null;
        $approverLevel2 = $cuti->approved_level2_by ? User::find($cuti->approved_level2_by) : null;

        $pdfUrl = route('cuti.pdf', ['id'=>$cuti->id, 'qr'=>$qr]);

        $qrDataUri = null;
        if (class_exists(QrCode::class) && class_exists(PngWriter::class)) {
            $writer = new PngWriter();
            $qrCode = QrCode::create((string) $pdfUrl)->setSize(130)->setMargin(0);
            $qrResult = $writer->write($qrCode);
            $qrDataUri = 'data:image/png;base64,' . base64_encode($qrResult->getString());
        }

        $pdf = Pdf::loadView('cuti.pdf', [
            'cuti' => $cuti,
            'masaKerja' => $masaKerja,
            'approverLevel1' => $approverLevel1,
            'approverLevel2' => $approverLevel2,
            'qrDataUri' => $qr ? $qrDataUri : null,
            'pdfUrl' => $pdfUrl,
        ])->setPaper('legal', 'portrait');

        return $pdf->stream('cuti-' . $cuti->id . '.pdf');
    }

    public function edit($id)
    {
        $cuti = $this->findCutiOrFail($id);
        return view('cuti.edit', compact('cuti'));
    }

    public function update(Request $request, $id)
    {
        $cuti = $this->findCutiOrFail($id);

        $hasDokumenSakitCol = Schema::hasColumn('cuti', 'dokumen_sakit');
        $hasDokumenPpkCol = Schema::hasColumn('cuti', 'dokumen_ppk');

        $rules = [
            'jenis_cuti' => 'required|string|max:100',
            'alasan_cuti' => 'nullable|string|max:200',
            'lama_cuti' => 'nullable|integer|min:1',
            'alasan_mode' => 'nullable|string|max:20',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'alamat' => 'nullable|string|max:200',
            'no_telepon' => 'nullable|string|max:50',
        ];

        if ($hasDokumenSakitCol) {
            $rules['dokumen_sakit'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }
        if ($hasDokumenPpkCol) {
            $rules['dokumen_ppk'] = 'nullable|file|mimes:pdf|max:2048';
        }

        if(Auth::user()->role == 'admin') {
            $rules['status_pengajuan'] = 'required|string|max:50';
        }

        $request->validate($rules);

        if ($request->jenis_cuti === 'cuti_besar') {
            $request->validate([
                'alasan_mode' => 'required|in:bulan',
                'lama_cuti' => 'required|integer|min:1|max:3',
            ], [
                'lama_cuti.max' => 'Cuti besar maksimal 3 bulan',
            ]);
        }

        if ($request->jenis_cuti === 'cuti_sakit') {
            $request->validate([
                'dokumen_sakit' => $hasDokumenSakitCol ? (($cuti->dokumen_sakit ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:2048') : 'nullable',
                'alasan_mode' => 'required|in:hari',
                'lama_cuti' => 'required|integer|min:1|max:548',
            ], [
                'dokumen_sakit.required' => 'Surat keterangan dokter/bidan wajib dilampirkan',
            ]);

            if ((int) $request->lama_cuti <= 14) {
                $request->validate([
                    'lama_cuti' => 'required|integer|min:1|max:14',
                ], [
                    'lama_cuti.max' => 'Cuti sakit maksimal 14 hari untuk kategori sakit 1-14 hari',
                ]);
            }
        }

        if ($request->jenis_cuti === 'cuti_luar_tanggungan') {
            $request->validate([
                'dokumen_ppk' => $hasDokumenPpkCol ? (($cuti->dokumen_ppk ? 'nullable' : 'required') . '|file|mimes:pdf|max:2048') : 'nullable',
                'alasan_mode' => 'required|in:bulan',
                'lama_cuti' => 'required|integer|min:1|max:36',
            ], [
                'dokumen_ppk.required' => 'Surat keputusan PPK wajib dilampirkan',
                'lama_cuti.max' => 'Cuti di luar tanggungan negara maksimal 3 tahun (36 bulan)',
            ]);
        }

        $cuti->jenis_cuti = $request->jenis_cuti;
        $cuti->alasan_cuti = $request->alasan_cuti;
        $cuti->lama_cuti = $request->lama_cuti;
        $cuti->tanggal_mulai = $request->tanggal_mulai;
        $cuti->tanggal_selesai = $request->tanggal_selesai;
        $cuti->alamat = $request->alamat;
        $cuti->no_telepon = $request->no_telepon;

        if ($hasDokumenSakitCol && $request->jenis_cuti === 'cuti_sakit' && $request->hasFile('dokumen_sakit')) {
            if ($cuti->dokumen_sakit) {
                Storage::disk('public')->delete($cuti->dokumen_sakit);
            }
            $cuti->dokumen_sakit = $request->file('dokumen_sakit')->store('dokumen_cuti', 'public');
        }

        if ($hasDokumenPpkCol && $request->jenis_cuti === 'cuti_luar_tanggungan' && $request->hasFile('dokumen_ppk')) {
            if ($cuti->dokumen_ppk) {
                Storage::disk('public')->delete($cuti->dokumen_ppk);
            }
            $cuti->dokumen_ppk = $request->file('dokumen_ppk')->store('dokumen_cuti', 'public');
        }
        if(Auth::user()->role == 'admin') {
            $cuti->status_pengajuan = $request->status_pengajuan;
        }
        $cuti->save();

        if(Auth::user()->role == 'admin') {
            return redirect()->route('cuti.admin.index')->with('success', 'Data cuti berhasil diperbarui');
        }

        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil diperbarui');
    }

    public function destroy($id)
    {
        $cuti = $this->findCutiOrFail($id);
        $cuti->delete();

        if(Auth::user()->role == 'admin') {
            return redirect()->route('cuti.admin.index')->with('success', 'Data cuti berhasil dihapus');
        }

        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil dihapus');
    }

    public function adminShow($id)
    {
        if(Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        return $this->show($id);
    }

    public function adminEdit($id)
    {
        if(Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        return $this->edit($id);
    }

    public function adminUpdate(Request $request, $id)
    {
        if(Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        return $this->update($request, $id);
    }

    public function adminDestroy($id)
    {
        if(Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        return $this->destroy($id);
    }
}
