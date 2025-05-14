<?php
declare(strict_types=1);

namespace PatryQHyper\LaravelTranslationChecker;

use PatryQHyper\LaravelTranslationChecker\Commands\TranslationsCheckCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelTranslationCheckerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-translation-checker')
            ->hasConfigFile('laravel-translation-checker')
            ->hasCommand(TranslationsCheckCommand::class);
    }
}
