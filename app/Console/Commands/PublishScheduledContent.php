<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Story;
use App\Services\SitemapService;
use Illuminate\Console\Command;

class PublishScheduledContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the sitemaps once scheduled posts or stories have gone live';

    /**
     * Scheduled content goes live on its own once its time passes (see HasScheduledPublishing),
     * so this only rebuilds the sitemaps when something became live after they were last written.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $sitemapPath = SitemapService::path('sitemap.xml');
        $lastGenerated = is_file($sitemapPath) ? filemtime($sitemapPath) : 0;
        $since = now()->setTimestamp($lastGenerated);

        $becameLive = Post::where('status', 'published')->whereBetween('published_at', [$since, now()])->exists()
            || Story::where('status', 'published')->whereBetween('published_at', [$since, now()])->exists();

        if ($becameLive) {
            $sitemapService->generate();
            $this->info('Scheduled content is live. Sitemaps refreshed.');
        }

        return self::SUCCESS;
    }
}
