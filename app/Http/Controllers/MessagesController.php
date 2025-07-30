<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Flasher\Toastr\Prime\ToastrInterface;


use function Flasher\Toastr\Prime\toastr;

class MessagesController extends Controller
{
    function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('messages.index') => __('breadcrumbs.messages.index')
        ];

        return view('messages.index', ['breadcrumbs' => $breadcrumbs]);
    }

    function show(Message $message)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('messages.index') => __('breadcrumbs.messages.index'),
            route('messages.show', $message) => $message->subject
        ];

        $messages = Message::where('thread_id', $message->thread_id)
        ->with(['from', 'recipients', 'attachedFiles'])
        ->orderBy('created_at', 'desc')->get();

        $message->recipients()->where('user_id', auth()->id())->update(['is_read' => true, 'read_at' => now()]);

        return view('messages.show', ['messages' => $messages, 'currentMessage' => $message, 'breadcrumbs' => $breadcrumbs]);
    }

    function create(Request $request)
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('messages.index') => __('breadcrumbs.messages.index'),
            route('messages.create') => __('breadcrumbs.messages.create')
        ];

        if($request->has('reply_to')) {
            $message = Message::find($request->reply_to);
            $recipients = $message->recipients->pluck('id')->toArray();
            $recipients[] = $message->sender_id;

            return view('messages.create', ['message' => $message, 'recipients' => $recipients, 'breadcrumbs' => $breadcrumbs]);
        }

        if($request->has('to')) {
            $user = User::find($request->to);
            return view('messages.create', ['recipients' => [$user->id], 'breadcrumbs' => $breadcrumbs]);
        }
        
        return view('messages.create', ['breadcrumbs' => $breadcrumbs]);
    }

    function downloadAttachment(MessageAttachment $attachment)
    {
        return response()->download(storage_path('app/private/' . $attachment->path), $attachment->filename);
    }

    function downloadAllAttachments(Message $message)
    {
        $zip = new \ZipArchive();
        $zipFileName = storage_path('app/private/attachments.zip');
        if($zip->open($zipFileName, \ZipArchive::CREATE) === true) {
            foreach($message->attachedFiles as $attachment) {
                $zip->addFile(storage_path('app/private/' . $attachment->path), $attachment->filename);
            }
            $zip->close();
        }

        return response()->download($zipFileName, 'attachments.zip');
    }
    function store(Request $request)
    {
        if(!$request->recipients) {
            flash()->error('Please select at least one recipient');
            return back();
        }

        $attachmentFiles = [];
        if($request->has('attachments')) {
            foreach($request->attachments as $attachment) {
                $attachmentFiles[] = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $attachment->store('attachments'),
                    'size' => $attachment->getSize(),
                    'mime_type' => $attachment->getMimeType(),
                ];
            }
        }

        $threadId = $request->reply_to ? Message::find($request->reply_to)->thread_id : uniqid('', true);


        $message = Message::create([
            'sender_id' => auth()->id(),
            'subject' => $request->subject,
            'content' => $request->content,
            'thread_id' => $threadId,
            'reply_to' => $request->reply_to
        ]);


        if(count($attachmentFiles) > 0) {
            foreach($attachmentFiles as $attachment) {
                $message->attachedFiles()->create([
                    'filename' => $attachment['name'],
                    'path' => $attachment['path'],
                    'size' => $attachment['size'],
                    'mime_type' => $attachment['mime_type'],
                ]);
            }
        }

        $message->addRecipient(explode(',', $request->recipients));




        return redirect()->route('messages.show', $message);
    }
}
