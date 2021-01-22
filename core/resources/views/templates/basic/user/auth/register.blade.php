@extends(activeTemplate() .'layouts.master')
@section('content')

    <!--Hero Area-->
    <section class="hero-section">
        <div class="hero-area wave-animation">
            <div class="single-hero gradient-overlay">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-8 centered">
                            <div class="hero-sub">
                                <div class="table-cell">
                                    <div class="hero-left">
                                        @if($general->reg == 0)

                                            <h3>{{__($page_title)}} @lang("Has been Deactivated By Admin")</h3>
                                        @else

                                        <h2>{{__($page_title)}}</h2>
                                        <div class="account-form">
                                            <form class="row" action="{{ route('user.register') }}" method="post" id="recaptchaForm">
                                                @csrf
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <input type="text" name="firstname" value="{{old('firstname')}}" placeholder="@lang('First Name')" required>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <input type="text" name="lastname" value="{{old('lastname')}}" placeholder="@lang('Last Name')" required>
                                                </div>


                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <input type="text" name="username" value="{{old('username')}}" placeholder="@lang('Username')" required>
                                                </div>

                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <input type="email" name="email" value="{{old('email')}}"  placeholder="@lang('Enter Your E-mail')" required>
                                                </div>


                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <input type="text" id="mobile"  name="mobile"  value="{{old('mobile')}}"  placeholder="@lang('Enter Your Mobile No.')"  required>
                                                </div>


                                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <select name="country" id="country" required>
                                                        @include('partials.country')
                                                    </select>
                                                </div>



                                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                    <input type="password" name="password" placeholder="@lang('Enter Your Password')" required>
                                                </div>
                                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                    <input type="password" name="password_confirmation" placeholder="@lang('Re-type Password')" required>
                                                </div>




                                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                                    <button type="submit" id="recaptcha" class="bttn-mid btn-fill w-100">@lang('Create account')</button>
                                                </div>
                                            </form>
                                            <div class="extra-links">
                                                <a href="{{route('user.login')}}">@lang('Login account')</a>

                                            </div>
                                        </div>
                                            @endif
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
