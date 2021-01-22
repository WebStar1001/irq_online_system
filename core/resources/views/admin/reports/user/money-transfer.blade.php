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
                            <th scope="col">Sender</th>
                            <th scope="col">Receiver</th>
                            <th scope="col">Amount </th>
                            <th scope="col">Charge</th>
                            <th scope="col">Status</th>
                            <th scope="col">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>{{ show_datetime($trx->created_at) }}</td>
                            <td class="font-weight-bold">{{ strtoupper($trx->trx) }}</td>                            
                            <td><a href="{{ route('admin.users.detail', $trx->sender->id) }}">{{ $trx->sender->username }}</a></td>
                            <td><a href="{{ route('admin.users.detail', $trx->receiver->id) }}">{{ $trx->receiver->username }}</a></td>

                            <td class="budget ">
                               <strong> {{ formatter_money($trx->amount) }}
                                   @if($trx->currency_id == null) {{ $general->cur_text }} @else {{$trx->currency->code}} @endif
                               </strong>
                            </td>

                            <td class="budget text-danger">
                                <strong>
                                    {{ formatter_money($trx->charge) }}
                                    @if($trx->currency_id == null) {{ $general->cur_text }} @else {{$trx->currency->code}} @endif
                                </strong>
                            </td>

                            <td class="budget font-weight-bold">
                                @if($trx->status == 1)
                                <span class="badge badge-success">Completed</span>
                                @elseif($trx->status == 2)
                                <span class="badge badge-warning">Pending</span>
                                @elseif($trx->status == -2)
                                <span class="badge badge-success">Refund</span>
                                @endif
                            </td>

                            <td>
                                <button class="btn btn-success activateBtn" data-details="{{$trx}}"  data-toggle="modal" data-target="#activateModal"><i class="fa fa-fw fa-eye"></i></button>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Money Transfer Details</h5>
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
                <h5 class="modal-title">Search Money Transfer Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('admin.users.moneyTransfer', $user->id) }}" method="GET">
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
            var details = $(this).data('details');
            var info = `<ul class="list-group">
                  <li class="list-group-item"> Transfer Protection : ${(details.protection == 'true')? '<span class="badge badge-success">Protected</span>' : '<span class="badge badge-danger">Unprotected</span>'  }</li>
                  <li class="list-group-item"> Protection Code : <strong>${(details.code_protect != null) ? details.code_protect  : ""}</strong></li>
                  <li class="list-group-item"><strong>Details: </strong> ${details.note}</li>
                </ul>`;
            $('.modal-body-details').html(info);
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
