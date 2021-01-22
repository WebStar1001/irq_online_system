@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.frontend.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="key" value="faq">
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>FAQ Title</label>
                                <input type="text" class="form-control" placeholder="Your Post Title" name="title" value="{{old('title')}}"/>
                            </div>
                            
                            <div class="form-group">
                                <label>FAQ Content</label>
                                <textarea rows="10" class="form-control nicEdit" placeholder="Post description" name="body">{{old('body')}}</textarea>
                            </div>
                        </div>
                    </div>

                   
                </div>
                <div class="card-footer">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-lg btn-block btn-primary mr-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
