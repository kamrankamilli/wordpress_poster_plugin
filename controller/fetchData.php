<?php

function fetchData()
{
    global $wpdb;

    if (isset($_GET['account']) && $_GET['account'] == 'vk') {

        $allusers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}vkusers ORDER BY id ASC");
        if (!empty($allusers)) {

            foreach ($allusers as $user) {
                $id = $user->id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $is_active = $user->is_active;

?>
                <form action="" method="POST">
                    <li style="border:2px solid black; padding:20px;">

                        <?php if (!empty($first_name)) {
                            echo "Account: " . $first_name;
                        } ?>
                        <?php if (!empty($last_name)) {
                            echo $last_name;
                        } ?>
                        <?php if ($is_active) {
                            echo "<p style='color:green'>Activated</p>";
                        } else {
                            echo "<p style='color:red'>Deactivated</p>";
                        } ?>
                        <button type="submit" class="btn btn-primary" name="<?= $id ?>">Activate/Deactivate</button>
                        <button type="submit" class="btn btn-primary" name="<?= $id . 'del' ?>">Delete</button>

                        <ul style="margin-top:10px;">
                            <li><?php getUserCommunites($id); ?></li>
                        </ul>
                    </li>
                </form>
                <?php if (isset($_POST[$id . 'del'])) {
                    $wpdb->delete("{$wpdb->prefix}vkusers", array('id' => $id));
                    header('Location: admin.php?page=poster_accounts&account=vk');
                }
                if (isset($_POST[$id])) {
                    $is_active = $wpdb->get_row("SELECT is_active FROM {$wpdb->prefix}vkusers WHERE id = $id");
                    if ($is_active->is_active) {
                        $wpdb->update("{$wpdb->prefix}vkusers", array('is_active' => 0), array('id' => $id));
                        header('Location: admin.php?page=poster_accounts&account=vk');
                    } else {
                        $wpdb->update("{$wpdb->prefix}vkusers", array('is_active' => 1), array('id' => $id));
                        header('Location: admin.php?page=poster_accounts&account=vk');
                    }
                }
            }
        } else {
            echo '<p style="color:red">No user available! Add account!</p>';
        }
    } else if (isset($_GET['account']) && $_GET['account'] == 'ok') {

        $allusers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}okusers ORDER BY id ASC");
        if (!empty($allusers)) {

            foreach ($allusers as $user) {
                $id = $user->id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $is_active = $user->is_active;
                ?>
                <form action="" method="POST">
                    <li style="border:2px solid black; padding:20px;">

                        <?php if (!empty($first_name)) {
                            echo "Account: " . $first_name;
                        } ?>
                        <?php if (!empty($last_name)) {
                            echo $last_name;
                        } ?>
                        <?php if ($is_active) {
                            echo "<p style='color:green'>Activated</p>";
                        } else {
                            echo "<p style='color:red'>Deactivated</p>";
                        } ?>
                        <button type="submit" class="btn btn-primary" name="<?= $id ?>">Activate/Deactivate</button>
                        <button type="submit" class="btn btn-primary" name="<?= $id . 'del' ?>">Delete</button>

                        <ul style="margin-top:10px;">
                            <li><?php getUserCommunites($id); ?></li>
                        </ul>
                    </li>
                </form>
                <?php if (isset($_POST[$id . 'del'])) {
                    $wpdb->delete("{$wpdb->prefix}okusers", array('id' => $id));
                    header('Location: admin.php?page=poster_accounts&account=ok');
                }
                if (isset($_POST[$id])) {
                    $is_active = $wpdb->get_row("SELECT is_active FROM {$wpdb->prefix}okusers WHERE id = $id");
                    if ($is_active->is_active) {
                        $wpdb->update("{$wpdb->prefix}okusers", array('is_active' => 0), array('id' => $id));
                        header('Location: admin.php?page=poster_accounts&account=ok');
                    } else {
                        $wpdb->update("{$wpdb->prefix}okusers", array('is_active' => 1), array('id' => $id));
                        header('Location: admin.php?page=poster_accounts&account=ok');
                    }
                }
            }
        } else {
            echo '<p style="color:red">No user available! Add account!</p>';
        }
    }
}



