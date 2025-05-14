# Laravel-translation-checker

Library to show all missing translations within your Laravel project.

## Installation
```
composer require patryqhyper/laravel-translation-checker
```

## Publish config
```
php artisan vendor:publish --tag=translation-checker-config
```

## Usage
```
php artisan translations:check
```

This command will return in terminal missing translations for specific locales.

If you didn't disable `save_error_log_file` option in config, it will also save error log in storage/logs directory.

## Important information
There is no support for .json files yet. Only directory-based translations are supported. Feel free to contribute.
