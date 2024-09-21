<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Messenger extends Controller
{
    use FileUploadTrait;
    public function index()
    {

        return view('Messenger.Layout.app');
    }
    //search user profile.
    public function search(Request $request)
    {

        $getRecords = null;
        $input = $request['query'];
        $records = User::where('id', '!=', Auth::user()->id)
            ->where('name', 'LIKE', "%{$input}%")->orWhere('user_name', 'LIKE', "%{$input}%")
            ->paginate(10);

        if ($records->total() < 1) {

            $getRecords .= "<p class='text-center'>Nothing to show.</p>"; //for the search part.
        }


        foreach ($records as $record) {

            $getRecords .= view('Messenger.Components.search-item', compact('record'))->render();
        }

        return response()->json([
            'records' => $getRecords,
            'last_page' => $records->lastPage(), //for the pagination.
        ]);
    }

    public function fetchIdinfo(Request $request)
    {

        $fetch = User::where('id', $request['id'])->first();
        return response()->json([
            'fetch' => $fetch
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => ['required'],
            'id'  => ['required', 'integer'],
            'temporaryMsgId' => ['required'],
            'attachment' => ['nullable','max:1024','image']

        ]);
        $attachmentPath = $this->uploadFile($request,'attachment');
        $message = new Message();
        $message->from_id = Auth::user()->id;
        $message->to_id = $request->id;
        $message->body = $request->message;
        if($attachmentPath) $message->attachment = json_encode($attachmentPath);
        $message->save();

        return response()->json([
            'message' => $message->attachment ? $this->messageCard($message,true) : $this->messageCard($message) , //to recipient the message to the it place.
            'tempId' => $request->temporaryMsgId //id of the sender.
        ]);
    }

    public function messageCard($message , $attachment = false)
    {

        return view('Messenger.Components.message-card', compact('message','attachment'))->render();
    }
}
