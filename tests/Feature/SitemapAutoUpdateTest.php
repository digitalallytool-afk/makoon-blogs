<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Models\Printable;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\User;
use App\Services\SitemapService;

beforeEach(function () {
    $this->sitemapPath = public_path('sitemap.xml');
    $this->robotsPath = public_path('robots.txt');

    $this->sitemapBackup = file_exists($this->sitemapPath) ? file_get_contents($this->sitemapPath) : null;
    $this->robotsBackup = file_exists($this->robotsPath) ? file_get_contents($this->robotsPath) : null;
});

afterEach(function () {
    if ($this->sitemapBackup) {
        file_put_contents($this->sitemapPath, $this->sitemapBackup);
    } elseif (file_exists($this->sitemapPath)) {
        unlink($this->sitemapPath);
    }

    if ($this->robotsBackup) {
        file_put_contents($this->robotsPath, $this->robotsBackup);
    } elseif (file_exists($this->robotsPath)) {
        unlink($this->robotsPath);
    }
});

test('sitemap updates when models are stored, updated or deleted', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
    $author = Author::create(['name' => 'Author', 'description' => 'Desc']);

    // 1. Create a published post
    $post = Post::create([
        'title' => 'Test Post Title',
        'slug' => 'test-post-slug',
        'content' => 'Content',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    app(SitemapService::class)->generate();

    expect(file_exists($this->sitemapPath))->toBeTrue();
    $xmlContent = file_get_contents($this->sitemapPath);
    expect($xmlContent)->toContain('test-post-slug');

    // 2. Change to draft
    $post->update(['status' => 'draft']);
    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->sitemapPath);
    expect($xmlContent)->not->toContain('test-post-slug');

    // 3. Change back to published
    $post->update(['status' => 'published']);
    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->sitemapPath);
    expect($xmlContent)->toContain('test-post-slug');

    // 4. Delete the post
    $post->delete();
    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->sitemapPath);
    expect($xmlContent)->not->toContain('test-post-slug');
});

test('sitemap excludes draft stories and printables', function () {
    $storyCategory = StoryCategory::create(['name' => 'StoryCat', 'slug' => 'storycat']);
    $author = Author::create(['name' => 'Author', 'description' => 'Desc']);

    // Create draft story
    $story = Story::create([
        'title' => 'Draft Story',
        'slug' => 'draft-story-slug',
        'content' => 'Content',
        'status' => 'draft',
        'story_category_id' => $storyCategory->id,
        'author_id' => $author->id,
    ]);

    // Create draft printable
    $printable = Printable::create([
        'name' => 'Draft Printable',
        'slug' => 'draft-printable-slug',
        'file_path' => 'draft_path.pdf',
        'status' => 'draft',
    ]);

    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->sitemapPath);
    expect($xmlContent)->not->toContain('draft-story-slug');
    expect($xmlContent)->not->toContain('draft-printable-slug');
});

test('sitemap cleans duplicate blogs segment in canonical url', function () {
    $category = Category::create(['name' => 'Cat', 'slug' => 'cat']);
    $author = Author::create(['name' => 'Author', 'description' => 'Desc']);

    $post = Post::create([
        'title' => 'Test Post Clean',
        'slug' => 'test-post-clean-slug',
        'content' => 'Content',
        'status' => 'published',
        'category_id' => $category->id,
        'author_id' => $author->id,
        'canonical_url' => 'https://makoons.com/blogs/blogs/test-post-clean-slug',
    ]);

    app(SitemapService::class)->generate();

    expect(file_exists($this->sitemapPath))->toBeTrue();
    $xmlContent = file_get_contents($this->sitemapPath);

    expect($xmlContent)->toContain('https://makoons.com/blogs/test-post-clean-slug');
    expect($xmlContent)->not->toContain('https://makoons.com/blogs/blogs/test-post-clean-slug');
});
