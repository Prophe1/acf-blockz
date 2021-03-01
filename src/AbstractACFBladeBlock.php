<?php

declare(strict_types=1);

namespace Prophe1\ACFBlockz;

use function App\template;

/**
 * Class AbstractBladeBlock
 *
 * @package Itineris\AcfGutenblocks
 */
abstract class AbstractACFBladeBlock extends AbstractBladeBlock
{
    /**
     * Block acf parameters
     *
     * @var array
     */
    protected $acf;

    /**
     * We take all block fields and make them available as variables
     *
     * @param array $block
     */
    public function renderBlockCallback(array $block): void
    {
        $frontend = apply_filters(
            'acfblocks/render_block_frontend_path',
            "$this->dir/blocks/{$this->getName()}",
            $this
        );

        $this->acf = get_fields();

        // Erase acf pre_load after assigning fields to main class
        if (isset($block['id'])) {
            acf_reset_meta($block['id']);
        }

        // Pass block className with block parameters
        if (isset($block['className'])) {
            $this->acf['className'] = $block['className'];
        }

        // Overwrite controller key if this name was taken
        $this->acf['controller'] = $this;

        echo template($frontend, $this->acf);
    }
}
