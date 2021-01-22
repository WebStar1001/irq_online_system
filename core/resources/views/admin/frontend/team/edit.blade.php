@extends('admin.layouts.app')

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
                                        <div class="profilePicPreview" style="background-image: url({{ get_image(config('constants.frontend.team.path') .'/'. $post->value->image) }})">
                                                <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" class="profilePicUpload" name="image_input" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                            <label for="profilePicUpload1" class="bg-primary">Post image</label>
                                            <small class="mt-2 text-facebook">Supported files: <b>jpeg, jpg</b>. Image will be resized into <b>{{ Config::get('constants.frontend.team.size') }}px</b> </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                                <div class="form-group">
                                    <label>Member Name</label>
                                    <input type="text" class="form-control" placeholder="Member Name" name="name" value="{{ @$post->value->name }}" required/>
                                </div>
                                <div class="form-group">
                                    <label>Member Designation</label>
                                    <input type="text" class="form-control" placeholder="Member Designation" name="designation" value="{{ @$post->value->designation }}" required/>
                                </div>
                            <div class="form-group">
                                <label>Facebook Link</label>
                                <input type="url" class="form-control" placeholder="Facebook Link" name="facebook" value="{{ @$post->value->facebook }}"/>
                            </div>
                            <div class="form-group">
                                <label>Twitter Link</label>
                                <input type="url" class="form-control" placeholder="Twitter Link" name="twitter" value="{{ @$post->value->twitter }}"/>
                            </div>
                            <div class="form-group">
                                <label>Linkedin Link</label>
                                <input type="url" class="form-control" placeholder="Linkedin Link" name="linkedin" value="{{ @$post->value->linkedin }}"/>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-block btn-primary mr-2">Update+</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.frontend.team.index') }}" class="btn btn-dark" ><i class="fa fa-fw fa-reply"></i>Back</a>
@endpush
