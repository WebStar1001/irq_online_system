@extends('admin.layouts.app')

@section('panel')
    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table align-items-center table-light">
                    <thead>
                        <tr>
                            <th scope="col">SL</th>
                            <th scope="col">Currency Name</th>
                            <th scope="col">Currency Symbol</th>
                            <th scope="col">Currency Rate</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($events as $k=>$mac)
                        <tr>
                            <td data-label="Sl">{{++$k}}</td>
                            <td data-label="Currency Name">{{$mac->name}}</td>
                            <td data-label="Currency Symbol">{{$mac->code}}</td>
                            <td data-label="Currency Rate"><strong>{{$mac->rate}} {{$mac->code}}</strong> </td>
                            <td data-label="Status">
                                <span class="badge  badge-pill  badge-{{ $mac->status ==0 ? 'warning' : 'success' }}">{{ $mac->status == 0 ? 'Deactive' : 'Active' }}</span>
                            </td>
                            <td data-label="Action">
                                <button type="button" class="btn btn-primary  btn-icon btn-pill edit_button"
                                        data-toggle="modal" data-target="#myModal"
                                        data-act="Edit"
                                        data-name="{{$mac->name}}"
                                        data-code="{{$mac->code}}"
                                        data-rate="{{$mac->rate}}"
                                        data-status="{{$mac->status}}"
                                        data-id="{{$mac->id}}">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="card-footer py-4">
                    <nav aria-label="...">
                        {{ $events->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit button -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><b class="abir_act"></b> Currency </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form method="post" action="{{route('admin.update.currency')}}">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <input class="form-control abir_id" type="hidden" name="id">
                        <div class="form-group">
                            <label> Currency Name :</label>
                            <input class="form-control form-control-lg abir_name" name="name" placeholder="Currency Name"
                                   required>
                        </div>

                        <div class="form-group">
                            <label> Currency Symbol :</label>
                            <input class="form-control form-control-lg abir_code" name="code" placeholder=" Currency Symbol"
                                   required>
                        </div>
                        <div class="form-group">
                            <label> Currency Rate :</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">1 {{$general->cur_text}} = </span></div>
                                <input type="text" class="form-control  form-control-lg abir_rate" name="rate"  placeholder="Amount">
                            </div>
                        </div>

                        <div class="form-group">
                            <label> Select Status :</label>
                            <select name="status" id="event-status" class="form-control form-control-lg abir_status" required>
                                <option value="">Status</option>
                                <option value="1">Active</option>
                                <option value="0">DeActive</option>
                            </select>
                            <br>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <!-- Modal for Edit button -->
    <div class="modal fade" id="apiKeyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Your Rate Api Key </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form method="post" action="{{route('admin.update.ApiKey')}}">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label> Api Key :</label>
                            <input class="form-control form-control-lg " name="currency_api_key" placeholder="Enter API key" value="{{$general->currency_api_key}}" required>
                        </div>
                        <span class="mt-4">This Api key provide by <a href="https://currencylayer.com" target="_blank">Currencylayer</a> </span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')

<div class="float-right">

    <button type="button" class="btn btn-dark btn-md  add_button"
            data-toggle="modal" data-target="#apiKeyModal">
        <i class="fa fa-spinner"></i> Add Your Api Key
    </button>

    <button type="button" class="btn btn-success btn-md  edit_button"
            data-toggle="modal" data-target="#myModal"
            data-act="Add New"
            data-name=""
            data-id="0">
        <i class="fa fa-plus"></i> Add Currency
    </button>
</div>
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            $(document).on("click", '.edit_button', function (e) {

                var name = $(this).data('name');
                var code = $(this).data('code');
                var rate = $(this).data('rate');
                var status = $(this).data('status');
                var id = $(this).data('id');
                var act = $(this).data('act');

                $(".abir_id").val(id);
                $(".abir_name").val(name);
                $(".abir_code").val(code);
                $(".abir_rate").val(rate);


                $(".abir_status").val(status).attr('selected', 'selected');
                $(".abir_act").text(act);

            });
        });
    </script>
@endpush
