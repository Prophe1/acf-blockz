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
     * Autoload with composer autoloader or spl
     *
     * @since 0.1.0
     */
    private function autoloader()
    {
        if (file_exists(ACFBLOCK_DIR . '/vendor/autoload.php'))
        {
            require_once ACFBLOCK_DIR . '/vendor/autoload.php';
        } else {
            spl_autoload_register([$this, 'autoload']);
        }
    }

    /**
     * This function is called by spl_autoload_register function it loads classes
     *
     * @param $class
     */
    private function autoload($class): void
    {
        // project-specific namespace prefix
        $prefix = __NAMESPACE__;

        // base directory for the namespace prefix
        $base_dir = __DIR__ . '/src/';

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relative_class = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    }

    /**
     * Initialize blocks
     *
     * @since 0.1.0
     */
    public function init(): void
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
