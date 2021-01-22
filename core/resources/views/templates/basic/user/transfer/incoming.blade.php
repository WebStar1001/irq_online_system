@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')
    <section class="section-padding gray-bg blog-area">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">

                            @if(count($moneyTransferProtected) > 0)
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content mb-4">
                                    <div class="card">
                                        <h5 class="card-header"> @lang('Protected Money')</h5>
                                        <div class="card-body">

                                            @foreach($moneyTransferProtected as $k=>$data)
                                                <button class="btn btn-dark btn-lg protectedDetails"  data-route="{{route('user.transferRelease',[encrypt($data->id)])}}"  data-resource="{{$data}}" data-toggle="modal" data-target="#protectedDetails">
                                                    {{formatter_money($data->amount)}} {{$data->currency->code}}
                                                </button>
                                                @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content ">
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
                                                        <th scope="col">@lang('Sender')</th>
                                                        <th scope="col">@lang('Amount')</th>
                                                        <th scope="col">@lang('Action')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($moneyTransfer as $data)
                                                        <tr>
                                                            <th data-label="@lang('Trx')">{{$data->trx}} </th>
                                                            <td data-label="@lang('Date')">{{date('d M Y', strtotime($data->created_at))}}</td>
                                                            <td data-label="@lang('Sender')">{{$data->sender->username}}</td>
                                                            <td data-label="@lang('Amount')">{{formatter_money($data->amount)}} {{$data->currency->code}}</td>

                                                            <td data-label="@lang('Action')">
                                                                <a href="javascript:void(0)" class="btn btn-dark btn-sm details"  data-resource="{{$data}}" data-toggle="modal" data-target="#exampleModal"><i class="ti ti-eye"></i></a>
                                                            </td>
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
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item">@lang('Sender Full Name') : <strong class="full-name"></strong></li>
                        <li class="list-group-item">@lang('Sender Email') : <strong class="sender-email"></strong></li>
                        <li class="list-group-item">@lang('Sender Contact Info') : <strong class="sender-contact"> </strong></li>
                        <li class="list-group-item">@lang('Receive Amount') : <strong class="receive-amount"></strong></li>
                        <li class="list-group-item">@lang('status') : <strong class="transfer-status"></strong></li>
                        <li class="list-group-item">@lang('Note') : <p class="transfer-note"></p></li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="protectedDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Details')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="" method="post" class="action-url">
                    @csrf
                    {{method_field('put')}}
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item">@lang('Sender Full Name') : <strong class="full-name"></strong></li>
                        <li class="list-group-item">@lang('Sender Email') : <strong class="sender-email"></strong></li>
                        <li class="list-group-item">@lang('Sender Contact Info') : <strong class="sender-contact"> </strong></li>
                        <li class="list-group-item">@lang('Receive Amount') : <strong class="receive-amount"></strong></li>
                        <li class="list-group-item">@lang('status') : <strong class="transfer-status"></strong></li>
                        <li class="list-group-item">@lang('Note') : <p class="transfer-note"></p></li>

                        <li class="list-group-item">
                            <div class="form-group">
                                <label>@lang('Enter Protection Code')</label>
                                <input type="text" name="code" value="{{old('code')}}" placeholder="@lang('Enter Protection Code')" class="form-control form-control-lg" required>
                            </div>
                        </li>
                    </ul>


                </div>

                <div class="modal-footer">
                    <span class="formAdd"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@section('script')
    <script>
        $(document).ready(function () {
            $('.details').on('click', function () {
                var data = $(this).data('resource');

                $('.full-name').text(`${data.sender.firstname + data.sender.lastname} `);
                $('.sender-email').text(`${data.sender.email}`);
                $('.sender-contact').text(`${data.sender.mobile}`);
                $('.receive-amount').text(`${data.amount}  ${data.currency.code}`);
                $('.transfer-note').text(`${data.note}`);


                if(data.status == 1) {
                    $('.transfer-status').html(`<span class="badge badge-success">@lang('Success')</span>`);
                }else if(data.status == 2){
                    $('.transfer-status').html(`<span class="badge badge-warning">@lang('Pending')</span>`);
                }else if(data.status == -2){
                    $('.transfer-status').html(`<span class="badge badge-danger">@lang('Cancel')</span>`);
                }
            })

        });


        $(document).ready(function () {
            $('.protectedDetails').on('click', function () {
                var data = $(this).data('resource');
                var route = $(this).data('route');
                $('.action-url').attr('action',route)

                $('.full-name').text(`${data.sender.firstname + data.sender.lastname} `);
                $('.sender-email').text(`${data.sender.email}`);
                $('.sender-contact').text(`${data.sender.mobile}`);
                $('.receive-amount').text(`${data.amount}  ${data.currency.code}`);
                $('.transfer-note').text(`${data.note}`);


                var form  = `<button type="submit" class="btn btn-primary">@lang('Active Now')</button>`;


                if(data.status == 1) {
                    $('.formAdd').html(``);
                    $('.transfer-status').html(`<span class="badge badge-success">@lang('Success')</span>`);
                }else if(data.status == 2){
                    $('.formAdd').html(form);
                    $('.transfer-status').html(`<span class="badge badge-warning">@lang('Pending')</span>`);
                }else if(data.status == -2){
                    $('.formAdd').html(``);
                    $('.transfer-status').html(`<span class="badge badge-danger">@lang('Cancel')</span>`);
                }
            })

        });
    </script>
@endsection
