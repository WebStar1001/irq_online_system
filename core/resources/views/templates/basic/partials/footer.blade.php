@php
    $rnm = Request::route()->getName();
    $sp = explode('.',$rnm);

    $arr= ['user','express','invoice']
@endphp

@if(!in_array($sp[0],$arr))
<!--Footer Area-->
<footer class="footer-area section-padding-2 blue-bg wave-animation">
    <div class="container">
        <div class="row mb-40">
            <div class="col-md-4">
                <div class="footer-widget">
                    <a href=""><img src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}" alt=""></a>
                    <p>{{__($shortAbout->value->web_footer)}}</p>
                    <div class="social">
                        @foreach($socials as $data)
                        <a href="{{$data->value->url}}" target="_blank" class="cl-facebook" title="{{$data->value->title}}">{!! $data->value->icon !!}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-8">

                <div class="row">
                    <div class="col-md-4 offset-md-1">
                        <div class="footer-widget footer-nav">
                            <h3>@lang('Regular links')</h3>
                            <ul>
                                <li><a href="{{route('home')}}">@lang('Home')</a></li>
                                <li><a href="{{route('home.about')}}">@lang('About')</a></li>
                                <li><a href="{{route('home.announce')}}">@lang('Announcement')</a></li>
                                <li><a href="{{route('home.faq')}}">@lang('Faqs')</a></li>
                                <li><a href="{{route('home.contact')}}">@lang('Contact Us')</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="footer-widget footer-nav">
                            <h3>@lang('Essentials')</h3>
                            <ul>
                                <li><a href="{{route('documentation')}}">@lang('API Documentation')</a></li>
                                @foreach($company_policy as $policy)
                                <li><a href="{{route('home.policy',[$policy, str_slug($policy->value->title)])}}">{{__($policy->value->title)}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="footer-widget">
                            <div class="m-app">
                                <a href=""><i class="fab fa-apple"></i>@lang('App Store')</a>
                                <a href=""><i class="fa fa-play"></i>@lang('Google Play')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-xl-12 centered cl-white copyright mt-50">
                <p class="mb-0">@lang('Copyright')  &copy;  {{date('Y')}} - {{__($general->sitename)}} . @lang('All Rights Reserved.')</p>
            </div>
        </div>

    </div>
</footer><!--/Footer Area-->

@php
    if($plugins[1]->status == 1){
        $appKeyCode = $plugins[1]->shortcode->app_key->value;
        $twakTo = str_replace("{{app_key}}",$appKeyCode,$plugins[1]->script);
        echo $twakTo;
    }
@endphp




@else

<!--Footer Area-->
<footer class="footer-area py-4 blue-bg wave-animation">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 centered cl-white copyright ">
                <p class="mb-0">@lang('Copyright') &copy; {{date('Y')}} - {{__($general->sitename)}} . @lang('All Rights Reserved.')</p>
            </div>
        </div>
    </div>
</footer><!--/Footer Area-->
@endif
