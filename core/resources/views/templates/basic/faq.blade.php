@extends(activeTemplate().'layouts.master')
@section('title',"$page_title")
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


    <!-- Section -->
    <section class="section-padding gradient-overlay">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 wow fadeInRight" data-wow-delay="0.4s">
                    <div class="faq-contents nic-text">
                        <ul class="accordion">
                            @foreach($faqs as $k => $data)
                            <li>
                                <a href="javascript:void(0)"><i class="fa fa-hand-o-right"></i> {{__($data->value->title)}}</a>
                                <p>@php echo strip_tags($data->value->body) @endphp</p>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Section -->



    @include('templates.basic.partials.subscribe')
@endsection
