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
                            <li class="breadcrumb-item active" aria-current="page">Edit Blog</li>
                        </ol>
                    </nav>
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

            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" id="article-form">
                @csrf
                @method('PUT')
                {{-- Hidden status field controlled by the publish/draft buttons --}}
                <input type="hidden" name="status" id="post-status" value="{{ old('status', $post->status) }}">

                <div class="row g-4">
                    <!-- ===================== Left Column ===================== -->
                    <div class="col-12 col-xl-8">

                        <!-- Title & Content card -->
                        <div class="card radius-10 border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label for="post-title" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Article Title</label>
                                    <input type="text" class="form-control form-control-lg border-0 bg-light-subtle shadow-none font-20"
                                           id="post-title" name="title" value="{{ old('title', $post->title) }}"
                                           placeholder="Enter article title..." required
                                           style="font-weight: 600; padding: 12px 15px;">
                                </div>

                                <!-- Editor -->
                                <div class="mb-4">
                                    <label for="post-content" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Article Content</label>
                                    <textarea class="form-control" id="post-content" name="content" required>{{ old('content', $post->content) }}</textarea>
                                </div>

                                <!-- Excerpt -->
                                <div class="mb-0">
                                    <label for="post-excerpt" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Excerpt</label>
                                    <textarea class="form-control border-0 bg-light" id="post-excerpt" name="excerpt" rows="3"
                                              placeholder="Write a brief article summary...">{{ old('excerpt', $post->excerpt) }}</textarea>
                                    <small class="text-muted mt-1 d-block font-11">Optional summary shown in listings and previews.</small>
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
                                <small class="text-muted font-11 d-block mt-1">Improve search engine visibility. Leave blank to use article title &amp; excerpt.</small>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <!-- Meta Title -->
                                    <div class="col-12">
                                        <label for="seo-meta-title" class="form-label font-weight-bold text-secondary font-12 text-uppercase">
                                            Meta Title
                                        </label>
                                        <input type="text" class="form-control border-0 bg-light"
                                               id="seo-meta-title" name="meta_title"
                                               value="{{ old('meta_title', $post->meta_title) }}"
                                               placeholder="SEO page title (recommended: 50–60 chars)"
                                               maxlength="255">
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted font-11">Shown in browser tabs and search results.</small>
                                            <small class="text-muted font-11" id="meta-title-count">0 / 60</small>
                                        </div>
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="col-12">
                                        <label for="seo-meta-desc" class="form-label font-weight-bold text-secondary font-12 text-uppercase">
                                            Meta Description
                                        </label>
                                        <textarea class="form-control border-0 bg-light"
                                                  id="seo-meta-desc" name="meta_description" rows="3"
                                                  placeholder="Brief description for search results (recommended: 150–160 chars)"
                                                  maxlength="500">{{ old('meta_description', $post->meta_description) }}</textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted font-11">Shown below the title in Google search results.</small>
                                            <small class="text-muted font-11" id="meta-desc-count">0 / 160</small>
                                        </div>
                                    </div>

                                    <!-- Meta Keywords -->
                                    <div class="col-12">
                                        <label for="seo-meta-keywords" class="form-label font-weight-bold text-secondary font-12 text-uppercase">
                                            Meta Keywords
                                        </label>
                                        <input type="text" class="form-control border-0 bg-light"
                                               id="seo-meta-keywords" name="meta_keywords"
                                               value="{{ old('meta_keywords', $post->meta_keywords) }}"
                                               placeholder="laravel, php, web development">
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
                                                <span>Draft blogs are <strong>excluded</strong> from sitemap</span>
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

                        <!-- Publish Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Publish / Update</h6>
                            </div>
                            <div class="card-body pt-1">
                                <!-- Status indicator -->
                                <div class="d-flex align-items-center gap-2 mb-3 py-2 px-3 bg-light rounded font-13">
                                    <i class="bx bx-info-circle text-secondary fs-5"></i>
                                    <span class="text-secondary">Status: </span>
                                    <span class="font-weight-bold text-dark" id="status-label">{{ ucfirst(old('status', $post->status)) }}</span>
                                </div>
                                <!-- Selected Blog Option -->
                                <div class="form-check form-switch mb-3" style="padding-left: 2.5em;">
                                    <input class="form-check-input" type="checkbox" id="post-is-selected" name="is_selected" value="1" {{ old('is_selected', $post->is_selected) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold text-dark font-13" for="post-is-selected">
                                        Selected Blog
                                    </label>
                                    <small class="text-muted d-block font-11 mt-1">Show this blog post in the homepage "Selected blogs" slider.</small>
                                </div>
                                <!-- Trending Blog Option -->
                                <div class="form-check form-switch mb-3" style="padding-left: 2.5em;">
                                    <input class="form-check-input" type="checkbox" id="post-is-trending" name="is_trending" value="1" {{ old('is_trending', $post->is_trending) ? 'checked' : '' }}>
                                    <label class="form-check-label font-weight-bold text-dark font-13" for="post-is-trending">
                                        Trending Blog
                                    </label>
                                    <small class="text-muted d-block font-11 mt-1">Show this blog post in the homepage "Trending blogs" section manually.</small>
                                </div>
                                <!-- View Count -->
                                <div class="mb-0">
                                    <label class="form-label font-weight-bold text-dark font-13" for="post-view-count">View Count</label>
                                    <input type="number" class="form-control font-13 @error('view_count') is-invalid @enderror" id="post-view-count" name="view_count" value="{{ old('view_count', $post->view_count) }}" min="0">
                                    @error('view_count')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted d-block font-11 mt-1">Specify view count for this blog.</small>
                                </div>
                            </div>
                            <div class="card-footer bg-light-subtle p-3 border-top-0">
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <a href="{{ route('allPost') }}" class="btn btn-outline-secondary px-2 py-1" style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        Cancel
                                    </a>
                                    <!-- Save as Draft -->
                                    <button type="submit" class="btn btn-outline-warning px-2 py-1"
                                            onclick="setStatus('draft')" id="btn-draft" style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        <i class="bx bx-save me-1"></i>Draft
                                    </button>
                                    <!-- Preview -->
                                    <button type="button" class="btn btn-outline-info px-2 py-1" id="btn-preview" style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        <i class="bx bx-show me-1"></i>Preview
                                    </button>
                                    <!-- Publish -->
                                    <button type="submit" class="btn btn-primary px-2 py-1"
                                            onclick="setStatus('published')" id="btn-publish" style="font-size: 11px; border-radius: 20px; font-weight: 500;">
                                        <i class="bx bx-paper-plane me-1"></i>Update
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Category Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Category</h6>
                            </div>
                            <div class="card-body pt-1">
                                <label for="post-category" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Select Category</label>
                                <select class="form-select" id="post-category" name="category_id" required>
                                    <option value="" disabled>-- Choose Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                            @if($category->parent)
                                                {{ $category->parent->name }} > {{ $category->name }}
                                            @else
                                                {{ $category->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2 font-11">Classify this article under a category.</small>
                            </div>
                        </div>

                        <!-- Author Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Author</h6>
                            </div>
                            <div class="card-body pt-1">
                                <label for="post-author" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Assign Author</label>
                                <select class="form-select" id="post-author" name="author_id" required>
                                    <option value="" disabled>-- Choose Author --</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id', $post->author_id) == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-2 font-11">Assign an author profile to this article.</small>
                            </div>
                        </div>

                        <!-- Featured Image Card -->
                        <div class="card radius-10 border-0 shadow-sm mb-0">
                            <div class="card-header bg-transparent border-bottom-0 pt-3">
                                <h6 class="mb-0 font-weight-bold text-dark">Featured Image</h6>
                            </div>
                            <div class="card-body pt-1">
                                <input type="hidden" name="featured_image_url" id="featured_image_url" value="{{ $post->featured_image }}">
                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm radius-30 px-3 font-weight-bold w-100" onclick="selectFeaturedImage()">
                                        <i class="bx bx-image me-1"></i>Choose from Media Library
                                    </button>
                                    <div class="text-center font-11 text-secondary">— or upload new —</div>
                                    <input type="file" class="form-control" id="post-featured-image" name="featured_image" accept="image/*" onchange="clearSelectedFeaturedImage()">
                                </div>
                                <div class="mt-3 {{ $post->featured_image ? '' : 'd-none' }}" id="featured-image-preview-container">
                                    <label class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Featured Image Preview</label>
                                    <img src="{{ $post->featured_image ? asset($post->featured_image) : '' }}" id="featured-image-preview" class="img-fluid rounded border d-block shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;" alt="featured image preview">
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

    @push('styles')
        <!-- Summernote Lite CSS -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
        <style>
            .note-editor.note-frame {
                border: 1px solid rgba(0,0,0,0.15);
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
                border: 1px solid rgba(0,0,0,0.1);
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
                // Define Custom Media Library Button for Summernote
                var MediaLibraryButton = function (context) {
                    var ui = $.summernote.ui;
                    var button = ui.button({
                        contents: '<i class="bx bx-images"/> Media Library',
                        tooltip: 'Insert Image from Media Library',
                        click: function () {
                            openMediaLibraryModal(function(url) {
                                context.invoke('editor.insertImage', url);
                            });
                        }
                    });
                    return button.render();
                };

                $('#post-content').summernote({
                    placeholder: 'Write your article content here...',
                    tabsize: 2,
                    height: 450,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph', 'height']],
                        ['table', ['table']],
                        ['insert', ['mediaLibrary', 'link', 'video', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    buttons: {
                        mediaLibrary: MediaLibraryButton
                    },
                    dialogsInBody: true,
                    callbacks: {
                        onImageUpload: function(files) {
                            for (let i = 0; i < files.length; i++) {
                                uploadImage(files[i]);
                            }
                        },
                        onPaste: function(e) {
                            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
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
                            $('#post-content').summernote('insertImage', response.url);
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
                    document.getElementById('post-featured-image').value = '';
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
            function setStatus(value) {
                document.getElementById('post-status').value = value;
                document.getElementById('status-label').textContent =
                    value === 'draft' ? 'Draft' : 'Published';
            }

            // Character counters for SEO fields
            const metaTitleInput = document.getElementById('seo-meta-title');
            const metaTitleCount = document.getElementById('meta-title-count');
            const metaDescInput  = document.getElementById('seo-meta-desc');
            const metaDescCount  = document.getElementById('meta-desc-count');

            function updateCounter(input, counter, limit) {
                const len = input.value.length;
                counter.textContent = len + ' / ' + limit;
                counter.classList.toggle('text-danger', len > limit);
            }

            metaTitleInput.addEventListener('input', () => updateCounter(metaTitleInput, metaTitleCount, 60));
            metaDescInput.addEventListener('input',  () => updateCounter(metaDescInput,  metaDescCount,  160));

            // Initialise on page load
            updateCounter(metaTitleInput, metaTitleCount, 60);
            updateCounter(metaDescInput,  metaDescCount,  160);

            // Preview button handler
            document.getElementById('btn-preview').addEventListener('click', function(e) {
                e.preventDefault();
                var form = document.getElementById('article-form');
                var originalAction = form.action;
                var originalTarget = form.target;
                
                form.action = "{{ route('posts.preview') }}";
                form.target = '_blank';
                form.submit();
                
                form.action = originalAction;
                if (originalTarget) {
                    form.target = originalTarget;
                } else {
                    form.removeAttribute('target');
                }
            });
        </script>
    @endpush
@endsection
