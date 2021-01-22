@extends(activeTemplate().'layouts.master')
@section('title','Profile')
@section('user_content')


    <div class="contact pt-120px pb-120px">

        <div class="container">
            <div class="row">


                <div class="col-lg-6 col-md-6">
                    @if(Auth::user()->ts)
                        <div class="single-service-item card border-0">
                            <div class="content">
                                <div class="card-header text-center">
                                    <h4 class="title">@lang('Two Factor Authenticator')</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" value="{{$prevcode}}" class="form-control input-lg" id="code" readonly>
                                            <span class="input-group-addon btn btn-success" id="copybtn">@lang('Copy')</span>
                                        </div>
                                    </div>
                                    <div class="form-group mx-auto text-center">
                                        <img class="mx-auto" src="{{$prevqr}}">
                                    </div>
                                    <button type="button" class="btn btn-block btn-lg btn-danger" data-toggle="modal" data-target="#disableModal">@lang('Disable Two Factor Authenticator')</button>
                                </div>



                            </div>
                        </div><!-- //. single service item -->
                    @else
                        <div class="single-service-item card border-0">
                            <div class="content">
                                <div class="card-header text-center bg-white">
                                    <h4 class="title">@lang('Two Factor Authenticator')</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name="key" value="{{$secret}}" class="form-control input-lg" id="code" readonly>
                                            <span class="input-group-addon btn btn-success" id="copybtn">@lang('Copy')</span>
                                        </div>
                                    </div>
                                    <div class="form-group mx-auto text-center">
                                        <img class="mx-auto" src="{{$qrCodeUrl}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <script type="text/javascript">
                    document.getElementById("copybtn").onclick = function()
                    {
                        document.getElementById('code').select();
                        document.execCommand('copy');
                    }
                </script>


                <div class="col-lg-6 col-md-6">

                    <div class="single-service-item card border-0">
                        <div class="content card-body">
                            <h4 class="title">Google Authenticator </h4>
                            <h5 style="text-transform: capitalize;">Use Google Authenticator to Scan the QR code  or use the code</h5><hr/>
                            <p>Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.</p>
                            <a class="btn btn-success btn-md mt-2" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">DOWNLOAD APP</a>
                        </div>
                    </div><!-- //. single service item -->

                </div>


            </div>
        </div>
    </div>
    <!-- service area end -->



    <!--Enable Modal -->
    <div id="enableModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Verify Your OTP')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="{{route('go2fa.create')}}" method="POST">
                    <div class="modal-body">

                        {{csrf_field()}}
                        <div class="form-group">
                            <input type="hidden" name="key" value="{{$secret}}">
                            <input type="text" class="form-control" name="code" placeholder="Enter Google Authenticator Code">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success pull-left">Verify</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                    <h4 class="modal-title">Verify Your OTP to Disable</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="{{route('disable.2fa')}}" method="POST">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <input type="text" class="form-control" name="code" placeholder="Enter Google Authenticator Code">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-block pull-left">Verify</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    @endsection
