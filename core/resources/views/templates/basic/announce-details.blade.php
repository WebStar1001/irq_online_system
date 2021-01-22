@extends(activeTemplate().'layouts.master')
@section('title','| '.$data['title'])
@section('content')

    @php
        if($plugins[3]->status == 1){
            $appID = $plugins[3]->shortcode->app_id->value;
            $fbComment = str_replace("{{app_id}}",$appID,$plugins[3]->script);
            echo $fbComment;
    }
    @endphp

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

    <!--Blog Area-->
    <section class="blog-area section-padding-2">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="blog-details">
                        <img src="{{asset($data['image'])}}" alt="">
                        <div class="blog-meta">
                            <span><a href=""><i class="flaticon-calendar"></i>{{date('d M Y', strtotime($post->created_at))}}</a></span>
                        </div>
                        <h2>{{$data['title']}}</h2>
                        <p>
                            @php echo  $data['details'] @endphp
                        </p>
                    </div>
                    <div class="post-share-and-tag row">
                        <div class="col-xl-7 col-lg-7 col-md-6">
                            <div class="tags">

                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 col-md-6">
                            <div class="social">
                                <span><i class="fa fa-share-alt"></i></span>

                                <a href="https://www.facebook.com/sharer/sharer.php?u={{urlencode(url()->current()) }}" class="cl-facebook"><i class="fa fa-facebook"></i></a>
                                <a href="https://twitter.com/intent/tweet?text=my share text&amp;url={{urlencode(url()->current()) }}" class="cl-twitter"><i class="fa fa-twitter"></i></a>
                                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{urlencode(url()->current()) }}&amp;title=my share text&amp;summary=dit is de linkedin summary" class="cl-linkedin"><i class="fa fa-linkedin"></i></a>
                                <a href="https://pinterest.com/pin/create/bookmarklet/?media={{asset($data['image'])}}&url={{urlencode(url()->current()) }}&is_video=[is_video]&description={{$data['title']}}" class="cl-pinterest"><i class="fa fa-pinterest-p"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="blog-all-comments">

                        <div class="comment-area">
                            <div class="fb-comments" data-colorscheme="dark" data-width="100%"
                                 data-href="{{url()->current()}}"
                                 data-numposts="10"></div>
                        </div>

                    </div>


                </div>

            </div>
        </div>
    </section><!--/Blog Area-->


@endsection
