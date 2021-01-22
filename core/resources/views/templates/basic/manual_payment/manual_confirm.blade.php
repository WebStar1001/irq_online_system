@extends(activeTemplate().'layouts.user')
@section('content')

    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">

                        <div class="row justify-content-center">
                                <div class="col-lg-12 col-md-12 mb-4">

                                    <div class="card">

                                        <div class="card-header">{{__($page_title)}}</div>


                                        <div class="card-body">
                                            <form action="{{ route('user.manualDeposit.update') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                            <div class="row">


                                                <div class="col-md-12">
                                                        <p class="text-center mt-2">@lang('You have requested ') <b class="text-success">{{ formatter_money($data['amount'])  }} {{$data['method_currency']}}</b> @lang(', Please pay ') <b class="text-success">{{$data['final_amo'] .' '.$data['method_currency'] }}</b> @lang(' for successful payment')</p>
                                                        <h4 class="text-center mb-4">@lang('Please follow the instruction bellow')</h4>

                                                </div>



                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="a-trans">@lang('Verify Image')</label>
                                                        <input type="file" class="form-control" name="verify_image">
                                                    </div>
                                                </div>

                                                @foreach(json_decode($method->parameter) as $input)
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="a-trans">{{__($input)}}</label>
                                                        <input type="text" class="form-control" name="ud[{{str_slug($input) }}]" placeholder="{{ $input }}">
                                                    </div>
                                                </div>
                                                @endforeach

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                    <button type="submit" class="custom-btn btn-block mt-2 text-center">@lang('Pay Now')</button>
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
    </section><!--/Dashboard area-->


@endsection

