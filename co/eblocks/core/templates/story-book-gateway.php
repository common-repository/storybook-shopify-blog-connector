<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . '../services/gate/StoryGateFactory.php');
$stbGate = new StoryGateFactory();
$stbGate->process();
?>