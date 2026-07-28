<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @hasSection('title')
        <title>{!! html_entity_decode(View::getSection('title'), ENT_QUOTES, 'UTF-8') !!}</title>
    @else
        <title>Makoons Blogs</title>
    @endif
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('uploads/2026/06/favicon.png') }}" type="image/png" />
    @hasSection('meta_description')
        @php
            $metaDesc = html_entity_decode(View::getSection('meta_description'), ENT_QUOTES, 'UTF-8');
            $metaDesc = str_replace('"', '&quot;', $metaDesc);
        @endphp
        <meta name="description" content="{!! $metaDesc !!}">
    @else
        <meta name="description" content="Makoons Blogs for preschool, daycare, parenting, activities, and early learning blog posts.">
    @endif
    @hasSection('meta_keywords')
    <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    @hasSection('canonical_url')
    <link rel="canonical" href="@yield('canonical_url')">
    @else
    <link rel="canonical" href="{{ url()->current() }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9Oer+R5ULwNIA6yK2tQPDvK7O4p4jG5KV5Rh7L5Z4xJ" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}?v=1.2.0">
</head>