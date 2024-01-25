<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/islamBelabbes
 * @since             1.0.0
 * @package           lazy blocks auto assets addon
 *
 * @wordpress-plugin
 * Plugin Name:       lazy blocks auto assets - addon
 * Plugin URI:        https://github.com/islamBelabbes
 * Description:       a plugin to help add custom css and js to lazy blocks
 * Version:           1.0.0
 * Author:            islam belabbes
 * Author URI:        https://github.com/islamBelabbes/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lazy-blocks-auto-assets-addon
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


class lazy_BlockAssetManager {

    private $blockName;

    public function __construct($blockName) {

        $this->blockName = $blockName;

        // Hook callbacks to the appropriate filters
        add_filter("lazyblock/{$this->blockName}/frontend_callback", array($this, 'frontendCallback'), 10, 2);
        add_filter("lazyblock/{$this->blockName}/editor_callback", array($this, 'editorCallback'), 10, 2);
    }

    public function frontendCallback($output) {
        $this->enqueueScripts();
        $this->enqueueStyles();
        return $output;
    }

    public function editorCallback($output) {
        $this->enqueueScripts();
        return $output;
    }

    private function enqueueScripts() {
        wp_enqueue_script("blockScript_{$this->blockName}", $this->getBlocksDirectory() . '/block.js', array('wp-element'), '1.0.0', array(
            'strategy' => 'defer',
        ));
    }

    private function enqueueStyles() {
        wp_enqueue_style("blockStyle_{$this->blockName}",  $this->getBlocksDirectory() . '/block.css');
    }

    private function getBlocksDirectory() {
        return get_template_directory_uri() . "/blocks/lazyblock-{$this->blockName}";
    }
}

function lazy_autoRegisterBlocks() {
    $blocksDirectory = get_template_directory() . '/blocks/';
    $blockFolders = array_filter(glob($blocksDirectory . 'lazyblock-*'), 'is_dir');

    foreach ($blockFolders as $blockFolder) {
        $blockName = str_replace('lazyblock-', '', basename($blockFolder));
        new lazy_BlockAssetManager($blockName);
    }
}

lazy_autoRegisterBlocks();
