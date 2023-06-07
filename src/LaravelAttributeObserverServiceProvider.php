<?php

namespace AlexStewartJA\LaravelAttributeObserver;

use AlexStewartJA\LaravelAttributeObserver\Commands\LaravelAttributeObserverMakeCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
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

    private const EVENT_REGEX = '/([A-Z]{1}\w[a-z]*$)/';

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

    /**
     * This is where all the fun happens.
     */
    private function observeModels(): void
    {
        if (empty($this->observers)) {
            return;
        }

        foreach (array_keys($this->observers) as $modelClass) {
            // Carry on if no attribute observers are defined for this model
            if (empty($this->observers[$modelClass])) {
                continue;
            }

            if (is_object($modelClass)) {
                $modelClass = get_class($modelClass);
            }

            if (! class_exists($modelClass)) {
                continue;
            }

            foreach ($this->observers[$modelClass] as $observerClass) {
                if (is_object($observerClass)) {
                    $observerClass = get_class($observerClass);
                }

                if (! class_exists($observerClass)) {
                    continue;
                }

                $observerInstance = App::make($observerClass);
                $observerEventsAttribs = $this->parseObserverMethods($observerInstance);
                $observedEvents = array_keys($observerEventsAttribs);

                foreach ($observedEvents as $observedEvent) {
                    $modelClass::{$observedEvent}(function (Model $model) use ($observedEvent, $observerEventsAttribs, $observerInstance) {
                        if (! $this->wasChanged($model, $observedEvent)) {
                            return;
                        }

                        foreach ($observerEventsAttribs[$observedEvent] as $attribute) {
                            if ($this->modelHasAttribute($model, $attribute) && $this->wasChanged($model, $observedEvent, $attribute)) {
                                $method = 'on' . Str::studly($attribute) . Str::ucfirst($observedEvent);
                                $observerInstance->{$method}($model, $model->getAttributeValue($attribute), $model->getOriginal($attribute));
                            }
                        }
                    });
                }
            }
        }
    }

    /**
     * Scan an attribute observer, then parse and collate all 'legal' methods defined on it.
     *
     * @param mixed $observerObjectOrClass Object or fully qualified class name as a string
     * @return array
     */
    private function parseObserverMethods($observerObjectOrClass): array
    {
        $eventsAttribsMapping = [];

        // Methods that react to attribute changes start with 'on'. Let's grab those...
        $observerMethods = array_map(
            static function ($method) {
                return Str::startsWith($method, 'on') ? substr($method, 2) : false;
            },
            get_class_methods($observerObjectOrClass)
        );

        foreach ($observerMethods as $observerMethod) {
            if (! $observerMethod) {
                continue;
            }

            // The last capitalized word in the method's name is always the event being reacted to
            preg_match(self::EVENT_REGEX, $observerMethod, $matches);
            $event = strtolower($matches[1]);

            if (in_array($event, self::EVENTS)) {
                $attribute = Str::snake(str_replace($matches[1], '', $observerMethod));

                $eventsAttribsMapping[$event][] = $attribute;
            }
        }

        return $eventsAttribsMapping;
    }

    /**
     * Comprehensively check for the presence of an attribute on a model instance.
     *
     * @param Model $model
     * @param string $attribute
     * @return bool
     */
    private function modelHasAttribute(Model $model, string $attribute): bool
    {
        return ! method_exists($model, $attribute) &&
            (array_key_exists($attribute, $model->getAttributes()) ||
                array_key_exists($attribute, $model->getCasts()) ||
                $model->hasGetMutator($attribute) ||
                array_key_exists($attribute, $model->getRelations()));
    }

    /**
     * Dynamically check if the model was changed or is dirty.
     *
     * @param Model $model
     * @param string $event
     * @param string|null $attribute
     * @return bool
     */
    private function wasChanged(Model $model, string $event, string $attribute = null): bool
    {
        // Pull past-tense/post-mutation events from constants array
        $postEvents = array_filter(self::EVENTS, fn ($e) => Str::endsWith($e, 'ed'));

        // If the model was just inserted then all `created` events are valid
        if ($event === 'created' && $model->wasRecentlyCreated) {
            return true;
        }

        if (in_array($event, $postEvents)) {
            return $attribute
                ? $model->wasChanged($attribute)
                : $model->wasChanged();
        }

        return $attribute
            ? $model->isDirty($attribute)
            : $model->isDirty();
    }
}
