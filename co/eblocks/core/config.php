<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class STB_Config {

    // get predefined pages
    static function getPredefinedPages() {
        return array(
            'storybookentry' => array(
                'name' => 'story-book-gateway',
                'template' => 'story-book-gateway.php'
            )          
        );
    }

    // return app specific slugs
    public static function getSlugs() {
        return array(
            'menuSlug' => 'story-book-dashboard',
            'preventUninstall' => 'story_book_prevent_uninstall',
        );
    }

    public static function appInfo() {
        return array(
            'appName' => 'StoryBook - Shopify Blog Connector'
        );
    }

    static function getRESTNamespace() {
        return 'storybook-api';
    }

    static function getAPIVersion() {
        return 'v1';
    }
}
?>