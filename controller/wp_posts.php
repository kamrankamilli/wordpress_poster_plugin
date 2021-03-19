<?php

function wp_posts()
{
    $posts = get_posts(array('post_status' => 'publish', 'posts_per_page' => -1));

    $arrPosts = array();

    if (!empty($posts)) {
        foreach ($posts as $post) {

            array_push($arrPosts, array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'excerpt' => $post->post_excerpt,
                'author' => get_the_author_meta('display_name', $post->post_author),
                'link' => get_permalink($post->ID),
                'categories' => get_the_category($post->ID),
                'tags' => get_the_tags($post->ID),
                'uniq_id' => $post->guid,
                'image_url' => get_the_post_thumbnail_url(($post->ID), 'post-thumbnail')
            ));
        }
    } else {
        echo '<option value="" disabled>No posts</option>';
    }

    return $arrPosts;
}

function postSelectBox($arrPosts)
{
    foreach ($arrPosts as $post) {
?>

        <option <?php if (!empty($_GET['id'])) {
                    if ($post['id'] == $_GET['id']) {
                        echo 'selected';
                    }
                } ?> value='<?php echo $post['id'] ?>'>
            <p>ID: <?php echo $post['id'] ?></p>
            <p>Post Title: <?php echo $post['title'] ?></p>
            <p>Author: <?php echo $post['author'] ?></p>
        </option>



<?php }
}
?>