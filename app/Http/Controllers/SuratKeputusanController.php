<?php

namespace App\Http\Controllers;

use App\Models\SuratKeputusan;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratKeputusanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Mengizinkan akses untuk 'admin', 'kepala', dan 'pegawai'
            if (!in_array(Auth::user()->role, ['admin', 'kepala', 'pegawai', 'verifikator'])) {
                abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
            }
            return $next($request);
        });

        // Middleware untuk membatasi akses ke method create, store, edit, update, dan destroy
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role != 'admin' && in_array($request->route()->getActionMethod(), ['create', 'store', 'edit', 'update', 'destroy'])) {
                abort(403, 'Anda tidak memiliki akses untuk melakukan operasi ini');
            }
            return $next($request);
        }, ['except' => ['index', 'show', 'getSuratKeputusan']]);
    }

    public function index()
    {
        return view('surat-keputusan.index');
    }

    public function getSuratKeputusan(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'draw' => $request->draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Anda harus login terlebih dahulu'
                ], 401);
            }

            $query = SuratKeputusan::with('users');

            // Jika user adalah pegawai, kepala, atau verifikator, hanya tampilkan surat keputusan yang ditujukan kepadanya
            if (in_array(Auth::user()->role, ['pegawai', 'kepala', 'verifikator'])) {
                $query->whereHas('users', function($q) {
                    $q->where('users.id', Auth::id());
                });
            }

            if ($request->filled('bulan') && $request->filled('tahun')) {
                $query->whereMonth('tanggal_surat', $request->bulan)
                    ->whereYear('tanggal_surat', $request->tahun);
            }

            $data = DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('pengguna', function($row) {
                    $pengguna = [];
                    foreach($row->users as $user) {
                        $pengguna[] = $user->name;
                    }
                    $penggunaString = implode(', ', $pengguna);
                    return strlen($penggunaString) > 50 ? substr($penggunaString, 0, 50) . '...' : $penggunaString;
                })
                ->addColumn('action', function($row) {
                    $actionBtn = '';

                    // Tampilkan tombol edit hanya untuk admin
                    if (Auth::user()->role == 'admin') {
                        $actionBtn .= '<a href="'.route('surat-keputusan.edit', $row->id).'" class="btn btn-sm text-white" title="Edit" style="background-color:#6f42c1;width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa fa-pencil"></i>
                        </a>';
                    }

                    // Tampilkan tombol view untuk semua role
                    $actionBtn .= '<a href="'.route('surat-keputusan.show', $row->id).'" class="btn btn-sm btn-success" title="Detail" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;margin-left:6px;">
                        <i class="fa fa-eye"></i>
                    </a>';

                    // Tampilkan tombol delete hanya untuk admin
                    if (Auth::user()->role == 'admin') {
                        $actionBtn .= '<form action="'.route('surat-keputusan.destroy', $row->id).'" method="POST" class="d-inline" style="margin-left:6px;">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')" style="width:38px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>';
                    }

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);

            return $data;
        } catch (\Exception $e) {
            Log::error('Error in getSuratKeputusan: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $users = User::all();
        return view('surat-keputusan.create', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nomor_surat' => 'required|unique:surat_keputusan',
                'judul_surat' => 'required',
                'tanggal_surat' => 'required|date',
                'user_id' => 'required|array|min:1',
                'user_id.*' => 'exists:users,id',
                'file' => 'required|file|mimes:pdf,doc,docx|max:5120'
            ], [
                'file.max' => 'File tidak bisa lebih dari 5MB'
            ]);

            // Simpan file terlebih dahulu
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'berkas/suratkeputusan';

            // Buat direktori jika belum ada
            $directory = public_path($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Simpan file
            $file->move($directory, $fileName);

            DB::beginTransaction();
            try {
                // Buat surat keputusan
                $suratKeputusan = SuratKeputusan::create([
                    'nomor_surat' => $request->nomor_surat,
                    'judul_surat' => $request->judul_surat,
                    'tanggal_surat' => $request->tanggal_surat,
                    'file' => $fileName
                ]);

                // Attach users
                $suratKeputusan->users()->attach($request->user_id);

                DB::commit();

                return redirect()->route('surat-keputusan.create')
                    ->with('success', 'Surat Keputusan berhasil ditambahkan');
            } catch (\Exception $e) {
                DB::rollBack();

                // Hapus file jika gagal
                if (file_exists($directory . '/' . $fileName)) {
                    unlink($directory . '/' . $fileName);
                }
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in store SuratKeputusan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $suratKeputusan = SuratKeputusan::with('users')->findOrFail($id);

            // Cek apakah user memiliki akses ke surat ini
            if (in_array(Auth::user()->role, ['pegawai', 'kepala', 'verifikator']) && !$suratKeputusan->users->contains(Auth::id())) {
                abort(403, 'Anda tidak memiliki akses untuk melihat surat keputusan ini');
            }

            return view('surat-keputusan.show', compact('suratKeputusan'));
        } catch (\Exception $e) {
            Log::error('Error pada SuratKeputusanController@show: ' . $e->getMessage());
            return redirect()->route('surat-keputusan.index')
                ->with('error', 'Terjadi kesalahan saat menampilkan detail surat keputusan');
        }
    }

    public function edit($id)
    {
        $suratKeputusan = SuratKeputusan::findOrFail($id);
        $users = User::all();
        return view('surat-keputusan.edit', compact('suratKeputusan', 'users'));
    }

    public function update(Request $request, SuratKeputusan $suratKeputusan)
    {
        try {
            $request->validate([
                'nomor_surat' => 'required|unique:surat_keputusan,nomor_surat,' . $suratKeputusan->id,
                'judul_surat' => 'required',
                'tanggal_surat' => 'required|date',
                'user_id' => 'required|array|min:1',
                'user_id.*' => 'exists:users,id',
                'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
            ], [
                'file.max' => 'File tidak bisa lebih dari 5MB'
            ]);

            $data = [
                'nomor_surat' => $request->nomor_surat,
                'judul_surat' => $request->judul_surat,
                'tanggal_surat' => $request->tanggal_surat
            ];

            $fileName = null;
            $oldFileName = null;

            // Proses file jika ada
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $oldFileName = $suratKeputusan->file;

                // Simpan file baru
                $file->move(public_path('berkas/suratkeputusan'), $fileName);
                $data['file'] = $fileName;
            }

            DB::beginTransaction();
            try {
                // Update surat keputusan
                $suratKeputusan->update($data);

                // Update relasi users
                $suratKeputusan->users()->sync($request->user_id);

                DB::commit();

                // Hapus file lama hanya jika update berhasil dan ada file baru
                if ($fileName && $oldFileName) {
                    Storage::delete('berkas/suratkeputusan/' . $oldFileName);
                }

                return redirect()->route('surat-keputusan.index')->with('success', 'Surat Keputusan berhasil diperbarui');
            } catch (\Exception $e) {
                DB::rollBack();

                // Hapus file baru jika operasi database gagal
                if ($fileName && Storage::exists('berkas/suratkeputusan/' . $fileName)) {
                    Storage::delete('berkas/suratkeputusan/' . $fileName);
                }

                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in update SuratKeputusan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $suratKeputusan = SuratKeputusan::findOrFail($id);

            // Hapus file dari direktori public
            $filePath = public_path('berkas/suratkeputusan/' . $suratKeputusan->file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Hapus relasi dan data surat keputusan
            $suratKeputusan->users()->detach();
            $suratKeputusan->delete();

            return redirect()->route('surat-keputusan.index')
                ->with('success', 'Surat Keputusan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error in destroy SuratKeputusan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
