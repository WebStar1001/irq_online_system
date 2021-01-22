<div class="row justify-content-end">
    <div class="col-xl-8">
        <div class="essen-btns">
            <a href="{{route('user.transferIncoming')}}" class="@if(Request::routeIs('user.transferIncoming')) active @endif"><i class="fa fa-arrow-down"></i>@lang('Received')</a>
            <a href="{{route('user.transferOutgoing')}}" class="@if(Request::routeIs('user.transferOutgoing')) active @endif"><i class="fa fa-arrow-up"></i>@lang('Sent')</a>
            <a href="{{route('user.moneyTransfer')}}" class="@if(Request::routeIs('user.moneyTransfer')) active @endif"><i class="fa fa-paper-plane"></i>@lang('Send Now')</a>
        </div>
    </div>
</div>
