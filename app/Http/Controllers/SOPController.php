<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\SOP;
use Illuminate\Support\Facades\Storage; // Untuk menyimpan file
use Maatwebsite\Excel\Facades\Excel; // Untuk mengimpor dan menangani file Excel
use App\Imports\SOPImport; // Membuat import class (bisa dibuat di langkah selanjutnya)
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SOPController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role != 'admin' && Auth::user()->role != 'kepala' && Auth::user()->role != 'verifikator' && Auth::user()->role != 'pegawai') {
                abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SOP::query();

            if ($request->filled('bulan') && $request->filled('tahun')) {
                $query->whereMonth('tanggal', $request->bulan)
                      ->whereYear('tanggal', $request->tahun);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tanggal', function($row){
                    return Carbon::parse($row->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('file_sop', function ($row) {
                    return '<a href="'.route('sop.view', $row->id).'" target="_blank" class="text-danger">
                                <i class="far fa-file-pdf fa-2x"></i>
                            </a>';
                })
                ->addColumn('action', function ($row) {
                    $buttonDelete = '';
                    if (Auth::user()->role == 'admin') {
                        $buttonDelete = '<form action="'.route('sop.destroy', $row->id).'" method="POST" class="d-inline" style="margin-left:6px;">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>';
                    }

                    $buttonEdit = '';
                    if (Auth::user()->role != 'kepala' && Auth::user()->role != 'pegawai') {
                        $buttonEdit = '<a href="'.route('sop.edit', $row->id).'" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-pencil"></i>
                        </a>';
                    }

                    return $buttonEdit
                        .'<a href="'.route('sop.show', $row->id).'" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                            <i class="fa fa-eye"></i>
                        </a>'
                        .$buttonDelete;
                })
                ->rawColumns(['file_sop', 'action'])
                ->make(true);
        }

        // Dapatkan tahun unik dari database untuk dropdown
        $tahunList = SOP::selectRaw('YEAR(tanggal) as tahun')
                        ->distinct()
                        ->orderBy('tahun', 'desc')
                        ->pluck('tahun');

        return view('sop.index', compact('tahunList'));
    }

    public function show($id)
    {
    // Cek apakah pengguna memiliki peran 'admin' atau 'kepala'
    if (Auth::user()->role != 'admin' && Auth::user()->role != 'kepala' && Auth::user()->role != 'pegawai') {
        abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
    }

    // Mengambil data SOP berdasarkan ID
    $sop = SOP::findOrFail($id);

    // Menampilkan tampilan dengan data SOP
    return view('sop.show', compact('sop'));
    }

    // Menampilkan halaman form pembuatan SOP
    public function create()
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        // Mengembalikan tampilan form create SOP
        return view('sop.create');
    }

    // Menyimpan data SOP
    public function store(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        try {
            // Validasi input dari formulir dengan pesan kustom
            $request->validate([
                'judul' => 'required|string|max:255',
                'file_sop' => [
                    'required',
                    'file',
                    'mimes:pdf',
                ],
                'tanggal' => 'required|date',
            ], [
                'judul.required' => 'Mohon isi judul SOP',
                'judul.max' => 'Judul terlalu panjang, maksimal 255 karakter',
                'file_sop.required' => 'Mohon pilih file SOP yang akan diunggah',
                'file_sop.file' => 'Data yang diunggah harus berupa file',
                'file_sop.mimes' => 'Maaf, format file tidak sesuai. Silakan unggah file dengan format PDF',
                'tanggal.required' => 'Mohon isi tanggal SOP',
                'tanggal.date' => 'Format tanggal tidak valid'
            ]);

            // Menyimpan file SOP
            $file = $request->file('file_sop');

            // Batasan ukuran untuk file PDF
            if ($file->getSize() > 2048 * 1024) {
                return response()->json([
                    'errors' => ['file_sop' => ['Ukuran file PDF maksimal 2MB']]
                ], 422);
            }

            // Buat direktori jika belum ada
            $directory = public_path('berkas/sop');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate nama file unik
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'berkas/sop/' . $fileName;

            // Pindahkan file ke direktori public
            $file->move($directory, $fileName);

            // Menyimpan data SOP ke database
            $sop = new SOP();
            $sop->judul = $request->judul;
            $sop->file_sop = $filePath;
            $sop->tanggal = $request->tanggal;
            $sop->save();

            if ($request->ajax()) {
                return response()->json(['message' => 'SOP berhasil disimpan']);
            }

            return redirect()->route('sop.create')
                ->with('success', 'SOP berhasil disimpan');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => ['error' => ['Terjadi kesalahan saat menyimpan SOP. Silakan coba lagi.']]
                ], 422);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan SOP. Silakan coba lagi.']);
        }
    }

    public function viewFile($id)
    {
        try {
            $sop = SOP::findOrFail($id);

            // Normalisasi path file
            $filePath = str_replace('\\', '/', $sop->file_sop);
            $filePath = ltrim($filePath, '/');

            // Cek apakah file ada di public
            $publicPath = public_path($filePath);
            if (file_exists($publicPath)) {
                $extension = pathinfo($publicPath, PATHINFO_EXTENSION);
                $contentType = 'application/pdf'; // Default untuk PDF

                return response()->file($publicPath, ['Content-Type' => $contentType]);
            }

            // Jika tidak ada di public, coba cari di storage
            $storagePath = storage_path('app/public/' . $filePath);
            if (file_exists($storagePath)) {
                return response()->file($storagePath, ['Content-Type' => 'application/pdf']);
            }

            throw new \Exception('File tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error viewing SOP file: ' . $e->getMessage());
            abort(404, 'File tidak ditemukan');
        }
    }

    public function destroy($id)
    {
        try {
            $sop = SOP::findOrFail($id);

            // Hapus file menggunakan Storage facade
            if ($sop->file_sop) {
                $filePath = $sop->file_sop;

                // Coba hapus menggunakan Storage facade
                if (Storage::exists($filePath)) {
                    if (!Storage::delete($filePath)) {
                        Log::error('Failed to delete file using Storage facade: ' . $filePath);
                    } else {
                        Log::info('File deleted using Storage facade: ' . $filePath);
                    }
                }

                // Backup: coba hapus langsung dari public
                $publicPath = public_path($filePath);
                if (file_exists($publicPath)) {
                    if (!unlink($publicPath)) {
                        Log::error('Failed to delete file from public: ' . $publicPath);
                    } else {
                        Log::info('File deleted from public: ' . $publicPath);
                    }
                }
            }

            // Hapus record dari database
            $sop->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'SOP berhasil dihapus']);
            }

            return redirect()->route('sop.index')
                ->with('success', 'SOP berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting SOP: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus SOP: ' . $e->getMessage()], 500);
            }

            return redirect()->route('sop.index')
                ->with('error', 'Gagal menghapus SOP: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (Auth::user()->role == 'kepala' || Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $sop = SOP::findOrFail($id);
        return view('sop.edit', compact('sop'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        try {
            // Validasi input dari formulir dengan pesan kustom
            $request->validate([
                'judul' => 'required|string|max:255',
                'file_sop' => [
                    'nullable',
                    'file',
                    'mimes:pdf',
                    'max:2048', // Maksimal 2MB
                ],
                'tanggal' => 'required|date',
            ], [
                'judul.required' => 'Mohon isi judul SOP',
                'judul.max' => 'Judul terlalu panjang, maksimal 255 karakter',
                'file_sop.file' => 'Data yang diunggah harus berupa file',
                'file_sop.mimes' => 'Maaf, format file tidak sesuai. Silakan unggah file dengan format PDF',
                'file_sop.max' => 'Ukuran file maksimal 2MB',
                'tanggal.required' => 'Mohon isi tanggal SOP',
                'tanggal.date' => 'Format tanggal tidak valid'
            ]);

            // Ambil data SOP yang akan diupdate
            $sop = SOP::findOrFail($id);

            // Update data SOP
            $sop->judul = $request->judul;
            $sop->tanggal = $request->tanggal;

            // Jika ada file baru yang diunggah
            if ($request->hasFile('file_sop')) {
                // Hapus file lama jika ada
                $oldFilePath = public_path($sop->file_sop);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }

                // Simpan file baru
                $file = $request->file('file_sop');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('berkas/sop'), $fileName);
                $filePath = 'berkas/sop/' . $fileName;
                $sop->file_sop = $filePath;
            }

            $sop->save();

            return redirect()->route('sop.index')
                ->with('success', 'SOP berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui SOP. Silakan coba lagi.']);
        }
    }

    public function getData(Request $request)
    {
        $data = Sop::query();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('tanggal', function($row){
                return \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y');
            })
            ->addColumn('file_sop', function ($row) {
                return '<a href="'.route('sop.view', $row->id).'" target="_blank" class="text-danger">
                            <i class="far fa-file-pdf fa-2x"></i>
                        </a>';
            })
            ->addColumn('action', function ($row) {
                $buttonDelete = '';
                if (Auth::user()->role == 'admin') {
                    $buttonDelete = '<form onsubmit="return confirm(\'Yakin ingin menghapus data ini secara permanen?\')" class="d-inline"
                        action="'.route('sop.destroy', $row->id).'" method="POST">'.
                        csrf_field().'
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-icon-text">Hapus</button>
                        </form>';
                }

                return '<a href="'.route('sop.edit', $row->id).'" class="btn btn-info btn-icon-text mr-1">Edit</a>'
                        .'<a href="'.route('sop.show', $row->id).'" class="btn btn-primary btn-icon-text mr-1">Detail</a>'
                        .$buttonDelete;
            })
            ->rawColumns(['file_sop', 'action'])
            ->make(true);
    }
}
