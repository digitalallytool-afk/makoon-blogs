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
                            <li class="breadcrumb-item active" aria-current="page">Add New Session</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Breadcrumb -->

            <!-- Validation alerts -->
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

            <form action="{{ route('videoSessions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="status" id="video-status" value="published">

                <div class="row g-4">
                    <!-- ===================== Left Column ===================== -->
                    <div class="col-12 col-xl-8">

                        <!-- Main content card -->
                        <div class="card radius-10 border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label for="session-title" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Session Title</label>
                                    <input type="text" class="form-control form-control-lg border-0 bg-light-subtle shadow-none font-20"
                                           id="session-title" name="title" value="{{ old('title') }}"
                                           placeholder="Enter session title..." required
                                           style="font-weight: 600; padding: 12px 15px;">
                                </div>

                                <div class="mb-4">
                                    <label for="session_category_id" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Session Category</label>
                                    <select class="form-select border-0 bg-light" id="session_category_id" name="session_category_id" required style="padding: 10px 15px;">
                                        <option value="" disabled selected>Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('session_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label for="session-desc" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Description</label>
                                    <textarea class="form-control border-0 bg-light" id="session-desc" name="description" rows="5"
                                              placeholder="Provide details about this video session...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Video URL and Live Preview Card -->
                        <div class="card radius-10 border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-video text-danger me-2 fs-5"></i>
                                    <h6 class="mb-0 font-weight-bold text-dark">YouTube Video Link</h6>
                                </div>
                                <small class="text-muted font-11 d-block mt-1">Paste any standard YouTube url (e.g. watch link, sharing link, or embed link).</small>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label for="video_url" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Video URL</label>
                                    <input type="text" class="form-control border-0 bg-light" id="video_url" name="video_url"
                                           value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=..." required
                                           style="padding: 10px 15px;">
                                </div>

                                <!-- Dynamic Preview Container -->
                                <div class="d-none" id="youtube-preview-container">
                                    <label class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Live Player Preview</label>
                                    <div class="ratio ratio-16x9 rounded border overflow-hidden shadow-sm bg-black">
                                        <iframe src="" id="youtube-iframe" title="YouTube video player" frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen></iframe>
                                    </div>
                                </div>

                                <div class="alert alert-danger border-0 bg-light-danger text-danger d-none font-13" id="youtube-error">
                                    <i class="bx bx-x-circle me-1"></i>Please enter a valid YouTube link.
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- ===================== End Left Column ===================== -->

                    <!-- ===================== Right Column ===================== -->
                    <div class="col-12 col-xl-4 d-flex flex-column gap-4">

                        <!-- Publish Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Publish</h6>
                            </div>
                            <div class="card-body pt-1">
                                <!-- Status indicator -->
                                <div class="d-flex align-items-center gap-2 mb-3 py-2 px-3 bg-light rounded font-13">
                                    <i class="bx bx-info-circle text-secondary fs-5"></i>
                                    <span class="text-secondary">Status: </span>
                                    <span class="font-weight-bold text-dark" id="status-label">Published</span>
                                </div>
                            </div>
                            <div class="card-footer bg-light-subtle p-3 border-top-0">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('allVideoSession') }}" class="btn btn-outline-secondary btn-sm radius-30 px-3">
                                        Cancel
                                    </a>
                                    <!-- Save as Draft -->
                                    <button type="submit" class="btn btn-outline-warning btn-sm radius-30 px-3"
                                            onclick="setStatus('draft')" id="btn-draft">
                                        <i class="bx bx-save me-1"></i>Save Draft
                                    </button>
                                    <!-- Publish -->
                                    <button type="submit" class="btn btn-primary btn-sm radius-30 px-3"
                                            onclick="setStatus('published')" id="btn-publish">
                                        <i class="bx bx-paper-plane me-1"></i>Publish
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Thumbnail Image Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Featured Image</h6>
                            </div>
                            <div class="card-body pt-1">
                                <input type="hidden" name="featured_image_url" id="featured_image_url">
                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm radius-30 px-3 font-weight-bold w-100" onclick="selectFeaturedImage()">
                                        <i class="bx bx-image me-1"></i>Choose from Media Library
                                    </button>
                                    <div class="text-center font-11 text-secondary">— or upload new —</div>
                                    <input type="file" class="form-control" id="session-featured-image" name="featured_image" accept="image/*" onchange="clearSelectedFeaturedImage()">
                                </div>
                                <div class="mt-3 d-none" id="featured-image-preview-container">
                                    <img src="" id="featured-image-preview" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                                    <button type="button" class="btn btn-outline-danger btn-sm radius-30 px-2 mt-2 w-100" onclick="removeFeaturedImage()">Remove Selection</button>
                                </div>
                                <small class="text-muted d-block mt-2 font-11">Max 20 MB. Accepted: JPEG, PNG, GIF, WEBP.</small>
                            </div>
                        </div>

                    </div>
                    <!-- ===================== End Right Column ===================== -->

                </div>
            </form>
        </div>
    </div>

    @include('backend.components.media_modal')

    @push('scripts')
        <script>
            // Featured Image selection from Media Library
            function selectFeaturedImage() {
                openMediaLibraryModal(function(url, path) {
                    document.getElementById('featured_image_url').value = path;
                    document.getElementById('featured-image-preview').src = url;
                    document.getElementById('featured-image-preview-container').classList.remove('d-none');
                    document.getElementById('session-featured-image').value = '';
                });
            }

            function clearSelectedFeaturedImage() {
                document.getElementById('featured_image_url').value = '';
                document.getElementById('featured-image-preview-container').classList.add('d-none');
            }

            function removeFeaturedImage() {
                document.getElementById('featured_image_url').value = '';
                document.getElementById('featured-image-preview').src = '';
                document.getElementById('featured-image-preview-container').classList.add('d-none');
            }

            // Set hidden status field and update the label
            function setStatus(value) {
                document.getElementById('video-status').value = value;
                document.getElementById('status-label').textContent =
                    value === 'draft' ? 'Draft' : 'Published';
            }

            // YouTube URL regex parsing and dynamic live preview
            const videoUrlInput = document.getElementById('video_url');
            const previewContainer = document.getElementById('youtube-preview-container');
            const iframe = document.getElementById('youtube-iframe');
            const errorAlert = document.getElementById('youtube-error');

            function getYoutubeId(url) {
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }

            function updatePreview() {
                const url = videoUrlInput.value.trim();
                if (!url) {
                    previewContainer.classList.add('d-none');
                    errorAlert.classList.add('d-none');
                    return;
                }

                const videoId = getYoutubeId(url);
                if (videoId) {
                    iframe.src = `https://www.youtube.com/embed/${videoId}`;
                    previewContainer.classList.remove('d-none');
                    errorAlert.classList.add('d-none');
                } else {
                    previewContainer.classList.add('d-none');
                    errorAlert.classList.remove('d-none');
                }
            }

            // Bind update preview on keyup / paste / change
            videoUrlInput.addEventListener('keyup', updatePreview);
            videoUrlInput.addEventListener('paste', function() {
                setTimeout(updatePreview, 100);
            });
            videoUrlInput.addEventListener('change', updatePreview);

            // Trigger preview on initial load if validation redirected with old input
            if (videoUrlInput.value) {
                updatePreview();
            }
        </script>
    @endpush
@endsection
