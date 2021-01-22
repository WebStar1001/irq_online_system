@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')


    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title float-left">@lang('Exchange Log')</h5>

                                            <button class="btn btn-info float-right"   data-toggle="modal" data-target="#searchModal">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>

                                        <div class="card-body">


                                            <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
                                                <table class="table table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">@lang('Date')</th>
                                                        <th scope="col">@lang('TRX')</th>
                                                        <th scope="col">@lang('Exchange Amount') </th>
                                                        <th scope="col">@lang('Charge')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse($transactions as $trx)
                                                        <tr>
                                                            <td>{{ show_datetime($trx->created_at) }}</td>
                                                            <td class="font-weight-bold">{{ strtoupper($trx->trx) }}</td>

                                                            <td class="budget ">
                                                                <strong class="text-success">
                                                                    {{ formatter_money($trx->from_currency_amount) }} {{$trx->from_currency->code}}
                                                                    <i class="fa fa-exchange-alt text-danger"></i>
                                                                    {{ formatter_money($trx->to_currency_amount) }} {{$trx->to_currency->code}}
                                                                </strong>
                                                            </td>

                                                            <td class="budget text-danger">
                                                                <strong class="text-danger">
                                                                    {{ formatter_money($trx->from_currency_charge) }} {{$trx->from_currency->code}}
                                                                </strong>
                                                            </td>




                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td class="text-muted text-center" colspan="100%">@lang('No Data Found!')</td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                                {{ $transactions->appends($_GET)->links('partials.pagination')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/Dashboard area-->


    {{-- Search MODAL --}}
    <div id="searchModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Search Exchange')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{route('user.exchangeLog.search')}}" method="GET">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Select Currency')</label>
                                    <select name="currency"  class="form-control">
                                        <option value="">@lang('All Currency')</option>
                                        @foreach($currencyList as $k => $val)
                                            <option value="{{$val->code}}" @isset($currency) {{($currency == $val->code) ? 'selected' : ''}} @endisset >{{$val->code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label>@lang('Start Date')</label>

                                    <div class="input-group date">
                                        <input type="text" name="start_date" id="start_date" class="form-control datepicker"  value="{{@$start_date}}" placeholder="Start Date"  autocomplete="off">
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label>@lang('End Date')</label>
                                    <div class="input-group date">
                                        <input type="text" name="end_date"  id="end_date" class="form-control datepicker " value="{{@$end_date}}" placeholder="End Date"  autocomplete="off">

                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="modal-footer">

                        <button class="btn btn-success" type="submit"> <i class="fa fa-search"></i> @lang('Search')</button>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">@lang('Close')</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection



@section('import-css')

    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@stop

@section('import-js')

    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
@stop

@section('script')

    <script>
        $('#start_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });
        $('#end_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });


    </script>
@stop
