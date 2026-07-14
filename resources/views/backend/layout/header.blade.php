<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('uploads/2026/06/favicon.png') }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('backend') }}/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <link href="{{ asset('backend') }}/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('backend') }}/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('backend') }}/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('backend') }}/assets/css/pace.min.css" rel="stylesheet" />
    <script src="{{ asset('backend') }}/assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend') }}/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('backend') }}/assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('backend') }}/assets/css/app.css" rel="stylesheet">
    <link href="{{ asset('backend') }}/assets/css/icons.css" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/css/dark-theme.css" />
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/css/semi-dark.css" />
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/css/header-colors.css" />
    <!-- Eye-Comfort Custom Theme (loaded last to override defaults) -->
    <link rel="stylesheet" href="{{ asset('backend') }}/assets/css/premium-design.css" />
    <title>Makoon Panel - Admin Dashboard</title>
    @stack('styles')
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
				<div class="d-flex align-items-center justify-content-center w-100">
					<img src="{{ asset('backend/assets/images/makoons-logo.png') }}" class="brand-logo-img" alt="logo" style="width: 140px; height: auto; max-height: 45px; object-fit: contain;">
				</div>
				<div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
				</div>
			</div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                @can('view-dashboard')
                <li class="menu-label text-uppercase">Navigation</li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        <div class="parent-icon"><i class='bx bx-home-alt'></i>
                        </div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>
                @endcan

                @canany(['view-posts', 'create-posts', 'manage-categories', 'manage-authors'])
                <li class="menu-label text-uppercase">Blog Management</li>
                @can('manage-authors')
                <li>
                    <a href="{{ route('authors.index') }}">
                        <div class="parent-icon"><i class='bx bx-user'></i>
                        </div>
                        <div class="menu-title">Authors</div>
                    </a>
                </li>
                @endcan

                @can('manage-categories')
                <li>
                    <a href="{{ route('categories') }}">
                        <div class="parent-icon"><i class='bx bx-grid-alt'></i>
                        </div>
                        <div class="menu-title">Categories</div>
                    </a>
                </li>
                @endcan

                @can('create-posts')
                <li>
                    <a href="{{ route('newPost') }}">
                        <div class="parent-icon"><i class='bx bx-plus-circle'></i>
                        </div>
                        <div class="menu-title">Add New Blog</div>
                    </a>
                </li>
                @endcan

                @can('view-posts')
                <li>
                    <a href="{{ route('allPost') }}">
                        <div class="parent-icon"><i class='bx bx-detail'></i>
                        </div>
                        <div class="menu-title">All Blogs</div>
                    </a>
                </li>
                @endcan
                @endcanany

                @canany(['view-stories', 'create-stories', 'manage-story-categories'])
                <li class="menu-label text-uppercase">Story Management</li>
                @can('manage-story-categories')
                <li>
                    <a href="{{ route('storyCategories') }}">
                        <div class="parent-icon"><i class='bx bx-category'></i>
                        </div>
                        <div class="menu-title">Story Categories</div>
                    </a>
                </li>
                @endcan

                @can('create-stories')
                <li>
                    <a href="{{ route('newStory') }}">
                        <div class="parent-icon"><i class='bx bx-plus-circle'></i>
                        </div>
                        <div class="menu-title">Add New Story</div>
                    </a>
                </li>
                @endcan

                @can('view-stories')
                <li>
                    <a href="{{ route('allStory') }}">
                        <div class="parent-icon"><i class='bx bx-book-content'></i>
                        </div>
                        <div class="menu-title">All Stories</div>
                    </a>
                </li>
                @endcan
                @endcanany

                @canany(['view-printables', 'create-printables'])
                <li class="menu-label text-uppercase">Printable Management</li>
                @can('create-printables')
                <li>
                    <a href="{{ route('newPrintable') }}">
                        <div class="parent-icon"><i class='bx bx-plus-circle'></i>
                        </div>
                        <div class="menu-title">Add New Printable</div>
                    </a>
                </li>
                @endcan

                @can('view-printables')
                <li>
                    <a href="{{ route('allPrintable') }}">
                        <div class="parent-icon"><i class='bx bx-download'></i>
                        </div>
                        <div class="menu-title">All Printables</div>
                    </a>
                </li>
                @endcan
                @endcanany

                @canany(['view-video-sessions', 'create-video-sessions', 'manage-session-categories'])
                <li class="menu-label text-uppercase">Video Management</li>
                @can('manage-session-categories')
                <li>
                    <a href="{{ route('sessionCategories') }}">
                        <div class="parent-icon"><i class='bx bx-category-alt'></i>
                        </div>
                        <div class="menu-title">Session Categories</div>
                    </a>
                </li>
                @endcan

                @can('create-video-sessions')
                <li>
                    <a href="{{ route('newVideoSession') }}">
                        <div class="parent-icon"><i class='bx bx-plus-circle'></i>
                        </div>
                        <div class="menu-title">Add New Session</div>
                    </a>
                </li>
                @endcan

                @can('view-video-sessions')
                <li>
                    <a href="{{ route('allVideoSession') }}">
                        <div class="parent-icon"><i class='bx bx-video'></i>
                        </div>
                        <div class="menu-title">All Sessions</div>
                    </a>
                </li>
                @endcan
                @endcanany

                @can('manage-media')
                <li>
                    <a href="{{ route('mediaLibrary') }}">
                        <div class="parent-icon"><i class='bx bx-image'></i>
                        </div>
                        <div class="menu-title">Media Library</div>
                    </a>
                </li>
                @endcan

                @can('manage-users')
                <li class="menu-label text-uppercase">Access Control</li>
                <li>
                    <a href="{{ route('users.index') }}">
                        <div class="parent-icon"><i class='bx bx-user-pin'></i>
                        </div>
                        <div class="menu-title">User Management</div>
                    </a>
                </li>
                @endcan
            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand gap-3">
                    <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
                    </div>

                    <div class="search-bar d-lg-block d-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                        <a href="javascript:;" class="btn d-flex align-items-center"><i class='bx bx-search'></i>Search</a>
                    </div>

                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center gap-1">
                            <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                                data-bs-target="#SearchModal">
                                <a class="nav-link" href="javascript:;"><i class='bx bx-search'></i>
                                </a>
                            </li>
                            <li class="nav-item dropdown dropdown-laungauge d-none d-sm-flex">
                            </li>
                            <li class="nav-item dark-mode d-none d-sm-flex">
                                <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                                </a>
							</li>
							<li class="nav-item dropdown dropdown-app">
								<div class="dropdown-menu dropdown-menu-end p-0">
									<div class="app-container p-2 my-2">
									</div>
								</div>
							</li>

							<li class="nav-item dropdown dropdown-large">
								<div class="dropdown-menu dropdown-menu-end">
									<div class="header-notifications-list">
									</div>
									<a href="javascript:;">
										<div class="text-center msg-footer">
											<button class="btn btn-primary w-100">View All Notifications</button>
										</div>
									</a>
								</div>
							</li>
							<li class="nav-item dropdown dropdown-large">
								<div class="dropdown-menu dropdown-menu-end">
									<div class="header-message-list">
									</div>
									<a href="javascript:;">
										<div class="text-center msg-footer">
											<div class="d-flex align-items-center justify-content-between mb-3">
												<h5 class="mb-0">Total</h5>
												<h5 class="mb-0 ms-auto">$489.00</h5>
											</div>
											<button class="btn btn-primary w-100">Checkout</button>
										</div>
									</a>
								</div>
							</li>
						</ul>
					</div>
					<div class="user-box dropdown px-3">
						<a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#"
							role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="data:image/svg+xml;utf8,&lt;svg xmlns='http://www.w3.org/2000/svg' width='110' height='110' viewBox='0 0 24 24'&gt;&lt;circle cx='12' cy='12' r='12' fill='%23efefea'/&gt;&lt;circle cx='12' cy='8' r='4' fill='%23b0afaf'/&gt;&lt;path d='M4 20c0-3 3-5 8-5s8 2 8 5' fill='%23b0afaf'/&gt;&lt;/svg&gt;" class="user-img" alt="user avatar">
							<div class="user-info">
								<p class="user-name mb-0">{{ Auth::user()->name }}</p>
                                <p class="designattion mb-0">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
							</div>
						</a>
						<ul class="dropdown-menu dropdown-menu-end">
							<li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
										class="bx bx-user fs-5"></i><span>Profile</span></a>
							</li>
							<li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
										class="bx bx-cog fs-5"></i><span>Settings</span></a>
							</li>
							<li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
										class="bx bx-home-circle fs-5"></i><span>Dashboard</span></a>
							</li>
							<li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
										class="bx bx-dollar-circle fs-5"></i><span>Earnings</span></a>
							</li>
							<li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i
										class="bx bx-download fs-5"></i><span>Downloads</span></a>
							</li>
							<li>
								<div class="dropdown-divider mb-0"></div>
							</li>
							<li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form" class="d-none">
                                    @csrf
                                </form>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bx bx-log-out-circle"></i><span>Logout</span>
                                </a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
		<!--end header -->
