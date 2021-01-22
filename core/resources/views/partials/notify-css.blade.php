@if($general->alert == 1)
    <link rel="stylesheet" href="{{ asset('assets/admin/css/iziToast.min.css') }}">
@elseif($general->alert == 2)
    <link rel="stylesheet" href="{{ asset('assets/admin/css/toastr.min.css') }}">
@endif
