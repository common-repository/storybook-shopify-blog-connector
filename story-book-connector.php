<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
/*
Plugin Name: StoryBook - Shopify Blog Connector
Plugin URI: https://apps.shopify.com/storybook-wp-blog-connector
Description: StoryBook connects your WordPress blog to your Shopify store.
Author: SakuraPlugins
Version: 1.1
Author URI: https://www.sakuraplugins.com
License: GPLv2 or later
*/

define('STB_APP_INSTALL_URL', 'https://apps.shopify.com/storybook-wp-blog-connector');
define('STB_APP_ACCES_KEY_SLUG', 'stb_access_key');
define('STB_APP_CURRENT_CONNECTION_KEY_SLUG', 'stb_current_connection');

require_once(plugin_dir_path(__FILE__) . 'co/eblocks/core/core.php');
$MTR_coreInstance = new STB_Core();
$MTR_coreInstance->run();

register_deactivation_hook( __FILE__, array('STB_Core', 'onDeactivate'));


?>