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

                                                <div class="form-group">
                                                    <label>Document Name</label>
                                                    <select name="name">
                                                        <option>Passport</option>
                                                        <option>Front ID Card</option>
                                                        <option>Back ID Card</option>
                                                        <option>Other Document</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Document Photo</label>
                                                    <input type="file" name="photo" class="form-control input-lg">
                                                    <span
                                                        style="color: #ff6600;">Standard Image Size: 400 x 300 px</span>
                                                </div>

                                                <div class="form-group">
                                                    <label for="details">Account Details</label>
                                                    <textarea class="form-control" id="details" name="details" rows="5">
			</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-block btn-lg btn-primary">Send
                                                        Verify Request
                                                    </button>
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
    <script src="{{ asset('assets/admin/js/bootstrap-fileinput.js') }}"></script>
@endsection
