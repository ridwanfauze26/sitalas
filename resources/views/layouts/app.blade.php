<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">



<head>

  <!-- Required meta tags -->

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- plugins:css -->

  <link rel="stylesheet" href="{{ asset('admin/vendors/mdi/css/materialdesignicons.min.css') }}">

  <link rel="stylesheet" href="{{ asset('admin/vendors/base/vendor.bundle.base.css') }}">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- endinject -->

  <!-- plugin css for this page -->

  <link rel="stylesheet" href="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">

  <!-- End plugin css for this page -->

  <!-- inject:css -->

  <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">

  <!-- endinject -->

  <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
  <style>
    /* CSS Master Fix untuk Tampilan Kalender Flatpickr yang Rapi & Elegan */
    .flatpickr-calendar {
      box-sizing: border-box !important;
      padding: 20px !important;
      background: #ffffff !important;
      border: 1px solid #f3f4f6 !important;
      border-radius: 24px !important;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
      width: 330px !important;
      font-family: inherit !important;
    }
    .flatpickr-calendar *,
    .flatpickr-calendar *:before,
    .flatpickr-calendar *:after {
      box-sizing: border-box !important;
      font-family: inherit !important;
    }
    .flatpickr-months {
      background: transparent !important;
      border: none !important;
      padding: 0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      position: relative !important;
      margin-bottom: 20px !important;
      height: 50px !important;
    }
    .flatpickr-months .flatpickr-month {
      color: #1f2937 !important;
      fill: #1f2937 !important;
      height: 100% !important;
      flex: 1 !important;
      position: relative !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 0 !important;
      margin: 0 !important;
    }
    .flatpickr-current-month {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: auto !important;
      position: relative !important;
      padding: 0 !important;
      height: 100% !important;
      color: #1f2937 !important;
      left: 0 !important;
      text-align: center !important;
      font-size: 16px !important;
      font-weight: 700 !important;
      gap: 6px !important;
    }
    .flatpickr-current-month .flatpickr-monthDropdown-months {
      font-size: 16px !important;
      font-weight: 700 !important;
      color: #1f2937 !important;
      background: transparent !important;
      border: none !important;
      padding: 0 !important;
      padding-right: 0 !important;
      margin: 0 !important;
      cursor: default !important;
      -webkit-appearance: none !important;
      -moz-appearance: none !important;
      appearance: none !important;
      pointer-events: none !important;
      width: auto !important;
      display: inline-block !important;
    }
    .flatpickr-current-month .flatpickr-monthDropdown-months:focus {
      outline: none !important;
    }
    /* Squeeze the select element to the exact width of the active option text */
    .flatpickr-current-month .flatpickr-monthDropdown-months option:not(:checked) {
      display: none !important;
    }
    .flatpickr-current-month .flatpickr-monthDropdown-months::-ms-expand {
      display: none !important;
    }
    .flatpickr-current-month .numInputWrapper {
      position: relative !important;
      display: inline-flex !important;
      align-items: center !important;
      width: auto !important;
      height: auto !important;
      padding: 0 !important;
      margin: 0 !important;
    }
    .flatpickr-current-month .numInputWrapper input.cur-year {
      font-size: 16px !important;
      font-weight: 700 !important;
      color: #1f2937 !important;
      background: transparent !important;
      border: none !important;
      padding: 0 !important;
      margin: 0 !important;
      text-align: left !important;
      width: 38px !important;
      pointer-events: none !important;
    }
    /* Hide HTML5 Up/Down arrows in input number */
    .numInputWrapper input.cur-year::-webkit-outer-spin-button,
    .numInputWrapper input.cur-year::-webkit-inner-spin-button {
      -webkit-appearance: none !important;
      margin: 0 !important;
    }
    .numInputWrapper input.cur-year {
      -moz-appearance: textfield !important;
    }
    .numInputWrapper span.arrowUp,
    .numInputWrapper span.arrowDown {
      display: none !important;
    }
    .flatpickr-prev-month,
    .flatpickr-next-month {
      position: absolute !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      width: 36px !important;
      height: 36px !important;
      border: 1px solid #e5e7eb !important;
      border-radius: 50% !important;
      background: #ffffff !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      cursor: pointer !important;
      z-index: 10 !important;
      padding: 0 !important;
      margin: 0 !important;
      transition: all 0.2s ease !important;
    }
    .flatpickr-prev-month:hover,
    .flatpickr-next-month:hover {
      background: #f9fafb !important;
      border-color: #d1d5db !important;
    }
    .flatpickr-prev-month {
      left: 0px !important;
      right: auto !important;
    }
    .flatpickr-next-month {
      right: 0px !important;
      left: auto !important;
    }
    .flatpickr-prev-month svg,
    .flatpickr-next-month svg {
      width: 10px !important;
      height: 10px !important;
      fill: #4b5563 !important;
      display: block !important;
      margin: auto !important;
    }
    .flatpickr-prev-month:hover svg,
    .flatpickr-next-month:hover svg {
      fill: #1f2937 !important;
    }
    
    /* Weekdays */
    .flatpickr-weekdays {
      display: flex !important;
      width: 290px !important;
      margin-bottom: 12px !important;
      border-bottom: 1px solid #f3f4f6 !important;
      padding-bottom: 10px !important;
    }
    .flatpickr-weekdaycontainer {
      display: flex !important;
      flex: 1 !important;
      width: 100% !important;
    }
    .flatpickr-weekday {
      display: inline-block !important;
      flex: 0 0 14.2857% !important;
      width: 14.2857% !important;
      text-align: center !important;
      font-weight: 600 !important;
      color: #9ca3af !important;
      font-size: 12px !important;
      text-transform: uppercase !important;
    }
    
    /* Days grid */
    .flatpickr-days {
      width: 290px !important;
    }
    .dayContainer {
      width: 290px !important;
      min-width: 290px !important;
      max-width: 290px !important;
      display: flex !important;
      flex-wrap: wrap !important;
      justify-content: flex-start !important;
    }
    .flatpickr-day {
      flex: 0 0 14.2857% !important;
      max-width: 41px !important;
      height: 41px !important;
      line-height: 41px !important;
      display: inline-block !important;
      text-align: center !important;
      border-radius: 50% !important;
      margin: 0 !important;
      box-sizing: border-box !important;
      cursor: pointer !important;
      border: none !important;
      color: #1f2937 !important;
      font-weight: 600 !important;
      background: transparent !important;
      transition: all 0.15s ease !important;
    }
    .flatpickr-day:hover {
      background: #f3f4f6 !important;
      color: #1f2937 !important;
    }
    .flatpickr-day.today {
      color: #3B82F6 !important;
      font-weight: bold !important;
      border: 1px solid #3B82F6 !important;
    }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
      background: #3B82F6 !important;
      color: #ffffff !important;
      font-weight: bold !important;
      border: none !important;
    }
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
      color: #d1d5db !important;
      font-weight: 400 !important;
    }
    
    /* Custom Footer */
    .flatpickr-custom-footer {
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      padding: 10px 12px !important;
      background: #f9fafb !important;
      border-radius: 12px !important;
      margin-top: 15px !important;
      width: 290px !important;
      border: none !important;
    }
    .flatpickr-custom-footer .today-label {
      font-weight: 700 !important;
      font-size: 11px !important;
      color: #4b5563 !important;
      letter-spacing: 0.5px !important;
      text-transform: uppercase !important;
    }
    .flatpickr-custom-footer .divider {
      display: none !important;
    }
    .flatpickr-custom-footer .time-container {
      display: flex !important;
      flex-direction: column !important;
      align-items: flex-end !important;
    }
    .flatpickr-custom-footer .time-clock {
      font-weight: 700 !important;
      font-size: 12px !important;
      color: #1f2937 !important;
      line-height: 1 !important;
    }
    .flatpickr-custom-footer .time-date {
      font-size: 9px !important;
      color: #9ca3af !important;
      line-height: 1 !important;
      margin-top: 2px !important;
    }
  </style>
  @stack('css')

