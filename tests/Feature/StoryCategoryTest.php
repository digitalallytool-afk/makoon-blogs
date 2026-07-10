<?php

use App\Models\StoryCategory;
use App\Models\User;

beforeEach(function () {
    // Run the roles and permissions seeder
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('authorized user can view story categories page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->get(route('storyCategories'));
    $response->assertSuccessful();
    $response->assertViewHas('categories');
});

test('unauthorized user cannot view story categories page', function () {
    $user = User::factory()->create(); // a standard user with no roles/permissions

    $response = $this->actingAs($user)->get(route('storyCategories'));
    $response->assertForbidden();
});

test('can create a story category with auto-generated unique slug', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // 1. Create a story category
    $response = $this->actingAs($superAdmin)->post(route('storyCategories.store'), [
        'name' => 'Tech Stories',
        'description' => 'Stories about tech.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('story_categories', [
        'name' => 'Tech Stories',
        'slug' => 'tech-stories',
        'description' => 'Stories about tech.',
    ]);

    // 2. Create another story category with the same name to verify unique slug generation
    $response2 = $this->actingAs($superAdmin)->post(route('storyCategories.store'), [
        'name' => 'Tech Stories',
        'description' => 'Another description.',
    ]);

    $response2->assertRedirect();
    $this->assertDatabaseHas('story_categories', [
        'name' => 'Tech Stories',
        'slug' => 'tech-stories-1',
        'description' => 'Another description.',
    ]);
});

test('can update a story category and ensure slug uniqueness', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category1 = StoryCategory::create([
        'name' => 'Design Stories',
        'slug' => 'design-stories',
    ]);

    $category2 = StoryCategory::create([
        'name' => 'Refactoring Stories',
        'slug' => 'refactoring-stories',
    ]);

    // Update category2 to have the same name as category1 (checking unique slug generation excluding self)
    $response = $this->actingAs($superAdmin)->put(route('storyCategories.update', $category2->id), [
        'name' => 'Design Stories',
        'description' => 'Updated desc.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $category2 = $category2->fresh();
    expect($category2->name)->toEqual('Design Stories');
    expect($category2->slug)->toEqual('design-stories-1'); // should receive suffix
});

test('can delete a story category', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = StoryCategory::create([
        'name' => 'Trash Category',
        'slug' => 'trash-category',
    ]);

    // Delete story category
    $response = $this->actingAs($superAdmin)->delete(route('storyCategories.destroy', $category->id));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('story_categories', ['id' => $category->id]);
});
