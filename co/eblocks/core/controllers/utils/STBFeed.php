<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . 'STBContent.php');
require_once(plugin_dir_path(__FILE__) . 'ISTBBaseContent.php');
require_once(plugin_dir_path(__FILE__) . 'STBUtils.php');
require_once(plugin_dir_path(__FILE__) . '../../sidebars/STBSidebar.php');

class STBFeed extends STBContent implements ISTB_BaseContent {

    protected $mainData;

    function __construct($mainData) {
        $this->mainData = $mainData;
    }

    private function prepareQuery() {
        $postsPerPage = intval($this->mainData->feed_posts_per_page);
        
        $args = array(
            'post_type'      => 'post',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'posts_per_page' => $postsPerPage,
            'paged' => $this->mainData->router->page,
        );
        
        if ($this->mainData->router->path === 'category') {
            $cat = get_category_by_slug($this->mainData->tax_slug);
            $args['cat'] = $cat->term_id;
        }

        if ($this->mainData->router->path === 'tag') {
            $args['tag'] = $this->mainData->tax_slug;
        }        
        
        return new WP_Query($args);
    }

    private function preparePost() {
        $postId = get_the_ID();

        $postCategories = wp_get_post_categories($postId);
        $cats = array();
        if ($postCategories) {
            foreach($postCategories as $c){
                $cat = get_category($c);
                array_push($cats, array('name' => $cat->name, 'slug' => $cat->slug));
            }
        }
        $postData = new stdClass();
        $postData->postId = $postId;
        $postData->postClass = implode(" ", get_post_class());
        $postData->hasThumb = has_post_thumbnail($postId);
        $postData->post_name = get_post_field('post_name', $postId);
        $postData->cats = $cats;
        $postData->post_thumbnail = get_the_post_thumbnail($postId, 'large');
        $postData->post_thumbnail_url_large = get_the_post_thumbnail_url($postId, 'large');
        $postData->title = get_the_title();
        $postData->excerpt = get_the_excerpt();
        $postData->commentsNo = intval(get_comments_number($postId));
        $postData->date = get_the_date('', $postId);

        $image_id = get_post_thumbnail_id($postId);
        $image_meta_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
        if ($image_meta_alt) {
            $postData->image_alt = $image_meta_alt;
        }
        
        return $postData;
    }

    public function render() {
        $currentPath = $this->mainData->blogPath;
        $resourcequery = $this->prepareQuery();
        $posts = [];
        if ($resourcequery->have_posts()) {
            while ($resourcequery->have_posts()) {
                $resourcequery->the_post();
                array_push($posts, $this->preparePost());
            }
        }

        $routePath = $currentPath;
        if ($this->mainData->router->path === 'category') {
            $routePath .= '/category/' . $this->mainData->tax_slug;
        }

        if ($this->mainData->router->path === 'tag') {
            $routePath .= '/tag/' . $this->mainData->tax_slug;
        }   

        $paginationHtml = STBUtils::__pagination($this->mainData->router->page, $resourcequery->max_num_pages, $routePath);
        wp_reset_postdata();

        $sidebar = new STBSidebar($this->mainData);

        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->paginationHtml = $paginationHtml;
        $response->data->max_num_pages = $resourcequery->max_num_pages;
        $response->data->posts = $posts;
        $response->data->widgetsHtml = $sidebar->renderWidgets();
        
        if ($this->mainData->router->path === 'category') {
            $category = get_category_by_slug($this->mainData->tax_slug);
            $response->data->category = $category;
        }

        if ($this->mainData->router->path === 'tag') {
            $term = get_term_by('slug', $this->mainData->tax_slug, 'post_tag');
            $response->data->tag = $term;
        }

        $response->status = 'OK';
        
        return $response;
    }

}
?>