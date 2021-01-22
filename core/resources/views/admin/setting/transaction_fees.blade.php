@extends('admin.layouts.app')

@section('panel')

    <div class="row">

        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">


                    <div class="row mt-4">
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h4 class="card-title font-weight-normal">Money Transfer Charge</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.transaction-fees.update') }}" method="POST">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Percent Charge</label>
                                            <div class="col-sm-12">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="transfer_percent_charge" value="{{ $moneyTransfer->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Fix Charge</label>
                                            <div class="col-sm-12">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" placeholder="Fix Charge" name="transfer_fix_charge" value="{{ $moneyTransfer->fix_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Minimum Transfer</label>
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Minimum Transfer" name="minimum_transfer" value="{{ $moneyTransfer->minimum_transfer}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Maximum Transfer</label>
                                            <div class="col-sm-12">
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Maximum Transfer" name="maximum_transfer" value="{{ $moneyTransfer->maximum_transfer}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit"  name="sbtn" value="1" class="btn btn-primary mr-2 btn-block">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>






                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h4 class="card-title font-weight-normal">Request Money Charge</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.transaction-fees.update') }}" method="POST">
                                        @csrf

                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Percent Charge</label>
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="request_money_percent_charge" value="{{ $request_money->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                         <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Fix Charge</label>
                                            <div class="col-sm-12">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" placeholder="Fix Charge" name="request_money_fix_charge" value="{{ $request_money->fix_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                         <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Minimum Transfer</label>
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Minimum Transfer" name="request_money_minimum_transfer" value="{{ $request_money->minimum_transfer}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">Maximum Transfer</label>
                                            <div class="col-sm-12">
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Maximum Transfer" name="request_money_maximum_transfer" value="{{ $request_money->maximum_transfer}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <div class="form-group">
                                            <button type="submit"  name="sbtn" value="3" class="btn btn-primary mr-2 btn-block">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>







                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h4 class="card-title font-weight-normal">Voucher Charge</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.transaction-fees.update') }}" method="POST">
                                        @csrf

                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">New Voucher Percent Charge</label>
                                            <div class="col-sm-12">
                                                <div class="input-group ">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="new_voucher_percent_charge" value="{{ $newVoucher->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">New Voucher Fix Charge</label>
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Fix Charge" name="new_voucher_fix_charge" value="{{ $newVoucher->fix_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label">New Voucher Minimum Amount</label>
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Minimum Amount" name="new_voucher_minimum_amount" value="{{ $newVoucher->minimum_amount}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class=" row">
                                            <div class="form-group col-sm-6">

                                                <label class=" col-form-label">Active Voucher Percent Charge</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="active_voucher_percent_charge" value="{{ $activeVoucher->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group col-sm-6">

                                                <label class="col-form-label">Active Voucher Fix Charge</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Fix Charge" name="active_voucher_fix_charge" value="{{ $activeVoucher->fix_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <button type="submit"  name="sbtn" value="5" class="btn btn-primary mr-2 btn-block mt-3">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-4">


                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h4 class="card-title font-weight-normal">Invoice Charge</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.transaction-fees.update') }}" method="POST">
                                        @csrf

                                        <div class="form-group row">
                                            <div class="col-sm-6">

                                                <label class="col-form-label">Percent Charge</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="invoice_percent_charge" value="{{ $invoice->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">

                                                <label class=" col-form-label">Fix Charge</label>
                                                <div class="input-group mb-1">
                                                    <input type="text" class="form-control" placeholder="Fix Charge" name="invoice_fix_charge" value="{{ $invoice->fix_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group ">
                                            <button type="submit"  name="sbtn" value="4" class="btn btn-primary mr-2 btn-block mt-4">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h4 class="card-title font-weight-normal">Money Exchange Charge</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.transaction-fees.update') }}" method="POST">
                                        @csrf
                                        <div class="form-group row">
                                            <div class="col-sm-12">

                                                <label class="col-form-label">Percent Charge</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="exchange_percent_charge" value="{{ $money_exchange->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit"  name="sbtn" value="2" class="btn btn-primary mr-2 btn-block">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h4 class="card-title font-weight-normal">API Charge</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.transaction-fees.update') }}" method="POST">
                                        @csrf
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label class="col-form-label">Percent Charge</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Percent Charge" name="api_percent_charge" value="{{ $api_charge->percent_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <label class="col-form-label">Fix Charge</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Fix Charge" name="api_fix_charge" value="{{ $api_charge->fix_charge}}" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">{{$general->cur_text}}</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <button type="submit"  name="sbtn" value="6" class="btn btn-primary mr-2 btn-block">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>





                    </div>





                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
@endpush

@push('style')
    <style>
        .sp-replacer {
            padding: 0;
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: 5px 0 0 5px;
            border-right: none;
        }

        .sp-preview {
            width: 100px;
            height: 44px;
            border: 0;
        }

        .sp-preview-inner {
            width: 110px;
        }

        .sp-dd {
            display: none;
        }

        .input-group > .form-control:not(:first-child) {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
    </style>
@endpush

@push('style-lib')
@endpush

@push('script')
@endpush
