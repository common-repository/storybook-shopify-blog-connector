<?php
require_once(plugin_dir_path(__FILE__) . 'iStbWiget.php');

class stb_w_blog_categories implements iStbWiget {

    private $widget;

    function __construct($widget) {
        $this->widget = $widget;
    }

    public function render() {
        $allCategories = get_categories();    
        $output = "<ul>";
        $blogPath = $this->widget->blogPath;
        if ($allCategories) {
            foreach($allCategories as $c) {
                if ($c->category_count !== 0) {
                    $slug = $c->slug;
                    $name = $c->name;
                    $count = $c->category_count;
                    $output .= "
                    <li>
                    <a href=\"$blogPath/category/$slug\">
                        $name ($count)
                    </a>
                    </li>
                    ";
                }
            }
        }
        $output .= "</ul>";
        return $output;
    }
}
?>