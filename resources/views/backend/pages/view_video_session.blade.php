@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Sessions</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('allVideoSession') }}">Video Sessions</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Session</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('allVideoSession') }}" class="btn btn-outline-secondary radius-30 px-3">
                        <i class="bx bx-arrow-back me-1"></i>Back to List
                    </a>
                    @can('edit-video-sessions')
                        <a href="{{ route('videoSessions.edit', $videoSession->id) }}" class="btn btn-warning text-dark radius-30 px-3">
                            <i class="bx bx-edit me-1"></i>Edit Session
                        </a>
                    @endcan
                </div>
            </div>
            <!-- End Breadcrumb -->

            <div class="row g-4">
                <!-- Left Column: Video details & Player -->
                <div class="col-12 col-xl-8">
                    <div class="card radius-10 border-0 shadow-sm overflow-hidden mb-4">
                        <!-- Responsive Video Iframe -->
                        <div class="ratio ratio-16x9 bg-black border-bottom">
                            <iframe src="{{ $videoSession->video_url }}" title="{{ $videoSession->title }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen></iframe>
                        </div>

                        <div class="card-body p-4">
                            <!-- Title & Category Badge -->
                            <div class="mb-3">
                                <span class="badge bg-primary px-3 py-1 radius-30 mb-2">{{ $videoSession->sessionCategory->name ?? 'Uncategorized' }}</span>
                                <h3 class="mb-0 font-weight-bold text-dark">{{ $videoSession->title }}</h3>
                            </div>

                            <!-- Metadata Grid -->
                            <div class="row g-3 align-items-center border-bottom pb-3 mb-4 text-secondary font-13">
                                <div class="col-6 col-md-4">
                                    <span class="d-block font-11 text-uppercase text-muted">Uploaded Date</span>
                                    <div class="d-flex align-items-center gap-1 mt-1 text-dark">
                                        <i class="bx bx-calendar fs-5 text-secondary"></i>
                                        <span>{{ $videoSession->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <span class="d-block font-11 text-uppercase text-muted">Status</span>
                                    <div class="mt-1">
                                        @if($videoSession->status === 'published')
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
                            </div>

                            <!-- Description -->
                            @if($videoSession->description)
                                <div class="mb-0">
                                    <h6 class="font-weight-bold text-secondary text-uppercase font-12 mb-2">Description</h6>
                                    <p class="text-dark font-15 lh-lg" style="white-space: pre-line;">{{ $videoSession->description }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                <!-- Right Column: Config -->
                <div class="col-12 col-xl-4 d-flex flex-column gap-4">
                    <!-- Config details -->
                    <div class="card radius-10 border-0 shadow-sm mb-0">
                        <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-cog text-secondary me-2 fs-5"></i>
                                <h6 class="mb-0 font-weight-bold text-dark">Configuration</h6>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">YouTube URL</span>
                                <a href="{{ $videoSession->video_url }}" target="_blank" class="text-primary text-break font-12">
                                    {{ $videoSession->video_url }}
                                    <i class="bx bx-link-external ms-1"></i>
                                </a>
                            </div>
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Slug</span>
                                <code class="font-12">{{ $videoSession->slug }}</code>
                            </div>
                            @if($videoSession->image)
                                <div class="mb-0">
                                    <span class="d-block font-11 text-uppercase text-muted mb-2">Custom Thumbnail</span>
                                    <img src="{{ asset($videoSession->image) }}" class="img-fluid rounded border w-100" style="max-height: 120px; object-fit: cover;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
