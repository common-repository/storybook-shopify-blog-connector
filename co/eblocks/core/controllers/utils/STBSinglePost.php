<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(plugin_dir_path(__FILE__) . 'STBContent.php');
require_once(plugin_dir_path(__FILE__) . 'ISTBBaseContent.php');
require_once(plugin_dir_path(__FILE__) . 'STBUtils.php');
require_once(plugin_dir_path(__FILE__) . '../../sidebars/STBSidebar.php');

class STBSinglePost extends STBContent implements ISTB_BaseContent {

    private $currentPost;
    private $title;
    private $hasThumb;
    private $shopifyPostUrl;
    private $postTbumbURL;
    private $commentsNumber;

    private function getPost() {
        $posts = get_posts(array(
                'name' => $this->mainData->slug,
                'posts_per_page' => 1,
                'post_type' => 'post',
                'post_status' => 'publish'
        ));
        return !empty($posts) && is_array($posts) && isset($posts[0])? $posts[0] : NULL;  
    }

    private function _getPostNav() {
        $previous_post = get_previous_post();
        $next_post = get_next_post();
        $prevNav = "";
        $nextNav = "";
        $blogPath = $this->mainData->blogPath;
        $ppLabel = $this->mainData->labels->previousPost;
        $npLabel = $this->mainData->labels->nextPost;
        if ($previous_post) {
            $ppTitle =  $previous_post->post_title;
            $prevNav = "
<a href=\"$blogPath/$previous_post->post_name\">
<span class=\"label\">$ppLabel</span>
<span class=\"title\">$ppTitle</span>
</a>
            ";
        }
        if ($next_post) {
            $npTitle =  $next_post->post_title;
            $nextNav = "
<a href=\"$blogPath/$next_post->post_name\">
<span class=\"label\">$npLabel</span>
<span class=\"title\">$npTitle</span></a>
            ";
        }
        $out = "
        <div class=\"next-prev-nav\">$prevNav$nextNav</div>
        ";
        return $out;
    }

    private function _getRelatedPosts($tags, $postId) {
        $out = "";
        if ($this->mainData->postSingle->showRelatedPosts) {
            global $post;
            if ($tags) {
                $tag_ids = array();
                foreach ($tags as $individual_tag) {
                    array_push($tag_ids, $individual_tag->term_id);
                }
            }

            $args = array(
                // 'tag__in' => $tag_ids,
                'post__not_in' => array($postId),
                'posts_per_page'=> $this->mainData->postSingle->maxRelatedPosts,
                'caller_get_posts'=> 1,
                'post_status' => 'publish'
            );

            if (isset($tag_ids) && is_array($tag_ids)) {
                $args['tag__in'] = $tag_ids;
            }

            $related_query = new WP_Query($args);
            $allRelatedHtml = "";
            while( $related_query->have_posts()) {
                $related_query->the_post();
                $relatedHasThumb = has_post_thumbnail(get_the_ID());
                $max = $relatedHasThumb ? 100 : 130;
                $title = get_the_title();
                $titleStipped = strlen($title) > $max ? substr($title, 0, $max) . '...' : $title;
                $thumbHtml = "";
                $post_name = $post->post_name;
                if ($relatedHasThumb) {
                    $thumb = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                    $thumbHtml = "<div class=\"img-ui\" style=\"background: url($thumb); background-position: center; background-size: cover; \"></div>";
                }
                $blogPath = $this->mainData->blogPath;
                $meme = "<div class=\"name\">";
                if (!$relatedHasThumb) {
                    $meme = "<div class=\"name auto\">";
                }
                $relatedPostHtml = "
<a class =\"r-link\" href=\"$blogPath/$post_name\">
<div class=\"r-content\">
    $thumbHtml
    $meme
    $titleStipped
    </div>
</div>
</a>
";
                $allRelatedHtml .= $relatedPostHtml;
            }
            wp_reset_query();
            $relatedPostsTitle = $this->mainData->labels->relatedPostsTitle;
            $out = "
            <div class=\"related\">
            <h5 class=\"section-title\">$relatedPostsTitle</h5>
            <div class=\"related-content\">$allRelatedHtml</div>
            </div>
            ";
        }
        return $out;
    }

    private function _eSocialLink($socialEntry, $shopifyPostUrl, $title = '', $media = '') {
        if ($socialEntry->type === 'fb') {
            return 'https://www.facebook.com/sharer.php?u=' . urlencode($shopifyPostUrl);
        } if ($socialEntry->type === 'tw') {
            return 'https://twitter.com/intent/tweet?url=' . urlencode($shopifyPostUrl) . '&text=' . $title;
        } if ($socialEntry->type === 'rd') {
            return 'https://www.reddit.com/submit?url=' . urlencode($shopifyPostUrl) . '&title=' . $title;
        } if ($socialEntry->type === 'pt') {
            return 'http://pinterest.com/pin/create/button/?url=' . urlencode($shopifyPostUrl) . '&description=' . $title . '&media=' . urlencode($media);
        } else {
            return '#';
        }
    }

