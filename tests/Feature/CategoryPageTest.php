<?php

use App\Models\Category;
use App\Models\StoryCategory;

test('category page renders post category details and returns 200', function () {
    // Seed post category
    $category = Category::create([
        'name' => 'Parenting Guide Test',
        'slug' => 'parenting-guide-test',
        'description' => 'Test description for parenting guide.',
    ]);

    $response = $this->get('/parenting-guide-test');

    $response->assertSuccessful();
    $response->assertViewIs('frontend.pages.category-details');
    $response->assertSee('Parenting Guide Test');
    $response->assertSee('Test description for parenting guide.');
});

test('category page renders story category details and returns 200', function () {
    // Seed story category
    $storyCategory = StoryCategory::create([
        'name' => 'Classroom Story Test',
        'slug' => 'classroom-story-test',
        'description' => 'Test description for classroom story.',
    ]);

    $response = $this->get('/classroom-story-test');

    $response->assertSuccessful();
    $response->assertViewIs('frontend.pages.category-details');
    $response->assertSee('Classroom Story Test');
    $response->assertSee('Test description for classroom story.');
});

test('category page returns 404 for invalid slug', function () {
    $response = $this->get('/non-existent-category-slug');

    $response->assertNotFound();
});

test('blogs list page returns 200 and no view exception occurs', function () {
    $response = $this->get('/all-blogs');

    $response->assertSuccessful();
    $response->assertViewIs('frontend.pages.all-posts');
});

