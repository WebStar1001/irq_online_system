@extends(activeTemplate().'layouts.user')
@section('title','')
@section('import-css')
    <link href="{{ asset('assets/admin/css/bootstrap-fileinput.css') }}" rel="stylesheet">
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

                                                <div class="row justify-content-end">
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('First Name')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="firstname" value="{{$user->firstname}}" placeholder="@lang('First Name')" >
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('Last Name')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="lastname" value="{{$user->lastname}}" placeholder="@lang('Last Name')" >
                                                    </div>

                                                    @if($user->merchant == 2)
                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('Company Name')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="company_name" value="{{$user->company_name}}" placeholder="@lang('Company Name')" >
                                                    </div>
                                                    @endif


                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('Email Address')</label>
                                                        <input type="email"  class="form-control form-control-lg" name="email" value="{{$user->email}}" placeholder="@lang('Email Address')" >
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('Contact Number')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="mobile" value="{{$user->mobile}}" placeholder="@lang('Contact Number')" >
                                                    </div>



                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('Address')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="address" value="{{$user->address->address}}" placeholder="@lang('Address')" >
                                                    </div>

                                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                        <label for="a-trans">@lang('State')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="state" value="{{$user->address->state}}" placeholder="@lang('State')" >
                                                    </div>

                                                    <div class=" @if($user->merchant == 2) col-xl-6 col-lg-6 col-md-6  @else  col-xl-4 col-lg-4 col-md-4  @endif col-sm-6">
                                                        <label for="a-trans">@lang('Zip')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="zip" value="{{$user->address->zip}}" placeholder="@lang('Zip')" >
                                                    </div>

                                                    <div class="@if($user->merchant == 2) col-xl-6 col-lg-6 col-md-6  @else  col-xl-4 col-lg-4 col-md-4  @endif col-sm-6">
                                                        <label for="a-trans">@lang('City')</label>
                                                        <input type="text"  class="form-control form-control-lg" name="city" value="{{$user->address->city}}" placeholder="@lang('City')" >
                                                    </div>



                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="currency">@lang('Country')</label>
                                                        <select class="form-control form-control-lg" name="country">
                                                            @include('partials.country')
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                        <div class="form-group">
                                                            <div class="fileinput fileinput-new " data-provides="fileinput">
                                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;"
                                                                     data-trigger="fileinput">
                                                                    <img  src="{{ get_image(config('constants.user.profile.path') .'/'. $user->image) }}" alt="...">

                                                                </div>
                                                                <div class="fileinput-preview fileinput-exists thumbnail"
                                                                     style="max-width: 200px; max-height: 150px"></div>

                                                                <div class="img-input-div">
                                                                    <span class="btn btn-info btn-file">
                                                                        <span class="fileinput-new "> @lang('Select image')</span>
                                                                        <span class="fileinput-exists"> @lang('Change')</span>
                                                                        <input type="file" name="image" accept="image/*">
                                                                    </span>
                                                                    <a href="#" class="btn btn-danger fileinput-exists"
                                                                       data-dismiss="fileinput"> @lang('Remove')</a>
                                                                </div>

                                                                <code>@lang('Image size 800*800')</code>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-6">
                                                        <button type="submit" class="custom-btn">@lang('Update Profile')</button>
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

    <script>
        document.forms['editForm'].elements['country'].value = "{{$user->address->country}}"
    </script>
@endsection

@section('import-js')
    <script src="{{ asset('assets/admin/js/bootstrap-fileinput.js') }}"></script>
@endsection
