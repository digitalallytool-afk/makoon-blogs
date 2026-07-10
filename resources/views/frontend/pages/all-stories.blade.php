@extends('frontend.layout.main')
@section('title', 'All Stories | Makoons — Preschool Stories for Families')
@section('meta_description', 'Read Makoons preschool stories from classrooms, lunchboxes, art tables, morning routines, and everyday school life. Short, human stories written for parents.')
@section('meta_keywords', 'preschool stories, school stories for parents, classroom stories, children stories, parenting stories, daycare stories, early childhood stories, preschool moments')
@section('canonical_url', route('stories'))
@section('body_class', 'all-stories-page')

@section('content')
    <main>
      <section class="all-stories-masthead" style="border-bottom: none;">
        <div class="container-xl all-stories-masthead-inner">
          <nav class="breadcrumb-row" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>All Stories</span></nav>
          <span class="eyebrow">Story library</span>
          <h1>Everyday school stories from Makoons</h1>
          <p>Small moments from classrooms, play corners, lunch tables, art shelves, and morning circles. Written for parents who like seeing the child behind the routine.</p>
        </div>
      </section>

      <section class="section-block" id="latest" style="border-top: none; border-bottom: none; padding-top: 0;">
        <div class="container-xl">
          <div class="story-library-lead" style="margin-bottom: 2rem;">
            <span>Featured collection</span>
            <h2>Stories parents can read in a quiet minute</h2>
            <p>These are short, human pieces from preschool life: confidence arriving slowly, friendships forming over food, art becoming language, and routines making children feel safe.</p>
          </div>

          <div class="article-tools" aria-label="Story search and filters" style="margin-bottom: 3rem;">
            <div class="article-filter-panel">
              <div class="filter-stack" aria-label="Story category filters">
                <div class="filter-group">
                  <span>Story category</span>
                  <div class="quick-filters">
                    <button type="button" class="active" data-story-category-filter="all">All</button>
                    @foreach($storyCategories as $storyCat)
                      <button type="button" data-story-category-filter="{{ $storyCat->slug }}">{{ $storyCat->name }}</button>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

            <label class="article-search">
              <span>Search stories</span>
              <span class="article-search-field">
                <svg aria-hidden="true" viewBox="0 0 24 24">
                  <path d="M10.75 4.5a6.25 6.25 0 1 0 0 12.5 6.25 6.25 0 0 0 0-12.5ZM3 10.75a7.75 7.75 0 1 1 13.74 4.9l3.56 3.55a.78.78 0 0 1-1.1 1.1l-3.55-3.56A7.75 7.75 0 0 1 3 10.75Z"></path>
                </svg>
                <input type="search" placeholder="Search stories..." data-story-search>
              </span>
            </label>
          </div>

          <div class="stories-grid" aria-label="All preschool stories">
            @foreach($stories as $index => $story)
              @php
                $storyImages = ['story-one', 'story-two', 'story-three', 'story-four', 'story-five', 'story-six'];
                $fallbackImgClass = $storyImages[$index % 6] ?? 'story-one';
                
                $readTime = 3;
                if (str_contains(strtolower($story->title), 'helper')) { $readTime = 4; }
                elseif (str_contains(strtolower($story->title), 'lunchbox')) { $readTime = 3; }
                elseif (str_contains(strtolower($story->title), 'paint')) { $readTime = 3; }
                elseif (str_contains(strtolower($story->title), 'song')) { $readTime = 4; }
                elseif (str_contains(strtolower($story->title), 'friend')) { $readTime = 3; }
                elseif (str_contains(strtolower($story->title), 'clean-up')) { $readTime = 2; }
                $sViewsStr = $story->view_count >= 1000 ? number_format($story->view_count / 1000, 1) . 'k' : $story->view_count;
                $storyUrl = route('story.show', $story->slug);
                
                $isStoryUrl = $story->featured_image && \Illuminate\Support\Str::contains($story->featured_image, '/');
                $bgStyle = $isStoryUrl ? 'style="background-image: url(' . asset($story->featured_image) . ');"' : '';
                $storyImgClass = $isStoryUrl ? '' : ($story->featured_image ?? $fallbackImgClass);
              @endphp
              <article class="story-library-card" data-story-category="{{ $story->storyCategory->slug ?? '' }}" data-title="{{ $story->title }}">
                <a class="story-library-image {{ $storyImgClass }}" {!! $bgStyle !!} href="{{ $storyUrl }}" aria-label="Read the story about {{ $story->title }}"></a>
                <div class="story-library-copy" style="display: flex; flex-direction: column; justify-content: space-between; min-height: 190px;">
                  <div>
                    <span class="card-tag yellow-tag" style="margin-bottom: 0.55rem; display: inline-block;">
                      <a href="{{ route('category.show', $story->storyCategory->slug) }}">{{ $story->storyCategory->name ?? '' }}</a>
                    </span>
                    <h3 style="margin-bottom: 0.55rem;"><a href="{{ $storyUrl }}">{{ $story->title }}</a></h3>
                    <p style="margin-bottom: 1rem;">{{ $story->excerpt }}</p>
                  </div>
                  <div class="article-meta" style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(17,17,17,0.06);">
                    <a class="post-author" href="{{ route('author') }}?author={{ Str::slug($story->author->name ?? '') }}&type=stories">
                      @if($story->author && $story->author->image)
                        <img src="{{ asset($story->author->image) }}" alt="{{ $story->author->name }}">
                      @endif
                      <span>{{ $story->author->name ?? 'Makoons' }}</span>
                    </a>
                    <div class="read-info">
                      <time datetime="{{ $story->created_at->format('Y-m-d') }}">{{ $story->created_at->format('F j, Y') }}</time>
                      <span>{{ $readTime }} min</span>
                      <span class="view-count">{{ $sViewsStr }} views</span>
                    </div>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    </main>

@endsection
