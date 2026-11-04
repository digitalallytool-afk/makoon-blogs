<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\User;
use App\Services\SitemapService;
use Carbon\Carbon;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    $this->admin = User::where('email', 'admin@example.com')->first();
    $this->category = Category::create(['name' => 'Parenting', 'slug' => 'parenting']);
    $this->storyCategory = StoryCategory::create(['name' => 'Tales', 'slug' => 'tales']);
    $this->author = Author::create(['name' => 'Jane Writer']);
});

function postPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Scheduled Post',
        'content' => 'Body',
        'status' => 'published',
        'category_id' => test()->category->id,
        'author_id' => test()->author->id,
    ], $overrides);
}

function storyPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Scheduled Story',
        'content' => 'Body',
        'status' => 'published',
        'story_category_id' => test()->storyCategory->id,
        'author_id' => test()->author->id,
    ], $overrides);
}

test('a post scheduled in IST is stored in UTC and hidden until it is due', function () {
    $istInput = now('Asia/Kolkata')->addDay()->setTime(10, 30)->format('Y-m-d\TH:i');

    $this->actingAs($this->admin)
        ->post(route('posts.store'), postPayload(['published_at' => $istInput]))
        ->assertRedirect(route('allPost'))
        ->assertSessionHas('success', fn ($m) => str_contains($m, 'scheduled') && str_contains($m, 'IST'));

    $post = Post::firstWhere('title', 'Scheduled Post');
    $expectedUtc = Carbon::parse($istInput, 'Asia/Kolkata')->utc();

    expect($post->isScheduled())->toBeTrue()
        ->and($post->published_at->equalTo($expectedUtc))->toBeTrue()
        ->and($post->created_at->equalTo($expectedUtc))->toBeTrue()
        ->and(Post::published()->count())->toBe(0)
        ->and(Post::scheduled()->count())->toBe(1);

    $this->get(route('blog.show', $post->slug))->assertNotFound();
    expect($post->scheduledAtForInput())->toBe($istInput);
});

test('a scheduled post goes live by itself once its time passes', function () {
    $post = Post::create(postPayload(['slug' => 'later']));
    $post->applySchedule(now()->addHour());
    $post->save();

    expect(Post::published()->count())->toBe(0);

    $this->travel(2)->hours();

    expect(Post::published()->count())->toBe(1)
        ->and($post->fresh()->isPublished())->toBeTrue();
    $this->get(route('blog.show', 'later'))->assertOk();
});

test('posts without a schedule publish immediately as before', function () {
    $this->actingAs($this->admin)->post(route('posts.store'), postPayload());

    $post = Post::firstWhere('title', 'Scheduled Post');
    expect($post->published_at)->toBeNull()
        ->and(Post::published()->count())->toBe(1);
});

test('drafts and past dates ignore the schedule', function () {
    $this->actingAs($this->admin)->post(route('posts.store'), postPayload([
        'title' => 'Draft',
        'status' => 'draft',
        'published_at' => now('Asia/Kolkata')->addDay()->format('Y-m-d\TH:i'),
    ]));
    $this->actingAs($this->admin)->post(route('posts.store'), postPayload([
        'title' => 'Past',
        'published_at' => now('Asia/Kolkata')->subDay()->format('Y-m-d\TH:i'),
    ]));

    expect(Post::firstWhere('title', 'Draft')->published_at)->toBeNull()
        ->and(Post::firstWhere('title', 'Past')->published_at)->toBeNull()
        ->and(Post::published()->count())->toBe(1);
});

test('clearing the schedule on update publishes the post now', function () {
    $post = Post::create(postPayload(['slug' => 'later']));
    $post->applySchedule(now()->addDays(2));
    $post->save();

    $this->actingAs($this->admin)
        ->put(route('posts.update', $post), postPayload(['title' => 'Scheduled Post', 'published_at' => '']))
        ->assertRedirect(route('allPost'));

    $post->refresh();
    expect($post->published_at)->toBeNull()
        ->and($post->created_at->isFuture())->toBeFalse()
        ->and(Post::published()->count())->toBe(1);
});

test('stories can be scheduled the same way', function () {
    $istInput = now('Asia/Kolkata')->addDays(2)->setTime(9, 0)->format('Y-m-d\TH:i');

    $this->actingAs($this->admin)
        ->post(route('stories.store'), storyPayload(['published_at' => $istInput]))
        ->assertRedirect(route('allStory'));

    $story = Story::firstWhere('title', 'Scheduled Story');
    expect($story->isScheduled())->toBeTrue()
        ->and(Story::published()->count())->toBe(0);

    $this->get(route('story.show', $story->slug))->assertNotFound();

    $this->actingAs($this->admin)->get(route('allStory', ['status' => 'scheduled']))
        ->assertOk()->assertSee('Scheduled Story');
});

test('admin lists can filter scheduled posts and show their badge', function () {
    $post = Post::create(postPayload(['slug' => 'later']));
    $post->applySchedule(now()->addDay());
    $post->save();

    $this->actingAs($this->admin)->get(route('allPost', ['status' => 'scheduled']))
        ->assertOk()->assertSee('Scheduled Post')->assertSee('IST');
    $this->actingAs($this->admin)->get(route('allPost', ['status' => 'published']))
        ->assertOk()->assertDontSee('Scheduled Post');
});

test('the scheduler command refreshes sitemaps once scheduled content is live', function () {
    $post = Post::create(postPayload(['slug' => 'later']));
    $post->applySchedule(now()->addHour());
    $post->save();
    app(SitemapService::class)->generate();

    expect(file_get_contents(SitemapService::path('blogs-sitemap.xml')))->not->toContain('/later');

    $this->travel(2)->hours();
    touch(SitemapService::path('sitemap.xml'), now()->subHours(2)->getTimestamp());
    $this->artisan('app:publish-scheduled')->assertSuccessful();

    expect(file_get_contents(SitemapService::path('blogs-sitemap.xml')))->toContain('/later');
});
