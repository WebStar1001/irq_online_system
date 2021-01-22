@extends(activeTemplate().'layouts.user')

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

                                    <div class="card-header text-center">@lang('Stripe Payment')</div>


                                    <div class="card-body">

                                        <div class="card-wrapper"></div>
                                        <br><br>

                                        <form role="form" id="payment-form" method="{{$data->method}}" action="{{$data->url}}">
                                            {{csrf_field()}}
                                            <input type="hidden" value="{{$data->track}}" name="track">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="name">@lang('CARD NAME')</label>
                                                    <div class="form-group-group ">
                                                        <input type="text" class="form-control form-control-lg " name="name" placeholder="Card Name" autocomplete="off" autofocus/>

                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="cardNumber">@lang('CARD NUMBER')</label>
                                                    <div class="input-group">
                                                        <input type="tel" class="form-control form-control-lg " name="cardNumber" placeholder="Valid Card Number" autocomplete="off" required autofocus />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-4">
                                                <div class="col-md-6">
                                                    <label for="cardExpiry">@lang('EXPIRATION DATE')</label>
                                                    <input type="tel" class="form-control form-control-lg input-sz" name="cardExpiry" placeholder="MM / YYYY" autocomplete="off" required />
                                                </div>
                                                <div class="col-md-6 ">

                                                    <label for="cardCVC">@lang('CVC CODE')</label>
                                                    <input type="tel" class="form-control form-control-lg input-sz custom-input" name="cardCVC" placeholder="CVC" autocomplete="off" required />
                                                </div>

                                                <div class="col-md-12 ">
                                                    <button class="custom-btn  btn-lg btn-block" type="submit"> @lang('PAY NOW')</button>
                                                </div>

                                            </div>

                                        </form>


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
    <script type="text/javascript" src="https://rawgit.com/jessepollak/card/master/dist/card.js"></script>

    <script>
        (function ($) {
            $(document).ready(function () {
                var card = new Card({
                    form: '#payment-form',
                    container: '.card-wrapper',
                    formSelectors: {
                        numberInput: 'input[name="cardNumber"]',
                        expiryInput: 'input[name="cardExpiry"]',
                        cvcInput: 'input[name="cardCVC"]',
                        nameInput: 'input[name="name"]'
                    }
                });
            });
        })(jQuery);
    </script>
@stop


