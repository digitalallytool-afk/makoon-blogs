@extends('frontend.layout.main')
@section('title', $category->name . ' | Makoons Blogs')
@section('meta_description', $category->description ?? 'Browse blogs and stories in the ' . $category->name . ' category.')
@section('body_class', $type === 'blogs' ? 'all-posts-page' : 'all-stories-page')

@section('content')
<main>
    <section class="{{ $type === 'blogs' ? 'all-posts-masthead' : 'all-stories-masthead' }}">
        <div class="container-xl {{ $type === 'blogs' ? 'all-posts-masthead-inner' : 'all-stories-masthead-inner' }}">
            <nav class="breadcrumb-row" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                @if($type === 'blogs')
                    <a href="{{ route('blogs') }}">Blogs</a>
                @else
                    <a href="{{ route('stories') }}">Stories</a>
                @endif
                <span>/</span>
                <span>{{ $category->name }}</span>
            </nav>
            <span class="eyebrow">{{ ucfirst($type === 'blogs' ? 'Blogs' : $type) }} Category</span>
            <h1>{{ $category->name }}</h1>
            <p>{{ $category->description ?? 'Discover our curated selection of ' . ($type === 'blogs' ? 'blogs' : $type) . '.' }}</p>
        </div>
    </section>

    @if($type === 'blogs')
    <section class="all-posts-section">
        <div class="container-xl">
            <div class="all-posts-summary" style="margin-top: 2rem;">
                <p class="result-count" data-result-count>Showing {{ $posts->count() }} blogs</p>
                <a href="{{ route('blogs') }}">Back to all blogs</a>
            </div>

            <div class="all-posts-groups">
                <section class="post-category-group" data-article-group>
                    <div class="all-posts-grid">
                        @foreach($posts as $post)
                            @php
                                $mainCategorySlug = $post->category->parent ? $post->category->parent->slug : ($post->category->slug ?? '');
                                $subCategorySlug = $post->category->slug ?? '';
                                
                                $tagClass = '';
                                if ($subCategorySlug === 'daycare') {
                                    $tagClass = 'blue-tag';
                                } elseif ($subCategorySlug === 'parenting') {
                                    $tagClass = 'yellow-tag';
                                } elseif ($subCategorySlug === 'activities') {
                                    $tagClass = 'green-tag';
                                } elseif ($subCategorySlug === 'play') {
                                    $tagClass = 'yellow-tag';
                                } elseif ($subCategorySlug === 'food') {
                                    $tagClass = 'green-tag';
                                }
                                
                                $isUrl = $post->featured_image && \Illuminate\Support\Str::contains($post->featured_image, '/');
                                $bgStyle = $isUrl ? 'style="background-image: url(' . asset($post->featured_image) . ');"' : '';
                                $mediaClass = $isUrl ? '' : ($post->featured_image ?? 'media-one');
                                $readTime = 5;
                                $viewsStr = $post->view_count >= 1000 ? number_format($post->view_count / 1000, 1) . 'k' : $post->view_count;
                                $articleUrl = route('blog.show', $post->slug);
                            @endphp
                            <article class="article-card all-post-card" data-main-category="{{ $mainCategorySlug }}"
                                data-sub-category="{{ $subCategorySlug }}" data-category="{{ $subCategorySlug }}"
                                data-title="{{ $post->title }}" data-date="{{ $post->created_at->format('Y-m-d') }}" 
                                data-views="{{ $post->view_count }}" data-read-time="{{ $readTime }}">
                                <a class="card-media {{ $mediaClass }}" {!! $bgStyle !!} href="{{ $articleUrl }}"
                                    aria-label="Read {{ $post->title }}"></a>
                                <div class="card-copy">
                                    <span class="card-tag {{ $tagClass }}">
                                        <a href="{{ route('category.show', $subCategorySlug) }}">{{ $post->category->name ?? '' }}</a>
                                    </span>
                                    <h3><a href="{{ $articleUrl }}">{{ $post->title }}</a></h3>
                                    <p>{{ $post->excerpt }}</p>
                                    <div class="article-meta">
                                        <a class="post-author" href="{{ route('author') }}?author={{ Str::slug($post->author->name ?? '') }}">
                                            @if($post->author && $post->author->image)
                                                <img src="{{ asset($post->author->image) }}" alt="{{ $post->author->name }}">
                                            @endif
                                            <span>{{ $post->author->name ?? '' }}</span>
                                        </a>
                                        <div class="read-info">
                                            <time datetime="{{ $post->created_at->format('Y-m-d') }}">{{ $post->created_at->format('F j, Y') }}</time>
                                            <span>{{ $readTime }} min</span>
                                            <span class="view-count">{{ $viewsStr }} views</span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
            <p class="empty-state" data-empty-state>No blogs found in this category.</p>
        </div>
    </section>
    @else
    <section class="all-stories-section">
        <div class="container-xl all-stories-inner">
            <div class="all-posts-summary" style="margin-bottom: 2rem; margin-top: 2rem;">
                <p class="result-count" data-result-count>Showing {{ $stories->count() }} stories</p>
                <a href="{{ route('stories') }}">Back to all stories</a>
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
    @endif
</main>
@endsection
