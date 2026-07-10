<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use App\Models\Printable;
use App\Models\SessionCategory;
use App\Models\Story;
use App\Models\StoryCategory;
use App\Models\VideoSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['category.parent', 'author'])
            ->published()
            ->latest()
            ->get();

        $trendingPosts = Post::with(['category', 'author'])
            ->published()
            ->orderByDesc('view_count')
            ->limit(4)
            ->get();

        $heroPost = Post::with(['category', 'author'])
            ->published()
            ->where('slug', 'what-helps-children-feel-at-home-in-a-new-classroom')
            ->first() ?? $posts->first();

        $featuredPosts = Post::with(['category', 'author'])
            ->published()
            ->where('is_selected', true)
            ->latest()
            ->limit(3)
            ->get();

        if ($featuredPosts->isEmpty()) {
            $featuredPosts = Post::with(['category', 'author'])
                ->published()
                ->orderByDesc('view_count')
                ->skip(4)
                ->take(3)
                ->get();
        }

        $stories = Story::with(['storyCategory', 'author'])
            ->published()
            ->latest()
            ->limit(4)
            ->get();

        $printables = Printable::where('status', 'published')
            ->latest()
            ->limit(4)
            ->get();

        $videoSessions = VideoSession::with('sessionCategory')
            ->published()
            ->latest()
            ->limit(3)
            ->get();

        $mainCategories = Category::whereNull('parent_id')->get();
        $subCategories = Category::whereNotNull('parent_id')->get();

        $homeCategories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount(['posts' => function ($q) {
                    $q->published();
                }]);
            }])
            ->withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->get()
            ->map(function ($category) {
                $category->posts_count = $category->posts_count + $category->children->sum('posts_count');

                return $category;
            })
            ->filter(function ($category) {
                return $category->posts_count > 0;
            })
            ->sortByDesc('posts_count')
            ->take(4);

        return view('frontend.pages.index', compact(
            'posts',
            'trendingPosts',
            'heroPost',
            'featuredPosts',
            'stories',
            'printables',
            'videoSessions',
            'homeCategories',
            'mainCategories',
            'subCategories'
        ));
    }

    public function about(): View
    {
        return view('frontend.pages.why-we-write');
    }

    public function blogs(): View
    {
        $posts = Post::with(['category.parent', 'author'])
            ->published()
            ->latest()
            ->get();

        $mainCategories = Category::whereNull('parent_id')->get();
        $subCategories = Category::whereNotNull('parent_id')->get();

        return view('frontend.pages.all-posts', compact('posts', 'mainCategories', 'subCategories'));
    }

    public function categoryDetails(string $slug): View
    {
        // Try to find in post categories
        $postCategory = Category::where('slug', $slug)->first();
        if ($postCategory) {
            // Fetch category posts. If it is a parent category, get posts of all child subcategories too
            $categoryIds = $postCategory->parent_id === null
                ? $postCategory->children()->pluck('id')->push($postCategory->id)
                : collect([$postCategory->id]);

            $posts = Post::with(['category.parent', 'author'])
                ->published()
                ->whereIn('category_id', $categoryIds)
                ->latest()
                ->get();

            $mainCategories = Category::whereNull('parent_id')->get();
            $subCategories = Category::whereNotNull('parent_id')->get();

            return view('frontend.pages.category-details', [
                'category' => $postCategory,
                'type' => 'blogs',
                'posts' => $posts,
                'mainCategories' => $mainCategories,
                'subCategories' => $subCategories,
            ]);
        }

        // Try to find in story categories
        $storyCategory = StoryCategory::where('slug', $slug)->first();
        if ($storyCategory) {
            $stories = Story::with(['storyCategory', 'author'])
                ->published()
                ->where('story_category_id', $storyCategory->id)
                ->latest()
                ->get();

            return view('frontend.pages.category-details', [
                'category' => $storyCategory,
                'type' => 'stories',
                'stories' => $stories,
            ]);
        }

        abort(404);
    }

    public function previewPost(Request $request): View
    {
        $categoryId = $request->input('category_id');
        $category = null;
        if ($categoryId) {
            $category = Category::with('parent')->find($categoryId);
        }

        $authorId = $request->input('author_id');
        $author = null;
        if ($authorId) {
            $author = Author::find($authorId);
        }

        $featuredImage = 'media-one';
        if ($imageUrl = $request->input('featured_image_url')) {
            $featuredImage = $imageUrl;
        } elseif ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('temp_previews', 'public');
            $featuredImage = asset('storage/'.$path);
        }

        $post = new Post([
            'title' => $request->input('title', 'Preview Title'),
            'slug' => $request->input('slug', 'preview-slug'),
            'content' => $request->input('content', ''),
            'excerpt' => $request->input('excerpt', ''),
            'featured_image' => $featuredImage,
            'category_id' => $categoryId,
            'author_id' => $authorId,
            'view_count' => 0,
        ]);
        $post->created_at = now();

        $post->setRelation('category', $category);
        $post->setRelation('author', $author);

        $relatedPosts = Post::with(['category', 'author'])
            ->published()
            ->limit(3)
            ->get();

        $homeCategories = Category::withCount(['posts' => function ($query) {
            $query->published();
        }])
            ->whereNotNull('parent_id')
            ->orderByDesc('posts_count')
            ->limit(4)
            ->get();

        return view('frontend.pages.blog-details', [
            'post' => $post,
            'trendingPosts' => $relatedPosts,
            'homeCategories' => $homeCategories,
            'isPreview' => true,
        ]);
    }

    public function previewStory(Request $request): View
    {
        $storyCategoryId = $request->input('story_category_id');
        $storyCategory = null;
        if ($storyCategoryId) {
            $storyCategory = StoryCategory::find($storyCategoryId);
        }

        $authorId = $request->input('author_id');
        $author = null;
        if ($authorId) {
            $author = Author::find($authorId);
        }

        $featuredImage = 'story-one';
        if ($imageUrl = $request->input('featured_image_url')) {
            $featuredImage = $imageUrl;
        } elseif ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('temp_previews', 'public');
            $featuredImage = asset('storage/'.$path);
        }

        $story = new Story([
            'title' => $request->input('title', 'Preview Title'),
            'slug' => $request->input('slug', 'preview-slug'),
            'content' => $request->input('content', ''),
            'excerpt' => $request->input('excerpt', ''),
            'description' => $request->input('description', ''),
            'featured_image' => $featuredImage,
            'story_category_id' => $storyCategoryId,
            'author_id' => $authorId,
            'view_count' => 0,
        ]);
        $story->created_at = now();

        $story->setRelation('storyCategory', $storyCategory);
        $story->setRelation('author', $author);

        $trendingStories = Story::with(['storyCategory', 'author'])
            ->published()
            ->limit(4)
            ->get();

        $storyCategories = StoryCategory::withCount(['stories' => function ($query) {
            $query->published();
        }])
            ->get()
            ->filter(function ($cat) {
                return $cat->stories_count > 0;
            })
            ->sortByDesc('stories_count')
            ->take(4);

        return view('frontend.pages.story-details', [
            'story' => $story,
            'trendingStories' => $trendingStories,
            'storyCategories' => $storyCategories,
            'isPreview' => true,
        ]);
    }

    public function stories(): View
    {
        $stories = Story::with(['storyCategory', 'author'])
            ->published()
            ->latest()
            ->get();

        $storyCategories = StoryCategory::all();

        return view('frontend.pages.all-stories', compact('stories', 'storyCategories'));
    }

    public function printables(Request $request): View
    {
        $search = $request->query('search');

        $query = Printable::where('status', 'published');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $printables = $query->latest()->get();

        return view('frontend.pages.all-printables', compact('printables', 'search'));
    }

    public function sessions(): View
    {
        $videoSessions = VideoSession::with('sessionCategory')
            ->published()
            ->latest()
            ->get();

        $sessionCategories = SessionCategory::all();

        return view('frontend.pages.all-sessions', compact('videoSessions', 'sessionCategories'));
    }

    public function blogDetails(string $slug): View
    {
        $post = Post::with(['category.parent', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $viewed = session()->get('viewed_posts', []);
        if (! in_array($post->id, $viewed)) {
            $post->increment('view_count');
            session()->push('viewed_posts', $post->id);
        }

        $trendingPosts = Post::with(['category', 'author'])
            ->published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('view_count')
            ->latest()
            ->limit(4)
            ->get();

        $homeCategories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount(['posts' => function ($q) {
                    $q->published();
                }]);
            }])
            ->withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->get()
            ->map(function ($category) {
                $category->posts_count = $category->posts_count + $category->children->sum('posts_count');

                return $category;
            })
            ->filter(function ($category) {
                return $category->posts_count > 0;
            })
            ->sortByDesc('posts_count')
            ->take(4);

        return view('frontend.pages.blog-details', compact('post', 'trendingPosts', 'homeCategories'));
    }

    public function storyDetails(string $slug): View
    {
        $story = Story::with(['storyCategory', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $viewedStories = session()->get('viewed_stories', []);
        if (! in_array($story->id, $viewedStories)) {
            $story->increment('view_count');
            session()->push('viewed_stories', $story->id);
        }

        $trendingStories = Story::with(['storyCategory', 'author'])
            ->published()
            ->where('id', '!=', $story->id)
            ->orderByDesc('view_count')
            ->limit(4)
            ->get();

        $storyCategories = StoryCategory::withCount(['stories' => function ($query) {
            $query->published();
        }])
            ->get()
            ->filter(function ($cat) {
                return $cat->stories_count > 0;
            })
            ->sortByDesc('stories_count')
            ->take(4);

        return view('frontend.pages.story-details', compact('story', 'trendingStories', 'storyCategories'));
    }

    public function author(Request $request): View
    {
        $slug = $request->query('author');
        $type = $request->query('type', 'blogs');

        $author = null;
        if ($slug) {
            // authors table has no slug column — derive name from slug and match by name
            $nameFromSlug = str_replace('-', ' ', $slug);
            $author = Author::whereRaw('LOWER(name) = ?', [strtolower($nameFromSlug)])->first();

            // fallback: partial match in case of slight differences
            if (! $author) {
                $author = Author::whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($nameFromSlug).'%'])->first();
            }
        }

        if (! $author) {
            $author = Author::first();
        }

        if ($type === 'stories') {
            $author->load(['stories' => function ($query) {
                $query->published()->latest();
            }, 'stories.storyCategory']);

            $storiesCount = $author->stories->count();
            $totalViews = $author->stories->sum('view_count');
            $formattedViews = $totalViews >= 1000 ? number_format($totalViews / 1000, 1).'k' : $totalViews;

            $mainTopic = 'School Life';
            if ($storiesCount > 0) {
                $mostFrequentCategory = $author->stories->groupBy('story_category_id')
                    ->sortByDesc(fn ($stories) => $stories->count())
                    ->first();
                if ($mostFrequentCategory && $mostFrequentCategory->first()->storyCategory) {
                    $mainTopic = $mostFrequentCategory->first()->storyCategory->name;
                }
            }

            return view('frontend.pages.author', [
                'author' => $author,
                'itemsCount' => $storiesCount,
                'formattedViews' => $formattedViews,
                'mainTopic' => $mainTopic,
                'type' => 'stories',
            ]);
        } else {
            $author->load(['posts' => function ($query) {
                $query->published()->latest();
            }, 'posts.category']);

            $postsCount = $author->posts->count();
            $totalViews = $author->posts->sum('view_count');
            $formattedViews = $totalViews >= 1000 ? number_format($totalViews / 1000, 1).'k' : $totalViews;

            $mainTopic = 'Parenting';
            if ($postsCount > 0) {
                $mostFrequentCategory = $author->posts->groupBy('category_id')
                    ->sortByDesc(fn ($posts) => $posts->count())
                    ->first();
                if ($mostFrequentCategory && $mostFrequentCategory->first()->category) {
                    $mainTopic = $mostFrequentCategory->first()->category->name;
                }
            }

            return view('frontend.pages.author', [
                'author' => $author,
                'itemsCount' => $postsCount,
                'formattedViews' => $formattedViews,
                'mainTopic' => $mainTopic,
                'type' => 'blogs',
            ]);
        }
    }

    public function authorSana(Request $request): View
    {
        $author = Author::whereRaw('LOWER(name) LIKE ?', ['%sana%'])->first();
        $type = $request->query('type', 'blogs');

        if (! $author) {
            $author = Author::first();
        }

        if ($type === 'stories') {
            $author->load(['stories' => function ($query) {
                $query->published()->latest();
            }, 'stories.storyCategory']);

            $storiesCount = $author->stories->count();
            $totalViews = $author->stories->sum('view_count');
            $formattedViews = $totalViews >= 1000 ? number_format($totalViews / 1000, 1).'k' : $totalViews;

            $mainTopic = 'School Life';
            if ($storiesCount > 0) {
                $mostFrequentCategory = $author->stories->groupBy('story_category_id')
                    ->sortByDesc(fn ($stories) => $stories->count())
                    ->first();
                if ($mostFrequentCategory && $mostFrequentCategory->first()->storyCategory) {
                    $mainTopic = $mostFrequentCategory->first()->storyCategory->name;
                }
            }

            return view('frontend.pages.author', [
                'author' => $author,
                'itemsCount' => $storiesCount,
                'formattedViews' => $formattedViews,
                'mainTopic' => $mainTopic,
                'type' => 'stories',
            ]);
        } else {
            $author->load(['posts' => function ($query) {
                $query->published()->latest();
            }, 'posts.category']);

            $postsCount = $author->posts->count();
            $totalViews = $author->posts->sum('view_count');
            $formattedViews = $totalViews >= 1000 ? number_format($totalViews / 1000, 1).'k' : $totalViews;

            $mainTopic = 'Food';
            if ($postsCount > 0) {
                $mostFrequentCategory = $author->posts->groupBy('category_id')
                    ->sortByDesc(fn ($posts) => $posts->count())
                    ->first();
                if ($mostFrequentCategory && $mostFrequentCategory->first()->category) {
                    $mainTopic = $mostFrequentCategory->first()->category->name;
                }
            }

            return view('frontend.pages.author', [
                'author' => $author,
                'itemsCount' => $postsCount,
                'formattedViews' => $formattedViews,
                'mainTopic' => $mainTopic,
                'type' => 'blogs',
            ]);
        }
    }
}
