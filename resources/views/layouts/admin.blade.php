<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    <link
    rel="icon"
    href="assets/img/kaiadmin/favicon.ico"
    type="image/x-icon"
  />

    <script src="{{ asset('admin_assets/assets/js/plugin/webfont/webfont.min.js')}}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["{{ asset('admin_assets/assets/css/fonts.min.css')}}"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/plugins.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/kaiadmin.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/demo.css')}}" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css') <!-- Page-specific CSS -->
</head>

    <body>
        <div class="wrapper">
            @include('layouts.partials.admin_sidebar')

            <div class="main-panel">
                @include('layouts.partials.admin_header')

                @yield('content')

                @include('layouts.partials.admin_footer')
            </div>

        </div>

        <!--   Core JS Files   -->
    <script src="{{ asset('admin_assets/assets/js/core/jquery-3.7.1.min.js')}}"></script>
    <script src="{{ asset('admin_assets/assets/js/core/popper.min.js')}}"></script>
    <script src="{{ asset('admin_assets/assets/js/core/bootstrap.min.js')}}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('admin_assets/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js')}}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('admin_assets/assets/js/plugin/chart.js/chart.min.js')}}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('admin_assets/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js')}}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('admin_assets/assets/js/plugin/chart-circle/circles.min.js')}}"></script>

    <!-- Datatables -->
    <script src="{{ asset('admin_assets/assets/js/plugin/datatables/datatables.min.js')}}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('admin_assets/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('admin_assets/assets/js/plugin/jsvectormap/jsvectormap.min.js')}}"></script>
    <script src="{{ asset('admin_assets/assets/js/plugin/jsvectormap/world.js')}}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('admin_assets/assets/js/plugin/sweetalert/sweetalert.min.js')}}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('admin_assets/assets/js/kaiadmin.min.js')}}"></script>


    <script src="{{ asset('admin_assets/assets/js/setting-demo.js')}}"></script>


    @yield('js')
    </body>
</html>
