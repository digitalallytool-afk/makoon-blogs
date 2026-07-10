<?php

use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

beforeEach(function () {
    // Run the roles and permissions seeder
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('authorized user can view categories page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->get(route('categories'));
    $response->assertSuccessful();
    $response->assertViewHasAll(['categories', 'subcategories']);
});

test('unauthorized user cannot view categories page', function () {
    $user = User::factory()->create(); // a standard user with no roles/permissions

    $response = $this->actingAs($user)->get(route('categories'));
    $response->assertForbidden();
});

test('can create a main category with auto-generated unique slug', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // 1. Create a category
    $response = $this->actingAs($superAdmin)->post(route('categories.store'), [
        'name' => 'Web Development',
        'description' => 'All about programming webs.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'Web Development',
        'slug' => 'web-development',
        'description' => 'All about programming webs.',
        'parent_id' => null,
    ]);

    // 2. Create another category with the same name to verify unique slug generation
    $response2 = $this->actingAs($superAdmin)->post(route('categories.store'), [
        'name' => 'Web Development',
        'description' => 'Another description.',
    ]);

    $response2->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'name' => 'Web Development',
        'slug' => 'web-development-1',
        'description' => 'Another description.',
        'parent_id' => null,
    ]);
});

test('can create a subcategory associated with parent category', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Create parent category first
    $parent = Category::create([
        'name' => 'Backend Frameworks',
        'slug' => 'backend-frameworks',
    ]);

    // Create subcategory
    $response = $this->actingAs($superAdmin)->post(route('categories.store'), [
        'name' => 'Laravel',
        'description' => 'The PHP framework.',
        'parent_id' => $parent->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'Laravel',
        'slug' => 'laravel',
        'description' => 'The PHP framework.',
        'parent_id' => $parent->id,
    ]);

    // Verify relations
    $sub = Category::where('slug', 'laravel')->first();
    expect($sub->parent->id)->toEqual($parent->id);
    expect($parent->children->first()->id)->toEqual($sub->id);
});

test('can update a category and ensure slug uniqueness', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category1 = Category::create([
        'name' => 'Design Patterns',
        'slug' => 'design-patterns',
    ]);

    $category2 = Category::create([
        'name' => 'Refactoring',
        'slug' => 'refactoring',
    ]);

    // Update category2 to have the same name as category1 (checking unique slug generation excluding self)
    $response = $this->actingAs($superAdmin)->put(route('categories.update', $category2->id), [
        'name' => 'Design Patterns',
        'description' => 'Updated desc.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $category2 = $category2->fresh();
    expect($category2->name)->toEqual('Design Patterns');
    expect($category2->slug)->toEqual('design-patterns-1'); // should receive suffix since design-patterns belongs to category1
});

test('cannot set a category as its own parent', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $category = Category::create([
        'name' => 'Self Loop',
        'slug' => 'self-loop',
    ]);

    $response = $this->actingAs($superAdmin)->put(route('categories.update', $category->id), [
        'name' => 'Self Loop',
        'parent_id' => $category->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
    
    $category = $category->fresh();
    expect($category->parent_id)->toBeNull();
});

test('can delete a category and verify cascade delete of subcategories', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $parent = Category::create([
        'name' => 'Frontend',
        'slug' => 'frontend',
    ]);

    $child = Category::create([
        'name' => 'Vue.js',
        'slug' => 'vue-js',
        'parent_id' => $parent->id,
    ]);

    // Delete parent category
    $response = $this->actingAs($superAdmin)->delete(route('categories.destroy', $parent->id));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('categories', ['id' => $parent->id]);
    $this->assertDatabaseMissing('categories', ['id' => $child->id]); // should be deleted by cascade foreign key
});
