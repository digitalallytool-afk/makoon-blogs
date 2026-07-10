@extends('backend.layout.main')
@push('styles')
    <style>
        .article-content table {
            width: 100% !important;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
            border-collapse: collapse;
            font-size: 15px;
        }
        .article-content table th,
        .article-content table td {
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .article-content table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .article-content table tr:nth-of-type(even) {
            background-color: rgba(0, 0, 0, 0.02);
        }
    </style>
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Blogs</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('allPost') }}">Blogs</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Blog</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('allPost') }}" class="btn btn-outline-secondary radius-30 px-3">
                        <i class="bx bx-arrow-back me-1"></i>Back to List
                    </a>
                    @can('edit-posts')
                        <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning text-dark radius-30 px-3">
                            <i class="bx bx-edit me-1"></i>Edit Blog
                        </a>
                    @endcan
                </div>
            </div>
            <!-- End Breadcrumb -->

            <div class="row g-4">
                <!-- Left Column: Article Content -->
                <div class="col-12 col-xl-8">
                    <div class="card radius-10 border-0 shadow-sm overflow-hidden mb-4">
                        @if($post->featured_image)
                            <div class="position-relative" style="max-height: 400px; overflow: hidden; background-color: #f8f9fa;">
                                <img src="{{ asset($post->featured_image) }}" class="img-fluid w-100" style="object-fit: cover; max-height: 400px;" alt="{{ $post->title }}">
                                <div class="position-absolute bottom-0 start-0 w-100 p-4 bg-gradient-dark text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.85));">
                                    <span class="badge bg-primary px-3 py-1 radius-30 mb-2">{{ $post->category->name ?? 'Uncategorized' }}</span>
                                    <h2 class="mb-0 font-weight-bold text-white fs-3 shadow-text">{{ $post->title }}</h2>
                                </div>
                            </div>
                        @else
                            <div class="card-body border-bottom p-4">
                                <span class="badge bg-primary px-3 py-1 radius-30 mb-2">{{ $post->category->name ?? 'Uncategorized' }}</span>
                                <h2 class="mb-0 font-weight-bold text-dark fs-2">{{ $post->title }}</h2>
                            </div>
                        @endif

                        <div class="card-body p-4">
                            <!-- Metadata Grid -->
                            <div class="row g-3 align-items-center border-bottom pb-3 mb-4 text-secondary font-13">
                                <div class="col-6 col-md-3">
                                    <span class="d-block font-11 text-uppercase text-muted">Author</span>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        @if($post->author && $post->author->image)
                                            <img src="{{ asset($post->author->image) }}" class="rounded-circle border" width="28" height="28" style="object-fit: cover;" alt="{{ $post->author->name }}">
                                        @endif
                                        <span class="font-weight-bold text-dark">{{ $post->author->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="d-block font-11 text-uppercase text-muted">Published Date</span>
                                    <div class="d-flex align-items-center gap-1 mt-1 text-dark">
                                        <i class="bx bx-calendar fs-5 text-secondary"></i>
                                        <span>{{ $post->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="d-block font-11 text-uppercase text-muted">Status</span>
                                    <div class="mt-1">
                                        @if($post->status === 'published')
                                            <span class="badge bg-light-success text-success px-3 py-1 radius-30">
                                                <i class="bx bx-check-circle me-1"></i>Published
                                            </span>
                                        @else
                                            <span class="badge bg-light-warning text-warning px-3 py-1 radius-30">
                                                <i class="bx bx-pencil me-1"></i>Draft
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="d-block font-11 text-uppercase text-muted">Views</span>
                                    <div class="d-flex align-items-center gap-1 mt-1 text-dark">
                                        <i class="bx bx-show fs-5 text-secondary"></i>
                                        <span class="font-weight-bold">{{ number_format($post->view_count) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            @if($post->excerpt)
                                <div class="p-4 bg-light rounded border-start border-4 border-primary mb-4">
                                    <h6 class="font-weight-bold text-secondary text-uppercase font-12 mb-2">Excerpt / Summary</h6>
                                    <p class="mb-0 text-dark font-15 italic" style="font-style: italic;">"{{ $post->excerpt }}"</p>
                                </div>
                            @endif

                            <!-- Content -->
                            <div class="article-content text-dark font-16 lh-lg">
                                {!! $post->content !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: SEO & URL Metadata -->
                <div class="col-12 col-xl-4 d-flex flex-column gap-4">
                    <!-- SEO Details -->
                    <div class="card radius-10 border-0 shadow-sm mb-0">
                        <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-search-alt-2 text-primary me-2 fs-5"></i>
                                <h6 class="mb-0 font-weight-bold text-dark">SEO Metadata</h6>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Meta Title</span>
                                <p class="mb-0 font-weight-bold text-dark font-14">{{ $post->meta_title ?? '—' }}</p>
                            </div>
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Meta Description</span>
                                <p class="mb-0 text-secondary font-13">{{ $post->meta_description ?? '—' }}</p>
                            </div>
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Meta Keywords</span>
                                <p class="mb-0 text-secondary font-13">
                                    @if($post->meta_keywords)
                                        @foreach(explode(',', $post->meta_keywords) as $keyword)
                                            <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($keyword) }}</span>
                                        @endforeach
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Article Config Details -->
                    <div class="card radius-10 border-0 shadow-sm mb-0">
                        <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-cog text-secondary me-2 fs-5"></i>
                                <h6 class="mb-0 font-weight-bold text-dark">Configuration</h6>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Canonical URL</span>
                                <a href="{{ $post->canonical_url }}" target="_blank" class="text-primary text-break font-12">
                                    {{ $post->canonical_url ?? '—' }}
                                    <i class="bx bx-link-external ms-1"></i>
                                </a>
                            </div>
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Slug</span>
                                <code class="font-12">{{ $post->slug }}</code>
                            </div>
                            <div class="mb-0">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Category Route</span>
                                <span class="badge bg-light-primary text-primary px-3 py-1 radius-30 font-12">
                                    {{ $post->category->name ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
