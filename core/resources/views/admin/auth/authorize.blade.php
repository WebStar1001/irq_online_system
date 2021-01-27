@extends(activeTemplate().'layouts.master')
@section('title','SMS verification form')
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
                                        <h2>@lang($page_title)</h2>
                                        <div class="account-form">
                                            <form method="POST" action="{{route('admin.go2fa.verify')}}" class="row">
                                                @csrf
                                                <div class="col-xl-12">
                                                    <p class="text-white">{{\Carbon\Carbon::now()}}</p>
                                                </div>
                                                <div class="col-xl-12">
                                                    <label for="InputName">@lang("Google Authenticator Code")  </label>
                                                    <input type="text" name="code"
                                                           placeholder="Enter Google Authenticator Code" required>
                                                </div>

                                                <button type="submit"
                                                        class="bttn-mid btn-fill w-100">@lang('Submit')</button>
                                            </form>

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
@endsection
