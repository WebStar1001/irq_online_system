@extends(activeTemplate().'layouts.user')
@section('title','')
@section('import-css')
@stop
@section('content')
    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">

                        <div class="row">


                            <div class="col-lg-6 col-md-6">
                                @if(Auth::user()->tauth == '1')
                                        <div class="card">
                                                <h5 class="card-header">@lang('Two Factor Authenticator')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" value="{{$prevcode}}" class="form-control form-control-lg" id="code" readonly>
                                                        <span class="input-group-addon btn btn-success" id="copybtn">@lang('Copy')</span>
                                                    </div>
                                                </div>
                                                <div class="form-group mx-auto text-center">
                                                    <img class="mx-auto" src="{{$prevqr}}">
                                                </div>
                                                <button type="button" class="btn btn-block btn-lg btn-danger" data-toggle="modal" data-target="#disableModal">@lang('Disable Two Factor Authenticator')</button>
                                            </div>
                                        </div>
                                @else
                                        <div class="card">

                                            <h5 class="card-header">@lang('Two Factor Authenticator')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" name="key" value="{{$secret}}" class="form-control form-control-lg" id="code" readonly>
                                                        <span class="input-group-addon btn btn-success" id="copybtn">@lang('Copy')</span>
                                                    </div>
                                                </div>
                                                <div class="form-group mx-auto text-center">
                                                    <img class="mx-auto" src="{{$qrCodeUrl}}">
                                                </div>
                                                <button type="button" class="btn btn-block btn-lg btn-primary small-font-size-in-mobile-device" data-toggle="modal" data-target="#enableModal">@lang('Enable Two Factor Authenticator')</button>
                                            </div>
                                        </div>

                                @endif
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class=" card ">
                                    <h5 class="card-header">@lang('Google Authenticator')</h5>
                                    <div class=" card-body">
                                        <h5 class="text-uppercase">@lang('Use Google Authenticator to Scan the QR code  or use the code')</h5><hr/>
                                        <p>@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.')</p>
                                        <a class="btn btn-success btn-md" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">@lang('DOWNLOAD APP')</a>
                                    </div>
                                </div><!-- //. single service item -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/Dashboard area-->





    <!--Enable Modal -->
    <div id="enableModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Verify Your OTP')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="{{route('user.go2fa.create')}}" method="POST">
                    <div class="modal-body">

                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="hidden" name="key" value="{{$secret}}">
                            <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success pull-left">@lang('Verify')</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('Close')</button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <!--Disable Modal -->
    <div id="disableModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Verify Your OTP to Disable')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="{{route('user.disable.2fa')}}" method="POST">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-block pull-left">@lang('Verify')</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.getElementById("copybtn").onclick = function(){
            document.getElementById('code').select();
            document.execCommand('copy');
        }
    </script>
@endsection

@section('import-js')
@endsection
