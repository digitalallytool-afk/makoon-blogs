<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    // Run the roles and permissions seeder
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('authorized user can view add new article page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->get(route('newPost'));
    $response->assertSuccessful();
    $response->assertViewHasAll(['categories', 'authors']);
});

test('unauthorized user cannot view add new article page', function () {
    $user = User::factory()->create(); // standard user with no role

    $response = $this->actingAs($user)->get(route('newPost'));
    $response->assertForbidden();
});

test('can create a post with title, content, excerpt, category, author, and optional featured image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create a mock Category and Author first
    $category = Category::create([
        'name' => 'Technology',
        'slug' => 'technology',
    ]);

    $author = Author::create([
        'name' => 'Jane Writer',
    ]);

    // Store a post
    $featuredImage = UploadedFile::fake()->image('post_cover.png');

    $response = $this->actingAs($superAdmin)->post(route('posts.store'), [
        'title' => 'My First Article',
        'content' => 'This is the detailed body of my first article.',
        'excerpt' => 'An excerpt summary.',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
        'featured_image' => $featuredImage,
        'is_trending' => 1,
    ]);

    $response->assertRedirect(route('allPost'));
    $response->assertSessionHas('success');

    $post = Post::where('title', 'My First Article')->first();
    expect($post)->not->toBeNull();
    expect($post->slug)->toEqual('my-first-article');
    expect($post->category_id)->toEqual($category->id);
    expect($post->author_id)->toEqual($author->id);
    expect($post->view_count)->toEqual(0);
    expect($post->featured_image)->not->toBeNull();
    expect($post->is_trending)->toBeTrue();

    // Check file exists on physical disk
    $path = public_path($post->featured_image);
    expect(file_exists($path))->toBeTrue();

    // Clean up file
    @unlink($path);
});

test('can create a post and generate unique slug if title collision occurs', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create category and author
    $category = Category::create(['name' => 'Technology', 'slug' => 'technology']);
    $author = Author::create(['name' => 'Jane Writer']);

    // Create first post
    $post1 = Post::create([
        'title' => 'Duplicate Title',
        'slug' => 'duplicate-title',
        'content' => 'Content 1',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    // Store a second post via endpoint with same title
    $response = $this->actingAs($superAdmin)->post(route('posts.store'), [
        'title' => 'Duplicate Title',
        'content' => 'Content 2',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response->assertRedirect(route('allPost'));

    // Verify it was saved with a unique slug
    $post2 = Post::where('content', 'Content 2')->first();
    expect($post2)->not->toBeNull();
    expect($post2->slug)->toEqual('duplicate-title-1');
});

test('validation errors occur if required fields are missing', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Submit missing fields
    $response = $this->actingAs($superAdmin)->post(route('posts.store'), [
        'title' => '',
        'content' => '',
        'category_id' => 9999, // non-existent
        'author_id' => 9999,   // non-existent
    ]);

    $response->assertSessionHasErrors(['title', 'content', 'status', 'category_id', 'author_id']);
});

test('authorized user can view article detail page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
    $author = Author::create(['name' => 'John']);
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Test Content',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->get(route('posts.show', $post->id));
    $response->assertSuccessful();
    $response->assertViewIs('backend.pages.view_post');
    $response->assertViewHas('post');
});

test('unauthorized user cannot view article detail page', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
    $author = Author::create(['name' => 'John']);
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Test Content',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($user)->get(route('posts.show', $post->id));
    $response->assertForbidden();
});

test('authorized user can view edit article page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
    $author = Author::create(['name' => 'John']);
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Test Content',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->get(route('posts.edit', $post->id));
    $response->assertSuccessful();
    $response->assertViewIs('backend.pages.edit_post');
    $response->assertViewHasAll(['post', 'categories', 'authors']);
});

test('authorized user can update an article', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
    $author = Author::create(['name' => 'John']);
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Test Content',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->put(route('posts.update', $post->id), [
        'title' => 'Updated Post Title',
        'content' => 'Updated Content',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
        'is_trending' => 1,
    ]);

    $response->assertRedirect(route('allPost'));
    $response->assertSessionHas('success');

    $post->refresh();
    expect($post->title)->toEqual('Updated Post Title');
    expect($post->slug)->toEqual('updated-post-title');
    expect($post->is_trending)->toBeTrue();
});

