@extends(activeTemplate().'invoicePayment.layout')
@section('title','| '.$page_title)
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
                                        <ul class="list-group">

                                            <li class="list-group-item">
                                                <img src="{{get_image(config('constants.deposit.gateway.path') .'/'. $data->gateway->image) }}" style="max-width:100px; max-height:100px; margin:0 auto;"/>
                                            </li>


                                            <li class="list-group-item">
                                                @lang('Amount'): <strong>{{formatter_money($data->amount)}} </strong> {{$data->method_currency}}
                                            </li>


                                            <li class="list-group-item">
                                                @lang('Charge'): <strong>{{formatter_money($data->charge)}}</strong> {{$data->baseCurrency()}}
                                            </li>

                                            <li class="list-group-item">
                                                @lang('Payable'): <strong> {{$data->final_amo}}</strong> {{$data->baseCurrency()}}
                                            </li>


                                            @if($data->method_currency != $data->currency->code)
                                            <li class="list-group-item">
                                                @lang('Conversion Rate'): <strong>1 {{$data->method_currency}} =  {{formatter_money($data->cur_rate * $data->gate_rate)}}  {{$data->currency->code}}  </strong>
                                            </li>
                                            @endif




                                            @if($data->gateway->crypto==1)
                                                <li class="list-group-item">
                                                    @lang('Conversion with') <b> {{ $data->method_currency }}</b> @lang('and final value will Show on next step')
                                                </li>
                                            @endif
                                        </ul>

                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <a href="{{route('getInvoice.payment',$invoice->trx)}}" class="btn btn-danger btn-lg btn-block" >@lang('Cancel')</a>
                                                <br>
                                            </div>

                                            <div class="col-md-6">
                                                <a href="{{route('invoice.deposit.confirm')}}" class="btn btn-primary custom-sbtn btn-lg btn-block"  id="btn-confirm">@lang('Pay Now')</a>
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
