<?php

use App\Models\Author;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Run the roles and permissions seeder
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
});

test('authorized user can view authors index page', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($superAdmin)->get(route('authors.index'));
    $response->assertSuccessful();
    $response->assertViewHas('authors');
});

test('unauthorized user cannot view authors index page', function () {
    $user = User::factory()->create(); // standard user with no role

    $response = $this->actingAs($user)->get(route('authors.index'));
    $response->assertForbidden();
});

test('can create an author with name and description and optional profile image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // 1. Create author without image
    $response = $this->actingAs($superAdmin)->post(route('authors.store'), [
        'name' => 'John Doe',
        'description' => 'A famous tech blogger.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('authors', [
        'name' => 'John Doe',
        'image' => null,
        'description' => 'A famous tech blogger.',
    ]);

    // 2. Create author with uploaded image
    $imageFile = UploadedFile::fake()->image('author_avatar.jpg');

    $response2 = $this->actingAs($superAdmin)->post(route('authors.store'), [
        'name' => 'Jane Doe',
        'image' => $imageFile,
        'description' => 'Another blogger.',
    ]);

    $response2->assertRedirect();
    $response2->assertSessionHas('success');

    $author = Author::where('name', 'Jane Doe')->first();
    expect($author)->not->toBeNull();
    expect($author->image)->not->toBeNull();
    
    // Verify the image file exists on the physical disk
    $path = public_path($author->image);
    expect(file_exists($path))->toBeTrue();

    // Clean up file
    @unlink($path);
});

test('can update an author and replace their profile image', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Setup an author with an initial image
    $initialImage = UploadedFile::fake()->image('old_avatar.jpg');
    $initialFilename = time() . '_old_avatar.jpg';
    $initialImage->move(public_path('uploads/authors'), $initialFilename);
    $initialImagePath = 'uploads/authors/' . $initialFilename;

    $author = Author::create([
        'name' => 'Original Name',
        'image' => $initialImagePath,
        'description' => 'Original desc.',
    ]);

    // Verify setup
    expect(file_exists(public_path($author->image)))->toBeTrue();

    // Update with a new name and a new image file
    $newImage = UploadedFile::fake()->image('new_avatar.jpg');

    $response = $this->actingAs($superAdmin)->put(route('authors.update', $author->id), [
        'name' => 'Updated Name',
        'image' => $newImage,
        'description' => 'Updated desc.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $author = $author->fresh();
    expect($author->name)->toEqual('Updated Name');
    expect($author->description)->toEqual('Updated desc.');
    expect($author->image)->not->toEqual($initialImagePath); // path must have changed
    
    // Old file must be physically deleted
    expect(file_exists(public_path($initialImagePath)))->toBeFalse();
    // New file must physically exist
    expect(file_exists(public_path($author->image)))->toBeTrue();

    // Clean up
    @unlink(public_path($author->image));
});

test('can delete an author and delete their image file from disk', function () {
    $superAdmin = User::where('email', 'admin@example.com')->first();

    // Setup an author with an image
    $imageFile = UploadedFile::fake()->image('to_delete.jpg');
    $filename = time() . '_to_delete.jpg';
    $imageFile->move(public_path('uploads/authors'), $filename);
    $imagePath = 'uploads/authors/' . $filename;

    $author = Author::create([
        'name' => 'Author to Delete',
        'image' => $imagePath,
        'description' => 'Going to be deleted.',
    ]);

    expect(file_exists(public_path($author->image)))->toBeTrue();

    // Delete the author
    $response = $this->actingAs($superAdmin)->delete(route('authors.destroy', $author->id));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Database record is gone
    $this->assertDatabaseMissing('authors', ['id' => $author->id]);
    // Physical file is deleted
    expect(file_exists(public_path($imagePath)))->toBeFalse();
});
