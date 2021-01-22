<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
    <aside class="sidebar">
        <ul>
            <li class="@if(Request::routeIs('user.moneyTransfer')) active @endif"><a href="{{route('user.moneyTransfer')}}"><i class="ti-direction"></i>@lang('Money Transfer')</a></li>
            <li class="@if(Request::routeIs('user.home')) active @endif"><a href="{{route('user.home')}}"><i class="ti-direction-alt"></i>@lang('Transactions')</a></li>
            <li class="@if(Request::routeIs('user.exchange')) active @endif"><a href="{{route('user.exchange')}}"><i class="ti-exchange-vertical"></i>@lang('Currency Exchange')</a></li>
            <li class="@if(Request::routeIs('user.request-money.inbox')) active @endif"><a href="{{route('user.request-money.inbox')}}"><i class="ti-wallet"></i>@lang('Request Money')</a></li>

            <li class="@if(Request::routeIs('user.invoice')) active @endif"><a href="{{route('user.invoice')}}"><i class="ti-check-box"></i>@lang('Invoice')</a></li>

            <li class="@if(Request::routeIs('user.vouchers')) active @endif"><a href="{{route('user.vouchers')}}"><i class="ti-clipboard"></i>@lang('Voucher')</a></li>

            <li class="@if(Request::routeIs('user.ticket')) active @endif"><a href="{{route('user.ticket')}}"><i class="ti-help-alt"></i>@lang('Support')</a></li>

        </ul>
    </aside>
</div>
