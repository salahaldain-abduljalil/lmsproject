@if ($attachment)
    @php
        $image = json_decode($message->attachment);
    @endphp
    <div class="wsus__single_chat_area  message-card" data-id="{{ $message->id}}">
        <div class="wsus__single_chat {{ $message->from_id === auth()->user()->id ? 'chat_right' : '' }}">
            <a class="venobox" data-gall="gallery01" href="{{ asset($image) }}">
                <img src="{{ asset($image) }}" alt="" class="img-fluid w-100">
            </a>
            @if ($message->body)
                <p class="messages">{{ $message->body }}</p>
            @endif
            <span class="time">{{ timeago($message->created_at) }}</span>
            <a class="action" href="#"><i class="fas fa-trash"></i></a>
        </div>
    </div>
@else
    <div class="wsus__single_chat_area  message-card" data-id="{{ $message->id}}"  >
        <div class="wsus__single_chat {{ $message->from_id === auth()->user()->id ? 'chat_right' : '' }}">
            <p class="messages">{{ $message->body }}</p>
            <span class="time"> {{ timeago($message->created_at) }}</span>
            <a class="action" href="#"><i class="fas fa-trash"></i></a>
        </div>
    </div>
@endif
