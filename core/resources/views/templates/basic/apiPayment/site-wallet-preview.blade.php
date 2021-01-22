@extends(activeTemplate().'invoicePayment.layout')
@section('title','Payment')
@section('content')

    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area height-100vh">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">


                        <div class="row ">
                            <div class="col-lg-12">
                                <h3 class="text-info text-center mt-4 mb-4">{{$page_title}}</h3>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-lg-10 col-md-10 mb-4">
                                <div class="card text-center">
                                    <div class="card-header">@lang('Payment Preview')</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">

                                                <img src="{{get_image(config('constants.logoIcon.path') .'/gateway.png' ) }}" style="{{$general->sitename}}"/>
                                            </div>
                                            <div class="col-md-6">

                                                <ul class="list-group">



                                                    <li class="list-group-item">
                                                        @lang('Pay TO'): <strong>{{$apiPayment->merchant->fullname}} </strong>
                                                    </li>

                                                    <li class="list-group-item">
                                                        @lang('Pay Amount'): <strong>{{formatter_money($allData->amount)}} </strong> {{$allData->currency}}
                                                    </li>
                                                    <li class="list-group-item">
                                                        @lang('Pay Details'): <strong>{{$allData->details}} </strong>
                                                    </li>




                                                </ul>
                                            </div>
                                        </div>


                                        <div class="row mt-5">
                                            <div class="col-md-6">
                                                <a href="{{route('express.payment',$apiPayment->transaction)}}" class="btn btn-danger btn-lg btn-block" >@lang('Cancel')</a>
                                                <br>
                                            </div>

                                            <div class="col-md-6">
                                                <form action="{{route('express.wallet.payment.confirm')}}" method="post">
                                                    @csrf
                                                    <button class="btn btn-primary custom-sbtn btn-lg btn-block" type="submit" name="submit" value="1">@lang('Pay Now')</button>
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



@stop
