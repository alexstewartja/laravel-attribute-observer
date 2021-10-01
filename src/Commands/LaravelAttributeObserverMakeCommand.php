<?php

namespace AlexStewartJA\LaravelAttributeObserver\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class LaravelAttributeObserverMakeCommand extends ObserverMakeCommand
{
    public $name = 'make:laravel-attribute-observer';

    public $description = 'Create a new attribute observer class';

    protected $type = 'Attribute Observer';

    protected function getStub(): string
    {
        return $this->option('model')
            ? __DIR__ . '/stubs/attribute-observer.stub'
            : $this->resolveStubPath('/stubs/observer.plain.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\AttributeObservers';
    }

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the attribute observer applies to.'],
        ];
    }
}
