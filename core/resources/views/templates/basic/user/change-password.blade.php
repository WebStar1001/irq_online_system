@extends(activeTemplate().'layouts.user')
@section('title','')
@section('import-css')
@stop
@section('content')
    <!--Dashboard area-->
    <section class="section-padding gray-bg">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <h5 class="card-header">{{__($page_title)}}</h5>
                                        <div class="card-body">
                                            <form action="" method="post" name="editForm" enctype="multipart/form-data">
                                                @csrf

                                                <div class="row justify-content-center">
                                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8">
                                                        <label for="a-trans">@lang('Current Password')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="current_password"  placeholder="@lang('Current Password')" >
                                                    </div>

                                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8">
                                                        <label for="a-trans">@lang('New Password')</label>
                                                        <input type="password"  class="form-control form-control-lg" name="password"  placeholder="@lang('New Password')" >
                                                    </div>

                                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8">
                                                        <label for="a-trans">@lang('Confirm Password')</label>
                                                        <input type="password"  class="form-control form-control-lg" name="password_confirmation"  placeholder="@lang('Confirm Password')" >
                                                    </div>

                                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8">
                                                        <button type="submit" class="custom-btn">@lang('Change Password')</button>
                                                    </div>
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
        </div>
    </section><!--/Dashboard area-->

@endsection

@section('import-js')
@endsection
