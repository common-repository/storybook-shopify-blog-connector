<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . '../config.php');
require_once(plugin_dir_path(__FILE__) . '../services/ConnectionService.php');

class STB_DisconnectBlogController extends WP_REST_Controller {

    public const BASE = 'disconnect-signal';

    public function register_routes() {
        $namespace = STB_Config::getRESTNamespace() . '/' . STB_Config::getAPIVersion();

        register_rest_route($namespace, '/' . self::BASE, array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'requestDisconnect' ),
                'args'                => array(),
            ),
        ));
    }
    
    /**
     * Uninstall shop
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function requestDisconnect($request) {
        try {
            $accessKey = get_option(STB_APP_ACCES_KEY_SLUG, '');
            $decoded = base64_decode($accessKey);
            $jsonAr = json_decode($decoded, true);

            $cs = new ConnectionService($accessKey);

            $siteUrl = get_site_url();
            $result = $cs->getStatus($jsonAr['connectRoute'] . '/status', array(
                'blogHost' => $siteUrl,
            ));
            if (isset($result['data']) && isset($result['data']['status']) && $result['data']['status'] !== 'CONNECTED') {
                delete_option(STB_APP_ACCES_KEY_SLUG);
                delete_option(STB_APP_CURRENT_CONNECTION_KEY_SLUG);
            }
        } catch (Exception $e) {
            
        }

        return new WP_REST_Response(array('status' => 'OK'), 200);
    }
}

?>