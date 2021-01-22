@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')
    <section class="section-padding gray-bg blog-area" id="app">
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


                                            <form  class="" action="{{route('user.active_code.preview')}}" method="post">
                                                @csrf
                                                <div class="row mt-5">
                                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-6">
                                                        <label for="a-trans"> @lang('Voucher Code') </label>
                                                        <input type="text" name="code"  placeholder="XXXX XXXX XXXX XXXX">
                                                    </div>

                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <br>
                                                        <button type="submit" class="mt-2 custom-btn" @click="checkCalc"  >@lang('Active Now')</button>
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
