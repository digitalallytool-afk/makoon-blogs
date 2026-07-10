<!-- Media Library Modal -->
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 radius-10 shadow-lg">
            <div class="modal-header bg-light border-bottom p-3">
                <h5 class="modal-title font-weight-bold text-dark"><i class="bx bx-image text-primary me-2"></i>Media
                    Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs bg-light border-bottom px-3 pt-2" id="mediaModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active font-weight-bold py-2" id="upload-tab-btn" data-bs-toggle="tab"
                            data-bs-target="#modal-upload-tab" type="button" role="tab"
                            aria-controls="modal-upload-tab" aria-selected="true">
                            Upload Files
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link font-weight-bold py-2" id="library-tab-btn" data-bs-toggle="tab"
                            data-bs-target="#modal-library-tab" type="button" role="tab"
                            aria-controls="modal-library-tab" aria-selected="false">
                            Media Library
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content p-4" id="mediaModalTabsContent">
                    <!-- Tab 1: Upload -->
                    <div class="tab-pane fade show active" id="modal-upload-tab" role="tabpanel"
                        aria-labelledby="upload-tab-btn">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5 border border-dashed rounded bg-light"
                            id="modal-dropzone" style="border-color: #dee2e6 !important; min-height: 300px;">
                            <i class="bx bx-cloud-upload text-primary fs-1 mb-3"></i>
                            <h5 class="mb-1 font-weight-bold text-dark">Drag and drop file here to upload</h5>
                            <p class="text-secondary font-12 mb-3">or</p>
                            <input type="file" id="modal-file-input" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-outline-primary radius-30 px-4 font-weight-bold"
                                onclick="document.getElementById('modal-file-input').click()">Select File</button>
                            <small class="text-muted mt-3">Maximum file size: 20 MB. Accepted formats: JPEG, PNG, GIF,
                                WEBP.</small>
                            <div class="spinner-border text-primary mt-3 d-none" id="modal-upload-spinner"
                                role="status">
                                <span class="visually-hidden">Uploading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Library Grid -->
                    <div class="tab-pane fade" id="modal-library-tab" role="tabpanel" aria-labelledby="library-tab-btn">
                        <!-- Toolbar -->
                        <div class="row g-2 mb-3 align-items-center justify-content-between">
                            <div class="col-12 col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="bx bx-search text-secondary"></i></span>
                                    <input type="text" id="modal-search" class="form-control border-start-0 ps-0"
                                        placeholder="Search images..." onkeyup="filterModalMedia()">
                                </div>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 overflow-y-auto"
                            id="modal-media-grid" style="max-height: 400px; min-height: 250px;">
                            <!-- Dynamically loaded items will go here -->
                        </div>

                        <!-- Grid Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top d-none"
                            id="modal-pagination-container">
                            <div class="text-secondary font-11" id="modal-pagination-info">Showing 0–0 of 0 images</div>
                            <nav aria-label="Modal pagination">
                                <ul class="pagination pagination-sm mb-0" id="modal-pagination-links">
                                    <!-- Dynamic pagination links -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top p-3">
                <div class="me-auto text-secondary font-12" id="modal-selected-info">No image selected.</div>
                <button type="button" class="btn btn-secondary btn-sm radius-30 px-3"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm radius-30 px-4 font-weight-bold"
                    id="modal-select-btn" disabled>Insert Image</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .modal-media-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent !important;
        }

        .modal-media-card:hover {
            border-color: rgba(13, 110, 253, 0.4) !important;
        }

        .modal-media-card.selected {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .modal-media-card.selected::after {
            content: "\ec73";
            /* Boxicons check circle icon */
            font-family: 'boxicons' !important;
            position: absolute;
            top: 5px;
            right: 5px;
            color: #0d6efd;
            font-size: 18px;
            background: #fff;
            border-radius: 50%;
            line-height: 1;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            let selectedFile = null;
            let selectCallback = null;
            let allMediaItems = [];
            let filteredItems = [];
            let currentPage = 1;
            const pageSize = 12;

            // Global functions to open the modal
            window.openMediaLibraryModal = function(callback) {
                selectCallback = callback;
                selectedFile = null;

                // Reset modal select button state
                $('#modal-select-btn').prop('disabled', true);
                $('#modal-selected-info').text('No image selected.');

                // Switch to Library tab programmatically
                let libraryTabEl = document.getElementById('library-tab-btn');
                if (libraryTabEl) {
                    libraryTabEl.click();
                }

                loadModalMedia();

                // Show modal safely using Bootstrap instance or jQuery fallback
                var modalEl = document.getElementById('mediaLibraryModal');
                if (modalEl) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    } else {
                        $(modalEl).modal('show');
                    }
                }
            };

            // Load media files via AJAX
            function loadModalMedia() {
                let grid = $('#modal-media-grid');
                grid.html(
                    '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>'
                );
                $('#modal-pagination-container').addClass('d-none');

                $.getJSON("{{ route('api.media.index') }}", function(data) {
                    allMediaItems = data.filter(item => item.type === 'image');
                    filteredItems = [...allMediaItems];
                    currentPage = 1;
                    renderMediaGrid(filteredItems);
                }).fail(function() {
                    grid.html(
                        '<div class="col-12 text-center py-5 text-danger"><p>Failed to load media files.</p></div>'
                    );
                });
            }

            // Render library grid
            function renderMediaGrid(items) {
                let grid = $('#modal-media-grid');
                grid.empty();

                if (items.length === 0) {
                    grid.html(
                        '<div class="col-12 text-center py-5 text-secondary"><i class="bx bx-image-alt fs-1 mb-2"></i><p>No images found.</p></div>'
                    );
                    $('#modal-pagination-container').addClass('d-none');
                    return;
                }

                // Slice items for the current page
                let start = (currentPage - 1) * pageSize;
                let pageItems = items.slice(start, start + pageSize);

                pageItems.forEach(function(item) {
                    let card = $(`
                    <div class="col modal-media-col" data-path="${item.path}" data-url="${item.url}">
                        <div class="card h-100 radius-10 border shadow-sm mb-0 overflow-hidden modal-media-card position-relative">
                            <div class="ratio ratio-1x1 bg-light border-bottom overflow-hidden">
                                <img src="${item.url}" class="object-fit-cover w-100 h-100" alt="${item.name}">
                            </div>
                            <div class="p-2 card-body" style="padding: 6px !important;">
                                <h6 class="text-truncate mb-0 font-10 font-weight-bold text-dark" title="${item.name}">${item.name}</h6>
                            </div>
                        </div>
                    </div>
                `);

                    // Mark selected item visually if it matches our selectedFile
                    if (selectedFile && selectedFile.path === item.path) {
                        card.find('.modal-media-card').addClass('selected');
                    }

                    card.click(function() {
                        $('.modal-media-card').removeClass('selected');
                        $(this).find('.modal-media-card').addClass('selected');
                        selectedFile = {
                            url: item.url,
                            path: item.path
                        };
                        $('#modal-selected-info').html(
                            `Selected: <strong class="text-dark">${item.name}</strong>`);
                        $('#modal-select-btn').prop('disabled', false);
                    });

                    grid.append(card);
                });

                renderModalPagination(items.length);
            }

            // Render pagination buttons dynamically
            function renderModalPagination(totalItems) {
                let container = $('#modal-pagination-container');
                let info = $('#modal-pagination-info');
                let links = $('#modal-pagination-links');

                links.empty();

                if (totalItems <= pageSize) {
                    container.addClass('d-none');
                    return;
                }

                container.removeClass('d-none');

                let totalPages = Math.ceil(totalItems / pageSize);
                let startItem = (currentPage - 1) * pageSize + 1;
                let endItem = Math.min(currentPage * pageSize, totalItems);

                info.text(`Showing ${startItem}–${endItem} of ${totalItems} images`);

                // Previous Page Button
                let prevClass = currentPage === 1 ? 'disabled' : '';
                let prevBtn = $(`
                <li class="page-item ${prevClass}">
                    <a class="page-link" href="javascript:;" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `);
                if (currentPage > 1) {
                    prevBtn.click(function() {
                        currentPage--;
                        renderMediaGrid(filteredItems);
                    });
                }
                links.append(prevBtn);

                // Page Number Buttons
                for (let i = 1; i <= totalPages; i++) {
                    let activeClass = i === currentPage ? 'active' : '';
                    let pageBtn = $(`
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="javascript:;">${i}</a>
                    </li>
                `);
                    pageBtn.click(function() {
                        currentPage = i;
                        renderMediaGrid(filteredItems);
                    });
                    links.append(pageBtn);
                }

                // Next Page Button
                let nextClass = currentPage === totalPages ? 'disabled' : '';
                let nextBtn = $(`
                <li class="page-item ${nextClass}">
                    <a class="page-link" href="javascript:;" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `);
                if (currentPage < totalPages) {
                    nextBtn.click(function() {
                        currentPage++;
                        renderMediaGrid(filteredItems);
                    });
                }
                links.append(nextBtn);
            }

            // Search filter inside modal
            window.filterModalMedia = function() {
                let searchVal = $('#modal-search').val().toLowerCase();
                filteredItems = allMediaItems.filter(item => item.name.toLowerCase().includes(searchVal));
                currentPage = 1;
                renderMediaGrid(filteredItems);
            };

            // File upload from modal
            $('#modal-file-input').change(function() {
                let file = this.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('image', file);

                $('#modal-upload-spinner').removeClass('d-none');

                $.ajax({
                    url: "{{ route('api.media.upload') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#modal-upload-spinner').addClass('d-none');
                        $('#modal-file-input').val('');

                        // Switch tab to library programmatically
                        let libraryTabEl = document.getElementById('library-tab-btn');
                        if (libraryTabEl) {
                            libraryTabEl.click();
                        }

                        loadModalMedia();

                        // Inform user
                        alert("Image uploaded successfully!");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#modal-upload-spinner').addClass('d-none');
                        console.error("AJAX upload error: ", textStatus, errorThrown);
                        alert("Upload failed. Ensure the file is an image under 20MB.");
                    }
                });
            });

            // Insert Button Click Handler
            $('#modal-select-btn').click(function() {
                if (selectedFile && selectCallback) {
                    selectCallback(selectedFile.url, selectedFile.path);

                    // Hide modal safely using Bootstrap instance or jQuery fallback
                    var modalEl = document.getElementById('mediaLibraryModal');
                    if (modalEl) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.hide();
                        } else {
                            $(modalEl).modal('hide');
                        }
                    }
                }
            });
        })();
    </script>
@endpush
