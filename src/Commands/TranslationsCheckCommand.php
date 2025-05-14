<?php
declare(strict_types=1);

namespace PatryQHyper\LaravelTranslationChecker\Commands;

use Illuminate\Console\Command;

class TranslationsCheckCommand extends Command
{
    protected $signature = 'translations:check';

    protected $description = 'Command description';

    public function handle(): void
    {
        $this->components->info('Fetching directories, files and translation keys...');
        $parser = app(config('laravel-translation-checker.directory_parser_class'));

        $parser->parse();

        $this->components->success('Successfully fetched directories, files and translation keys.');

        $this->components->info('Checking for missing translation keys...');

        $missingKeys = [];

        $allKeys = array_unique(array_merge(...array_values($parser->getKeys())));

        foreach ($parser->getKeys() as $locale => $translationKeys) {
            $diff = array_diff($allKeys, $translationKeys);
            if (!empty($diff)) {
                $missingKeys[$locale] = array_values($diff);
            }
        }

        foreach ($missingKeys as $locale => $keys) {
            if (count($keys) > 0) {
                $this->components->error(sprintf('Found %d translation keys missing for locale %s', count($keys), $locale));
            }
        }

        if (count($missingKeys) > 0) {
            if (config('laravel-translation-checker.save_error_log_file', true)) {
                file_put_contents(storage_path(sprintf('logs/patryqhyper_laravel_translation_checker_error_log_%s.log', today()->format('Y-m-d_H_i_s'))), sprintf('PatryQHyper/LaravelTranslationChecker error log. Date %s', now()->format('Y-m-d H:i:s')) . PHP_EOL . PHP_EOL . json_encode($missingKeys, JSON_PRETTY_PRINT));
            }

            $this->components->twoColumnDetail('Key', 'Locale');
            foreach ($missingKeys as $locale => $keys) {
                foreach ($keys as $key) {
                    $this->components->twoColumnDetail($key, $locale);
                }
            }

            return;
        }

        $this->components->success('Missing translation keys not found');
    }
}
