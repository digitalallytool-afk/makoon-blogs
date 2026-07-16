<?php

namespace App\Http\Middleware;

use App\Models\StoryCategory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RewriteStoriesUrl
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\Response &&
            str_contains($response->headers->get('Content-Type') ?? '', 'text/html')) {

            $content = $response->getContent();
            if ($content !== false && $content !== '') {
                $appUrl = rtrim(config('app.url') ?? '', '/');

                if ($appUrl !== '') {
                    $sections = ['stories', 'printables', 'sessions'];
                    foreach ($sections as $section) {
                        $oldUrl = $appUrl.'/'.$section;
                        $newUrl = str_replace('/blogs', '', $appUrl).'/'.$section;

                        $content = str_replace($oldUrl, $newUrl, $content);
                        $content = str_replace('href="/blogs/'.$section, 'href="/'.$section, $content);
                        $content = str_replace('action="/blogs/'.$section, 'action="/'.$section, $content);
                    }

                    // Dynamically rewrite story category URLs from /blogs/slug to /stories/slug
                    $storyCategorySlugs = StoryCategory::pluck('slug')->toArray();
                    foreach ($storyCategorySlugs as $slug) {
                        $content = str_replace('href="/blogs/'.$slug, 'href="/stories/'.$slug, $content);
                        $content = str_replace('href="'.$appUrl.'/'.$slug, 'href="'.str_replace('/blogs', '', $appUrl).'/stories/'.$slug, $content);
                    }

                    $original = $response->original ?? null;
                    $response->setContent($content);
                    if ($original !== null) {
                        $response->original = $original;
                    }
                }
            }
        }

        return $response;
    }
}