test('authorized user can upload editor image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $image = UploadedFile::fake()->image('editor_image.jpg');

    $response = $this->actingAs($superAdmin)->post(route('editor.upload-image'), [
        'image' => $image,
    ]);

    $response->assertSuccessful();
    $data = $response->json();
    expect($data)->toHaveKey('url');

    // Clean up
    $path = parse_url($data['url'], PHP_URL_PATH);
    $localPath = public_path(ltrim($path, '/'));
    expect(file_exists($localPath))->toBeTrue();
    @unlink($localPath);
});

test('unauthorized user cannot upload editor image', function () {
    $user = User::factory()->create();
    $image = UploadedFile::fake()->image('editor_image.jpg');

    $response = $this->actingAs($user)->post(route('editor.upload-image'), [
        'image' => $image,
    ]);

    $response->assertForbidden();
});

test('deleting a post automatically unlinks embedded editor images from disk', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // 1. Upload an image to generate local file
    $image = UploadedFile::fake()->image('temp_editor_img.jpg');
    $uploadResponse = $this->actingAs($superAdmin)->post(route('editor.upload-image'), [
        'image' => $image,
    ]);
    $uploadResponse->assertSuccessful();
    $url = $uploadResponse->json()['url'];
    $path = parse_url($url, PHP_URL_PATH);
    $localPath = public_path(ltrim($path, '/'));
    expect(file_exists($localPath))->toBeTrue();

    // 2. Create the post referencing this image inside content
    $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
    $author = Author::create(['name' => 'John']);
    $post = Post::create([
        'title' => 'Post with image',
        'slug' => 'post-with-image',
        'content' => '<p>Here is an image: <img src="'.$url.'"></p>',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    // 3. Delete the post
    $deleteResponse = $this->actingAs($superAdmin)->delete(route('posts.destroy', $post->id));
    $deleteResponse->assertRedirect();

    // 4. Verify post is deleted and the local file is unlinked
    expect(Post::find($post->id))->toBeNull();
    expect(file_exists($localPath))->toBeFalse();
});

test('authorized user can list media library files via api', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create a temporary file in uploads/posts to make sure list is not empty
    $dir = public_path('uploads/posts');
    if (! file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    $tempFile = $dir.'/temp_list_test.jpg';
    file_put_contents($tempFile, 'fake data');

    $response = $this->actingAs($superAdmin)->get(route('api.media.index'));
    $response->assertSuccessful();

    $data = $response->json();
    expect(count($data))->toBeGreaterThan(0);

    // Assert structure contains path, name, etc.
    $found = false;
    foreach ($data as $item) {
        if ($item['name'] === 'temp_list_test.jpg') {
            $found = true;
            expect($item)->toHaveKeys(['name', 'url', 'path', 'size', 'type']);
            expect($item['path'])->toBe('uploads/posts/temp_list_test.jpg');
        }
    }
    expect($found)->toBeTrue();

    // Clean up
    @unlink($tempFile);
});

test('authorized user can upload media via api', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $image = UploadedFile::fake()->image('api_upload_test.jpg');

    $response = $this->actingAs($superAdmin)->post(route('api.media.upload'), [
        'image' => $image,
    ]);

    $response->assertSuccessful();
    $data = $response->json();
    expect($data)->toHaveKeys(['url', 'path', 'name', 'size', 'type']);
    expect($data['type'])->toBe('image');

    // Clean up file
    $localPath = public_path($data['path']);
    expect(file_exists($localPath))->toBeTrue();
    @unlink($localPath);
});

