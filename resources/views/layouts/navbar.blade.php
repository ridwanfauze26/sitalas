<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

    <div class="navbar-brand-wrapper d-flex justify-content-center">

      <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">  

        <div class="navbar-brand brand-logo">
          <img src="{{ asset('admin/images/BPMSPH-huge.png') }}" alt="logo" class="modern-logo"/>
        </div>

        <div class="navbar-brand brand-logo-mini">
          <img src="{{ asset('admin/images/bpmsph-mini.svg') }}" alt="logo" class="modern-logo-mini"/>
        </div>

      </div>  

    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

      <ul class="navbar-nav navbar-nav-right">

        {{-- <li class="nav-item dropdown mr-1">

          <a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center" id="messageDropdown" href="#" data-toggle="dropdown">

            <i class="mdi mdi-message-text mx-0"></i>

            <span class="count"></span>

          </a>

          <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="messageDropdown">

            <p class="mb-0 font-weight-normal float-left dropdown-header">Messages</p>

            <a class="dropdown-item">

              <div class="item-thumbnail">

                  <img src="{{ asset('admin/images/faces/face4.jpg') }}" alt="image" class="profile-pic">

              </div>

              <div class="item-content flex-grow">

                <h6 class="ellipsis font-weight-normal">David Grey

                </h6>

                <p class="font-weight-light small-text text-muted mb-0">

                  The meeting is cancelled

                </p>

              </div>

            </a>

            <a class="dropdown-item">

              <div class="item-thumbnail">

                  <img src="{{ asset('admin/images/faces/face2.jpg') }}" alt="image" class="profile-pic">

              </div>

              <div class="item-content flex-grow">

                <h6 class="ellipsis font-weight-normal">Tim Cook

                </h6>

                <p class="font-weight-light small-text text-muted mb-0">

                  New product launch

                </p>

              </div>

            </a>

            <a class="dropdown-item">

              <div class="item-thumbnail">

                  <img src="{{ asset('admin/images/faces/face3.jpg') }}" alt="image" class="profile-pic">

              </div>

              <div class="item-content flex-grow">

                <h6 class="ellipsis font-weight-normal"> Johnson

                </h6>

                <p class="font-weight-light small-text text-muted mb-0">

                  Upcoming board meeting

                </p>

              </div>

            </a>

          </div>

        </li> --}}

        <li class="nav-item nav-profile dropdown">

          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">

            <span class="mdi mdi-account menu-icon"></span>

            <span class="nav-profile-name">{{ Auth::user()->name }}</span>

          </a>

          <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

            <a class="dropdown-item" href="{{ route('pengguna.edit', Auth::user()->id) }}">

              <i class="mdi mdi-settings text-primary"></i>

              Pengaturan Pengguna

            </a>

            <a class="dropdown-item" href="{{ route('telegram.connect') }}">

              <i class="mdi mdi-telegram text-primary"></i>

              Hubungkan Telegram

            </a>

            <a class="dropdown-item" href="{{ route('logout') }}"

            onclick="event.preventDefault();document.getElementById('logout-form').submit();">

              <i class="mdi mdi-logout text-primary"></i>

              Keluar

            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">

              @csrf

          </form>

          </div>

        </li>

      </ul>

      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">

        <span class="mdi mdi-menu"></span>

      </button>

    </div>

  </nav>