<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{get_image(config('constants.logoIcon.path') .'/favicon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" type="image/png" href="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"/>
    <title>{{ $general->sitename }} | {{($page_title) ? $page_title : '' }} </title>
    @include('partials.seo')
    <link href="{{asset('assets/template/basic/css/bootstrap.min.css')}}" rel="stylesheet">
    @yield('import-css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"/>
    <link href="{{asset('assets/template/basic/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/nice-select.css')}}" rel="stylesheet">
    @include('partials.notify-css')
    <link rel="stylesheet" href="{{asset('assets/template/basic/css/style.php')}}?color={{ $general->bclr}}">
    @yield('style')
</head>
<body>
<!--Header Area-->
<header class="header-area">
    <nav class="navbar navbar-expand-lg main-menu">
        <div class="container-fluid">


            <a class="navbar-brand" href="{{route('home')}}"><img src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}" class="d-inline-block align-top" alt=""></a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="menu-toggle"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav m-auto">
                    <li class="nav-item @if(Request::routeIs('home')) active @endif"><a class="nav-link" href="{{route('home')}}">@lang('Home')</a></li>

                    <li class="nav-item @if(Request::routeIs('home.about')) active @endif"><a href="{{route('home.about')}}" class="nav-link">@lang("About")</a></li>
                    <li class="nav-item @if(Request::routeIs('home.announce')) active @endif"><a href="{{route('home.announce')}}" class="nav-link">@lang("Announcement")</a></li>


                    @if(count($menus) > 0)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('Menu')</a>
                        <ul class="dropdown-menu">
                            @foreach($menus as $k=>$data)
                            <li><a class="dropdown-item" href="{{route('home.menu',[$data->id, str_slug($data->value->title)])}}">{{$data->value->title}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @endif

                    <li class="nav-item @if(Request::routeIs('home.faq')) active @endif"><a href="{{route('home.faq')}}" class="nav-link "  >@lang("Faqs")</a></li>
                    <li class="nav-item @if(Request::routeIs('home.contact')) active @endif"><a href="{{route('home.contact')}}" class="nav-link ">@lang("Contact Us")</a></li>



                </ul>



                @if($general->language_status == 1)
                <select class="custom-select-2 sources" id="langSel" placeholder="En">
{{--                    <option value="en"><i class="flag-icon flag-icon-es"> </i>@lang('En')</option>--}}
                    @foreach($lan as $item)
                        <option value="{{$item->code}}" @if(session('lang') === $item->code) selected  @endif ><i class="flag-icon flag-icon-es"></i>{{ __($item->code) }}</option>
                    @endforeach
                </select>
                @endif



                <div class="header-btn justify-content-end">
                    @auth
                        <a href="{{route('user.home')}}" class="bttn-small btn-emt">@lang('Dashboard')</a>
                    @endauth
                    @guest
                        <a href="{{route('user.login')}}" class="bttn-small btn-emt">@lang('Account')</a>
                    @endguest

                </div>
            </div>
        </div>
    </nav>
</header><!--/Header Area-->


@yield('content')

@include('templates.basic.partials.footer')


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

<script>
    $(document).on("click", ".custom-option", function() {
        window.location.href = "{{url('/')}}/change-lang/"+$(this).data('value') ;

    });
    $(".custom-select-2").each(function () {
        var classes = $(this).attr("class"),
            selected = $(this).find(':selected').text();

        var template = '<div class="' + classes + '">';
        template += '<span class="custom-select-trigger">' + selected + '</span>';
        template += '<div class="custom-options">';
        $(this).find("option").each(function () {
            template += '<span class="custom-option ' + $(this).attr("class") + '" data-value="' + $(this).attr("value") + '">' + $(this).html() + '</span>';
        });
        template += '</div></div>';

        $(this).wrap('<div class="custom-select-wrapper"></div>');
        $(this).hide();
        $(this).after(template);
    });



</script>
<script src="{{asset('assets/template/basic/js/script.js')}}"></script>
@yield('script')






</body>
</html>
