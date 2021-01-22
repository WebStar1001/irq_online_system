@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <h5 class="card-header">{{__($page_title)}}</h5>
                                        <div class="card-body">

                                            @include(activeTemplate().'user.voucher.nav')

                                            <form action="{{route('user.voucher.SaveCode')}}" method="post">
                                                @csrf

                                                <div class="row text-center mt-5">

                                                    <div class="col-md-4">
                                                        <strong>@lang('Amount')</strong>
                                                        <p class="padding-top-10">{{formatter_money($amount)}} {{__($code)}}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>@lang('Charge') ({{formatter_money($percentCharge)}}% + {{formatter_money($fixedCharge)}} {{__($code)}})</strong>
                                                        <p class="padding-top-10">{{formatter_money($charge)}} {{__($code)}}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>@lang('You Got')</strong>
                                                        <p class="padding-top-10">{{formatter_money($amount-$charge)}} {{__($code)}}</p>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-end text-center mt-3">

                                                    <input type="hidden" name="code" value="{{$voucher}}">
                                                    <div class="col-md-2  mt-3">
                                                        <button type="submit" class="btn btn-success btn-block ">@lang('Confirm')</button>
                                                    </div>
                                                    <div class="col-md-2  ">
                                                        <div class="form-group mt-3">
                                                            <a href="{{route('user.vouchers.active_code')}}" class="btn btn-danger btn-block">@lang('Cancel')</a>
                                                        </div>
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
        </div>
    </section>



@endsection


@section('script')
@endsection
