<?php

declare(strict_types=1);

namespace Prophe1\ACFBlockz;

/**
 * Class AbstractBlock
 *
 * @package Prophe1\ACFBlockz
 */
abstract class AbstractBlock extends Block implements InitializableInterface
{

    /**
     * @return string
     */
    public function fileExtension(): string
    {
        return '.php';
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * @param array $block
     */
    public function renderBlockCallback(array $block): void
    {
        $frontend = apply_filters(
            'acfblocks/render_block_frontend_path',
            "{$this->dir}/blocks/{$this->getName()}{$this->fileExtension()}",
            $this
        );

        if (file_exists($frontend)) {
            $path = $frontend;
        } else {
            $path = locate_template($frontend);
        }

        if (empty($path)) {
            return;
        }

        $block['slug'] = str_replace('acf/', '', $block['name']);
        $block['classes'] = implode(' ', [
            $block['slug'],
            $block['className'] ?? '',
            $block['align'] ?? '',
        ]);

        $controller = $this;

        ob_start();

        include apply_filters('acfblocks/render_block_html', $path, $controller);

        $html = ob_get_clean();

        echo apply_filters('acfblocks/render_block_html_output', $html, $controller);
    }
}
