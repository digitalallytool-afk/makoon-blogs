@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Header Welcome -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1 font-weight-bold text-dark">Makoon Panel Dashboard</h4>
                    <p class="mb-0 text-secondary font-13">Welcome back! Here is a live summary of your articles, stories, printables, and video sessions.</p>
                </div>
            </div>

            <!-- Stats row -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
                <!-- Articles Stats -->
                <div class="col">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-secondary font-12 text-uppercase font-weight-bold">Total Blogs</p>
                                    <h3 class="my-1 font-weight-bold text-dark">{{ number_format($totalPosts) }}</h3>
                                    <p class="mb-0 font-11 text-success"><i class="bx bx-check-double me-1"></i>{{ number_format($publishedPosts) }} published</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-light-primary text-primary ms-auto">
                                    <i class='bx bx-file-blank'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Stories Stats -->
                <div class="col">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-secondary font-12 text-uppercase font-weight-bold">Total Stories</p>
                                    <h3 class="my-1 font-weight-bold text-dark">{{ number_format($totalStories) }}</h3>
                                    <p class="mb-0 font-11 text-success"><i class="bx bx-book-content me-1"></i>{{ number_format($publishedStories) }} published</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-light-success text-success ms-auto">
                                    <i class='bx bx-detail'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Printables Stats -->
                <div class="col">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-secondary font-12 text-uppercase font-weight-bold">Total Printables</p>
                                    <h3 class="my-1 font-weight-bold text-dark">{{ number_format($totalPrintables) }}</h3>
                                    <p class="mb-0 font-11 text-success"><i class="bx bx-download me-1"></i>{{ number_format($publishedPrintables) }} published</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-light-warning text-warning ms-auto">
                                    <i class='bx bx-cloud-download'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Video Sessions Stats -->
                <div class="col">
                    <div class="card radius-10 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-secondary font-12 text-uppercase font-weight-bold">Video Sessions</p>
                                    <h3 class="my-1 font-weight-bold text-dark">{{ number_format($totalVideoSessions) }}</h3>
                                    <p class="mb-0 font-11 text-success"><i class="bx bx-play-circle me-1"></i>{{ number_format($publishedVideoSessions) }} published</p>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-light-danger text-danger ms-auto">
                                    <i class='bx bx-video'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->

            <div class="row g-4">
                <!-- Left column: Recent Articles & Recent Stories -->
                <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                    <!-- Recent Articles -->
                    <div class="card radius-10 border-0 shadow-sm w-100 mb-0">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">Recent Blogs</h6>
                                    <small class="text-muted font-12">Latest updates from your publishers</small>
                                </div>
                                <a href="{{ route('allPost') }}" class="btn btn-outline-primary btn-sm ms-auto radius-30 px-3">View All Blogs</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover">
                                    <thead class="table-light text-secondary font-12 text-uppercase">
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th class="text-end">Views</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-14">
                                        @forelse($recentPosts as $post)
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold text-dark d-block text-truncate" style="max-width: 250px;">{{ $post->title }}</span>
                                                    <small class="text-muted font-11">By {{ $post->author->name ?? '—' }}</small>
                                                </td>
                                                <td><span class="badge bg-light-primary text-primary px-2 py-1 radius-30">{{ $post->category->name ?? 'Uncategorized' }}</span></td>
                                                <td>{{ $post->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($post->status === 'published')
                                                        <span class="badge bg-light-success text-success px-3 py-1 radius-30">Published</span>
                                                    @else
                                                        <span class="badge bg-light-warning text-warning px-3 py-1 radius-30">Draft</span>
                                                    @endif
                                                </td>
                                                <td class="text-end"><span class="font-weight-bold text-dark"><i class="bx bx-show me-1 font-12 text-secondary"></i>{{ number_format($post->view_count) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-secondary">No blogs created yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Stories -->
                    <div class="card radius-10 border-0 shadow-sm w-100 mb-0">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">Recent Stories</h6>
                                    <small class="text-muted font-12">Latest uploads from stories section</small>
                                </div>
                                <a href="{{ route('allStory') }}" class="btn btn-outline-primary btn-sm ms-auto radius-30 px-3">View All Stories</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-hover">
                                    <thead class="table-light text-secondary font-12 text-uppercase">
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th class="text-end">Views</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-14">
                                        @forelse($recentStories as $story)
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold text-dark d-block text-truncate" style="max-width: 250px;">{{ $story->title }}</span>
                                                    <small class="text-muted font-11">By {{ $story->author->name ?? '—' }}</small>
                                                </td>
                                                <td><span class="badge bg-light-success text-success px-2 py-1 radius-30">{{ $story->storyCategory->name ?? 'Uncategorized' }}</span></td>
                                                <td>{{ $story->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($story->status === 'published')
                                                        <span class="badge bg-light-success text-success px-3 py-1 radius-30">Published</span>
                                                    @else
                                                        <span class="badge bg-light-warning text-warning px-3 py-1 radius-30">Draft</span>
                                                    @endif
                                                </td>
                                                <td class="text-end"><span class="font-weight-bold text-dark"><i class="bx bx-show me-1 font-12 text-secondary"></i>{{ number_format($story->view_count) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-secondary">No stories created yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column: Recent Printables & Recent Video Sessions -->
                <div class="col-12 col-lg-4 d-flex flex-column gap-4">
                    <!-- Recent Printables -->
                    <div class="card radius-10 border-0 shadow-sm w-100 mb-0">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">Recent Printables</h6>
                                    <small class="text-muted font-12">Latest documents uploaded</small>
                                </div>
                                <a href="{{ route('allPrintable') }}" class="btn btn-outline-primary btn-sm ms-auto radius-30 px-2 font-11">All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                @forelse($recentPrintables as $printable)
                                    <div class="d-flex align-items-center gap-3 border-bottom pb-2">
                                        <div class="widgets-icons rounded bg-light-primary text-primary" style="width:38px; height:38px; font-size: 18px;">
                                            <i class="bx bx-cloud-download"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="mb-0 font-weight-bold text-dark font-13 text-truncate">{{ $printable->name }}</h6>
                                            <small class="text-muted font-11">Size: {{ number_format($printable->file_size / (1024 * 1024), 2) }} MB | {{ $printable->download_count }} dl</small>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-secondary font-13 mb-0 text-center py-3">No printables found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Recent Video Sessions -->
                    <div class="card radius-10 border-0 shadow-sm w-100 mb-0">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">Recent Video Sessions</h6>
                                    <small class="text-muted font-12">Latest uploads</small>
                                </div>
                                <a href="{{ route('allVideoSession') }}" class="btn btn-outline-primary btn-sm ms-auto radius-30 px-2 font-11">All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                @forelse($recentVideoSessions as $session)
                                    <div class="d-flex align-items-center gap-3 border-bottom pb-2">
                                        <div class="widgets-icons rounded bg-light-danger text-danger" style="width:38px; height:38px; font-size: 18px;">
                                            <i class="bx bx-video"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="mb-0 font-weight-bold text-dark font-13 text-truncate">{{ $session->title }}</h6>
                                            <small class="text-muted font-11">Category: {{ $session->sessionCategory->name ?? '—' }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-secondary font-13 mb-0 text-center py-3">No video sessions found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end row-->
        </div>
    </div>
@endsection