test('authorized user can upload media via web route', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $image = UploadedFile::fake()->image('web_upload_test.jpg');

    $response = $this->actingAs($superAdmin)->post(route('media.upload'), [
        'files' => [$image],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Find the uploaded file in public/uploads/posts
    $postsDir = public_path('uploads/posts');
    $files = scandir($postsDir);
    $foundFile = null;
    foreach ($files as $file) {
        if (str_contains($file, 'web_upload_test.jpg')) {
            $foundFile = $postsDir.'/'.$file;
            break;
        }
    }

    expect($foundFile)->not->toBeNull();
    expect(file_exists($foundFile))->toBeTrue();
    @unlink($foundFile);
});

test('authorized user can delete media via web route', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create a temporary file to delete
    $dir = public_path('uploads/posts');
    if (! file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    $tempFile = $dir.'/temp_delete_test.jpg';
    file_put_contents($tempFile, 'fake data');
    expect(file_exists($tempFile))->toBeTrue();

    $response = $this->actingAs($superAdmin)->delete(route('media.delete'), [
        'path' => 'uploads/posts/temp_delete_test.jpg',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect(file_exists($tempFile))->toBeFalse();
});

test('can create a post using a pre-existing media library URL for the featured image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = Category::create([
        'name' => 'Tech Selection',
        'slug' => 'tech-selection',
    ]);

    $author = Author::create([
        'name' => 'Jane Selection',
    ]);

    // Reference a pre-existing/uploaded image url
    $existingPath = 'uploads/posts/pre_existing_cover.jpg';
    $fullPath = public_path($existingPath);
    if (! file_exists(dirname($fullPath))) {
        mkdir(dirname($fullPath), 0755, true);
    }
    file_put_contents($fullPath, 'existing file data');
    expect(file_exists($fullPath))->toBeTrue();

    $response = $this->actingAs($superAdmin)->post(route('posts.store'), [
        'title' => 'My Media Library Cover Article',
        'content' => 'Post using media library cover.',
        'excerpt' => 'An excerpt.',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
        'featured_image_url' => $existingPath, // selected from media library
    ]);

    $response->assertRedirect(route('allPost'));
    $response->assertSessionHas('success');

    $post = Post::where('title', 'My Media Library Cover Article')->first();
    expect($post)->not->toBeNull();
    expect($post->featured_image)->toBe($existingPath);

    // Keep the file, clean it up after testing
    expect(file_exists($fullPath))->toBeTrue();
    @unlink($fullPath);
});

test('can update a post using a pre-existing media library URL for the featured image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = Category::create([
        'name' => 'Tech Selection Update',
        'slug' => 'tech-selection-update',
    ]);

    $author = Author::create([
        'name' => 'Jane Selection Update',
    ]);

    $post = Post::create([
        'title' => 'Article to Update',
        'slug' => 'article-to-update',
        'content' => 'Initial content',
        'category_id' => $category->id,
        'author_id' => $author->id,
        'featured_image' => 'uploads/posts/old_image.jpg',
    ]);

    // Pre-exist the new library file
    $newPath = 'uploads/posts/new_existing_cover.jpg';
    $fullPath = public_path($newPath);
    if (! file_exists(dirname($fullPath))) {
        mkdir(dirname($fullPath), 0755, true);
    }
    file_put_contents($fullPath, 'new file data');

    $response = $this->actingAs($superAdmin)->put(route('posts.update', $post->id), [
        'title' => 'Article to Update Updated',
        'content' => 'Updated content',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
        'featured_image_url' => $newPath, // updated via media library url
    ]);

    $response->assertRedirect(route('allPost'));
    $response->assertSessionHas('success');

    $post->refresh();
    expect($post->title)->toBe('Article to Update Updated');
    expect($post->featured_image)->toBe($newPath);

    // Clean up
    @unlink($fullPath);
});

test('can create a post with a custom slug', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = Category::create([
        'name' => 'Custom Slug Category',
        'slug' => 'custom-slug-category',
    ]);

    $author = Author::create([
        'name' => 'Custom Slug Author',
    ]);

    $response = $this->actingAs($superAdmin)->post(route('posts.store'), [
        'title' => 'My Article Title',
        'slug' => 'this-is-a-completely-custom-slug',
        'content' => 'Content here',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response->assertRedirect(route('allPost'));
    $post = Post::where('title', 'My Article Title')->first();
    expect($post)->not->toBeNull();
    expect($post->slug)->toBe('this-is-a-completely-custom-slug');
});

test('can update a post with a custom slug', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = Category::create([
        'name' => 'Custom Slug Category 2',
        'slug' => 'custom-slug-category-2',
    ]);

    $author = Author::create([
        'name' => 'Custom Slug Author 2',
    ]);

    $post = Post::create([
        'title' => 'Post Title',
        'slug' => 'original-slug',
        'content' => 'Initial content',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->put(route('posts.update', $post->id), [
        'title' => 'Post Title',
        'slug' => 'new-custom-slug',
        'content' => 'Updated content',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response->assertRedirect(route('allPost'));
    $post->refresh();
    expect($post->slug)->toBe('new-custom-slug');
});

test('will auto-update post slug on title change if no custom slug provided', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = Category::create([
        'name' => 'Custom Slug Category 3',
        'slug' => 'custom-slug-category-3',
    ]);

    $author = Author::create([
        'name' => 'Custom Slug Author 3',
    ]);

    $post = Post::create([
        'title' => 'Original Title',
        'slug' => 'original-title',
        'content' => 'Initial content',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->put(route('posts.update', $post->id), [
        'title' => 'Completely New Title',
        'content' => 'Updated content',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response->assertRedirect(route('allPost'));
    $post->refresh();
    expect($post->slug)->toBe('completely-new-title');
});
