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
                            <li class="breadcrumb-item active" aria-current="page">Edit Printable</li>
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

            <form action="{{ route('printables.update', $printable->id) }}" method="POST" enctype="multipart/form-data" id="printable-form">
                @csrf
                @method('PUT')
                {{-- Hidden status field controlled by the publish/draft buttons --}}
                <input type="hidden" name="status" id="printable-status" value="{{ $printable->status }}">

                {{-- Hidden fields populated after AJAX file upload completes --}}
                <input type="hidden" name="file_path" id="file_path" value="{{ old('file_path', $printable->file_path) }}">
                <input type="hidden" name="file_name" id="file_name" value="{{ old('file_name', $printable->file_name) }}">
                <input type="hidden" name="file_size" id="file_size" value="{{ old('file_size', $printable->file_size) }}">

                <div class="row g-4">
                    <!-- ===================== Left Column ===================== -->
                    <div class="col-12 col-xl-8">

                        <!-- Name & Description card -->
                        <div class="card radius-10 border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label for="printable-name" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Printable Name</label>
                                    <input type="text" class="form-control form-control-lg border-0 bg-light-subtle shadow-none font-20"
                                           id="printable-name" name="name" value="{{ old('name', $printable->name) }}"
                                           placeholder="Enter printable name..." required
                                           style="font-weight: 600; padding: 12px 15px;">
                                </div>

                                <div class="mb-4">
                                    <label for="printable-slug" class="form-label font-weight-bold text-secondary font-12 text-uppercase">URL Slug</label>
                                    <input type="text" class="form-control border-0 bg-light-subtle shadow-none font-14"
                                           id="printable-slug" name="slug" value="{{ old('slug', $printable->slug) }}"
                                           placeholder="Optional: Enter custom URL slug (e.g. custom-slug)..."
                                           style="padding: 10px 15px;">
                                    <small class="text-muted mt-1 d-block font-11">Leave blank to automatically generate from the name.</small>
                                </div>

                                <div class="mb-0">
                                    <label for="printable-desc" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Description</label>
                                    <textarea class="form-control border-0 bg-light" id="printable-desc" name="description" rows="5"
                                              placeholder="Provide details about what this printable contains...">{{ old('description', $printable->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Card with Progress Bar -->
                        <div class="card radius-10 border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom pt-3 pb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-cloud-upload text-primary me-2 fs-5"></i>
                                    <h6 class="mb-0 font-weight-bold text-dark">Upload Printable Document</h6>
                                    <span class="badge bg-light-danger text-danger ms-2 font-11">Required</span>
                                </div>
                                <small class="text-muted font-11 d-block mt-1">Upload a new document to replace the current one (PDF/ZIP up to 50 MB).</small>
                            </div>
                            <div class="card-body p-4">
                                <!-- Current File details -->
                                <div class="p-3 bg-light border rounded mb-3" id="current-file-details">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bx bxs-file text-danger fs-3"></i>
                                        <div class="flex-grow-1">
                                            <span class="d-block font-weight-bold text-dark font-14">{{ $printable->file_name }}</span>
                                            <span class="text-muted font-11">Current File Size: {{ number_format($printable->file_size / (1024 * 1024), 2) }} MB</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="file-upload-input" class="form-label font-weight-bold text-secondary font-12 text-uppercase">Replace File (Optional)</label>
                                    <input class="form-control" type="file" id="file-upload-input" accept=".pdf,.zip,.doc,.docx,.png,.jpg,.jpeg">
                                </div>

                                <!-- Progress Bar Container -->
                                <div class="progress mb-3 d-none" style="height: 25px;" id="upload-progress-container">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary font-weight-bold" 
                                         role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="upload-progress-bar">0%</div>
                                </div>

                                <!-- Status indicators -->
                                <div class="alert alert-info border-0 bg-light-info text-info d-none" role="alert" id="upload-status-alert">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-info-circle fs-5 me-2" id="status-icon"></i>
                                        <div id="upload-status-text">Preparing upload...</div>
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
                                <h6 class="mb-0 font-weight-bold text-dark">Publish</h6>
                            </div>
                            <div class="card-body pt-1">
                                <!-- Status indicator -->
                                <div class="d-flex align-items-center gap-2 mb-3 py-2 px-3 bg-light rounded font-13">
                                    <i class="bx bx-info-circle text-secondary fs-5"></i>
                                    <span class="text-secondary">Status: </span>
                                    <span class="font-weight-bold text-dark text-capitalize" id="status-label">{{ $printable->status }}</span>
                                </div>
                            </div>
                            <div class="card-footer bg-light-subtle p-3 border-top-0">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('allPrintable') }}" class="btn btn-outline-secondary btn-sm radius-30 px-3">
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
                                <h6 class="mb-0 font-weight-bold text-dark">Thumbnail Image</h6>
                            </div>
                            <div class="card-body pt-1">
                                <input type="hidden" name="featured_image_url" id="featured_image_url" value="{{ $printable->image }}">
                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm radius-30 px-3 font-weight-bold w-100" onclick="selectFeaturedImage()">
                                        <i class="bx bx-image me-1"></i>Choose from Media Library
                                    </button>
                                    <div class="text-center font-11 text-secondary">— or upload new —</div>
                                    <input type="file" class="form-control" id="printable-featured-image" name="featured_image" accept="image/*" onchange="clearSelectedFeaturedImage()">
                                </div>
                                <div class="mt-3 {{ $printable->image ? '' : 'd-none' }}" id="featured-image-preview-container">
                                    <img src="{{ $printable->image ? asset($printable->image) : '' }}" id="featured-image-preview" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
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
                    document.getElementById('printable-featured-image').value = '';
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

            // Set hidden status field and update the label before form submits
            function setStatus(value) {
                document.getElementById('printable-status').value = value;
                document.getElementById('status-label').textContent =
                    value === 'draft' ? 'Draft' : 'Published';
            }

            // AJAX file upload with dynamic progress bar
            document.getElementById('file-upload-input').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate 50MB size limit
                const maxSize = 50 * 1024 * 1024; // 50MB
                if (file.size > maxSize) {
                    alert('File size exceeds the 50 MB limit.');
                    this.value = '';
                    return;
                }

                // Show progress bar and alert box
                const container = document.getElementById('upload-progress-container');
                const bar = document.getElementById('upload-progress-bar');
                const alertBox = document.getElementById('upload-status-alert');
                const statusText = document.getElementById('upload-status-text');
                const statusIcon = document.getElementById('status-icon');

                container.classList.remove('d-none');
                alertBox.classList.remove('d-none');
                alertBox.className = 'alert alert-info border-0 bg-light-info text-info';
                statusIcon.className = 'bx bx-info-circle fs-5 me-2';
                statusText.textContent = 'Uploading: 0%';
                bar.style.width = '0%';
                bar.textContent = '0%';

                // Disable submit buttons during upload
                document.getElementById('btn-draft').disabled = true;
                document.getElementById('btn-publish').disabled = true;

                // Setup Form Data
                const formData = new FormData();
                formData.append('file', file);

                // Perform AJAX upload via XMLHttpRequest to monitor progress
                const xhr = new XMLHttpRequest();
                xhr.open('POST', "{{ route('printables.upload-file') }}", true);
                xhr.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");

                // Progress Listener
                xhr.upload.addEventListener('progress', function(event) {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        bar.style.width = percentComplete + '%';
                        bar.textContent = percentComplete + '%';
                        statusText.textContent = `Uploading: ${percentComplete}% (${Math.round(event.loaded / (1024 * 1024) * 100) / 100}MB / ${Math.round(event.total / (1024 * 1024) * 100) / 100}MB)`;
                    }
                });

                // Completion Listener
                xhr.addEventListener('load', function() {
                    document.getElementById('btn-draft').disabled = false;
                    document.getElementById('btn-publish').disabled = false;

                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        
                        // Fill hidden input fields
                        document.getElementById('file_path').value = response.path;
                        document.getElementById('file_name').value = response.name;
                        document.getElementById('file_size').value = response.size;

                        // Update alert classes to success
                        alertBox.className = 'alert alert-success border-0 bg-light-success text-success';
                        statusIcon.className = 'bx bx-check-circle fs-5 me-2';
                        statusText.textContent = 'Upload complete! File successfully replaced.';
                        
                        // Set progress bar to full green
                        bar.className = 'progress-bar bg-success font-weight-bold';
                        bar.textContent = '100% Complete';

                        // Hide current details since it's replaced
                        document.getElementById('current-file-details').classList.add('d-none');
                    } else {
                        // Error handling
                        let errorMsg = 'Failed to upload printable. Please try again.';
                        try {
                            const errResponse = JSON.parse(xhr.responseText);
                            errorMsg = errResponse.message || errResponse.error || errorMsg;
                        } catch(e) {}

                        alertBox.className = 'alert alert-danger border-0 bg-light-danger text-danger';
                        statusIcon.className = 'bx bx-x-circle fs-5 me-2';
                        statusText.textContent = 'Upload failed: ' + errorMsg;
                        
                        bar.className = 'progress-bar bg-danger font-weight-bold';
                        bar.textContent = 'Failed';
                    }
                });

                // Error Listener
                xhr.addEventListener('error', function() {
                    document.getElementById('btn-draft').disabled = false;
                    document.getElementById('btn-publish').disabled = false;

                    alertBox.className = 'alert alert-danger border-0 bg-light-danger text-danger';
                    statusIcon.className = 'bx bx-x-circle fs-5 me-2';
                    statusText.textContent = 'Upload failed due to connection error.';
                    
                    bar.className = 'progress-bar bg-danger';
                    bar.textContent = 'Error';
                });

                // Send request
                xhr.send(formData);
            });

            // Prevent form submit if file is not uploaded
            document.getElementById('printable-form').addEventListener('submit', function(e) {
                const filePath = document.getElementById('file_path').value;
                if (!filePath) {
                    e.preventDefault();
                    alert('Please upload a printable file first.');
                }
            });
        </script>
    @endpush
@endsection
