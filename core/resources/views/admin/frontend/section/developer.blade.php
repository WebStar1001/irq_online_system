@extends('admin.layouts.app')

@push('style-lib')
    <link href="{{ asset('assets/admin/css/bootstrap-fileinput.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script src="{{ asset('assets/admin/js/bootstrap-fileinput.js') }}"></script>
@endpush
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.frontend.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <div class="form-row">
                            <div class="col-md-4">

                                <div class="form-group">
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{ get_image(config('constants.frontend.developer.path') .'/'. $post->value->image) }})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload" name="image_input" id="profilePicUpload1" accept=".png">
                                                <label for="profilePicUpload1" class="bg-primary">Post image</label>
                                                <small class="mt-2 text-facebook">Supported files: <b>jpeg, jpg</b>. Image will be resized into </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label> Title</label>
                                    <input type="text" class="form-control" placeholder="Your  Title" name="title" value="{{ $post->value->title }}" required/>
                                </div>
                                <div class="form-group">
                                    <label> Content</label>
                                    <textarea rows="10" class="form-control nicEdit" placeholder=" description" name="details" required>{{ $post->value->details }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-row justify-content-center">
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-block btn-primary mr-2">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

