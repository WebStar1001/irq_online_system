@extends(activeTemplate().'invoicePayment.layout')
@section('title','| '.$page_title)

@section('style')
@stop

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
                                                    <li class="list-group-item text-color"> @lang('Pay To Name') : <strong>{{$invoicePayment->user->firstname . ' '.$invoicePayment->user->lastname}}</strong> </li>
                                                    <li class="list-group-item text-color"> @lang('Pay To Email') : <strong>{{$invoicePayment->user->email}}</strong> </li>
                                                    <li class="list-group-item text-color"> @lang('Payable Amount') : <strong>{{$invoicePayment->amount}} {{$invoicePayment->currency->code}}</strong> </li>
                                                </ul>
                                            </div>
                                        </div>


                                        <div class="row mt-5">
                                            <div class="col-md-6">

                                                <a href="{{route('getInvoice.payment',$invoicePayment->trx)}}" class="btn btn-danger ">@lang('Cancel Payment')</a>
                                            </div>

                                            <div class="col-md-6">
                                                <form method="POST" action="{{route('invoice.confirm.id',encrypt($invoicePayment->id)) }}">
                                                    {{csrf_field()}}
                                                    {{method_field('PUT')}}

                                                    <button type="submit" class="btn btn-primary custom-sbtn"  id="btn-confirm">@lang('Confirm Now')</button>
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
