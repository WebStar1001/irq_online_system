@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body text-center border-bottom">
                <img src="{{ get_image(config('constants.user.profile.path') .'/'. $user->image) }}" alt="profile-image" class="user-image">
                <h5 class="card-title mt-3">{{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <p class="clearfix">
                    <span class="float-left">Username</span>
                    <span class="float-right font-weight-bold"><a href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a></span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Account No</span>
                    <span class="float-right font-weight-bold">{{ $user->account_number }}</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">E-mail</span>
                    <span class="float-right text-muted">{{ $user->email }}</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Phone</span>
                    <span class="float-right text-muted">{{ $user->mobile ?: 'Not available'}}</span>
                </p>

                <p class="clearfix">
                    <span class="float-left">Status</span>
                    <span class="float-right text-muted">
                        @switch($user->status)
                            @case(1)
                            <span class="badge badge-pill badge-success">Active</span>
                            @break
                            @case(2)
                            <span class="badge badge-pill badge-danger">Banned</span>
                            @break
                        @endswitch
                    </span>
                </p>

                @foreach($userWallet as $k => $val)

                    <ul class="list-group">
                        <li class="list-group-item"><span class="float-left">{{$val->currency->name}} :</span>
                            <span class="float-right text-dark font-weight-bold">{{formatter_money($val->amount)}} {{$val->currency->code}} </span></li>
                    </ul>
                @endforeach



            </div>



        </div>

    </div>
    <div class="col-lg-9">
        <div class="card">

            <div class="row p-4">
                <div class="col-lg-4">
                    <div class="card outline-success">
                        <div class="card-body">
                            <div class="media align-items-center">
                            <div class="media-body text-left">
                                <h4 class="mb-0 text-success"> {{ formatter_money($withdrawals) }}</h4>
                                <span class="text-success">Total Withdrawals</span>
                            </div>
                            <div class="align-self-center icon-lg">
                                <i class="fa fa-money text-success"></i>
                            </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.withdrawals', $user->id) }}" class="text-white text-center">
                            <div class="card-footer btn btn-block btn-success">View All</div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card outline-dark">
                        <div class="card-body">
                            <div class="media align-items-center">
                            <div class="media-body text-left">
                                <h4 class="mb-0 text-dark"> {{ formatter_money($deposits) }}</h4>
                                <span class="text-dark">Total Deposits</span>
                            </div>
                            <div class="align-self-center icon-lg">
                                <i class="fa fa-money text-dark"></i>
                            </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.deposits', $user->id) }}" class="text-white text-center">
                            <div class="card-footer btn btn-block btn-dark">View All</div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card outline-primary">
                        <div class="card-body">
                            <div class="media align-items-center">
                            <div class="media-body text-left">
                                <h4 class="mb-0 text-primary">{{ $transactions }}</h4>
                                <span class="text-primary">Total Transactions</span>
                            </div>
                            <div class="align-self-center icon-lg">
                                <i class="fa fa-exchange text-primary"></i>
                            </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.transactions', $user->id) }}" class="text-white text-center">
                            <div class="card-footer btn-block btn btn-primary">View All</div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <a class="text-white text-center btn-block" data-toggle="modal" href="#addSubModal">
                        <div class="card outline-primary">
                            <div class="card-body bg-primary">Add/Subtract Balance</div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('admin.users.login.history.single', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-success">
                            <div class="card-body">Login Logs</div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('admin.users.email.single', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-orange">
                            <div class="card-body">Send Email</div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('admin.users.moneyTransfer', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-blue">
                            <div class="card-body">Money Transfer</div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3">
                    <a href="{{ route('admin.users.moneyExchange', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-dark-blue">
                            <div class="card-body">Money Exchange</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="{{ route('admin.users.moneyRequest', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-dark">
                            <div class="card-body">Money Request</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="{{ route('admin.users.voucherLog', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-primary">
                            <div class="card-body">Voucher Log</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3">
                    <a href="{{ route('admin.users.invoiceLog', $user->id) }}" class="text-white text-center btn-block">
                        <div class="card  bg-success">
                            <div class="card-body">Invoice Log</div>
                        </div>
                    </a>
                </div>





            </div>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="firstname" value="{{ $user->firstname }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="lastname" value="{{ $user->lastname }}" required>
                            </div>
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="{{ $user->email }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone </label>
                                <input class="form-control" type="text" name="mobile" value="{{ $user->mobile }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">

                        <label>Address</label>
                        <br>
                        <small>Street</small>
                        <input class="form-control" type="text" value="{{ $user->address->address }}" name="address" placeholder="Street">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-lg-3">
                            <small>City</small>
                            <input class="form-control" type="text" value="{{ $user->address->city }}" name="city" placeholder="City">
                        </div>
                        <div class="form-group col-lg-3">
                            <small>State</small>
                            <input class="form-control" type="text" value="{{ $user->address->state }}" name="state" placeholder="State">
                        </div>
                        <div class="form-group col-lg-3">
                            <small>Zip/Postal</small>
                            <input class="form-control" type="text" value="{{ $user->address->zip }}" name="zip" placeholder="Zip/Postal">
                        </div>
                        <div class="form-group col-lg-3">
                            <small>Country</small>
                            <select name="country" class="form-control"> @include('partials.country') </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-lg-4">
                            <p class="text-muted">Status</p>
                            <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-onstyle="success"    data-offstyle="danger" data-on="Active" data-off="Banned"  data-width="100%" name="status" @if($user->status) checked @endif>
                        </div>

                        <div class="form-group col-lg-4">
                            <p class="text-muted">Email Verification</p>
                            <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Verified" data-off="Unverified" name="ev" @if($user->ev) checked @endif>
                        </div>

                        <div class="form-group col-lg-4">
                            <p class="text-muted">SMS Verification</p>
                            <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Verified" data-off="Unverified" name="sv" @if($user->sv) checked @endif>
                        </div>
                        <div class="form-group col-lg-4">
                            <p class="text-muted">Document Verification</p>
                            <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Verified" data-off="Unverified" name="docv" @if($user->docv) checked @endif>
                        </div>

                        <div class="form-group col-lg-4">
                            <p class="text-muted">2FA Status</p>
                            <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="On" data-off="Off" name="ts" @if($user->ts) checked @endif>
                        </div>

                        <div class="form-group col-lg-4">
                            <p class="text-muted">2FA Verification</p>
                            <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Verified" data-off="Unverified" name="tv" @if($user->tv) checked @endif>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input type="submit" class="btn btn-block btn-primary mt-2" value="Save Changes">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Sub Balance MODAL --}}
<div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add / Subtract Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.addSubBalance', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <input type="checkbox" data-width="100%" data-height="44px" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Add Balance" data-off="Subtract Balance" name="act" checked>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Select Currency</label>
                            <select name="currency" class="form-control">
                                @foreach($userWallet as $k => $val)
                                <option value="{{$val->id}}">{{$val->currency->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Amount<span class="text-danger">*</span></label>
                            <div class="input-group has_append">
                                <input type="text" name="amount" class="form-control" placeholder="Please provide positive amount">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Send Email MODAL --}}
<div id="sendEmailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.email.single', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Subject<span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" placeholder="Email Subject">
                        </div>
                        <div class="form-group col-md-12">
                            <label>Message<span class="text-danger">*</span></label>
                            <textarea rows="5" name="message" class="form-control nicEdit" placeholder="Your Message"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .user-image {
        width: 200px;
        height: 200px;
    }
</style>
@endpush

@push('script')
<script>
$("select[name=country]").val("{{ $user->address->country }}");
</script>
@endpush
