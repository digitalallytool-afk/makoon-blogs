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
    $this->blogsSitemapPath = public_path('blogs-sitemap.xml');
    $this->storiesSitemapPath = public_path('stories-sitemap.xml');
    $this->printablesSitemapPath = public_path('printables-sitemap.xml');
    $this->sessionsSitemapPath = public_path('sessions-sitemap.xml');
    $this->robotsPath = public_path('robots.txt');

    $this->sitemapBackup = file_exists($this->sitemapPath) ? file_get_contents($this->sitemapPath) : null;
    $this->blogsSitemapBackup = file_exists($this->blogsSitemapPath) ? file_get_contents($this->blogsSitemapPath) : null;
    $this->storiesSitemapBackup = file_exists($this->storiesSitemapPath) ? file_get_contents($this->storiesSitemapPath) : null;
    $this->printablesSitemapBackup = file_exists($this->printablesSitemapPath) ? file_get_contents($this->printablesSitemapPath) : null;
    $this->sessionsSitemapBackup = file_exists($this->sessionsSitemapPath) ? file_get_contents($this->sessionsSitemapPath) : null;
    $this->robotsBackup = file_exists($this->robotsPath) ? file_get_contents($this->robotsPath) : null;
});

afterEach(function () {
    // Restore or clean Sitemap Index
    if ($this->sitemapBackup) {
        file_put_contents($this->sitemapPath, $this->sitemapBackup);
    } elseif (file_exists($this->sitemapPath)) {
        unlink($this->sitemapPath);
    }

    // Restore or clean Blogs Sitemap
    if ($this->blogsSitemapBackup) {
        file_put_contents($this->blogsSitemapPath, $this->blogsSitemapBackup);
    } elseif (file_exists($this->blogsSitemapPath)) {
        unlink($this->blogsSitemapPath);
    }

    // Restore or clean Stories Sitemap
    if ($this->storiesSitemapBackup) {
        file_put_contents($this->storiesSitemapPath, $this->storiesSitemapBackup);
    } elseif (file_exists($this->storiesSitemapPath)) {
        unlink($this->storiesSitemapPath);
    }

    // Restore or clean Printables Sitemap
    if ($this->printablesSitemapBackup) {
        file_put_contents($this->printablesSitemapPath, $this->printablesSitemapBackup);
    } elseif (file_exists($this->printablesSitemapPath)) {
        unlink($this->printablesSitemapPath);
    }

    // Restore or clean Sessions Sitemap
    if ($this->sessionsSitemapBackup) {
        file_put_contents($this->sessionsSitemapPath, $this->sessionsSitemapBackup);
    } elseif (file_exists($this->sessionsSitemapPath)) {
        unlink($this->sessionsSitemapPath);
    }

    // Restore or clean Robots
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

    // Verify index is created and references sub-sitemaps
    expect(file_exists($this->sitemapPath))->toBeTrue();
    $indexContent = file_get_contents($this->sitemapPath);
    expect($indexContent)->toContain('blogs-sitemap.xml');
    expect($indexContent)->toContain('stories-sitemap.xml');

    // Verify blog sitemap contains post slug
    expect(file_exists($this->blogsSitemapPath))->toBeTrue();
    $xmlContent = file_get_contents($this->blogsSitemapPath);
    expect($xmlContent)->toContain('test-post-slug');

    // 2. Change to draft
    $post->update(['status' => 'draft']);
    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->blogsSitemapPath);
    expect($xmlContent)->not->toContain('test-post-slug');

    // 3. Change back to published
    $post->update(['status' => 'published']);
    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->blogsSitemapPath);
    expect($xmlContent)->toContain('test-post-slug');

    // 4. Delete the post
    $post->delete();
    app(SitemapService::class)->generate();

    $xmlContent = file_get_contents($this->blogsSitemapPath);
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

    // Verify stories sitemap exists and does not contain draft
    expect(file_exists($this->storiesSitemapPath))->toBeTrue();
    $storiesContent = file_get_contents($this->storiesSitemapPath);
    expect($storiesContent)->not->toContain('draft-story-slug');

    // Verify printables sitemap exists and does not contain draft
    expect(file_exists($this->printablesSitemapPath))->toBeTrue();
    $printablesContent = file_get_contents($this->printablesSitemapPath);
    expect($printablesContent)->not->toContain('draft-printable-slug');
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

    expect(file_exists($this->blogsSitemapPath))->toBeTrue();
    $xmlContent = file_get_contents($this->blogsSitemapPath);

    expect($xmlContent)->toContain('https://makoons.com/blogs/test-post-clean-slug');
    expect($xmlContent)->not->toContain('https://makoons.com/blogs/blogs/test-post-clean-slug');
});
