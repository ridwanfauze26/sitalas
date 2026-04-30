<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SuratKeluarExport;
use Maatwebsite\Excel\Facades\Excel;
use File;
use Gate;
use Auth;
use DataTables;

class SuratKeluarController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function($request, $next){
            if(Gate::any(['admin','kepala','verifikator','pegawai'])) return $next($request);
            abort(403, 'Anda tidak memiliki hak akses untuk mengakses halaman ini');
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bread = "Surat Keluar";
        return view('suratkeluar.index', compact('bread'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'pegawai' && Auth::user()->role  != 'verifikator') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $bread = "Tambah Surat Keluar";
        $klasifikasi = \App\KlasifikasiSurat::get();
        $user = \App\User::get();
        $latest = \App\SuratKeluar::latest('tanggal_surat')->orderByRaw('mid(nomor_surat,3,7) DESC')->first();
        return view('suratkeluar.insert', compact('bread', 'klasifikasi','user','latest'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'pegawai' && Auth::user()->role  != 'verifikator') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $request->validate([
            // 'nomor_surat' => 'required|string|max:50',
            // 'kode_surat' => 'required',
            'tanggal_surat' => 'required',
            'tujuan' => 'required|string|max:100',
            'isi_singkat' => 'required|string|max:200',
            'tanggal_pengiriman' => 'required',
            'media_pengiriman' => 'required',
            'berkas_scan' => 'file|mimes:pdf|max:2048'
        ]);

        if($request->hasFile('berkas_scan')){
            $file = $request->file('berkas_scan');
            $nama_file = time()."_".$file->getClientOriginalName();
            $tujuan_upload = 'berkas/suratkeluar';
            $file->move($tujuan_upload,$nama_file);
        }else{
            $nama_file='Belum Upload';
        }

        $surat = new \App\SuratKeluar;
        $surat->nomor_surat = $request->nomor_surat;    
        $surat->kode_surat = $request->kode_surat;
        $surat->sifat_surat = $request->sifat_surat;
        $surat->tanggal_surat = $request->tanggal_surat;
        $surat->tujuan = $request->tujuan;
        $surat->isi_singkat = $request->isi_singkat;
        $surat->tanggal_pengiriman = $request->tanggal_pengiriman;
        $surat->media_pengiriman = $request->media_pengiriman;
        $surat->berkas_scan = $nama_file;
        if(Auth::user()->role == 'admin'){
            $surat->user_id = $request->user;
        }else{
            $surat->user_id = Auth::user()->id;
        }
        
        $surat->save();

        return back()->with('success', 'Surat keluar berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $suratkeluar =  \App\SuratKeluar::findOrFail($id);
        $bread = "Detail Surat Keluar";
        $klasifikasi = \App\KlasifikasiSurat::get();
        return view('suratkeluar.show', compact('suratkeluar', 'bread', 'klasifikasi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'pegawai' && Auth::user()->role  != 'verifikator') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $suratkeluar =  \App\SuratKeluar::findOrFail($id);
        $bread = "Edit Surat Keluar";
        $klasifikasi = \App\KlasifikasiSurat::get();
        $user = \App\User::get();
        $latest = \App\SuratKeluar::latest('tanggal_surat')->orderByRaw('mid(nomor_surat,3,7) DESC')->first();
        return view('suratkeluar.edit', compact('suratkeluar', 'bread', 'klasifikasi','latest', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'pegawai' && Auth::user()->role  != 'verifikator') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $request->validate([
            // 'nomor_surat' => 'required|string|max:50',
            // 'kode_surat' => 'required',
            'tanggal_surat' => 'required',
            'tujuan' => 'required|string|max:100',
            'isi_singkat' => 'required|string|max:200'
        ]);

        $surat = \App\SuratKeluar::findOrFail($id);
        if(Auth::user()->role  == 'admin'){
            $surat->nomor_surat = $request->nomor_surat;
            $surat->kode_surat = $request->kode_surat;
            $surat->sifat_surat = $request->sifat_surat;
        }
        $surat->tanggal_surat = $request->tanggal_surat;
        $surat->tujuan = $request->tujuan;
        $surat->isi_singkat = $request->isi_singkat;
        $surat->tanggal_pengiriman = $request->tanggal_pengiriman;
        $surat->media_pengiriman = $request->media_pengiriman;
        if(Auth::user()->role == 'admin'){
            $surat->user_id = $request->user;
        }else{
            $surat->user_id = Auth::user()->id;
        }
        if($request->hasFile('berkas_scan')){
            $request->validate([
                'berkas_scan' => 'file|mimes:pdf|max:2048'
            ]);
            $image_path = 'berkas/suratkeluar/'.$surat->berkas_scan; 
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
            $file = $request->file('berkas_scan');
            $nama_file = time()."_".$file->getClientOriginalName();
            $tujuan_upload = 'berkas/suratkeluar';
            $file->move($tujuan_upload,$nama_file);
            $surat->berkas_scan = $nama_file;
        }
        $surat->save();

        return back()->with('success', 'Surat keluar berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->role  != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $surat = \App\SuratKeluar::findOrFail($id);

        // $image_path = 'berkas/suratkeluar/'.$surat->berkas_scan; 
        // if(File::exists($image_path)) {
        //     File::delete($image_path);
        // }
        $surat->delete();
        return back()->with('success', 'Data surat keluar berhasil dihapus');
    }
    public function export_excel(Request $request)
	{
		return Excel::download(new SuratKeluarExport($request->tahun,$request->bulan,$request->kode_surat), 'SuratKeluar'.date('dmY').'_'.$request->kode_surat.$request->bulan.$request->tahun.'.xlsx');
	}

    public function getNoSurat($date){
        $arr['data'] = \App\SuratKeluar::where('tanggal_surat','=',date($date))->orderByRaw('mid(nomor_surat,3,7) DESC')->pluck('nomor_surat')->first();
        echo json_encode($arr);
        exit;
    }

    public function getSuratKeluar()
    {
        if(Auth::user()->role  != 'pegawai') {
            $suratKeluar = \App\SuratKeluar::leftjoin('users','user_id','=','users.id')
            ->selectRaw('surat_keluar.id, surat_keluar.nomor_surat, surat_keluar.berkas_scan, surat_keluar.tujuan, surat_keluar.isi_singkat, users.name, DATE_FORMAT(surat_keluar.tanggal_surat ,"%d-%m-%Y") as date')
            ->latest('tanggal_surat')->orderByRaw('nomor_surat IS NULL DESC, mid(nomor_surat,3,7) DESC' )->get();
            return Datatables::of($suratKeluar)
            ->addColumn('action', function ($suratKeluar) {
                $buttonDelete='';
                if(Auth::user()->role  == 'admin'){
                    $buttonDelete = ' <form onsubmit="return confirm(\'Yakin ingin menghapus data ini secara permanen?\')" class="d-inline" action="'.route('surat-keluar.destroy', $suratKeluar->id).'" method="POST" style="margin-left:6px;">'.
                                            csrf_field().'
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="fa fa-trash"></i>
                    </button>
                    </form>';
                }

                return '<a href="'.route('surat-keluar.edit', $suratKeluar->id).'" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="'.route('surat-keluar.show', $suratKeluar->id).'" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                            <i class="fa fa-eye"></i>
                        </a>'.$buttonDelete;
               
                
            })
            ->make(true);
        }else{
            $suratKeluar = \App\SuratKeluar::selectRaw('id,nomor_surat,tujuan,isi_singkat,DATE_FORMAT(tanggal_surat,"%d-%m-%Y") as date, berkas_scan')
            ->where('user_id','=',Auth::user()->id)->latest('tanggal_surat')->orderByRaw('nomor_surat IS NULL DESC, mid(nomor_surat,3,7) DESC')
            ->get();
            return Datatables::of($suratKeluar)
            ->addColumn('action', function ($suratKeluar) {
                return '<a href="'.route('surat-keluar.edit', $suratKeluar->id).'" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="'.route('surat-keluar.show', $suratKeluar->id).'" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                            <i class="fa fa-eye"></i>
                        </a>';
            })
            ->make(true);
        }
    }
}
