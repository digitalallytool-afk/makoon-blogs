@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Printables</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('allPrintable') }}">Printables</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Printable</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('allPrintable') }}" class="btn btn-outline-secondary radius-30 px-3">
                        <i class="bx bx-arrow-back me-1"></i>Back to List
                    </a>
                    @can('edit-printables')
                        <a href="{{ route('printables.edit', $printable->id) }}" class="btn btn-warning text-dark radius-30 px-3">
                            <i class="bx bx-edit me-1"></i>Edit Printable
                        </a>
                    @endcan
                </div>
            </div>
            <!-- End Breadcrumb -->

            <div class="row g-4">
                <!-- Left Column: Printable details -->
                <div class="col-12 col-xl-8">
                    <div class="card radius-10 border-0 shadow-sm overflow-hidden mb-4">
                        @if($printable->image)
                            <div class="position-relative" style="max-height: 350px; overflow: hidden; background-color: #f8f9fa;">
                                <img src="{{ asset($printable->image) }}" class="img-fluid w-100" style="object-fit: cover; max-height: 350px;" alt="{{ $printable->name }}">
                                <div class="position-absolute bottom-0 start-0 w-100 p-4 bg-gradient-dark text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.85));">
                                    <h2 class="mb-0 font-weight-bold text-white fs-3 shadow-text">{{ $printable->name }}</h2>
                                </div>
                            </div>
                        @else
                            <div class="card-body border-bottom p-4">
                                <h2 class="mb-0 font-weight-bold text-dark fs-2">{{ $printable->name }}</h2>
                            </div>
                        @endif

                        <div class="card-body p-4">
                            <!-- Metadata Grid -->
                            <div class="row g-3 align-items-center border-bottom pb-3 mb-4 text-secondary font-13">
                                <div class="col-6 col-md-4">
                                    <span class="d-block font-11 text-uppercase text-muted">Uploaded Date</span>
                                    <div class="d-flex align-items-center gap-1 mt-1 text-dark">
                                        <i class="bx bx-calendar fs-5 text-secondary"></i>
                                        <span>{{ $printable->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <span class="d-block font-11 text-uppercase text-muted">Status</span>
                                    <div class="mt-1">
                                        @if($printable->status === 'published')
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
                                <div class="col-6 col-md-4">
                                    <span class="d-block font-11 text-uppercase text-muted">Downloads Count</span>
                                    <div class="d-flex align-items-center gap-1 mt-1 text-dark">
                                        <i class="bx bx-download fs-5 text-secondary"></i>
                                        <span class="font-weight-bold">{{ number_format($printable->download_count) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($printable->description)
                                <div class="mb-4">
                                    <h6 class="font-weight-bold text-secondary text-uppercase font-12 mb-2">Description</h6>
                                    <p class="text-dark font-15 lh-lg" style="white-space: pre-line;">{{ $printable->description }}</p>
                                </div>
                            @endif

                            <!-- Printable File card -->
                            <div class="p-4 bg-light rounded border mb-0">
                                <h6 class="font-weight-bold text-secondary text-uppercase font-12 mb-3">Download Attachment</h6>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bx bxs-file-pdf text-danger fs-1"></i>
                                        <div>
                                            <h6 class="mb-1 font-weight-bold text-dark">{{ $printable->file_name }}</h6>
                                            <small class="text-secondary font-12">File Size: {{ number_format($printable->file_size / (1024 * 1024), 2) }} MB</small>
                                        </div>
                                    </div>
                                    <a href="{{ asset($printable->file_path) }}" download class="btn btn-success radius-30 px-4 font-weight-bold">
                                        <i class="bx bx-download me-1"></i>Download File
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings & Config -->
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
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Storage Path</span>
                                <code class="font-12 text-break">{{ $printable->file_path }}</code>
                            </div>
                            <div class="mb-3">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">Slug</span>
                                <code class="font-12">{{ $printable->slug }}</code>
                            </div>
                            <div class="mb-0">
                                <span class="d-block font-11 text-uppercase text-muted mb-1">File Extension</span>
                                <span class="badge bg-light-primary text-primary px-3 py-1 radius-30 font-12 uppercase text-uppercase">
                                    {{ pathinfo($printable->file_name, PATHINFO_EXTENSION) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
