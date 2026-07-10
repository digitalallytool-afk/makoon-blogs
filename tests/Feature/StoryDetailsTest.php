<?php

use App\Models\User;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\Author;

test('guest cannot preview a story', function () {
    $response = $this->post('/stories/preview', [
        'title' => 'Guest Story Preview Title',
        'content' => 'Guest content',
    ]);

    $response->assertRedirect('/login');
});

test('authenticated user can preview a story', function () {
    $user = User::factory()->create();

    $storyCategory = StoryCategory::create([
        'name' => 'Story Category Test',
        'slug' => 'story-category-test',
        'description' => 'Test description for story category.',
    ]);

    $author = Author::create([
        'name' => 'Mira Sharma',
        'description' => 'Test description for author.',
    ]);

    $response = $this->actingAs($user)->post('/stories/preview', [
        'title' => 'Test Story Preview Title',
        'content' => 'Test content for preview story.',
        'excerpt' => 'Test excerpt',
        'story_category_id' => $storyCategory->id,
        'author_id' => $author->id,
    ]);

    $response->assertSuccessful();
    $response->assertViewIs('frontend.pages.story-details');
    $response->assertSee('Test Story Preview Title');
    $response->assertSee('Test content for preview story.');
    $response->assertSee('Story Category Test');
    $response->assertSee('Mira Sharma');
    $response->assertSee('Preview Mode: This story is not yet published.');
});

test('can display story details page with categories and trending stories', function () {
    $storyCategory = StoryCategory::create([
        'name' => 'Test Cat',
        'slug' => 'test-cat',
        'description' => 'Desc',
    ]);

    $author = Author::create([
        'name' => 'Mira Sharma',
        'description' => 'Desc',
    ]);

    $story = Story::create([
        'title' => 'Main Story',
        'slug' => 'main-story',
        'content' => 'Main content',
        'excerpt' => 'Main excerpt',
        'status' => 'published',
        'story_category_id' => $storyCategory->id,
        'author_id' => $author->id,
        'view_count' => 10,
    ]);

    $trendingStory = Story::create([
        'title' => 'Trending Story Title',
        'slug' => 'trending-story',
        'content' => 'Trending content',
        'excerpt' => 'Trending excerpt',
        'status' => 'published',
        'story_category_id' => $storyCategory->id,
        'author_id' => $author->id,
        'view_count' => 100, // higher views
    ]);

    $response = $this->get('/stories/main-story');

    $response->assertSuccessful();
    $response->assertViewIs('frontend.pages.story-details');
    $response->assertSee('Main Story');
    $response->assertSee('Trending Story Title');
    $response->assertSee('100 views');
    $response->assertSee('Test Cat');
    $response->assertSee('2 stories');
});
