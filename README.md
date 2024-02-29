# InsightDumper

InsightDumper is a PHP debugging tool designed to provide developers with a clear and detailed view of their data during development. Combining advanced variable dumping features with a colorful and intuitive visual presentation, InsightDumper transforms the way developers interact with their data, making the debugging process not just more efficient but also enjoyable.

## Key Features

- **Enhanced Visualization**: Enjoy a colorful and structured display of data, making the inspection of complex arrays, objects, and other PHP data types straightforward and direct.
- **Seamless Integration**: Designed for effortless integration into any PHP project, whether you're using a specific framework like Laravel or Symfony, or operating in a pure PHP environment.
- **Rich Functionality**: From straightforward variable dumping to advanced features like execution tracing and performance profiling, InsightDumper is equipped to meet all your debugging needs.
- **Customization**: Tailor the display and behavior of InsightDumper to perfectly match your debugging workflow and preferences.

Whether you're a solo developer working on your passion project or a team building a large-scale PHP application, InsightDumper is here to illuminate your debugging process with clarity, color, and deep insight into your data.

## Installation

To get started with InsightDumper, install it via Composer:

```bash
composer require pixelee/insight-dumper
```

## Usage

### Standard PHP Project

1. **Setup**: Include Composer's autoload file and the `in()` function file in your project.

```php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/vendor/pixelee/insight-dumper/Resources/functions/in.php';
```

2. **Variable Dumping**: Use the `in()` function to dump variables for debugging.

```php
$data = ['name' => 'John', 'age' => 30];
in($data);
```

### PHP MVC Frameworks

#### Laravel

- **Service Provider and Facade (Optional)**: Optionally, register InsightDumper as a service provider and create a facade for an elegant syntax.
- **Usage**: Call `in()` within your application to debug data effortlessly.

```php
// In a controller method
$users = User::all();
in($users);
```

#### Symfony

- **Service Configuration**: Optionally, configure InsightDumper as a service in `services.yaml`.
- **Usage**: Use `in()` in controllers or services for debugging.

```php
// In a controller action
$users = $this->userRepository->findAll();
in($users);
```
