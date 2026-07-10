@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Media</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Media Library</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-primary radius-30 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#uploadCollapse" aria-expanded="false" aria-controls="uploadCollapse">
                        <i class="bx bx-upload me-1"></i>Upload New
                    </button>
                </div>
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

            @if(session('error'))
                <div class="alert alert-danger border-0 bg-light-danger text-danger alert-dismissible fade show font-14 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle fs-4 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Upload Area (Collapsed by Default) -->
            <div class="collapse mb-4" id="uploadCollapse">
                <div class="card radius-10 border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <form action="{{ route('media.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex flex-column align-items-center justify-content-center p-5 border border-dashed rounded bg-white" style="border-color: #dee2e6 !important;">
                                <i class="bx bx-cloud-upload text-primary fs-1 mb-3"></i>
                                <h5 class="mb-1 font-weight-bold text-dark">Select files to upload</h5>
                                <p class="text-secondary font-12 mb-3">Choose one or multiple files from your computer</p>
                                <input type="file" name="files[]" id="media-files" class="d-none" multiple required onchange="this.form.submit()">
                                <button type="button" class="btn btn-outline-primary btn-sm radius-30 px-4 font-weight-bold" onclick="document.getElementById('media-files').click()">Select Files from Device</button>
                                <small class="text-muted mt-3">Maximum upload file size: 512 MB. Accepted: images, audio, video, documents.</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Filter & Toolbar -->
            <div class="card radius-10 border-0 shadow-sm mb-4">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="d-flex gap-2 align-items-center py-1 flex-wrap">
                                <select class="form-select form-select-sm w-auto" id="filter-type" onchange="filterMedia()">
                                    <option value="all">All media items</option>
                                    <option value="image">Images</option>
                                    <option value="video">Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="document">Documents</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-auto col-md-3">
                            <div class="input-group input-group-sm">
                                <input type="text" id="search-media" class="form-control" placeholder="Search media..." onkeyup="filterMedia()">
                                <span class="input-group-text bg-light"><i class="bx bx-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Grid -->
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3" id="media-grid">
                @forelse($mediaFiles as $media)
                    <div class="col media-item-col" data-name="{{ strtolower($media['name']) }}" data-type="{{ $media['type'] }}">
                        <div class="card radius-10 border-0 shadow-sm mb-0 h-100 position-relative media-item-card overflow-hidden">
                            <div class="ratio ratio-1x1 bg-light border-bottom overflow-hidden position-relative">
                                @if($media['type'] === 'image')
                                    <img src="{{ $media['url'] }}" class="object-fit-cover hover-scale transition-all w-100 h-100" alt="{{ $media['name'] }}">
                                @elseif($media['type'] === 'video')
                                    <div class="d-flex flex-column align-items-center justify-content-center text-danger position-absolute top-0 start-0 w-100 h-100">
                                        <i class="bx bx-video text-danger" style="font-size: 44px;"></i>
                                        <small class="font-9 text-uppercase mt-1 text-secondary font-weight-bold">Video</small>
                                    </div>
                                @elseif($media['type'] === 'audio')
                                    <div class="d-flex flex-column align-items-center justify-content-center text-success position-absolute top-0 start-0 w-100 h-100">
                                        <i class="bx bx-headphone text-success" style="font-size: 44px;"></i>
                                        <small class="font-9 text-uppercase mt-1 text-secondary font-weight-bold">Audio</small>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center text-secondary position-absolute top-0 start-0 w-100 h-100">
                                        <i class="bx bx-file text-secondary" style="font-size: 44px;"></i>
                                        <small class="font-9 text-uppercase mt-1 text-secondary font-weight-bold">Document</small>
                                    </div>
                                @endif

                                <!-- Actions Hover Overlay -->
                                <div class="media-actions-overlay d-flex align-items-center justify-content-center gap-2 position-absolute top-0 start-0 w-100 h-100 opacity-0 transition-all" style="background: rgba(0,0,0,0.45); z-index: 5;">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center hover-scale-sm" style="width: 34px; height: 34px; padding: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.15);" onclick="navigator.clipboard.writeText('{{ $media['url'] }}'); alert('Link copied!')" title="Copy URL">
                                        <i class="bx bx-copy text-dark font-14"></i>
                                    </button>
                                    
                                    <form action="{{ route('media.delete') }}" method="POST" class="m-0" onsubmit="return confirm('Delete this file permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="path" value="{{ $media['path'] }}">
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center hover-scale-sm" style="width: 34px; height: 34px; padding: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.15);" title="Delete File">
                                            <i class="bx bx-trash text-white font-14"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="text-truncate mb-0 font-12 font-weight-bold text-dark" title="{{ $media['name'] }}">{{ $media['name'] }}</h6>
                                <small class="text-muted font-10 d-block">{{ strtoupper(pathinfo($media['name'], PATHINFO_EXTENSION)) }} - {{ number_format($media['size'] / 1024, 1) }} KB</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-secondary">
                        <i class="bx bx-image-alt fs-1 mb-2"></i>
                        <p class="mb-0">No media files found in library.</p>
                    </div>
                @endforelse
            </div>

            <!-- Total Strip & Pagination -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-4">
                <div class="text-secondary font-13">Showing <strong id="visible-count">{{ $mediaFiles->count() }}</strong> of <strong>{{ $mediaFiles->total() }}</strong> items in Media Library</div>
                <div class="media-pagination">
                    {{ $mediaFiles->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Style overrides for custom effects -->
    <style>
        .hover-scale {
            transition: transform 0.25s ease;
        }
        .media-item-card:hover .hover-scale {
            transform: scale(1.06);
        }
        .media-actions-overlay {
            transition: opacity 0.2s ease-in-out;
        }
        .media-item-card:hover .media-actions-overlay {
            opacity: 1 !important;
        }
        .hover-scale-sm {
            transition: transform 0.15s ease-in-out;
        }
        .hover-scale-sm:hover {
            transform: scale(1.1) !important;
        }
    </style>

    @push('scripts')
        <script>
            function filterMedia() {
                const searchVal = document.getElementById('search-media').value.toLowerCase();
                const typeVal = document.getElementById('filter-type').value;
                const cols = document.querySelectorAll('.media-item-col');
                let count = 0;

                cols.forEach(col => {
                    const name = col.getAttribute('data-name');
                    const type = col.getAttribute('data-type');
                    
                    const matchesSearch = name.includes(searchVal);
                    const matchesType = (typeVal === 'all' || type === typeVal);

                    if (matchesSearch && matchesType) {
                        col.classList.remove('d-none');
                        count++;
                    } else {
                        col.classList.add('d-none');
                    }
                });

                document.getElementById('visible-count').textContent = count;
            }
        </script>
    @endpush
@endsection
