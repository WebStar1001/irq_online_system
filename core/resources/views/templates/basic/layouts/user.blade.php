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
    <!-- Bootstrap -->
    <link href="{{asset('assets/template/basic/css/bootstrap.min.css')}}" rel="stylesheet">
    @yield('import-css')

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"/>
    <link href="{{asset('assets/template/basic/css/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/themify-icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/template/basic/css/nice-select.css')}}" rel="stylesheet">
    @include('partials.notify-css')
    <!-- Main css -->
    <link rel="stylesheet" href="{{asset('assets/template/basic/css/style.php')}}?color={{ $general->bclr}}">
    @yield('style')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>
<body>
<!--nav-->
<section class="header-area-2">
    <nav class="navbar navbar-expand-xl dashboard-menu">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('home')}}"><img src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}" class="d-inline-block align-top" alt=""></a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="menu-toggle"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{isActive('user.home') ? 'active': ''}}"><a class="nav-link" href="{{route('user.home')}}">@lang('Dashboard')</a></li>
                    <li class="nav-item {{isActive('user.transaction') ? 'active': ''}}"><a class="nav-link" href="{{route('user.transaction')}}">@lang('Transaction')</a></li>

                    @if($general->mt_status == 1)
                    <li class="nav-item {{isActive('user.moneyTransfer') ? 'active': ''}} "><a class="nav-link" href="{{route('user.moneyTransfer')}}">@lang('Send')</a></li>
                    @endif

                    @if($general->exm_status == 1)
                    <li class="nav-item {{isActive('user.exchange') ? 'active': ''}}"><a class="nav-link" href="{{route('user.exchange')}}">@lang('Exchange')</a></li>
                    @endif

                    @if($general->rqm_status == 1)
                    <li class="nav-item {{isActive('user.request-money.create') ? 'active': ''}}"><a class="nav-link" href="{{route('user.request-money.create')}}">@lang('Request')</a></li>
                    @endif
                    @if($general->invoice_status == 1)
                    <li class="nav-item  {{isActive('user.invoice') ? 'active': ''}} "><a class="nav-link" href="{{route('user.invoice')}}">@lang('Invoice')</a></li>
                    @endif
                    @if($general->voucher_status == 1)
                    <li class="nav-item {{isActive('user.vouchers') ? 'active': ''}} "><a class="nav-link" href="{{route('user.vouchers')}}">@lang('Voucher')</a></li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('Deposit')</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('user.deposit')}}">@lang('Deposit Money')</a></li>
                            <li><a class="dropdown-item" href="{{route('user.depositLog')}}">@lang('Deposit Log')</a></li>
                        </ul>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('Withdraw')</a>
                        <ul class="dropdown-menu">
                            @if($general->withdraw_status == 1)
                            <li><a class="dropdown-item" href="{{route('user.withdraw.money')}}">@lang('Withdraw Money')</a></li>
                            @endif

                            <li><a class="dropdown-item" href="{{route('user.withdrawLog')}}">@lang('Withdraw Log')</a></li>
                        </ul>
                    </li>



                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Hi, {{Auth::user()->username}}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('user.edit-profile')}}">@lang('Edit Profile')</a></li>
                            <li><a class="dropdown-item" href="{{route('user.doc-ver')}}">@lang('Document Verify')</a></li>
                            <li><a class="dropdown-item" href="{{route('user.change-password')}}">@lang('Change Password')</a></li>
                            <li><a class="dropdown-item" href="{{route('user.ticket')}}">@lang('Support')</a></li>

                            <li><a class="dropdown-item" href="{{route('user.api-key')}}">@lang('API KEY')</a></li>
                            <li><a class="dropdown-item" href="{{route('user.twoFA')}}">@lang('2FA Security')</a></li>
                            <li><a class="dropdown-item" href="{{route('user.loginHistory')}}">@lang('Login History')</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="header-btn justify-content-end">
                    <a href="{{route('user.logout')}}" class="bttn-small btn-emt">@lang('Logout')</a>
                </div>
            </div>
        </div>
    </nav>
</section><!--/nav-->

@include('templates.basic.partials.user-breadcrumb')

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
<script src="{{asset('assets/template/basic/js/jquery.nice-select.min.js')}}"></script>

@include('partials.notify-js')

<script src="{{asset('assets/template/basic/js/script.js')}}"></script>
@yield('script')
</body>
</html>
