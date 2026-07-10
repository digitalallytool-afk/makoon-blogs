<div class="search-overlay" data-search-overlay aria-hidden="true">
    <div class="search-backdrop" data-search-close></div>
    <section class="search-panel" role="dialog" aria-modal="true" aria-label="Search blogs">
        <div class="search-panel-head">
            <span>Search blogs</span>
            <button type="button" data-search-close aria-label="Close search">×</button>
        </div>
        <label class="global-search-field">
            <svg aria-hidden="true" viewBox="0 0 24 24">
                <path
                    d="M10.75 4.5a6.25 6.25 0 1 0 0 12.5 6.25 6.25 0 0 0 0-12.5ZM3 10.75a7.75 7.75 0 1 1 13.74 4.9l3.56 3.55a.78.78 0 0 1-1.1 1.1l-3.55-3.56A7.75 7.75 0 0 1 3 10.75Z">
                </path>
            </svg>
            <input type="search" placeholder="Search preschool, daycare, parenting..." data-global-search-input>
        </label>
        <div class="search-suggestions" data-global-search-results>
            @php
                $searchPosts = \App\Models\Post::with('category')->published()->latest()->get();
            @endphp
            @foreach($searchPosts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" data-search-item
                    data-title="{{ $post->title }}" data-category="{{ $post->category->name ?? '' }}">
                    {{ $post->title }} <span>{{ $post->category->name ?? '' }}</span>
                </a>
            @endforeach
        </div>
        <p class="search-empty" data-global-search-empty>No matching blogs found.</p>
    </section>
</div>
<header class="site-header sticky-top">
    <nav class="navbar">
        <div class="container-xl nav-shell">
            <a class="brand-lockup" href="{{ route('home') }}" aria-label="Makoons Blogs home">
                <img src="{{ asset('frontend/images/makoons-logo.png') }}" alt="Makoons logo">
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mainNav">
                <span></span>
                <span></span>
            </button>

            <div class="navbar-collapse" id="mainNav">
                <ul class="navbar-nav">
                    <li><a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li><a class="nav-link {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">About us</a></li>
                    <li><a class="nav-link {{ Route::is('blogs') || Route::is('blog.show') || Route::is('blog.details') ? 'active' : '' }}" href="{{ route('blogs') }}">Blogs</a></li>
                    <li><a class="nav-link {{ Route::is('stories') ? 'active' : '' }}" href="{{ route('stories') }}">Stories</a></li>
                    <li><a class="nav-link {{ Route::is('printables') ? 'active' : '' }}" href="{{ route('printables') }}">Printables</a></li>
                    <li><a class="nav-link {{ Route::is('sessions') ? 'active' : '' }}" href="{{ route('sessions') }}">Sessions</a></li>
                    <li><a class="nav-link" href="#contact">Contact</a></li>
                    <li><button class="search-button" type="button" aria-label="Open search" aria-expanded="false"
                            data-search-open><svg aria-hidden="true" viewBox="0 0 24 24">
                                <path
                                    d="M10.75 4.5a6.25 6.25 0 1 0 0 12.5 6.25 6.25 0 0 0 0-12.5ZM3 10.75a7.75 7.75 0 1 1 13.74 4.9l3.56 3.55a.78.78 0 0 1-1.1 1.1l-3.55-3.56A7.75 7.75 0 0 1 3 10.75Z">
                                </path>
                            </svg><span>Search</span></button></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
