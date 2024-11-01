<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . '../config.php');

class STB_GetAllFeedCtrl extends WP_REST_Controller {

    public const BASE = 'feed';

    public function register_routes() {
        $namespace = STB_Config::getRESTNamespace() . '/' . STB_Config::getAPIVersion();

        register_rest_route($namespace, '/' . self::BASE, array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'getFeed' ),
                'args'                => array(),
            ),
        ));
    }

    private function renderNewsFeed($mainData) {
        require_once(plugin_dir_path(__FILE__) . 'utils/STBFeed.php');
        $feed = new STBFeed($mainData);
        return $feed->render();
    }

    private function renderSinglePost($mainData) {
        require_once(plugin_dir_path(__FILE__) . 'utils/STBSinglePost.php');
        $feed = new STBSinglePost($mainData);
        return $feed->render();
    }
    
    /**
     * Uninstall shop
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getFeed($request) {
        $bodyParams = $request->get_body_params();
        $mainData = json_decode(stripslashes($bodyParams['data']));

        $response = [];
        switch ($mainData->router->path) {
            case 'index':
                $response = $this->renderNewsFeed($mainData);          
                break;
            case 'category':
                $response = $this->renderNewsFeed($mainData);
                break;
            case 'tag':
                $response = $this->renderNewsFeed($mainData);
                break;
            case 'post-single':
                $response = $this->renderSinglePost($mainData);
                break;
            default:
                return new WP_REST_Response(['status' => 'NOT FOUND'], 404);
        }

        if ($response->status !== 'OK') {
            return new WP_REST_Response($response, 404);
        }

        return new WP_REST_Response($response, 200);
    }
}

?>