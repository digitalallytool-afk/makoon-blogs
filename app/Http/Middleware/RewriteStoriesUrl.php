<?php

namespace App\Http\Middleware;

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
                    $oldUrl = $appUrl.'/stories';
                    $newUrl = str_replace('/blogs', '', $appUrl).'/stories';

                    $content = str_replace($oldUrl, $newUrl, $content);
                    $content = str_replace('href="/blogs/stories', 'href="/stories', $content);
                    $content = str_replace('action="/blogs/stories', 'action="/stories', $content);

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
