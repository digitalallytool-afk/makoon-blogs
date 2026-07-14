<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate sitemap.xml and robots.txt using config APP_URL';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService)
    {
        $this->info('Regenerating sitemap.xml and robots.txt...');
        $sitemapService->generate();
        $this->info('Sitemap successfully regenerated!');

        return Command::SUCCESS;
    }
}
