<?php

declare(strict_types=1);

namespace Prophe1\ACFBlockz;

use function App\template;

/**
 * Class AbstractBladeBlock
 *
 * @package Itineris\AcfGutenblocks
 */
abstract class AbstractBladeBlock extends Block implements InitializableInterface
{

    /**
     * @return string
     */
    public function fileExtension(): string
    {
        return '.blade.php';
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return function_exists('\App\template');
    }

    /**
     * @param array $block
     */
    public function renderBlockCallback(array $block): void
    {
        $frontend = apply_filters(
            'acfblocks/render_block_frontend_path',
            "$this->dir/blocks/{$this->getName()}",
            $this
        );

        $block['slug'] = str_replace('acf/', '', $block['name']);
        $block['classes'] = Util::sanitizeHtmlClasses([
            $block['slug'],
            $block['className'] ?? '',
            $block['align'] ?? '',
        ]);

        echo template($frontend, [
            'block' => $block,
            'controller' => $this,
        ]);
    }
}
