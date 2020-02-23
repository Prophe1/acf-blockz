<?php

namespace Prophe1\ACFBlockz\Blocks;

use WP_Block_Type_Registry;
use function App\template;

/**
 * Class Blocks
 * @package App\Helpers\Gutenberg
 */
class Content
{
    /**
     * The id of the block
     *
     * @var string
     */
    private $id;

    /**
     * The default container for a block
     *
     * @var string
     */
    private $container;

    /**
     * The default containers for a blocks
     *
     * @var array
     */
    private $containers;

    /**
     * Whether or not we should disable the inner container
     *
     * @var bool
     */
    private $disable_inner_container = false;

    /**
     * The WP block type instance
     *
     * @var \WP_Block_Type|null
     */
    private $block_type;

    /**
     * @var int
     */
    private $block_index = 0;

    /**
     * Block alignment
     *
     * @var string
     */
    private $block_alignment = '';

    /**
     * Block classes
     *
     * @var array
     */
    private $block_classes = [];

    /**
     * The content of a block
     *
     * @var string
     */
    private $block_content;

    /**
     * The block attributes
     *
     * @var array
     */
    private $block;

    /**
     * The block inner
     * @var string
     */
    private $inner;

    /**
     * The blocks columns
     *
     * @var string
     */
    private $columns;

    /**
     * Is the block dynamic
     *
     * @var bool
     */
    private $is_dynamic = false;

    /**
     * The block type
     *
     * @var string
     */
    private $type;

    /**
     * The block slug
     *
     * @var string
     */
    private $slug;

    /**
     * The block width
     *
     * @var string
     */
    private $width;

    /**
     * The block counter
     * Used for the id
     *
     * @var int
     */
    public static $counter = 0;

    /**
     * @var array
     */
    private $containerClasses;

    /**
     * @var array
     */
    private $alignmentClasses;

    /**
     * Blocks constructor.
     */
    public function __construct()
    {
        $this->containerClasses = apply_filters('content/containerClasses', [
            'sm' => 'inner--small',
            'md' => 'inner--medium',
            'full' => 'inner--full'
        ]);

        $this->alignmentClasses = apply_filters('content/alignmentClasses', [
            'center' => 'align-center',
            'left' => 'align-left',
            'right' => 'align-right'
        ]);

        $containers = apply_filters('content/render', [
            'default_inner' => $this->getContainerClass('md'),
            'inner_small' => array(),
            'no_container' => array(),
            'small_default' => array()
        ]);

        $this->setContainers($containers);
        $this->setContainer($this->getContainers('default_inner'));
        $this->block_type = WP_Block_Type_Registry::get_instance();
    }

    /**
     * @param $position string
     * @return string
     */
    private function getAlignmentClass($position): string
    {
        return $this->alignmentClasses[$position];
    }

    /**
     * @param $size string
     * @return string
     */
    private function getContainerClass($size): string
    {
        return $this->containerClasses[$size];
    }

    /**
     * @param string $key
     * @return array|string
     */
    public function getContainers(string $key)
    {
        return $this->containers[$key];
    }

    /**
     * @return string
     */
    public function getContainer(): string
    {
        return $this->container;
    }

    /**
     * @param  string  $container
     */
    public function setContainer(string $container): void
    {
        $this->container = $container;
    }

    /**
     * @param  string  $container
     */
    public function setContainers(array $containers): void
    {
        $this->containers = $containers;
    }

    /**
     * Checks if the block is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return (!trim($this->block_content) && empty($this->block['blockName'])) || (!trim($this->block_content) && empty($this->block['blockName']));
    }

    /**
     * Checks if the block is dynamic and sets it
     */
    private function isblockDynamic()
    {
        $this->is_dynamic = $this->block['blockName'] && null !== $this->block_type && $this->block_type->is_dynamic();
    }

    /**
     * Checks for empty paragraph tags
     *
     * @return bool
     */
    private function removeEmptyParagraphTags()
    {
        return isset($this->block['blockName']) && $this->block['blockName'] == 'core/paragraph' && trim(strip_tags($this->block['innerHTML'])) == '';
    }

    /**
     * Setup the block
     *
     * @return $this
     */
    private function setupblock()
    {
        $this->block_type = WP_Block_Type_Registry::get_instance()->get_registered($this->block['blockName']);
        $this->isblockDynamic();
        return $this;
    }

    /**
     * Set the type and slug for the block
     *
     * @return $this
     */
    private function setTypeAndSlug()
    {
        if (isset($this->block['blockName'])) {
            // Getting block type and slug
            list($type, $slug) = explode('/', $this->block['blockName']);
        } else {
            $type = 'custom';
            $slug = 'content';
        }

        $this->type = $type;
        $this->slug = $slug;
        return $this;
    }

