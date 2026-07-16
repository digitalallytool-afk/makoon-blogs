<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Printable;
use App\Models\Story;
use App\Models\VideoSession;
use Illuminate\Support\Facades\URL;

class SitemapService
{
    /**
     * Regenerate all sitemaps and robots.txt.
     * Call this after any model is created, updated, deleted, or status-changed.
     */
    public function generate(): void
    {
        $this->generateSitemapIndex();
        $this->generateBlogsSitemap();
        $this->generateStoriesSitemap();
        $this->generatePrintablesSitemap();
        $this->generateSessionsSitemap();
        $this->generateRobots();
    }

    /**
     * Build and write public/sitemap.xml as a Sitemap Index.
     */
    private function generateSitemapIndex(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.$baseUrl.'/sitemap.xsl"?>'."\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        $sitemaps = [
            'blogs-sitemap.xml',
            'stories-sitemap.xml',
            'printables-sitemap.xml',
            'sessions-sitemap.xml',
        ];

        foreach ($sitemaps as $sitemap) {
            $xml .= "    <sitemap>\n";
            $xml .= "        <loc>{$baseUrl}/{$sitemap}</loc>\n";
            $xml .= '        <lastmod>'.now()->toAtomString()."</lastmod>\n";
            $xml .= "    </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        file_put_contents(public_path('sitemap.xml'), $xml);
    }

    /**
     * Build and write public/blogs-sitemap.xml.
     */
    private function generateBlogsSitemap(): void
    {
        $posts = Post::published()
            ->with('category')
            ->latest('updated_at')
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.$baseUrl.'/sitemap.xsl"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'."\n";
        $xml .= '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">'."\n";

        // Homepage & Index Page
        $xml .= $this->buildUrlEntry($baseUrl.'/', now()->toAtomString(), 'daily', '1.0');
        $xml .= $this->buildUrlEntry($baseUrl, now()->toAtomString(), 'daily', '0.9');

        // Static Pages
        $xml .= $this->buildUrlEntry($baseUrl.'/about-us', now()->toAtomString(), 'monthly', '0.6');
        $xml .= $this->buildUrlEntry($baseUrl.'/author-sana-kapoor', now()->toAtomString(), 'monthly', '0.6');

        // Blog Posts
        foreach ($posts as $post) {
            $canonical = $post->canonical_url
                ? rtrim($post->canonical_url, '/')
                : (str_ends_with($baseUrl, '/blogs') ? $baseUrl.'/'.$post->slug : $baseUrl.'/blogs/'.$post->slug);

            // Clean duplicate /blogs/blogs/ pattern if it accidentally exists
            $canonical = str_replace('/blogs/blogs/', '/blogs/', $canonical);

            $xml .= $this->buildUrlEntry(
                $canonical,
                $post->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('blogs-sitemap.xml'), $xml);
    }

    /**
     * Build and write public/stories-sitemap.xml.
     */
    private function generateStoriesSitemap(): void
    {
        $stories = Story::published()
            ->with('storyCategory')
            ->latest('updated_at')
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');
        $baseDomainUrl = str_replace('/blogs', '', $baseUrl);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.$baseUrl.'/sitemap.xsl"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        // Stories Index Page
        $xml .= $this->buildUrlEntry($baseDomainUrl.'/stories', now()->toAtomString(), 'daily', '0.9');

        // Stories posts
        foreach ($stories as $story) {
            $canonical = $story->canonical_url
                ? rtrim($story->canonical_url, '/')
                : $baseDomainUrl.'/stories/'.$story->slug;

            $xml .= $this->buildUrlEntry(
                $canonical,
                $story->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('stories-sitemap.xml'), $xml);
    }

    /**
     * Build and write public/printables-sitemap.xml.
     */
    private function generatePrintablesSitemap(): void
    {
        $printables = Printable::published()
            ->latest('updated_at')
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');
        $baseDomainUrl = str_replace('/blogs', '', $baseUrl);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.$baseUrl.'/sitemap.xsl"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        // Printables Index Page
        $xml .= $this->buildUrlEntry($baseDomainUrl.'/printables', now()->toAtomString(), 'weekly', '0.8');

        // Printables sheets
        foreach ($printables as $printable) {
            $xml .= $this->buildUrlEntry(
                $baseDomainUrl.'/printables/'.$printable->slug,
                $printable->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('printables-sitemap.xml'), $xml);
    }

    /**
     * Build and write public/sessions-sitemap.xml.
     */
    private function generateSessionsSitemap(): void
    {
        $videoSessions = VideoSession::published()
            ->latest('updated_at')
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');
        $baseDomainUrl = str_replace('/blogs', '', $baseUrl);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="'.$baseUrl.'/sitemap.xsl"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        // Parenting Sessions Index Page
        $xml .= $this->buildUrlEntry($baseDomainUrl.'/sessions', now()->toAtomString(), 'weekly', '0.8');

        // Parenting Sessions
        foreach ($videoSessions as $videoSession) {
            $xml .= $this->buildUrlEntry(
                $baseDomainUrl.'/video-sessions/'.$videoSession->slug,
                $videoSession->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sessions-sitemap.xml'), $xml);
    }

    /**
     * Build a single <url> entry for the sitemap.
     */
    private function buildUrlEntry(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "    <url>\n"
            .'        <loc>'.htmlspecialchars($loc)."</loc>\n"
            ."        <lastmod>{$lastmod}</lastmod>\n"
            ."        <changefreq>{$changefreq}</changefreq>\n"
            ."        <priority>{$priority}</priority>\n"
            ."    </url>\n";
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
