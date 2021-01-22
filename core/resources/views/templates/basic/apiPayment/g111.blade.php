@extends(activeTemplate().'invoicePayment.layout')

@section('style')

    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .StripeElement {
            box-sizing: border-box;

            height: 40px;

            padding: 10px 12px;

            border: 1px solid transparent;
            border-radius: 4px;
            background-color: white;

            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
@stop
@section('content')



    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area height-100vh">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">

                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-8 mb-4">

                                <div class="card text-center">

                                    <div class="card-header">@lang('Stripe Payment - Final Step')</div>


                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <img src="{{get_image(config('constants.deposit.gateway.path') .'/'. $deposit->gateway->image) }}" style="width:100%;"/>
                                            </div>
                                            <div class="col-md-8">
                                              <h3 class="mt-3 mb-3 ">@lang('Please Send') <span class="text-success">{{$deposit->final_amo}} {{$deposit->method_currency}}</span></h3>


                                                <form action="{{$data->url}}" method="{{$data->method}}">
                                                    <script
                                                        src="{{$data->src}}"
                                                        class="stripe-button custom-btn mt-5"
                                                        @foreach($data->val as $key=> $value)
                                                        data-{{$key}}="{{$value}}"
                                                        @endforeach
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


