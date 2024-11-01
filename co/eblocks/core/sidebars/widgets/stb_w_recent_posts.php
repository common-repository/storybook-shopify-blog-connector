<?php
require_once(plugin_dir_path(__FILE__) . 'iStbWiget.php');

class stb_w_recent_posts implements iStbWiget {

    private $widget;

    function __construct($widget) {
        $this->widget = $widget;
    }

    public function render() {
        $args = array(
            'numberposts' => $this->widget->data->max,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'post_type' => 'post',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $entries = wp_get_recent_posts($args);
        $output = "<ul>";
        $count = 0;
        foreach ($entries as $postEntry) {
            $thumbUrl = get_the_post_thumbnail_url($postEntry['ID'], 'thumbnail');
            $separator = $count !== sizeof($entries) - 1 ? 'separator' : '';
            $blogPath = $this->widget->blogPath;
            $postName = $postEntry['post_name'];
            $postTitle = $postEntry['post_title'];
            $date = get_the_date(NULL, $postEntry['ID']);
            $imgHtml = $thumbUrl ? "<div class=\"pull-left img\" style=\"background: url($thumbUrl); background-position: center; background-size: cover;\"></div>" : "";
            $output .= "
            <li class=\"rich-list-item\">
                <a class=\"rich-link $separator\" href=\"$blogPath/$postName\">
                    $imgHtml
                    <div>
                        <span>$postTitle</span>
                        <span class=\"date\">$date</span>
                    </div>
                    <div class=\"clearfix\"></div>
                </a>
            </li>
            ";
            $count++;
        }
        $output .= '</ul>';
        return $output;
    }
}
?>