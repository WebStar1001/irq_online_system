@extends('admin.layouts.app')

@section('panel')
<div class="row">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header font-weight-bold">
                <h4 class="float-left">Subject : {{ $ticket->subject }}</h4>

                <div class="float-right">
                @if($ticket->status == 0)
                        <span class="badge badge-primary">Open</span>
                @elseif($ticket->status == 1)
                        <span class="badge badge-success">Answered</span>
                @elseif($ticket->status == 2)
                        <span class="badge badge-info">Customer Replied</span>
                @elseif($ticket->status == 3)
                        <span class="badge badge-danger">Closed</span>
                @endif
                </div>
            </div>

            <div class="card-body ">

                <div class="accordion" id="accordionExample">

                    <div class="card">
                        <div class="card-header bg-dark-blue " id="headingThree">
                            <h2 class="mb-0 ">
                                <a class="btn btn-link collapsed  text-white  float-left accor" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <i class="fa fa-pencil"></i> @lang('Reply')
                                </a>


                                <a class="btn btn-link collapsed text-white  float-right accor" href="javascript:void(0)" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <i class="fa fa-plus"></i>
                                </a>

                            </h2>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">

                            <div class="card-body">



                                <div class="row">
                                    <div class="col-md-12">
                                        <form method="post" class="form-horizontal" action="{{ route('admin.ticket.send', $ticket->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                            <textarea class="form-control" name="message" rows="5" id="inputMessage"
                                      placeholder="Your Message ..."></textarea>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-sm-12">
                                                    <label for="inputAttachments">@lang('Attachments')</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="file" name="attachments[]" id="inputAttachments" class="form-control" />
                                                    <div id="fileUploadsContainer"></div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-primary btn-block" onclick="extraTicketAttachment()">
                                                        <i class="fa fa-plus"></i> @lang('Add More')
                                                    </button>
                                                </div>
                                                <div class="col-xs-12 ticket-attachments-message text-muted">
                                                    Allowed File Extensions: .jpg, .jpeg, .png, .pdf,
                                                </div>
                                            </div>

                                            <div class="row justify-content-center mt-4">

                                                <div class="col-md-4">
                                                @if($ticket->status != 4)
                                                        <button class="btn btn-primary  " type="submit"
                                                                name="replayTicket" value="1"><i
                                                                class="fa fa-fw fa-lg fa-check-circle"></i>Reply
                                                        </button>
                                                        <button class="btn btn-danger  " type="button"
                                                                data-toggle="modal" data-target="#DelModal"><i
                                                                class="fa fa-fw fa-lg fa-times-circle"></i>Close
                                                        </button>
                                                @else
                                                        <button class="btn btn-danger  " type="submit"
                                                                name="replayTicket" value="1"><i
                                                                class="fa fa-fw fa-lg fa-times-circle"></i>Closed
                                                        </button>
                                                @endif

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>





                <div class="messanger ">
                    <div class="messages ">
                        <ol class="commentlist noborder nomargin nopadding clearfix  ">
                            @foreach($messages as $message)
                                @if($message->type == 1)
                                    <div class="row">
                                        <div class="col-md-10">
                                            <li class="comment even thread-even depth-1 " id="li-comment-1">
                                                <div id="comment-1" class="comment-wrap clearfix">
                                                    <div class="comment-meta">
                                                        <div class="comment-author vcard">
                                                                <span class="comment-avatar clearfix">
                                                                        <img alt=""
                                                                             src="{{ get_image(config('constants.user.profile.path') .'/'. $ticket->user->image) }}"
                                                                             class="avatar avatar-60 photo avatar-default"
                                                                             width="60" height="60">

                                                                </span>

                                                        </div>
                                                    </div>
                                                    <div class="comment-content clearfix">
                                                        <div class="comment-author"><a
                                                                href="{{route('admin.users.detail', $ticket->user_id)}}">{{ $ticket->user->username }}</a>
                                                            <span>{{ $message->created_at->format('d F, Y - h:i A') }}</span>
                                                        </div>
                                                        <p>{{ $message->message }}</p>
                                                        @if($message->attachments()->count() > 0)
                                                            <div class="mt-2">
                                                                @foreach($message->attachments as $k=> $image)
                                                                    <a href="{{route('admin.ticket.download',Crypt::encrypt($image->id))}}" class="ml-4"><i class="fa fa-file"></i>  {{++$k}} @lang('File Download')</a>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <button data-id="{{$message->id}}" type="button"
                                                                data-toggle="modal" data-target="#DelMessage" class="btn btn-danger btn-sm float-right mt-2 delete-message"><i class="fa fa-trash"></i> Delete</button>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </li>
                                        </div>
                                    </div>
                                @elseif($message->type == 2)
                                    <div class="row">
                                        <div class="col-md-10 offset-md-2">

                                            <li class="comment even thread-even depth-1" id="li-comment-1">
                                                <div id="comment-1" class="comment-wrap clearfix">
                                                    <div class="comment-meta">
                                                        <div class="comment-author vcard">
                                                                <span class="comment-avatar clearfix">
                                                                    <img alt=""
                                                                         src="{{ asset('assets/images/logoIcon/logo.png') }}"
                                                                         class="avatar avatar-60 photo avatar-default"
                                                                         width="60" height="60"></span>
                                                        </div>
                                                    </div>
                                                    <div class="comment-content clearfix">
                                                        <div class="comment-author">
                                                            Me<span>{{ $message->created_at->format('d F, Y - h:i A') }}</span>
                                                        </div>
                                                        <p>{{ $message->message }}</p>
                                                        @if($message->attachments()->count() > 0)
                                                            <div class="mt-2">
                                                                @foreach($message->attachments as $k=>$image)
                                                                    <a href="{{route('admin.ticket.download',encrypt($image->id))}}" class="ml-4"><i class="fa fa-file"></i> {{++$k}} @lang('File Download')</a>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <button data-id="{{$message->id}}" type="button"
                                                                data-toggle="modal" data-target="#DelMessage" class="btn btn-danger btn-sm float-right mt-2  delete-message"><i class="fa fa-trash"></i> Delete</button>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </li>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class='fa fa-exclamation-triangle'></i><strong>Confirmation!</strong>
                        </h4>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">X</button>
                    </div>
                    <div class="modal-body">
                        <strong>Are you  want to Close This Support Ticket?</strong>
                    </div>
                    <div class="modal-footer">
                        <form method="post" action="{{ route('admin.ticket.send', $ticket->id) }}">
                            @csrf
                            @method('PUT')

                            <button type="submit" class="btn btn-primary custom-btn-background" name="replayTicket"
                                    value="2"><i class="fa fa-check"></i> Yes I'm Sure.
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>



        <div class="modal fade" id="DelMessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class='fa fa-exclamation-triangle'></i><strong>Confirmation!</strong>
                        </h4>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">X</button>
                    </div>
                    <div class="modal-body">
                        <strong>Are you sure to delete this?</strong>
                    </div>
                    <div class="modal-footer">
                        <form method="post" action="{{ route('admin.ticket.delete')}}">
                            @csrf
                            <input type="hidden" name="message_id" class="message_id">
                            <button type="submit" class="btn btn-primary "><i class="fa fa-check"></i> Yes I'm Sure.
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')

@endpush

@push('style')
    <link rel="stylesheet" href="{{asset('assets/admin/css/simplemde.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/ticket.css')}}">
@endpush

@push('script')
    <script src="{{asset('assets/admin/js/simplemde.min.js')}}"></script>

    <script>
        var simplemde = new SimpleMDE({ element: document.getElementById("inputMessage") });

        $(document).ready(function () {
            $('.card-body').scrollTop($('.card-body')[0].scrollHeight);


            $('.delete-message').on('click', function (e) {
                $('.message_id').val($(this).data('id'));
            })

        });

        function extraTicketAttachment() {
            $("#fileUploadsContainer").append('<input type="file" name="attachments[]" class="form-control mt-1" required />')
        }


    </script>
@endpush
