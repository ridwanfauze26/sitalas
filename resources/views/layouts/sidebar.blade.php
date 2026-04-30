<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      @if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'verifikator' || Auth::user()->role  == 'pegawai' )
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
          <i class="mdi mdi-home menu-icon"></i>
          <span class="menu-title">Beranda</span>
        </a>
      </li>
      @endif
      @if(Auth::user()->role == 'admin')
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('jabatan.*') || request()->routeIs('klasifikasi-surat.*') || request()->routeIs('unit-bagian.*') ? 'active' : '' }}" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
          <i class="mdi mdi-database menu-icon"></i>
          <span class="menu-title">Data Master</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="ui-basic">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link {{ request()->routeIs('jabatan.*') ? 'active' : '' }}" href="{{ route('jabatan.index') }}">Jabatan</a></li>
            <li class="nav-item">
               <a class="nav-link {{ request()->routeIs('klasifikasi-surat.*') ? 'active' : '' }}" href="{{ route('klasifikasi-surat.index') }}">Klasifikasi Surat</a>
            </li>
            <li class="nav-item">
               <a class="nav-link {{ request()->routeIs('unit-bagian.*') ? 'active' : '' }}" href="{{ route('unit-bagian.index') }}">Unit Bagian</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('surat-masuk.*') ? 'active' : '' }}" href="{{ route('surat-masuk.index') }}">
          <i class="mdi mdi-package-down menu-icon"></i>
          <span class="menu-title">Surat Masuk</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('surat-keluar.*') ? 'active' : '' }}" href="{{ route('surat-keluar.index') }}">
          <i class="mdi mdi-package-up menu-icon"></i>
          <span class="menu-title">Surat Keluar</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cuti.*') ? 'active' : '' }}" href="{{ Auth::user()->role == 'admin' ? route('cuti.admin.index') : route('cuti.index') }}">
          <i class="mdi mdi-calendar-check menu-icon"></i>
          <span class="menu-title">Cuti</span>
        </a>
      </li>
      @if(Auth::user()->role == 'admin' || Auth::user()->isCutiApprover())
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('cuti.persetujuan.*') ? 'active' : '' }}" href="{{ route('cuti.persetujuan.index') }}">
          <i class="mdi mdi-check-decagram menu-icon"></i>
          <span class="menu-title">Persetujuan Cuti</span>
        </a>
      </li>
      @endif
      @if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'verifikator' || Auth::user()->role == 'pegawai')
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#dokumen" aria-expanded="false" aria-controls="dokumen">
            <i class="mdi mdi-file-document menu-icon"></i>
            <span class="menu-title">Dokumen</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="dokumen">
            <ul class="nav flex-column sub-menu">
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'verifikator' || Auth::user()->role == 'pegawai')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sop.index') }}">SOP</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('formulir.index') }}">Formulir</a>
                </li>
                @endif
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'kepala' || Auth::user()->role == 'pegawai' || Auth::user()->role == 'verifikator')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('surat-keputusan.*') ? 'active' : '' }}" href="{{ route('surat-keputusan.index') }}">
                        <span class="menu-title">Surat Keputusan</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
      </li>
      @endif
      @if(Auth::user()->role == 'admin')
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}" href="{{ route('pengguna.index') }}">
          <i class="mdi mdi-account menu-icon"></i>
          <span class="menu-title">Manajemen Pengguna</span>
        </a>
      </li>
      @endif
      @if(Auth::user()->role != 'kepala')
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('disposisi') ? 'active' : '' }}" href="{{ route('disposisi') }}">
          <i class="mdi mdi-human-greeting menu-icon"></i>
          <span class="menu-title">Disposisi</span>
        </a>
      </li>
      @endif
      @if(Auth::user()->role == 'admin')
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}" href="{{ route('report') }}">
          <i class="mdi mdi-printer menu-icon"></i>
          <span class="menu-title">Report</span>
        </a>
      </li>
      @endif
    </ul>
</nav>

@push('js')
<script>
function lihatSOP() {
    var bulan = document.getElementById('bulan-sop').value;
    var tahun = document.getElementById('tahun-sop').value;

    if(!bulan || !tahun) {
        alert('Silakan pilih bulan dan tahun terlebih dahulu');
        return;
    }

    // Ganti dengan route yang sesuai untuk halaman SOP
    window.location.href = `/sop/${bulan}/${tahun}`;
}
</script>
@endpush
