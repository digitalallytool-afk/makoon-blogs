@extends('frontend.layout.main')
@section('title', 'All Blogs | Makoons — Preschool & Parenting Reads')
@section('meta_description', 'Browse all Makoons blogs by preschool, daycare, parenting, activities, food, play, and early learning categories. Practical reads for families raising young children.')
@section('meta_keywords', 'preschool blogs, parenting blogs, daycare tips, early childhood education, family reads, preschool activities, children learning, parenting advice')
@section('canonical_url', route('blogs'))
@section('body_class', 'all-posts-page')

@section('content')
        <main>
            <section class="all-posts-masthead">
                <div class="container-xl all-posts-masthead-inner">
                    <nav class="breadcrumb-row" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>All
                            Blogs</span></nav>
                    <span class="eyebrow">Blog library</span>
                    <h1>All blogs for preschool families</h1>
                    <p>Browse practical reads by school life, family support, and at-home learning. Use filters to quickly
                        find the blog post closest to the question you have today.</p>
                </div>
            </section>

            <section class="section-block" id="latest" style="border-top: none; border-bottom: none;">
                <div class="container-xl">

                    <div class="article-tools" aria-label="Blog search and filters">
                        <div class="article-filter-panel">
                            <div class="filter-stack" aria-label="Blog category filters">
                                <div class="filter-group">
                                    <span>Main category</span>
                                    <div class="quick-filters main-filters">
                                        <button type="button" class="active" data-main-filter="all">All</button>
                                        @foreach($mainCategories as $mainCat)
                                            <button type="button" data-main-filter="{{ $mainCat->slug }}">{{ $mainCat->name }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <span>Sub category</span>
                                    <div class="quick-filters sub-filters">
                                        <button type="button" class="active" data-sub-filter="all">All</button>
                                        @foreach($subCategories as $subCat)
                                            <button type="button" data-sub-filter="{{ $subCat->slug }}">{{ $subCat->name }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label class="article-search">
                            <span>Search blogs</span>
                            <span class="article-search-field">
                                <svg aria-hidden="true" viewBox="0 0 24 24">
                                    <path
                                        d="M10.75 4.5a6.25 6.25 0 1 0 0 12.5 6.25 6.25 0 0 0 0-12.5ZM3 10.75a7.75 7.75 0 1 1 13.74 4.9l3.56 3.55a.78.78 0 0 1-1.1 1.1l-3.55-3.56A7.75 7.75 0 0 1 3 10.75Z">
                                    </path>
                                </svg>
                                <input type="search" placeholder="Search drop-off, food, play..." data-article-search>
                            </span>
                        </label>
                    </div>


                    <div class="article-grid all-posts-groups" data-article-group>
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
                                
                                $readTime = 5;
                                if (str_contains(strtolower($post->title), 'week')) { $readTime = 5; }
                                elseif (str_contains(strtolower($post->title), 'routine')) { $readTime = 5; }
                                elseif (str_contains(strtolower($post->title), 'playing')) { $readTime = 6; }
                                elseif (str_contains(strtolower($post->title), 'kitchen')) { $readTime = 3; }
                                
                                $isUrl = $post->featured_image && \Illuminate\Support\Str::contains($post->featured_image, '/');
                                $bgStyle = $isUrl ? 'style="background-image: url(' . asset($post->featured_image) . ');"' : '';
                                $mediaClass = $isUrl ? '' : ($post->featured_image ?? 'media-one');
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
                                    <span class="card-tag {{ $tagClass }}"><a href="{{ route('category.show', $subCategorySlug) }}">{{ $post->category->name ?? '' }}</a></span>
                                    <h3><a href="{{ $articleUrl }}">{{ $post->title }}</a></h3>
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
                    <p class="empty-state" data-empty-state>No matching blogs found. Try another search or filter.</p>
                </div>
            </section>
        </main>
@endsection
