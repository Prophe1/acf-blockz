<?php
/**
 * Plugin Name: ACFBlockz
 * Plugin URI:  https://github.com/Prophe1/acf-blockz
 * Description: OOP way to add Advanced Custom Blocks.
 * Author:      Prophe1
 * Author URI:  https://github.com/Prophe1/
 * Version:     0.1.0
 */

namespace Prophe1\ACFBlockz;

/**
 * Class ACFBlockz
 *
 * @since 0.1.0
 * @package Prophe1\ACFBlockz
 */
class ACFBlockz
{
    /**
     * ACFBlockz constructor.
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        $this->constants();
        $this->autoloader();
        $this->init();

        add_action('acf/init', [$this, 'init']);
    }

    /**
     * Define constants
     *
     * @since 0.1.0
     */
    private function constants()
    {
        $this->define("ACFBLOCK_SAGE_NAMESPACE", "App");
        $this->define("ACFBLOCK_DIR", plugin_dir_path(__FILE__));
    }

    /**
     * Define constants if doesn't exists
     *
     * @since 0.1.0
     * @param      $name
     * @param bool $value
     */
    private function define($name, $value = true)
    {
        if (! defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Include required files
     *
     * @since 0.1.0
     */
    private function autoloader()
    {
        if (file_exists(ACFBLOCK_DIR . '/vendor/autoload.php')) {
            require_once ACFBLOCK_DIR . '/vendor/autoload.php';
        }
    }

    /**
     * Initialize blocks
     *
     * @since 0.1.0
     */
    public function init()
    {
        $blocks = apply_filters('acfblocks/blocks', []);

        if (empty($blocks)) {
            return;
        }

        $loader = new Loader();

        foreach ($blocks as $block) {
            $loader->add($block);
        }

        $loader->init();
    }
}

new ACFBlockz();
