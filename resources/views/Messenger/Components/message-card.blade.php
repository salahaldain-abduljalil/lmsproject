<div class="wsus__single_chat_area">
    <div class="wsus__single_chat chat_right">
        <p class="messages">{{$message->body}}</p>
        <span class="time"> {{  timeago($message->created_at)  }}</span>
        <a class="action" href="#"><i class="fas fa-trash"></i></a>
    </div>
</div>
