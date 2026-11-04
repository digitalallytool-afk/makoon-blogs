<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleXMLElement;

class SitemapController extends Controller
{
    /**
     * Sitemap files that may be served, keyed by route name.
     *
     * @var array<int, string>
     */
    public const FILES = [
        'sitemap.xml',
        'blogs-sitemap.xml',
        'stories-sitemap.xml',
        'printables-sitemap.xml',
        'sessions-sitemap.xml',
    ];

    /**
     * Serve a sitemap as raw XML for crawlers, or as a clickable HTML page for browser navigations.
     */
    public function show(Request $request, string $file): Response
    {
        abort_unless(in_array($file, self::FILES, true), 404);

        $path = SitemapService::path($file);
        abort_unless(is_file($path), 404);

        $xml = file_get_contents($path);

        if ($request->header('Sec-Fetch-Dest') === 'document') {
            $document = simplexml_load_string($xml);
            abort_if($document === false, 500);

            return response($this->htmlView($document, $file)->render(), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Vary' => 'Sec-Fetch-Dest',
            ]);
        }

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Vary' => 'Sec-Fetch-Dest',
        ]);
    }

    private function htmlView(SimpleXMLElement $document, string $file): View
    {
        $isIndex = $document->getName() === 'sitemapindex';
        $entries = [];

        foreach ($isIndex ? $document->sitemap : $document->url as $entry) {
            $entries[] = [
                'loc' => (string) $entry->loc,
                'lastmod' => (string) ($entry->lastmod ?? ''),
            ];
        }

        return view('sitemap-viewer', [
            'file' => $file,
            'isIndex' => $isIndex,
            'entries' => $entries,
        ]);
    }
}
