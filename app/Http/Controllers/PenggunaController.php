<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Auth;

class PenggunaController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role  != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $pengguna = \App\User::orderBy('name')->get();
        return view('pengguna.index', compact('pengguna'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->role  != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }
        $jabatan = \App\Jabatan::orderBy('nama','desc')->get();
        $unitBagian = \App\UnitBagian::orderBy('nama','asc')->get();
        return view('pengguna.insert', compact('jabatan', 'unitBagian'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->role  != 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:11',
            'jabatan_id' => 'required|integer',
            'unit_bagian_id' => 'nullable|integer',
            'email' => 'required|string|email|max:255|unique:users',
            'nip' => 'required|integer|digits_between:8,18',
            'password' => 'required|string|min:8|max:32',
        ]);

        $check = \App\Jabatan::findOrFail($request->jabatan_id);
        $unitBagian = null;
        if($request->unit_bagian_id) $unitBagian = \App\UnitBagian::findOrFail($request->unit_bagian_id);

        \App\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'nip' => $request->nip,
            'jabatan_id' => $request->jabatan_id,
            'unit_bagian_id' => $request->unit_bagian_id,
            'unit_bagian_nama' => $unitBagian ? $unitBagian->nama : null,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Data pengguna berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->role  != 'admin' && Auth::user()->id != $id) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $pengguna =  \App\User::findOrFail($id);
        
        if(\Auth::user()->role != 'admin' && $pengguna->id != \Auth::user()->id)
            abort(403, 'Anda tidak mempunyai hak akses');

        $jabatan = \App\Jabatan::orderBy('nama')->get();
        $unitBagian = \App\UnitBagian::orderBy('nama','asc')->get();
        return view('pengguna.edit', compact('jabatan', 'unitBagian', 'pengguna'));
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
        if(Auth::user()->role  != 'admin' && Auth::user()->id != $id) {
            abort(403, 'Anda tidak memiliki akses untuk mengakses halaman ini');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|integer|digits_between:8,18'
        ]);

        $user =  \App\User::findOrFail($id);
        if(\Auth::user()->role != 'admin' && $user->id != \Auth::user()->id)
            abort(403, 'Anda tidak mempunyai hak akses');

        $user->nip = $request->nip;
        $user->email = $request->email;
        if(Auth::user()->role == 'admin') {
        $request->validate([
            'role' => 'required|string|max:11',
            'jabatan_id' => 'required|integer',
            'unit_bagian_id' => 'nullable|integer'
        ]);
        $check = \App\Jabatan::findOrFail($request->jabatan_id);
        $unitBagian = null;
        if($request->unit_bagian_id) $unitBagian = \App\UnitBagian::findOrFail($request->unit_bagian_id);
        $user->role = $request->role;
        $user->jabatan_id = $request->jabatan_id;
        $user->unit_bagian_id = $request->unit_bagian_id;
        $user->unit_bagian_nama = $unitBagian ? $unitBagian->nama : null;
        }
        $user->name = $request->name;
        if($request->password) {
            $request->validate([
                'password' => 'required|string|min:8|max:32',
            ]);
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Data pengguna berhasil diubah');
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

        \App\User::findOrFail($id)->delete();
        return back()->with('success', 'Data pengguna berhasil dihapus');
    }
}
