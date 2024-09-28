<div class="wsus__user_list">
    <div class="wsus__user_list_header">
        <h3>
            <span><img src="{{ asset('chatasset/images/chat_list_icon.png') }}" alt="Chat" class="img-fluid"></span>
            MESSAGES
        </h3>
        <span class="setting" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="fas fa-user-cog"></i>
        </span>

        @include('Messenger.Layout.profile-modal')
    </div>
    {{-- Search Form --}}
    @include('Messenger.Layout.search')

    <div class="wsus__favourite_user">
        <div class="top">favourites</div>
        @foreach ($favorites as $li)
        <div class="row favourite_user_slider messenger-list-item" data-id="{{ $li->users->id }}">
            <div class="col-xl-3">
                <a href="#" class="wsus__favourite_item">
                    <div class="img">
                        <img src="{{ asset($li->users?->avatar) }}" alt="User" class="img-fluid">
                        <span class="inactive"></span>
                    </div>
                    <p>{{ $li->users?->name }}</p>
                </a>
            </div>


        </div>

        @endforeach

    </div>

    <div class="wsus__save_message messenger-list-item" data-id="{{ auth()->user()->id }}">
        <div class="top">your space</div>
        <div class="wsus__save_message_center">
            <div class="icon">
                <i class="far fa-bookmark"></i>
            </div>
            <div class="text">
                <h3>Saved Messages</h3>
                <p>Save messages secretly</p>
            </div>
            <span>you</span>
        </div>
    </div>

    <div class="wsus__user_list_area">
        <div class="top">All Messages</div>
        <div class="wsus__user_list_area_height messenger-contacts">

        </div>


        <!-- <div class="wsus__user_list_liading">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div> -->

    </div>
</div>
