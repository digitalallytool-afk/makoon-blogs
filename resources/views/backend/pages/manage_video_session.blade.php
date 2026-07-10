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
                            <li class="breadcrumb-item active" aria-current="page">Video Sessions</li>
                        </ol>
                    </nav>
                </div>
                @can('create-video-sessions')
                <div class="ms-auto">
                    <a href="{{ route('newVideoSession') }}" class="btn btn-primary radius-30 px-3">
                        <i class="bx bx-plus me-1"></i>Add New Session
                    </a>
                </div>
                @endcan
            </div>
            <!-- End Breadcrumb -->

            <!-- Session Alerts -->
            @if(session('success'))
                <div class="alert alert-success border-0 bg-light-success text-success alert-dismissible fade show font-14 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle fs-4 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Sessions List Card -->
            <div class="card radius-10 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Toolbar: Search, Filter, Export -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <form method="GET" action="{{ route('allVideoSession') }}" class="d-flex flex-wrap gap-2 flex-grow-1">
                            <!-- Search -->
                            <div class="input-group input-group-sm" style="width: 220px;">
                                <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-secondary"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                       placeholder="Search sessions..." value="{{ $search ?? '' }}">
                            </div>

                            <!-- Category Filter -->
                            <select name="session_category_id" class="form-select form-select-sm" style="width:160px;"
                                    onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Status Filter -->
                            <select name="status" class="form-select form-select-sm" style="width:120px;"
                                    onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="published" {{ ($status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft"     {{ ($status ?? '') === 'draft'     ? 'selected' : '' }}>Draft</option>
                            </select>

                            <button type="submit" class="btn btn-sm btn-primary radius-30 px-3">
                                <i class="bx bx-filter-alt me-1"></i>Filter
                            </button>

                            @if($search || $status || $categoryId)
                                <a href="{{ route('allVideoSession') }}" class="btn btn-sm btn-outline-secondary radius-30 px-3">
                                    <i class="bx bx-x me-1"></i>Clear
                                </a>
                            @endif
                        </form>

                    </div>

                    <!-- Counts -->
                    <div class="d-flex align-items-center mb-3 border-bottom pb-2 gap-3 font-13">
                        <span class="font-weight-bold text-secondary">{{ $videoSessions->total() }} total</span>
                        <span class="text-muted">|</span>
                        <span class="text-success">{{ $videoSessions->getCollection()->where('status','published')->count() }} published (this page)</span>
                        <span class="text-muted">|</span>
                        <span class="text-warning">{{ $videoSessions->getCollection()->where('status','draft')->count() }} draft (this page)</span>
                    </div>

                    <!-- Video Sessions Table -->
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="table-light text-secondary font-12 text-uppercase">
                                <tr>
                                    <th>Preview Image</th>
                                    <th>Session Details</th>
                                    <th>Category</th>
                                    <th>Video link</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="font-14">
                                @forelse($videoSessions as $session)
                                    <tr>
                                        <td width="70">
                                            @if($session->image)
                                                <img src="{{ asset($session->image) }}"
                                                     class="rounded border"
                                                     width="60" height="40"
                                                     style="object-fit: cover;"
                                                     alt="{{ $session->title }}">
                                            @else
                                                @php
                                                    // Parse youtube ID to generate fallback thumbnail
                                                    $youtubeId = '';
                                                    if (preg_match('/embed\/([^?]+)/', $session->video_url, $m)) {
                                                        $youtubeId = $m[1];
                                                    }
                                                @endphp
                                                @if($youtubeId)
                                                    <img src="https://img.youtube.com/vi/{{ $youtubeId }}/default.jpg"
                                                         class="rounded border"
                                                         width="60" height="40"
                                                         style="object-fit: cover;"
                                                         alt="{{ $session->title }}">
                                                @else
                                                    <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                                                         style="width:60px; height:40px;">
                                                        <i class="bx bx-video text-secondary fs-5"></i>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <h6 class="mb-1 font-weight-bold text-dark">{{ $session->title }}</h6>
                                            <small class="text-muted d-block">Slug: <code>{{ $session->slug }}</code></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary px-3 py-1 radius-30">
                                                {{ $session->sessionCategory->name ?? 'Uncategorized' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ $session->video_url }}" target="_blank" class="text-primary font-12 d-flex align-items-center gap-1">
                                                <i class="bx bx-play-circle text-danger fs-5"></i>YouTube Player URL
                                            </a>
                                        </td>
                                        <td>
                                            @if($session->status === 'published')
                                                <span class="badge bg-light-success text-success px-3 py-1 radius-30">
                                                    <i class="bx bx-check-circle me-1"></i>Published
                                                </span>
                                            @else
                                                <span class="badge bg-light-warning text-warning px-3 py-1 radius-30">
                                                    <i class="bx bx-pencil me-1"></i>Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark d-block">{{ $session->created_at->format('M d, Y') }}</span>
                                            <small class="text-muted font-11">{{ $session->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex order-actions gap-2 justify-content-end">
                                                @can('view-video-sessions')
                                                    <a href="{{ route('videoSessions.show', $session->id) }}" class="text-primary bg-light-primary border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;" title="View">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                @endcan

                                                @can('edit-video-sessions')
                                                    <a href="{{ route('videoSessions.edit', $session->id) }}" class="text-warning bg-light-warning border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;" title="Edit">
                                                        <i class="bx bxs-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete-video-sessions')
                                                    <form action="{{ route('videoSessions.destroy', $session->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this video session?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-danger bg-light-danger border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;" title="Delete">
                                                            <i class="bx bxs-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-secondary">
                                            <i class="bx bx-video-off fs-1 d-block mb-2"></i>
                                            No video sessions found. <a href="{{ route('newVideoSession') }}" class="text-primary font-weight-bold">Create the first one!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($videoSessions->hasPages())
                        <div class="mt-4">
                            {{ $videoSessions->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
