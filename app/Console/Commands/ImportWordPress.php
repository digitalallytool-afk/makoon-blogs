<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Support\Str;

class ImportWordPress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:wordpress {file : The path to the WordPress XML export file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import posts, categories, and authors from a WordPress XML export file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Loading XML file...");
        $xmlContent = file_get_contents($filePath);

        // Parse XML safely
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_PARSEHUGE | LIBXML_NOCDATA);

        if (!$xml) {
            $this->error("Failed to parse XML file.");
            return 1;
        }

        $namespaces = $xml->getDocNamespaces(true);
        $wpNamespace = isset($namespaces['wp']) ? $namespaces['wp'] : 'http://wordpress.org/export/1.2/';
        $contentNamespace = isset($namespaces['content']) ? $namespaces['content'] : 'http://purl.org/rss/1.0/modules/content/';
        $excerptNamespace = isset($namespaces['excerpt']) ? $namespaces['excerpt'] : 'http://wordpress.org/export/1.2/excerpt/';

        $items = $xml->channel->item;
        $this->info("Found " . count($items) . " total items in the export file.");

        // 1. Build a map of attachment IDs to attachment URLs
        $attachments = [];
        $this->info("Scanning attachments...");
        foreach ($items as $item) {
            $wp = $item->children($wpNamespace);
            $postType = (string)$wp->post_type;
            if ($postType === 'attachment') {
                $postId = (int)$wp->post_id;
                $attachmentUrl = (string)$wp->attachment_url;
                $attachments[$postId] = $attachmentUrl;
            }
        }
        $this->info("Found " . count($attachments) . " attachments/images.");

        // 2. Import categories, authors, and posts
        $importedCount = 0;
        $this->info("Importing posts...");
        foreach ($items as $item) {
            $wp = $item->children($wpNamespace);
            $postType = (string)$wp->post_type;
            $status = (string)$wp->status;

            if ($postType !== 'post' || $status !== 'publish') {
                continue;
            }

            $title = (string)$item->title;
            $slug = (string)$wp->post_name;

            // Fallback for empty slugs
            if (empty($slug)) {
                $slug = Str::slug($title);
            }

            // Extract content and clean inline image paths
            $contentEncoded = $item->children($contentNamespace)->encoded;
            $content = (string)$contentEncoded;

            // Replace standard WordPress upload references with local ones
            $content = str_replace('wp-content/uploads/', 'uploads/', $content);
            $content = str_replace('https://makoons.com/uploads/', '/uploads/', $content);
            $content = str_replace('http://makoons.com/uploads/', '/uploads/', $content);

            $excerptEncoded = $item->children($excerptNamespace)->encoded;
            $excerpt = (string)$excerptEncoded;
            if (empty($excerpt)) {
                $excerpt = Str::limit(strip_tags($content), 150);
            }

            // Extract Author
            $dc = $item->children('http://purl.org/dc/elements/1.1/');
            $authorName = (string)$dc->creator;
            if (empty($authorName)) {
                $authorName = 'Makoons Admin';
            }

            $author = Author::firstOrCreate(
                ['name' => $authorName],
                ['description' => 'Author imported from WordPress']
            );

            // Extract Category
            $categoryName = 'Uncategorized';
            $categorySlug = 'uncategorized';
            foreach ($item->category as $catAttr) {
                if ((string)$catAttr['domain'] === 'category') {
                    $categoryName = (string)$catAttr;
                    $categorySlug = (string)$catAttr['nicename'];
                    break;
                }
            }

            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                [
                    'name' => $categoryName,
                    'description' => 'Category imported from WordPress'
                ]
            );

            // Extract Featured Image (if set via metadata)
            $featuredImageLocalPath = null;
            foreach ($wp->postmeta as $meta) {
                $metaKey = (string)$meta->meta_key;
                if ($metaKey === '_thumbnail_id') {
                    $thumbnailId = (int)$meta->meta_value;
                    if (isset($attachments[$thumbnailId])) {
                        $fullImageUrl = $attachments[$thumbnailId];
                        // Parse path relative to wp-content/uploads
                        if (preg_match('/wp-content\/uploads\/(.+)$/', $fullImageUrl, $matches)) {
                            $featuredImageLocalPath = 'uploads/' . $matches[1];
                        } else {
                            $featuredImageLocalPath = $fullImageUrl;
                        }
                    }
                    break;
                }
            }

            // Parse created date
            $createdAt = (string)$wp->post_date;
            $createdAtTime = strtotime($createdAt) ?: time();
            $formattedDate = date('Y-m-d H:i:s', $createdAtTime);

            // Check if post already exists with this slug
            $post = Post::where('slug', $slug)->first();
            if ($post) {
                $post->update([
                    'title' => $title,
                    'content' => $content,
                    'excerpt' => $excerpt,
                    'featured_image' => $featuredImageLocalPath,
                    'category_id' => $category->id,
                    'author_id' => $author->id,
                    'status' => 'published',
                ]);
            } else {
                Post::create([
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'excerpt' => $excerpt,
                    'featured_image' => $featuredImageLocalPath,
                    'category_id' => $category->id,
                    'author_id' => $author->id,
                    'status' => 'published',
                    'created_at' => $formattedDate,
                    'updated_at' => $formattedDate,
                ]);
            }

            $importedCount++;
        }

        $this->info("Successfully imported/updated {$importedCount} published posts.");
        return 0;
    }
}
