<table>
    <tbody>
        <tr>
            <td>Tahun </td>
            <td>: {{$tahun}} </td>
        </tr>
        <tr>
            <td>Bulan </td>
            <td>: {{$bulan}} </td>
        </tr>
        <tr>
            <td>Klasifikasi </td>
            <td>: {{$klasifikasi}} </td>
        </tr>
        <tr>
            <td>Jumlah Surat </td>
            <td>: {{$jmlData}} </td>
        </tr>
       
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th>No Urut</th>
        <th>No Agenda</th>
        <th>No Surat</th>
        <th>Kode Klasfikasi</th>
        <th>Asal Surat</th>
        <th>Uraian Informasi Arsip</th>
        <th>Tanggal Surat</th>
        <th>Tanggal Terima Surat</th>
        <th>Jumlah</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @foreach($suratmasuk as $s)
        <tr>
            <td>{{ $nomor++ }}</td>
            <td>{{ $s->nomor_agenda }}</td>
            <td>{{ $s->nomor_surat }}</td>
            <td>{{ $s->kode_surat }}</td>
            <td>{{ $s->pengirim }}</td>
            <td>{{ $s->isi_singkat }}</td>
            <td>{{ $s->tanggal }}</td>
            <td>{{ $s->tanggal_terima }}</td>
            <td>1</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>

