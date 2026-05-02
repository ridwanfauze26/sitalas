<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SOPController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\SuratKeputusanController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false, 'reset' => false]);

Route::get('/cuti', 'CutiController@index')->middleware('auth')->name('cuti.index');
Route::get('/cuti/ajukan', 'CutiController@create')->middleware('auth')->name('cuti.ajukan');
Route::post('/cuti', 'CutiController@store')->middleware('auth')->name('cuti.store');
Route::get('/cuti/tahunan/saldo', 'CutiController@tahunanSaldo')->middleware('auth')->name('cuti.tahunan.saldo');

Route::get('/cuti/{id}', 'CutiController@show')->middleware('auth')->name('cuti.show');
Route::get('/cuti/{id}/{qr}/pdf', 'CutiController@pdf')->middleware('auth')->name('cuti.pdf');
Route::get('/cuti/{id}/edit', 'CutiController@edit')->middleware('auth')->name('cuti.edit');
Route::put('/cuti/{id}', 'CutiController@update')->middleware('auth')->name('cuti.update');
Route::delete('/cuti/{id}', 'CutiController@destroy')->middleware('auth')->name('cuti.destroy');

Route::get('/cuti-admin', 'CutiController@adminIndex')->middleware('auth')->name('cuti.admin.index');

Route::get('/cuti-admin/{id}', 'CutiController@adminShow')->middleware('auth')->name('cuti.admin.show');
Route::get('/cuti-admin/{id}/edit', 'CutiController@adminEdit')->middleware('auth')->name('cuti.admin.edit');
Route::put('/cuti-admin/{id}', 'CutiController@adminUpdate')->middleware('auth')->name('cuti.admin.update');
Route::delete('/cuti-admin/{id}', 'CutiController@adminDestroy')->middleware('auth')->name('cuti.admin.destroy');

Route::get('/cuti-persetujuan', 'CutiController@persetujuanIndex')->middleware('auth')->name('cuti.persetujuan.index');
Route::post('/cuti-persetujuan/{id}/approve', 'CutiController@approve')->middleware('auth')->name('cuti.persetujuan.approve');
Route::post('/cuti-persetujuan/{id}/reject', 'CutiController@reject')->middleware('auth')->name('cuti.persetujuan.reject');

Route::get('/telegram/connect', 'TelegramController@connect')->middleware('auth')->name('telegram.connect');



// Kelola Data Master Klasifikasi Surat
Route::resource('klasifikasi-surat', 'KlasifikasiSuratController')->except([
    'create', 'show'
]);

// Kelola Data Master Jabatan
Route::resource('jabatan', 'JabatanController')->except([
    'create', 'show'
]);

// Kelola Data Master Unit Bagian
Route::resource('unit-bagian', 'UnitBagianController')->except([
    'create', 'show'
]);

// Kelola Data Pengguna
Route::resource('pengguna', 'PenggunaController')->except([
    'show'
]);

// Kelola Data Surat Keluar
Route::get('/surat-keluar/getSuratKeluar', 'SuratKeluarController@getSuratKeluar')->name('surat_keluar');
Route::get('/surat-keluar/getNoSurat/{date}', 'SuratKeluarController@getNoSurat')->name('no_surat');
Route::get('/surat-keluar/export_excel', 'SuratKeluarController@export_excel')->name('export_suratkeluar_get');
Route::post('/surat-keluar/export_excel', 'SuratKeluarController@export_excel')->name('export_suratkeluar');
Route::resource('surat-keluar', 'SuratKeluarController');

// Kelola Data Surat Masuk
Route::get('/surat-masuk/getSuratMasuk', 'SuratMasukController@getSuratMasuk')->name('surat_masuk');
Route::post('/surat-masuk/export_excel', 'SuratMasukController@export_excel')->name('export_suratmasuk');
Route::post('/surat-masuk/export_excel/{excel}', 'SuratMasukController@export_excel')->name('export_suratmasuk_with_param');
Route::resource('surat-masuk', 'SuratMasukController');

Route::get('/report', 'HomeController@report')->name('report');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/sedang-cuti', 'HomeController@sedangCuti')->middleware('auth')->name('home.sedang_cuti');
Route::get('/report/getDataTable/{tbname}', 'HomeController@getDataTable')->name('tbname');
Route::get('/disposisi/getDisposisi', 'HomeController@getDisposisi')->name('get_disposisi');
Route::get('/disposisi', 'HomeController@disposisi')->name('disposisi');
Route::post('/respon/{id}', 'HomeController@respon')->name('respon');
Route::get('/tracking-disposisi/{id}/user/{user}', 'HomeController@tracking');

// Kelola Data SOP
Route::get('/sop/data', [SOPController::class, 'getData'])->name('sop.data');
Route::resource('sop', SOPController::class);
Route::delete('/sop/{id}', [SOPController::class, 'destroy'])->name('sop.destroy');
Route::get('/sop/view/{id}', 'SOPController@viewFile')->name('sop.view');
Route::get('/sop/show/{id}', [SOPController::class, 'show'])->name('sop.show');
Route::get('/sop/edit/{id}', [SOPController::class, 'edit'])->name('sop.edit');

// Kelola Data Formulir
Route::get('/formulir/data', [FormulirController::class, 'getData'])->name('formulir.data');
Route::resource('formulir', FormulirController::class);
Route::delete('/formulir/{id}', [FormulirController::class, 'destroy'])->name('formulir.destroy');
Route::get('/formulir/view/{id}', 'FormulirController@viewFile')->name('formulir.view');
Route::get('/formulir/edit/{id}', [FormulirController::class, 'edit'])->name('formulir.edit');
Route::get('/formulir/show/{id}', [FormulirController::class, 'show'])->name('formulir.show');

// Kelola Data Surat Keputusan
Route::get('/surat-keputusan/data', [SuratKeputusanController::class, 'getSuratKeputusan'])->name('surat-keputusan.data');
Route::resource('surat-keputusan', SuratKeputusanController::class);
Route::get('/surat-keputusan/view/{id}', [SuratKeputusanController::class, 'viewFile'])->name('surat-keputusan.view');
Route::get('/surat-keputusan/show/{id}', [SuratKeputusanController::class, 'show'])->name('surat-keputusan.show');
Route::get('/surat-keputusan/edit/{id}', [SuratKeputusanController::class, 'edit'])->name('surat-keputusan.edit');