    /**
     * Set the ID
     *
     * @return $this
     */
    private function setId()
    {
        self::$counter++;
        // Set Default Inner
        $this->id = isset($block['attrs']['section_id']) ? $this->block['attrs']['section_id'] : $this->slug . '-' . self::$counter;
        return $this;
    }

    /**
     * Set the width
     *
     * @return $this
     */
    private function setWidth()
    {
        // Set Default Inner
        $this->width = isset($block['attrs']['width']) && $this->block['attrs']['width'] ? "--width:" . $this->block['attrs']['width'] . "%" : false;

        return $this;
    }

    /**
     * Overwrites the default container
     *
     * @return $this
     */
    private function setBlockContainer()
    {
        // sets inner--small container around
        if (in_array($this->block['blockName'], $this->getContainers('inner_small'))) {
            $this->setContainer($this->getContainerClass('sm'));
        }

        // removes inner wrapper for spacer component
        if (in_array($this->block['blockName'], $this->getContainers('no_container'))) {
            $this->setContainer('');
        }

        if ($this->inner) {
            $this->setContainer(false);
        }

        return $this;
    }

    /**
     * Sets the block alignment
     *
     * @return $this
     */
    private function setBlockAlignment()
    {
        // Set the block container to small then allow overrides with alignment options
        if (in_array($this->block['blockName'], $this->getContainers('small_default'), true)) {
            $this->setContainer($this->getContainerClass('sm'));
        }

        if (isset($this->block['attrs']['align'])) {
            switch ($this->block['attrs']['align']) {
                case 'full':
                    $this->setContainer($this->getContainerClass('full'));
                    break;

                case 'wide':
                    $this->setContainer($this->getContainerClass('md'));
                    break;

                case 'center':
                    $this->block_alignment = sprintf(' %s', $this->getAlignmentClass('center'));
                    break;

                case 'left':
                    $this->block_alignment = sprintf(' %s', $this->getAlignmentClass('left'));
                    break;

                case 'right':
                    $this->block_alignment = sprintf(' %s', $this->getAlignmentClass('right'));
                    break;
            }
        }

        // Force inner--small
        if (in_array($this->block['blockName'], $this->getContainers('inner_small'), true)) {
            $this->setContainer($this->getContainerClass('sm'));
        }

        return $this;
    }

    /**
     * Adds a background class to a block
     *
     * @return $this
     */
    private function setBackground()
    {
        if (isset($this->block['attrs']['id']) && $background_color = get_field('block_background_color',
                $this->block['attrs']['id'])) {
            $this->block_classes[] = ' has-background--' . $background_color;
        }

        return $this;
    }

    /**
     * Adds any custom classes to our block
     *
     * @return $this
     */
    private function setBlockClasses()
    {
        if (isset($this->block['attrs']['className'])) {
            $this->block_classes[] = $this->block['attrs']['className'];
        }
        return $this;
    }

    /**
     *
     */
    private function setBlockContent()
    {
        foreach ($this->block['innerContent'] as $chunk) {
            $this->block_content = is_string($chunk) ? $chunk : \render_gutenberg_blocks(true,
                $this->block['innerBlocks'][$this->block_index++], $this->disable_inner_container, $this->columns);
        }

        if ($this->is_dynamic) {
            global $post;
            $global_post = $post;
            $this->block_content = $this->block_type->render($this->block['attrs'], $this->block_content);
            $post = $global_post;
        }
    }


    /**
     * @param  string  $block_content
     * @param  array  $block
     * @param  bool  $inner
     * @param  bool  $columns
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\Factory|\Illuminate\View\View
     */
    public static function render($block_content = '', array $block, $inner = false, $columns = false)
    {
        $content = new Content();

        $content->block_content = $block_content;
        $content->block = $block;
        $content->inner = $inner;
        $content->columns = $columns;

        if ($content->isEmpty() || $content->removeEmptyParagraphTags()) {
            return false;
        }

        $content->setupBlock()
            ->setTypeAndSlug()
            ->setId()
            ->setWidth()
            ->setBlockContainer()
            ->setBlockAlignment()
            ->setBackground()
            ->setBlockClasses()
            ->setBlockContent();


        return template(
            'blocks.block-container',
            [
                'block'     => $content->block,
                'content'   => $content->block_content,
                'container' => $content->getContainer(),
                'type'      => $content->type,
                'slug'      => str_replace(['acf-'], '', $content->slug),
                'class'     => implode(' ', $content->block_classes),
                'align'     => $content->block_alignment,
                'ids'       => $content->id,
                'width'     => $content->width
            ]
        );
    }
}
