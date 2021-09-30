<?php

namespace AlexStewartJA\LaravelAttributeObserver;

use AlexStewartJA\LaravelAttributeObserver\Commands\LaravelAttributeObserverMakeCommand;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAttributeObserverServiceProvider extends PackageServiceProvider
{
    private const EVENTS = [
        'creating',
        'created',
        'updating',
        'updated',
        'saving',
        'saved',
        'deleting',
        'deleted',
    ];

    private array $observers;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->observers = config('attribute-observer.observers', []);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-attribute-observer')
            ->hasConfigFile()
            ->hasCommand(LaravelAttributeObserverMakeCommand::class);
    }

    public function boot()
    {
        parent::boot();
        $this->observeModels();
    }

    private function observeModels()
    {
        if (empty($this->observers)) {
            return;
        }

        $app = $this->app;

        foreach (array_keys($this->observers) as $class) {
            if (class_exists($class)) {
                foreach (self::EVENTS as $event) {
                    try {
                        $class::{$event}(
                            function (Model $model) use ($event, $app) {
                                $className = get_class($model);

                                if (! isset($this->observers[$className])) {
                                    return;
                                }

                                foreach ($model->getChanges() as $key => $value) {
                                    foreach ($this->observers[$className] as $observer) {
                                        if (! is_object($observer)) {
                                            try {
                                                $observerInstance = $app->make($observer);
                                            } catch (BindingResolutionException $bindingResolutionException) {
                                                Log::error($bindingResolutionException);

                                                continue;
                                            }
                                        }

                                        if (isset($observerInstance)) {
                                            $method = 'on' . Str::studly($key) . ucfirst($event);
                                            if (method_exists($observerInstance, $method)) {
                                                $observerInstance->{$method}($model, $value, $model->getOriginal($key));
                                            }
                                        }
                                    }
                                }
                            }
                        );
                    } catch (\Throwable $exception) {
                        Log::error($exception);
                    }
                }
            }
        }
    }
}
