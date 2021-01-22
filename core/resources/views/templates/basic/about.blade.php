@extends(activeTemplate().'layouts.master')
@section('title','| '.$page_title)
@section('content')
    <!--breadcrumb area-->
    <section class="breadcrumb-area fixed-head blue-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 centered">
                    <div class="banner-title">
                        <h2>{{__($page_title)}}</h2>
                    </div>
                    <ul>
                        <li><a href="{{route('home')}}">@lang('Home')</a></li>
                        <li>{{__($page_title)}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section><!--/breadcrumb area-->


    <section class="about-area gradient-overlay">
        <div class="about-content mid-bg-gray wow fadeInUp" data-wow-delay="0.4s">
            <div class="about-content-inner-2 cl-white ">
                <div class="section-title mb-10">
                    <h4>{{__($about->value->title)}}</h4>
                    <h2>{{__($about->value->sub_title)}}</h2>
                </div>
                <p>
                    @php echo __($about->value->details) @endphp
                </p>
                <img class="sign" src="{{asset('assets/images/frontend/about/signature.png')}}" alt="">
            </div>
        </div>
        <div class="about-left wow fadeInUp" data-wow-delay="0.4s" style="background: url({{asset('assets/images/frontend/about/about.jpg')}}) no-repeat center / cover;">
            <div class="left-img-wrap">

            </div>
        </div>
    </section>



    <!-- Team Area -->
    <section class="team-area section-padding-2 blue-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-6 centered wow fadeInUp" data-wow-delay="0.3s">
                    <div class="section-title cl-white">
                        <h4>{{__($team_caption->value->title)}}</h4>
                        <h2>{{__($team_caption->value->short_details)}}</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                @foreach($teams as $data)
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="single-team-4">
                        <img src="{{get_image(config('constants.frontend.team.path').'/'.$data->value->image)}}" alt="{{__($data->value->name)}}">
                        <div class="team-content">
                            <h4>{{__($data->value->name)}}</h4>
                            <p>{{__($data->value->designation)}}</p>
                            <div class="social">
                                @if(!empty($data->value->facebook))
                                <a href="{{$data->value->facebook}}" class="cl-facebook"> <i class="fa fa-facebook"></i></a>
                                @endif

                                @if(!empty($data->value->twitter))
                                <a href="{{$data->value->twitter}}" class="cl-twitter"><i class="fa fa-twitter"></i></a>
                                @endif

                                @if(!empty($data->value->linkedin))
                                <a href="{{$data->value->linkedin}}" class="cl-linkedin"><i class="fa fa-linkedin"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section><!-- /Team Area -->


    <!-- Review Area -->
    <section class="review-area section-padding gradient-overlay">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="section-title cl-white">
                        <h2>{{__($testimonial_caption->value->title)}}</h2>
                        <p>{{__($testimonial_caption->value->short_details)}}</p>
                    </div>
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
    </section><!-- /Review Area -->


    @include('templates.basic.partials.subscribe')

@endsection
