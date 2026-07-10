<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\URL;

class SitemapService
{
    /**
     * Regenerate sitemap.xml and robots.txt from all currently published posts.
     * Call this after any post is created, updated, deleted, or status-changed.
     */
    public function generate(): void
    {
        $this->generateSitemap();
        $this->generateRobots();
    }

    /**
     * Build and write public/sitemap.xml.
     */
    private function generateSitemap(): void
    {
        $posts = Post::published()
            ->with('category')
            ->latest('updated_at')
            ->get();

        $stories = \App\Models\Story::published()
            ->with('storyCategory')
            ->latest('updated_at')
            ->get();

        $printables = \App\Models\Printable::published()
            ->latest('updated_at')
            ->get();

        $videoSessions = \App\Models\VideoSession::published()
            ->latest('updated_at')
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        // Homepage
        $xml .= $this->buildUrlEntry($baseUrl . '/', now()->toAtomString(), 'daily', '1.0');

        // Static library / section pages
        $xml .= $this->buildUrlEntry($baseUrl . '/blogs', now()->toAtomString(), 'daily', '0.9');
        $xml .= $this->buildUrlEntry($baseUrl . '/stories', now()->toAtomString(), 'daily', '0.9');
        $xml .= $this->buildUrlEntry($baseUrl . '/printables', now()->toAtomString(), 'weekly', '0.8');
        $xml .= $this->buildUrlEntry($baseUrl . '/sessions', now()->toAtomString(), 'weekly', '0.8');
        $xml .= $this->buildUrlEntry($baseUrl . '/about-us', now()->toAtomString(), 'monthly', '0.6');
        $xml .= $this->buildUrlEntry($baseUrl . '/author-sana-kapoor', now()->toAtomString(), 'monthly', '0.6');

        // Add Posts
        foreach ($posts as $post) {
            $canonical = $post->canonical_url
                ? rtrim($post->canonical_url, '/')
                : $baseUrl . '/blogs/' . $post->slug;

            $xml .= $this->buildUrlEntry(
                $canonical,
                $post->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        // Add Stories
        foreach ($stories as $story) {
            $canonical = $story->canonical_url
                ? rtrim($story->canonical_url, '/')
                : $baseUrl . '/stories/' . $story->slug;

            $xml .= $this->buildUrlEntry(
                $canonical,
                $story->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        // Add Printables
        foreach ($printables as $printable) {
            $xml .= $this->buildUrlEntry(
                $baseUrl . '/printables/' . $printable->slug,
                $printable->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        // Add Video Sessions
        foreach ($videoSessions as $videoSession) {
            $xml .= $this->buildUrlEntry(
                $baseUrl . '/video-sessions/' . $videoSession->slug,
                $videoSession->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sitemap.xml'), $xml);
    }

    /**
     * Build a single <url> entry for the sitemap.
     */
    private function buildUrlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "    <url>\n"
            . "        <loc>" . htmlspecialchars($loc) . "</loc>\n"
            . "        <lastmod>{$lastmod}</lastmod>\n"
            . "        <changefreq>{$changefreq}</changefreq>\n"
            . "        <priority>{$priority}</priority>\n"
            . "    </url>\n";
    }

    /**
     * Write public/robots.txt pointing to the sitemap.
     */
    private function generateRobots(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "\n";
        $robots .= "Sitemap: {$baseUrl}/sitemap.xml\n";

        file_put_contents(public_path('robots.txt'), $robots);
    }
}
