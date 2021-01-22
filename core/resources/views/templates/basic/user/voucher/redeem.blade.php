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

                                            @include(activeTemplate().'user.voucher.nav')


                                            <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
                                                <table class="table table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">@lang('Created Date')</th>
                                                        <th scope="col">@lang('Code')</th>
                                                        <th scope="col">@lang('Amount')</th>
                                                        <th scope="col">@lang('Useable Amount')</th>
                                                        <th scope="col">@lang('Charge')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($invests) >0)
                                                    @foreach($invests as $data)
                                                        <tr>
                                                            <td data-label="@lang('Date')">{!! date('d M, Y ', strtotime($data->updated_at)) !!} </td>
                                                            <td data-label="@lang('Code')">{{$data->code}}</td>
                                                            <td data-label="@lang('Amount')">
                                                                <strong>{{formatter_money($data->amount)}}   {{__($data->currency->code)}}</strong>
                                                            </td>
                                                            <td data-label="@lang('Useable Amount')">
                                                                <strong class="text-success">{{formatter_money($data->useable_amount)}}   {{__($data->currency->code)}}</strong>
                                                            </td>
                                                            <td data-label="@lang('Charge')">
                                                                <strong class="text-danger">{{formatter_money($data->use_charge)}}   {{__($data->currency->code)}}</strong>
                                                            </td>


                                                        </tr>
                                                    @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5"> @lang('No results found')!!</td>
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
                                {{$invests->links('partials.pagination')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection




@section('script')


@endsection
