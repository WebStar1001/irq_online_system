<?php

namespace App\Http\Controllers\API;

use App\ContactTopic;
use App\Http\Controllers\Controller;
use App\SupportAttachment;
use App\SupportMessage;
use App\SupportTicket;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
use Image;
use File;
use Validator;

class UserSupportTicketController extends Controller
{
    public function department()
    {
        $topics = ContactTopic::all();
        return response($topics, 200);
    }

    public function storeSupportTicket(Request $request)
    {
        $ticket = new SupportTicket();
        $message = new SupportMessage();

        $imgs = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

        $rules = [
            'attachments' => [
                'sometimes',
                'max:4096',
                function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                    foreach ($imgs as $img) {
                        $ext = strtolower($img->getClientOriginalExtension());
                        if (($img->getClientSize() / 1000000) > 2) {
                            return $fail("Images MAX  2MB ALLOW!");
                        }
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg, pdf images are allowed");
                        }
                    }
                    if (count($imgs) > 5) {
                        return $fail("Maximum 5 images can be uploaded");
                    }
                },
            ],
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'department' => 'required',
            'priority' => 'required',
            'message' => 'required',
        ];

        $validator = Validator($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()]);
        }

        $department = ContactTopic::where('id', $request->department)->first();
        if (!$department) {
            return response(['errors' => 'Invalid Department']);
        }

        $ticket->user_id = Auth::id();
        $random = rand(100000, 999999);

        $ticket->ticket = 'S-' . $random;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->subject = $request->subject;
        $ticket->department = $department->name;
        $ticket->priority = $request->priority;
        $ticket->status = 0;
        $ticket->save();

        $message->supportticket_id = $ticket->id;
        $message->support_type = 1;
        $message->message = $request->message;
        $message->save();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $image) {
                $filename = rand(1000, 9999) . time() . '.' . $image->getClientOriginalExtension();
                $image->move('assets/images/support', $filename);
                SupportAttachment::create([
                    'support_message_id' => $message->id,
                    'image' => $filename,
                ]);
            }
        }

        $response['success'] = 'Ticket created successfully!';
        $response['result'] = true;
        return response($response);
    }

    public function supportTicket()
    {
        $supports = SupportTicket::where('user_id', Auth::id())->latest()->paginate(15);
        $supports = resourcePaginate($supports, function ($data) use ($supports) {
            if ($data->status == 0) {
                $level = 'Open';
            } elseif ($data->status == 1) {
                $level = 'Answered';
            } elseif ($data->status == 2) {
                $level = 'Customer Replied';
            } elseif ($data->status == 3) {
                $level = 'Closed';
            }
            return [
                'date' => $data->created_at->format('d M, Y h:i A'),
                'ticket_number' => $data->ticket,
                'subject' => $data->subject,
                'status' => $level,
                'action' => route('message', $data->ticket),
            ];
        });
        return response($supports, 200);
    }

    public function supportMessage($ticket)
    {
        $my_ticket = SupportTicket::where('ticket', $ticket)->latest()->first();
        if (!$my_ticket) {
            return response(['errors' => 'Invalid Ticket']);
        }

        if ($my_ticket->status == 0) {
            $level = 'Open';
        } elseif ($my_ticket->status == 1) {
            $level = 'Answered';
        } elseif ($my_ticket->status == 2) {
            $level = 'Customer Replied';
        } elseif ($my_ticket->status == 3) {
            $level = 'Closed';
        }


        $messages = SupportMessage::where('supportticket_id', $my_ticket->id)->latest()->get()->map(function ($data) {

            if ($data->attachments()->count() > 0) {
                $attachments = [];
                foreach ($data->attachments as $k => $image) {
                    array_push($attachments, route('ticket.download', [$image->id]));

                }
            } else {
                $attachments = null;
            }
            return [
                'id' => $data->id,
                'user_type' => [
                    'type' => $data->type,
                    'user' => ($data->type == 1) ? 'user' : 'admin'
                ],
                'user_profile' => ($data->type == 1) ? get_image(config('constants.user.profile.path') . '/' . Auth::user()->image) : get_image(config('constants.logoIcon.path') . '/logo.png'),
                'user_name' => ($data->type == 1) ? $data->ticket->user->fullname : 'Admin',
                'date' => date('d M, Y - h:i A', strtotime($data->created_at)),
                'message' => $data->message,
                'attachments' => $attachments,
                'delete_link' => ($data->type == 1) ? route('ticket.delete', $data->id) : false
            ];
        });
        if ($my_ticket->user_id == Auth::id()) {

            $response['can_reply'] = ($my_ticket->status != 3) ? true : false;
            $response['ticket_status'] = $level;
            $response['messages'] = $messages;
            return response($response);
        } else {
            return response(['errors' => 'Invalid Ticket']);
        }
    }

    public function ticketDownload($ticket_id)
    {

        $attachment = SupportAttachment::where('id', $ticket_id)->first();
        if (!$attachment) {
            $response = ['errors' => ['Invalid Request To Download!']];
            return response($response);
        }


        $file = $attachment->image;
        $full_path = 'assets/images/support/' . $file;

        $title = str_slug($attachment->supportMessage->ticket->subject);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype = mime_content_type($full_path);


        header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }

    public function ticketDelete($message_id)
    {

        $message = SupportMessage::where('id', $message_id)->latest()->first();
        if (!$message) {
            return response(['errors' => 'Invalid Message']);
        }

        if ($message->ticket->user_id != Auth::id()) {
            return response(['errors' => 'Unauthorized']);
        }
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $img) {
                @unlink('assets/images/support/' . $img->image);
                $img->delete();
            }
        }
        $message->delete();

        $response['success'] = 'Delete successfully.';
        $response['result'] = true;
        return response($response, 200);
    }


    public function supportMessageStore(Request $request)
    {
        $rules = [
            'id' => 'required',
            'replayTicket' => [
                'required',
                Rule::in([1, 2]),
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all(), 422]);
        }

        $id = $request->id;
        $ticket = SupportTicket::where('id',$id)->first();
        if (!$ticket) {
            return response(['errors' => 'Invalid Ticket']);
        }
        $message = new SupportMessage();
        if ($ticket->status != 3) {

            if ($request->replayTicket == 1) {
                $imgs = $request->file('attachments');
                $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

                $rules =  [
                    'attachments' => [
                        'max:4096',
                        function ($attribute, $value, $fail) use ($imgs, $allowedExts) {
                            foreach ($imgs as $img) {
                                $ext = strtolower($img->getClientOriginalExtension());
                                if (($img->getClientSize() / 1000000) > 2) {
                                    return $fail("Images MAX  2MB ALLOW!");
                                }
                                if (!in_array($ext, $allowedExts)) {
                                    return $fail("Only png, jpg, jpeg, pdf images are allowed");
                                }
                            }
                            if (count($imgs) > 5) {
                                return $fail("Maximum 5 images can be uploaded");
                            }
                        },
                    ],
                    'message' => 'required',
                ];


                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response(['errors' => $validator->errors()->all(), 422]);
                }

                $ticket->status = 2;
                $ticket->save();

                $message->supportticket_id = $ticket->id;
                $message->support_type = 1;
                $message->message = $request->message;
                $message->save();

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $image) {
                        $filename = rand(1000, 9999) . time() . '.' . $image->getClientOriginalExtension();
                        $image->move('assets/images/support', $filename);
                        SupportAttachment::create([
                            'support_message_id' => $message->id,
                            'image' => $filename,
                        ]);
                    }
                }

                $response['success'] = 'Support ticket replied successfully!';
                $response['result'] = true;
                return response($response, 200);
            } elseif ($request->replayTicket == 2) {
                $ticket->status = 3;
                $ticket->save();

                $response['success'] = 'Support ticket closed successfully!';
                $response['result'] = true;
                return response($response, 200);
            }
        } else {
            return response(['errors' => 'Support ticket already closed!']);
        }

    }


}
