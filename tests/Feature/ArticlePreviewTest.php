<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Author;

test('guest cannot preview a post', function () {
    $response = $this->post('/posts/preview', [
        'title' => 'Guest Preview Title',
        'content' => 'Guest content',
    ]);

    $response->assertRedirect('/login');
});

test('authenticated user can preview a post', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'Parenting Guide Test',
        'slug' => 'parenting-guide-test',
        'description' => 'Test description for parenting guide.',
    ]);

    $author = Author::create([
        'name' => 'Mira Sharma',
        'description' => 'Test description for author.',
    ]);

    $response = $this->actingAs($user)->post('/posts/preview', [
        'title' => 'Test Post Preview Title',
        'content' => 'Test content for preview post.',
        'excerpt' => 'Test excerpt',
        'category_id' => $category->id,
        'author_id' => $author->id,
    ]);

    $response->assertSuccessful();
    $response->assertViewIs('frontend.pages.blog-details');
    $response->assertSee('Test Post Preview Title');
    $response->assertSee('Test content for preview post.');
    $response->assertSee('Parenting Guide Test');
    $response->assertSee('Mira Sharma');
    $response->assertSee('Preview Mode: This blog post is not yet published.');
});

