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
                            <li class="breadcrumb-item active" aria-current="page">Session Categories</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Breadcrumb -->

            <!-- Session Alert Notifications -->
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

            <div class="row g-4">
                <!-- Left Panel: Create Category Form -->
                <div class="col-12 col-lg-4">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                            <h6 class="mb-0 font-weight-bold text-dark">Add New Category</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('sessionCategories.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="category-name" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Category Name</label>
                                    <input type="text" class="form-control" id="category-name" name="name"
                                           placeholder="e.g. Photoshop Tutorial" required>
                                </div>
                                <div class="mb-4">
                                    <label for="category-desc" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Description</label>
                                    <textarea class="form-control" id="category-desc" name="description" rows="4"
                                              placeholder="Briefly describe the category content..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary radius-30 px-4 w-100">
                                    <i class="bx bx-plus-circle me-1"></i>Create Category
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Category Listing Table -->
                <div class="col-12 col-lg-8">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom pt-3 pb-3 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 font-weight-bold text-dark">Category Directory</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover">
                                    <thead class="table-light text-secondary font-12 text-uppercase">
                                        <tr>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Description</th>
                                            <th class="text-center">Video Sessions</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-14">
                                        @forelse($categories as $category)
                                            <tr>
                                                <td class="font-weight-bold text-dark">{{ $category->name }}</td>
                                                <td><code>{{ $category->slug }}</code></td>
                                                <td class="text-truncate text-secondary" style="max-width: 200px;">
                                                    {{ $category->description ?? '—' }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light-primary text-primary px-3 py-1 radius-30 font-12">
                                                        {{ $category->video_sessions_count }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex order-actions gap-2 justify-content-end">
                                                        <button type="button" class="text-warning bg-light-warning border-0 rounded-circle"
                                                                style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                                data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}"
                                                                title="Edit Category">
                                                            <i class="bx bxs-edit"></i>
                                                        </button>

                                                        <form action="{{ route('sessionCategories.destroy', $category->id) }}" method="POST" class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-danger bg-light-danger border-0 rounded-circle"
                                                                    style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;"
                                                                    title="Delete Category">
                                                                <i class="bx bxs-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 radius-10 shadow">
                                                        <div class="modal-header border-bottom">
                                                            <h5 class="modal-title font-weight-bold text-dark">Edit Session Category</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('sessionCategories.update', $category->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body p-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label font-weight-bold text-secondary font-12 text-uppercase">Category Name</label>
                                                                    <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                                                                </div>
                                                                <div class="mb-0">
                                                                    <label class="form-label font-weight-bold text-secondary font-12 text-uppercase">Description</label>
                                                                    <textarea class="form-control" name="description" rows="4">{{ $category->description }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary radius-30 px-3" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-sm btn-primary radius-30 px-3">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Edit Modal -->

                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-secondary">
                                                    <i class="bx bx-category fs-2 d-block mb-1 text-muted"></i>
                                                    No categories created yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
