<?php
declare(strict_types=1);

namespace PatryQHyper\LaravelTranslationChecker\Parsers;

abstract class DirectoryParser
{
    /**
     * List of found translation keys
     *
     * @var array
     */
    protected array $keys = [];

    /**
     * List of found locales
     *
     * @var array
     */
    protected array $locales = [];

    abstract public function parse(?string $directory = null): void;

    public function getKeys(): array
    {
        return $this->keys;
    }
}
