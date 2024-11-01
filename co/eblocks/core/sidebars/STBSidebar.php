<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . 'widgets/stb_w_blog_categories.php');
require_once(plugin_dir_path(__FILE__) . 'widgets/stb_w_blog_tags.php');
require_once(plugin_dir_path(__FILE__) . 'widgets/stb_w_recent_posts.php');
require_once(plugin_dir_path(__FILE__) . 'widgets/stb_w_popular_posts.php');
require_once(plugin_dir_path(__FILE__) . 'widgets/stb_advertisement.php');

class STBSidebar {

    private $mainData;
    function __construct($mainData) {
        $this->mainData = $mainData;
    }

    function beforeSidebar() {
        return '<div class="stb-sidebar">';
    }

    function afterSidebar() {
        return '</div>';
    }

    function _eWidgetTitle($title) {
        return "<h3 class=\"title\">$title</h3>";
    }

    function _eContentStart() {
        return "<div class=\"content\">";
    }

    function _eContentEnd() {
        return "</div>";
    }

    function renderWidget($widget) {
        $widgetHtml = '';
        if ($widget->title !== '') {
            $widgetHtml .= $this->_eWidgetTitle($widget->title);
        }
        $widgetHtml .= $this->_eContentStart();
        $widget->blogPath = $this->mainData->blogPath;
        $wdgBuilder = new $widget->slug($widget);
        // $widgetHtml .= 'widget content';
        $widgetHtml .= $wdgBuilder->render();
        $widgetHtml .= $this->_eContentEnd();
        return $widgetHtml;
    }

    function renderWidgets() {
        $widgets = $this->mainData->widgets;
        $sidebarHtml = '';
        $sidebarHtml .= $this->beforeSidebar();
        foreach ($widgets as $widget) {
            $sidebarHtml .= '<div class="widget">';
            $sidebarHtml .= $this->renderWidget($widget);
            $sidebarHtml .= '</div>';
        }
        $sidebarHtml .= $this->afterSidebar();
        return $sidebarHtml;
    }
}
?>