@extends(activeTemplate() .'layouts.master')
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
                                        <h2>{{__($page_title)}}</h2>
                                        <div class="account-form">

                                            <form action="{{route('user.login')}}" method="post" class="row" id="recaptchaForm">
                                                @csrf

                                                <div class="col-xl-12">
                                                    <input type="text"  name="username" placeholder="@lang('Enter Username')" required>
                                                </div>

                                                <div class="col-xl-12">
                                                    <input type="password" name="password" placeholder="@lang('Enter Password')">
                                                </div>

                                                <div class="col-xl-12">
                                                    <button type="submit"  id="recaptcha"  class="bttn-mid btn-fill w-100">@lang('Sign In')</button>
                                                </div>
                                            </form>
                                            <div class="extra-links">
                                                <a href="{{ route('user.password.request') }}">@lang('Forget Password')</a>
                                                <a href="{{ route('user.register') }}">Register now</a>
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
    </section>
    <!--/Hero Area-->



    @if($plugins[2]->status == 1)
        <script src="//code.jquery.com/jquery-3.4.1.min.js"></script>
        @php echo recaptcha() @endphp
    @endif
@endsection
