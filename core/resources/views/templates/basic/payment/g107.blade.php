@extends(activeTemplate().'layouts.user')
@section('content')





    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
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
                                                <h3 class="mt-3 mb-3 ">@lang('Please Pay') <span class="text-success">{{$deposit->final_amo}} {{$deposit->method_currency}}</span></h3>
                                                <h3 class="mt-3 mb-3 ">@lang('To Get') <span class="text-success">{{formatter_money($deposit->wallet_amount)}}  {{$deposit->currency->code}}</span></h3>



                                                <button type="button" class="custom-btn mt-5" id="btn-confirm">@lang('Pay Now')</button>
                                                <form action="{{ route('ipn.g107') }}" method="POST">
                                                    @csrf
                                                    <script
                                                        src="//js.paystack.co/v1/inline.js"
                                                        data-key="{{ $data->key }}"
                                                        data-email="{{ $data->email }}"
                                                        data-amount="{{$data->amount}}"
                                                        data-currency="{{$data->currency}}"
                                                        data-ref="{{ $data->ref }}"
                                                        data-custom-button="btn-confirm"
                                                    >
                                                    </script>
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
        </div>
    </section><!--/Dashboard area-->

@endsection

