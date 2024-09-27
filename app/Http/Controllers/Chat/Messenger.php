<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'attachment' => ['nullable', 'max:1024', 'image']

        ]);
        $attachmentPath = $this->uploadFile($request, 'attachment');
        $message = new Message();
        $message->from_id = Auth::user()->id;
        $message->to_id = $request->id;
        $message->body = $request->message;
        if ($attachmentPath) $message->attachment = json_encode($attachmentPath);
        $message->save();

        return response()->json([
            'message' => $message->attachment ? $this->messageCard($message, true) : $this->messageCard($message), //to recipient the message to the it place.
            'tempId' => $request->temporaryMsgId //id of the sender.
        ]);
    }

    public function messageCard($message, $attachment = false)
    {

        return view('Messenger.Components.message-card', compact('message', 'attachment'))->render();
    }

    //fetch messages From Database.
    public function fetchMessage(Request $request)
    {

        $messages = Message::where('from_id', Auth::user()->id)->where('to_id', $request->id)
            ->orWhere('to_id', Auth::user()->id)->where('from_id', $request->id)->latest()->paginate(20);

        $response = [
            'last_page' => $messages->lastPage(),
            'last_message' => $messages->last(),
            'messages' => ''
        ];


        if (count($messages) > 0) {

            $response['messages'] = "<div class='d-flex justify-content-center align-items-center h-100'><p>say 'Hi' and start Messaging</p></div>";
            return response()->json($response);
        }
        $allMessages = '';
        foreach ($messages->reverse() as $message) {   //reverse() the function used to display the descendant or latest data.

            $allMessages .= $this->messageCard($message, $message->attachment ? true : false);
        }
        $response['messages'] = $allMessages;
        return response()->json($response);
    }

    public function fetchContact(Request $request){
      //the steps
      //1 we are joined the messages and users table depending to the relationship.
      //2 selecting the messages which depending to the current users.
      //3 we must to ignore ourself.
      //4
        $users = Message::join("users",function($join){

            $join->on('messages.from_id','=','users.id')->orOn('messages.to_id','=','users.id');
        })->where(function($q){
            $q->where('messages.from_id',Auth::user()->id)
            ->orWhere('messages.to_id',Auth::user()->id);  //this is query it for the real chatting.
        })->where('users.id','!=',Auth::user()->id)->select('users.*',DB::raw('MAX(messages.created_at) max_created_at'))
        ->orderBy('max_created_at','desc')->groupBy('users.id')
        ->paginate(10);

        if(count($users) > 0){
            $contacts = '';
            foreach($users as $user){
               $contacts .= $this->getContactItem($user);
            }

        }else{
            $contacts = "<p>Yout contact List is Empty!</p>";

        }

        return response()->json([
            'contacts' => $contacts,
            'last_page' => $users->lastpage()
        ]);


    }
    public function getContactItem($user){

        $lastMessage = Message::where('from_id', Auth::user()->id)->where('to_id', $user->id)
        ->orWhere('from_id', $user->id)->where('to_id', Auth::user()->id)->latest()->first();

        $unseenCounter = Message::where('from_id',$user->id)->where('to_id',Auth::user()->id)->where('seen',0)->count();

        return view('Messenger.Components.contact-list-item',compact('lastMessage','unseenCounter','user'))->render();
    }

    //update contact item.

    public function updateContactItem(Request $request){

        $user = User::where('id',$request->user_id)->first();

        if(!$user){

            return response()->json([
                'message' => 'message not Found'
            ],401);
        }
        $contactItem = $this->getContactItem($user);
        return response()->json([
            'contact_item' => $contactItem
        ],200);

    }
    public function makeseen(Request $request){

        Message::where('from_id',$request->id)
        ->where('to_id',Auth::user()->id)->where('seen',0)
        ->update(['seen' => 1]);

        return true;
    }
}
