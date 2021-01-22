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



    <section class="section-padding gradient-overlay">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="section-title cl-white nic-text">
                        <p>@php echo $menu->value->body @endphp</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    @include('templates.basic.partials.subscribe')
@endsection
