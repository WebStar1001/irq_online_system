@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')

    <section class="section-padding gray-bg">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <h5 class="card-header">{{__($page_title)}}</h5>
                                        <form method="POST" action="{{route('user.confirm.transfer')}}">
                                            {{csrf_field()}}
                                        <div class="card-body">
                                            <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
                                                <table class="table table-striped">

                                                    <tbody>
                                                    <tr>
                                                        <th scope="row"> @lang('Amount') : </th>
                                                        <td>{{formatter_money($transfer->amount)}} {{$transfer->currency->code}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"> @lang('Charge') :</th>
                                                        <td>{{formatter_money($transfer->charge)}} {{$transfer->currency->code}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"> @lang('Payable') :</th>
                                                        <td> {{formatter_money(($transfer->amount+$transfer->charge))}} {{$transfer->currency->code}}</td>
                                                    </tr>
                                                    @if (Session::has('sender_wallet'))
                                                    <tr>
                                                        <th scope="row"> @lang('Remaining') {{$transfer->currency->code }} @lang('Balance') </th>
                                                        <td>{{__(Session::get('sender_wallet'))}} {{$transfer->currency->code}}</td>
                                                    </tr>
                                                    @endif

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @if($transfer->status ==0)
                                            <div class="card-footer">
                                                <input type="hidden" name="id" value="{{Crypt::encrypt($transfer->id)}}">
                                                <button type="submit" class="custom-btn"
                                                        id="btn-confirm">@lang('Confirm Now')
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
