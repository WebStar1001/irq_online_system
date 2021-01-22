@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive table-responsive-xl">
                <table class="table align-items-center table-light">
                    <thead>
                        <tr>
                            <th scope="col">SL</th>
                            <th scope="col">User</th>
                            <th scope="col">Date</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Ticket</th>
                            <th scope="col">Department</th>
                            <th scope="col">status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse($items as $item)
                        <tr>
                            <td data-label="SL">{{$loop->index+1}}</td>
                            <td data-label="User"><a href="{{ route('admin.users.detail', $item->user_id)}}"> {{$item->user->firstname}} {{$item->user->lastname}}</a></td>

                            <td data-label="Date">{{ $item->created_at->format('d F, Y H:i A') }}</td>
                            <td data-label="Subject">{{ $item->subject }} </td>
                            <td data-label="Ticket">{{ $item->ticket }} </td>

                            <td data-label="Department"><strong>{{ $item->department }}</strong> </td>
                            <td data-label="Status">
                                @if($item->status == 0)
                                    <span class="badge badge-primary">Open</span>
                                @elseif($item->status == 1)
                                    <span class="badge badge-success ">Answered</span>
                                @elseif($item->status == 2)
                                    <span class="badge badge-info ">Customer Replied</span>
                                @elseif($item->status == 3)
                                    <span class="badge badge-danger ">Closed</span>
                                @endif
                            </td>
                            <td data-label="Action">
                                <a href="{{ route('admin.ticket.reply', $item->id) }}" class="btn btn-primary btn-sm btn-icon btn-pill"><i
                                        class="fa fa-eye"></i></a>
                            </td>
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
                    {{ $items->appends($_GET)->links() }}
                </nav>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')

@endpush
