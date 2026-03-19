<?php

test('expense routes use unique names and expected methods', function () {
    expect(route('expense.index', absolute: false))->toBe('/expense');
    expect(route('expense.store', absolute: false))->toBe('/expense');

    $indexRoute = app('router')->getRoutes()->getByName('expense.index');
    $storeRoute = app('router')->getRoutes()->getByName('expense.store');

    expect($indexRoute)->not->toBeNull();
    expect($storeRoute)->not->toBeNull();
    expect($indexRoute->methods())->toContain('GET', 'HEAD');
    expect($storeRoute->methods())->toBe(['POST']);
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
