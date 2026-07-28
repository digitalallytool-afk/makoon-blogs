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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('allStory') }}">Stories</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add New Story</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Breadcrumb -->

            <!-- Session Alerts -->
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

            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-light-danger text-danger alert-dismissible fade show font-14 shadow-sm mb-4"
                    role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle fs-4 me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" id="story-form">
                @csrf
                {{-- Hidden status field controlled by the publish/draft buttons --}}
                <input type="hidden" name="status" id="story-status" value="published">

                <div class="row g-4">
                    <!-- ===================== Left Column ===================== -->
                    <div class="col-12 col-xl-8">

                        <!-- Title & Content card -->
                        <div class="card radius-10 border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label for="story-title"
                                        class="form-label font-weight-bold text-secondary font-12 text-uppercase">Story
                                        Title</label>
                                    <input type="text"
                                        class="form-control form-control-lg border-0 bg-light-subtle shadow-none font-20"
                                        id="story-title" name="title" value="{{ old('title') }}"
                                        placeholder="Enter story title..." required
                                        style="font-weight: 600; padding: 12px 15px;">
                                </div>

                                <div class="mb-4">
                                    <label for="story-slug"
                                        class="form-label font-weight-bold text-secondary font-12 text-uppercase">URL
                                        Slug</label>
                                    <input type="text" class="form-control border-0 bg-light-subtle shadow-none font-14"
                                        id="story-slug" name="slug" value="{{ old('slug') }}"
                                        placeholder="Optional: Enter custom URL slug (e.g. custom-slug)..."
                                        style="padding: 10px 15px;">
                                    <small class="text-muted mt-1 d-block font-11">Leave blank to automatically generate
                                        from the title.</small>
                                </div>

                                <!-- Editor -->
                                <div class="mb-4">
                                    <label for="story-content"
                                        class="form-label font-weight-bold text-secondary font-12 text-uppercase">Story
                                        Content</label>
                                    <textarea class="form-control" id="story-content" name="content" required>{{ old('content') }}</textarea>
                                </div>

                                <!-- Excerpt -->
                                <div class="mb-0">
                                    <label for="story-excerpt"
                                        class="form-label font-weight-bold text-secondary font-12 text-uppercase">Excerpt</label>
                                    <textarea class="form-control border-0 bg-light" id="story-excerpt" name="excerpt" rows="3"
                                        placeholder="Write a brief story summary...">{{ old('excerpt') }}</textarea>
                                    <small class="text-muted mt-1 d-block font-11">Optional summary shown in listings and
                                        previews.</small>
                                </div>
                            </div>
                        </div>

                        <!-- =========== SEO Card =========== -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-search-alt-2 text-primary me-2 fs-5"></i>
                                    <h6 class="mb-0 font-weight-bold text-dark">SEO Settings</h6>
                                    <span class="badge bg-light-primary text-primary ms-2 font-11">Optional</span>
                                </div>
                                <small class="text-muted font-11 d-block mt-1">Improve search engine visibility. Leave blank
                                    to use story title &amp; excerpt.</small>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <!-- Meta Title -->
                                    <div class="col-12">
                                        <label for="seo-meta-title"
                                            class="form-label font-weight-bold text-secondary font-12 text-uppercase">
                                            Meta Title
                                        </label>
                                        <input type="text" class="form-control border-0 bg-light" id="seo-meta-title"
                                            name="meta_title" value="{{ old('meta_title') }}"
                                            placeholder="SEO page title (recommended: 50–60 chars)" maxlength="255">
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted font-11">Shown in browser tabs and search
                                                results.</small>
                                            <small class="text-muted font-11" id="meta-title-count">0 / 60</small>
                                        </div>
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="col-12">
                                        <label for="seo-meta-desc"
                                            class="form-label font-weight-bold text-secondary font-12 text-uppercase">
                                            Meta Description
                                        </label>
                                        <textarea class="form-control border-0 bg-light" id="seo-meta-desc" name="meta_description" rows="3"
                                            placeholder="Brief description for search results (recommended: 150–160 chars)" maxlength="500">{{ old('meta_description') }}</textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted font-11">Shown below the title in Google search
                                                results.</small>
                                            <small class="text-muted font-11" id="meta-desc-count">0 / 160</small>
                                        </div>
                                    </div>

                                    <!-- Meta Keywords -->
                                    <div class="col-12">
                                        <label for="seo-meta-keywords"
                                            class="form-label font-weight-bold text-secondary font-12 text-uppercase">
                                            Meta Keywords
                                        </label>
                                        <input type="text" class="form-control border-0 bg-light"
                                            id="seo-meta-keywords" name="meta_keywords"
                                            value="{{ old('meta_keywords') }}"
                                            placeholder="story, vlog, personal narrative">
                                        <small class="text-muted d-block mt-1 font-11">Comma-separated keywords.</small>
                                    </div>

                                    <!-- Sitemap/Robots note -->
                                    <div class="col-12">
                                        <div class="d-flex gap-3 flex-wrap">
                                            <div class="d-flex align-items-center gap-2 text-success font-12">
                                                <i class="bx bx-check-circle fs-5"></i>
                                                <span>Auto-added to <strong>sitemap.xml</strong> when published</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 text-success font-12">
                                                <i class="bx bx-check-circle fs-5"></i>
                                                <span><strong>robots.txt</strong> auto-updated with sitemap link</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 text-warning font-12">
                                                <i class="bx bx-info-circle fs-5"></i>
                                                <span>Draft stories are <strong>excluded</strong> from sitemap</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- ===================== End Left Column ===================== -->

                    <!-- ===================== Right Column ===================== -->
                    <div class="col-12 col-xl-4 d-flex flex-column gap-4">

                        <!-- SEO Analyzer Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0" id="seo-analyzer-card">
                            <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 font-weight-bold text-dark">SEO Analyzer</h6>
                                    <span class="badge font-11" id="seo-score-badge"
                                        style="background:#e9ecef;color:#666;">— / 100</span>
                                </div>
                            </div>
                            <div class="card-body pt-2">
                                <!-- Score Gauge -->
                                <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded" style="background:#f8f9fa;">
                                    <div style="position:relative;width:52px;height:52px;flex-shrink:0;">
                                        <svg viewBox="0 0 36 36" style="width:52px;height:52px;transform:rotate(-90deg);">
                                            <circle cx="18" cy="18" r="15.9" fill="none"
                                                stroke="#e9ecef" stroke-width="3" />
                                            <circle cx="18" cy="18" r="15.9" fill="none"
                                                stroke="#198754" stroke-width="3" stroke-dasharray="0 100"
                                                stroke-linecap="round" id="seo-gauge-circle"
                                                style="transition:stroke-dasharray .5s ease,stroke .3s;" />
                                        </svg>
                                        <span id="seo-score-num"
                                            style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#333;">0</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold font-13" id="seo-score-label">Start filling fields
                                        </div>
                                        <div class="text-muted font-11" id="seo-score-sub">Add title, meta &amp; keywords
                                            to see your SEO score</div>
                                    </div>
                                </div>

                                <!-- Google SERP Preview -->
                                <div class="mb-3">
                                    <div class="font-weight-bold text-secondary font-11 text-uppercase mb-1">Google Preview
                                    </div>
                                    <div
                                        style="border:1px solid #e0e0e0;border-radius:8px;padding:10px 12px;background:#fff;">
                                        <div id="serp-url"
                                            style="font-size:11px;color:#1a7f37;margin-bottom:2px;word-break:break-all;">
                                            makoons.com › stories › ...</div>
                                        <div id="serp-title"
                                            style="font-size:14px;color:#1a0dab;font-weight:600;line-height:1.3;margin-bottom:3px;">
                                            Your story title will appear here</div>
                                        <div id="serp-desc" style="font-size:12px;color:#4d5156;line-height:1.5;">Your
                                            meta description will appear here. Make it compelling to get more clicks from
                                            search results.</div>
                                    </div>
                                </div>

                                <!-- Checks List -->
                                <div class="font-weight-bold text-secondary font-11 text-uppercase mb-2">SEO Checks</div>
                                <div id="seo-checks-list" style="display:flex;flex-direction:column;gap:5px;"></div>
                            </div>
                        </div>

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
                                <!-- View Count -->
                                <div class="mb-0">
                                    <label class="form-label font-weight-bold text-dark font-13"
                                        for="story-view-count">View Count</label>
                                    <input type="number"
                                        class="form-control font-13 @error('view_count') is-invalid @enderror"
                                        id="story-view-count" name="view_count" value="{{ old('view_count', 0) }}"
                                        min="0">
                                    @error('view_count')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted d-block font-11 mt-1">Specify initial view count for this
                                        story.</small>
                                </div>
                            </div>
                            <div class="card-footer bg-light-subtle p-3 border-top-0">
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <a href="{{ route('allStory') }}" class="btn btn-outline-secondary px-2 py-1"
                                        style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        Cancel
                                    </a>
                                    <!-- Save as Draft -->
                                    <button type="submit" class="btn btn-outline-warning px-2 py-1"
                                        onclick="setStatus('draft')" id="btn-draft"
                                        style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        <i class="bx bx-save me-1"></i>Draft
                                    </button>
                                    <!-- Preview -->
                                    <button type="button" class="btn btn-outline-info px-2 py-1" id="btn-preview"
                                        style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        <i class="bx bx-show me-1"></i>Preview
                                    </button>
                                    <!-- Publish -->
                                    <button type="submit" class="btn btn-primary px-2 py-1"
                                        onclick="setStatus('published')" id="btn-publish"
                                        style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        <i class="bx bx-paper-plane me-1"></i>Publish
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Category Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Story Category</h6>
                            </div>
                            <div class="card-body pt-1">
                                <label for="story-category"
                                    class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Select
                                    Category</label>
                                <select class="form-select" id="story-category" name="story_category_id" required>
                                    <option value="" disabled selected>-- Choose Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('story_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2 font-11">Classify this story under a
                                    category.</small>
                            </div>
                        </div>

                        <!-- Author Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Author</h6>
                            </div>
                            <div class="card-body pt-1">
                                <label for="story-author"
                                    class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Assign
                                    Author</label>
                                <select class="form-select" id="story-author" name="author_id" required>
                                    <option value="" disabled selected>-- Choose Author --</option>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            {{ old('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2 font-11">Assign an author profile to this
                                    story.</small>
                            </div>
                        </div>

                        <!-- Featured Image Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Featured Image</h6>
                            </div>
                            <div class="card-body pt-1">
                                <input type="hidden" name="featured_image_url" id="featured_image_url">
                                <div class="d-flex flex-column gap-2">
                                    <button type="button"
                                        class="btn btn-outline-primary btn-sm radius-30 px-3 font-weight-bold w-100"
                                        onclick="selectFeaturedImage()">
                                        <i class="bx bx-image me-1"></i>Choose from Media Library
                                    </button>
                                    <div class="text-center font-11 text-secondary">— or upload new —</div>
                                    <input type="file" class="form-control" id="story-featured-image"
                                        name="featured_image" accept="image/*" onchange="clearSelectedFeaturedImage()">
                                </div>
                                <div class="mt-3 d-none" id="featured-image-preview-container">
                                    <img src="" id="featured-image-preview"
                                        class="img-fluid rounded border shadow-sm"
                                        style="max-height: 150px; object-fit: cover; width: 100%;">
                                    <button type="button" class="btn btn-outline-danger btn-sm radius-30 px-2 mt-2 w-100"
                                        onclick="removeFeaturedImage()">Remove Selection</button>
                                </div>
                                <small class="text-muted d-block mt-2 font-11">Max 20 MB. Accepted: JPEG, PNG, GIF,
                                    WEBP.</small>
                            </div>
                        </div>

                    </div>
                    <!-- ===================== End Right Column ===================== -->

                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <!-- Summernote Lite CSS -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
        <style>
            .note-editor.note-frame {
                border: 1px solid rgba(0, 0, 0, 0.15);
                border-radius: 4px;
                background-color: #fff;
            }

            .note-editor .note-editable {
                font-size: 16px;
                line-height: 1.6;
                color: #333;
                background-color: #fff;
                padding: 15px !important;
            }

            .note-editor.note-frame .note-statusbar {
                background-color: #f8f9fa;
            }

            .note-btn-group .note-btn {
                background-color: #fff;
                border: 1px solid rgba(0, 0, 0, 0.1);
                color: #333;
            }
        </style>
    @endpush

    @include('backend.components.media_modal')

    @push('scripts')
        <!-- Summernote Lite JS -->
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
        <script>
            $(document).ready(function() {
                // Helper to extract YouTube ID
                function getYouTubeId(url) {
                    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                    const match = url.match(regExp);
                    return (match && match[2].length === 11) ? match[2] : null;
                }

                // Define Custom Media Library Button for Summernote
                var MediaLibraryButton = function(context) {
                    var ui = $.summernote.ui;
                    var button = ui.button({
                        contents: '<i class="bx bx-images"/> Media Library',
                        tooltip: 'Insert Image from Media Library',
                        click: function() {
                            openMediaLibraryModal(function(url) {
                                context.invoke('editor.insertImage', url);
                            });
                        }
                    });
                    return button.render();
                };

                // Define Custom YouTube Insert Button for Summernote
                var YouTubeVideoButton = function(context) {
                    var ui = $.summernote.ui;
                    var button = ui.button({
                        contents: '<i class="bx bxl-youtube text-danger"/> YouTube Video',
                        tooltip: 'Insert YouTube Video',
                        click: function() {
                            var url = prompt("Enter YouTube video link (e.g., https://youtu.be/...):");
                            if (url) {
                                var videoId = getYouTubeId(url);
                                if (videoId) {
                                    var embedHtml =
                                        '<div class="embedded-video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; margin: 1.5rem 0; border-radius: 12px;"><iframe src="https://www.youtube.com/embed/' +
                                        videoId +
                                        '" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe></div>';
                                    context.invoke('editor.pasteHTML', embedHtml);
                                } else {
                                    alert(
                                        "Invalid YouTube URL. Please make sure to copy a valid YouTube video link.");
                                }
                            }
                        }
                    });
                    return button.render();
                };

                $('#story-content').summernote({
                    placeholder: 'Write your story content here...',
                    tabsize: 2,
                    height: 450,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript',
                            'subscript', 'clear'
                        ]],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph', 'height']],
                        ['table', ['table']],
                        ['insert', ['mediaLibrary', 'link', 'youtubeVideo', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    buttons: {
                        mediaLibrary: MediaLibraryButton,
                        youtubeVideo: YouTubeVideoButton
                    },
                    dialogsInBody: true,
                    callbacks: {
                        onImageUpload: function(files) {
                            for (let i = 0; i < files.length; i++) {
                                uploadImage(files[i]);
                            }
                        },
                        onPaste: function(e) {
                            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData)
                                .getData('text/html');
                            if (!bufferText) {
                                return;
                            }
                            e.preventDefault();

                            var div = document.createElement('div');
                            div.innerHTML = bufferText;

                            var elements = div.getElementsByTagName('*');
                            for (var i = 0; i < elements.length; i++) {
                                elements[i].removeAttribute('style');
                                elements[i].removeAttribute('class');
                            }

                            document.execCommand('insertHTML', false, div.innerHTML);
                        }
                    }
                });

                function uploadImage(file) {
                    let data = new FormData();
                    data.append("image", file);

                    $.ajax({
                        url: "{{ route('editor.upload-image') }}",
                        method: "POST",
                        data: data,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#story-content').summernote('insertImage', response.url);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Image upload failed: ", textStatus, errorThrown);
                            alert("Failed to upload image. Please check logs.");
                        }
                    });
                }
            });

            // Featured Image selection from Media Library
            function selectFeaturedImage() {
                openMediaLibraryModal(function(url, path) {
                    document.getElementById('featured_image_url').value = path;
                    document.getElementById('featured-image-preview').src = url;
                    document.getElementById('featured-image-preview-container').classList.remove('d-none');
                    // Clear file input to avoid conflicts
                    document.getElementById('story-featured-image').value = '';
                });
            }

            function clearSelectedFeaturedImage() {
                // If a file is uploaded directly, clear the media library selection
                document.getElementById('featured_image_url').value = '';
                document.getElementById('featured-image-preview-container').classList.add('d-none');
            }

            function removeFeaturedImage() {
                document.getElementById('featured_image_url').value = '';
                document.getElementById('featured-image-preview').src = '';
                document.getElementById('featured-image-preview-container').classList.add('d-none');
            }

            // Set hidden status field and update the label before form submits
            // Wait, we need setStatus to match the story status field
            function setStatus(value) {
                document.getElementById('story-status').value = value;
                document.getElementById('status-label').textContent =
                    value === 'draft' ? 'Draft' : 'Published';
            }

            // Character counters for SEO fields
            const metaTitleInput = document.getElementById('seo-meta-title');
            const metaTitleCount = document.getElementById('meta-title-count');
            const metaDescInput = document.getElementById('seo-meta-desc');
            const metaDescCount = document.getElementById('meta-desc-count');

            function updateCounter(input, counter, limit) {
                const len = input.value.length;
                counter.textContent = len + ' / ' + limit;
                counter.classList.toggle('text-danger', len > limit);
            }

            metaTitleInput.addEventListener('input', () => updateCounter(metaTitleInput, metaTitleCount, 60));
            metaDescInput.addEventListener('input', () => updateCounter(metaDescInput, metaDescCount, 160));

            // Initialise on page load (for old() values)
            updateCounter(metaTitleInput, metaTitleCount, 60);
            updateCounter(metaDescInput, metaDescCount, 160);

            // Preview button handler
            document.getElementById('btn-preview').addEventListener('click', function(e) {
                e.preventDefault();
                var form = document.getElementById('story-form');
                var originalAction = form.action;
                var originalTarget = form.target;

                form.action = "{{ route('stories.preview') }}";
                form.target = '_blank';
                form.submit();

                form.action = originalAction;
                if (originalTarget) {
                    form.target = originalTarget;
                } else {
                    form.removeAttribute('target');
                }
            });

            // ===================== SEO Analyzer =====================
            (function() {
                const titleEl = document.getElementById('story-title');
                const metaTitle = document.getElementById('seo-meta-title');
                const metaDesc = document.getElementById('seo-meta-desc');
                const metaKw = document.getElementById('seo-meta-keywords');
                const contentEl = document.getElementById('story-content');

                const scoreNum = document.getElementById('seo-score-num');
                const scoreBadge = document.getElementById('seo-score-badge');
                const scoreLabel = document.getElementById('seo-score-label');
                const scoreSub = document.getElementById('seo-score-sub');
                const gauge = document.getElementById('seo-gauge-circle');
                const serpTitle = document.getElementById('serp-title');
                const serpDesc = document.getElementById('serp-desc');
                const serpUrl = document.getElementById('serp-url');
                const checksList = document.getElementById('seo-checks-list');

                const POWER_WORDS = ['best', 'top', 'guide', 'how', 'why', 'what', 'tips', 'ways', 'secrets', 'proven',
                    'ultimate', 'complete', 'easy', 'free', 'new', 'fast', 'quick', 'simple'
                ];

                function wordCount(str) {
                    const t = str.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
                    return t ? t.split(' ').length : 0;
                }

                function keywordDensity(content, kw) {
                    if (!kw || !content) return 0;
                    const text = content.replace(/<[^>]*>/g, ' ').toLowerCase();
                    const words = text.split(/\s+/).filter(Boolean);
                    const matches = words.filter(w => w.includes(kw.toLowerCase())).length;
                    return words.length > 0 ? (matches / words.length) * 100 : 0;
                }

                function hasPowerWord(str) {
                    return POWER_WORDS.some(w => str.toLowerCase().includes(w));
                }

                function analyse() {
                    const title = (titleEl ? titleEl.value : '').trim();
                    const mTitle = (metaTitle ? metaTitle.value : '').trim();
                    const mDesc = (metaDesc ? metaDesc.value : '').trim();
                    const kwStr = (metaKw ? metaKw.value : '').trim();
                    const content = (contentEl ? contentEl.value : '').trim();
                    const kwList = kwStr ? kwStr.split(',').map(k => k.trim()).filter(Boolean) : [];
                    const focusKw = kwList[0] || '';
                    const wc = wordCount(content);
                    const density = keywordDensity(content, focusKw);

                    serpTitle.textContent = mTitle || title || 'Your story title will appear here';
                    serpTitle.style.color = (mTitle || title) ? '#1a0dab' : '#999';
                    serpDesc.textContent = mDesc || 'Your meta description will appear here.';
                    serpDesc.style.color = mDesc ? '#4d5156' : '#bbb';
                    if (title) {
                        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                        serpUrl.textContent = 'makoons.com › stories › ' + slug;
                    }

                    const checks = [{
                            label: 'Meta Title length',
                            run: () => {
                                if (!mTitle) return 'fail';
                                const l = mTitle.length;
                                if (l >= 50 && l <= 60) return 'pass';
                                if (l >= 40 && l <= 70) return 'warn';
                                return 'fail';
                            },
                            msgs: {
                                pass: `Meta Title: ${mTitle.length} chars (ideal 50–60)`,
                                warn: `Meta Title: ${mTitle.length} chars (try for 50–60)`,
                                fail: !mTitle ? 'Meta Title is empty' :
                                    `Meta Title: ${mTitle.length} chars (too short/long)`
                            }
                        },
                        {
                            label: 'Focus keyword in Title',
                            run: () => {
                                if (!focusKw || !mTitle) return 'fail';
                                return mTitle.toLowerCase().includes(focusKw.toLowerCase()) ? 'pass' : 'fail';
                            },
                            msgs: {
                                pass: `"${focusKw}" found in meta title`,
                                warn: '',
                                fail: !focusKw ? 'Add at least one keyword' : `"${focusKw}" missing from meta title`
                            }
                        },
                        {
                            label: 'Power words in Title',
                            run: () => {
                                if (!mTitle && !title) return 'fail';
                                return hasPowerWord(mTitle || title) ? 'pass' : 'warn';
                            },
                            msgs: {
                                pass: 'Title contains a power word 🎯',
                                warn: 'Add power words like Best, Guide, Tips…',
                                fail: 'Title is empty'
                            }
                        },
                        {
                            label: 'Meta Description length',
                            run: () => {
                                if (!mDesc) return 'fail';
                                const l = mDesc.length;
                                if (l >= 150 && l <= 160) return 'pass';
                                if (l >= 120 && l <= 180) return 'warn';
                                return 'fail';
                            },
                            msgs: {
                                pass: `Description: ${mDesc.length} chars (perfect!)`,
                                warn: `Description: ${mDesc.length} chars (aim for 150–160)`,
                                fail: !mDesc ? 'Meta Description is empty' :
                                    `Description: ${mDesc.length} chars (too short/long)`
                            }
                        },
                        {
                            label: 'Focus keyword in Description',
                            run: () => {
                                if (!focusKw || !mDesc) return 'fail';
                                return mDesc.toLowerCase().includes(focusKw.toLowerCase()) ? 'pass' : 'fail';
                            },
                            msgs: {
                                pass: `"${focusKw}" found in description`,
                                warn: '',
                                fail: !focusKw ? 'Set a focus keyword first' : `"${focusKw}" missing from description`
                            }
                        },
                        {
                            label: 'Story Title length',
                            run: () => {
                                if (!title) return 'fail';
                                if (title.length <= 60) return 'pass';
                                if (title.length <= 70) return 'warn';
                                return 'fail';
                            },
                            msgs: {
                                pass: `Story title length OK (${title.length} chars)`,
                                warn: `Story title a bit long (${title.length} chars)`,
                                fail: !title ? 'Story title is empty' : `Story title too long (${title.length} chars)`
                            }
                        },
                        {
                            label: 'Keywords filled',
                            run: () => kwList.length > 0 ? 'pass' : 'fail',
                            msgs: {
                                pass: `${kwList.length} keyword(s) added`,
                                warn: '',
                                fail: 'Add comma-separated keywords'
                            }
                        },
                        {
                            label: 'Keyword count (no stuffing)',
                            run: () => {
                                if (kwList.length === 0) return 'fail';
                                if (kwList.length <= 5) return 'pass';
                                if (kwList.length <= 8) return 'warn';
                                return 'fail';
                            },
                            msgs: {
                                pass: `${kwList.length} keywords (ideal ≤5)`,
                                warn: `${kwList.length} keywords (consider reducing)`,
                                fail: kwList.length === 0 ? 'No keywords set' : `${kwList.length} keywords (too many!)`
                            }
                        },
                        {
                            label: 'Keyword density',
                            run: () => {
                                if (!focusKw || !content) return 'warn';
                                if (density <= 3) return 'pass';
                                if (density <= 5) return 'warn';
                                return 'fail';
                            },
                            msgs: {
                                pass: `Keyword density: ${density.toFixed(1)}% (good)`,
                                warn: !content ? 'Add content to check density' :
                                    `Keyword density: ${density.toFixed(1)}% (slightly high)`,
                                fail: `Keyword density: ${density.toFixed(1)}% (too high!)`
                            }
                        },
                        {
                            label: 'Content word count',
                            run: () => {
                                if (wc >= 300) return 'pass';
                                if (wc >= 100) return 'warn';
                                return 'fail';
                            },
                            msgs: {
                                pass: `${wc} words (great length!)`,
                                warn: `${wc} words (aim for 300+)`,
                                fail: `${wc} words (too short for Google)`
                            }
                        },
                    ];

                    let score = 0;
                    const icons = {
                        pass: '<span style="color:#198754;font-size:13px;">✔</span>',
                        warn: '<span style="color:#ffc107;font-size:13px;">⚠</span>',
                        fail: '<span style="color:#dc3545;font-size:13px;">✖</span>'
                    };
                    const points = {
                        pass: 10,
                        warn: 5,
                        fail: 0
                    };

                    checksList.innerHTML = checks.map(c => {
                        const status = c.run();
                        score += points[status];
                        const msg = c.msgs[status] || c.label;
                        const color = status === 'pass' ? '#198754' : status === 'warn' ? '#856404' : '#842029';
                        const bg = status === 'pass' ? '#d1e7dd' : status === 'warn' ? '#fff3cd' : '#f8d7da';
                        return `<div style="display:flex;align-items:flex-start;gap:7px;padding:5px 8px;border-radius:6px;background:${bg};"><span style="flex-shrink:0;margin-top:1px;">${icons[status]}</span><span style="font-size:11.5px;color:${color};line-height:1.4;">${msg}</span></div>`;
                    }).join('');

                    const pct = Math.round(score);
                    scoreNum.textContent = pct;
                    scoreBadge.textContent = pct + ' / 100';
                    gauge.setAttribute('stroke-dasharray', pct + ' ' + (100 - pct));
                    if (pct >= 70) {
                        gauge.setAttribute('stroke', '#198754');
                        scoreBadge.style.cssText = 'background:#d1e7dd;color:#0a3622;';
                        scoreLabel.textContent = 'Good SEO Score 🟢';
                        scoreSub.textContent = 'Your SEO looks solid. Keep improving!';
                    } else if (pct >= 40) {
                        gauge.setAttribute('stroke', '#ffc107');
                        scoreBadge.style.cssText = 'background:#fff3cd;color:#664d03;';
                        scoreLabel.textContent = 'Average Score 🟡';
                        scoreSub.textContent = 'Some improvements needed for better ranking.';
                    } else {
                        gauge.setAttribute('stroke', '#dc3545');
                        scoreBadge.style.cssText = 'background:#f8d7da;color:#58151c;';
                        scoreLabel.textContent = 'Poor SEO Score 🔴';
                        scoreSub.textContent = 'Fill in meta fields and keywords to improve.';
                    }
                }

                [titleEl, metaTitle, metaDesc, metaKw].forEach(el => {
                    if (el) el.addEventListener('input', analyse);
                });
                setInterval(() => {
                    if (window.tinymce && tinymce.activeEditor) {
                        const val = tinymce.activeEditor.getContent();
                        if (contentEl && contentEl.value !== val) {
                            contentEl.value = val;
                            analyse();
                        }
                    }
                }, 2000);
                analyse();
            })();
        </script>
    @endpush
@endsection
