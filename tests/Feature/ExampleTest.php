<?php

it('returns a redirect to login for guests', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});
