<?php
require_once(plugin_dir_path(__FILE__) . 'iStbWiget.php');

class stb_w_popular_posts implements iStbWiget {

    private $widget;

    function __construct($widget) {
        $this->widget = $widget;
    }

    public function render() {
        $args = array(
            'orderby' => 'meta_value_num',
            'meta_key' => 'story_book_post_views_count',
            'order' => 'DESC',
            'posts_per_page' => intval($this->widget->data->max),
            'post_status'    => 'publish',
        );
        $popular_posts = new WP_Query($args);

        $output = "<ul>";
        
        if ( $popular_posts->have_posts() ) {
            $count = 0;
            while ( $popular_posts->have_posts() ) {
                $popular_posts->the_post();
                global $post;

                $thumbUrl = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                $imgHtml = $thumbUrl ? "<div class=\"pull-left img\" style=\"background: url($thumbUrl); background-position: center; background-size: cover;\"></div>" : "";

                $separator = $count !== $popular_posts->post_count - 1 ? 'separator' : '';
                $blogPath = $this->widget->blogPath;
                $postName = $post->post_name;;
                $postTitle = $post->post_title;
                $date = get_the_date(NULL, get_the_ID());

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
            }
        }
        $output .= '</ul>';
        wp_reset_postdata();
        return $output;
        
    }
}
?>