<?php

namespace App\Http\Controllers;

use App\SuratKeluar;
use Illuminate\Http\Request;
use Auth;
use DataTables;
use DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'kepala' && Auth::user()->role  != 'verifikator' && Auth::user()->role  != 'pegawai') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $visibleLevels = [];
        if (Auth::user()->role === 'admin') {
            $visibleLevels = [1, 2, 3];
        } elseif (Auth::user()->role === 'kepala') {
            $visibleLevels = [2, 3];
        } elseif ((int) Auth::user()->cuti_level === 1) {
            $visibleLevels = [2, 3];
        } elseif ((int) Auth::user()->cuti_level === 2) {
            $visibleLevels = [3];
        }

        $cutiSedang = collect();
        if ($visibleLevels) {
            $query = \App\Cuti::with(['user', 'user.jabatan'])
                ->where('status_pengajuan', 'Disetujui')
                ->whereNotNull('tanggal_mulai')
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_mulai', '=', $today);

            if (Auth::user()->role !== 'admin') {
                $query->whereHas('user.jabatan', function ($q) use ($visibleLevels) {
                    $q->whereIn('level', $visibleLevels);
                });
            }

            $cutiSedang = $query->orderBy('tanggal_mulai', 'asc')->get();
        }

        $klasifikasi = \App\KlasifikasiSurat::count();
        $suratmasuk = \App\SuratMasuk::count();
        $belumDisposisi = \App\SuratMasuk::Where('disposisi_ke','=','[]')->count();
        $belumBernomor = \App\SuratKeluar::whereNULL('nomor_surat')->count();
        if(Auth::user()->role == 'pegawai'){
            $suratkeluar = \App\SuratKeluar::where('user_id','=',Auth::user()->id)->pluck('nomor_surat');
            // $array = array(1, "hello", 1, "world", "hello");
            // dd($suratkeluar);
        }else{
            $suratkeluar = \App\SuratKeluar::count();
            
        }
        $disposisi = \App\Disposisi::where('user_id','=',Auth::user()->id)->count();
        $suratmasukperbulan = \App\SuratMasuk::select('tanggal_penerimaan') 
        ->whereYear('tanggal_penerimaan',date('Y')) 
        ->latest('tanggal_penerimaan')                                                                                   
        ->get()->groupBy(function($time) {                            
           return \Carbon\Carbon::parse($time->tanggal_penerimaan)->format('F');                        
        });
        $suratkeluarperbulan = \App\SuratKeluar::select('tanggal_surat') 
        ->whereYear('tanggal_surat',date('Y')) 
        ->latest('tanggal_surat')                                                                                   
        ->get()->groupBy(function($time) {                            
           return \Carbon\Carbon::parse($time->tanggal_surat)->format('F');                        
        });
        $bulansuratmasuk = [];
        $jumlahsuratmasukperbulan = [];

        $bulansuratkeluar = [];
        $jumlahsuratkeluarperbulan = [];
        foreach($suratmasukperbulan as $entry => $val) {
            array_unshift($bulansuratmasuk,$entry);
            array_unshift($jumlahsuratmasukperbulan,count($val));
        }

        foreach($suratkeluarperbulan as $entry => $val) {
            array_unshift($bulansuratkeluar,$entry);
            array_unshift($jumlahsuratkeluarperbulan,count($val));
        }
        // $ouput = array_keys($suratmasukperbulan);
        // var_dump($suratmasukperbulan);
        // $bulan=asort($bulan);
        // dd($bulan);
        $user = \App\User::count();
        return view('home', compact('klasifikasi', 'suratmasuk', 'suratkeluar', 'user', 'bulansuratmasuk', 'jumlahsuratmasukperbulan','bulansuratkeluar', 'jumlahsuratkeluarperbulan', 'disposisi', 'belumDisposisi', 'belumBernomor', 'cutiSedang'));
    }

    public function sedangCuti()
    {
        if (Auth::user()->role  != 'admin' && Auth::user()->role  != 'kepala' && Auth::user()->role  != 'verifikator' && Auth::user()->role  != 'pegawai') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $visibleLevels = [];
        if (Auth::user()->role === 'admin') {
            $visibleLevels = [1, 2, 3];
        } elseif (Auth::user()->role === 'kepala') {
            $visibleLevels = [2, 3];
        } elseif ((int) Auth::user()->cuti_level === 1) {
            $visibleLevels = [2, 3];
        } elseif ((int) Auth::user()->cuti_level === 2) {
            $visibleLevels = [3];
        }

        if (!$visibleLevels) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $query = \App\Cuti::with(['user', 'user.jabatan'])
            ->where('status_pengajuan', 'Disetujui')
            ->whereNotNull('tanggal_mulai')
            ->whereNotNull('tanggal_selesai')
            ->whereDate('tanggal_mulai', '=', $today);

        if (Auth::user()->role !== 'admin') {
            $query->whereHas('user.jabatan', function ($q) use ($visibleLevels) {
                $q->whereIn('level', $visibleLevels);
            });
        }

        $cutiSedang = $query->orderBy('tanggal_mulai', 'asc')->get();

        return view('home_sedang_cuti', compact('cutiSedang', 'today'));
    }

    public function tracking($id, $user)
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'kepala') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $disposisi = \App\Disposisi::where('surat_masuk_id', $id)->where('user_id', $user)->latest()->first()->id;
        $tracking = \App\Tracking::where('disposisi_id', $disposisi)->latest()->get();
        return view('tracking', compact('tracking'));
    }

    public function disposisi()
    {
        $bread = "Disposisi";
        return view('disposisi', compact('bread'));
    }

    public function respon(Request $req, $id)
    {
        $disposisi = \App\Disposisi::findOrFail($id);
        if($disposisi->user_id != Auth::user()->id) {
            abort(403, 'Anda tidak memiliki hak akses');
        }
        $disposisi->status = 1;
        $disposisi->save();
        
        $tracking = new \App\Tracking;
        $tracking->disposisi_id = $disposisi->id;
        $tracking->hasil_disposisi = $req->hasil_disposisi;
        $tracking->save();

        return back()->with('success', 'Tanggapan berhasil ditambahkan');
    }

    public function getDisposisi()
    {
        $disposisi =\App\Disposisi::where('user_id', Auth::user()->id)->latest()->get();
        // $newDate = \Carbon\Carbon::parse($disposisi->created_at)->format('d-m-Y h:i');
        return Datatables::of($disposisi)
        ->addColumn('sifat', function($disposisi){return $disposisi->surat_masuk->sifat;})
        ->addColumn('isi_singkat', function($disposisi){return $disposisi->surat_masuk->isi_singkat;})
        ->addColumn('date', function($disposisi){return \Carbon\Carbon::parse($disposisi->created_at)->format('d-m-Y h:i');})
        ->addColumn('noAgenda', function($disposisi){return $disposisi->surat_masuk->nomor_agenda;})
        ->addColumn('tanggal_penerimaan', function($disposisi){return \Carbon\Carbon::parse($disposisi->surat_masuk->tanggal_penerimaan)->format('d/m/Y');})
        ->addColumn('tanggal_surat', function($disposisi){return \Carbon\Carbon::parse($disposisi->surat_masuk->tanggal_surat)->format('d/m/Y');})
        ->addColumn('sifat_surat', function($disposisi){return $disposisi->surat_masuk->sifat;})
        ->addColumn('nomor_surat', function($disposisi){return $disposisi->surat_masuk->nomor_surat;})
        ->addColumn('pengirim', function($disposisi){return $disposisi->surat_masuk->pengirim;})
        ->addColumn('disposisi', function($disposisi){return $disposisi->surat_masuk->disposisi;})
        ->addColumn('isi_disposisi', function($disposisi){return $disposisi->surat_masuk->isi_disposisi;})
        ->addColumn('berkas_scan', function($disposisi){return $disposisi->surat_masuk->berkas_scan;})
        ->addColumn('action', function ($disposisi) {
            return '
            <button type="button" class="btn p-2 btn-primary" data-toggle="modal" data-target="#detailModal" onclick="getDisposisi('.$disposisi->id.')"">
                Detail Disposisi
            </button>
            <button type="button" class="btn p-2 btn-success" data-toggle="modal" data-target="#responseModal" onclick="setLink('.$disposisi->id.')"">
                Tanggapi
            </button>';
        })
        ->make(true);
    }

    public function report()
    {
        $klasifikasi = \App\KlasifikasiSurat::get();
        $bread = "Laporan";
        return view('report', compact('bread','klasifikasi'));
    }

    public function getDataTable($tablename)
    {
        switch($tablename)
        {
            case "suratmasuk":
                $arr['data'] = \App\SuratMasuk::leftjoin('klasifikasi_surat','kode_surat','=','klasifikasi_surat.kode')
                ->selectRaw('DATE_FORMAT(surat_masuk.tanggal_penerimaan,"%m") as bulan,surat_masuk.tanggal_penerimaan, surat_masuk.kode_surat, klasifikasi_surat.nama')
                ->orderBy('kode_surat')
                ->orderBy('tanggal_penerimaan')
                ->get()
                ->groupBy(function($time) {                            
                    return \Carbon\Carbon::parse($time->tanggal_penerimaan)->format('Y');                        
                 });
                 echo json_encode($arr);
                // return response()->json($arr);
                break;
            case "suratkeluar":
                $arr['data'] = \App\SuratKeluar::leftjoin('klasifikasi_surat','kode_surat','=','klasifikasi_surat.kode')
                ->selectRaw('DATE_FORMAT(surat_keluar.tanggal_surat,"%m") as bulan,surat_keluar.tanggal_surat, surat_keluar.kode_surat, klasifikasi_surat.nama')
                ->orderBy('kode_surat')
                ->orderBy('tanggal_surat') 
                ->get()
                ->groupBy(function($time) {                            
                    return \Carbon\Carbon::parse($time->tanggal_surat)->format('Y');                        
                 });
                 echo json_encode($arr);
                // return response()->json($arr);
                break;
        }
    }
  
}
