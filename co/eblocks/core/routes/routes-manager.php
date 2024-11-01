<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . '../controllers/GetAllFeedCtrl.php');
require_once(plugin_dir_path(__FILE__) . '../controllers/DisconnectBlogController.php');


class STB_RoutesManager {
    static function registerRoutes() {
        $allFeedController = new STB_GetAllFeedCtrl();
        $allFeedController->register_routes();
        $disconnectController = new STB_DisconnectBlogController();
        $disconnectController->register_routes();
    }
}

?>