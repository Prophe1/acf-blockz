<?php

declare(strict_types=1);

namespace Prophe1\ACFBlockz;

/**
 * Interface InitializableInterface
 *
 * @package Prophe1\ACFBlockz
 */
interface InitializableInterface
{
    public function fileExtension(): string;

    public function isValid(): bool;

    public function renderBlockCallback(array $block): void;

    public function init(): void;
}
