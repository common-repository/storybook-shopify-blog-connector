<?php
require_once(plugin_dir_path(__FILE__) . 'iStbWiget.php');

class stb_w_blog_tags implements iStbWiget {

    private $widget;

    function __construct($widget) {
        $this->widget = $widget;
    }

    public function render() {
        $allTags = get_tags();
        $blogPath = $this->widget->blogPath;
        $output = "<ul>";
        if ($allTags) {
            $count = 0;
            foreach($allTags as $t){
                $slug = $t->slug;
                $name = $t->name;
                $separator = $count !== sizeof($allTags) - 1 ? "<span class=\"tag-separator\"> / <span>" : "";
                if ($t->taxonomy === 'post_tag' && $t->count !== 0) {
                    $output .= "
                    <li class=\"tag-entry\">
                        <a href=\"$blogPath/tag/$slug\">
                        $name
                        </a>
                    </li>
                    $separator
                    ";
                }
                $count++;
            }
        }
        $output .= "</ul>";
        return $output;
    }
}
?>