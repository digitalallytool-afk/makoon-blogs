@extends('frontend.layout.main')
@section('title', ($post->meta_title ?? $post->title) . ' | Makoons Blogs')
@section('meta_description', $post->meta_description ?? $post->excerpt)
@section('meta_keywords', $post->meta_keywords ?? ($post->category->name ?? 'preschool') . ', parenting, early learning, Makoons')
@section('canonical_url', $post->canonical_url ? rtrim($post->canonical_url, '/') : route('blog.show', $post->slug))
@section('body_class', 'detail-page')

@section('content')
    @if(isset($isPreview) && $isPreview)
        <div style="background-color: #fff3cd; color: #664d03; padding: 12px; text-align: center; font-weight: bold; border-bottom: 1px solid #ffe69c; position: sticky; top: 0; z-index: 1050; font-family: 'Inter', sans-serif;">
            ⚠️ Preview Mode: This blog post is not yet published.
        </div>
    @endif
    <div class="reading-progress" data-reading-progress></div>
    <main>
      <section class="article-masthead">
        <div class="container-xl article-masthead-inner">
          <div class="article-masthead-kicker">
            <nav class="breadcrumb-row" aria-label="Breadcrumb">
              <a href="{{ route('blogs') }}">Blogs</a>
              @if($post->category)
                @php
                  $displayCategory = $post->category->parent ?? $post->category;
                @endphp
                <span>/</span>
                <a href="{{ route('category.show', $displayCategory->slug) }}">{{ $displayCategory->name }}</a>
              @endif
            </nav>
          </div>
          <h1>{{ $post->title }}</h1>
          <p>
            {{ $post->excerpt }}
          </p>

          <div class="article-topline">
            <div class="detail-byline article-author-block">
              <a class="article-author-link" href="{{ route('author') }}?author={{ Str::slug($post->author->name ?? '') }}" aria-label="View blogs by {{ $post->author->name ?? '' }}">
                @if($post->author && $post->author->image)
                  <img src="{{ asset($post->author->image) }}" alt="{{ $post->author->name }}">
                @else
                  <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20viewBox%3D%220%200%20120%20120%22%3E%0A%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2260%22%20fill%3D%22%23f9efe5%22/%3E%0A%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2252%22%20r%3D%2228%22%20fill%3D%22%23b9794e%22/%3E%0A%20%20%3Cpath%20d%3D%22M31%2053c2-24%2016-35%2031-35%2018%200%2030%2014%2029%2036-11-11-21-11-32-10-12%201-20-2-28%209z%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Cpath%20d%3D%22M24%20114c6-24%2021-36%2036-36s30%2012%2036%2036%22%20fill%3D%22%23f5b42b%22/%3E%0A%20%20%3Cpath%20d%3D%22M38%2084c8%208%2036%208%2044%200%22%20stroke%3D%22%23fff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%20fill%3D%22none%22/%3E%0A%20%20%3Ccircle%20cx%3D%2250%22%20cy%3D%2255%22%20r%3D%223%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Ccircle%20cx%3D%2270%22%20cy%3D%2255%22%20r%3D%223%22%20fill%3D%22%232e1a12%22/%3E%0A%20%20%3Cpath%20d%3D%22M52%2066c5%204%2011%204%2016%200%22%20stroke%3D%22%232e1a12%22%20stroke-width%3D%223%22%20stroke-linecap%3D%22round%22%20fill%3D%22none%22/%3E%0A%3C/svg%3E" alt="">
                @endif
                <span>
                  <strong>{{ $post->author->name ?? 'Makoons Team' }}</strong>
                  <em>Blog author</em>
                </span>
              </a>
              <span class="article-meta-line">
                <span>{{ $post->created_at->format('F j, Y') }}</span>
                <span>5 min read</span>
                @if($post->category)
                  @php
                    $displayCategory = $post->category->parent ?? $post->category;
                  @endphp
                  <span><a href="{{ route('category.show', $displayCategory->slug) }}">{{ $displayCategory->name }}</a></span>
                @endif
                <span class="detail-view-count" aria-label="{{ $post->view_count }} views">
                  <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"></path></svg>
                  {{ $post->view_count >= 1000 ? number_format($post->view_count / 1000, 1) . 'k' : $post->view_count }} views
                </span>
              </span>
            </div>

            <div class="socialShare" aria-label="Share this blog post">
              <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" class="socialShare__link" data-social-share="Facebook" aria-label="Share to Facebook">
                <span class="icon"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m15.9700599 1c-8.26766469 0-14.9700599 6.70239521-14.9700599 14.9700599 0 7.0203593 4.83353293 12.9113772 11.3538922 14.5293413v-9.954491h-3.08682633v-4.5748503h3.08682633v-1.9712575c0-5.09520959 2.305988-7.45688623 7.3083832-7.45688623.948503 0 2.58503.18622754 3.2544911.37185629v4.14670654c-.3532934-.0371257-.9670659-.0556886-1.7293414-.0556886-2.454491 0-3.402994.9299401-3.402994 3.3473054v1.6179641h4.8898204l-.8401198 4.5748503h-4.0497006v10.2856287c7.4125749-.8952096 13.1562875-7.2065868 13.1562875-14.860479-.0005988-8.26766469-6.702994-14.9700599-14.9706587-14.9700599z"></path></svg></span>
              </a>
              <a target="_blank" href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}" class="socialShare__link" data-social-share="Twitter" aria-label="Share to Twitter">
                <span class="icon"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m18.461198 13.6964303 10.9224206-12.6964303h-2.5882641l-9.4839364 11.024132-7.57479218-11.024132h-8.73662592l11.4545721 16.6704401-11.4545721 13.3141565h2.58841076l10.01528114-11.6418582 7.9995355 11.6418582h8.7366259l-11.879291-17.2881663zm-3.5451833 4.1208802-1.1605868-1.66-9.23437656-13.20879216h3.97564793l7.45224943 10.65991686 1.1605868 1.66 9.6870415 13.8562592h-3.9756479l-7.9049144-11.3067482z"></path></svg></span>
              </a>
              <a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}" class="socialShare__link" data-social-share="LinkedIn" aria-label="Share on LinkedIn">
                <span class="icon"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 2.3391C0 1.04725 1.04653 0 2.3391 0H25.6609C26.9527 0 28 1.04653 28 2.3391V25.6609C28 26.9527 26.9535 28 25.6609 28H2.3391C1.04725 28 0 26.9535 0 25.6609V2.3391ZM11.0833 10.675H14.875V12.5802C15.4216 11.4847 16.8222 10.5 18.9263 10.5C22.9594 10.5 23.9167 12.6805 23.9167 16.6816V24.0917H19.8333V17.5928C19.8333 15.3143 19.2868 14.0292 17.8967 14.0292C15.9688 14.0292 15.1667 15.4146 15.1667 17.5928V24.0917H11.0833V10.675ZM4.08333 23.9167H8.16667V10.5H4.08333V23.9167ZM8.75 6.125C8.75 7.57458 7.57458 8.75 6.125 8.75C4.67542 8.75 3.5 7.57458 3.5 6.125C3.5 4.67542 4.67542 3.5 6.125 3.5C7.57458 3.5 8.75 4.67542 8.75 6.125Z" fill="currentColor"></path></svg></span>
              </a>
              <a target="_blank" href="https://www.pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}&description={{ urlencode($post->title) }}" class="socialShare__link" data-social-share="Pinterest" aria-label="Share to Pinterest">
                <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 999.9 999.9" width="28" height="28"><path d="M0 500c2.6-141.9 52.7-260.4 150.4-355.4S364.6 1.3 500 0c145.8 2.6 265.3 52.4 358.4 149.4 93.1 97 140.3 213.9 141.6 350.6-2.6 140.6-52.7 258.8-150.4 354.5-97.7 95.6-214.2 144.1-349.6 145.4-46.9 0-93.7-7.2-140.6-21.5 9.1-14.3 18.2-30.6 27.3-48.8 10.4-22.1 23.4-63.8 39.1-125 3.9-16.9 9.8-39.7 17.6-68.4 9.1 15.6 24.7 29.9 46.9 43 58.6 27.3 120.4 24.7 185.5-7.8 67.7-39.1 114.6-99.6 140.6-181.6 23.4-85.9 20.5-165.7-8.8-239.2C778.3 277 725.9 224 650.4 191.4c-95-27.3-187.5-24.4-277.3 8.8s-152.3 90.2-187.5 170.9C176.5 401 171 430.7 169 460c-2 29.3-1 57.9 2.9 85.9s13.7 53.1 29.3 75.2 36.5 39.1 62.5 50.8c6.5 2.6 11.7 2.6 15.6 0 5.2-2.6 10.4-13 15.6-31.2 5.2-18.2 7.2-30.6 5.9-37.1-1.3-2.6-3.9-7.2-7.8-13.7-27.3-44.3-36.5-90.8-27.3-139.6 9.1-48.8 29.3-90.2 60.5-124 48.2-43 104.5-66.4 168.9-70.3 64.4-3.9 119.5 13.7 165 52.7 24.7 28.6 40.7 63.1 47.8 103.5s7.2 79.1 0 116.2c-7.2 37.1-19.9 71.9-38.1 104.5-32.6 50.8-71 76.8-115.2 78.1-26-1.3-47.2-11.4-63.5-30.3s-21.2-40.7-14.6-65.4c2.6-14.3 10.4-42.3 23.4-84 13-41.7 20.2-72.9 21.5-93.7-3.9-49.5-26.7-74.9-68.4-76.2-32.6 3.9-56.6 18.6-72.3 43.9s-24.1 54.4-25.4 86.9c3.9 37.8 9.8 63.8 17.6 78.1-14.3 58.6-25.4 105.5-33.2 140.6-2.6 9.1-9.8 37.1-21.5 84s-18.2 82.7-19.5 107.4V957C206.3 914 133.3 851.9 80 770.5 26.7 689.1 0 598.9 0 500z"></path></svg></span>
              </a>
              <button class="socialShare__link socialShare__clip" aria-label="Copy Link" data-copy-link>
                <span class="socialShare__copyChain"><span class="icon"><svg width="28" height="28" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M459.654,233.373l-90.531,90.5c-49.969,50-131.031,50-181,0c-7.875-7.844-14.031-16.688-19.438-25.813l42.063-42.063c2-2.016,4.469-3.172,6.828-4.531c2.906,9.938,7.984,19.344,15.797,27.156c24.953,24.969,65.563,24.938,90.5,0l90.5-90.5c24.969-24.969,24.969-65.563,0-90.516c-24.938-24.953-65.531-24.953-90.5,0l-32.188,32.219c-26.109-10.172-54.25-12.906-81.641-8.891l68.578-68.578c50-49.984,131.031-49.984,181.031,0C509.623,102.342,509.623,183.389,459.654,233.373z M220.326,382.186l-32.203,32.219c-24.953,24.938-65.563,24.938-90.516,0c-24.953-24.969-24.953-65.563,0-90.531l90.516-90.5c24.969-24.969,65.547-24.969,90.5,0c7.797,7.797,12.875,17.203,15.813,27.125c2.375-1.375,4.813-2.5,6.813-4.5l42.063-42.047c-5.375-9.156-11.563-17.969-19.438-25.828c-49.969-49.984-131.031-49.984-181.016,0l-90.5,90.5c-49.984,50-49.984,131.031,0,181.031c49.984,49.969,131.031,49.969,181.016,0l68.594-68.594C274.561,395.092,246.42,392.342,220.326,382.186z"></path></svg></span></span>
                <span class="socialShare__copyCheck"><span class="icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"></path></svg></span></span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <section class="article-layout-section">
        <div class="container-xl article-clean-layout">
          <aside class="article-keyword-sidebar article-detail-side" aria-label="Blog sidebar">
            <div class="trending-sidebar detail-topic-panel" aria-label="Explore blogs by topic">
              <span class="eyebrow">Explore by topic</span>
              <h2>Trending blogs for everyday parent questions</h2>
              <p>Find practical reads by the moments families ask about most: settling in, routines, food, play, and early learning at home.</p>

              <div class="detail-topic-articles" aria-label="Trending blogs">
                @foreach($trendingPosts as $rPost)
                  @php
                    $rViewsStr = $rPost->view_count >= 1000 ? number_format($rPost->view_count / 1000, 1) . 'k' : $rPost->view_count;
                  @endphp
                  <a href="{{ route('blog.show', $rPost->slug) }}">
                    <span>
                      @if($rPost->category)
                        {{ $rPost->category->name }} · 
                      @endif
                      5 min read
                    </span>
                    <strong>{{ $rPost->title }}</strong>
                  </a>
                @endforeach
              </div>

              <form class="trending-subscribe detail-topic-subscribe" action="#" method="POST">
                <label for="detail-topic-email">Helpful preschool tips for your child’s everyday growth</label>
                <div>
                  <input id="detail-topic-email" name="email" type="email" placeholder="Email address" required>
                  <button type="submit">Subscribe</button>
                </div>
              </form>
            </div>

            <div class="article-category-card" aria-label="Blog categories">
              <div class="category-card-head">
                <span class="keyword-label">Categories</span>
                <p>Explore more blogs by topic</p>
              </div>
              @foreach($homeCategories as $hCat)
                <a href="{{ route('category.show', $hCat->slug) }}" class="{{ $hCat->id === $post->category_id ? 'active' : '' }}">
                  <span>{{ $hCat->name }}</span>
                  <small>{{ $hCat->posts_count }} {{ Str::plural('blog', $hCat->posts_count) }}</small>
                </a>
              @endforeach
            </div>
          </aside>

          <article class="article-content clean-article" data-article-content>
            @if($post->featured_image)
              <div class="detail-featured-image-wrapper" style="margin-bottom: 2rem; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: auto; display: block;">
              </div>
            @endif

            @if($post->excerpt && !str_contains($post->content, $post->excerpt))
              <p class="lead-paragraph">
                {{ $post->excerpt }}
              </p>
            @endif

            {!! $post->content !!}

            <div class="article-end-share">
              <span>Found this helpful?</span>
              <button type="button" data-copy-link>Copy blog link</button>
            </div>
          </article>
        </div>
      </section>

      <section class="related-clean-section">
        <div class="container-xl related-clean-inner">
          <div class="related-clean-heading">
            <span class="eyebrow">Trending Blogs</span>
            <h2>Popular posts</h2>
          </div>

          <div class="related-article-grid">
            @foreach($trendingPosts as $index => $tPost)
              @php
                $isUrl = $tPost->featured_image && \Illuminate\Support\Str::contains($tPost->featured_image, '/');
                $bgStyle = $isUrl ? 'style="background-image: url(' . asset($tPost->featured_image) . ');"' : '';
                $tViewsStr = $tPost->view_count >= 1000 ? number_format($tPost->view_count / 1000, 1) . 'k' : $tPost->view_count;
                $mediaClasses = ['related-one', 'related-two', 'related-three', 'related-four'];
                $mediaClass = $isUrl ? '' : ($mediaClasses[$index % 4] ?? 'related-one');
              @endphp
              <article class="related-article-card">
                <a class="related-article-media {{ $mediaClass }}" {!! $bgStyle !!} href="{{ route('blog.show', $tPost->slug) }}" aria-label="Read {{ $tPost->title }} blog post"></a>
                <div class="related-article-copy">
                  <span>
                    @if($tPost->category)
                      <a href="{{ route('category.show', $tPost->category->slug) }}">{{ $tPost->category->name }}</a> · 
                    @endif
                    5 min read
                  </span>
                  <h3><a href="{{ route('blog.show', $tPost->slug) }}">{{ $tPost->title }}</a></h3>
                  <p>{{ $tPost->excerpt }}</p>
                  <div class="card-bottom-meta">
                    <span class="view-count" aria-label="{{ $tViewsStr }} views"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5.25c5.15 0 8.6 4.1 9.72 5.65a1.88 1.88 0 0 1 0 2.2c-1.12 1.55-4.57 5.65-9.72 5.65s-8.6-4.1-9.72-5.65a1.88 1.88 0 0 1 0-2.2C3.4 9.35 6.85 5.25 12 5.25Zm0 1.5c-4.35 0-7.35 3.52-8.5 5.03a.38.38 0 0 0 0 .44c1.15 1.51 4.15 5.03 8.5 5.03s7.35-3.52 8.5-5.03a.38.38 0 0 0 0-.44C19.35 10.27 16.35 6.75 12 6.75Zm0 2.25a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 1.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"></path></svg>{{ $tViewsStr }} views</span>
                    <a class="post-author" href="{{ route('author') }}?author={{ Str::slug($tPost->author->name ?? '') }}" aria-label="View blogs by {{ $tPost->author->name ?? '' }}">
                      @if($tPost->author && $tPost->author->image)
                        <img src="{{ asset($tPost->author->image) }}" alt="{{ $tPost->author->name }}">
                      @endif
                      <span>{{ $tPost->author->name ?? '' }}</span>
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