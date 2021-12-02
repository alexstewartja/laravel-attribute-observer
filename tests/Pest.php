<?php

use AlexStewartJA\LaravelAttributeObserver\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

test('generates an attribute observer', function () {
    $this->artisan('make:laravel-attribute-observer TestAttributeObserver')
        ->assertExitCode(0);
});

test('generates an attribute observer (for an Eloquent Model)', function () {
    $this->artisan('make:laravel-attribute-observer TestUserAttributeObserver --model=User')
        ->assertExitCode(0);
});
