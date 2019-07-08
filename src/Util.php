<?php

declare(strict_types=1);

namespace Prophe1\ACFBlockz;

/**
 * Class Util
 *
 * @package Prophe1\ACFBlocks
 */
class Util
{

    /**
     * @param string $string
     * @return string
     */
    public static function camelToKebab(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

    /**
     * @param array $classes
     * @return string
     */
    public static function sanitizeHtmlClasses(array $classes): string
    {
        return implode(' ', array_map(function ($class) : string {
            return sanitize_html_class((string)$class);
        }, $classes));
    }
}
