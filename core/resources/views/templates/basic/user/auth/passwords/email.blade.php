@extends(activeTemplate().'layouts.master')
@section('title','SMS verification form')
@section('content')

<!--Hero Area-->
<section class="hero-section">
    <div class="hero-area wave-animation">
        <div class="single-hero gradient-overlay">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 centered">
                        <div class="hero-sub">
                            <div class="table-cell">
                                <div class="hero-left">
                                    <h2>@lang('Forgot Password')</h2>
                                    <div class="account-form">
                                        <form action="{{ route('user.password.email') }}" method="post" class="row">
                                            @csrf
                                            <div class="col-xl-12 col-lg-12">
                                                <label class="text-white">@lang('Email Address')</label>
                                                <input type="email" name="email" placeholder="@lang('Your E-mail Address')"  required>
                                            </div>
                                            <button type="submit" class="bttn-mid btn-fill w-100"> @lang('Submit')</button>
                                        </form>
                                        <div class="extra-links">
                                            <a href="{{route('user.login')}}">@lang('Back To Login')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section><!--/Hero Area-->

@stop
