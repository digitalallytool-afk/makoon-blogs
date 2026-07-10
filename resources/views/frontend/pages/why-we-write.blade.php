@extends('frontend.layout.main')
@section('title', 'Why We Write | Makoons — About Our Preschool Blogs')
@section('meta_description', 'Why Makoons writes blog posts from everyday preschool life for parents and families. A small team of educators and parents writing about what they see in real classrooms.')
@section('meta_keywords', 'about Makoons, why we write, preschool blog, parenting blog, early childhood educators, preschool writers, family blog, school stories')
@section('canonical_url', route('about'))
@section('body_class', 'why-page')

@section('content')
        <main>
            <section class="why-masthead">
                <div class="container-xl why-masthead-inner">
                    <nav class="breadcrumb-row" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>Why
                            we write</span></nav>
                    <span class="eyebrow">Why we write</span>
                    <h1>Written from everyday school life.</h1>
                    <p>These blog posts come from the small questions we hear every week: food, naps, friendships, first
                        tears, messy play, and how children slowly become more independent.</p>
                </div>
            </section>
 
            <section class="why-content-section">
                <div class="container-xl why-content-layout">
                    <aside class="why-side-note">
                        <span>Our promise</span>
                        <p>We write for parents who want calm, practical, human guidance, not pressure.</p>
                    </aside>
 
                    <article class="why-article-copy">
                        <p class="why-lead">Preschool years are full of small questions. Some look simple on the surface:
                            Will my child eat? Why do they cry at drop-off? Why do they repeat the same game? But behind
                            each question is a parent trying to understand a child who is growing every day.</p>
 
                        <h2>We write from what we notice.</h2>
                        <p>Our blog posts begin with real moments from school life: a child standing near the door, a lunchbox
                            coming back half full, two children learning to share a toy, a teacher seeing confidence arrive
                            quietly over time. We turn those moments into clear reading for families.</p>
 
                        <h2>We keep childhood gentle.</h2>
                        <p>The goal is not to make every parent an expert. The goal is to make everyday parenting feel a
                            little less confusing. We try to explain routines, feelings, play, food, and learning in
                            language that feels useful and kind.</p>
 
                        <div class="why-principles">
                            <div><span>01</span><strong>Practical</strong>
                                <p>Every blog post should give parents something simple they can understand or try.</p>
                            </div>
                            <div><span>02</span><strong>Calm</strong>
                                <p>We avoid fear-based advice. Children grow better with trust, rhythm, and patience.</p>
                            </div>
                            <div><span>03</span><strong>Observed</strong>
                                <p>We write from classroom patterns, not from distant theory alone.</p>
                            </div>
                        </div>

                        <h2>We believe small things matter.</h2>
                        <p>A goodbye ritual, a familiar snack, a repeated song, a story before sleep, a teacher remembering
                            a child’s name: these are small things. But for young children, small things often become the
                            structure that helps them feel safe enough to grow.</p>

                        <blockquote>We write because parents deserve clear words for the little moments they are already
                            noticing.</blockquote>
                    </article>
                </div>
            </section>
        </main>
    @endsection