    private function renderSocial() {
        $socialEntriesToShow = array();
        foreach ($this->mainData->postSingle->social as $key => $entry) {
            if ($entry->show) {
                if ($entry->type === 'pt' && !$this->hasThumb) {
                    // do nothing
                } else {
                    array_push($socialEntriesToShow, $entry);
                }
            }
        }
        $entriesHtml = '';
        for ($i=0; $i < sizeof($socialEntriesToShow); $i++) {
            $type = $socialEntriesToShow[$i]->type;
            $socialUrl = $this->_eSocialLink($socialEntriesToShow[$i], $this->shopifyPostUrl, $this->title, $this->postTbumbURL);
            $svg = STBUtils::_eSWGIcon($socialEntriesToShow[$i]->type, .5);
            $entriesHtml .= "
            <li class=\"link\">
                <a class=\"$type\" target=\"_blank\" href=\"$socialUrl\">
                $svg
                </a>
            </li>
            ";
            if ($i < sizeof($socialEntriesToShow) - 1) {
                $entriesHtml .= '
                <li>
                    <div class="separator"></div>
                </li>   
                ';
            }
        }
        $socialTitle = $this->mainData->labels->socialShareTitle;
        $out = "";
        if (sizeof($socialEntriesToShow) > 0) {
            $out .= "
            <div class=\"single-social\">
                <h5 class=\"section-title\">$socialTitle</h5>
                <ul>$entriesHtml</ul>
            </div>
            ";
        }
        return $out;
    }

    private function _getCommentsHtml($postId) {
        $out = "";
        if ($this->mainData->postSingle->showComments && $this->commentsNo !== 0) {
            global $wp_query;
            $args = array(
                'post_id' => $postId,
                'status' => 'approve',
                'order'   => 'ASC'
            );
            $wp_query->comments = get_comments( $args );
            $comments = wp_list_comments(array(
                'echo' => false,
                'callback' => array('STBUtils', 'custom_comment_output'),
            ));
            $html = preg_replace('#<div class="reply">(.*?)</div>#', '', $comments);
            $commentsListTitle = $this->mainData->labels->commentsListTitle;
            $out = "
            <div class=\"comments-section\"><h5 class=\"section-title\">$commentsListTitle</h5><ul>$html</ul></div>
            ";
        }
        return $out;
    }

    private function remove_http($url) {
        $disallowed = array('http:', 'https:');
        foreach($disallowed as $d) {
           if(strpos($url, $d) === 0) {
              return str_replace($d, '', $url);
           }
        }
        return $url;
     }

     private function _set_post_views($postID) {
        $count_key = 'story_book_post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        if($count === ''){
            $count = 0;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, 0);
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    }

     public function render() {
        $response = new stdClass();
        $response->data = new stdClass();
        $response->data->post = new stdClass();

        $postData = new stdClass();
        $postTemp = false;

        $args = array(
            'name' => $this->mainData->slug,
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $post;
                $this->_set_post_views($post->ID);
                $postTemp = $post;
                ob_start();
                the_content();
                $contentOutput = ob_get_contents();
                ob_end_clean();
                $postId = $post->ID;
                $image_id = get_post_thumbnail_id($postId);
                $tags = wp_get_post_tags($postId);

                $this->commentsNo = intval(get_comments_number($post));

                $excerpt = get_the_excerpt();
                $metaDescription = "";
                if (isset($excerpt) && is_string($excerpt)) {
                    $metaDescription = substr($excerpt, 0, 100);
                }
        
                $yoast_wpseo_metadesc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
                if (is_string($yoast_wpseo_metadesc) && strlen($yoast_wpseo_metadesc) > 0) {
                    $metaDescription = $yoast_wpseo_metadesc;
                }

                $postData->postId = $postId;
                $postData->post_title = get_the_title();
                $postData->post_description = $metaDescription;
                $postData->hasThumb = has_post_thumbnail($postId);
                $this->hasThumb = $postData->hasThumb;
                $this->title = $postData->post_title;
                $this->shopifyPostUrl = 'https://' . $this->mainData->shopifyDomain . $this->mainData->blogPath . '/' . $post->post_name;
                $postData->postTbumbURL = get_the_post_thumbnail_url($post->ID, 'large');
                $this->postTbumbURL = $postData->postTbumbURL;
                $postData->postClass = implode(" ", get_post_class());
                $postData->commentsNo = $this->commentsNo;
                $postData->post_content = $contentOutput;

                $postData->date =  get_the_date('', $postId);
                $postData->commentsHtml = trim($this->_getCommentsHtml($post->ID));
                $postData->socialHtml = trim($this->renderSocial());
                $postData->postNavHtml = trim($this->_getPostNav());
                $postData->relatedPostsHtml = trim($this->_getRelatedPosts($tags, $postId));

                $postData->og_image_url = '';
                $image_alt = "";
                if ($this->hasThumb) {
                    $postData->og_image_url = $postData->postTbumbURL;
                    $image_meta_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
                    if ($image_meta_alt) {
                        $image_alt = $image_meta_alt;
                    }
                    if (is_string($postData->og_image_url)) {
                        $postData->og_image_url = $this->remove_http($postData->og_image_url);
                    }
                }
                $postData->postTbumbAlt = $image_alt;
        
                $postCategories = wp_get_post_categories($postId);
                $postData->cats = array();
                if ($postCategories) {
                    foreach($postCategories as $c){
                        $cat = get_category($c);
                        array_push($postData->cats, array('name' => $cat->name, 'slug' => $cat->slug));
                    }
                }
        
                $postData->tags = array();
                if ($tags) {
                    foreach($tags as $t){
                        array_push($postData->tags, array('name' => $t->name, 'slug' => $t->slug));
                    }
                }

            }
        }
        if (!$postTemp) {
            $response->status = 'NOT FOUND';
            return $response;
        }
        
        $response->data->post = $postData;
        $sidebar = new STBSidebar($this->mainData);
        $response->data->widgetsHtml = $sidebar->renderWidgets();
        $response->status = 'OK';
        
        wp_reset_postdata();
        return $response;
    }
}
?>