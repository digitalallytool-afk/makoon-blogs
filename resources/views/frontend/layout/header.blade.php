<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Makoons Blogs')</title>
    <meta name="description"
        content="@yield('meta_description', 'Makoons Blogs for preschool, daycare, parenting, activities, and early learning blog posts.')">
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
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}?v=1.0.9">
</head>