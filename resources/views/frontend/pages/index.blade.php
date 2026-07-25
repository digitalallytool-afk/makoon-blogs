@extends('frontend.layout.main')
@section('title', 'Makoons Blogs | Preschool Parenting & Early Learning')
@section('meta_description',
    'Makoons Blogs — practical reads for preschool families on parenting, school life,
    activities, printables, stories, and early learning. Written by educators for real families.')
@section('meta_keywords',
    'preschool blogs, parenting tips, early learning, daycare, preschool activities, children
    education, parenting stories, printable worksheets, parenting sessions')
@section('canonical_url', route('home'))
@section('body_class', 'home-page')

@section('content')
    <main>
        <section class="article-hero">
            <div class="container-xl">
                <article class="hero-grid">
                    <div class="hero-copy-block">
                        <span class="eyebrow">Preschool & Parenting Blog</span>
                        <h1>Expert Advice For Parents, Educators and Preschool Franchise Owners</h1>
                        <p>
                            Welcome to our blog, a trusted source of information about preschools, parenting, child health
                            and nutrition, early learning, childcare, and starting preschool franchises. Whether you are a
                            parent seeking help with your child’s development or an entrepreneur who wants to own a
                            preschool franchise, you will find a lot of information in our articles.
                        </p>
                        <a href="#latest" class="primary-link">Start reading <span style="margin-left: 8px;"
                                aria-hidden="true">→</span></a>
                        <div class="editor-note" aria-label="Editor note">
                            <span>Editor’s note</span>
                            <p>Written for parents who want clear, useful reading without making childhood feel
                                complicated.</p>
                        </div>
                    </div>
                    <div class="hero-media" role="img"
                        aria-label="Children learning together in a bright preschool classroom"
                        style="--hero-bg: url('{{ $heroPost && $heroPost->featured_image ? asset($heroPost->featured_image) : asset('uploads/2024/10/kids-happily-builds-blocks-in-classroom-makoons.jpg') }}');">
                        <a href="{{ $heroPost ? route('blog.show', $heroPost->slug) : route('blogs') }}"
                            class="hero-feature-note" style="text-decoration: none; color: inherit; display: block;">
                            <span>This week</span>
                            <strong>{{ $heroPost->title ?? 'What helps children feel at home in a new classroom' }}</strong>
                        </a>
                    </div>
                </article>
            </div>
        </section>

        <nav class="mobile-home-jump" aria-label="Quick home sections">
            <a href="#featured">Featured</a>
            <a href="#latest">Blogs</a>
            <a href="#stories">Stories</a>
            <a href="#printables">Printables</a>
            <a href="#topics">Sessions</a>
        </nav>

        <section class="trending-section" aria-label="Trending articles and categories">
            <div class="container-xl trending-inner">
                <aside class="trending-sidebar">
                    <span class="eyebrow">Explore by topic</span>
                    <h2>What would you like to read about today?</h2>
                    <p>Find practical reads by the moments families ask about most: settling in, routines, food, play,
                        and early learning at home.</p>

                    <div class="trending-categories" aria-label="Blog categories">
                        @foreach ($homeCategories as $cat)
                            <a href="{{ route('category.show', $cat->slug) }}">
                                <span>{{ $cat->name }}</span>
                                <small>{{ $cat->posts_count }} {{ Str::plural('blog', $cat->posts_count) }}</small>
                            </a>
                        @endforeach
                    </div>

                    <form class="trending-subscribe" action="#" method="POST">
                        <label for="trend-email">Helpful preschool tips for your child’s everyday growth</label>
                        <div>
                            <input id="trend-email" name="email" type="email" placeholder="Email address" required>
                            <button type="submit">Subscribe</button>
                        </div>
                    </form>
                </aside>

                <div class="trending-posts" aria-label="Trending posts">
                    <div class="trending-posts-head">
                        <span class="eyebrow">Trending blogs</span>
                        <h3>Most-read blogs this week</h3>
                    </div>
                    <div class="trending-post-carousel" aria-label="Most-read blog carousel">
                        @foreach ($trendingPosts as $index => $post)
                            @php
                                $isUrl =
                                    $post->featured_image &&
                                    \Illuminate\Support\Str::contains($post->featured_image, '/');
                                $trendBgStyle = $isUrl
                                    ? 'style="background-image: url(' . asset($post->featured_image) . ');"'
                                    : '';
                                $trendClasses = ['trend-one', 'trend-two', 'trend-three', 'trend-four'];
                                $trendClass = $isUrl ? '' : $trendClasses[$index] ?? 'trend-one';

                                $readTime = 5;
                                if (str_contains(strtolower($post->title), 'week')) {
                                    $readTime = 5;
                                } elseif (str_contains(strtolower($post->title), 'routine')) {
                                    $readTime = 5;
                                } elseif (str_contains(strtolower($post->title), 'playing')) {
                                    $readTime = 6;
                                } elseif (str_contains(strtolower($post->title), 'kitchen')) {
                                    $readTime = 3;
                                }

                                $viewsStr =
                                    $post->view_count >= 1000
                                        ? number_format($post->view_count / 1000, 1) . 'k'
                                        : $post->view_count;
                                $articleUrl = route('blog.show', $post->slug);
                            @endphp
                            <article class="trending-post-card">
                                <a class="trending-post-image {{ $trendClass }}" {!! $trendBgStyle !!}
                                    href="{{ $articleUrl }}" aria-label="Read {{ $post->title }} blog post"></a>
                                <div>
                                    <span><a
                                            href="{{ route('category.show', $post->category->slug) }}">{{ $post->category->name ?? '' }}</a>
                                        · {{ $readTime }} min read</span>
                                    <h3><a href="{{ $articleUrl }}">{{ $post->title }}</a></h3>
                                    <div class="card-bottom-meta">
                                        <span class="view-count" aria-label="{{ $viewsStr }} views"><svg
                                                aria-hidden="true" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z">
                                                </path>
                                            </svg>{{ $viewsStr }} views</span>

                                        <a class="post-author"
                                            href="{{ route('author') }}?author={{ Str::slug($post->author->name ?? '') }}"
                                            aria-label="View blogs by {{ $post->author->name ?? '' }}">
                                            @if ($post->author && $post->author->image)
                                                <img src="{{ asset($post->author->image) }}"
                                                    alt="{{ $post->author->name }}">
                                            @endif
                                            <span>{{ $post->author->name ?? '' }}</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mobile-carousel-controls" aria-label="Trending blog slider controls">
                        <button type="button" data-carousel-target=".trending-post-carousel" data-carousel-direction="-1"
                            aria-label="Previous trending article"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                        <button type="button" data-carousel-target=".trending-post-carousel" data-carousel-direction="1"
                            aria-label="Next trending article"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block" id="latest">
            <div class="container-xl">
                <div class="section-title-row">
                    <div>
                        <span class="eyebrow">Latest blogs</span>
                        <h2>Recent posts</h2>
                    </div>
                    <a href="{{ route('blogs') }}">All posts</a>
                </div>

                <div class="article-tools" aria-label="Blog search and filters" style="background-color:#FBFAF7">
                    <div class="article-filter-panel">
                        <div class="filter-stack" aria-label="Blog category filters">
                            <div class="filter-group">
                                <span>Main category</span>
                                <div class="quick-filters main-filters">
                                    <button type="button" class="active" data-main-filter="all">All</button>
                                    @foreach ($mainCategories as $mainCat)
                                        <button type="button"
                                            data-main-filter="{{ $mainCat->slug }}">{{ $mainCat->name }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="filter-group">
                                <span>Sub category</span>
                                <div class="quick-filters sub-filters">
                                    <button type="button" class="active" data-sub-filter="all">All</button>
                                    @foreach ($subCategories as $subCat)
                                        <button type="button"
                                            data-sub-filter="{{ $subCat->slug }}">{{ $subCat->name }}</button>
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
                <div class="article-grid">
                    @foreach ($posts->take(32) as $post)
                        @php
                            $mainCategorySlug = $post->category->parent
                                ? $post->category->parent->slug
                                : $post->category->slug ?? '';
                            $subCategorySlug = $post->category->slug ?? '';

                            $tagClass = '';
                            if ($subCategorySlug === 'daycare') {
                                $tagClass = 'blue-tag';
                            } elseif ($subCategorySlug === 'parenting') {
                                $tagClass = 'yellow-tag';
                            } elseif ($subCategorySlug === 'activities') {
                                $tagClass = 'green-tag';
                            }

                            $readTime = 5;
                            if (str_contains(strtolower($post->title), 'week')) {
                                $readTime = 5;
                            } elseif (str_contains(strtolower($post->title), 'routine')) {
                                $readTime = 5;
                            } elseif (str_contains(strtolower($post->title), 'playing')) {
                                $readTime = 6;
                            } elseif (str_contains(strtolower($post->title), 'kitchen')) {
                                $readTime = 3;
                            }

                            $isUrl =
                                $post->featured_image && \Illuminate\Support\Str::contains($post->featured_image, '/');
                            $bgStyle = $isUrl
                                ? 'style="background-image: url(' . asset($post->featured_image) . ');"'
                                : '';
                            $mediaClass = $isUrl ? '' : $post->featured_image ?? 'media-one';
                            $viewsStr =
                                $post->view_count >= 1000
                                    ? number_format($post->view_count / 1000, 1) . 'k'
                                    : $post->view_count;
                            $articleUrl = route('blog.show', $post->slug);
                        @endphp
                        <article class="article-card" data-main-category="{{ $mainCategorySlug }}"
                            data-sub-category="{{ $subCategorySlug }}" data-category="{{ $subCategorySlug }}"
                            data-title="{{ $post->title }}">
                            <a class="card-media {{ $mediaClass }}" {!! $bgStyle !!} href="{{ $articleUrl }}"
                                aria-label="Read {{ $post->title }} blog post"></a>
                            <div class="card-copy">
                                <span class="card-tag {{ $tagClass }}"><a
                                        href="{{ route('category.show', $subCategorySlug) }}">{{ $post->category->name ?? '' }}</a></span>
                                <h3><a href="{{ $articleUrl }}">{{ $post->title }}</a></h3>
                                <div class="article-meta">
                                    <a class="post-author"
                                        href="{{ route('author') }}?author={{ Str::slug($post->author->name ?? '') }}"
                                        aria-label="View blogs by {{ $post->author->name ?? '' }}">
                                        @if ($post->author && $post->author->image)
                                            <img src="{{ $post->author->image }}" alt="{{ $post->author->name }}">
                                        @else
                                            <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%20120%20120%22%3E%0A%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2260%22%20fill%3D%22%23f9efe5%22/%3E%0A%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2253%22%20r%3D%2228%22%20fill%3D%22%23b9794e%22/%3E%0A%20%20%3Cpath%20d%3D%22M31%2053c2-24%2016-35%2031-35%2018%200%2030%2014%2029%2036-11-11-21-11-32-10-12%201-20-2-28%209z%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Cpath%20d%3D%22M24%20114c6-24%2021-36%2036-36s30%2012%2036%2036%22%20fill%3D%22%23f5b42b%22/%3E%0A%20%20%3Cpath%20d%3D%22M38%2084c8%208%2036%208%2044%200%22%20stroke%3D%22%23ffffff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%20fill%3D%22none%22/%3E%0A%20%20%3Ccircle%20cx%3D%2250%22%20cy%3D%2255%22%20r%3D%223%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Ccircle%20cx%3D%2270%22%20cy%3D%2255%22%20r%3D%223%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Cpath%20d%3D%22M52%2066c5%204%2011%204%2016%200%22%20stroke%3D%22%232e1a12%22%20stroke-width%3D%223%22%20stroke-linecap%3D%22round%22%20fill%3D%22none%22/%3E%0A%20%20%3Ctitle%3EIndian%20classroom%20educator%20avatar%3C/title%3E%0A%3C/svg%3E"
                                                alt="">
                                        @endif
                                        <span>{{ $post->author->name ?? '' }}</span>
                                    </a>
                                    <div class="read-info">
                                        <time
                                            datetime="{{ $post->created_at->format('Y-m-d') }}">{{ $post->created_at->format('F j, Y') }}</time>
                                        <span>{{ $readTime }} min</span>
                                        <span class="view-count" aria-label="{{ $viewsStr }} views"><svg
                                                aria-hidden="true" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z">
                                                </path>
                                            </svg>{{ $viewsStr }} views</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mobile-carousel-controls" aria-label="Latest blog slider controls" data-latest-carousel-controls>
                    <button type="button" data-carousel-target="#latest .article-grid" data-carousel-direction="-1"
                        aria-label="Previous blog"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                    <button type="button" data-carousel-target="#latest .article-grid" data-carousel-direction="1"
                        aria-label="Next blog"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                </div>
                <div style="display: flex; justify-content: center; margin-top: 3.5rem;">
                    <a href="{{ route('blogs') }}" class="primary-link" style="margin-top: 0;">Explore all posts <span
                            style="margin-left: 8px;" aria-hidden="true">→</span></a>
                </div>
                <p class="empty-state" data-empty-state>No matching blogs found. Try another keyword or category.
                </p>
            </div>
        </section>

        <section class="section-block featured-slider-section" id="featured">
            <div class="container-xl">
                <div class="section-title-row">
                    <div>
                        <span class="eyebrow">Selected blogs</span>
                        <h2>A few pieces parents keep coming back to</h2>
                    </div>
                    <div class="slider-actions" aria-label="Slider controls">
                        <button class="slider-button" type="button" data-slider-prev
                            aria-label="Previous featured article"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                        <button class="slider-button" type="button" data-slider-next
                            aria-label="Next featured article"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                    </div>
                </div>

                <div class="article-slider" data-article-slider>
                    @foreach ($featuredPosts as $index => $post)
                        @php
                            $isUrl =
                                $post->featured_image && \Illuminate\Support\Str::contains($post->featured_image, '/');
                            $featureBgStyle = $isUrl
                                ? 'style="background-image: url(' . asset($post->featured_image) . ');"'
                                : '';
                            $featureClasses = [
                                'feature-one',
                                'feature-two',
                                'feature-three',
                                'feature-four',
                                'feature-five',
                            ];
                            $featureClass = $isUrl ? '' : $featureClasses[$index] ?? 'feature-one';
                            $viewsStr =
                                $post->view_count >= 1000
                                    ? number_format($post->view_count / 1000, 1) . 'k'
                                    : $post->view_count;
                            $articleUrl = route('blog.show', $post->slug);
                        @endphp
                        <article class="feature-card">
                            <a href="{{ $articleUrl }}" class="feature-media {{ $featureClass }}"
                                {!! $featureBgStyle !!} aria-label="Read {{ $post->title }} blog post"></a>
                            <div class="feature-copy">
                                <span><a
                                        href="{{ route('category.show', $post->category->slug) }}">{{ $post->category->name ?? '' }}</a></span>
                                <h3><a href="{{ $articleUrl }}">{{ $post->title }}</a></h3>
                                <div class="card-bottom-meta">
                                    <span class="view-count" aria-label="{{ $viewsStr }} views"><svg
                                            aria-hidden="true" viewBox="0 0 24 24">
                                            <path
                                                d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z">
                                            </path>
                                        </svg>{{ $viewsStr }} views</span>

                                    <a class="post-author"
                                        href="{{ route('author') }}?author={{ Str::slug($post->author->name ?? '') }}"
                                        aria-label="View blogs by {{ $post->author->name ?? '' }}">
                                        @if ($post->author && $post->author->image)
                                            <img src="{{ asset($post->author->image) }}"
                                                alt="{{ $post->author->name }}">
                                        @endif
                                        <span>{{ $post->author->name ?? '' }}</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>



        <section class="category-card-band topic-directory-section">
            <div class="container-xl topic-directory">
                <div class="category-intro topic-directory-intro">
                    <span class="eyebrow">Topics</span>
                    <h2>What would you like to read about today?</h2>
                    <p>Start with the question closest to your day, then read short, practical blog posts from preschool
                        life, daycare routines, home activities, and everyday parenting.</p>
                </div>

                <div class="topic-directory-list" aria-label="Blog topic categories">
                    @foreach ($homeCategories->values() as $index => $cat)
                        <a class="topic-directory-item" href="{{ route('category.show', $cat->slug) }}">
                            <span class="topic-code">0{{ $index + 1 }}</span>
                            <div>
                                <strong>{{ $cat->name }}</strong>
                                <em>{{ $cat->description ?? 'Explore blogs and guides under ' . $cat->name . '.' }}</em>
                            </div>
                            <small>{{ $cat->posts_count }} {{ Str::plural('blog', $cat->posts_count) }}</small>
                            <span class="topic-arrow" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-block recent-stories-section" id="stories">
            <div class="container-xl recent-stories-inner">
                <div class="recent-stories-head stories-head-with-link">
                    <div>
                        <span class="eyebrow">Recent stories</span>
                        <h2>Small school moments worth reading</h2>
                        <p>Fresh stories from classrooms, play corners, and everyday preschool routines, told in a
                            lighter, more visual way.</p>
                    </div>
                    <a href="{{ route('stories') }}" class="story-explore-link">Explore all stories <span
                            aria-hidden="true">→</span></a>
                </div>

                <div class="story-board" aria-label="Recent preschool stories">
                    @if ($firstStory = $stories->first())
                        @php
                            $readTime = 4;
                            if (str_contains(strtolower($firstStory->title), 'helper')) {
                                $readTime = 4;
                            } elseif (str_contains(strtolower($firstStory->title), 'lunchbox')) {
                                $readTime = 3;
                            } elseif (str_contains(strtolower($firstStory->title), 'paint')) {
                                $readTime = 4;
                            } elseif (str_contains(strtolower($firstStory->title), 'song')) {
                                $readTime = 5;
                            }
                            $firstStoryUrl = route('story.show', $firstStory->slug);
                            $isFirstStoryUrl =
                                $firstStory->featured_image &&
                                \Illuminate\Support\Str::contains($firstStory->featured_image, '/');
                            $firstBgStyle = $isFirstStoryUrl
                                ? 'style="background-image: url(' . asset($firstStory->featured_image) . ');"'
                                : '';
                            $firstStoryImgClass = $isFirstStoryUrl ? '' : $firstStory->featured_image ?? 'story-one';
                        @endphp
                        <article class="story-feature">
                            <a class="story-feature-image {{ $firstStoryImgClass }}" {!! $firstBgStyle !!}
                                href="{{ $firstStoryUrl }}" aria-label="Read {{ $firstStory->title }}"></a>
                            <div class="story-feature-copy"
                                style="display: flex; flex-direction: column; justify-content: space-between; min-height: 220px;">
                                <div>
                                    <span class="card-tag yellow-tag"
                                        style="margin-bottom: 0.55rem; display: inline-block;">{{ $firstStory->storyCategory->name ?? '' }}</span>
                                    <h3 style="margin-bottom: 0.55rem;"><a
                                            href="{{ $firstStoryUrl }}">{{ $firstStory->title }}</a></h3>
                                    <p style="margin-bottom: 1rem;">{{ $firstStory->excerpt }}</p>
                                </div>
                                <div class="article-meta"
                                    style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(17,17,17,0.06);">
                                    <a class="post-author"
                                        href="{{ route('author') }}?author={{ Str::slug($firstStory->author->name ?? '') }}&type=stories">
                                        @if ($firstStory->author && $firstStory->author->image)
                                            <img src="{{ asset($firstStory->author->image) }}"
                                                alt="{{ $firstStory->author->name }}">
                                        @endif
                                        <span>{{ $firstStory->author->name ?? 'Makoons' }}</span>
                                    </a>
                                    <div class="read-info">
                                        <time
                                            datetime="{{ $firstStory->created_at->format('Y-m-d') }}">{{ $firstStory->created_at->format('F j, Y') }}</time>
                                        <span>{{ $firstStory->view_count >= 1000 ? number_format($firstStory->view_count / 1000, 1) . 'k' : $firstStory->view_count }}
                                            views</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endif

                    <div class="story-stack">
                        @foreach ($stories->skip(1) as $index => $story)
                            @php
                                $storyClasses = ['story-two', 'story-three', 'story-four'];
                                $storyClass = $storyClasses[$index] ?? 'story-two';
                                $storyUrl = route('story.show', $story->slug);
                                $isStoryUrl =
                                    $story->featured_image &&
                                    \Illuminate\Support\Str::contains($story->featured_image, '/');
                                $bgStyle = $isStoryUrl
                                    ? 'style="background-image: url(' . asset($story->featured_image) . ');"'
                                    : '';
                                $storyImgClass = $isStoryUrl ? '' : $story->featured_image ?? $storyClass;
                            @endphp
                            <article class="story-strip">
                                <a class="story-strip-image {{ $storyImgClass }}" {!! $bgStyle !!}
                                    href="{{ $storyUrl }}" aria-label="Read {{ $story->title }}"></a>
                                <div
                                    style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; width: 100%;">
                                    <div>
                                        <span
                                            style="font-size: 11px; color: #b9794e; font-weight: 600; text-transform: uppercase;">{{ $story->storyCategory->name ?? '' }}</span>
                                        <h3
                                            style="margin-top: 2px; margin-bottom: 6px; font-size: 15px; font-weight: 700; line-height: 1.3;">
                                            <a href="{{ $storyUrl }}">{{ $story->title }}</a>
                                        </h3>
                                        <p
                                            style="font-size: 12px; color: #666; margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $story->excerpt }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between"
                                        style="font-size: 11px; color: #777; border-top: 1px solid rgba(17,17,17,0.04); padding-top: 6px; margin-top: 4px;">
                                        <a class="d-flex align-items-center gap-1 text-dark"
                                            href="{{ route('author') }}?author={{ Str::slug($story->author->name ?? '') }}&type=stories"
                                            style="font-weight: 600; text-decoration: none;">
                                            @if ($story->author && $story->author->image)
                                                <img src="{{ asset($story->author->image) }}"
                                                    alt="{{ $story->author->name }}"
                                                    style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;">
                                            @endif
                                            <span>{{ $story->author->name ?? 'Makoons' }}</span>
                                        </a>
                                        <span>{{ $story->view_count >= 1000 ? number_format($story->view_count / 1000, 1) . 'k' : $story->view_count }}
                                            views</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mobile-carousel-controls" aria-label="Story slider controls">
                        <button type="button" data-carousel-target=".story-stack" data-carousel-direction="-1"
                            aria-label="Previous story"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                        <button type="button" data-carousel-target=".story-stack" data-carousel-direction="1"
                            aria-label="Next story"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                    </div>
                </div>
                <div style="display: flex; justify-content: center; margin-top: 3.5rem;">
                    <a href="{{ route('stories') }}" class="primary-link" style="margin-top: 0;">Explore all stories
                        <span style="margin-left: 8px;" aria-hidden="true">→</span></a>
                </div>
            </div>
        </section>

        <section class="section-block printables-section" id="printables">
            <div class="container-xl printables-inner printable-collage-inner">
                <div class="printables-intro printable-collage-intro printables-head-with-link">
                    <div>
                        <span class="eyebrow">Free printables</span>
                        <h2>Free printable coloring pages for kids</h2>
                        <p>Welcome to our printables corner. You will find playful sheets for coloring, festivals, space
                            adventures, and quiet creative time at home. Come on, let us make art.</p>
                    </div>
                    <a href="{{ route('printables') }}" class="printables-explore-link">Explore all printables <span
                            aria-hidden="true">→</span></a>
                </div>

                <div class="printables-grid printable-collage-grid" aria-label="Printable activity downloads">
                    @foreach ($printables as $index => $printable)
                        @php
                            $printableClasses = [
                                'printable-halloween',
                                'printable-holidays',
                                'printable-halloween-two',
                                'printable-space',
                            ];
                            $printableClass = $printableClasses[$index] ?? 'printable-halloween';
                        @endphp
                        @php
                            $isPrintableUrl =
                                $printable->image && \Illuminate\Support\Str::contains($printable->image, '/');
                            $bgStyle = $isPrintableUrl
                                ? 'style="background-image: url(' .
                                    asset($printable->image) .
                                    '); background-size: contain; background-repeat: no-repeat; background-position: center; background-color: #ffffff;"'
                                : '';
                            $mediaClass = $printable->image ?? $printableClass;
                        @endphp
                        <article class="printable-card printable-collage-card">
                            <a class="printable-preview printable-fallback {{ $mediaClass }}" {!! $bgStyle !!}
                                href="{{ route('printables') }}" aria-label="Open {{ $printable->name }} printable">
                                @if (!$isPrintableUrl)
                                    <span>{{ $printable->name }}</span>
                                @endif
                            </a>
                            <div class="printable-copy">
                                <h3><a href="{{ route('printables') }}">{{ $printable->name }}</a></h3>
                                <p>{{ $printable->description }}</p>
                                <a href="{{ asset($printable->file_path) }}" class="printable-action"
                                    download>Download</a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mobile-carousel-controls" aria-label="Printable slider controls">
                    <button type="button" data-carousel-target=".printables-section .printable-collage-grid"
                        data-carousel-direction="-1" aria-label="Previous printable"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                    <button type="button" data-carousel-target=".printables-section .printable-collage-grid"
                        data-carousel-direction="1" aria-label="Next printable"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                </div>
            </div>
        </section>

        <section class="section-block parent-sessions-section" id="topics">
            <div class="container-xl parent-sessions-inner">
                <div class="section-title-row parent-sessions-head">
                    <div>
                        <span class="eyebrow">Parenting sessions</span>
                        <h2>Watch short guides made for preschool parents</h2>
                        <p>Simple video sessions on growth, food choices, classroom exposure, and the everyday questions
                            parents bring to school.</p>
                    </div>
                    <a href="{{ route('sessions') }}" class="parent-sessions-link">View all sessions <span
                            aria-hidden="true">→</span></a>
                </div>

                <div class="parent-session-grid" aria-label="Parenting video sessions">
                    @foreach ($videoSessions as $index => $session)
                        @php
                            $sessionThumbs = ['session-one', 'session-two', 'session-three'];
                            $sessionThumb = $sessionThumbs[$index] ?? 'session-one';

                            $duration = 10;
                            if (str_contains(strtolower($session->title), 'confidence')) {
                                $duration = 12;
                            } elseif (str_contains(strtolower($session->title), 'food')) {
                                $duration = 9;
                            } elseif (str_contains(strtolower($session->title), 'growing')) {
                                $duration = 10;
                            }
                        @endphp
                        @php
                            $hasCustomImage = !empty($session->image);
                            $imageUrl = $hasCustomImage ? asset($session->image) : null;
                        @endphp
                        <article class="parent-session-card">
                            @if ($hasCustomImage)
                                <a class="session-thumb" href="{{ $session->video_url ?? '#' }}"
                                    data-video-url="{{ $session->video_url }}"
                                    style="background-image: url('{{ $imageUrl }}');"
                                    aria-label="Watch {{ $session->title }}">
                                    <span class="play-icon" aria-hidden="true">▶</span>
                                    <small>{{ $session->duration ?? $duration }} min</small>
                                </a>
                            @else
                                <div class="session-thumb video-playing">
                                    <iframe src="{{ $session->video_url }}" style="width:100%; height:100%; border:0;"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                            @endif
                            <div class="session-copy">
                                <span>{{ $session->sessionCategory->name ?? '' }}</span>
                                <h3><a href="{{ route('sessions') }}">{{ $session->title }}</a></h3>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mobile-carousel-controls" aria-label="Parenting session slider controls">
                    <button type="button" data-carousel-target=".parent-session-grid" data-carousel-direction="-1"
                        aria-label="Previous parenting session"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                    <button type="button" data-carousel-target=".parent-session-grid" data-carousel-direction="1"
                        aria-label="Next parenting session"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="pointer-events: none;"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                </div>
            </div>
        </section>

        <section class="about-note" id="about">
            <div class="container-xl founder-grid">
                <div>
                    <span class="eyebrow">About these blogs</span>
                    <h2>Written from everyday school life.</h2>
                    <p>These blogs come from the small questions we hear every week: food, naps, friendships, first
                        tears, messy play, and how children slowly become more independent.</p>
                    <a href="{{ route('about') }}" class="primary-link dark-link editorial-cta">Why we write <span
                            aria-hidden="true">→</span></a>
                </div>
                <div class="founder-image" role="img" aria-label="A welcoming preschool activity space"></div>
            </div>
        </section>
    </main>
@endsection
