<?php

namespace AlexStewartJA\LaravelAttributeObserver\Tests;

use AlexStewartJA\LaravelAttributeObserver\LaravelAttributeObserverServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelAttributeObserverServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
