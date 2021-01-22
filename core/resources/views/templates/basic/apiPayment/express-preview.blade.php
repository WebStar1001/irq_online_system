@extends(activeTemplate().'invoicePayment.layout')
@section('title','Payment')
@section('content')

    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">


                        @include(activeTemplate().'apiPayment.payment-intro')

                        <div class="row ">
                            <div class="col-lg-12">
                                <h3 class="text-info text-center mt-4 mb-4">{{$page_title}}</h3>
                            </div>
                        </div>

                        <div class="row justify-content-center">

                            <div class="col-lg-12 col-md-12 mb-4">

                                <div class="card text-center">

                                    <div class="card-header">@lang('Payment Preview')</div>


                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6">

                                                <img src="{{get_image(config('constants.deposit.gateway.path') .'/'. $data->gateway->image) }}" style=""/>
                                            </div>
                                            <div class="col-md-6">

                                                <ul class="list-group">

                                                    @if($data->method_currency != $data->currency->code)
                                                        <li class="list-group-item">
                                                            @lang('Conversion Rate'): <strong>1 {{$data->method_currency}} =  {{formatter_money($data->cur_rate * $data->gate_rate)}}  {{$data->currency->code}}  </strong>
                                                        </li>
                                                    @endif


                                                    <li class="list-group-item">
                                                        @lang('Amount'): <strong>{{formatter_money($data->amount)}} </strong> {{$data->method_currency}}
                                                    </li>

                                                    <li class="list-group-item">
                                                        @lang('Charge'): <strong>{{formatter_money($data->charge)}}</strong> {{$data->method_currency}}
                                                    </li>

                                                        <li class="list-group-item">
                                                            @lang('Payable'): <strong> {{$data->final_amo}}</strong> {{$data->baseCurrency()}}
                                                        </li>





                                                    @if($data->gateway->crypto==1)
                                                        <li class="list-group-item">
                                                            @lang('Conversion with') <b> {{ $data->method_currency }}</b> @lang('and final value will Show on next step')
                                                        </li>
                                                    @endif


                                                </ul>
                                            </div>
                                        </div>


                                        <div class="row mt-5">
                                            <div class="col-md-6">
                                                <a href="{{route('express.payment',$data->express_payment->transaction)}}" class="btn btn-danger btn-lg btn-block" >@lang('Cancel')</a>
                                                <br>
                                            </div>

                                            <div class="col-md-6">
                                                <a href="{{route('express.payment.confirm')}}" class="btn btn-primary custom-sbtn btn-lg btn-block"  id="btn-confirm">@lang('Pay Now')</a>
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



@stop
