<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyCutiUsersSeeder extends Seeder
{
    public function run()
    {
        $jabatanKepalaBalaiId = \App\Jabatan::firstOrCreate(['nama' => 'Kepala Balai'])->id;
        $jabatanPelayananTeknikId = \App\Jabatan::firstOrCreate(['nama' => 'Sub Koordinator Substansi Pelayanan Teknik'])->id;
        $jabatanKasubbagTUId = \App\Jabatan::firstOrCreate(['nama' => 'Kepala Sub Bagian Tata Usaha'])->id;
        $jabatanPenyiapanSampelId = \App\Jabatan::firstOrCreate(['nama' => 'Sub Koordinator Substansi Penyiapan Sampel'])->id;
        $jabatanStafId = \App\Jabatan::firstOrCreate(['nama' => 'Staf'])->id;

        $password = Hash::make('password123');

        \App\User::updateOrCreate(
            ['email' => 'dinar.level1@dummy.test'],
            [
                'name' => 'drh. Dinar Hadi Wahyu Hartawan, M.Sc',
                'password' => $password,
                'jabatan_id' => $jabatanKepalaBalaiId,
                'role' => 'kepala',
                'nip' => '1000000001',
            ]
        );

        \App\User::updateOrCreate(
            ['email' => 'diyan.level2@dummy.test'],
            [
                'name' => 'drh. Diyan Cahyaningsari, M.Si',
                'password' => $password,
                'jabatan_id' => $jabatanPelayananTeknikId,
                'role' => 'verifikator',
                'nip' => '1000000002',
            ]
        );

        \App\User::updateOrCreate(
            ['email' => 'wiwit.level2@dummy.test'],
            [
                'name' => 'drh. Wiwit Subiyanti',
                'password' => $password,
                'jabatan_id' => $jabatanKasubbagTUId,
                'role' => 'verifikator',
                'nip' => '1000000003',
            ]
        );

        \App\User::updateOrCreate(
            ['email' => 'anik.level2@dummy.test'],
            [
                'name' => 'drh. Rr. Anik Winanningrum',
                'password' => $password,
                'jabatan_id' => $jabatanPenyiapanSampelId,
                'role' => 'verifikator',
                'nip' => '1000000004',
            ]
        );

        \App\User::updateOrCreate(
            ['email' => 'pegawai.level3@dummy.test'],
            [
                'name' => 'pegawaidummy',
                'password' => $password,
                'jabatan_id' => $jabatanStafId,
                'role' => 'pegawai',
                'nip' => '1000000005',
            ]
        );
    }
}
