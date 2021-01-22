@extends(activeTemplate().'invoicePayment.layout')
@section('content')
    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">

                        <div class="row justify-content-center">
                            <div class="col-lg-10 col-md-10 mb-4">

                                <div class="card ">

                                    <div class="card-header text-center">@lang('Payment Preview')</div>


                                    <div class="card-body text-center">

                                        <h3 class="text-color"> @lang('PLEASE SEND EXACTLY') <span style="color: green"> {{ $data->amount }}</span> {{$data->currency}}</h3>
                                        <h5>@lang('TO') <span style="color: green"> {{ $data->sendto }}</span></h5>
                                        <img src="{{$data->img}}" alt="">
                                        <h4 class="text-color bold">@lang('SCAN TO SEND')</h4>


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


