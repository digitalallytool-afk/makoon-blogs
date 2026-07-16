<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard (accessible with view-dashboard permission)
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('dashboard')
        ->middleware('can:view-dashboard');

    // Articles list (accessible with view-posts permission)
    Route::get('/allpost', [AdminController::class, 'allPosts'])
        ->name('allPost')
        ->middleware('can:view-posts');

    // Add New Article (accessible with create-posts permission)
    Route::get('/newpost', [AdminController::class, 'newPost'])
        ->name('newPost')
        ->middleware('can:create-posts');

    Route::post('/posts', [AdminController::class, 'storePost'])
        ->name('posts.store')
        ->middleware('can:create-posts');

    Route::get('/posts/export', [AdminController::class, 'exportPosts'])
        ->name('posts.export')
        ->middleware('can:view-posts');

    Route::delete('/posts/{post}', [AdminController::class, 'destroyPost'])
        ->name('posts.destroy')
        ->middleware('can:delete-posts');

    // View Article detail (accessible with view-posts permission)
    Route::get('/posts/{post}', [AdminController::class, 'showPost'])
        ->name('posts.show')
        ->middleware('can:view-posts');

    // Edit Article form (accessible with edit-posts permission)
    Route::get('/posts/{post}/edit', [AdminController::class, 'editPost'])
        ->name('posts.edit')
        ->middleware('can:edit-posts');

    // Update Article in storage (accessible with edit-posts permission)
    Route::put('/posts/{post}', [AdminController::class, 'updatePost'])
        ->name('posts.update')
        ->middleware('can:edit-posts');

    // Editor Image Upload (accessible with create-posts permission)
    Route::post('/editor/upload-image', [AdminController::class, 'uploadEditorImage'])
        ->name('editor.upload-image')
        ->middleware('can:create-posts');

    // Categories (accessible with manage-categories permission)
    Route::get('/categories', [AdminController::class, 'categories'])
        ->name('categories')
        ->middleware('can:manage-categories');

    Route::post('/categories', [AdminController::class, 'storeCategory'])
        ->name('categories.store')
        ->middleware('can:manage-categories');

    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])
        ->name('categories.update')
        ->middleware('can:manage-categories');

    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])
        ->name('categories.destroy')
        ->middleware('can:manage-categories');

    Route::get('/categories/export', [AdminController::class, 'exportCategories'])
        ->name('categories.export')
        ->middleware('can:manage-categories');

    // Story Categories (accessible with manage-story-categories permission)
    Route::get('/story-categories', [AdminController::class, 'storyCategories'])
        ->name('storyCategories')
        ->middleware('can:manage-story-categories');

    Route::post('/story-categories', [AdminController::class, 'storeStoryCategory'])
        ->name('storyCategories.store')
        ->middleware('can:manage-story-categories');

    Route::put('/story-categories/{storyCategory}', [AdminController::class, 'updateStoryCategory'])
        ->name('storyCategories.update')
        ->middleware('can:manage-story-categories');

    Route::delete('/story-categories/{storyCategory}', [AdminController::class, 'destroyStoryCategory'])
        ->name('storyCategories.destroy')
        ->middleware('can:manage-story-categories');

    Route::get('/story-categories/export', [AdminController::class, 'exportStoryCategories'])
        ->name('storyCategories.export')
        ->middleware('can:manage-story-categories');

    // Stories (accessible with view-stories / create-stories / edit-stories / delete-stories permission)
    Route::get('/allstory', [AdminController::class, 'allStories'])
        ->name('allStory')
        ->middleware('can:view-stories');

    Route::get('/newstory', [AdminController::class, 'newStory'])
        ->name('newStory')
        ->middleware('can:create-stories');

    Route::post('/stories', [AdminController::class, 'storeStory'])
        ->name('stories.store')
        ->middleware('can:create-stories');

    Route::get('/stories/export', [AdminController::class, 'exportStories'])
        ->name('stories.export')
        ->middleware('can:view-stories');

    Route::delete('/stories/{story}', [AdminController::class, 'destroyStory'])
        ->name('stories.destroy')
        ->middleware('can:delete-stories')
        ->where('story', '[0-9]+');

    Route::get('/stories/{story}', [AdminController::class, 'showStory'])
        ->name('stories.show')
        ->middleware('can:view-stories')
        ->where('story', '[0-9]+');

    Route::get('/stories/{story}/edit', [AdminController::class, 'editStory'])
        ->name('stories.edit')
        ->middleware('can:edit-stories')
        ->where('story', '[0-9]+');

    Route::put('/stories/{story}', [AdminController::class, 'updateStory'])
        ->name('stories.update')
        ->middleware('can:edit-stories')
        ->where('story', '[0-9]+');

    // Printables (accessible with view-printables / create-printables / edit-printables / delete-printables permission)
    Route::get('/allprintable', [AdminController::class, 'allPrintables'])
        ->name('allPrintable')
        ->middleware('can:view-printables');

    Route::get('/newprintable', [AdminController::class, 'newPrintable'])
        ->name('newPrintable')
        ->middleware('can:create-printables');

    Route::post('/printables', [AdminController::class, 'storePrintable'])
        ->name('printables.store')
        ->middleware('can:create-printables');

    Route::delete('/printables/{printable}', [AdminController::class, 'destroyPrintable'])
        ->name('printables.destroy')
        ->middleware('can:delete-printables');

    Route::get('/printables/{printable}', [AdminController::class, 'showPrintable'])
        ->name('printables.show')
        ->middleware('can:view-printables');

    Route::get('/printables/{printable}/edit', [AdminController::class, 'editPrintable'])
        ->name('printables.edit')
        ->middleware('can:edit-printables');

    Route::put('/printables/{printable}', [AdminController::class, 'updatePrintable'])
        ->name('printables.update')
        ->middleware('can:edit-printables');

    Route::get('/printables/export', [AdminController::class, 'exportPrintables'])
        ->name('printables.export')
        ->middleware('can:view-printables');

    Route::post('/printables/upload-file', [AdminController::class, 'uploadPrintableFile'])
        ->name('printables.upload-file')
        ->middleware('can:create-printables');

    // Session Categories (accessible with manage-session-categories permission)
    Route::get('/session-categories', [AdminController::class, 'sessionCategories'])
        ->name('sessionCategories')
        ->middleware('can:manage-session-categories');

    Route::post('/session-categories', [AdminController::class, 'storeSessionCategory'])
        ->name('sessionCategories.store')
        ->middleware('can:manage-session-categories');

    Route::put('/session-categories/{sessionCategory}', [AdminController::class, 'updateSessionCategory'])
        ->name('sessionCategories.update')
        ->middleware('can:manage-session-categories');

    Route::delete('/session-categories/{sessionCategory}', [AdminController::class, 'destroySessionCategory'])
        ->name('sessionCategories.destroy')
        ->middleware('can:manage-session-categories');

    Route::get('/session-categories/export', [AdminController::class, 'exportSessionCategories'])
        ->name('sessionCategories.export')
        ->middleware('can:manage-session-categories');

    // Video Sessions (accessible with view-video-sessions / create-video-sessions / edit-video-sessions / delete-video-sessions permission)
    Route::get('/allvideosession', [AdminController::class, 'allVideoSessions'])
        ->name('allVideoSession')
        ->middleware('can:view-video-sessions');

    Route::get('/newvideosession', [AdminController::class, 'newVideoSession'])
        ->name('newVideoSession')
        ->middleware('can:create-video-sessions');

    Route::post('/video-sessions', [AdminController::class, 'storeVideoSession'])
        ->name('videoSessions.store')
        ->middleware('can:create-video-sessions');

    Route::delete('/video-sessions/{videoSession}', [AdminController::class, 'destroyVideoSession'])
        ->name('videoSessions.destroy')
        ->middleware('can:delete-video-sessions');

    Route::get('/video-sessions/{videoSession}', [AdminController::class, 'showVideoSession'])
        ->name('videoSessions.show')
        ->middleware('can:view-video-sessions');

    Route::get('/video-sessions/{videoSession}/edit', [AdminController::class, 'editVideoSession'])
        ->name('videoSessions.edit')
        ->middleware('can:edit-video-sessions');

    Route::put('/video-sessions/{videoSession}', [AdminController::class, 'updateVideoSession'])
        ->name('videoSessions.update')
        ->middleware('can:edit-video-sessions');

    Route::get('/video-sessions/export', [AdminController::class, 'exportVideoSessions'])
        ->name('videoSessions.export')
        ->middleware('can:view-video-sessions');

    // Authors (accessible with manage-authors permission)
    Route::get('/authors', [AdminController::class, 'authors'])
        ->name('authors.index')
        ->middleware('can:manage-authors');

    Route::post('/authors', [AdminController::class, 'storeAuthor'])
        ->name('authors.store')
        ->middleware('can:manage-authors');

    Route::put('/authors/{author}', [AdminController::class, 'updateAuthor'])
        ->name('authors.update')
        ->middleware('can:manage-authors');

    Route::delete('/authors/{author}', [AdminController::class, 'destroyAuthor'])
        ->name('authors.destroy')
        ->middleware('can:manage-authors');

    Route::get('/authors/export', [AdminController::class, 'exportAuthors'])
        ->name('authors.export')
        ->middleware('can:manage-authors');

    // Media Library (accessible with manage-media permission)
    Route::get('/media-library', [AdminController::class, 'mediaLibrary'])
        ->name('mediaLibrary')
        ->middleware('can:manage-media');

    Route::post('/media-library/upload', [AdminController::class, 'uploadMedia'])
        ->name('media.upload')
        ->middleware('can:manage-media');

    Route::delete('/media-library/delete', [AdminController::class, 'deleteMedia'])
        ->name('media.delete')
        ->middleware('can:manage-media');

    // API endpoints for AJAX Media Modal selection (accessible to authenticated users with view-posts/create-posts)
    Route::get('/api/media', [AdminController::class, 'apiGetMedia'])
        ->name('api.media.index')
        ->middleware('can:view-posts');

    Route::post('/api/media/upload', [AdminController::class, 'apiUploadMedia'])
        ->name('api.media.upload')
        ->middleware('can:create-posts');

    // User Management (accessible with manage-users gate - Super Admin only)
    Route::get('/users', [AdminController::class, 'users'])
        ->name('users.index')
        ->middleware('can:manage-users');

    // Store New User (accessible with manage-users gate - Super Admin only)
    Route::post('/users', [AdminController::class, 'storeUser'])
        ->name('users.store')
        ->middleware('can:manage-users');

    // Update User Profile, Roles, Permissions, and Status (accessible with manage-users gate - Super Admin only)
    Route::post('/users/{user}/permissions', [AdminController::class, 'updateUserPermissions'])
        ->name('users.permissions')
        ->middleware('can:manage-users');

    // Delete User Account (accessible with manage-users gate - Super Admin only)
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])
        ->name('users.destroy')
        ->middleware('can:manage-users');

    // Preview Post
    Route::post('/posts/preview', [HomeController::class, 'previewPost'])
        ->name('posts.preview');

    // Preview Story
    Route::post('/stories/preview', [HomeController::class, 'previewStory'])
        ->name('stories.preview');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [HomeController::class, 'about'])->name('about');
