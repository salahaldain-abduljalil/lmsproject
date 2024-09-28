@php
    $image = json_decode($photo->attachment);
@endphp

<li>
    <a class="venobox" data-gall="gallery01" href="{{ $image->attachment }}">
        <img src="{{ $image->attachment }}" alt="" class="img-fluid w-100" loading="lazy">
    </a>
</li>
