<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SuratMasukExport;
use App\Exports\DisposisiExport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use File;
use Auth;

class SuratMasukController extends Controller
{
    private $users = ['admin','kepala','verifikator','pegawai'];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!(in_array(Auth::user()->role,$this->users))) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $bread = "Surat Masuk";
        return view('suratmasuk.index', compact('bread'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  != 'verifikator' && Auth::user()->role  != 'pegawai') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $bread = "Tambah Surat Masuk";
        $suratmasuk = \App\SuratMasuk::latest('created_at')->first();
        $klasifikasi = \App\KlasifikasiSurat::get();
        $pamong = \App\User::get();
        return view('suratmasuk.insert', compact('bread', 'klasifikasi', 'pamong', 'suratmasuk'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->role  == 'verifikator' && Auth::user()->role  == 'pegawai') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        if(Auth::user()->role  == 'admin' || Auth::user()->role  == 'verifikator'){
            $request->validate([
                'nomor_surat' => 'required|string|max:50',
                // 'sifat' => 'required|string|max:20',
                'kode_surat' => 'required',
                'tanggal_penerimaan' => 'required',
                'tanggal_surat' => 'required',
                // 'isi_disposisi' => 'string',
                'pengirim' => 'required|string|max:100',
                'isi_singkat' => 'required|string|max:200',
                'berkas_scan' => 'file|mimes:pdf|max:2048'
            ]);
        }else{
            $request->validate([
                'berkas_scan' => 'file|mimes:pdf|max:2048'
            ]);
        }


        if($request->hasFile('berkas_scan')){
            $file = $request->file('berkas_scan');
            $nama_file = time()."_".$file->getClientOriginalName();
            $tujuan_upload = 'berkas/suratmasuk';
            $file->move($tujuan_upload,$nama_file);
        }

        $surat = new \App\SuratMasuk;
        if(Auth::user()->role== 'admin' || Auth::user()->role  == 'verifikator')
        {
            $surat->nomor_surat = $request->nomor_surat;
            $surat->sifat = $request->sifat;
            $surat->kode_surat = $request->kode_surat;
            $surat->tanggal_penerimaan = $request->tanggal_penerimaan;
            $surat->tanggal_surat = $request->tanggal_surat;
            $surat->pengirim = $request->pengirim;
            $surat->isi_singkat = $request->isi_singkat;
            $surat->nomor_agenda = $request->nomor_agenda;
        }else
        {
            $surat->tanggal_penerimaan = \Carbon\Carbon::now();
        }
        // $surat->isi_disposisi = $request->isi_disposisi;
        // if($request->disposisi_ke) {
        //     $surat->disposisi_ke = json_encode($request->disposisi_ke);
        // }
        $surat->user_id = Auth::user()->id;
        $surat->berkas_scan = $nama_file;
        $surat->save();

        // foreach($request->disposisi_ke as $user) {
        //     $disposisi = new \App\Disposisi;
        //     $disposisi->user_id = $user;
        //     $disposisi->surat_masuk_id = $surat->id;
        //     $disposisi->save();

        //     $tracking = new \App\Tracking;
        //     $tracking->disposisi_id = $disposisi->id;
        //     $tracking->hasil_disposisi = 'Belum ditanggapi';
        //     $tracking->save();
        // }

        return back()->with('success', 'Surat masuk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $suratmasuk =  \App\SuratMasuk::findOrFail($id);
        $bread = "Detail Surat Masuk";
        $klasifikasi = \App\KlasifikasiSurat::get();
        $pamong = \App\User::get();
        $disposisi = \App\Disposisi::where('surat_masuk_id','=',$id)->pluck('status','user_id');
        // dd($disposisi[14]);
        return view('suratmasuk.show', compact('suratmasuk', 'bread', 'klasifikasi', 'pamong','disposisi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!(in_array(Auth::user()->role,$this->users))) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $suratmasuk =  \App\SuratMasuk::findOrFail($id);
        $sm = \App\SuratMasuk::latest('nomor_agenda')->first();
        $bread = "Edit Surat Masuk";
        $klasifikasi = \App\KlasifikasiSurat::get();
        $pamong = \App\User::orderBy('jabatan_id')->get();
        return view('suratmasuk.edit', compact('suratmasuk', 'bread', 'klasifikasi', 'pamong', 'sm'));
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
        if(!(in_array(Auth::user()->role,$this->users))) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $surat = \App\SuratMasuk::findOrFail($id);
    if(Auth::user()->role!='pegawai'){

        $surat->nomor_surat = $request->nomor_surat;
        $surat->sifat = $request->sifat;
        $surat->kode_surat = $request->kode_surat;
        $surat->tanggal_penerimaan = $request->tanggal_penerimaan;
        $surat->tanggal_surat = $request->tanggal_surat;
        $surat->pengirim = $request->pengirim;
        $surat->isi_singkat = $request->isi_singkat;
        $surat->nomor_agenda = $request->nomor_agenda;
        $surat->disposisi = $request->disposisi;
        $surat->isi_disposisi = $request->isi_disposisi;
        if(Auth::user()->role=='verifikator' || Auth::user()->role=='admin'){
            $surat->verifikasi = $request->verifikasi;
            $surat->catatan = $request->catatan;
        }
        // if($request->verifikasi){
        //     $surat->verifikasi = 1;
        // }else{
        // $surat->verifikasi = 0;
        // }


        if($request->disposisi_ke) {
            $surat->disposisi_ke = json_encode($request->disposisi_ke);
        }else{
            $surat->disposisi_ke = json_encode(array());
        }
    }
        if($request->hasFile('berkas_scan')){
            $request->validate([
                'berkas_scan' => 'file|mimes:pdf|max:2048'
            ]);
            $image_path = 'berkas/suratmasuk/'.$surat->berkas_scan;
            if(File::exists($image_path)) {
                File::delete($image_path);
            }
            $file = $request->file('berkas_scan');
            $nama_file = time()."_".$file->getClientOriginalName();
            $tujuan_upload = 'berkas/suratmasuk';
            $file->move($tujuan_upload,$nama_file);
            $surat->berkas_scan = $nama_file;
        }


        $surat->save();


        if(!empty($request->disposisi_ke)){
        foreach($request->disposisi_ke as $user) {
            $check = \App\Disposisi::where('user_id', $user)->where('surat_masuk_id', $surat->id)->first();
            if(!$check) {
                $disposisi = new \App\Disposisi;
                $disposisi->user_id = $user;
                $disposisi->surat_masuk_id = $surat->id;
                $disposisi->save();
            }
        }


        $deleteother = \App\Disposisi::where('surat_masuk_id', $surat->id)->whereNotIn('user_id', $request->disposisi_ke)->delete();
        }else{
            // echo '<script language="javascript">window.alert("tes")</script>';
            $deleteSurat = \App\Disposisi::where('surat_masuk_id', $surat->id)->delete();
        }

        return back()->with('success', 'Surat masuk berhasil diperbarui');
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
        $surat = \App\SuratMasuk::findOrFail($id);

        $image_path = 'berkas/suratmasuk/'.$surat->berkas_scan;
        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        $surat->delete();

        $disposisi = $surat = \App\Disposisi::where('surat_masuk_id', $id)->delete();

        return back()->with('success', 'Data surat masuk berhasil dihapus');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export_excel(Request $request, $excel = null)
	{
        if(!(in_array(Auth::user()->role,$this->users))) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        if($excel == 'SuratMasuk' || $request->jenis_surat == 'suratmasuk'){
            return Excel::download(new SuratMasukExport($request->tahun,$request->bulan,$request->kode_surat), 'SuratMasuk'.date('dmY').'_'.$request->kode_surat.$request->bulan.$request->tahun.'.xlsx');
        }else{
            return Excel::download(new DisposisiExport, 'Diposisi'.date('dmY').'.xlsx');
        }
	}

    public function getSuratMasuk()
    {

        if(Auth::user()->role  != 'pegawai') {
            $suratMasuk = \App\SuratMasuk::leftjoin('users','user_id','=','users.id')
            ->selectRaw('surat_masuk.id,
            surat_masuk.created_at,
            surat_masuk.updated_at,
            surat_masuk.nomor_agenda,
            DATE_FORMAT(surat_masuk.tanggal_penerimaan ,"%d-%m-%Y") as date,
            surat_masuk.nomor_surat,
            surat_masuk.pengirim,
            surat_masuk.isi_singkat,
            users.name,
            surat_masuk.verifikasi,
            surat_masuk.disposisi_ke')
            ->latest('created_at')
            ->get();
            return Datatables::of($suratMasuk)
            ->addColumn('action', function ($suratMasuk) {
                $buttonDelete='';
                if(Auth::user()->role == 'kepala'){
                    $buttonEdit = 'Disposisi';
                }
                elseif(Auth::user()->role == 'verifikator'){
                    $buttonEdit = 'Periksa';
                }
                else{
                    $buttonEdit = 'Edit';
                    if(Auth::user()->role  == 'admin'){
                        $buttonDelete='<form onsubmit="return confirm(\'Yakin ingin menghapus data ini secara permanen?\')" class="d-inline"
                        action="'.route('surat-masuk.destroy', $suratMasuk->id).'" method="POST" style="margin-left:6px;">'.
                                                csrf_field().'
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-trash"></i>
                        </button>
                        </form>';
                    }
                }

                return '<a href="'.route('surat-masuk.edit', $suratMasuk->id).'" class="btn btn-sm text-white" title="'.$buttonEdit.'" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="'.route('surat-masuk.show', $suratMasuk->id).'" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                            <i class="fa fa-eye"></i>
                        </a>'.$buttonDelete;

            })
            ->make(true);
        }else{
            $suratMasuk = \App\SuratMasuk::selectRaw('id,DATE_FORMAT(tanggal_penerimaan,"%d-%m-%Y") as date,berkas_scan')
            ->where('user_id','=',Auth::user()->id)->latest('tanggal_penerimaan')->orderByRaw('mid(nomor_surat,3,7) DESC')
            ->get();
            return Datatables::of($suratMasuk)
            ->addColumn('action', function ($suratMasuk) {
                return '<button type="button" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;" data-toggle="modal" data-target="#detailModal" onclick="getSurat(\''.$suratMasuk->berkas_scan.'\')">
                            <i class="fa fa-eye"></i>
                        </button>';
            })
            ->make(true);
        }
    }
}
