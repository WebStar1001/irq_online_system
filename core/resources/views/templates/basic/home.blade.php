@extends(activeTemplate().'layouts.master')
@section('title','Home Content')
@section('content')

    <!--Hero Area-->
    @if(isset($homeContent))
    <section class="hero-section" style="background: url('{{asset('assets/template/basic/images/')}}/map.png');">
        <div class="hero-area">
            <div id="particles-js"></div>
            <div class="single-hero">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-11 centered">
                            <div class="hero-sub">
                                <div class="table-cell">
                                    <div class="hero-left">
                                        <h4>{{__($homeContent->value->title)}}</h4>
                                        <h1>{{__($homeContent->value->sub_title)}}</h1>
                                        <p>@php echo __($homeContent->value->details) @endphp </p>
                                        <a href="{{route('user.register')}}" class="bttn-mid btn-fill"><i class="ti-user"></i>@lang('Register')</a>
                                        <a href="{{route('user.login')}}" class="bttn-mid btn-emt"><i class="ti-key"></i>@lang('Login')</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--/Hero Area-->



    <!--Section-->
    @if(count($whychooses)>0)
    <section class="section-padding-2 gradient-overlay poly-particle">
        <div class="triangle-particle">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-7 centered">
                        <div class="section-title cl-white">
                            <h2>{{__($whychoose_caption->value->title)}}</h2>
                            <p>{{__($whychoose_caption->value->short_details)}}</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center centered">
                    @foreach($whychooses as $k => $data)
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                        <div class="single-box">
                            <img src="{{get_image(config('constants.frontend.whychoose.path').'/'.$data->value->image)}}" alt="{{__($data->value->title)}}">
                            <h3>{{__($data->value->title)}}</h3>
                            <p>{{__($data->value->details)}}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
    <!--/Section-->

    <!--We Accept Brands Area-->
    <section class="section-padding blue-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 centered">
                    <div class="section-title cl-white">
                        <h2>@lang('Payment we accept')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                @foreach($weAccept as $data)
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 col-6">
                    <div class="single-brands">
                        <a href="javascript:void(0)" title="{{__($data->name)}}"><img src="{{get_image(config('constants.deposit.gateway.path') .'/'. $data->image)}}" alt="{{$data->name}}"></a>
                    </div>
                </div>
                @endforeach

            </div>


        </div>
    </section><!--/Brands Area-->

    <!--feature Area-->
    @if(count($flowsteps)>0)
    <section class="feature-area section-padding-2 gradient-overlay">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 centered wow fadeInUp" data-wow-delay="0.3s">
                    <div class="section-title cl-white">
                        <h2>{{__($flowstep_caption->value->title)}}</h2>
                        <p>{{__($flowstep_caption->value->short_details)}}</p>
                    </div>
                </div>
            </div>

            @foreach($flowsteps->chunk(3) as $i =>$flowstep)

            <div class="row justify-content-center">

                @foreach($flowstep as $k => $data)
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="single-feature-2 @if($loop->first) bottom-after @elseif($loop->last) @else bottom-before @endif">
                        @php echo $data->value->icon @endphp
                        <h4>{{++$k}} {{__($data->value->title)}}</h4>
                        <p>{{__($data->value->details)}}</p>
                    </div>
                </div>
                @endforeach

            </div>
            @endforeach
        </div>
    </section>
    @endif
    <!--/feature Area-->

    <!-- Cta -->
    <section class="cta section-padding gradient-overlay">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 cl-white">
                    <div class="dev-first">
                        <div class="section-title mb-10">
                            <h2>{{__($developer->value->title)}}</h2>
                        </div>
                        <p>{{__($developer->value->details)}}


                            <a href="{{route('documentation')}}" class="bttn-mid btn-fill mt-5"><i class="fas fa-code"></i>@lang('View Documentation')</a>
                        </p>






                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 cl-white">
                    <img src="{{get_image(config('constants.frontend.developer.path') .'/'. $developer->value->image)}}" alt="{{url('/')}}">
                </div>
            </div>
        </div>
    </section><!-- /Cta -->



    <!-- How it works Area -->
    @if(count($howitworks)>0)
    <section class="growth-stat section-padding blue-bg cl-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-6 centered wow fadeInUp" data-wow-delay="0.3s">
                    <div class="section-title">
                        <h2>{{__($howitwork_caption->value->title)}}</h2>
                        <p>{{__($howitwork_caption->value->short_details)}}</p>
                    </div>
                </div>
            </div>

            @foreach($howitworks as $k => $data)
                @php $i++; @endphp
            @if($k%2==0)
            <div class="row mb-30">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="0.4s">
                    <img src="{{get_image(config('constants.frontend.howitwork.path').'/'.$data->value->image)}}" alt="{{__($data->value->title)}}">
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="growth-content">
                        <h3><span>{{$i}}</span> {{__($data->value->title)}}</h3>
                        <p>{{__($data->value->details)}}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="row mb-30">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 order-2 order-xl-1 order-lg-1 order-md-1 order-sm-2 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="growth-content">
                        <h3><span>{{$i}}</span>{{__($data->value->title)}}</h3>
                        <p>{{__($data->value->details)}}</p>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 order-1 order-xl-2 order-lg-2 order-md-2 order-sm-1 wow fadeInUp" data-wow-delay="0.4s">
                    <img src="{{get_image(config('constants.frontend.howitwork.path').'/'.$data->value->image)}}" alt="{{__($data->value->title)}}">
                </div>
            </div>
            @endif
            @endforeach

        </div>
    </section>
    @endif
    <!-- /How it works Area -->


    <!-- Review Area -->
    @if(count($testimonials)>0)
    <section class="review-area section-padding gradient-overlay">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-6 centered wow fadeInUp" data-wow-delay="0.3s">
                    <div class="section-title cl-white">
                        <h2>{{__($testimonial_caption->value->title)}}</h2>
                        <p>{{__($testimonial_caption->value->short_details)}}</p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 wow fadeInUp" data-wow-delay="0.4s">

                    <div class="testimonials owl-carousel">
                        @foreach($testimonials as $data)
                            <div class="single-review">
                                <div class="reviewer-thumb">
                                    <img src="{{get_image(config('constants.frontend.testimonial.path').'/'.$data->value->image)}}" alt="{{__($data->value->author)}}">
                                    <h3>{{__($data->value->author)}}</h3>
                                    <span>{{__($data->value->designation)}}</span>
                                </div>
                                <p>{{__($data->value->quote)}}</p>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!-- /Review Area -->

    <!-- Cta -->
    @if(isset($transection))
    <section class="cta section-padding gradient-overlay" style="background: url('{{asset('assets/template/basic/images/')}}/map.png')">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 centered cl-white">
                    <div class="section-title mb-20">
                        <h2>{{__($transection->value->title)}}</h2>
                        <p>
                            @php echo __($transection->value->details) @endphp
                        </p>
                    </div>
                    <a href="{{route('user.register')}}" class="bttn-mid btn-emt">@lang('Register now')</a>
                </div>
            </div>
        </div>
    </section>
    @endif
    <!-- /Cta -->



    <!--Blog Area-->
    @if(count($blogs)>0)
    <section class="blog-area section-padding-2 blue-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 centered wow fadeInUp" data-wow-delay="0.3s">
                    <div class="section-title cl-white">
                        <h4>@lang('Announcement')</h4>
                        <h2>@lang('Recent Announcement')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                @foreach($blogs as $k=> $data)
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="single-blog-2">
                        <div class="single-blog-img">
                            <img src="{{get_image(config('constants.frontend.blog.post.path').'/'.$data->value->image)}}" alt="{{$data->value->title}}">
                            <a href="{{route('home.announce.details',[$data->id, str_slug($data->value->title)])}}"><i class="flaticon-add"></i></a>
                        </div>
                        <div class="single-blog-content">
                            <div class="blog-meta">
                                <span><a href=""><i class="flaticon-calendar"></i>{{date('d M Y', strtotime($data->created_at))}}</a></span>
                            </div>
                            <h3><a href="{{route('home.announce.details',[$data->id, str_slug($data->value->title)])}}">{{__(str_limit($data->value->title, 40))}}</a></h3>
                            <p>{{str_limit($data->value->body,80)}}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    <!--/Blog Area-->



    @include('templates.basic.partials.subscribe')

@endsection


@section('import-css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"/>
@endsection

