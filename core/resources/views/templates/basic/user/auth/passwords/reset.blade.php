@extends(activeTemplate().'layouts.master')
@section('title','Reset Password')
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
                                        <h2>@lang('Reset Password')</h2>
                                        <div class="account-form">
                                            <form action="{{ route('user.password.update')}}" method="post" class="row">
                                                @csrf
                                                <input type="hidden" name="email" value="{{ $email }}">
                                                <input type="hidden" name="token" value="{{ $token }}">

                                                <div class="col-xl-12 col-lg-12">
                                                    <label class="text-white">@lang('New Password')</label>
                                                    <input type="password" name="password" placeholder="@lang('New Password')"  required>
                                                </div>

                                                <div class="col-xl-12 col-lg-12">
                                                    <label class="text-white">@lang('Confirm Password')</label>
                                                    <input type="password" name="password_confirmation" placeholder="@lang('Confirm Password')"  required>
                                                </div>
                                                <button type="submit" class="bttn-mid btn-fill w-100"> @lang('Reset Password')</button>
                                            </form>
                                            <div class="extra-links">
                                                <a href="{{route('user.login')}}">@lang('Login Here')</a>
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
