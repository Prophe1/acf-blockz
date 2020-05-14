<?php

declare(strict_types=1);

namespace Prophe1\ACFBlockz;

/**
 * Class Block
 *
 * @package Prophe1\ACFBlockz
 */
class Block
{
    /**
     * The directory name of the block.
     *
     * @since 0.1.0
     * @var string $name
     */
    protected $name = '';

    /**
     * The display name of the block.
     *
     * @since 0.1.0
     * @var string $title
     */
    protected $title = '';

    /**
     * The description of the block.
     *
     * @since 0.1.0
     * @var string $description
     */
    protected $description;

    /**
     * The category this block belongs to.
     *
     * @since 0.1.0
     * @var string $category
     */
    protected $category;

    /**
     * The icon of this block.
     *
     * @since 0.1.0
     * @var string $icon
     */
    protected $icon = '';

    /**
     * An array of keywords the block will be found under.
     *
     * @since 0.1.0
     * @var $keywords array
     */
    protected $keywords = [];

    /**
     * An array of Post Types the block will be available to.
     *
     * @since 0.1.0
     * @var $post_types array
     */
    protected $post_types = [];

    /**
     * The default display mode of the block that is shown to the user.
     *
     * @since 0.1.0
     * @var $mode string
     */
    protected $mode = 'preview';

    /**
     * The block alignment class.
     *
     * @since 0.1.0
     * @var $align string
     */
    protected $align = '';

    /**
     * Features supported by the block.
     *
     * @since 0.1.0
     * @var $supports array
     */
    protected $supports = [];

    /**
     * The blocks directory path.
     *
     * @since 0.1.0
     * @var $dir string
     */
    public $dir;

    /**
     * The blocks accessibility.
     *
     * @since 0.1.0
     * @var $enabled bool
     */
    protected $enabled = true;

    /**
     * Block constructor.
     *
     * @param $settings array
     * @throws \ReflectionException
     */
    public function __construct(array $settings)
    {
        // Path related definitions.
        $reflection     = new \ReflectionClass($this);
        $block_path     = $reflection->getFileName();
        $this->name     = Util::camelToKebab(basename($block_path, '.php'));

        // Replace default values with new ones
        $settings = array_replace($this->getDefaultSettings(), $settings);

        // Allow user to filter values
        $settings = apply_filters('acfblocks/block_settings', $settings, $this->name);

        // User definitions.
        $this->enabled     = $settings['enabled'];
        $this->icon        = $settings['icon'];
        $this->dir         = $settings['dir'];
        $this->title       = $settings['title'];
        $this->description = $settings['description'];
        $this->category    = $settings['category'];
        $this->icon        = $settings['icon'];
        $this->supports    = $settings['supports'];
        $this->post_types  = $settings['post_types'];
        $this->align       = $settings['align'];
        $this->mode        = $settings['mode'];

        // Set ACF Fields to the block.
        $this->fields = $this->registerFields();
    }

    /**
     * Is the block enabled?
     *
     * @since 0.1.0
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * User defined ACF fields
     *
     * @since 0.1.0
     * @return array
     */
    protected function registerFields(): array
    {
        return [];
    }

    /**
     * Get Default Settings
     *
     * @return array
     */
    public function getDefaultSettings(): array
    {
        return [
            'icon'          => apply_filters('acfblocks/default_icon', 'admin-generic'),
            'dir'           => '',
            'enabled'       => true,
            'supports'      => array(),
            'align'         => false,
            'mode'          => 'preview'
        ];
    }

    /**
     * Get the block ACF fields
     *
     * @since 0.1.0
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the block name
     *
     * @since 0.1.0
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the block title
     *
     * @since 0.1.0
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the block description
     *
     * @since 0.1.0
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the block category
     *
     * @since 0.1.0
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Get the block icon
     *
     * @since 0.1.0
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the block keywords
     *
     * @since 0.1.0
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * Get the block post types
     *
     * @since 0.1.0
     * @return array
     */
    public function getPostTypes(): ?array
    {
        return $this->post_types;
    }

    /**
     * Get the block mode
     *
     * @since 0.1.0
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get the block alignment
     *
     * @since 0.1.0
     * @return string|bool
     */
    public function getAlignment()
    {
        return $this->align;
    }

    /**
     * Get featured supported by the block
     *
     * @since 0.1.0
     * @return array
     */
    public function getSupports(): array
    {
        return $this->supports;
    }

    /**
     * Get the block registration data
     *
     * @since 0.1.0
     * @return array
     */
    public function getBlockData(): array
    {
        return [
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'category' => $this->getCategory(),
            'icon' => $this->getIcon(),
            'keywords' => $this->getKeywords(),
            'post_types' => $this->getPostTypes(),
            'mode' => $this->getMode(),
            'align' => $this->getAlignment(),
            'supports' => $this->getSupports(),
        ];
    }

    /**
     * Initialize Block
     *
     * @since 0.1.0
     * @return void
     */
    public function init(): void
    {
        $block_data = $this->getBlockData();
        $block_data['render_callback'] = [$this, 'renderBlockCallback'];
        $fields = $this->getFields();

        acf_register_block($block_data);

        if (! empty($fields)) {
            acf_add_local_field_group($fields);
        }
    }
}
