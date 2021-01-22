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

                                            @include(activeTemplate().'user.transfer.nav')

                                            <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
                                                <table class="table table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">@lang('Trx')</th>
                                                        <th scope="col">@lang('Date')</th>
                                                        <th scope="col">@lang('Receiver')</th>
                                                        <th scope="col">@lang('Amount')</th>
                                                        <th scope="col">@lang('Status')</th>
                                                        <th scope="col">@lang('Action')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($moneyTransfer as $data)
                                                    <tr>
                                                        <th data-label="@lang('Trx')" scope="row">{{$data->trx}} </th>
                                                        <td data-label="@lang('Date')">{{date('d M Y', strtotime($data->created_at))}}</td>
                                                        <td data-label="@lang('Receiver')">{{$data->receiver->username}}</td>
                                                        <td data-label="@lang('Amount')">{{formatter_money($data->amount)}} {{$data->currency->code}}</td>
                                                        <td data-label="@lang('Status')">
                                                            @if($data->status == 1)
                                                            <span class="badge badge-success">@lang('Paid')</span>
                                                            @elseif($data->status == 2)
                                                                <span class="badge badge-warning">@lang('Pending')</span>
                                                            @elseif($data->status == -2)
                                                                <span class="badge badge-danger">@lang('Refund')</span>
                                                            @endif
                                                        </td>
                                                        <td data-label="@lang('Action')"><a href="javascript:void(0)" class="btn btn-dark btn-sm details"  data-resource="{{$data}}" data-toggle="modal" data-target="#exampleModal"><i class="ti ti-eye"></i></a></td>
                                                    </tr>
                                                    @endforeach

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
                                {{$moneyTransfer->links('partials.pagination')}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Transfer Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item">@lang('Receiver Full Name') : <strong class="full-name"></strong></li>
                        <li class="list-group-item">@lang('Receiver Email') : <strong class="receiver-email"></strong></li>
                        <li class="list-group-item">@lang('Receiver Contact Info') : <strong class="receiver-contact"> </strong></li>
                        <li class="list-group-item">@lang('Send Amount') : <strong class="send-amount"></strong></li>
                        <li class="list-group-item">@lang('Charge') : <strong class="send-charge"></strong></li>
                        <span class="protectionAdd"></span>
                        <li class="list-group-item">@lang('status') : <strong class="transfer-status"></strong></li>
                        <li class="list-group-item">@lang('Note') : <p class="transfer-note"></p></li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <span class="formAdd"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script>
        $(document).ready(function () {
            $('.details').on('click', function () {
                var data = $(this).data('resource');



                $('.full-name').text(`${data.receiver.firstname + data.receiver.lastname} `);
                $('.receiver-email').text(`${data.receiver.email}`);
                $('.receiver-contact').text(`${data.receiver.mobile}`);
                $('.send-amount').text(`${data.amount}  ${data.currency.code}`);
                $('.send-charge').text(`${data.charge}  ${data.currency.code}`);
                $('.transfer-note').text(`${data.note}`);



                var form  = ``;

                if(data.protection == 'true') {
                    var protectionCode = `
                        <li class="list-group-item">@lang('Code Protect') : <strong class="code-protect">${ data.code_protect}</strong></li>`;
                    $('.protectionAdd').html(protectionCode);
                }else{
                    $('.protectionAdd').html(``);
                }

                if(data.status == 1) {
                    $('.formAdd').html(``);
                    $('.transfer-status').html(` <span class="badge badge-success">@lang('Paid')</span>`);
                }else if(data.status == 2){
                    $('.formAdd').html(``);
                    $('.transfer-status').html(` <span class="badge badge-warning">@lang('Pending')</span>`);
                }else if(data.status == -2){
                    $('.formAdd').html(``);
                    $('.transfer-status').html(` <span class="badge badge-danger">@lang('Refund')</span>`);
                }
            })

        });
    </script>
@endsection
