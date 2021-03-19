<?php

/**
 * @package Poster
 */

class Callbacks
{
    function admin_page()
    {
        return require_once plugin_dir_path(dirname(__FILE__, 2)) . 'templates/dashboard.php';
    }
    //callback for admin submenu settings
    function admin_page_settings()
    {
        return require_once plugin_dir_path(dirname(__FILE__, 2)) . 'templates/settings.php';
    }

    function admin_page_accounts()
    {
        return require_once plugin_dir_path(dirname(__FILE__, 2)) . 'templates/accounts.php';
    }

    function admin_page_share()
    {
        return require_once plugin_dir_path(dirname(__FILE__, 2)) . 'templates/share.php';
    }

    function register_post_meta_boxes()
    {
        // $screens = ['post', 'product'];
        // foreach ($screens as $screen) {
        add_meta_box(
            'box_id',
            'Poster',
            array($this, 'display_shared_posts'),
            'post',
            'side'
        );
        //}
    }
    function register_product_meta_boxes()
    {
        add_meta_box(
            'box_id',
            'Poster',
            array($this, 'display_shared_products'),
            'product',
            'side'
        );
    }

    function display_shared_posts($post)
    {

        global $wpdb;
        $VKsharedPosts = $wpdb->get_results("SELECT t2.id, user_id,first_name, last_name, title, content, link, shared_time   
                                            FROM {$wpdb->prefix}vkusers t1, {$wpdb->prefix}vksharedposts t2
                                            WHERE t1.id = t2.user_id AND shared_post_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($VKsharedPosts)) {
            foreach ($VKsharedPosts as $spost) {
                $user_id = $spost->user_id;
                $first_name = $spost->first_name;
                $last_name = $spost->last_name;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
?>
                <form action="" method="POST">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Account VK: <?php echo $first_name . " " . $last_name; ?></p>
                        <p><a href="<?php echo $link ?>">Post Link</a></p>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=vk&user_id=<?= $user_id ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>

            <?php

            }
        }
        $OKsharedPosts = $wpdb->get_results("SELECT t2.id, user_id,first_name, last_name, title, content, link, shared_time   
                                            FROM {$wpdb->prefix}okusers t1, {$wpdb->prefix}oksharedposts t2
                                            WHERE t1.id = t2.user_id AND shared_post_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($OKsharedPosts)) {
            foreach ($OKsharedPosts as $spost) {
                $user_id = $spost->user_id;
                $first_name = $spost->first_name;
                $last_name = $spost->last_name;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="POST">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Account OK: <?php echo $first_name . " " . $last_name; ?></p>
                        <p><a href="<?php echo $link ?>">Post Link</a></p>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=ok&user_id=<?= $user_id ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>

            <?php

            }
        }
        $VKsharedPostsCom = $wpdb->get_results("SELECT t1.community_id, community_name, title, content, link, shared_time
                                            FROM {$wpdb->prefix}vksharedposts t1, {$wpdb->prefix}vkcommunities t2
                                            WHERE t1.community_id = t2.community_id AND shared_post_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($VKsharedPostsCom)) {
            foreach ($VKsharedPostsCom as $spost) {
                $community_id = $spost->community_id;
                $community_name = $spost->community_name;
                $title = $spost->title;
                $content = $spost->content;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="post">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Community VK: <?php echo $community_name; ?></p>
                        <p><a href="<?php echo $link ?>">Post Link</a></p>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=vk&community_id=<?= $community_id; ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>

            <?php
            }
        }
        $OKsharedPostsCom = $wpdb->get_results("SELECT t1.group_id, group_name, title, content, link, shared_time
                                            FROM {$wpdb->prefix}oksharedposts t1, {$wpdb->prefix}okgroups t2
                                            WHERE t1.group_id = t2.group_id AND shared_post_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($OKsharedPostsCom)) {
            foreach ($OKsharedPostsCom as $spost) {
                $group_id = $spost->group_id;
                $group_name = $spost->group_name;
                $title = $spost->title;
                $content = $spost->content;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="post">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Group OK: <?php echo $group_name; ?></p>
                        <p><a href="<?php echo $link ?>">Post Link</a></p>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=ok&community_id=<?= $group_id; ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>

            <?php
            }
        }
        if (empty($VKsharedPosts) &&  empty($VKsharedPostsCom) && empty($OKsharedPosts) && empty($OKsharedPostsCom)) {
            echo '<div style="text-align:center;"><a href = "admin.php?page=poster_share' . '"style="color:red;">Share Again</a></div>';
        }
    }

