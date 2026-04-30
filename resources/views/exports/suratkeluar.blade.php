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
        <th>No Surat</th>
        <th>Klasifikasi Surat</th>
        <th>Uraian Informasi Arsip</th>
        <th>Ditujukan</th>
        <th>Tanggal Surat</th>
        <th>Jumlah</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @foreach($suratkeluar as $s)
        <tr>
            <td>{{ $nomor++ }}</td>
            <td>{{ $s->kode }}</td>
            <td>{{ $s->klasifikasi }}</td>
            <td>{{ $s->isi_singkat }}</td>
            <td>{{ $s->tujuan }}</td>
            <td>{{ $s->tanggal }}</td>
            <td>1</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>

