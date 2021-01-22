<div class="row justify-content-end">
    <div class="col-xl-8">
        <div class="essen-btns">
            <a href="{{route('user.request-money.inbox')}}" class="@if(Request::routeIs('user.request-money.inbox')) active @endif"><i class="fas fa-allergies"></i> @lang('Request To Me')
            </a>
            <a href="{{route('user.request-money.sent')}}" class="@if(Request::routeIs('user.request-money.sent')) active @endif"><i class="fas fa-hand-holding-usd"></i> @lang('My Request')</a>
            <a href="{{route('user.request-money.create')}}" class="@if(Request::routeIs('user.request-money.create')) active @endif"><i class="fa fa-plus"></i> @lang('Request Now')</a>
        </div>


    </div>
</div>
