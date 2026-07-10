@extends('frontend.layout.main')
@section('title', 'Parenting Sessions | Makoons — Video Guides for Preschool Parents')
@section('meta_description', 'Watch Makoons parenting video sessions on child development, nutrition, food habits, growth milestones, and everyday parenting advice for preschool families.')
@section('meta_keywords', 'parenting sessions, parenting videos, child development, preschool parenting advice, food and health for kids, growth milestones, parenting guides, early childhood parenting')
@section('canonical_url', route('sessions'))
@section('body_class', 'all-sessions-page')

@section('content')
        <main>
            <section class="sessions-masthead">
                <div class="container-xl sessions-masthead-inner">
                    <nav class="breadcrumb-row" aria-label="Breadcrumb"><a
                            href="{{ route('home') }}">Home</a><span>/</span><span>Parenting Sessions</span></nav>
                    <span class="eyebrow">Parenting sessions</span>
                    <h1>Short guides made for preschool parents</h1>
                    <p>Simple video sessions on growth, food choices, classroom exposure, emotional learning, and the
                        everyday questions parents bring to school.</p>
                </div>
            </section>

            <section class="sessions-library-section" id="latest">
                <div class="container-xl sessions-library-inner">
                    <div class="sessions-feature-layout" style="grid-template-columns: 1fr;">
                        @php
                            $featuredSession = $videoSessions->first();
                        @endphp
                        @if($featuredSession)
                            @php
                                $hasFeaturedImage = !empty($featuredSession->image);
                                $featuredImageUrl = $hasFeaturedImage ? asset($featuredSession->image) : null;
                            @endphp
                            <article class="sessions-feature-video">
                                @if($hasFeaturedImage)
                                    <a class="sessions-feature-thumb" href="{{ $featuredSession->video_url ?? '#' }}" data-video-url="{{ $featuredSession->video_url }}"
                                        style="background-image: url('{{ $featuredImageUrl }}');"
                                        aria-label="Watch {{ $featuredSession->title }}"><span
                                            class="sessions-play">▶</span><small>{{ $featuredSession->duration }} min</small></a>
                                @else
                                    <div class="sessions-feature-thumb video-playing">
                                        <iframe src="{{ $featuredSession->video_url }}" style="width:100%; height:100%; border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    </div>
                                @endif
                                <div class="sessions-feature-copy">
                                    <span>Featured session</span>
                                    <h2>{{ $featuredSession->title }}</h2>
                                    <p>{{ $featuredSession->description }}</p>
                                    @if($hasFeaturedImage)
                                        <a href="{{ $featuredSession->video_url ?? '#' }}" class="sessions-watch-link">Watch session <span
                                                aria-hidden="true">→</span></a>
                                    @endif
                                </div>
                            </article>
                        @endif
                    </div>

                    <div class="sessions-topic-row">
                        <div><span>01</span><strong>Development</strong><em>Confidence, growth, independence</em></div>
                        <div><span>02</span><strong>Food</strong><em>Labels, snacks, lunch habits</em></div>
                        <div><span>03</span><strong>Emotions</strong><em>Big feelings, routines, language</em></div>
                        <div><span>04</span><strong>School life</strong><em>Exposure, teachers, transitions</em></div>
                    </div>

                    <div class="topic-tabs" aria-label="Filter sessions by category" style="margin-bottom: 2rem; margin-top: 2rem;">
                        <button type="button" class="active" data-session-category-filter="all">All</button>
                        @foreach($sessionCategories as $cat)
                            <button type="button" data-session-category-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
                        @endforeach
                    </div>

                    <div class="article-tools" aria-label="Sessions search" style="margin-bottom: 2rem; justify-content: flex-end;">
                        <label class="article-search">
                            <span>Search sessions</span>
                            <span class="article-search-field">
                                <svg aria-hidden="true" viewBox="0 0 24 24">
                                    <path d="M10.75 4.5a6.25 6.25 0 1 0 0 12.5 6.25 6.25 0 0 0 0-12.5ZM3 10.75a7.75 7.75 0 1 1 13.74 4.9l3.56 3.55a.78.78 0 0 1-1.1 1.1l-3.55-3.56A7.75 7.75 0 0 1 3 10.75Z"></path>
                                </svg>
                                <input type="search" placeholder="Search sessions..." data-session-search>
                            </span>
                        </label>
                    </div>

                    <div class="sessions-card-grid" aria-label="All parenting sessions">
                        @foreach($videoSessions as $index => $session)
                            @php
                                $sessionThumbs = ['session-one', 'session-two', 'session-three', 'session-four'];
                                $sessionThumbClass = $sessionThumbs[$index % 4] ?? 'session-two';
                                $hasCustomImage = !empty($session->image);
                                $imageUrl = $hasCustomImage ? asset($session->image) : null;
                            @endphp
                            <article class="session-list-card" data-title="{{ $session->title }}" data-session-category="{{ $session->sessionCategory->slug ?? '' }}">
                                @if($hasCustomImage)
                                    <a class="session-thumb" href="{{ $session->video_url ?? '#' }}" data-video-url="{{ $session->video_url }}"
                                       style="background-image: url('{{ $imageUrl }}');">
                                        <span class="play-icon">▶</span>
                                        <small>{{ $session->duration }} min</small>
                                    </a>
                                @else
                                    <div class="session-thumb video-playing">
                                        <iframe src="{{ $session->video_url }}" style="width:100%; height:100%; border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    </div>
                                @endif
                                <div>
                                    <span>{{ $session->sessionCategory->name ?? '' }}</span>
                                    <h3>{{ $session->title }}</h3>
                                    <p>{{ $session->description }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <p class="empty-state" data-empty-state>No matching sessions found. Try another search.</p>
                </div>
            </section>
        </main>
    @endsection
