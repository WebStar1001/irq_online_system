@extends(activeTemplate().'invoicePayment.layout')
@section('title','| '.$page_title)
@section('content')

    <section class="section-padding gray-bg ">
        <div class="container">
            @include(activeTemplate().'invoicePayment.invoice')

            @if($invoice->status == 0)
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">

                        <div class="row ">
                            <div class="col-lg-12">
                                <h2 class="text-info text-center mt-4">@lang('Select One to Pay')  {{formatter_money($invoice->amount)}} {{$invoice->currency->code}}</h2>
                            </div>
                        </div>

                        <div class="row">

                            @guest
                                <div class="col-md-4 mt-4">
                                    <div class="card cash-card">
                                        <a href="#" data-toggle="modal" data-target="#addMyModal">
                                            <div class="card-body">
                                                <span class="cash-thumb">
                                                    <img src="{{asset('assets/images/logoIcon/gateway.png')}}" alt="{{$general->sitename}}"></span>
                                                <div class="cash-content">
                                                    <h4 class="card-title">Pay With {{$general->sitename}}</h4>
                                                    <p>@lang('A Trust Payment Gateway')</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endguest


                                @auth
                                    <div class="col-md-4 mt-4">
                                        <div class="card cash-card">
                                            <a href="{{route('invoice.preview.confirm',encrypt(Session::get('invoiceTrx')))}}">
                                                <div class="card-body">
                                            <span class="cash-thumb">
                                                <img src="{{asset('assets/images/logoIcon/gateway.png')}}" alt="{{$general->sitename}}"></span>
                                                    <div class="cash-content">
                                                        <h4 class="card-title">Pay With {{$general->sitename}}</h4>
                                                        <p>@lang('A Trust Payment Gateway')</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endauth

                                @foreach($gateways as $data)
                                    <div class="col-md-4 mt-4">
                                        <div class="card cash-card">
                                            <a href="{{route('invoice.preview',encrypt($data->id))}}">
                                                <div class="card-body">
                                                    <span class="cash-thumb">
                                                        <img src="{{get_image(config('constants.deposit.gateway.path').'/'. $data->image)}}" alt="{{$data->name}}">
                                                    </span>
                                                    <div class="cash-content">
                                                        <h5 class="card-title">{{__($data->name)}}</h5>
                                                        <p>{{__(str_limit($data->method->description,40))}} </p>
                                                    </div>

                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach




                        </div>
                    </div>
                </div>
            </div>

            @elseif($invoice->status == 1)
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <h2 class="text-success text-center mt-4">@lang('Payment Completed')</h2>
                    </div>
                </div>
            @endif


        </div>
    </section>



    <!-- The Modal -->
    <div class="modal fade" id="addMyModal">
        <div class="modal-dialog ">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Sign In')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="post" id="frm" onsubmit="login(event)">
                {{csrf_field()}}
                <!-- Modal body -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Username')</label>
                            <input type="text" name="username" class="form-control form-control-lg"  placeholder="Enter Username">
                            <span class="error text-danger username"></span>
                        </div>

                        <div class="form-group">
                            <label>@lang('Password')</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter Password">
                            <span class="error text-danger password"></span>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success btn-block btn-lg">@lang('Submit')</button>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@stop

@section('script')
    <script>
        function login(e) {
            e.preventDefault();

            var fd = new FormData(document.getElementById('frm'));

            $.ajax({
                url: "{{route('invoice.signin')}}",
                type: "post",
                data: fd,
                contentType: false,
                processData: false,
                success: function (data) {
                    var error =  $('.error');
                    $('.error').each(function (i, val) {
                        $(this).html('');
                    })



                    if(data.fail == true){
                        if(typeof data.errors.username != 'undefined')
                        {
                            $('.username').html(data.errors.username[0]);
                        }
                        if(typeof  data.errors.password != 'undefined')
                        {
                            $('.password').html(data.errors.password[0]);
                        }
                    }

                    if(data.status == 'credential')
                    {
                        $('.username').html(data.msg);
                    }

                    if(data.status == 'authenticate')
                    {

                        location.href = data.url;
                    }


                },
                error: function (data) {
                }

            });


        }
    </script>
@endsection
