<?php

use App\Models\Author;
use App\Models\StoryCategory;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    // Run the roles and permissions seeder
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('authorized user can view add new story page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->get(route('newStory'));
    $response->assertSuccessful();
    $response->assertViewHasAll(['categories', 'authors']);
});

test('unauthorized user cannot view add new story page', function () {
    $user = User::factory()->create(); // standard user with no role

    $response = $this->actingAs($user)->get(route('newStory'));
    $response->assertForbidden();
});

test('can create a story with title, content, excerpt, category, author, and optional featured image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create a mock StoryCategory and Author first
    $category = StoryCategory::create([
        'name' => 'Adventures',
        'slug' => 'adventures',
    ]);

    $author = Author::create([
        'name' => 'Jack Storyteller',
    ]);

    // Store a story
    $featuredImage = UploadedFile::fake()->image('story_cover.png');

    $response = $this->actingAs($superAdmin)->post(route('stories.store'), [
        'title'              => 'My First Adventure Story',
        'content'            => 'This is the detailed body of my first adventure story.',
        'excerpt'            => 'A short summary.',
        'status'             => 'published',
        'story_category_id'  => $category->id,
        'author_id'          => $author->id,
        'featured_image'     => $featuredImage,
    ]);

    $response->assertRedirect(route('allStory'));
    $response->assertSessionHas('success');

    $story = Story::where('title', 'My First Adventure Story')->first();
    expect($story)->not->toBeNull();
    expect($story->slug)->toEqual('my-first-adventure-story');
    expect($story->story_category_id)->toEqual($category->id);
    expect($story->author_id)->toEqual($author->id);
    expect($story->view_count)->toEqual(0);
    expect($story->featured_image)->not->toBeNull();

    // Check file exists on physical disk
    $path = public_path($story->featured_image);
    expect(file_exists($path))->toBeTrue();

    // Clean up file
    @unlink($path);
});

test('can create a story and generate unique slug if title collision occurs', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create category and author
    $category = StoryCategory::create(['name' => 'Tales', 'slug' => 'tales']);
    $author = Author::create(['name' => 'John Teller']);

    // Create first story
    $story1 = Story::create([
        'title' => 'Duplicate Tale',
        'slug' => 'duplicate-tale',
        'content' => 'Content 1',
        'story_category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    // Store a second story via endpoint with same title
    $response = $this->actingAs($superAdmin)->post(route('stories.store'), [
        'title'              => 'Duplicate Tale',
        'content'            => 'Content 2',
        'status'             => 'published',
        'story_category_id'  => $category->id,
        'author_id'          => $author->id,
    ]);

    $response->assertRedirect(route('allStory'));

    // Verify it was saved with a unique slug
    $story2 = Story::where('content', 'Content 2')->first();
    expect($story2)->not->toBeNull();
    expect($story2->slug)->toEqual('duplicate-tale-1');
});

test('validation errors occur if required fields are missing', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Submit missing fields
    $response = $this->actingAs($superAdmin)->post(route('stories.store'), [
        'title' => '',
        'content' => '',
        'story_category_id' => 9999, // non-existent
        'author_id' => 9999,   // non-existent
    ]);

    $response->assertSessionHasErrors(['title', 'content', 'status', 'story_category_id', 'author_id']);
});

test('authorized user can view story detail page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $category = StoryCategory::create(['name' => 'Fables', 'slug' => 'fables']);
    $author = Author::create(['name' => 'Aesop']);
    $story = Story::create([
        'title' => 'The Tortoise and the Hare',
        'slug' => 'the-tortoise-and-the-hare',
        'content' => 'Moral story details.',
        'story_category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->get(route('stories.show', $story->id));
    $response->assertSuccessful();
    $response->assertViewIs('backend.pages.view_story');
    $response->assertViewHas('story');
});

test('unauthorized user cannot view story detail page', function () {
    $user = User::factory()->create();
    $category = StoryCategory::create(['name' => 'Fables', 'slug' => 'fables']);
    $author = Author::create(['name' => 'Aesop']);
    $story = Story::create([
        'title' => 'The Tortoise and the Hare',
        'slug' => 'the-tortoise-and-the-hare',
        'content' => 'Moral story details.',
        'story_category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($user)->get(route('stories.show', $story->id));
    $response->assertForbidden();
});

test('authorized user can view edit story page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $category = StoryCategory::create(['name' => 'Fables', 'slug' => 'fables']);
    $author = Author::create(['name' => 'Aesop']);
    $story = Story::create([
        'title' => 'The Tortoise and the Hare',
        'slug' => 'the-tortoise-and-the-hare',
        'content' => 'Moral story details.',
        'story_category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->get(route('stories.edit', $story->id));
    $response->assertSuccessful();
    $response->assertViewIs('backend.pages.edit_story');
    $response->assertViewHasAll(['story', 'categories', 'authors']);
});

test('authorized user can update a story', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();
    $category = StoryCategory::create(['name' => 'Fables', 'slug' => 'fables']);
    $author = Author::create(['name' => 'Aesop']);
    $story = Story::create([
        'title' => 'The Tortoise and the Hare',
        'slug' => 'the-tortoise-and-the-hare',
        'content' => 'Moral story details.',
        'story_category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response = $this->actingAs($superAdmin)->put(route('stories.update', $story->id), [
        'title'              => 'The Tortoise and the Hare Updated',
        'content'            => 'Updated moral content.',
        'status'             => 'published',
        'story_category_id'  => $category->id,
        'author_id'          => $author->id,
    ]);

    $response->assertRedirect(route('allStory'));
    $response->assertSessionHas('success');

    $story->refresh();
    expect($story->title)->toEqual('The Tortoise and the Hare Updated');
    expect($story->slug)->toEqual('the-tortoise-and-the-hare-updated');
});

test('deleting a story automatically unlinks embedded editor images from disk', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // 1. Upload an image to generate local file
    $image = UploadedFile::fake()->image('temp_editor_story_img.jpg');
    $uploadResponse = $this->actingAs($superAdmin)->post(route('editor.upload-image'), [
        'image' => $image,
    ]);
    $uploadResponse->assertSuccessful();
    $url = $uploadResponse->json()['url'];
    $path = parse_url($url, PHP_URL_PATH);
    $localPath = public_path(ltrim($path, '/'));
    expect(file_exists($localPath))->toBeTrue();

    // 2. Create the story referencing this image inside content
    $category = StoryCategory::create(['name' => 'Fables', 'slug' => 'fables']);
    $author = Author::create(['name' => 'Aesop']);
    $story = Story::create([
        'title'             => 'Story with image',
        'slug'              => 'story-with-image',
        'content'           => '<p>Here is an image: <img src="' . $url . '"></p>',
        'story_category_id' => $category->id,
        'author_id'         => $author->id,
    ]);

    // 3. Delete the story
    $deleteResponse = $this->actingAs($superAdmin)->delete(route('stories.destroy', $story->id));
    $deleteResponse->assertRedirect();

    // 4. Verify story is deleted and the local file is unlinked
    expect(Story::find($story->id))->toBeNull();
    expect(file_exists($localPath))->toBeFalse();
});
