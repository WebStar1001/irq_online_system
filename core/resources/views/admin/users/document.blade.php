@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center table-light">
                        <thead>
                        <tr>
                            <th scope="col">User ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">Document Name</th>
                            <th scope="col">Requested Time</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody class="list">
                        @forelse($docs as $doc)
                            <tr>
                                <td>
                                    {{$doc->user_id}}
                                </td>
                                <td>{{$doc->user->username}}</td>
                                <td>{{$doc->name}}</td>
                                <td>{{$doc->created_at}}</td>
                                <td>
                                    <a href="" class="btn btn-rounded btn-primary text-white" data-toggle="modal"
                                       data-target="#Modal{{$doc->id}}">
                                        <i class="fa fa-fw fa-desktop"></i></a></td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach($docs as $doc)
        <div class="modal fade" id="Modal{{$doc->id}}" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Document of {{$doc->user->username}}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <table class="table-striped table table-hover">
                            <tr>
                                <td>Username:</td>
                                <td>{{$doc->user->username}}</td>
                            </tr>
                            <tr>
                                <td>Document Name:</td>
                                <td>{{$doc->name}}</td>
                            </tr>
                            <tr>
                                <td>Document Photo:</td>
                                <th>
                                    <img src="{{ asset('assets/images/document') }}/{{$doc->photo}}"
                                         class="img-responsive" style="padding: 5px;">
                                </th>
                            </tr>
                            <tr>
                                <td>Document Details:</td>
                                <td>
                                    {!! $doc->details !!}
                                </td>
                            </tr>
                        </table>


                        <form role="form" method="POST"
                              action="{{route('admin.users.documentApprove', $doc->user->id)}}">
                            {{ csrf_field() }}
                            {{method_field('put')}}
                            <div class="form-group">
                                <label>Approve</label>
                                <input data-toggle="toggle" data-onstyle="success" data-on="Approved"
                                       data-off="Not Approved" data-offstyle="danger" data-width="100%" type="checkbox"
                                       value="1" name="docv" {{ $doc->user->docv == "1" ? 'checked' : '' }}>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-lg btn-block" type="submit">
                                    Update
                                </button>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>


                </div>


            </div>
        </div>
    @endforeach
@endsection

@push('breadcrumb-plugins')
    <form
        action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}"
        method="GET" class="form-inline">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="Username or email"
                   value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
