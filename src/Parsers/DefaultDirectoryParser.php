<?php
declare(strict_types=1);

namespace PatryQHyper\LaravelTranslationChecker\Parsers;

class DefaultDirectoryParser extends DirectoryParser
{
    public function parse(?string $directory = null, ?string $currentLocale = null): void
    {
        if (!$directory) {
            $this->loadLocales();
            return;
        }

        if($currentLocale === 'vendor') {
            $newLocales = [];
            foreach ($this->locales as $locale) {
                if($locale === 'vendor') {
                    continue;
                }

                $newLocales[] = $locale;
            }
            $this->locales = $newLocales;
        }

        if ($currentLocale === 'vendor' && !config('laravel-translation-checker.check_language_vendor_directory')) {
            return;
        }

        if ($currentLocale === 'vendor' && config('laravel-translation-checker.check_language_vendor_directory')) {
            $vendorProjects = scandir($directory);

            foreach ($vendorProjects as $vendorProject) {
                if (in_array($vendorProject, array_merge(['.', '..', '.DS_Store'], config('laravel-translation-checker.excluded_vendor_packages', [])))) {
                    continue;
                }

                if (count(config('laravel-translation-checker.allowed_vendor_packages', [])) && !in_array($vendorProject, config('laravel-translation-checker.allowed_vendor_packages'))) {
                    continue;
                }

                $vendorProjectLanguages = scandir($directory . $vendorProject . '/');
                $projectLanguages = [];


                foreach ($vendorProjectLanguages as $vendorProjectLanguage) {
                    if (in_array($vendorProjectLanguage, ['.', '..', '.DS_Store'])) {
                        continue;
                    }

                    if (in_array($vendorProjectLanguage, $this->locales)) {
                        $projectLanguages[] = $vendorProjectLanguage;
                    }
                }

                foreach ($projectLanguages as $projectLanguage) {
                    $this->parseDirectory($directory . $vendorProject . '/' . $projectLanguage . '/', $projectLanguage);
                }
            }
            return;
        }

        $this->parseDirectory($directory, $currentLocale);
    }

    private function loadLocales(): void
    {
        $files = scandir(config('laravel-translation-checker.languages_path'));

        foreach ($files as $file) {
            if (in_array($file, ['.', '..', '.DS_Store'])) {
                continue;
            }

            if (str($file)->endsWith('.json')) {
                $this->locales[] = str($file)->chopEnd('.json');
                //TODO: .json file support
                throw new \Exception('Json files are not supported yet.');

                continue;
            }

            if (!is_dir(config('laravel-translation-checker.languages_path') . $file)) {
                continue;
            }

            $this->locales[] = $file;
        }

        $this->locales = array_filter($this->locales, function ($locale) {
            return !in_array($locale, config('laravel-translation-checker.excluded_locales'));
        });

        $this->parseLocales();
    }

    private function parseLocales(): void
    {
        foreach ($this->locales as $locale) {
            $this->parse(config('laravel-translation-checker.languages_path') . $locale . '/', $locale);
        }
    }

    private function parseDirectory(string $directory, string $currentLocale): void
    {
        $data = scandir($directory);
        $dirWithoutBasePath = str_replace(config('laravel-translation-checker.languages_path') . $currentLocale, '', $directory);
        if (str($dirWithoutBasePath)->contains(config('laravel-translation-checker.languages_path') . 'vendor/')) {
            $dirWithoutBasePath = str_replace(config('laravel-translation-checker.languages_path') . 'vendor/', '', $dirWithoutBasePath);
            [$project, $locale, $rest] = explode('/', $dirWithoutBasePath, 3);
            $dirWithoutBasePath = str_replace('/' . $locale . '/', '/', 'vendor/'.$dirWithoutBasePath);
        }


        foreach ($data as $item) {
            if (in_array($item, ['.', '..', '.DS_Store'])) {
                continue;
            }

            if (is_dir($directory . $item)) {
                $this->parseDirectory($directory . $item . '/', $currentLocale);
            } else {
                if (str($item)->endsWith(['.php', '.json'])) {
                    $keys = $this->parseFileToArrayRepresentation($directory . $item);

                    foreach ($keys as $key) {
                        $this->keys[$currentLocale][] = $dirWithoutBasePath . $item . '.' . $key;
                    }
                }
            }
        }
    }

    private function parseFileToArrayRepresentation(string $path): ?array
    {
        $fileContent = null;
        if (str($path)->endsWith('.json')) {
            $fileContent = json_decode(file_get_contents($path), true);
        } else {
            $fileContent = include $path;
        }

        if (!is_array($fileContent)) {
            return null;
        }

        return $this->flattenFileKeys($fileContent);
    }

    private function flattenFileKeys(array $array, string $prefix = ''): array
    {
        $keys = [];

        foreach ($array as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $keys = array_merge($keys, $this->flattenFileKeys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }

        return $keys;
    }
}