Route::get('/blogs', [HomeController::class, 'index'])->name('blogs.home');
Route::get('/all-blogs', [HomeController::class, 'blogs'])->name('blogs');
Route::get('/stories', [HomeController::class, 'stories'])->name('stories');
Route::get('/printables', [HomeController::class, 'printables'])->name('printables');
Route::get('/sessions', [HomeController::class, 'sessions'])->name('sessions');
Route::get('/stories/{slug}', [HomeController::class, 'storyDetails'])->name('story.show');
Route::get('/author', [HomeController::class, 'author'])->name('author');
Route::get('/author-sana-kapoor', [HomeController::class, 'authorSana'])->name('author.sana');

// Redirects for backward compatibility (articles to blogs)
Route::redirect('/articles', '/blogs', 301);
Route::get('/articles/{slug}', function ($slug) {
    return redirect()->route('blog.show', $slug, 301);
});

// Legacy query-string redirects (backward compatibility)
Route::get('/article-details', function (Request $request) {
    $slug = $request->query('post');

    return $slug ? redirect()->route('blog.show', $slug, 301) : redirect()->route('blogs');
})->name('article.details');
Route::get('/blog-details', function (Request $request) {
    $slug = $request->query('post');

    return $slug ? redirect()->route('blog.show', $slug, 301) : redirect()->route('blogs');
})->name('blog.details');
Route::get('/story-details', function (Request $request) {
    $slug = $request->query('story');

    return $slug ? redirect()->route('story.show', $slug, 301) : redirect()->route('stories');
})->name('story.details');

