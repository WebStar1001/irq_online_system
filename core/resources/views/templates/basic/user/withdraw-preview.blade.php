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
                            <div class="col-md-12 col-sm-12">
                                <div class="card panel-custom">
                                    <div class="card-header">
                                        <h4 class="card-title text-white">@lang('Withdraw Preview')
                                            <div class="float-right">
                                                <a href="{{route('user.withdraw.money')}}" class="bttn-small btn-emt "><i class="fa fa-arrow-left"></i> @lang('Another Method')</a>
                                            </div>
                                        </h4>
                                    </div>
                                    <!-- panel body -->



                                    <div class="card-body">
                                        <div class="text-center">
                                            <h3>@lang('Current Balance') : <strong>{{ formatter_money($withdraw->wallet->amount)}} - {{ $withdraw->curr->code }}</strong></h3>
                                        </div>

                                        <div class="row mt-4 justify-content-between">
                                            <div class="col-5 myform">
                                                <div class="form-group">
                                                    <label class="control-label">@lang('Request Amount') : </label>

                                                    <div class="input-group">
                                                        <input type="text" value="{{formatter_money($withdraw->amount )}}" readonly  class="form-control form-control-lg" placeholder="@lang('Enter Amount')">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text ">{{$withdraw->curr->code}} </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">@lang('Withdrawal Charge') : </label>
                                                    <div class="input-group">
                                                        <input type="text" value="{{ formatter_money($withdraw->charge) }}" readonly   class="form-control form-control-lg" placeholder="@lang('Enter Amount')">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text ">{{ $withdraw->currency}} </span>
                                                        </div>
                                                    </div>
                                                </div>




                                                <div class="form-group">
                                                    <label class=" control-label">@lang('You Will Get') : </label>
                                                    <div class="input-group">
                                                        <input type="text" value="{{ formatter_money($withdraw->final_amount) }}" readonly class="form-control form-control-lg" placeholder="@lang('Enter  Amount')" required>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text ">{{ $withdraw->currency }} </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">

                                                    <label class="control-label">@lang('Available Balance') : </label>
                                                    <div class="input-group">
                                                        <input type="text" value="{{formatter_money($withdraw->wallet->amount - $withdraw->amount)}}"  class="form-control form-control-lg" placeholder="@lang('Enter Amount')" required>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text ">{{ $withdraw->curr->code }} </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <form action="{{route('user.withdraw.submit')}}" method="post">
                                                    @csrf
                                                @foreach(json_decode($withdraw->detail) as $k=> $value)
                                                <div class="form-group">
                                                    <label> {{str_replace('_',' ',$k)}} </label>
                                                    <input type="text" name="{{$k}}" value=""  class="form-control " placeholder="" >
                                                </div>
                                                @endforeach


                                                    <div class="form-group">
                                                        <button type="submit" class="custom-btn mt-4">@lang('Confirm')</button>
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
        </div>
    </section><!--/Dashboard area-->


@stop


@section('script')


@stop
