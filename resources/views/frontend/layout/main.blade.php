@include('frontend.layout.header')

<body class="@yield('body_class')" @yield('body_attributes')>
    @include('frontend.layout.navbar')

    @yield('content')

@include('frontend.layout.footer')
    