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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('allPost') }}">Blogs</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Authors</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Breadcrumb -->

            <!-- Session Status Alerts -->
            @if(session('success'))
                <div class="alert alert-success border-0 bg-light-success text-success alert-dismissible fade show font-14 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle fs-4 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 bg-light-danger text-danger alert-dismissible fade show font-14 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle fs-4 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0 bg-light-danger text-danger alert-dismissible fade show font-14 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle fs-4 me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Left Column: Add New Author Form -->
                <div class="col-12 col-lg-4">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-1 font-weight-bold text-dark">Add New Author</h5>
                            <p class="text-secondary font-12 mb-4">Create a new author profile</p>

                            <form action="{{ route('authors.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="author-name" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Full Name</label>
                                    <input type="text" class="form-control" id="author-name" name="name" required placeholder="e.g. Rajan Sharma">
                                </div>

                                <div class="mb-3">
                                    <label for="author-image" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Profile Image</label>
                                    <input type="file" class="form-control" id="author-image" name="image" accept="image/*">
                                    <small class="text-muted font-11 d-block mt-1">Recommended square image, max 2MB.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="author-desc" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Biography / Description</label>
                                    <textarea class="form-control" id="author-desc" name="description" rows="4" placeholder="Brief details about the author..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 radius-30 font-weight-bold mt-2">Add Author</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Authors List Table -->
                <div class="col-12 col-lg-8">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <!-- Search + Export toolbar -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <form method="GET" action="{{ route('authors.index') }}" class="d-flex gap-2 flex-grow-1">
                                    <div class="input-group input-group-sm" style="max-width:280px;">
                                        <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-secondary"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                                               placeholder="Search authors..." value="{{ $search ?? '' }}">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary radius-30 px-3">
                                        <i class="bx bx-search me-1"></i>Search
                                    </button>
                                    @if($search ?? '')
                                        <a href="{{ route('authors.index') }}" class="btn btn-sm btn-outline-secondary radius-30 px-3">
                                            <i class="bx bx-x"></i> Clear
                                        </a>
                                    @endif
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover">
                                    <thead class="table-light text-secondary font-12 text-uppercase">
                                        <tr>
                                            <th width="80">Avatar</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="text-end" width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-14 text-dark">
                                        @forelse($authors as $author)
                                            <tr>
                                                <td>
                                                    @if($author->image)
                                                        <img src="{{ asset($author->image) }}" class="rounded-circle border" width="45" height="45" style="object-fit: cover;" alt="{{ $author->name }}">
                                                    @else
                                                        <img src="https://placehold.co/110x110?text={{ urlencode(substr($author->name, 0, 1)) }}" class="rounded-circle border" width="45" height="45" alt="{{ $author->name }}">
                                                    @endif
                                                </td>
                                                <td>
                                                    <h6 class="mb-0 font-weight-bold">{{ $author->name }}</h6>
                                                    <small class="text-muted">ID: #{{ $author->id }}</small>
                                                </td>
                                                <td>
                                                    <span class="text-secondary font-13">
                                                        {{ $author->description ?? 'No biography provided.' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex order-actions gap-2 justify-content-end">
                                                        <button type="button" data-bs-toggle="modal" data-bs-target="#editAuthorModal-{{ $author->id }}" class="text-primary bg-light-primary border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                                                            <i class="bx bxs-edit"></i>
                                                        </button>
                                                        <form action="{{ route('authors.destroy', $author->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this author profile?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-danger bg-light-danger border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                                                                <i class="bx bxs-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Author Modal -->
                                            <div class="modal fade" id="editAuthorModal-{{ $author->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content text-start">
                                                        <form action="{{ route('authors.update', $author->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title font-weight-bold text-dark">Edit Author</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="mb-3">
                                                                    <label for="edit-author-name-{{ $author->id }}" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Full Name</label>
                                                                    <input type="text" class="form-control" id="edit-author-name-{{ $author->id }}" name="name" value="{{ $author->name }}" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="edit-author-image-{{ $author->id }}" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Profile Image</label>
                                                                    @if($author->image)
                                                                        <div class="mb-2">
                                                                            <img src="{{ asset($author->image) }}" class="rounded-circle border" width="60" height="60" style="object-fit: cover;" alt="{{ $author->name }}">
                                                                        </div>
                                                                    @endif
                                                                    <input type="file" class="form-control" id="edit-author-image-{{ $author->id }}" name="image" accept="image/*">
                                                                    <small class="text-muted font-11 mt-1 d-block">Leave blank to keep the current image.</small>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="edit-author-desc-{{ $author->id }}" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Biography / Description</label>
                                                                    <textarea class="form-control" id="edit-author-desc-{{ $author->id }}" name="description" rows="4">{{ $author->description }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light-subtle">
                                                                <button type="button" class="btn btn-secondary btn-sm radius-30 px-3" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary btn-sm radius-30 px-3">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-secondary">
                                                    No authors found. Create one to get started!
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($authors->hasPages())
                                <div class="mt-3">
                                    {{ $authors->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