    function display_shared_products($post)
    {
        global $wpdb;
        $VKsharedProducts = $wpdb->get_results("SELECT t2.id, user_id, first_name, last_name, title, content, link, shared_time
                                                FROM {$wpdb->prefix}vkusers t1, {$wpdb->prefix}vksharedproducts t2
                                                WHERE t1.id = t2.user_id AND shared_product_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($VKsharedProducts)) {
            foreach ($VKsharedProducts as $spost) {
                $user_id = $spost->user_id;
                $first_name = $spost->first_name;
                $last_name = $spost->last_name;
                $title = $spost->title;
                $content = $spost->content;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="post">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Account VK: <?php echo $first_name . " " . $last_name; ?></p>
                        <a href="<?php echo $link ?>">Post Link</a>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=vk&user_id=<?= $user_id ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>

            <?php
            }
        }
        $OKsharedProducts = $wpdb->get_results("SELECT t2.id, user_id, first_name, last_name, title, content, link, shared_time
                                                FROM {$wpdb->prefix}okusers t1, {$wpdb->prefix}oksharedproducts t2
                                                WHERE t1.id = t2.user_id AND shared_product_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($OKsharedProducts)) {
            foreach ($OKsharedProducts as $spost) {
                $user_id = $spost->user_id;
                $first_name = $spost->first_name;
                $last_name = $spost->last_name;
                $title = $spost->title;
                $content = $spost->content;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="post">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Account OK: <?php echo $first_name . " " . $last_name; ?></p>
                        <a href="<?php echo $link ?>">Post Link</a>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=ok&user_id=<?= $user_id ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>

            <?php
            }
        }

        $VKsharedProductsCom = $wpdb->get_results("SELECT t1.community_id, community_name, title, content, link, shared_time
                                                FROM {$wpdb->prefix}vksharedproducts t1, {$wpdb->prefix}vkcommunities t2
                                                WHERE t1.community_id = t2.community_id AND shared_product_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($VKsharedProductsCom)) {
            foreach ($VKsharedProductsCom as $spost) {
                $community_id = $spost->community_id;
                $community_name = $spost->community_name;
                $title = $spost->title;
                $content = $spost->content;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="post">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Community VK: <?php echo $community_name; ?></p>
                        <a href="<?php echo $link ?>">Post Link</a>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=vk&community_id=<?= $community_id; ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>
            <?php
            }
        }
        $OKsharedProductsCom = $wpdb->get_results("SELECT t1.group_id, group_name, title, content, link, shared_time
                                                    FROM {$wpdb->prefix}oksharedproducts t1, {$wpdb->prefix}okgroups t2
                                                    WHERE t1.group_id = t2.group_id AND shared_product_id = {$post->ID} ORDER BY shared_time DESC");
        if (!empty($OKsharedProductsCom)) {
            foreach ($OKsharedProductsCom as $spost) {
                $group_id = $spost->group_id;
                $group_name = $spost->group_name;
                $title = $spost->title;
                $content = $spost->content;
                $link = $spost->link;
                $shared_time = $spost->shared_time;
            ?>
                <form action="" method="post">
                    <div style="border: 1px solid black; text-align: center; margin-top:10px;">
                        <p>Group OK: <?php echo $group_name; ?></p>
                        <a href="<?php echo $link ?>">Post Link</a>
                        <p>Shared Time: <?php echo $shared_time; ?></p>
                        <a href="admin.php?page=poster_share&share=ok&group_id=<?= $group_id; ?>&id=<?= $post->ID; ?>" style="color:red;">Share Again</a>
                    </div>
                </form>
<?php
            }
        }
        if (empty($VKsharedProducts) && empty($VKsharedProductsCom) && empty($OKsharedProducts) && empty($OKsharedProductsCom)) {
            echo '<div style="text-align:center;"><a href = "admin.php?page=poster_share' . '"style="color:red;">Share Again</a></div>';
        }
    }
}

?>