function fetchDataToSelectBox()
{

    global $wpdb;

    if (isset($_GET['share']) && $_GET['share'] == 'vk') {

        $allcommunities = $wpdb->get_results("SELECT community_id, community_name, token FROM {$wpdb->prefix}vkcommunities t1, {$wpdb->prefix}vkusers t2 WHERE t1.user_id = t2.id AND t1.is_active");
        $allusers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}vkusers WHERE is_active = 1 ORDER BY id ASC");

        if (!empty($allusers)) {

            foreach ($allusers as $user) {

                $id = $user->id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $token = $user->token;

                ?>

                <option <?php if (!empty($_GET['user_id']) && $id == $_GET['user_id']) {
                            echo 'selected';
                        }
                        ?> value='<?php echo $id; ?>,<?php echo $token; ?>'>
                    <p>ID: <?php echo $id ?></p>
                    <p><?php echo $first_name ?></p>
                    <p><?php echo $last_name ?></p>
                </option>

            <?php }
            ?>

            <option disabled="disabled">Communites:</option>

            <?php
            foreach ($allcommunities as $community) {

                $id = $community->community_id;
                $community_name = $community->community_name;
                $token = $community->token;

            ?>

                <option <?php if (!empty($_GET['community_id']) && $id == $_GET['community_id']) {
                            echo 'selected';
                        }
                        ?> value='com,<?php echo $id; ?>,<?php echo $token; ?>'>
                    ID: <?php echo $id ?>
                    <?php echo $community_name ?>

                </option>

            <?php }
        } else {
            echo '<option value="" disabled>No authorized user or community</option>';
        }
    } else if (isset($_GET['share']) && $_GET['share'] == 'ok') {

        $allgroups = $wpdb->get_results("SELECT group_id, group_name, token,session_key FROM {$wpdb->prefix}okgroups t1, {$wpdb->prefix}okusers t2 WHERE t1.user_id = t2.id AND t1.is_active");
        $allusers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}okusers WHERE is_active = 1 ORDER BY id ASC");

        if (!empty($allusers)) {

            foreach ($allusers as $user) {

                $id = $user->id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $token = $user->token;
                $session_key = $user->session_key;

            ?>

                <option <?php if (!empty($_GET['user_id']) && $id == $_GET['user_id']) {
                            echo 'selected';
                        }
                        ?> value='<?php echo $id; ?>,<?php echo $session_key; ?>,<?php echo $token; ?>'>
                    <p>ID: <?php echo $id ?></p>
                    <p><?php echo $first_name ?></p>
                    <p><?php echo $last_name ?></p>
                </option>

            <?php }
            ?>

            <option disabled="disabled">Groups:</option>

            <?php
            foreach ($allgroups as $group) {

                $group_id = $group->group_id;
                $group_name = $group->group_name;
                $token = $group->token;
                $session_key = $group->session_key;

            ?>

                <option <?php if (!empty($_GET['group_id']) && $group_id == $_GET['group_id']) {
                            echo 'selected';
                        }
                        ?> value='com,<?php echo $group_id; ?>,<?php echo $session_key;?>,<?php echo $token; ?>'>
                    ID: <?php echo $group_id ?>
                    <?php echo $group_name ?>

                </option>

<?php }
        } else {
            echo '<option value="" disabled>No authorized user or group</option>';
        }
    }
}
?>

<?php

