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
