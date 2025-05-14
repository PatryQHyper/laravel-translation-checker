<?php
declare(strict_types=1);

return [

    /**
     * Root directory path for application language files.
     * The path should end with a trailing slash.
     */
    'languages_path' => base_path('lang/'),

    /**
     * List of locale codes to exclude from checking.
     * Example: ['pl', 'en']
     */
    'excluded_locales' => [],

    /**
     * List of file extensions to exclude from checking.
     * Example: ['php', 'json']
     */
    'excluded_file_extensions' => [],

    /**
     * Whether to check the contents of the lang/vendor directory.
     */
    'check_language_vendor_directory' => false,

    /**
     * Root directory path for published vendor language files.
     * The path should end with a trailing slash.
     */
    'vendor_language_directory_path' => base_path('lang/vendor/'),

    /**
     * List of specific vendor packages to include in the check.
     * This takes precedence over the excluded_vendor_packages setting.
     * Example: ['laravel-backup', 'laravel-health']
     */
    'allowed_vendor_packages' => [],

    /**
     * List of vendor packages to exclude from checking.
     * This has lower priority than the allowed_vendor_packages setting.
     */
    'excluded_vendor_packages' => [],

    /**
     * Class responsible for parsing language directories and extracting translation keys.
     * This class must extend \PatryQHyper\LaravelTranslationChecker\Parsers\DirectoryParser class.
     *
     * Default: \PatryQHyper\LaravelTranslationChecker\Parsers\DefaultDirectoryParser::class
     */
    'directory_parser_class' => \PatryQHyper\LaravelTranslationChecker\Parsers\DefaultDirectoryParser::class,

    /**
     * Should translation check put file with errors in storage/logs directory?
     * File name would look like: patryqhyper_laravel_translation_checker_error_log_DATE.log
     */
    'save_error_log_file' => true,

];
