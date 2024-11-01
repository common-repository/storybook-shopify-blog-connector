<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . 'config.php');
require_once(plugin_dir_path(__FILE__) . 'templates/render-admin.php');
require_once(plugin_dir_path(__FILE__) . 'services/ConnectionService.php');
require_once(plugin_dir_path(__FILE__) . 'routes/routes-manager.php');



class STB_Core {
    public function run() {
        $this->registerActions();
    }

    // admin menu handler
    public function adminMenuHandler() {
        $icon = plugins_url('', dirname( __FILE__ ) . '../') . '/assets/img/wp-admin-icon.png';
        add_menu_page( 'StoryBook Connector', 'StoryBook Connector', 'manage_options', STB_Config::getSlugs()['menuSlug'],  array($this, 'renderAdminPage'), $icon);
    }

    // render wp admin page
    public function renderAdminPage() {
        STB_SuperAdminController::render();
    }
    
    // find page
    public static function findPageByName($guid) {
        $args = array(
            'post_type'   => 'page',
            'name'   => $guid,
            'post_status' => 'publish',
            'numberposts' => 1
        );
        $result = get_posts($args);
        if (is_array($result) && sizeof($result) > 0) {
            return $result[0];
        }
        return false;
    }



    public function loadAdminScripts($hook) {
        if ($hook != 'toplevel_page_' . STB_Config::getSlugs()['menuSlug']) {
            return;
        }
        $css = plugins_url('', dirname( __FILE__ ) . '../') . '/assets/styles/style.css';
        $js = plugins_url('', dirname( __FILE__ ) . '../') . '/assets/js/main.js';
        wp_enqueue_script('jquery');
        wp_enqueue_style('css_' . STB_Config::getSlugs()['menuSlug'], $css);
        
        wp_enqueue_style('css_bootstrap' . STB_Config::getSlugs()['menuSlug'], 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');
        wp_enqueue_style('css_polaris' . STB_Config::getSlugs()['menuSlug'], 'https://sdks.shopifycdn.com/polaris/3.6.0/polaris.min.css');
        wp_enqueue_script('js_' . STB_Config::getSlugs()['menuSlug'], $js, array(), 'v1', true);
    }
    
    // on plugin remove
    public static function onDeactivate() { 
        // update_option(STB_Config::getSlugs()['preventUninstall'], 'false');
        delete_option(STB_APP_ACCES_KEY_SLUG);
        delete_option(STB_APP_CURRENT_CONNECTION_KEY_SLUG);
    }

    public function postSaveHook($postId) {
        $post = get_post($postId);
        $isArticle = $post->post_type === 'post';
        $slug = $post->post_name;
        if ($isArticle) {
            if (strpos($slug, '__trashed') !== false) {
                $slug = substr($slug, 0, strlen($slug) - 11);
            }
            $this->_sendSTBUpdate(array(
                'action' => 'post_update',
                'slug' => $slug
            ));
        }
    }

    public function termCreated( $term_id, $tt_id, $taxonomy ) {
        if ($taxonomy === 'post_tag' || $taxonomy === 'category') {
            $this->_sendSTBUpdate(array(
                'action' => 'tax_update',
                'term_id' => $term_id
            ));
        }
    }
        
    public function termEdited( $term_id, $taxonomy ) {
        if ($taxonomy === 'post_tag' || $taxonomy === 'category') {
            $this->_sendSTBUpdate(array(
                'action' => 'tax_update',
                'term_id' => $term_id
            ));
        }
    }

    public function termDeleted($Term_ID) {
        $this->_sendSTBUpdate(array(
            'action' => 'tax_update',
            'term_id' => $Term_ID
        ));
    }


    private function registerActions() {
        add_action('admin_menu', array($this, 'adminMenuHandler'));
        add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));
        add_action('wp_ajax_' . 'update_access_key', array($this, 'update_access_key'));
        add_action('wp_ajax_' . 'get_access_key', array($this, 'get_access_key'));
        add_action('rest_api_init', ['STB_RoutesManager', 'registerRoutes']);
        add_action('save_post', array($this, 'postSaveHook'));
        add_action('edited_terms', array($this, 'termEdited'), 10, 3 ); 
        add_action('created_term', array($this, 'termCreated'), 10, 3 );
        add_action('delete_term', array($this, 'termDeleted'));
    }

    // update access key
    public function update_access_key() {
        if (!is_admin()) {
            $this->renderAjaxError('Restricted access');
            wp_die();
        }

        if (!isset($_POST['accessKey'])) {
            $this->renderAjaxError('Error - Missing access key!');
        }

        update_option(STB_APP_ACCES_KEY_SLUG, $_POST['accessKey']);

        $decoded = base64_decode($_POST['accessKey']);
        $jsonAr = json_decode($decoded, true);
        
        $cs = new ConnectionService($_POST['accessKey']);
        $result = array();
        $namespace = STB_Config::getRESTNamespace() . '/' . STB_Config::getAPIVersion();
        $siteUrl = get_site_url();
        try {
            $result = $cs->connectShop($jsonAr['connectRoute'], array(
                'blogHost' => $siteUrl,
                'blogName' => get_bloginfo('name'),
                'storyBookPostUrl' => "$siteUrl/wp-json/$namespace",
            ));
        } catch (Exception $e) {
            $this->renderAjaxError('Soething went wrong!');
        }

        if (isset($result['status']) && $result['status'] === 'OK') {
            update_option(STB_APP_CURRENT_CONNECTION_KEY_SLUG, $result);
            $this->renderAjaxResponse($result);
        } else {
            $this->renderAjaxError('Soething went wrong, access Key might not be valid, the Shopify app might have been uninstalled!');
        }
    }

    private function _sendSTBUpdate($payload) {
        $accessKey = get_option(STB_APP_ACCES_KEY_SLUG, '');
        try {
            $decoded = base64_decode($accessKey);
            $jsonAr = json_decode($decoded, true);
            $cs = new ConnectionService($accessKey);
            $siteUrl = get_site_url();
            $result = $cs->sendUpdate($jsonAr['connectRoute'] . '/update', array_merge($payload, array('blogHost' => $siteUrl)));
        } catch (Exception $e) {}
    }
    
    // update access key
    public function get_access_key() {
        if (!is_admin()) {
            $this->renderAjaxError('Restricted access');
            wp_die();
        }
        
        $this->renderAjaxResponse(get_option(STB_APP_ACCES_KEY_SLUG, ''));
    }

    public function renderAjaxError($message = '') {
        echo json_encode(array(
            'status' => 'FAIL',
            'message' => $message,
        ));
        wp_die();
    }

    public function renderAjaxResponse($data = array()) {
        echo json_encode(array(
            'status' => 'OK',
            'data' => $data,
        ));
        wp_die();
    }
}
?>