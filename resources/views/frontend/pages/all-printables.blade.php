@extends('frontend.layout.main')
@section('title', 'Free Kids Activity Printables | Fun & Educational Worksheets')
@section('meta_description',
    'Download free kids activity printables to help children learn through fun. Explore
    coloring pages, tracing sheets, puzzles, math & many preschool worksheets')
@section('meta_keywords',
    'free kids activity printables, kids worksheets, preschool printables, printable activities
    for kids, free preschool worksheets, alphabet worksheets, coloring pages for kids, tracing worksheets, kindergarten
    printables')
@section('canonical_url', route('printables'))
@section('body_class', 'all-printables-page')

@section('content')
    <main>
        <section class="all-printables-masthead">
            <div class="container-xl all-printables-masthead-inner">
                <nav class="breadcrumb-row" aria-label="Breadcrumb"><a
                        href="{{ route('home') }}">Home</a><span>/</span><span>All Printables</span></nav>
                <span class="eyebrow">Printable library</span>
                <h1>Free printable coloring pages for kids</h1>
                <p>Welcome to our printables corner. Browse playful sheets for coloring, festivals, space adventures, quiet
                    afternoons, and simple creative time at home.</p>
            </div>
        </section>

        <section class="all-printables-section" id="latest">
            <div class="container-xl all-printables-inner">
                <div class="article-tools" aria-label="Printable search"
                    style="margin-bottom: 1rem; justify-content: flex-end;">
                    <label class="article-search">
                        <span>Search printables</span>
                        <span class="article-search-field">
                            <svg aria-hidden="true" viewBox="0 0 24 24">
                                <path
                                    d="M10.75 4.5a6.25 6.25 0 1 0 0 12.5 6.25 6.25 0 0 0 0-12.5ZM3 10.75a7.75 7.75 0 1 1 13.74 4.9l3.56 3.55a.78.78 0 0 1-1.1 1.1l-3.55-3.56A7.75 7.75 0 0 1 3 10.75Z">
                                </path>
                            </svg>
                            <input type="search" placeholder="Search printables..." data-printable-search>
                        </span>
                    </label>
                </div>

                <div class="printable-library-grid printable-collage-grid" aria-label="All printable downloads">
                    @foreach ($printables as $index => $printable)
                        @php
                            $printableClasses = [
                                'printable-halloween',
                                'printable-holidays',
                                'printable-halloween-two',
                                'printable-space',
                                'printable-nature',
                                'printable-shapes',
                            ];
                            $fallbackClass = $printableClasses[$index % 6] ?? 'printable-halloween';

                            $isPrintableUrl =
                                $printable->image && \Illuminate\Support\Str::contains($printable->image, '/');
                            $bgStyle = $isPrintableUrl
                                ? 'style="background-image: url(' .
                                    asset($printable->image) .
                                    '); background-size: contain; background-repeat: no-repeat; background-position: center; background-color: #ffffff;"'
                                : '';
                            $mediaClass = $printable->image ?? $fallbackClass;

                            $downloadUrl = $printable->file_path ? asset($printable->file_path) : '#';
                        @endphp
                        <article class="printable-card printable-collage-card printable-library-card"
                            data-title="{{ $printable->name }}">
                            <a class="printable-preview printable-fallback {{ $mediaClass }}" {!! $bgStyle !!}
                                href="{{ $downloadUrl }}" download aria-label="Download {{ $printable->name }} printable">
                                @if (!$isPrintableUrl)
                                    <span>{{ $printable->name }}</span>
                                @endif
                            </a>
                            <div class="printable-copy">
                                <span>Activity Sheet</span>
                                <h3>{{ $printable->name }}</h3>
                                <p>{{ $printable->description }}</p>
                                <a href="{{ $downloadUrl }}" class="printable-action" download>Download</a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p class="empty-state" data-empty-state>No matching printables found. Try another search.</p>
            </div>
        </section>
    </main>

@endsection
