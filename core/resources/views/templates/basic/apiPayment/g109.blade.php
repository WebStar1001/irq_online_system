@extends(activeTemplate().'invoicePayment.layout')
@section('content')

    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area height-100vh" >
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">

                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-8 mb-4">
                                <div class="card text-center">
                                    <div class="card-header">@lang('Payment Preview')</div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <img src="{{get_image(config('constants.deposit.gateway.path') .'/'. $deposit->gateway->image) }}" style="width:100%;"/>
                                            </div>
                                            <div class="col-md-8">
                                                <h3 class="mt-3 mb-3 ">@lang('Please Send ')<span class="text-success">{{$deposit->final_amo}} {{$deposit->method_currency}}</span></h3>

                                                <button type="button" class="custom-btn mt-5" id="btn-confirm" onClick="payWithRave()">@lang('Pay Now')</button>

                                                <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
                                                <script>
                                                    var btn = document.querySelector("#btn-confirm");
                                                    btn.setAttribute("type", "button");
                                                    const API_publicKey = "{{$data->API_publicKey}}";
                                                    function payWithRave() {
                                                        var x = getpaidSetup({
                                                            PBFPubKey: API_publicKey,
                                                            customer_email: "{{$data->customer_email}}",
                                                            amount: "{{$data->amount }}",
                                                            customer_phone: "{{$data->customer_phone}}",
                                                            currency: "{{$data->currency}}",
                                                            txref: "{{$data->txref}}",
                                                            onclose: function() {},
                                                            callback: function(response) {
                                                                var txref = response.tx.txRef;
                                                                var status = response.tx.status;
                                                                var chargeResponse = response.tx.chargeResponseCode;
                                                                if (chargeResponse == "00" || chargeResponse == "0") {
                                                                    window.location = '{{ url('ipn/g109') }}/' + txref +'/'+status;
                                                                } else {
                                                                    window.location = '{{ url('ipn/g109') }}/' + txref+'/'+status;
                                                                }
                                                                // x.close(); // use this to close the modal immediately after payment.
                                                            }
                                                        });
                                                    }
                                                </script>
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
    </section><!--/Dashboard area-->


@endsection

@section('script')

@stop
