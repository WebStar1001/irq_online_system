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
                                        <h5 class="card-header">{{__($page_title)}}
                                            <a href="{{route('user.invoice.create') }}" class="bttn-small btn-emt"><i class="fa fa-plus"></i> @lang('Create Invoice')</a>
                                        </h5>

                                        <div class="card-body">

                                            <div class="table-responsive table-responsive-xl table-responsive-lg table-responsive-md table-responsive-sm">
                                                <table class="table table-striped">
                                                    <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">@lang('Name')</th>
                                                        <th scope="col">@lang('E-mail')</th>
                                                        <th scope="col">@lang('Amount')</th>
                                                        <th scope="col">@lang('Publish')</th>
                                                        <th scope="col">@lang('Payment Status')</th>
                                                        <th scope="col">@lang('Action')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @if(count($invoices) > 0)
                                                        @foreach($invoices as $key => $data)
                                                            <tr>
                                                                <td data-label="@lang('Name')">{{$data->name}}</td>
                                                                <td data-label="@lang('E-mail')"><strong>{{$data->email}}</strong></td>
                                                                <td data-label="@lang('Amount')">{{$data->amount}}   {{$data->currency->code}}</td>


                                                                <td data-label="@lang('Publish')">
                                                                    @if($data->published == 1)
                                                                        <span class="badge badge-success">@lang('Yes')</span>
                                                                    @else
                                                                        <span class="badge badge-danger">@lang('No')</span>
                                                                    @endif
                                                                </td>

                                                                <td data-label="@lang('Payment Status')">
                                                                    @if($data->status == 0)
                                                                        <span class="badge badge-warning">@lang('Unpaid')</span>
                                                                    @elseif($data->status == 1)
                                                                        <span class="badge badge-success">@lang('Paid')</span>
                                                                    @elseif($data->status == -1)
                                                                        <span class="badge badge-danger">@lang('Canceled')</span>
                                                                    @endif
                                                                </td>


                                                                <td data-label="@lang('Action')">
                                                                    <a href="{{route('user.invoice.edit',$data->trx)}}" title="Edit" class="btn btn-dark btn-sm"><i class="fa fa-eye" ></i></a>


                                                                    @if($data->status != -1)
                                                                        <a href="{{route('getInvoice.pdf',$data->trx)}}" class="btn btn-success btn-sm" title="@lang('Download')"><i class="fa fa-download"></i> </a>
                                                                    @endif

                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="6">@lang('No Data Found')</td>
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
                                {{$invoices->links('partials.pagination')}}
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