</head>

<body>

  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->

    @include('layouts.navbar')

    <!-- partial -->

    <div class="container-fluid page-body-wrapper">

      <!-- partial:partials/_sidebar.html -->

    @include('layouts.sidebar')

        <!-- partial -->

        <div class="main-panel">

          <div class="content-wrapper">

            <div class="row">

              <div class="col-md-12 grid-margin">

                <div class="d-flex justify-content-between flex-wrap">

                  <div class="d-flex align-items-end flex-wrap">

                    <div class="mr-md-3 mr-xl-5">

                      <h2>@yield('judul')</h2>

                    </div>

                  </div>

                  {{-- <div class="d-flex justify-content-between align-items-end flex-wrap">

                    <button class="btn btn-primary"><i class="mdi mdi-plus"></i>&nbsp;Generate report</button>

                  </div> --}}

                </div>

              </div>

            </div>

            <main>

            @yield('content')

            </main>

        </div>

        <!-- content-wrapper ends -->

        <footer class="footer">

          <div class="d-sm-flex justify-content-center justify-content-sm-between">

            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © {{ date('Y') }} <a href="#" target="_blank">Layanan Sekretariat</a>. All rights reserved.</span>

            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="mdi mdi-heart text-danger"></i></span>

          </div>

        </footer>

        <!-- partial -->

      </div>

      <!-- main-panel ends -->

    </div>

    <!-- page-body-wrapper ends -->

  </div>

  <!-- container-scroller -->

  <script>

    @if(session()->has('success'))

    toastr["success"]('{{ session('success') }}')

    @elseif(session()->has('error'))

    toastr["error"]('{{ session('error') }}')

    @endif

    @if($errors->any())

    toastr["error"]("{!! implode('', $errors->all('<div>:message</div>')) !!}")

    @endif



    toastr.options = {

      "closeButton": true,

      "debug": false,

      "newestOnTop": false,

      "progressBar": true,

      "positionClass": "toast-top-right",

      "preventDuplicates": false,

      "onclick": null,

      "showDuration": "300",

      "hideDuration": "1000",

      "timeOut": "5000",

      "extendedTimeOut": "1000",

      "showEasing": "swing",

      "hideEasing": "linear",

      "showMethod": "fadeIn",

      "hideMethod": "fadeOut"

    }

  </script>

  <!-- plugins:js -->

  <script src="{{ asset('admin/vendors/base/vendor.bundle.base.js') }}"></script>

  <!-- endinject -->

  <!-- Plugin js for this page-->

  <script src="{{ asset('admin/vendors/chart.js/Chart.min.js') }}"></script>

  <script src="{{ asset('admin/vendors/datatables.net/jquery.dataTables.js') }}"></script>

  <script src="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>

  <!-- End plugin js for this page-->

  <!-- inject:js -->

  <script src="{{ asset('admin/js/off-canvas.js') }}"></script>

  <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>

  <script src="{{ asset('admin/js/template.js') }}"></script>

  <!-- endinject -->

  <!-- Custom js for this page-->

  <script src="{{ asset('admin/js/dashboard.js') }}"></script>

  <script src="{{ asset('admin/js/data-table.js') }}"></script>

  <script src="{{ asset('admin/js/jquery.dataTables.js') }}"></script>

  <script src="{{ asset('admin/js/dataTables.bootstrap4.js') }}"></script>

  <!-- End custom js for this page-->

  <script src="{{ asset('admin/js/jquery.cookie.js') }}" type="text/javascript"></script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

  <script>

    $(document).ready(function() {

        $('.select2').select2();

    });

  </script>

  @stack('js')

</body>



</html>