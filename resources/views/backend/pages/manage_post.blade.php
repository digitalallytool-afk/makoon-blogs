@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Blogs</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Blogs</li>
                        </ol>
                    </nav>
                </div>
                @can('create-posts')
                    <div class="ms-auto">
                        <a href="{{ route('newPost') }}" class="btn btn-primary radius-30 px-3">
                            <i class="bx bx-plus me-1"></i>Add New Blog
                        </a>
                    </div>
                @endcan
            </div>
            <!-- End Breadcrumb -->

            <!-- Session Status Alerts -->
            @if (session('success'))
                <div class="alert alert-success border-0 bg-light-success text-success alert-dismissible fade show font-14 shadow-sm mb-4"
                    role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle fs-4 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 bg-light-danger text-danger alert-dismissible fade show font-14 shadow-sm mb-4"
                    role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle fs-4 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Article List Card -->
            <div class="card radius-10 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Toolbar: Search, Filter, Export -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <form method="GET" action="{{ route('allPost') }}" class="d-flex flex-wrap gap-2 flex-grow-1">
                            <!-- Search -->
                            <div class="input-group input-group-sm" style="width: 220px;">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bx bx-search text-secondary"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                    placeholder="Search blogs..." value="{{ $search ?? '' }}">
                            </div>

                            <!-- Status Filter -->
                            <select name="status" class="form-select form-select-sm" style="width:130px;"
                                onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="published" {{ ($status ?? '') === 'published' ? 'selected' : '' }}>Published
                                </option>
                                <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft
                                </option>
                            </select>

                            <!-- Category Filter -->
                            <select name="category_id" class="form-select form-select-sm" style="width:160px;"
                                onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ ($category ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-sm btn-primary radius-30 px-3">
                                <i class="bx bx-filter-alt me-1"></i>Filter
                            </button>

                            @if ($search || $status || $category)
                                <a href="{{ route('allPost') }}" class="btn btn-sm btn-outline-secondary radius-30 px-3">
                                    <i class="bx bx-x me-1"></i>Clear
                                </a>
                            @endif
                        </form>

                        <!-- Export Button -->
                        <a href="{{ route('posts.export', array_filter(['search' => $search ?? '', 'status' => $status ?? '', 'category_id' => $category ?? ''])) }}"
                            class="btn btn-sm btn-outline-success radius-30 px-3 ms-auto">
                            <i class="bx bx-export me-1"></i>Export Excel
                        </a>
                    </div>

                    <!-- Count strip -->
                    <div class="d-flex align-items-center mb-3 border-bottom pb-2 gap-3 font-13">
                        <span class="font-weight-bold text-secondary">{{ $posts->total() }} total</span>
                        <span class="text-muted">|</span>
                        <span class="text-success">{{ $posts->getCollection()->where('status', 'published')->count() }}
                            published (this page)</span>
                        <span class="text-muted">|</span>
                        <span class="text-warning">{{ $posts->getCollection()->where('status', 'draft')->count() }} draft
                            (this page)</span>
                    </div>

                    <!-- Articles Table -->
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="table-light text-secondary font-12 text-uppercase">
                                <tr>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th class="text-center">Views</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="font-14">
                                @forelse($posts as $post)
                                    <tr>
                                        <td width="60">
                                            @if ($post->featured_image)
                                                <img src="{{ asset($post->featured_image) }}" class="rounded border"
                                                    width="55" height="45" style="object-fit: cover;"
                                                    alt="{{ $post->title }}">
                                            @else
                                                <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                                                    style="width:55px; height:45px;">
                                                    <i class="bx bx-image text-secondary fs-5"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <h6 class="mb-1 font-weight-bold text-dark">{{ $post->title }}</h6>
                                            <small class="text-muted d-block">Slug:
                                                <code>{{ $post->slug }}</code></small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($post->author && $post->author->image)
                                                    <img src="{{ asset($post->author->image) }}"
                                                        class="rounded-circle border" width="28" height="28"
                                                        style="object-fit: cover;" alt="{{ $post->author->name }}">
                                                @endif
                                                <span>{{ $post->author->name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary px-2 py-1 radius-30">
                                                {{ $post->category->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light-secondary text-secondary px-3 py-1 radius-30">
                                                <i class="bx bx-show me-1"></i>{{ number_format($post->view_count) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($post->status === 'published')
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
                                            <span class="text-dark d-block" title="Created on {{ $post->created_at->format('Y-m-d H:i:s') }}">
                                                Created: {{ $post->created_at->format('M d, Y') }}
                                            </span>
                                            @if ($post->updater)
                                                <small class="text-muted d-block font-11">
                                                    Updated: {{ $post->updated_at->diffForHumans() }}
                                                    <span class="text-secondary font-weight-bold">by {{ $post->updater->name }}</span>
                                                </small>
                                            @else
                                                <small class="text-muted font-11">{{ $post->created_at->diffForHumans() }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex order-actions gap-2 justify-content-end">
                                                @can('view-posts')
                                                    <a href="{{ route('posts.show', $post->id) }}"
                                                        class="text-primary bg-light-primary border-0 rounded-circle"
                                                        style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                        title="View">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                @endcan

                                                @can('edit-posts')
                                                    <a href="{{ route('posts.edit', $post->id) }}"
                                                        class="text-warning bg-light-warning border-0 rounded-circle"
                                                        style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                        title="Edit">
                                                        <i class="bx bxs-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete-posts')
                                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this article?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-danger bg-light-danger border-0 rounded-circle"
                                                            style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                            title="Delete">
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
                                            <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                            No blogs found. <a href="{{ route('newPost') }}"
                                                class="text-primary font-weight-bold">Create the first one!</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($posts->hasPages())
                        <div class="mt-4">
                            {{ $posts->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
