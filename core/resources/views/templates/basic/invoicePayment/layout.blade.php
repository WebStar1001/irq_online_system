<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{get_image(config('constants.logoIcon.path') .'/favicon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" type="image/png" href="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"/>
    <title>{{ $general->sitename($page_title ?? '') }} @yield('title')</title>
    @include('partials.seo')
    <!-- Bootstrap -->
    <link href="{{asset('assets/template/basic/css/bootstrap.min.css')}}" rel="stylesheet">
    @yield('import-css')
    <link href="{{asset('assets/template/basic/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/nice-select.css')}}" rel="stylesheet">
    @include('partials.notify-css')

    <link href="{{asset('assets/template/basic/css/invoice.css')}}" rel="stylesheet">
    <!-- Main css -->
    <link href="{{asset('assets/template/basic/css/main.css')}}" rel="stylesheet">

    @yield('style')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>



@yield('content')


@include(activeTemplate().'partials.footer')

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="{{asset('assets/template/basic/js/jquery-3.2.1.min.js')}}"></script>
<script src="{{asset('assets/template/basic/js/jquery-migrate.js')}}"></script>

<script src="{{asset('assets/template/basic/js/popper.js')}}"></script>
<script src="{{asset('assets/template/basic/js/bootstrap.min.js')}}"></script>
@yield('import-js')

<script src="{{asset('assets/template/basic/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('assets/template/basic/js/parallax.min.js')}}"></script>

<script src="{{asset('assets/template/basic/js/waypoints.min.js')}}"></script>
<script src="{{asset('assets/template/basic/js/jquery.counterup.min.js')}}"></script>
<script src="{{asset('assets/template/basic/js/scrollUp.min.js')}}"></script>

<script src="{{asset('assets/template/basic/js/particles.min.js')}}"></script>
<script src="{{asset('assets/template/basic/js/particle-app.js')}}"></script>
<script src="{{asset('assets/template/basic/js/jquery.nice-select.min.js')}}"></script>

@include('partials.notify-js')
<script src="{{asset('assets/template/basic/js/script.js')}}"></script>
@yield('script')
</body>
</html>
