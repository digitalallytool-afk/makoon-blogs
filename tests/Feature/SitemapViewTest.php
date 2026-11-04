<?php

use App\Services\SitemapService;

beforeEach(function () {
    app(SitemapService::class)->generate();
});

test('crawlers receive raw xml', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/xml');
    expect($response->getContent())->toContain('<sitemapindex');
    expect($response->getContent())->not->toContain('xml-stylesheet');
});

test('browser navigations receive a clickable html sitemap', function () {
    $response = $this->get('/sitemap.xml', ['Sec-Fetch-Dest' => 'document']);

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('text/html');
    $response->assertSee('<a href="', false);
    $response->assertSee('blogs-sitemap.xml');
});

test('child sitemaps are served and unknown files return 404', function () {
    $this->get('/blogs-sitemap.xml')->assertOk();
    $this->get('/blogs-sitemap.xml', ['Sec-Fetch-Dest' => 'document'])->assertOk()->assertSee('Back to sitemap index');
    $this->get('/missing-sitemap.xml')->assertNotFound();
});

test('sitemap viewer keeps the /blogs prefix on every sitemap link', function () {
    config(['app.url' => 'https://makoons.com/blogs']);
    app(SitemapService::class)->generate();

    $response = $this->get('/sitemap.xml', ['Sec-Fetch-Dest' => 'document']);

    foreach (['blogs', 'stories', 'printables', 'sessions'] as $section) {
        $response->assertSee("https://makoons.com/blogs/{$section}-sitemap.xml", false);
        $response->assertDontSee("https://makoons.com/{$section}-sitemap.xml", false);
    }
});

test('raw sitemap xml keeps the /blogs prefix for crawlers', function () {
    config(['app.url' => 'https://makoons.com/blogs']);
    app(SitemapService::class)->generate();

    $xml = $this->get('/sitemap.xml')->getContent();

    expect($xml)->toContain('https://makoons.com/blogs/stories-sitemap.xml')
        ->and($xml)->not->toContain('https://makoons.com/stories-sitemap.xml');
});
