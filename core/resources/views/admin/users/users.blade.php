@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table align-items-center table-light">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Delete</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse($users as $user)
                        <tr>
                            <td scope="row">
                                <div class="media align-items-center">
                                    <a href="{{ route('admin.users.detail', $user->id) }}" class="avatar avatar-sm rounded-circle mr-3">
                                        <img src="{{ get_image(config('constants.user.profile.path') .'/'. $user->image) }}" alt="image">
                                    </a>
                                    <div class="media-body">
                                        <a href="{{ route('admin.users.detail', $user->id) }}"><span class="name mb-0">{{ $user->fullname }}</span></a>
                                    </div>

                                </div>
                            </td>
                            <td><a href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td><button class="btn btn-rounded btn-primary text-white deleteBtn" data-url="{{ route('admin.users.delete', $user->id) }}"><i class="fa fa-fw fa-trash"></i></button></td>
                            <td><a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-rounded btn-primary text-white"><i class="fa fa-fw fa-desktop"></i></a></td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer py-4">
                <nav aria-label="...">
                    {{ $users->appends($_GET)->links() }}
                </nav>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Remove User</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form method="post" action="" class="form-inline">
                @csrf
                {{method_field('delete')}}
                <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                <div class="modal-body">
                    <p class="text-muted">Are you sure you want to Delete ?</p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger deleteButton">Delete</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <form action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}" method="GET" class="form-inline">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="Username or email or account number" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
@push('script')
    <script>
        $('.deleteBtn').on('click', function() {
            var modal = $('#deleteModal');
            var url = $(this).data('url');

            modal.find('form').attr('action', url);
            modal.modal('show');
        });
    </script>
@endpush
