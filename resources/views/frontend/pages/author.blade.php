@extends('frontend.layout.main')
@section('title', ($author->name ?? 'Author') . ' | Makoons Author')
@section('meta_description', 'Read all blogs written by ' . ($author->name ?? 'Author') . ' for Makoons Blogs.')
@section('body_class', 'author-page')

@section('content')
    <main>
      <section class="author-masthead">
        <div class="container-xl author-masthead-inner">
          <nav class="breadcrumb-row" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('blogs') }}">Authors</a>
            <span>/</span>
            <span>{{ $author->name ?? 'Author' }}</span>
          </nav>
          <div class="author-profile-card">
            @if($author && $author->image)
              <img src="{{ asset($author->image) }}" alt="{{ $author->name }}">
            @else
              <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=260&q=86" alt="{{ $author->name ?? 'Author' }}">
            @endif
            <div>
              <span class="eyebrow">Author</span>
              <h1>{{ $author->name ?? 'Author' }}</h1>
              <p>{{ $author->bio ?? 'Read all blogs from this Makoons author.' }}</p>
            </div>
          </div>
          <div class="author-stats" aria-label="Author statistics">
            <div><strong>{{ $itemsCount }}</strong><span>{{ $type === 'stories' ? 'Stories' : 'Blogs' }}</span></div>
            <div><strong>{{ $formattedViews }}</strong><span>Total views</span></div>
            <div><strong>{{ $mainTopic }}</strong><span>Main topic</span></div>
          </div>
        </div>
      </section>

      <section class="author-posts-section">
        <div class="container-xl author-posts-inner">
          <div class="author-posts-head">
            <span class="eyebrow">All {{ $type }} by {{ $author->name ?? 'Author' }}</span>
            <h2>Practical {{ strtolower($mainTopic) }} and comfort reads for parents</h2>
            <p>{{ $author->name ?? 'Author' }} has written {{ $itemsCount }} {{ Str::plural($type === 'stories' ? 'story' : 'blog', $itemsCount) }} for preschool families.</p>
          </div>

          <div class="author-post-list">
            @if($type === 'stories')
              @foreach($author->stories as $index => $story)
                @php
                  $storyClasses = ['story-one', 'story-two', 'story-three', 'story-four', 'story-five', 'story-six'];
                  $fallbackImgClass = $storyClasses[$index % 6] ?? 'story-one';
                  $isStoryUrl = $story->featured_image && \Illuminate\Support\Str::contains($story->featured_image, '/');
                  $bgStyle = $isStoryUrl ? 'style="background-image: url(' . asset($story->featured_image) . ');"' : '';
                  $storyImgClass = $isStoryUrl ? '' : ($story->featured_image ?? $fallbackImgClass);
                  $viewsStr = $story->view_count >= 1000 ? number_format($story->view_count / 1000, 1) . 'k' : $story->view_count;
                  $storyUrl = route('story.show', $story->slug);
                @endphp
                <article class="author-post-card">
                  <a class="card-media {{ $storyImgClass }}" {!! $bgStyle !!} href="{{ $storyUrl }}" aria-label="Read {{ $story->title }}"></a>
                  <div>
                    <span>{{ $story->storyCategory->name ?? '' }} · 4 min read</span>
                    <h3><a href="{{ $storyUrl }}">{{ $story->title }}</a></h3>
                    <p>{{ $story->excerpt }}</p>
                    <small>{{ $story->created_at->format('F j, Y') }} · {{ $viewsStr }} views</small>
                  </div>
                </article>
              @endforeach
            @else
              @foreach($author->posts as $index => $post)
                @php
                  $mediaClasses = ['media-one', 'media-two', 'media-three', 'media-four', 'media-five'];
                  $mediaClass = $mediaClasses[$index % 5] ?? 'media-one';
                  $isUrl = $post->featured_image && \Illuminate\Support\Str::contains($post->featured_image, '/');
                  $bgStyle = $isUrl ? 'style="background-image: url(' . asset($post->featured_image) . ');"' : '';
                  $postImgClass = $isUrl ? '' : ($post->featured_image ?? $mediaClass);
                  $viewsStr = $post->view_count >= 1000 ? number_format($post->view_count / 1000, 1) . 'k' : $post->view_count;
                  $articleUrl = route('blog.show', $post->slug);
                @endphp
                <article class="author-post-card">
                  <a class="card-media {{ $postImgClass }}" {!! $bgStyle !!} href="{{ $articleUrl }}" aria-label="Read {{ $post->title }}"></a>
                  <div>
                    <span>{{ $post->category->name ?? '' }} · 5 min read</span>
                    <h3><a href="{{ $articleUrl }}">{{ $post->title }}</a></h3>
                    <small>{{ $post->created_at->format('F j, Y') }} · {{ $viewsStr }} views</small>
                  </div>
                </article>
              @endforeach
            @endif
          </div>
        </div>
      </section>
    </main>
@endsection
