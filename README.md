# Sofa/Hookable

[![GitHub Tests Action Status](https://github.com/jarektkaczyk/hookable/workflows/Tests/badge.svg)](https://github.com/jarektkaczyk/hookable/actions?query=workflow%3Atests+branch%3Amaster) [![stable](https://poser.pugx.org/sofa/hookable/v/stable.svg)](https://packagist.org/packages/sofa/hookable) [![Downloads](https://poser.pugx.org/sofa/hookable/downloads)](https://packagist.org/packages/sofa/hookable)

This is a overhaul of the Hookable system from jarektkaczyk/hookable to support laravel 10 and make the system leaner.

Hooks system for the [Eloquent ORM (Laravel 10.0)](https://laravel.com/docs/5.2/eloquent).

Hooks are available for the following methods:

* `Model::getAttribute`
* `Model::setAttribute`
* `Model::save`
* `Model::toArray`
* `Model::replicate`
* `Model::isDirty`
* `Model::__isset`
* `Model::__unset`

and all methods available on the `Illuminate\Database\Eloquent\Builder` class.

## Installation

Clone the repo or pull as composer dependency:

```
composer require masterbroki/hookable:~10.0
```

## Usage

Use the hookable trait on the model:

```php
class MyModel extends \Illuminate\Database\Eloquent\Model{
    use Sofa\Hookable\Hookable;
}
```

In order to register a hook, use the static method `hook` on the model:

```php
MyModel::hook('myMethod', function($myParams){
    return "I want to select: " . implode(', ', $myParams);
});
```

And when the attribute is called:
```php
$result = MyModel::select(["first", "second"]);

// $result will equal "I want to select: first, second"
```

## Contribution

All contributions are welcome, PRs must be **tested**.
