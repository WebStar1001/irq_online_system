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
                                            <form action="{{ route('user.password.verify-code') }}" method="post" class="row">
                                                @csrf
                                                <input type="hidden" name="email" value="{{ $email }}">


                                                <div class="col-xl-12 col-lg-12">
                                                    <label class="text-white">@lang('Verification Code')</label>
                                                    <input type="text" name="code" id="pincode-input" class="magic-label">
                                                </div>
                                                <button type="submit" class="bttn-mid btn-fill w-100"> @lang('Verify Code')</button>
                                            </form>
                                            <div class="extra-links">
                                                <a href="{{ route('user.password.request') }}">@lang('Try to send again')</a>
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

@section('import-css')
<link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-pincode-input.css') }}"/>
@stop

@section('import-js')
<script src="{{ asset('assets/admin/js/bootstrap-pincode-input.js') }}"></script>
<script>
    $('#pincode-inputs').pincodeInput({
        inputs:6,
        placeholder:"- - - - - -",
        hidedigits:false
    });
</script>
@stop




