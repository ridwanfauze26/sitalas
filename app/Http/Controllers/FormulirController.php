<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Formulir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class FormulirController extends Controller
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
            $query = Formulir::query();

            if ($request->filled('bulan') && $request->filled('tahun')) {
                $query->whereMonth('tanggal', $request->bulan)
                      ->whereYear('tanggal', $request->tahun);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tanggal', function($row){
                    return Carbon::parse($row->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('file_formulir', function ($row) {
                    $extension = pathinfo($row->file_formulir, PATHINFO_EXTENSION);
                    $icon = '';
                    switch($extension) {
                        case 'pdf':
                            $icon = 'far fa-file-pdf text-danger';
                            break;
                        case 'docx':
                        case 'doc':
                            $icon = 'far fa-file-word text-primary';
                            break;
                        case 'xlsx':
                        case 'xls':
                            $icon = 'far fa-file-excel text-success';
                            break;
                        default:
                            $icon = 'far fa-file text-secondary';
                    }
                    return '<a href="'.route('formulir.view', $row->id).'" target="_blank">
                                <i class="'.$icon.' fa-2x"></i>
                            </a>';
                })
                ->addColumn('action', function ($row) {
                    $buttonDelete = '';
                    if (Auth::user()->role == 'admin') {
                        $buttonDelete = '<form action="'.route('formulir.destroy', $row->id).'" method="POST" class="d-inline" style="margin-left:6px;">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>';
                    }

                    $buttonEdit = '';
                    if (Auth::user()->role != 'kepala' && Auth::user()->role != 'pegawai') {
                        $buttonEdit = '<a href="'.route('formulir.edit', $row->id).'" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-pencil"></i>
                        </a>';
                    }

                    return $buttonEdit
                        .'<a href="'.route('formulir.show', $row->id).'" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                            <i class="fa fa-eye"></i>
                        </a>'
                        .$buttonDelete;
                })
                ->rawColumns(['file_formulir', 'action'])
                ->make(true);
        }

        // Dapatkan tahun unik dari database untuk dropdown
        $tahunList = Formulir::selectRaw('YEAR(tanggal) as tahun')
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun');

        return view('formulir.index', compact('tahunList'));
    }

    public function create()
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        return view('formulir.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        try {
            // Validasi input dari formulir dengan pesan kustom
            $request->validate([
                'judul' => 'required|string|max:255',
                'file_formulir' => [
                    'required',
                    'file',
                    'mimes:pdf',
                ],
                'tanggal' => 'required|date',
            ], [
                'judul.required' => 'Mohon isi judul formulir',
                'judul.max' => 'Judul terlalu panjang, maksimal 255 karakter',
                'file_formulir.required' => 'Mohon pilih file formulir yang akan diunggah',
                'file_formulir.file' => 'Data yang diunggah harus berupa file',
                'file_formulir.mimes' => 'Maaf, format file tidak sesuai. Silakan unggah file dengan format PDF',
                'tanggal.required' => 'Mohon isi tanggal formulir',
                'tanggal.date' => 'Format tanggal tidak valid'
            ]);

            // Menyimpan file formulir
            $file = $request->file('file_formulir');

            // Batasan ukuran untuk file PDF
            if ($file->getSize() > 2048 * 1024) {
                return response()->json([
                    'errors' => ['file_formulir' => ['Ukuran file PDF maksimal 2MB']]
                ], 422);
            }

            // Buat direktori jika belum ada
            $directory = public_path('berkas/formulir');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate nama file unik
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'berkas/formulir/' . $fileName;

            // Pindahkan file ke direktori public
            $file->move($directory, $fileName);

            // Menyimpan data formulir ke database
            $formulir = new Formulir();
            $formulir->judul = $request->judul;
            $formulir->file_formulir = $filePath;
            $formulir->tanggal = $request->tanggal;
            $formulir->save();

            if ($request->ajax()) {
                return response()->json(['message' => 'Formulir berhasil disimpan']);
            }

            return redirect()->route('formulir.create')
                ->with('success', 'Formulir berhasil disimpan');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => ['error' => ['Terjadi kesalahan saat menyimpan formulir. Silakan coba lagi.']]
                ], 422);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan formulir. Silakan coba lagi.']);
        }
    }

    public function viewFile($id)
    {
        try {
            $formulir = Formulir::findOrFail($id);

            // Normalisasi path file
            $filePath = str_replace('\\', '/', $formulir->file_formulir);
            $filePath = ltrim($filePath, '/');

            // Cek apakah file ada di public
            $publicPath = public_path($filePath);
            if (file_exists($publicPath)) {
                return response()->file($publicPath, ['Content-Type' => 'application/pdf']);
            }

            // Jika tidak ada di public, coba cari di storage
            $storagePath = storage_path('app/public/' . $filePath);
            if (file_exists($storagePath)) {
                return response()->file($storagePath, ['Content-Type' => 'application/pdf']);
            }

            throw new \Exception('File tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error viewing Formulir file: ' . $e->getMessage());
            abort(404, 'File tidak ditemukan');
        }
    }

    public function destroy($id)
    {
        try {
            $formulir = Formulir::findOrFail($id);

            // Hapus file dari direktori public
            $filePath = public_path($formulir->file_formulir);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Hapus data formulir
            $formulir->delete();

            return redirect()->route('formulir.index')
                ->with('success', 'Formulir berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error in destroy Formulir: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        if (Auth::user()->role != 'admin' && Auth::user()->role != 'kepala' && Auth::user()->role != 'pegawai') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $formulir = Formulir::findOrFail($id);
        $bread = "Detail Formulir";
        return view('formulir.show', compact('formulir', 'bread'));
    }

    public function edit($id)
    {
        if (Auth::user()->role == 'kepala' || Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $formulir = Formulir::findOrFail($id);
        $bread = "Edit Formulir";
        return view('formulir.edit', compact('formulir', 'bread'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'file_formulir' => 'nullable|file|mimes:pdf|max:2048',
            'tanggal' => 'required|date',
        ]);

        $formulir = Formulir::findOrFail($id);
        $formulir->judul = $request->judul;
        $formulir->tanggal = $request->tanggal;

        if ($request->hasFile('file_formulir')) {
            // Hapus file lama jika ada
            if ($formulir->file_formulir) {
                $oldFilePath = public_path($formulir->file_formulir);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Buat direktori jika belum ada
            $directory = public_path('berkas/formulir');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $file = $request->file('file_formulir');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Pindahkan file ke direktori public
            $file->move($directory, $fileName);

            // Simpan path relatif ke database
            $filePath = 'berkas/formulir/' . $fileName;
            $formulir->file_formulir = $filePath;
        }

        $formulir->save();

        return redirect()->route('formulir.index')
            ->with('success', 'Formulir berhasil diperbarui');
    }
}
