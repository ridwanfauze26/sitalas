@extends('layouts.app')
@section('judul','Laporan Surat')
@section('content')
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-12">
        <div class="card">
                 @if (Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'verifikator')
                 <form method="POST" id="form">
                    @csrf
                    <div class="row">
                            <label class="control-label text-left col-md-2 p-4">Jenis Surat</label>
                            <div class="col-md-3 my-2">
                                <select class="form-control" name="jenis_surat" id="jenis_surat" onchange="getYear()" required>
                                    <option value="">==Pilih==</option>
                                    <option value="suratmasuk">Surat Masuk</option>
                                    <option value="suratkeluar">Surat Keluar</option>
                                </select>
                            </div>
                        </div>

                    <div class="row">
                    <label class="text-left p-4 col-md-2">Tahun</label>
                        <div class="col-md-3 my-2">
                            <select class="form-control" name="tahun" id="tahun" onchange="getMonth()">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                    <label class="text-left p-4 col-md-2">Bulan</label>
                        <div class="col-md-3 my-2">
                            <select class="form-control" name="bulan" id="bulan" onchange="getKlasifikasi()">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                            <label class="control-label text-left col-md-2 p-4">Kode/Klasifikasi Surat</label>
                            <div class="col-md-9 my-4">
                                <select class="form-control custom-select select2" name="kode_surat" id="kode_surat">
                                </select>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-2 ml-3">
                        <button class="btn btn-success my-3" type="submit">
                            EXPORT EXCEL
                        </button>
                        </div>
                    </div>
                    </form>
                @endif
        </div>
    </div>
</div>
@endsection

@push('js')

<script>

function getYear() {
       var year = document.getElementById("tahun");
       var form = document.getElementById("form");
       let jenis_surat = document.getElementById("jenis_surat").value;

       if(jenis_surat=='suratmasuk'){
           form.setAttribute('action','{{route("export_suratmasuk")}}')
       }else{
            form.setAttribute('action','{{route("export_suratkeluar")}}')
       }
        let url = "{{route('tbname', ':jenis_surat')}}";
        url = url.replace(':jenis_surat',jenis_surat);
        const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
             if(xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                var temp = JSON.parse(this.responseText);
                // console.log(temp.data[2021][5].tanggal_penerimaan);

                year.innerHTML = "";
                var optionyear = document.createElement("option");
                optionyear.value ="";
                optionyear.innerHTML = "==Pilih Tahun==";
                year.add(optionyear)
                for(i=0;i<Object.keys(temp.data).length;i++){
                //   optionyear.text = Object.keys(temp.data)[i];
                    var optionyear = document.createElement("option");
                    optionyear.value = Object.keys(temp.data)[i];
                    optionyear.innerHTML = Object.keys(temp.data)[i];
                //   console.log(Object.keys(temp.data)[i]);
                    year.add(optionyear);
                }
            }
        }
        xmlhttp.open("GET", url);
        xmlhttp.send();

    }

    function getMonth(){
        var month = document.getElementById("bulan");
        var year = document.getElementById("tahun").value;
        let jenis_surat = document.getElementById("jenis_surat").value;


        let url = "{{route('tbname', ':jenis_surat')}}";
        url = url.replace(':jenis_surat',jenis_surat);
        const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
             if(xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                var temp = JSON.parse(this.responseText);


                month.innerHTML = "";
                var optionMonth = document.createElement("option");
                optionMonth.value ="";
                optionMonth.innerHTML = "==Pilih Bulan==";
                month.add(optionMonth)

                let months = Array.from(temp.data[year], ({bulan})=>bulan);

                var uniqueMonth= months.filter(onlyUnique);
                uniqueMonth.sort();
                const namaBulan = [
                                'Januari',
                                'Februari',
                                'Maret',
                                'April',
                                'Mei',
                                'Juni',
                                'Juli',
                                'Agustus',
                                'September',
                                'Oktober',
                                'November',
                                'Desember']

                console.log(uniqueMonth);
                for(i=0;i<uniqueMonth.length;i++){
                        var optionMonth = document.createElement("option");
                        optionMonth.value = uniqueMonth[i];
                        optionMonth.innerHTML = namaBulan[parseInt(uniqueMonth[i])-1];
                        month.add(optionMonth);

                }

            }
        }
        xmlhttp.open("GET", url);
        xmlhttp.send();
    }

    function getKlasifikasi()
    {
        var klasifikasi = document.getElementById("kode_surat");
        var month = document.getElementById("bulan").value;
        var year = document.getElementById("tahun").value;
        let jenis_surat = document.getElementById("jenis_surat").value;


        let url = "{{route('tbname', ':jenis_surat')}}";
        url = url.replace(':jenis_surat',jenis_surat);
        const xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
             if(xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                var temp = JSON.parse(this.responseText);

                klasifikasi.innerHTML = "";
                var optionKlasifikasi = document.createElement("option");
                optionKlasifikasi.value ="";
                optionKlasifikasi.innerHTML = "==Pilih Klasifikasi==";
                klasifikasi.add(optionKlasifikasi)
                let klasifikasiCheck ="" ;

                for(i=0;i<temp.data[year].length;i++){
                    if(month == temp.data[year][i].bulan ){
                        if(klasifikasiCheck!=temp.data[year][i].kode_surat && temp.data[year][i].kode_surat!=null){
                            var optionKlasifikasi = document.createElement("option");
                            optionKlasifikasi.value = temp.data[year][i].kode_surat;
                            optionKlasifikasi.innerHTML = temp.data[year][i].kode_surat +" - "+ temp.data[year][i].nama;
                            klasifikasi.add(optionKlasifikasi);
                            klasifikasiCheck = temp.data[year][i].kode_surat;
                        }
                    }
                }
                // console.log(month);
            }
        }
        xmlhttp.open("GET", url);
        xmlhttp.send();
    }

    function onlyUnique(value, index, self) {
         return self.indexOf(value) === index;
    }
    </script>
@endpush
