@extends(activeTemplate().'layouts.user')
@section('title','')
@section('import-css')
@stop
@section('content')
    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row dashboard-content">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-inner-content">

                        <div class="row">
                            @foreach($withdrawMethod as $data)
                                <div class="col-lg-4 col-md-4 mb-4">
                                    <div class="card">
                                        <h5 class="card-header text-center">{{__($data->name)}}</h5>
                                        <div class="card-body">
                                            <img src="{{get_image(config('constants.withdraw.method.path').'/'. $data->image)}}" class="card-img-top" alt="{{$data->name}}">

                                            <ul style="font-size: 15px;" class="list-group text-center">
                                                <li class="list-group-item">@lang('Limit') - {{formatter_money($data->min_limit)}} {{formatter_money($data->max_limit)}} </li>

                                                <li class="list-group-item"> @lang('Charge')
                                                    - {{formatter_money($data->fixed_charge)}}
                                                    + {{formatter_money($data->percent_charge)}}%
                                                </li>
                                                <li class="list-group-item">@lang('Processing Time')
                                                    - {{$data->delay}}</li>
                                            </ul>

                                        </div>
                                        <div class="card-footer">
                                            <a href="#"  data-id="{{$data->id}}" data-resource="{{$data}}"
                                               data-min_amount="{{formatter_money($data->min_limit)}}"
                                               data-max_amount="{{formatter_money($data->max_limit)}}"
                                               data-fix_charge="{{formatter_money($data->fixed_charge)}}"
                                               data-percent_charge="{{formatter_money($data->percent_charge)}}"
                                               data-base_symbol="{{$data->currency}}"

                                               class="custom-btn btn btn-block deposit" data-toggle="modal" data-target="#exampleModal">@lang('Withdraw Now')</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/Dashboard area-->


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title method-name" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('user.withdraw.moneyReq')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <p class="text-danger depositLimit"></p>
                        <p class="text-danger depositCharge"></p>

                        <div class="form-group">
                            <input type="hidden" name="currency"  class="edit-currency form-control" value="">
                            <input type="hidden" name="method_code" class="edit-method-code  form-control" value="">
                        </div>


                        <div class="form-group">
                            <label>@lang('Select Wallet') :</label>
                            <select name="currency_id" id="currency_id" class="form-control form-control-lg">
                                @foreach($currency as $val)
                                    <option value="{{$val->id}}" data-select_currency="{{$val}}">{{__($val->code)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Enter Amount'):</label>
                            <div class="input-group">
                                <input id="amount" type="text" class="form-control form-control-lg" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" name="amount" placeholder="0.00" required=""  value="{{old('amount')}}">

                                <div class="input-group-prepend">
                                    <span class="input-group-text currency-addon">CNY</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop


@section('script')
    <script>

        $(document).ready(function(){
            $('.deposit').on('click', function () {

                var id = $(this).data('id');
                var result = $(this).data('resource');
                var minAmount = $(this).data('min_amount');
                var maxAmount = $(this).data('max_amount');
                var baseSymbol = $(this).data('base_symbol');
                var fixCharge = $(this).data('fix_charge');
                var percentCharge = $(this).data('percent_charge');



                var selectedCurr =  $("#currency_id").find(':selected').data('select_currency');
                $('.currency-addon').text(`${baseSymbol}`);



                var depositLimit = `@lang('Withdraw Limit:') ${minAmount} - ${maxAmount}  ${baseSymbol}`;
                $('.depositLimit').text(depositLimit);
                var depositCharge = `@lang('Charge:') ${fixCharge} ${baseSymbol} + ${percentCharge} %`
                $('.depositCharge').text(depositCharge);
                $('.method-name').text(`@lang('Payment By ') ${result.name}`);


                $('.edit-currency').val(result.currency);
                $('.edit-method-code').val(result.id);

                console.log(result)

            });


        });
    </script>

@stop
