<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Printable;
use App\Models\Role;
use App\Models\SessionCategory;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\User;
use App\Models\VideoSession;
use App\Services\SitemapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Show the dashboard index page.
     */
    public function index(): View
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();

        $totalStories = Story::count();
        $publishedStories = Story::where('status', 'published')->count();

        $totalPrintables = Printable::count();
        $publishedPrintables = Printable::where('status', 'published')->count();

        $totalVideoSessions = VideoSession::count();
        $publishedVideoSessions = VideoSession::where('status', 'published')->count();

        $recentPosts = Post::with(['category', 'author'])->latest()->take(5)->get();
        $recentStories = Story::with(['storyCategory', 'author'])->latest()->take(5)->get();
        $recentPrintables = Printable::latest()->take(4)->get();
        $recentVideoSessions = VideoSession::with('sessionCategory')->latest()->take(4)->get();

        return view('backend.pages.index', compact(
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'totalStories',
            'publishedStories',
            'totalPrintables',
            'publishedPrintables',
            'totalVideoSessions',
            'publishedVideoSessions',
            'recentPosts',
            'recentStories',
            'recentPrintables',
            'recentVideoSessions'
        ));
    }

    /**
     * Show the articles management page.
     */
    public function allPosts(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $category = $request->input('category_id');

        $posts = Post::with(['category', 'author'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($category, fn ($q) => $q->where('category_id', $category))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::whereNull('parent_id')->get();

        return view('backend.pages.manage_post', compact('posts', 'categories', 'search', 'status', 'category'));
    }

    /**
     * Alias for allPosts.
     */
    public function managePosts(Request $request): View
    {
        return $this->allPosts($request);
    }

    /**
     * Export all filtered posts to CSV (Excel-compatible).
     */
    public function exportPosts(Request $request): StreamedResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $category = $request->input('category_id');

        $posts = Post::with(['category', 'author'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($category, fn ($q) => $q->where('category_id', $category))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($posts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Title', 'Slug', 'Category', 'Author', 'Status', 'Views', 'Created At']);
            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->id,
                    $post->title,
                    $post->slug,
                    $post->category->name ?? '',
                    $post->author->name ?? '',
                    $post->status,
                    $post->view_count,
                    $post->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'blogs-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the form for creating a new article.
     */
    public function newPost(): View
    {
        $categories = Category::all();
        $authors = Author::all();

        return view('backend.pages.new_post', compact('categories', 'authors'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function storePost(Request $request, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
            'is_selected' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['title']);
        if (empty($slug)) {
            $slug = 'blog';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/posts'), $filename);
            $imagePath = 'uploads/posts/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $imagePath = $request->input('featured_image_url');
        }

        Post::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'status' => $validated['status'],
            'category_id' => $validated['category_id'],
            'author_id' => $validated['author_id'],
            'featured_image' => $imagePath,
            'is_selected' => $request->boolean('is_selected'),
            'is_trending' => $request->boolean('is_trending'),
            'view_count' => 0,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => rtrim(config('app.url'), '/').'/blogs/'.$slug,
        ]);

        // Rebuild sitemap & robots.txt to reflect current published posts
        $sitemapService->generate();

        $message = $validated['status'] === 'draft'
            ? 'Blog saved as draft successfully.'
            : 'Blog published successfully.';

        return redirect()->route('allPost')->with('success', $message);
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroyPost(Post $post, SitemapService $sitemapService): RedirectResponse
    {
        // Delete featured image from disk if it exists
        if ($post->featured_image && file_exists(public_path($post->featured_image))) {
            @unlink(public_path($post->featured_image));
        }

        // Delete any embedded images in the post content
        if ($post->content) {
            preg_match_all('/<img[^>]+src="([^">]+)"/i', $post->content, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $src) {
                    $path = parse_url($src, PHP_URL_PATH);
                    if ($path) {
                        $localPath = public_path(ltrim($path, '/'));
                        // Make sure we only delete files inside our uploads folder to prevent accidental deletions of system files
                        if (str_contains($path, '/uploads/') && file_exists($localPath) && is_file($localPath)) {
                            @unlink($localPath);
                        }
                    }
                }
            }
        }

        $post->delete();

        // Rebuild sitemap to remove this post
        $sitemapService->generate();

        return redirect()->back()->with('success', 'Blog deleted successfully.');
    }

    /**
     * Display the specified article.
     */
    public function showPost(Post $post): View
    {
        $post->load(['category', 'author']);

        return view('backend.pages.view_post', compact('post'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function editPost(Post $post): View
    {
        $categories = Category::all();
        $authors = Author::all();

        return view('backend.pages.edit_post', compact('post', 'categories', 'authors'));
    }

    /**
     * Update the specified article in storage.
     */
    public function updatePost(Request $request, Post $post, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
            'is_selected' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Handle slug generation if title changed
        if ($validated['title'] !== $post->title) {
            $slug = Str::slug($validated['title']);
            if (empty($slug)) {
                $slug = 'blog';
            }
            $originalSlug = $slug;
            $count = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }
            $post->slug = $slug;
            $post->canonical_url = rtrim(config('app.url'), '/').'/blogs/'.$slug;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image file if it exists
            if ($post->featured_image && file_exists(public_path($post->featured_image))) {
                @unlink(public_path($post->featured_image));
            }

            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/posts'), $filename);
            $post->featured_image = 'uploads/posts/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $post->featured_image = $request->input('featured_image_url');
        }

        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->excerpt = $validated['excerpt'] ?? null;
        $post->status = $validated['status'];
        $post->category_id = $validated['category_id'];
        $post->author_id = $validated['author_id'];
        $post->is_selected = $request->boolean('is_selected');
        $post->is_trending = $request->boolean('is_trending');
        $post->meta_title = $validated['meta_title'] ?? null;
        $post->meta_description = $validated['meta_description'] ?? null;
        $post->meta_keywords = $validated['meta_keywords'] ?? null;

        $post->save();

        // Rebuild sitemap & robots.txt to reflect current published posts
        $sitemapService->generate();

        return redirect()->route('allPost')->with('success', 'Blog updated successfully.');
    }

    /**
     * Upload an image from the WYSIWYG editor.
     */
    public function uploadEditorImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/posts'), $filename);
            $url = asset('uploads/posts/'.$filename);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No image uploaded.'], 400);
    }

    /**
     * Show the categories management page.
     */
    public function categories(Request $request): View
    {
        $catSearch = $request->input('cat_search');
        $subSearch = $request->input('sub_search');

        $categories = Category::whereNull('parent_id')
            ->when($catSearch, fn ($q) => $q->where('name', 'like', "%{$catSearch}%"))
            ->paginate(12, ['*'], 'cat_page')
            ->withQueryString();

        $subcategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->when($subSearch, fn ($q) => $q->where('name', 'like', "%{$subSearch}%"))
            ->paginate(12, ['*'], 'sub_page')
            ->withQueryString();

        // All top-level categories for select dropdowns (no pagination)
        $allParentCategories = Category::whereNull('parent_id')->get();

        return view('backend.pages.add_Category', compact(
            'categories', 'subcategories', 'allParentCategories', 'catSearch', 'subSearch'
        ));
    }

    /**
     * Export all categories to CSV.
     */
    public function exportCategories(): StreamedResponse
    {
        $categories = Category::with('parent')->get();

        return response()->streamDownload(function () use ($categories) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Type', 'Parent Category', 'Description', 'Created At']);
            foreach ($categories as $cat) {
                fputcsv($handle, [
                    $cat->id,
                    $cat->name,
                    $cat->slug,
                    $cat->parent_id ? 'Subcategory' : 'Main Category',
                    $cat->parent->name ?? '',
                    $cat->description ?? '',
                    $cat->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'categories-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Store a newly created category or subcategory in storage.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $slug = Str::slug($validated['name']);
        if (empty($slug)) {
            $slug = 'category';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $type = isset($validated['parent_id']) ? 'Subcategory' : 'Category';

        return redirect()->back()->with('success', "{$type} created successfully.");
    }

    /**
     * Update the specified category or subcategory in storage.
     */
    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if (isset($validated['parent_id']) && (int) $validated['parent_id'] === $category->id) {
            return redirect()->back()->with('error', 'A category cannot be its own parent.');
        }

        $slug = Str::slug($validated['name']);
        if (empty($slug)) {
            $slug = 'category';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category or subcategory from storage.
     */
    public function destroyCategory(Category $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return redirect()->back()->with('success', "Category '{$name}' has been successfully deleted.");
    }

    /**
     * Show the media library page.
     */
    public function mediaLibrary(): View
    {
        $items = $this->getMediaFilesFromDisk();
        $perPage = 21;
        $page = Paginator::resolveCurrentPage('page') ?: 1;
        $currentPageItems = array_slice($items, ($page - 1) * $perPage, $perPage);

        $mediaFiles = new LengthAwarePaginator(
            $currentPageItems,
            count($items),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        return view('backend.pages.media_library', compact('mediaFiles'));
    }

    /**
     * Upload files to the media library from page.
     */
    public function uploadMedia(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:524288', // 512MB max matching comment in view
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/posts'), $filename);
            }
        }

        return redirect()->back()->with('success', 'Files uploaded successfully.');
    }

    /**
     * Delete a file from the media library page.
     */
    public function deleteMedia(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');
        $localPath = public_path($path);

        // Security check: ensure path is inside uploads folder and file exists
        if (str_contains($path, 'uploads/') && ! str_contains($path, '..') && file_exists($localPath) && is_file($localPath)) {
            @unlink($localPath);

            return redirect()->back()->with('success', 'File deleted successfully.');
        }

        return redirect()->back()->with('error', 'Invalid file path or file not found.');
    }

    /**
     * API: Get all media files.
     */
    public function apiGetMedia(): JsonResponse
    {
        return response()->json($this->getMediaFilesFromDisk());
    }

    /**
     * API: Upload a new media image.
     */
    public function apiUploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/posts'), $filename);
            $url = asset('uploads/posts/'.$filename);
            $path = 'uploads/posts/'.$filename;

            return response()->json([
                'url' => $url,
                'path' => $path,
                'name' => $filename,
                'size' => filesize(public_path($path)),
                'type' => 'image',
            ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }

    /**
     * Helper: Scan disk for uploaded files.
     */
    private function getMediaFilesFromDisk(): array
    {
        $files = [];
        $postsPath = public_path('uploads/posts');
        $authorsPath = public_path('uploads/authors');

        $paths = [$postsPath, $authorsPath];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $dirFiles = scandir($path);
                foreach ($dirFiles as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $filePath = $path.'/'.$file;
                        if (is_file($filePath)) {
                            $relativePath = 'uploads/'.basename($path).'/'.$file;
                            $files[] = [
                                'name' => $file,
                                'url' => asset($relativePath),
                                'path' => $relativePath,
                                'size' => filesize($filePath),
                                'time' => filemtime($filePath),
                                'type' => $this->getFileType($file),
                            ];
                        }
                    }
                }
            }
        }

        // Sort by time descending (newest first)
        usort($files, fn ($a, $b) => $b['time'] <=> $a['time']);

        return $files;
    }

    /**
     * Helper: Determine file type.
     */
    private function getFileType(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'])) {
            return 'image';
        }
        if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi'])) {
            return 'video';
        }
        if (in_array($ext, ['mp3', 'wav', 'ogg'])) {
            return 'audio';
        }

        return 'document';
    }

    /**
     * Show the user management page listing all users, roles, and permissions.
     */
    public function users(): View
    {
        $this->ensureRolesAndPermissionsAreSeeded();

        // Eager load roles and direct permissions to prevent N+1 queries
        $users = User::with(['roles', 'permissions'])->get();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('backend.pages.users', compact('users', 'roles', 'permissions'));
    }

    /**
     * Update user roles and direct permissions.
     */
    public function updateUserPermissions(Request $request, User $user): RedirectResponse
    {
        $this->ensureRolesAndPermissionsAreSeeded();

        $validated = $request->validate([
            'role' => 'required|exists:roles,slug',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,slug',
        ]);

        // Enforce primary Super Admin protection
        if ($user->id === 1 && $validated['role'] !== 'super-admin') {
            return redirect()->back()->with('error', 'The primary Super Admin cannot be demoted.');
        }

        // Sync the user's role
        $user->syncRoles([$validated['role']]);

        // Sync direct permissions
        if ($validated['role'] === 'super-admin') {
            // Super admins bypass all permissions, so clear direct ones
            $user->syncPermissions([]);
        } else {
            $user->syncPermissions($request->input('permissions', []));
        }

        return redirect()->back()->with('success', "Role and permissions for {$user->name} have been updated successfully.");
    }

    /**
     * Store a new user created by the Super Admin from inside the dashboard.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $this->ensureRolesAndPermissionsAreSeeded();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,slug',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,slug',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Sync role
        $user->syncRoles([$validated['role']]);

        // Sync permissions (only if role is admin)
        if ($validated['role'] === 'admin') {
            $user->syncPermissions($request->input('permissions', []));
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->back()->with('success', "User {$user->name} has been created successfully with the assigned role and permissions.");
    }

    /**
     * Delete a user account from the system.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        // Prevent deleting the primary Super Admin
        if ($user->id === 1) {
            return redirect()->back()->with('error', 'The primary Super Admin account cannot be deleted.');
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "User account for {$name} has been successfully deleted.");
    }

    /**
     * Show the authors management page.
     */
    public function authors(Request $request): View
    {
        $search = $request->input('search');

        $authors = Author::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('backend.pages.authors', compact('authors', 'search'));
    }

    /**
     * Export all authors to CSV.
     */
    public function exportAuthors(): StreamedResponse
    {
        $authors = Author::latest()->get();

        return response()->streamDownload(function () use ($authors) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Description', 'Created At']);
            foreach ($authors as $author) {
                fputcsv($handle, [
                    $author->id,
                    $author->name,
                    $author->description ?? '',
                    $author->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'authors-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Store a newly created author in storage.
     */
    public function storeAuthor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'description' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/authors'), $filename);
            $imagePath = 'uploads/authors/'.$filename;
        }

        Author::create([
            'name' => $validated['name'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Author created successfully.');
    }

    /**
     * Update the specified author in storage.
     */
    public function updateAuthor(Request $request, Author $author): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'description' => 'nullable|string',
        ]);

        $imagePath = $author->image;
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($author->image && file_exists(public_path($author->image))) {
                @unlink(public_path($author->image));
            }

            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/authors'), $filename);
            $imagePath = 'uploads/authors/'.$filename;
        }

        $author->update([
            'name' => $validated['name'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Author updated successfully.');
    }

    /**
     * Remove the specified author from storage.
     */
    public function destroyAuthor(Author $author): RedirectResponse
    {
        $name = $author->name;

        // Delete image file if exists
        if ($author->image && file_exists(public_path($author->image))) {
            @unlink(public_path($author->image));
        }

        $author->delete();

        return redirect()->back()->with('success', "Author '{$name}' has been successfully deleted.");
    }

    /**
     * Show the story categories management page.
     */
    public function storyCategories(Request $request): View
    {
        $search = $request->input('search');

        $categories = StoryCategory::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('backend.pages.add_StoryCategory', compact('categories', 'search'));
    }

    /**
     * Store a newly created story category in storage.
     */
    public function storeStoryCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        if (empty($slug)) {
            $slug = 'category';
        }
        $originalSlug = $slug;
        $count = 1;
        while (StoryCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        StoryCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Story Category created successfully.');
    }

    /**
     * Update the specified story category in storage.
     */
    public function updateStoryCategory(Request $request, StoryCategory $storyCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        if (empty($slug)) {
            $slug = 'category';
        }
        $originalSlug = $slug;
        $count = 1;
        while (StoryCategory::where('slug', $slug)->where('id', '!=', $storyCategory->id)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $storyCategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Story Category updated successfully.');
    }

    /**
     * Remove the specified story category from storage.
     */
    public function destroyStoryCategory(StoryCategory $storyCategory): RedirectResponse
    {
        $name = $storyCategory->name;
        $storyCategory->delete();

        return redirect()->back()->with('success', "Story Category '{$name}' has been successfully deleted.");
    }

    /**
     * Export all story categories to CSV.
     */
    public function exportStoryCategories(): StreamedResponse
    {
        $categories = StoryCategory::latest()->get();

        return response()->streamDownload(function () use ($categories) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Description', 'Created At']);
            foreach ($categories as $cat) {
                fputcsv($handle, [
                    $cat->id,
                    $cat->name,
                    $cat->slug,
                    $cat->description ?? '',
                    $cat->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'story-categories-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the stories management page.
     */
    public function allStories(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $category = $request->input('story_category_id');

        $stories = Story::with(['storyCategory', 'author'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($category, fn ($q) => $q->where('story_category_id', $category))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = StoryCategory::all();

        return view('backend.pages.manage_story', compact('stories', 'categories', 'search', 'status', 'category'));
    }

    /**
     * Show the form for creating a new story.
     */
    public function newStory(): View
    {
        $categories = StoryCategory::all();
        $authors = Author::all();

        return view('backend.pages.new_story', compact('categories', 'authors'));
    }

    /**
     * Store a newly created story in storage.
     */
    public function storeStory(Request $request, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'story_category_id' => 'required|exists:story_categories,id',
            'author_id' => 'required|exists:authors,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['title']);
        if (empty($slug)) {
            $slug = 'story';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Story::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/posts'), $filename);
            $imagePath = 'uploads/posts/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $imagePath = $request->input('featured_image_url');
        }

        Story::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'status' => $validated['status'],
            'story_category_id' => $validated['story_category_id'],
            'author_id' => $validated['author_id'],
            'featured_image' => $imagePath,
            'view_count' => 0,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => rtrim(config('app.url'), '/').'/stories/'.$slug,
        ]);

        // Rebuild sitemap & robots.txt to reflect current published stories
        $sitemapService->generate();

        $message = $validated['status'] === 'draft'
            ? 'Story saved as draft successfully.'
            : 'Story published successfully.';

        return redirect()->route('allStory')->with('success', $message);
    }

    /**
     * Display the specified story.
     */
    public function showStory(Story $story): View
    {
        $story->load(['storyCategory', 'author']);

        return view('backend.pages.view_story', compact('story'));
    }

    /**
     * Show the form for editing the specified story.
     */
    public function editStory(Story $story): View
    {
        $categories = StoryCategory::all();
        $authors = Author::all();

        return view('backend.pages.edit_story', compact('story', 'categories', 'authors'));
    }

    /**
     * Update the specified story in storage.
     */
    public function updateStory(Request $request, Story $story, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'story_category_id' => 'required|exists:story_categories,id',
            'author_id' => 'required|exists:authors,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Handle slug generation if title changed
        if ($validated['title'] !== $story->title) {
            $slug = Str::slug($validated['title']);
            if (empty($slug)) {
                $slug = 'story';
            }
            $originalSlug = $slug;
            $count = 1;
            while (Story::where('slug', $slug)->where('id', '!=', $story->id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }
            $story->slug = $slug;
            $story->canonical_url = rtrim(config('app.url'), '/').'/stories/'.$slug;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image file if it exists
            if ($story->featured_image && file_exists(public_path($story->featured_image))) {
                @unlink(public_path($story->featured_image));
            }

            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/posts'), $filename);
            $story->featured_image = 'uploads/posts/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $story->featured_image = $request->input('featured_image_url');
        }

        $story->title = $validated['title'];
        $story->content = $validated['content'];
        $story->excerpt = $validated['excerpt'] ?? null;
        $story->status = $validated['status'];
        $story->story_category_id = $validated['story_category_id'];
        $story->author_id = $validated['author_id'];
        $story->meta_title = $validated['meta_title'] ?? null;
        $story->meta_description = $validated['meta_description'] ?? null;
        $story->meta_keywords = $validated['meta_keywords'] ?? null;

        $story->save();

        // Rebuild sitemap & robots.txt to reflect current published stories
        $sitemapService->generate();

        return redirect()->route('allStory')->with('success', 'Story updated successfully.');
    }

    /**
     * Remove the specified story from storage.
     */
    public function destroyStory(Story $story, SitemapService $sitemapService): RedirectResponse
    {
        // Delete featured image from disk if it exists
        if ($story->featured_image && file_exists(public_path($story->featured_image))) {
            @unlink(public_path($story->featured_image));
        }

        // Delete any embedded images in the story content
        if ($story->content) {
            preg_match_all('/<img[^>]+src="([^">]+)"/i', $story->content, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $src) {
                    $path = parse_url($src, PHP_URL_PATH);
                    if ($path) {
                        $localPath = public_path(ltrim($path, '/'));
                        if (str_contains($path, '/uploads/') && file_exists($localPath) && is_file($localPath)) {
                            @unlink($localPath);
                        }
                    }
                }
            }
        }

        $story->delete();

        // Rebuild sitemap to remove this story
        $sitemapService->generate();

        return redirect()->back()->with('success', 'Story deleted successfully.');
    }

    /**
     * Export all filtered stories to CSV.
     */
    public function exportStories(Request $request): StreamedResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $category = $request->input('story_category_id');

        $stories = Story::with(['storyCategory', 'author'])
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($category, fn ($q) => $q->where('story_category_id', $category))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($stories) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Title', 'Slug', 'Story Category', 'Author', 'Status', 'Views', 'Created At']);
            foreach ($stories as $story) {
                fputcsv($handle, [
                    $story->id,
                    $story->title,
                    $story->slug,
                    $story->storyCategory->name ?? '',
                    $story->author->name ?? '',
                    $story->status,
                    $story->view_count,
                    $story->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'stories-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the printables management page.
     */
    public function allPrintables(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $printables = Printable::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('backend.pages.manage_printable', compact('printables', 'search', 'status'));
    }

    /**
     * Show the form for creating a new printable.
     */
    public function newPrintable(): View
    {
        return view('backend.pages.new_printable');
    }

    /**
     * Handle AJAX file upload for printable files (supporting up to 50MB).
     */
    public function uploadPrintableFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $originalName);

            // Move to uploads/printables/files
            $file->move(public_path('uploads/printables/files'), $filename);
            $filePath = 'uploads/printables/files/'.$filename;
            $fileSize = filesize(public_path($filePath));

            return response()->json([
                'success' => true,
                'path' => $filePath,
                'name' => $originalName,
                'size' => $fileSize,
            ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }

    /**
     * Store a newly created printable in storage.
     */
    public function storePrintable(Request $request, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
            'file_path' => 'required|string',
            'file_name' => 'required|string',
            'file_size' => 'required|integer',
        ]);

        $slug = Str::slug($validated['name']);
        if (empty($slug)) {
            $slug = 'printable';
        }
        $originalSlug = $slug;
        $count = 1;
        while (Printable::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/printables/images'), $filename);
            $imagePath = 'uploads/printables/images/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $imagePath = $request->input('featured_image_url');
        }

        Printable::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'file_path' => $validated['file_path'],
            'file_name' => $validated['file_name'],
            'file_size' => $validated['file_size'],
            'download_count' => 0,
            'status' => $validated['status'],
        ]);

        $sitemapService->generate();

        $message = $validated['status'] === 'draft'
            ? 'Printable saved as draft successfully.'
            : 'Printable published successfully.';

        return redirect()->route('allPrintable')->with('success', $message);
    }

    /**
     * Display the specified printable.
     */
    public function showPrintable(Printable $printable): View
    {
        return view('backend.pages.view_printable', compact('printable'));
    }

    /**
     * Show the form for editing the specified printable.
     */
    public function editPrintable(Printable $printable): View
    {
        return view('backend.pages.edit_printable', compact('printable'));
    }

    /**
     * Update the specified printable in storage.
     */
    public function updatePrintable(Request $request, Printable $printable, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
            'file_path' => 'required|string',
            'file_name' => 'required|string',
            'file_size' => 'required|integer',
        ]);

        // Handle slug generation if name changed
        if ($validated['name'] !== $printable->name) {
            $slug = Str::slug($validated['name']);
            if (empty($slug)) {
                $slug = 'printable';
            }
            $originalSlug = $slug;
            $count = 1;
            while (Printable::where('slug', $slug)->where('id', '!=', $printable->id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }
            $printable->slug = $slug;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image file if it exists
            if ($printable->image && file_exists(public_path($printable->image))) {
                @unlink(public_path($printable->image));
            }

            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/printables/images'), $filename);
            $printable->image = 'uploads/printables/images/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $printable->image = $request->input('featured_image_url');
        }

        // If the printable file path changed, delete the old file
        if ($validated['file_path'] !== $printable->file_path) {
            if ($printable->file_path && file_exists(public_path($printable->file_path))) {
                @unlink(public_path($printable->file_path));
            }
            $printable->file_path = $validated['file_path'];
            $printable->file_name = $validated['file_name'];
            $printable->file_size = $validated['file_size'];
        }

        $printable->name = $validated['name'];
        $printable->description = $validated['description'] ?? null;
        $printable->status = $validated['status'];

        $printable->save();

        $sitemapService->generate();

        return redirect()->route('allPrintable')->with('success', 'Printable updated successfully.');
    }

    /**
     * Remove the specified printable from storage.
     */
    public function destroyPrintable(Printable $printable, SitemapService $sitemapService): RedirectResponse
    {
        // Delete image file if exists
        if ($printable->image && file_exists(public_path($printable->image))) {
            @unlink(public_path($printable->image));
        }

        // Delete printable file if exists
        if ($printable->file_path && file_exists(public_path($printable->file_path))) {
            @unlink(public_path($printable->file_path));
        }

        $printable->delete();

        $sitemapService->generate();

        return redirect()->back()->with('success', 'Printable deleted successfully.');
    }

    /**
     * Export printables to CSV.
     */
    public function exportPrintables(Request $request): StreamedResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $printables = Printable::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($printables) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'File Name', 'File Size (Bytes)', 'Downloads', 'Status', 'Created At']);
            foreach ($printables as $printable) {
                fputcsv($handle, [
                    $printable->id,
                    $printable->name,
                    $printable->slug,
                    $printable->file_name,
                    $printable->file_size,
                    $printable->download_count,
                    $printable->status,
                    $printable->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'printables-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Session Categories CRUD Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Display session categories management page.
     */
    public function sessionCategories(): View
    {
        $categories = SessionCategory::withCount('videoSessions')->latest()->get();

        return view('backend.pages.add_SessionCategory', compact('categories'));
    }

    /**
     * Store a new session category.
     */
    public function storeSessionCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        if (empty($slug)) {
            $slug = 'category';
        }
        $originalSlug = $slug;
        $count = 1;
        while (SessionCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        SessionCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Video Session Category created successfully.');
    }

    /**
     * Update the specified session category.
     */
    public function updateSessionCategory(Request $request, SessionCategory $sessionCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validated['name'] !== $sessionCategory->name) {
            $slug = Str::slug($validated['name']);
            if (empty($slug)) {
                $slug = 'category';
            }
            $originalSlug = $slug;
            $count = 1;
            while (SessionCategory::where('slug', $slug)->where('id', '!=', $sessionCategory->id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }
            $sessionCategory->slug = $slug;
        }

        $sessionCategory->name = $validated['name'];
        $sessionCategory->description = $validated['description'] ?? null;
        $sessionCategory->save();

        return redirect()->back()->with('success', 'Video Session Category updated successfully.');
    }

    /**
     * Remove the specified session category.
     */
    public function destroySessionCategory(SessionCategory $sessionCategory): RedirectResponse
    {
        if ($sessionCategory->videoSessions()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category because it contains active video sessions.');
        }

        $sessionCategory->delete();

        return redirect()->back()->with('success', 'Video Session Category deleted successfully.');
    }

    /**
     * Export session categories to CSV.
     */
    public function exportSessionCategories(): StreamedResponse
    {
        $categories = SessionCategory::withCount('videoSessions')->latest()->get();

        return response()->streamDownload(function () use ($categories) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Description', 'Video Sessions Count', 'Created At']);
            foreach ($categories as $category) {
                fputcsv($handle, [
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->description,
                    $category->video_sessions_count,
                    $category->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'session-categories-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Video Sessions CRUD Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Display video sessions listing page.
     */
    public function allVideoSessions(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $categoryId = $request->input('session_category_id');

        $categories = SessionCategory::orderBy('name')->get();

        $videoSessions = VideoSession::with('sessionCategory')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->where('session_category_id', $categoryId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('backend.pages.manage_video_session', compact('videoSessions', 'categories', 'search', 'status', 'categoryId'));
    }

    /**
     * Show form for creating a new video session.
     */
    public function newVideoSession(): View
    {
        $categories = SessionCategory::orderBy('name')->get();

        return view('backend.pages.new_video_session', compact('categories'));
    }

    /**
     * Store a newly created video session.
     */
    public function storeVideoSession(Request $request, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_category_id' => 'required|exists:session_categories,id',
            'video_url' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
        ]);

        $embedUrl = $this->parseYoutubeEmbedUrl($validated['video_url']);
        if (! $embedUrl) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['video_url' => 'The video URL must be a valid YouTube link (e.g. watch link, sharing link, or embed link).']);
        }

        $slug = Str::slug($validated['title']);
        if (empty($slug)) {
            $slug = 'session';
        }
        $originalSlug = $slug;
        $count = 1;
        while (VideoSession::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/sessions/images'), $filename);
            $imagePath = 'uploads/sessions/images/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $imagePath = $request->input('featured_image_url');
        }

        VideoSession::create([
            'session_category_id' => $validated['session_category_id'],
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'video_url' => $embedUrl,
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        $sitemapService->generate();

        $message = $validated['status'] === 'draft'
            ? 'Video Session saved as draft successfully.'
            : 'Video Session published successfully.';

        return redirect()->route('allVideoSession')->with('success', $message);
    }

    /**
     * Display details of a video session.
     */
    public function showVideoSession(VideoSession $videoSession): View
    {
        return view('backend.pages.view_video_session', compact('videoSession'));
    }

    /**
     * Show form for editing a video session.
     */
    public function editVideoSession(VideoSession $videoSession): View
    {
        $categories = SessionCategory::orderBy('name')->get();

        return view('backend.pages.edit_video_session', compact('videoSession', 'categories'));
    }

    /**
     * Update the specified video session.
     */
    public function updateVideoSession(Request $request, VideoSession $videoSession, SitemapService $sitemapService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'session_category_id' => 'required|exists:session_categories,id',
            'video_url' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'featured_image_url' => 'nullable|string',
        ]);

        $embedUrl = $this->parseYoutubeEmbedUrl($validated['video_url']);
        if (! $embedUrl) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['video_url' => 'The video URL must be a valid YouTube link (e.g. watch link, sharing link, or embed link).']);
        }

        if ($validated['title'] !== $videoSession->title) {
            $slug = Str::slug($validated['title']);
            if (empty($slug)) {
                $slug = 'session';
            }
            $originalSlug = $slug;
            $count = 1;
            while (VideoSession::where('slug', $slug)->where('id', '!=', $videoSession->id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }
            $videoSession->slug = $slug;
        }

        if ($request->hasFile('featured_image')) {
            if ($videoSession->image && file_exists(public_path($videoSession->image))) {
                @unlink(public_path($videoSession->image));
            }

            $file = $request->file('featured_image');
            $filename = time().'_'.preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/sessions/images'), $filename);
            $videoSession->image = 'uploads/sessions/images/'.$filename;
        } elseif ($request->input('featured_image_url')) {
            $videoSession->image = $request->input('featured_image_url');
        }

        $videoSession->title = $validated['title'];
        $videoSession->session_category_id = $validated['session_category_id'];
        $videoSession->video_url = $embedUrl;
        $videoSession->description = $validated['description'] ?? null;
        $videoSession->status = $validated['status'];

        $videoSession->save();

        $sitemapService->generate();

        return redirect()->route('allVideoSession')->with('success', 'Video Session updated successfully.');
    }

    /**
     * Remove the specified video session.
     */
    public function destroyVideoSession(VideoSession $videoSession, SitemapService $sitemapService): RedirectResponse
    {
        if ($videoSession->image && file_exists(public_path($videoSession->image))) {
            @unlink(public_path($videoSession->image));
        }

        $videoSession->delete();

        $sitemapService->generate();

        return redirect()->back()->with('success', 'Video Session deleted successfully.');
    }

    /**
     * Export video sessions to CSV.
     */
    public function exportVideoSessions(Request $request): StreamedResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $categoryId = $request->input('session_category_id');

        $videoSessions = VideoSession::with('sessionCategory')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->where('session_category_id', $categoryId))
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($videoSessions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Title', 'Slug', 'Category', 'Video URL', 'Status', 'Created At']);
            foreach ($videoSessions as $session) {
                fputcsv($handle, [
                    $session->id,
                    $session->title,
                    $session->slug,
                    $session->sessionCategory->name ?? 'Uncategorized',
                    $session->video_url,
                    $session->status,
                    $session->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($handle);
        }, 'video-sessions-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Parse any YouTube link and return the corresponding embed URL.
     * Returns null if not a valid YouTube URL.
     */
    private function parseYoutubeEmbedUrl(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            $videoId = $matches[1];

            return 'https://www.youtube.com/embed/'.$videoId;
        }

        return null;
    }

    /**
     * Ensure roles and permissions are seeded in the database.
     */
    private function ensureRolesAndPermissionsAreSeeded(): void
    {
        if (Role::count() === 0 || Permission::count() === 0) {
            Artisan::call('db:seed', [
                '--class' => 'RolesAndPermissionsSeeder',
                '--no-interaction' => true,
            ]);
        }
    }
}
