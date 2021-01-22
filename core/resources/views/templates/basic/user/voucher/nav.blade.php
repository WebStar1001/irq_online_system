<div class="row justify-content-end">
    <div class="col-xl-8">
        <div class="essen-btns">
            <a href="{{route('user.vouchers')}}" class="@if(Request::routeIs('user.vouchers')) active @endif"><i class="fa fa-list"></i> @lang('My Vouchers')
            </a>
            <a href="{{route('user.vouchers.active_code')}}" class="@if(Request::routeIs('user.vouchers.active_code')) active @endif"><i class="fa fa-key"></i>@lang('Redeem')</a>
            <a href="{{route('user.vouchers.redeemLog')}}" class="@if(Request::routeIs('user.vouchers.redeemLog')) active @endif"><i class="fa fa-list-alt"></i>@lang('Redeem Log')</a>
            <a href="{{route('user.vouchers.new_voucher')}}" class="@if(Request::routeIs('user.vouchers.new_voucher')) active @endif"><i class="fa fa-plus"></i> @lang('Create')</a>
        </div>
    </div>
</div>