function getUserCommunites($id)
{
    global $wpdb;

    if (isset($_GET['account']) && $_GET['account'] == 'vk') {

        $alluserscom = $wpdb->get_results("SELECT id, community_id, first_name,last_name,community_name, community_type, t2.is_active FROM {$wpdb->prefix}vkusers t1, {$wpdb->prefix}vkcommunities t2 WHERE t1.id=t2.user_id AND t1.id={$id} ORDER BY id ASC");

        if (!empty($alluserscom)) {

            foreach ($alluserscom as $usercom) {
                $community_id = $usercom->community_id;
                $community_name = $usercom->community_name;
                $community_type = $usercom->community_type;
                $is_active = $usercom->is_active;

?>
                <form action="" method="post">
                    <ul>
                        <li style="border: 1px solid black; padding:20px;">
                            <?php if (!empty($community_name)) {
                                echo "Community: " . $community_name;
                            } ?>
                            <?php if (!empty($community_type)) {
                                echo $community_type;
                            } ?>
                            <?php if ($is_active) {
                                echo "<p style='color:green'>Activated</p>";
                            } else {
                                echo "<p style='color:red'>Deactivated</p>";
                            } ?>
                            <button type="submit" class="btn btn-primary" name="<?= $community_id ?>">Activate/Deactivate</button>
                            <button type="submit" class="btn btn-primary" name="<?= $community_id . 'del' ?>">Delete page</button>
                        </li>
                    </ul>
                </form>
                <?php
                if (isset($_POST[$community_id . 'del'])) {
                    $wpdb->delete("{$wpdb->prefix}vkcommunities", array('community_id' => $community_id));
                    header('Location: admin.php?page=poster_accounts&account=vk');
                }
                if (isset($_POST[$community_id])) {
                    $is_active = $wpdb->get_row("SELECT is_active FROM {$wpdb->prefix}vkcommunities WHERE community_id = $community_id");
                    if ($is_active->is_active) {
                        $wpdb->update("{$wpdb->prefix}vkcommunities", array('is_active' => 0), array('community_id' => $community_id));
                        header('Location: admin.php?page=poster_accounts&account=vk');
                    } else {
                        $wpdb->update("{$wpdb->prefix}vkcommunities", array('is_active' => 1), array('community_id' => $community_id));
                        header('Location: admin.php?page=poster_accounts&account=vk');
                    }
                }
            }
        }
    } else if (isset($_GET['account']) && $_GET['account'] == 'ok') {

        $allusersgrp = $wpdb->get_results("SELECT id, group_id, first_name,last_name, group_name, t2.is_active FROM {$wpdb->prefix}okusers t1, {$wpdb->prefix}okgroups t2 WHERE t1.id=t2.user_id AND t1.id={$id} ORDER BY id ASC");

        if (!empty($allusersgrp)) {

            foreach ($allusersgrp as $usergrp) {
                $group_id = $usergrp->group_id;
                $group_name = $usergrp->group_name;
                $is_active = $usergrp->is_active;

                ?>
                <form action="" method="post">
                    <ul>
                        <li style="border: 1px solid black; padding:20px;">
                            <?php if (!empty($group_name)) {
                                echo "Group: " . $group_name;
                            } ?>
                            <?php if ($is_active) {
                                echo "<p style='color:green'>Activated</p>";
                            } else {
                                echo "<p style='color:red'>Deactivated</p>";
                            } ?>
                            <button type="submit" class="btn btn-primary" name="<?= $group_id ?>">Activate/Deactivate</button>
                            <button type="submit" class="btn btn-primary" name="<?= $group_id . 'del' ?>">Delete page</button>
                        </li>
                    </ul>
                </form>
<?php
                if (isset($_POST[$group_id . 'del'])) {
                    $wpdb->delete("{$wpdb->prefix}okgroups", array('group_id' => $group_id));
                    header('Location: admin.php?page=poster_accounts&account=ok');
                }
                if (isset($_POST[$group_id])) {
                    $is_active = $wpdb->get_row("SELECT is_active FROM {$wpdb->prefix}okgroups WHERE group_id = $group_id");
                    if ($is_active->is_active) {
                        $wpdb->update("{$wpdb->prefix}okgroups", array('is_active' => 0), array('group_id' => $group_id));
                        header('Location: admin.php?page=poster_accounts&account=ok');
                    } else {
                        $wpdb->update("{$wpdb->prefix}okgroups", array('is_active' => 1), array('group_id' => $group_id));
                        header('Location: admin.php?page=poster_accounts&account=ok');
                    }
                }
            }
        }
    }
}


?>