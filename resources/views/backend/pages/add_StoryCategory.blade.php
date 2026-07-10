@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Stories</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('allStory') }}">Stories</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Story Categories</li>
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
                <!-- Left Column: Add New Story Category Form -->
                <div class="col-12 col-lg-4">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-1 font-weight-bold text-dark">Add Story Category</h5>
                            <p class="text-secondary font-12 mb-4">Create a new flat category for stories</p>

                            <form action="{{ route('storyCategories.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="cat-name" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Name</label>
                                    <input type="text" class="form-control" id="cat-name" name="name" required placeholder="e.g. Daily Vlog">
                                    <small class="text-muted font-11 d-block mt-1">The name is how it appears on your site.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="cat-desc" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Description</label>
                                    <textarea class="form-control" id="cat-desc" name="description" rows="4" placeholder="Brief details about topics under this story category..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 radius-30 font-weight-bold mt-2">Add Category</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Story Category List Table -->
                <div class="col-12 col-lg-8">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <!-- Search + Export toolbar -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <form method="GET" action="{{ route('storyCategories') }}" class="d-flex gap-2 flex-grow-1">
                                    <div class="input-group input-group-sm" style="max-width:280px;">
                                        <span class="input-group-text bg-light border-end-0"><i class="bx bx-search text-secondary"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                                               placeholder="Search categories..." value="{{ $search ?? '' }}">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary radius-30 px-3"><i class="bx bx-search me-1"></i>Search</button>
                                    @if($search ?? '')
                                        <a href="{{ route('storyCategories') }}" class="btn btn-sm btn-outline-secondary radius-30 px-3"><i class="bx bx-x"></i> Clear</a>
                                    @endif
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover">
                                    <thead class="table-light text-secondary font-12 text-uppercase">
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Slug</th>
                                            <th class="text-end" width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-14 text-dark">
                                        @forelse($categories as $category)
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0 font-weight-bold">{{ $category->name }}</h6>
                                                </td>
                                                <td>
                                                    <span class="text-secondary font-13">
                                                        {{ $category->description ?? 'No description provided.' }}
                                                    </span>
                                                </td>
                                                <td><code>{{ $category->slug }}</code></td>
                                                <td class="text-end">
                                                    <div class="d-flex order-actions gap-2 justify-content-end">
                                                        <button type="button" data-bs-toggle="modal" data-bs-target="#editCategoryModal-{{ $category->id }}" class="text-primary bg-light-primary border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                                                            <i class="bx bxs-edit"></i>
                                                        </button>
                                                        <form action="{{ route('storyCategories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this story category? All associated stories will be deleted as well.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-danger bg-light-danger border-0 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                                                                <i class="bx bxs-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Story Category Modal -->
                                            <div class="modal fade" id="editCategoryModal-{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form action="{{ route('storyCategories.update', $category->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title font-weight-bold text-dark">Edit Story Category</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4 text-start">
                                                                <div class="mb-3">
                                                                    <label for="edit-cat-name-{{ $category->id }}" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Name</label>
                                                                    <input type="text" class="form-control" id="edit-cat-name-{{ $category->id }}" name="name" value="{{ $category->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit-cat-desc-{{ $category->id }}" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Description</label>
                                                                    <textarea class="form-control" id="edit-cat-desc-{{ $category->id }}" name="description" rows="4">{{ $category->description }}</textarea>
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
                                                    No story categories found. Create one to get started!
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($categories->hasPages())
                                <div class="mt-3">
                                    {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