// Old HTML Compatibility Redirects
Route::get('/index.html', function () {
    return redirect()->route('home');
});
Route::get('/why-we-write.html', function () {
    return redirect()->route('about');
});
Route::get('/all-posts.html', function () {
    return redirect()->route('blogs');
});
Route::get('/all-stories.html', function () {
    return redirect()->route('stories');
});
Route::get('/all-printables.html', function () {
    return redirect()->route('printables');
});
Route::get('/all-sessions.html', function () {
    return redirect()->route('sessions');
});
Route::get('/article-details.html', function () {
    return redirect()->route('article.details');
});
Route::get('/blog-details.html', function () {
    return redirect()->route('blog.details');
});
Route::get('/author-sana-kapoor.html', function () {
    return redirect()->route('author.sana');
});
Route::get('/author.html', function () {
    return redirect()->to(route('author').'?'.http_build_query(request()->query()));
});

require __DIR__.'/auth.php';

Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'application/xml');
    }
    abort(404);
});

Route::get('/blogs-sitemap.xml', function () {
    $path = public_path('blogs-sitemap.xml');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'application/xml');
    }
    abort(404);
});

Route::get('/stories-sitemap.xml', function () {
    $path = public_path('stories-sitemap.xml');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'application/xml');
    }
    abort(404);
});

Route::get('/printables-sitemap.xml', function () {
    $path = public_path('printables-sitemap.xml');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'application/xml');
    }
    abort(404);
});

Route::get('/sessions-sitemap.xml', function () {
    $path = public_path('sessions-sitemap.xml');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'application/xml');
    }
    abort(404);
});

Route::get('/sitemap.xsl', function () {
    $path = public_path('sitemap.xsl');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'text/xsl');
    }
    abort(404);
});

Route::get('/robots.txt', function () {
    $path = public_path('robots.txt');
    if (file_exists($path)) {
        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'text/plain');
    }
    abort(404);
});

Route::get('/{slug}', [HomeController::class, 'blogDetails'])->name('blog.show');
Route::get('/{category_slug}', [HomeController::class, 'blogDetails'])->name('category.show');
