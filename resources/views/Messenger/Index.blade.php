<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="id" content="">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <title>Chatting Application</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/all.min.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/slick.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/venobox.min.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/emojionearea.min.css">
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/spacing.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/style.css">
    <link rel="stylesheet" href="{{ asset('chatasset/css') }}/responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/js/messenger.js'])

</head>

<body>

    <!--==================================
        Chatting Application Start
    ===================================-->
    @yield('contents')
    <!--==================================
        Chatting Application End
    ===================================-->

    <!--jquery library js-->
    <script src="{{asset('chatasset/js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <!--bootstrap js-->
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content'),
            }
        });
    </script>
    <script src="{{ asset('chatasset') }}/js/bootstrap.bundle.min.js"></script>
    <!--font-awesome js-->
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
    <script src="{{ asset('chatasset') }}/js/Font-Awesome.js"></script>
    <script src="{{ asset('chatasset') }}/js/slick.min.js"></script>
    <script src="{{ asset('chatasset') }}/js/venobox.min.js"></script>
    <script src="{{ asset('chatasset') }}/js/emojionearea.min.js"></script>

    <!--main/custom js-->
    <script src="{{ asset('chatasset') }}/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <script>
        var notyf = new Notyf({
            duration: 5000,

        });
    </script>
    @stack('scripts')
</body>

</html>
