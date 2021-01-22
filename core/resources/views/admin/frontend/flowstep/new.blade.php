@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <form action="{{ route('admin.frontend.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="key" value="flowstep">
                <div class="card-body">
                    <div class="form-row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Title" value="{{ old('title') }}" name="title" required />
                            </div>
                            <div class="form-group">
                                <label>Icon</label>
                                <div class="input-group has_append">
                                    <input type="text" class="form-control" name="icon" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary iconPicker"     data-icon="fas fa-home" role="iconpicker"></button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Details <span class="text-danger">*</span></label>
                                <textarea rows="10" class="form-control" placeholder="Details ..." name="details" required>{{ old('details') }}</textarea>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="card-footer">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-block btn-primary mr-2">Create</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.frontend.flowstep.index') }}" class="btn btn-dark" ><i class="fa fa-fw fa-reply"></i>Back</a>
@endpush



@push('style-lib')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"/>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-iconpicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
@endpush



@push('script')
    <script>
        $('.iconPicker').iconpicker({
            align: 'center', // Only in div tag
            arrowClass: 'btn-danger',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
            cols: 10,
            footer: true,
            header: true,
            icon: 'fas fa-bomb',
            iconset: 'fontawesome5',
            labelHeader: '{0} of {1} pages',
            labelFooter: '{0} - {1} of {2} icons',
            placement: 'bottom', // Only in button tag
            rows: 5,
            search: false,
            searchText: 'Search icon',
            selectedClass: 'btn-success',
            unselectedClass: ''
        }).on('change', function(e){
            $(this).parent().siblings('input[name=icon]').val(`<i class="${e.icon}"></i>`);
        });
    </script>

@endpush
