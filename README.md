# Laravel Attribute Observer

![Laravel Attribute Observer - Social Image](https://banners.beyondco.de/Laravel%20Attribute%20Observer.png?theme=light&packageManager=composer+require&packageName=alexstewartja%2Flaravel-attribute-observer&pattern=floatingCogs&style=style_2&description=Observe+%28and+react+to%29+attribute+changes+made+on+Eloquent+models.&md=1&showWatermark=0&fontSize=125px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alexstewartja/laravel-attribute-observer.svg?style=flat-square)](https://packagist.org/packages/alexstewartja/laravel-attribute-observer)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/alexstewartja/laravel-attribute-observer/run-tests?label=tests)](https://github.com/alexstewartja/laravel-attribute-observer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/alexstewartja/laravel-attribute-observer/Check%20&%20fix%20styling?label=code%20style)](https://github.com/alexstewartja/laravel-attribute-observer/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alexstewartja/laravel-attribute-observer.svg?style=flat-square)](https://packagist.org/packages/alexstewartja/laravel-attribute-observer)

[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg?style=flat-square)](https://www.paypal.com/donate?business=iamalexstewart%40gmail.com&no_recurring=0&item_name=Laravel+Attribute+Observer&currency_code=USD)
[![Buy Me A Coffee](https://img.shields.io/badge/-buy_me_a%C2%A0coffee-orange?logo=buy-me-a-coffee)](https://www.buymeacoffee.com/asja)

## Requirements

- PHP: 7.4+
- Laravel: 7+

## Installation

You can install the package via composer:

```bash
composer require alexstewartja/laravel-attribute-observer
```

## Configuration

Publish the config file (`config/attribute-observer.php`) with:
```bash
php artisan vendor:publish --provider="AlexStewartJA\LaravelAttributeObserver\LaravelAttributeObserverServiceProvider"
```

This is the default content of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Attribute Observers
    |--------------------------------------------------------------------------
    |
    | Here you may configure all desired Models and their respective Attribute
    | Observers. For example:
    |
    | 'observers' => [
    |    \App\Models\Order::class => [
    |        \App\AttributeObservers\OrderStatusObserver::class,
    |    ],
    | ]
    |
    */

    'observers' => [
        // Define your model & attribute observers here...
    ]
];
```

Populate the `observers` array with your desired **Model => Attribute Observer** mappings.

## Usage

### Attribute Observers

The `make:laravel-attribute-observer` Artisan command is the easiest way to create a new attribute observer class:

```bash
php artisan make:laravel-attribute-observer OrderStatusObserver --model=Order
```

This command will place the new attribute observer in your `App/AttributeObservers` directory. If this directory does not exist,
Artisan will create it for you. Your freshly generated attribute observer will look like the following:

```php
<?php

namespace App\AttributeObservers;

use App\Models\Order;

class OrderStatusObserver
{
    /**
     * Handle changes to the "id" field of Order on "created" events.
     *
     * @param \App\Models\Order $order
     * @param mixed $newValue The current value of the field
     * @param mixed $oldValue The previous value of the field
     * @return void
     */
    public function onIdCreated(Order $order, mixed $newValue, mixed $oldValue)
    {
        //
    }
}
```

You may then modify the code to match your business logic, for example:

```php
<?php

namespace App\AttributeObservers;

use App\Events\OrderStatusChanged;
use App\Models\Order;

class OrderStatusObserver
{
    /**
     * Handle changes to the "status" field of Order on "saved" events.
     *
     * @param \App\Models\Order $order
     * @param mixed $newValue The current value of the field
     * @param mixed $oldValue The previous value of the field
     * @return void
     */
    public function onStatusSaved(Order $order, mixed $newValue, mixed $oldValue)
    {
        // Dispatch an event that sends an order update email to the customer
        OrderStatusChanged::dispatch($order);
    }
}
```

> _Attribute Observer methods are always supplied with the **model instance**, the **new attribute value** and the **previous attribute
value**, in that order._

### Events

You may observe all the typical CRUD events dispatched by Eloquent models during their lifecycles. Supported events are:
`creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`.

The naming convention to follow when defining attribute observer methods is: **on|AttributeName|Event**

So, let's say we want to check a user's email against a global spam/vanity mail list each time they attempt to update it,
we would implement that logic in an attribute observer method called `onEmailUpdating`.

> _Note that the attribute name must be in **[PascalCase](https://techterms.com/definition/pascalcase)** and the
> event name must be **[Capitalized](https://wikipedia.org/wiki/Capitalization)**._

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alex Stewart](https://github.com/alexstewartja)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Donations

I maintain this package in my spare time.
If it's beneficial to you, consider donating or buying me a coffee to keep it improving.

[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg?style=flat-square)](https://www.paypal.com/donate?business=iamalexstewart%40gmail.com&no_recurring=0&item_name=Laravel+Attribute+Observer&currency_code=USD)
[![Buy Me A Coffee](https://img.shields.io/badge/-buy_me_a%C2%A0coffee-orange?logo=buy-me-a-coffee)](https://www.buymeacoffee.com/asja)

