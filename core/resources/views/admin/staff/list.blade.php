@extends('admin.layouts.app')

@section('panel')
    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table align-items-center table-light">
                    <thead>
                        <tr>

                            <th scope="col">SL</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                    @forelse($events as $k=>$mac)
                        <tr>
                            <td>{{++$k}}</td>
                            <td><strong>{{$mac->username}}</strong></td>
                            <td>{{$mac->email}}</td>
                            <td>{{$mac->mobile}}</td>
                            <td>
                                <span class="badge  badge-pill  badge-{{ $mac->status ==0 ? 'warning' : 'success' }}">{{ $mac->status == 0 ? 'Deactive' : 'Active' }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary  btn-icon btn-pill edit_button"
                                        data-toggle="modal" data-target="#myModal{{$mac->id}}">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                        </tr>


                        <!-- Modal for Edit button -->
                        <div class="modal fade" id="myModal{{$mac->id}}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel">Manage Staff </h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    </div>
                                    <form method="post" action="{{route('admin.updateStaff',$mac)}}">
                                        {{ method_field('put') }}
                                        {{ csrf_field() }}
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label> Name :</label>
                                                    <input class="form-control form-control-lg" name="name" placeholder="Staff Name" value="{{$mac->name}}" required>
                                                </div>


                                                <div class="form-group col-md-6">
                                                    <label> Username :</label>
                                                    <input class="form-control form-control-lg" name="username" placeholder="Username" value="{{$mac->username}}" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label> E-Mail :</label>
                                                    <input class="form-control form-control-lg" name="email" placeholder="Email Address" value="{{$mac->email}}" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label> Mobile :</label>
                                                    <input class="form-control form-control-lg" name="mobile" placeholder="Mobile Number" value="{{$mac->mobile}}" required>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label> Password :</label>
                                                    <input type="password" name="password" placeholder="Password"  class="form-control form-control-lg" value="">
                                                </div>


                                                <div class="form-group col-md-6">
                                                    <label> Select Status :</label>
                                                    <select name="status" id="event-status" class="form-control form-control-lg" required>
                                                        <option value="">Status</option>
                                                        <option value="1" @if($mac->status == 1) selected @endif>Active</option>
                                                        <option value="0"  @if($mac->status == 0) selected @endif>DeActive</option>
                                                    </select>
                                                    <br>
                                                </div>


                                                <div class="form-group col-md-12">
                                                    <div class="card">
                                                        <div class="card-header"><h5 class="text-center">Accessibility</h5></div>
                                                        <div class="card-body select-all-access">
                                                            <div class="row mt-2">
                                                                <div class="col-md-12"><label><input type="checkbox" class="selectAll" name="accessAll"> Select All</label></div>
                                                                <hr>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="1" name="access[]" @if(in_array('1',json_decode($mac->access))) checked @endif> Dashboard</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="2" name="access[]" @if(in_array('2',json_decode($mac->access))) checked @endif> Manage Currency</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="3" name="access[]" @if(in_array('3',json_decode($mac->access))) checked @endif> Manage Staffs</label></div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="4" name="access[]" @if(in_array('4',json_decode($mac->access))) checked @endif> Manage Users</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="5" name="access[]" @if(in_array('5',json_decode($mac->access))) checked @endif> Withdraw Systems</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="6" name="access[]" @if(in_array('6',json_decode($mac->access))) checked @endif> Deposit Systems</label></div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="7" name="access[]" @if(in_array('7',json_decode($mac->access))) checked @endif> Subscribers</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="8" name="access[]" @if(in_array('8',json_decode($mac->access))) checked @endif> Reports</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="9" name="access[]" @if(in_array('9',json_decode($mac->access))) checked @endif> Support Tickets</label></div>
                                                            </div>


                                                            <div class="row mt-2">
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="10" name="access[]" @if(in_array('10',json_decode($mac->access))) checked @endif> Plugins</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="11" name="access[]" @if(in_array('11',json_decode($mac->access))) checked @endif> Frontend Manager</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="12" name="access[]" @if(in_array('12',json_decode($mac->access))) checked @endif> General Settings</label></div>
                                                            </div>
                                                            <div class="row mt-2">
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="13" name="access[]" @if(in_array('13',json_decode($mac->access))) checked @endif> Email Manager</label></div>
                                                                <div class="col-md-4"><label><input type="checkbox" class="select-access" value="14" name="access[]" @if(in_array('14',json_decode($mac->access))) checked @endif> SMS  Manager</label></div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>

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
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Manage Staff </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form method="post" action="{{route('admin.storeStaff')}}">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label> Name :</label>
                                <input class="form-control form-control-lg" name="name" placeholder="Staff Name" value="{{old('name')}}" required>
                            </div>


                            <div class="form-group col-md-6">
                                <label> Username :</label>
                                <input class="form-control form-control-lg" name="username" placeholder="Username" value="{{old('username')}}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label> E-Mail :</label>
                                <input class="form-control form-control-lg" name="email" placeholder="Email Address" value="{{old('email')}}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label> Mobile :</label>
                                <input class="form-control form-control-lg" name="mobile" placeholder="Mobile Number" value="{{old('mobile')}}" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label> Password :</label>
                                <input type="password" name="password" placeholder="Password"  class="form-control form-control-lg" value="">
                            </div>


                            <div class="form-group col-md-6">
                                <label> Select Status :</label>
                                <select name="status" id="event-status" class="form-control form-control-lg" required>
                                    <option value="">Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">DeActive</option>
                                </select>
                                <br>
                            </div>


                            <div class="form-group col-md-12">
                                <div class="card">
                                    <div class="card-header"><h5 class="text-center">Accessibility</h5></div>
                                    <div class="card-body select-all-access">
                                        <div class="row mt-2">
                                            <div class="col-md-12"><label><input type="checkbox" class="selectAll" name="accessAll"> Select All</label></div>
                                            <hr>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="1" name="access[]"> Dashboard</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="2" name="access[]"> Manage Currency</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="3" name="access[]"> Manage Staffs</label></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="4" name="access[]"> Manage Users</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="5" name="access[]"> Withdraw Systems</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="6" name="access[]"> Deposit Systems</label></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="7" name="access[]"> Subscribers</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="8" name="access[]"> Reports</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="9" name="access[]"> Support Tickets</label></div>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="10" name="access[]"> Plugins</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="11" name="access[]"> Frontend Manager</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="12" name="access[]"> General Settings</label></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="13" name="access[]"> Email Manager</label></div>
                                            <div class="col-md-4"><label><input type="checkbox" class="select-access" value="14" name="access[]"> SMS  Manager</label></div>
                                        </div>





                                    </div>
                                </div>
                            </div>

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
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-success btn-md float-right edit_button"
            data-toggle="modal" data-target="#addModal"
            data-act="Add New"
            data-name=""
            data-id="0">
        <i class="fa fa-user-plus"></i> Add Staff
    </button>
@endpush



@push('script')
    <script>
        $(document).ready(function () {
            $('.selectAll').on('click', function () {
                if($(this).is(':checked')){
                    $(this).parents('.select-all-access').find('.select-access').attr('checked','checked')
                }else {
                    $(this).parents('.select-all-access').find('.select-access').removeAttr('checked')
                }
            })
        })

    </script>
@endpush
