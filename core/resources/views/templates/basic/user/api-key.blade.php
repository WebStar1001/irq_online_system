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
                                        <h5 class="card-header">
                                            {{__($page_title)}}

                                            <button class="btn btn-success float-right" data-toggle="modal" data-target="#keyModal">@lang('Generate Key')</button>

                                        </h5>
                                        <div class="card-body">
                                                <div class="row justify-content-end">
                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">

                                                        <div id="accordion">
                                                            @if(count($user->api_keys) > 0)
                                                            @foreach($user->api_keys as $k => $val)
                                                            <div class="card mb-2">
                                                                <div class="card-header" id="heading$k">
                                                                    <h5 class="mb-0">
                                                                        <button class="btn btn-link text-white text-decoration-none" data-toggle="collapse" data-target="#collapse{{$k}}" aria-expanded="true" aria-controls="collapse{{$k}}">
                                                                            {{$val->name}}
                                                                        </button>
                                                                        <button class="btn btn-danger delete-key float-right" data-id="{{$val->id}}" data-toggle="modal" data-target="#DelKeyModal"><i class="fa fa-trash"></i></button>
                                                                    </h5>
                                                                </div>

                                                                <div class="collapse show " aria-labelledby="heading{{$k}}" data-parent="#accordion">
                                                                    <div class="card-body">

                                                                        <p>@lang('Public Key') :  <strong>{{$val->public_key}}</strong></p>
                                                                        <p>@lang('Secret Key') :  <strong>{{$val->secret_key}}</strong></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach

                                                                @else

                                                            <p>@lang('No API Key Found!')</p>
                                                            @endif

                                                        </div>


                                                    </div>




                                                </div>


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


    <!-- Add Modal -->
    <div class="modal fade" id="keyModal" tabindex="-1" role="dialog" aria-labelledby="keyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="keyModalLabel">@lang('Generate New Key')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">@lang('Key Generate')</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Modal -->

    <div class="modal fade" id="DelKeyModal" tabindex="-1" role="dialog" aria-labelledby="keyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="keyModalLabel">@lang('Generate New Key')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="" method="post">
                    @csrf
                    @method('delete')
                    <div class="modal-body">
                        <input type="hidden" name="id" class="deleted-id">
                        <p>@lang('If you want to delete this, you will not make transaction this credentials')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">@lang('Remove')</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script>
        $(document).ready(function () {

            $('.delete-key').on('click', function () {
               $('.deleted-id').val($(this).data('id'));
            })

        })
    </script>

@endsection
