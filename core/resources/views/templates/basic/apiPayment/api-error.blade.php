@extends(activeTemplate().'invoicePayment.layout')
@section('title','| '.$page_title)

@section('style')
    <style>
        .dashboard-content{
            height: 100vh;
        }
    </style>

@stop

@section('content')

    <section class="section-padding gray-bg " >
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">

                        <div class="row ">
                            <div class="col-lg-12">
                                <div style="text-align: center; margin-top: 10%;">
                                    <a href="javascript:void(0)">
                                        <img src="{{asset('assets/images/logoIcon/logo.png')}}" alt="logo" class="invoice-logo-filter"
                                             style="max-width: 100%;">
                                    </a>
                                </div>

                                    <br><br><br><h1 class="text-center " style="color: red;"> {{ __(session('error')) }} </h1><br><br>
                                    <br>
           
                                    <br><br><br><h1 class="text-center " style="color: red;"> {{ __(session('alert')) }} </h1><br><br>
                                    <br>
                   
                                    <br><br><br><h1 class="text-center bold" style="color: green;"> {{ __(session('success')) }} </h1><br><br>
                                    
                                    
                                    
                                    
                                @if (session('error'))
                                    <br><br><br><h1 class="text-center " style="color: red;"> {{ __(session('error')) }} </h1><br><br>
                                    <br>
                                @elseif(session('alert'))
                                    <br><br><br><h1 class="text-center " style="color: red;"> {{ __(session('alert')) }} </h1><br><br>
                                    <br>
                                @elseif (session('success'))
                                    <br><br><br><h1 class="text-center bold" style="color: green;"> {{ __(session('success')) }} </h1><br><br>

                                @else
                                    <br><br><br><h1 class="text-center" style="color: red;">AN UNESPECTED ERROR OCCURED. <br>
                                        PLEASE CHECK BACK WITH API OWNER.</h1><br><br><br>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>






@stop
