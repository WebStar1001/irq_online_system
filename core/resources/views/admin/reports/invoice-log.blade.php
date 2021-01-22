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
                            <th scope="col">TRX</th>
                            <th scope="col">Creator</th>
                            <th scope="col">Amount </th>
                            <th scope="col">Charge</th>
                            <th scope="col">Total Amount</th>

                            <th scope="col">Status</th>
                            <th scope="col">Is Publish</th>
                            <th scope="col">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>{{ show_datetime($trx->created_at) }}</td>
                            <td class="font-weight-bold">{{ strtoupper($trx->trx) }}</td>                            
                            <td><a href="{{ route('admin.users.detail', $trx->user->id) }}">{{ $trx->user->username }}</a></td>

                            <td class="budget ">
                               <strong> {{ formatter_money($trx->amount) }}  {{$trx->currency->code}}</strong>
                            </td>
                            <td class="budget ">
                               <strong> {{ formatter_money($trx->charge) }}  {{$trx->currency->code}}</strong>
                            </td>
                            <td class="budget ">
                               <strong> {{ formatter_money($trx->amount) }}  {{$trx->currency->code}}</strong>
                            </td>


                            <td class="budget font-weight-bold">
                                @if($trx->status == 1)
                                <span class="badge badge-success">Paid</span>
                                @elseif($trx->status == 0)
                                <span class="badge badge-warning">Unpaid</span>
                                @elseif($trx->status == -1)
                                <span class="badge badge-danger">Cancel</span>
                                @endif
                            </td>

                            <td class="budget font-weight-bold">
                                @if($trx->published == 1)
                                    <span class="badge badge-success">Yes</span>
                                @elseif($trx->published == 0)
                                    <span class="badge badge-warning">No</span>
                                @endif
                            </td>

                            <td>
                                <button class="btn btn-success activateBtn" data-records="{{$trx}}"  data-list="{{$trx->details}}"  data-toggle="modal" data-target="#activateModal"><i class="fa fa-fw fa-eye"></i></button>
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


{{-- Detail MODAL --}}
<div id="activateModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body modal-body-details ">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>




{{-- Search MODAL --}}
<div id="searchModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Invoices</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('admin.report.invoice-log.search') }}" method="GET">
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
        $('.activateBtn').on('click', function() {
            var modal = $('#activateModal');
            var result = $(this).data('records');
            var detailsArr = Object.entries($(this).data('list'));


            var list = [];
            detailsArr.map( function(item,i) {
                list[i] = ` <li class="list-group-item"> ${item[0]} : <b class="float-right">${item[1]} ${result.currency.code}</b></li>`
            });
            var invoiceInfo = `
                <strong>Name: ${result.name}</strong><br>
                <b>Email: ${result.email}</b>
                <p>Address: ${result.address}</p>
                <h6>#TRX: ${result.trx}</h6>
                <li class="list-group-item mt-3 bg-dark"> Details : <b class="float-right">Amount</b></li>`;
            list.unshift(invoiceInfo);

            var totalCalc = `
                    <li class="list-group-item bg-dark mt-1 text-right"> Amount : <b>${result.amount} ${result.currency.code}</b></li>
                    <li class="list-group-item bg-dark  text-right"> Charge : <b>${result.charge} ${result.currency.code}</b></li>
                    <li class="list-group-item bg-dark  text-right"> Total Amount : <b>${result.total_amount} ${result.currency.code}</b></li>
                    `;
           list.push(totalCalc);






            $('.modal-body-details').html(list);
        });
    </script>

    <script>
       $(document).ready(function () {
           $('.datepicker').datepicker({
               format: 'dd-mm-yyyy'
           });
       })
    </script>
@endpush
