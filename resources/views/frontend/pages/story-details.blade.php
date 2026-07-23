@extends('frontend.layout.main')
@section('title', ($story->meta_title ?? $story->title) . ' | Makoons Stories')
@section('meta_description', $story->meta_description ?? $story->excerpt)
@section('meta_keywords', $story->meta_keywords ?? ($story->storyCategory->name ?? 'preschool stories') . ', children stories, classroom stories, Makoons')
@section('canonical_url', $story->canonical_url ? rtrim($story->canonical_url, '/') : route('story.show', $story->slug))
@section('body_class', 'detail-page all-stories-page')

@section('content')
    @if(isset($isPreview) && $isPreview)
        <div style="background-color: #fff3cd; color: #664d03; padding: 12px; text-align: center; font-weight: bold; border-bottom: 1px solid #ffe69c; position: sticky; top: 0; z-index: 1050; font-family: 'Inter', sans-serif;">
            ⚠️ Preview Mode: This story is not yet published.
        </div>
    @endif
    <div class="reading-progress" data-reading-progress></div>
    <main>
      <section class="article-masthead">
        <div class="container-xl article-masthead-inner">
          <div class="article-masthead-kicker">
            <nav class="breadcrumb-row" aria-label="Breadcrumb">
              <a href="{{ route('stories') }}">Stories</a>
              <span>/</span>
              <a href="{{ route('category.show', $story->storyCategory->slug) }}">{{ $story->storyCategory->name ?? '' }}</a>
            </nav>
          </div>
          <h1>{{ $story->title }}</h1>
          <p>
            {{ $story->excerpt }}
          </p>

          <div class="article-topline">
            <div class="detail-byline article-author-block">
              <a class="article-author-link" href="{{ route('author') }}?author={{ Str::slug($story->author->name ?? '') }}&type=stories" aria-label="View articles by {{ $story->author->name ?? '' }}">
                @if($story->author && $story->author->image)
                  <img src="{{ asset($story->author->image) }}" alt="{{ $story->author->name }}">
                @else
                  <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%20120%20120%22%3E%0A%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2260%22%20fill%3D%22%23f9efe5%22/%3E%0A%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2252%22%20r%3D%2228%22%20fill%3D%22%23b9794e%22/%3E%0A%20%20%3Cpath%20d%3D%22M31%2053c2-24%2016-35%2031-35%2018%200%2030%2014%2029%2036-11-11-21-11-32-10-12%201-20-2-28%209z%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Cpath%20d%3D%22M24%20114c6-24%2021-36%2036-36s30%2012%2036%2036%22%20fill%3D%22%23f5b42b%22/%3E%0A%20%20%3Cpath%20d%3D%22M38%2084c8%208%2036%208%2044%200%22%20stroke%3D%22%23fff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%20fill%3D%22none%22/%3E%0A%20%20%3Ccircle%20cx%3D%2250%22%20cy%3D%2255%22%20r%3D%223%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Ccircle%20cx%3D%2270%22%20cy%3D%2255%22%20r%3D%223%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Cpath%20d%3D%22M52%2066c5%204%2011%204%2016%200%22%20stroke%3D%22%232e1a12%22%20stroke-width%3D%223%22%20stroke-linecap%3D%22round%22%20fill%3D%22none%22/%3E%0A%3C/svg%3E" alt="">
                @endif
                <span>
                  <strong>{{ $story->author->name ?? 'Makoons Team' }}</strong>
                  <em>Story teller</em>
                </span>
              </a>
              @php
                $sViewsStr = $story->view_count >= 1000 ? number_format($story->view_count / 1000, 1) . 'k' : $story->view_count;
              @endphp
              <span class="article-meta-line">
                <span>{{ $story->created_at->format('F j, Y') }}</span>
                <span>4 min read</span>
                <span><a href="{{ route('category.show', $story->storyCategory->slug) }}">{{ $story->storyCategory->name ?? '' }}</a></span>
                <span class="detail-view-count" aria-label="{{ $story->view_count }} views">
                  <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"></path></svg>
                  {{ $sViewsStr }} views
                </span>
              </span>
            </div>
          </div>
        </div>
      </section>

      <section class="article-layout-section">
        <div class="container-xl article-clean-layout">
          <aside class="article-keyword-sidebar article-detail-side" aria-label="Stories sidebar">
            <div class="trending-sidebar detail-topic-panel" aria-label="Trending stories">
              <span class="eyebrow">Trending Stories</span>
              <h2>Popular stories from classrooms</h2>
              <p>The most-read stories from everyday school moments: lunchboxes, friendship, art, and music.</p>

              <div class="detail-topic-articles" aria-label="Trending stories">
                @foreach($trendingStories as $rStory)
                  @php
                    $rViewsStr = $rStory->view_count >= 1000
                      ? number_format($rStory->view_count / 1000, 1) . 'k'
                      : $rStory->view_count;
                  @endphp
                  <a href="{{ route('story.show', $rStory->slug) }}">
                    <span>
                      {{ $rStory->storyCategory->name ?? '' }}
                      · {{ $rViewsStr }} views
                    </span>
                    <strong>{{ $rStory->title }}</strong>
                  </a>
                @endforeach
              </div>
            </div>

            <div class="article-category-card" aria-label="Story categories">
              <div class="category-card-head">
                <span class="keyword-label">Categories</span>
                <p>Explore more stories by topic</p>
              </div>
              @foreach($storyCategories as $sCat)
                <a href="{{ route('category.show', $sCat->slug) }}" class="{{ $sCat->id === $story->story_category_id ? 'active' : '' }}">
                  <span>{{ $sCat->name }}</span>
                  <small>{{ $sCat->stories_count }} {{ Str::plural('story', $sCat->stories_count) }}</small>
                </a>
              @endforeach
            </div>
          </aside>

          <article class="article-content clean-article" data-article-content>
            @if($story->featured_image)
              <div class="detail-featured-image-wrapper" style="margin-bottom: 2rem; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <img src="{{ asset($story->featured_image) }}" alt="{{ $story->title }}" style="width: 100%; height: auto; display: block;">
              </div>
            @endif
            <!-- Dynamic Table of Contents (TOC) -->
            <div class="toc-container" id="toc-container" style="display: none;">
                <div class="toc-card">
                    <div class="toc-header">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="toc-icon"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <span>On this story</span>
                    </div>
                    <ul class="toc-list" id="toc-list"></ul>
                </div>
            </div>

            {!! $story->content !!}
          </article>
        </div>
      </section>

      <section class="related-clean-section">
        <div class="container-xl related-clean-inner">
          <div class="related-clean-heading">
            <span class="eyebrow">Trending Stories</span>
            <h2>Popular stories</h2>
          </div>

          <div class="related-article-grid">
            @foreach($trendingStories->take(3) as $index => $tStory)
              @php
                $isUrl = $tStory->featured_image && \Illuminate\Support\Str::contains($tStory->featured_image, '/');
                $bgStyle = $isUrl ? 'style="background-image: url(' . asset($tStory->featured_image) . ');"' : '';
                $tViewsStr = $tStory->view_count >= 1000 ? number_format($tStory->view_count / 1000, 1) . 'k' : $tStory->view_count;
                $mediaClasses = ['related-one', 'related-two', 'related-three', 'related-four'];
                $mediaClass = $isUrl ? '' : ($mediaClasses[$index % 4] ?? 'related-one');
              @endphp
              <article class="related-article-card">
                <a class="related-article-media {{ $mediaClass }}" {!! $bgStyle !!} href="{{ route('story.show', $tStory->slug) }}" aria-label="Read {{ $tStory->title }} story"></a>
                <div class="related-article-copy">
                  <span>
                    @if($tStory->storyCategory)
                      <a href="{{ route('category.show', $tStory->storyCategory->slug) }}">{{ $tStory->storyCategory->name }}</a> · 
                    @endif
                    4 min read
                  </span>
                  <h3><a href="{{ route('story.show', $tStory->slug) }}">{{ $tStory->title }}</a></h3>
                  <p>{{ $tStory->excerpt }}</p>
                  <div class="card-bottom-meta">
                    <span class="view-count" aria-label="{{ $tViewsStr }} views"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"></path></svg>{{ $tViewsStr }} views</span>
                    <a class="post-author" href="{{ route('author') }}?author={{ Str::slug($tStory->author->name ?? '') }}&type=stories" aria-label="View articles by {{ $tStory->author->name ?? '' }}">
                      @if($tStory->author && $tStory->author->image)
                        <img src="{{ asset($tStory->author->image) }}" alt="{{ $tStory->author->name }}">
                      @endif
                      <span>{{ $tStory->author->name ?? '' }}</span>
                    </a>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    </main>
@endsection
