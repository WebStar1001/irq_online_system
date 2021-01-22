<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ $general->sitename($page_title ?? '') }}</title>
  <link rel="shortcut icon" type="image/png" href="{{ get_image(config('constants.logoIcon.path') .'/favicon.png') }}"/>
  @stack('style-lib')
  
  <link rel="stylesheet" href="{{ asset('assets/admin/css/dashboard.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">

 
  @stack('style')
</head>
<body>
  @yield('content')

  <script src="{{ asset('assets/admin/js/dashboard.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/main.js') }}"></script>
  @stack('script-lib')

  <!-- Load toast -->
  @include('admin.partials.notify')

  <script src="{{ asset('assets/admin/js/nicEdit.js') }}"></script>
    {{-- LOAD NIC EDIT --}}
    <script type="text/javascript">
    bkLib.onDomLoaded(function() {
        $( ".nicEdit" ).each(function( index ) {
            $(this).attr("id","nicEditor"+index);
            new nicEditor({fullPanel : true}).panelInstance('nicEditor'+index,{hasPanel : true});
        });
    });
  </script>
  
  <script>$('[data-toggle=tooltip]').tooltip();</script>
  @stack('script')
</body>
</html>