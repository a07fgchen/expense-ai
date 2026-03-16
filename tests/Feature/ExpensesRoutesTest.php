<?php

test('expenses routes use unique names and expected methods', function () {
    expect(route('expenses.index', absolute: false))->toBe('/expenses');
    expect(route('expenses.store', absolute: false))->toBe('/expenses');

    $indexRoute = app('router')->getRoutes()->getByName('expenses.index');
    $storeRoute = app('router')->getRoutes()->getByName('expenses.store');

    expect($indexRoute)->not->toBeNull();
    expect($storeRoute)->not->toBeNull();
    expect($indexRoute->methods())->toContain('GET', 'HEAD');
    expect($storeRoute->methods())->toBe(['POST']);
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
