<?php

/**
 * Class ACFBlock_Render
 */
class ACFBlockRender
{
    /**
     * ACFBlock_Render constructor.
     */
    public function __construct()
    {
        add_filter('render_block', [$this, 'init'], 10, 2);
    }

    public function init($prev_content = '', $block, $inner = false, $columns = false)
    {
        $this->block = $this->retrieveClass($block);

        print_r($this->block);
        var_dump($block_content);

        die();
        static $counter = 0;
        global $post;

        $counter++;

        // Remove annoying p tags
        if (isset($block['blockName']) && $block['blockName'] == 'core/paragraph' && trim(strip_tags($block['innerHTML'])) == '') {
            return false;
        }

        if (!trim($block_content) && empty($block['blockName'])) {
            return false;
        }

        $block_type = WP_Block_Type_Registry::get_instance()->get_registered($block['blockName']);
        $is_dynamic = $block['blockName'] && null !== $block_type && $block_type->is_dynamic();
        $block_content = '';
        $index = 0;
        $disable_inner_container = false;
        $align = '';
        $columnsBlocks = array('core/columns', 'coblocks/row');
        $overwriteBlocks = array('coblocks/row', 'coblocks/column');

        // sets inner--small container for specified blocks
        $forceInnerSmall = array('core/paragraph', 'core/list', 'core/heading', 'core/image', 'acf/block-accordion-acf', 'acf/block-teaser-image-acf', 'acf/block-button-acf', 'acf/block-form-acf');

        // sets inner--small as default for some
        $forceSmallDefault = array('coblocks/row', 'core-embed/youtube');

        // removed inner container for specified blocks
        $forceNoContainer = array('acf/spacer');

        $defaultInner = 'inner--medium';

        if (isset($block['blockName']))
        {
            // Getting block type and slug
            list($type, $slug) = explode('/', $block['blockName']);
        } else {
            $type = 'custom';
            $slug = 'content';
        }

        // Set Default Inner
        $container = $defaultInner;
        $extra_class = '';
        $enable_ids = isset($block['attrs']['section_id']) ? $block['attrs']['section_id'] : $slug.'-'.$counter;
        $width = isset($block['attrs']['width']) && $block['attrs']['width'] ? "--width:".$block['attrs']['width']."%" : false;

        // Check block attributes for align's
        if (isset($block['attrs']) && isset($block['attrs']['align'])) {
            $align_type = $block['attrs']['align'];

            switch ($align_type) {
                case 'full':
                    $container = 'inner--full';
                    break;

                case 'wide' :
                    $container = 'inner--medium';
                    break;

                case 'center':
                    $align = ' align-center';
                    break;

                case 'left':
                    $align = ' align-left';
                    break;

                case 'right':
                    $align = ' align-right';
                    break;
            }
        }

        if (isset($block['className'])) {
            $extra_class .= ' ' . $block['className'];
        }

        if (isset($block['attrs']['id']) && $background_color = get_field('block_background_color', $block['attrs']['id']))
        {
            $extra_class .= ' has-background--' . $background_color;
        }

        // coblocks specific
        if ($block['blockName'] === 'coblocks/row') {
            // Print extra classes depending on the blocks settings
            isset($block['attrs']['align']) && $extra_class .= ' coblocks--align-' . $block['attrs']['align'];
            isset($block['attrs']['gutter'])
                ? $extra_class .= ' coblocks--gutter-' . $block['attrs']['gutter']
                : $extra_class .= ' coblocks--gutter-medium'; // Settings default value
            isset($block['attrs']['alignment'])
                ? $extra_class .= ' coblocks--alignment-' . $block['attrs']['alignment']
                : $extra_class .= ' coblocks--alignment-stretch'; // Settings default value
            isset($block['attrs']['breakpoint'])
                ? $extra_class .= ' coblocks--breakpoint-' . $block['attrs']['breakpoint']
                : $extra_class .= ' coblocks--breakpoint-small'; // Settings default value
        }

        if (in_array($block['blockName'], $columnsBlocks) || $columns) {
            $columns = true;
        }

        // If the alignment isn't defined, the default size should be "small"
        if (in_array($block['blockName'], $forceSmallDefault)) {
            !isset($block['attrs']['align']) && $container = 'inner--small';
        }

        // sets inner--small container around
        if (in_array($block['blockName'], $forceInnerSmall)) {
            $container = 'inner--small';
        }

        // removes inner wrapper for spacer component
        if (in_array($block['blockName'], $forceNoContainer)) {
            $container = '';
        }

        foreach ($block['innerContent'] as $chunk) {

            if ($columns || in_array($block['blockName'], array('stheme/advanced-image'))) {
                $disable_inner_container = true;
            }

            $block_content .= is_string($chunk) ? $chunk : render_gutenberg_blocks(true, $block['innerBlocks'][$index++], $disable_inner_container, $columns);
        }

        if ($is_dynamic) {
            $global_post = $post;
            $block_content = $block_type->render($block['attrs'], $block_content);
            $post = $global_post;
        }

        if ($inner) {
            $container = false;
        }

        if (in_array($block['blockName'], $overwriteBlocks)) {

            $parameters = array_merge($block['attrs'], [
                'content' => $block_content
            ]);

            $block_content = template('blocks.block-' . $slug . '-' . $type, $parameters);
        }

        return \App\template('blocks.block-container', [
            'block' => $block,
            'content' => $block_content,
            'container' => $container,
            'type' => $type,
            'slug' => $slug,
            'class' => $extra_class,
            'align' => $align,
        ]);
    }

    /**
     * @param array $block
     * @return object
     */
    private function retrieveClass($block = array())
    {
        list($namespace, $class_name) = explode('/', $block['blockName']);

        $namespace = ucfirst($namespace);
        $class_name = ucfirst($class_name);

        $class = "\\" . ACFBLOCK_SAGE_NAMESPACE . "\\Blocks\\$namespace\\$class_name";

        if (! class_exists($class)) {
            $class = "\\Prophe1\\ACFBlock\\$namespace\\$class_name";
        }

        return new $class($block);
    }

//    private function block_container()
//    {
//        $rendered = sprintf(
//            '<div class="%1$s %2$s-%1$s %2$s-%1$s--%3$s%4$s%5$s"%7$s>%6$s</div>',
//            $container_prefix,
//            $provider,
//            $block_slug,
//            $class,
//            $alignment,
//            $content,
//        );
//
//        return $rendered;
//    }
}

new ACFBlockRender();
