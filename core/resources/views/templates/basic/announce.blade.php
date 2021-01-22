@extends(activeTemplate().'layouts.master')
@section('title','| Announcement')
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



    <section class="blog-area section-padding-2 ">
        <div class="container">

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

            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    {{$blogs->links('partials.pagination')}}
                </div>
            </div>
        </div>
    </section>
@endsection
