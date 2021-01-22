<!--breadcrumb area-->
<section class="breadcrumb-area blue-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 centered">
                <div class="banner-title">
                    <h2>{{__($page_title)}}</h2>
                </div>
                @if($page_title != 'Dashboard')
                <ul>
                    <li><a href="{{route('user.home')}}">@lang('Dashboard')</a></li>
                    <li>{{__($page_title)}}</li>
                </ul>
                @endif
            </div>
        </div>
    </div>
</section><!--/breadcrumb area-->

