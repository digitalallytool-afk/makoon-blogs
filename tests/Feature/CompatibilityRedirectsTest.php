<?php

test('article details redirect with query parameter redirects to article show with 301', function () {
    $response = $this->get('/article-details?post=a-gentle-guide-to-your-childs-first-week-at-preschool');

    $response->assertRedirect('/blogs/a-gentle-guide-to-your-childs-first-week-at-preschool');
    $response->assertStatus(301);
});

test('article details redirect without query parameter redirects to articles list with 302', function () {
    $response = $this->get('/article-details');

    $response->assertRedirect('/blogs');
    $response->assertStatus(302);
});

test('blog details redirect with query parameter redirects to blog show with 301', function () {
    $response = $this->get('/blog-details?post=some-blog-slug');

    $response->assertRedirect('/blogs/some-blog-slug');
    $response->assertStatus(301);
});

test('blog details redirect without query parameter redirects to articles list with 302', function () {
    $response = $this->get('/blog-details');

    $response->assertRedirect('/blogs');
    $response->assertStatus(302);
});

test('story details redirect with query parameter redirects to story show with 301', function () {
    $response = $this->get('/story-details?story=some-story-slug');

    $response->assertRedirect('/stories/some-story-slug');
    $response->assertStatus(301);
});

test('story details redirect without query parameter redirects to stories list with 302', function () {
    $response = $this->get('/story-details');

    $response->assertRedirect('/stories');
    $response->assertStatus(302);
});

