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
                                        <h5 class="card-header">@lang('My Request')</h5>
                                        <div class="card-body">
                                           @include(activeTemplate().'user.request_money.nav')


                                            <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
                                                <table class="table table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">@lang('Trx')</th>
                                                        <th scope="col">@lang('Date')</th>
                                                        <th scope="col">@lang('Username')</th>
                                                        <th scope="col">@lang('Amount')</th>
                                                        <th scope="col">@lang('Status')</th>
                                                        <th scope="col">@lang('Action')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($requestMoney) >0)
                                                        @foreach($requestMoney as $k=>$data)
                                                        <tr>
                                                            <td data-label="@lang('Trx')">{{$data->trx}}</td>
                                                            <td data-label="@lang('Date')">{{date('d M Y',strtotime($data->created_at))}}</td>
                                                            <td data-label="@lang('Username')">
                                                                <strong>{{ $data->receiver->username  }}</strong>
                                                            </td>
                                                            <td data-label="@lang('Amount')">
                                                                <strong class="{{(($data->status == 1) ? 'text-success' : ( ($data->status == -1) ? 'text-danger' : '' ) )}}">  {{formatter_money($data->amount)}}   {{__($data->currency->code)}}</strong>
                                                            </td>
                                                            <td data-label="@lang('Status')">
                                                                @if($data->status == 0)
                                                                    <span class="badge badge-warning">@lang('Pending')</span>
                                                                @elseif($data->status == 1)
                                                                    <span class="badge badge-success">@lang('Paid')</span>
                                                                @elseif($data->status == -1)
                                                                    <span class="badge badge-danger">@lang('Rejected')</span>
                                                                @endif
                                                            </td>
                                                            <td data-label="@lang('Action')">
                                                                <a href="javascript:void(0);" class="btn btn-dark btn-sm details" data-total_amo="{{formatter_money($data->amount-$data->charge)}}" data-id="{{encrypt($data->id)}}" data-date="{{date('d M Y H:i A ',strtotime($data->created_at))}}" data-records="{{$data}}" data-toggle="modal" data-target="#myModal"><i class="ti ti-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="6"> @lang('No results found')!</td>
                                                        </tr>
                                                    @endif
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
                                {{$requestMoney->links('partials.pagination')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/Dashboard area-->



    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title"> @lang('Request Money Details')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2 offset-9 ">
                            <span class="received-status"></span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="margin-bottom-20"></div>
                        <div class="col-md-4">
                            <strong>@lang('Date')</strong>
                            <p class="padding-top-10 date"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>@lang('Sender')</strong>
                            <p class="padding-top-10 sender-username"></p>

                        </div>
                        <div class="col-md-4">
                            <strong>@lang('Receiver')</strong>
                            <p class="padding-top-10 receiver-username"></p>
                            <p class="receiver-email"></p>
                            <p class="receiver-phone"></p>
                        </div>
                    </div>




                    <div class="row mt-3">
                        <div class="margin-bottom-20"></div>

                        <div class="col-md-4">
                            <strong>@lang('Amount')</strong>
                            <p class="padding-top-10 send-amount"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>@lang('Fee')</strong>
                            <p class="padding-top-10 send-amount-fee"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>@lang('Total Amount')</strong>
                            <p class="padding-top-10 send-total-amount"></p>
                        </div>
                    </div>


                    <div class="row mt-4">
                        <div class="margin-bottom-20"></div>
                        <div class="col-md-12">
                            <strong>@lang('Title')</strong>
                            <p class="padding-top-10 receive-title"></p>
                        </div>
                    </div>

                    <span class="receive-info-status"></span>


                </div>
                <div class="modal-footer">

                    <span class="action-form"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>

        </div>
    </div>



@endsection


@section('script')
    <script>

        $(document).ready(function () {
            $('.details').on('click', function () {
                var id = $(this).data('id');
                var data = $(this).data('records');

                if(data.status == 0) {
                    $('.received-status').html(` <span class="badge badge-warning">@lang('Pending')</span>`);
                }else if(data.status == 1){
                    $('.received-status').html(` <span class="badge badge-success">@lang('Success')</span>`);
                    $('.action-form').html(``);
                }else if(data.status == -1){
                    $('.received-status').html(` <span class="badge badge-danger">@lang('Rejected')</span>`);
                    $('.action-form').html(``);
                }



                $('.date').text($(this).data('date'));
                $('.sender-username').text(data.user.username);

                $('.receiver-username').text(data.receiver.username);
                $('.receiver-email').text(data.receiver.email);
                $('.receiver-phone').text(data.receiver.mobile);

                $('.send-amount').text(`${data.amount}  ${data.currency.code}`);
                $('.send-amount-fee').text(`${data.charge}  ${data.currency.code}`);



                $('.send-total-amount').text($(this).data('total_amo') +  ` ${data.currency.code}` );

                $('.receive-title').text(`${data.title}`);

                if(data.info != null) {
                    var infoStatus = `
                    <div class="row mt-4 ">
                        <div class="margin-bottom-20"></div>
                        <div class="col-md-12">
                            <strong>@lang('Details')</strong>
                            <p class="padding-top-10"> ${data.info}</p>
                        </div>
                    </div>`;

                    $('.receive-info-status').html(infoStatus);
                }else{
                    $('.receive-info-status').html(``);
                }
            });

        });
    </script>

@endsection
