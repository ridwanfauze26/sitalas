<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gate;

class UnitBagianController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
            if(Gate::allows('admin')) return $next($request);
            abort(403, 'Anda tidak memiliki hak akses untuk mengakses halaman ini');
        });
    }

    public function index()
    {
        $unitBagian = \App\UnitBagian::orderBy('id')->get();
        return view('unitbagian.index', compact('unitBagian'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        \App\UnitBagian::insert($request->except('_token'));
        return back()->with('success', 'Data unit bagian berhasil ditambahkan');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $unitBagian = \App\UnitBagian::findOrFail($id);
        return view('unitbagian.edit', compact('unitBagian'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        \App\UnitBagian::findOrFail($id);
        \App\UnitBagian::where('id', $id)->update($request->except(['_token', '_method']));
        \App\User::where('unit_bagian_id', $id)->update(['unit_bagian_nama' => $request->nama]);
        return back()->with('success', 'Data unit bagian berhasil diubah');
    }

    public function destroy($id)
    {
        \App\UnitBagian::findOrFail($id)->delete();
        \App\User::where('unit_bagian_id', $id)->update([
            'unit_bagian_id' => null,
            'unit_bagian_nama' => null
        ]);
        return back()->with('success', 'Data unit bagian berhasil dihapus');
    }
}
