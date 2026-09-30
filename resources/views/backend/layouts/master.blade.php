<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> @yield('title') </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @stack('styles')
    @include('backend.layouts.partials.style')


</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">

        {{--  //header  --}}
        @include('backend.layouts.partials.header')

        @include('backend.layouts.partials.page-title')

        @include('backend.layouts.partials.sidebar')

        @yield('admin-content')

        @include('backend.layouts.partials.footer')

    </div>
    <!-- page container area end -->

    @include('backend.layouts.partials.offset')

    <!-- jquery latest version -->

    @include('backend.layouts.partials.scripts')
    @stack('scripts')

</body>

</html>
