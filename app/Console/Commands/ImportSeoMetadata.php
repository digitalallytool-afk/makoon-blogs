<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Story;
use Illuminate\Console\Command;

class ImportSeoMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-seo-metadata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Yoast SEO metadata from WordPress XML export files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = [
            'wordpress_export.xml' => Post::class,
            'stories_export.xml' => Story::class,
        ];

        foreach ($files as $fileName => $modelClass) {
            $filePath = base_path($fileName);
            if (! file_exists($filePath)) {
                $this->warn("File not found: {$fileName}");

                continue;
            }

            $this->info("Parsing {$fileName}...");
            $xml = simplexml_load_file($filePath, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml === false) {
                $this->error("Failed to parse XML in {$fileName}");

                continue;
            }

            $items = $xml->channel->item;
            $updatedCount = 0;

            foreach ($items as $item) {
                $wp = $item->children('wp', true);
                $postType = (string) $wp->post_type;
                $slug = (string) $wp->post_name;

                if (empty($slug)) {
                    continue;
                }

                // Look up record in database
                $record = $modelClass::where('slug', $slug)->first();
                if (! $record) {
                    $title = (string) $item->title;
                    $record = $modelClass::where('title', $title)->first();
                }

                if ($record) {
                    $metaTitle = null;
                    $metaDesc = null;
                    $metaKeywords = null;
                    $canonicalUrl = null;

                    foreach ($wp->postmeta as $meta) {
                        $key = (string) $meta->meta_key;
                        $value = (string) $meta->meta_value;

                        if ($key === '_yoast_wpseo_title') {
                            $metaTitle = $value;
                        } elseif ($key === '_yoast_wpseo_metadesc') {
                            $metaDesc = $value;
                        } elseif ($key === '_yoast_wpseo_focuskw') {
                            $metaKeywords = $value;
                        } elseif ($key === '_yoast_wpseo_canonical') {
                            $canonicalUrl = $value;
                        }
                    }

                    $updates = [];
                    if ($metaTitle) {
                        $updates['meta_title'] = $metaTitle;
                    }
                    if ($metaDesc) {
                        $updates['meta_description'] = $metaDesc;
                    }
                    if ($metaKeywords) {
                        $updates['meta_keywords'] = $metaKeywords;
                    }
                    if ($canonicalUrl) {
                        $updates['canonical_url'] = $canonicalUrl;
                    }

                    if (! empty($updates)) {
                        $record->update($updates);
                        $updatedCount++;
                    }
                }
            }

            $this->info("Successfully updated {$updatedCount} records from {$fileName}!");
        }

        return Command::SUCCESS;
    }
}
