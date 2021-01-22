@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table align-items-center table-light">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Voucher Code</th>

                            <th scope="col">Creator</th>
                            <th scope="col">Make Amount </th>
                            <th scope="col">Make Amount Charge </th>

                            <th scope="col">User </th>
                            <th scope="col">Useable Amount</th>
                            <th scope="col">Useable Charge</th>

                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>{{ show_datetime($trx->created_at) }}</td>
                            <td class="font-weight-bold">{{$trx->code }}</td>
                            <td><a href="{{ route('admin.users.detail', $trx->creator->id) }}">{{ $trx->creator->username }}</a></td>

                            <td class="budget ">
                               <strong>{{ formatter_money($trx->amount) }} {{$trx->currency->code}}</strong>
                            </td>
                            <td class="budget ">
                               <strong>{{ formatter_money($trx->charge) }} {{$trx->currency->code}}</strong>
                            </td>


                            <td>
                                @if($trx->user != null)
                                <a href="{{ route('admin.users.detail', $trx->user->id) }}">{{ $trx->user->username }}</a>
                                @else
                                    -
                                @endif
                            </td>



                            <td class="budget ">
                                @if($trx->user != null)
                                <strong>{{ formatter_money($trx->useable_amount) }} {{$trx->currency->code}}</strong>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="budget ">
                                @if($trx->user != null)
                                <strong>{{ formatter_money($trx->use_charge) }} {{$trx->currency->code}}</strong>
                                @else
                                -
                                @endif
                            </td>
                            <td class="budget ">
                                @if($trx->status == 1)
                                    <span class="badge badge-danger">Expired</span>
                                @else
                                    <span class="badge badge-success">Activated</span>
                                @endif
                            </td>






                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer py-4">
                <nav aria-label="...">
                    {{ $transactions->appends($_GET)->links() }}
                </nav>
            </div>
        </div>
    </div>
</div>





{{-- Search MODAL --}}
<div id="searchModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Voucher Gift Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('admin.report.voucher-log.search') }}" method="GET">
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Select Currency</label>
                            <select name="currency"  class="form-control">
                                <option value="">All Currency</option>
                                @foreach($currencyList as $k => $val)
                                    <option value="{{$val->code}}" @isset($currency) {{($currency == $val->code) ? 'selected' : ''}} @endisset >{{$val->code}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label>Start Date</label>

                                <div class="input-group date">
                                    <input type="text" name="start_date" class="form-control datepicker"  value="{{@$start_date}}" placeholder="Start Date"  autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"> <span class="fa fa-calendar"></span></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label>End Date</label>
                                <div class="input-group date">
                                    <input type="text" name="end_date" class="form-control datepicker" value="{{@$end_date}}" placeholder="End Date"  autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"> <span class="fa fa-calendar"></span></span>

                                    </div>
                                </div>
                            </div>
                        </div>

                </div>


            </div>
            <div class="modal-footer">

                <button class="btn btn-success" type="submit"> <i class="fa fa-search"></i> Search</button>
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>

            </form>
        </div>
    </div>
</div>



@endsection

@push('breadcrumb-plugins')

<button class="btn btn-success"   data-toggle="modal" data-target="#searchModal">
    <i class="fa fa-search"></i>
</button>
@endpush



@push('script-lib')
    <script src="{{asset('assets/admin/js/bootstrap-datepicker.min.js')}}"></script>
@endpush
@push('style-lib')
@endpush

@push('script')

    <script>
       $(document).ready(function () {
           $('.datepicker').datepicker({
               format: 'dd-mm-yyyy'
           });


       })
    </script>
@endpush
