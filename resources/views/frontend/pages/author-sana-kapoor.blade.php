@extends('frontend.layout.main')
@section('title', 'Sana Kapoor | Author at Makoons Blogs')
@section('meta_description', 'Sana Kapoor is a preschool educator and writer at Makoons. Read all her blog posts on parenting, early childhood, school routines, and child development.')
@section('meta_keywords', 'Sana Kapoor, Makoons author, preschool educator writer, parenting blogs author, early childhood writer, preschool blog author')
@section('canonical_url', route('author.sana'))
@section('body_class', 'author-page')

@section('content')
    <main>
      <section class="author-masthead">
        <div class="container-xl author-masthead-inner">
          <nav class="breadcrumb-row" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('blogs') }}">Authors</a><span>/</span><span>Sana Kapoor</span></nav>
          <div class="author-profile-card">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=260&q=86" alt="Sana Kapoor">
            <div>
              <span class="eyebrow">Author</span>
              <h1>Sana Kapoor</h1>
              <p>Sana writes about food, comfort routines, and the small practical choices that help preschool children settle into school life with more ease.</p>
            </div>
          </div>
          <div class="author-stats" aria-label="Author statistics">
            <div><strong>3</strong><span>Blogs</span></div>
            <div><strong>4.1k</strong><span>Total views</span></div>
            <div><strong>Food</strong><span>Main topic</span></div>
          </div>
        </div>
      </section>

      <section class="author-posts-section">
        <div class="container-xl author-posts-inner">
          <div class="author-posts-head">
            <span class="eyebrow">All posts by Sana</span>
            <h2>Practical food and comfort reads for parents</h2>
            <p>These posts focus on familiar foods, lunchbox confidence, and simple routines that make the first school weeks feel easier for children.</p>
          </div>

          <div class="author-post-list">
            <article class="author-post-card">
              <a class="card-media media-one" href="#" aria-label="Read Simple lunchbox ideas for the first month of school"></a>
              <div>
                <span>Food · 3 min read</span>
                <h3><a href="#">Simple lunchbox ideas for the first month of school</a></h3>
                <p>Familiar, easy-to-eat foods that support comfort while children settle into a new space.</p>
                <small>June 4, 2026 · 1.5k views</small>
              </div>
            </article>
            <article class="author-post-card">
              <a class="card-media media-two" href="#" aria-label="Read Familiar food and calmer preschool mornings"></a>
              <div>
                <span>Food · 4 min read</span>
                <h3><a href="#">Familiar food and calmer preschool mornings</a></h3>
                <p>Why predictable breakfast and snack choices can support smoother drop-offs.</p>
                <small>May 20, 2026 · 1.4k views</small>
              </div>
            </article>
            <article class="author-post-card">
              <a class="card-media media-three" href="#" aria-label="Read What to pack when your child is still settling"></a>
              <div>
                <span>Daycare · 4 min read</span>
                <h3><a href="#">What to pack when your child is still settling</a></h3>
                <p>A soft checklist for comfort objects, food, water, and simple school-day preparedness.</p>
                <small>April 18, 2026 · 1.2k views</small>
              </div>
            </article>
          </div>
        </div>
      </section>
    </main>

@endsection
