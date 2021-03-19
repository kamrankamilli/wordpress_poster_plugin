<?php
include(plugin_dir_path(dirname(__FILE__)) . '/controller/wallpost.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
    <style>
        a {
            outline: none;
            text-decoration: none;
            color: black;
        }

        .fa-vk {
            color: blue;
        }

        .fa-odnoklassniki {
            color: orange;
        }

        a:hover {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mt-3">
            <div class="col-md-6">
                <h1>Share</h1>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="row mt-5">
                    <div class="col-md-2">
                        <a href="admin.php?page=poster_share&share=vk"><i class="fab fa-vk fa-3x"></i>VKontakte</a>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-2">
                        <a href="admin.php?page=poster_share&share=ok"><i class="fab fa-odnoklassniki fa-3x"></i>Odnoklassniki</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row mt-3">
                    <div class="col">
                        <?php if (isset($_GET['share']) && $_GET['share'] == 'vk') { ?>
                            <h2>VKontakte</h2>
                            <form action="" method="post">
                                <div class="form-group" style="margin-top:20px;width:360px;">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <select class="form-select" name="users[]" id="selectbox">
                                                    <option value=" ">Select a user or community</option>
                                                    <option value="allaccounts">Share to active accounts and communities</option>
                                                    <option disabled>Users:</option>
                                                    <?php include(plugin_dir_path(dirname(__FILE__)) . '/controller/fetchData.php');
                                                    fetchDataToSelectBox(); ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="form-group">
                                                <select class="form-select" name="products" id="selectbox">
                                                    <option value="">Select a product</option>
                                                    <?php productSelectBox(wc_product_data());
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-2">OR</div>
                                        <div class="col-5">
                                            <div class="form-group">
                                                <select class="form-select" name="posts" id="selectbox">
                                                    <option value="">Select a post</option>
                                                    <?php
                                                    postSelectBox(wp_posts()); ?>
                                                </select>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="form-group w-50">
                                    <div class="row mt-3">
                                        <div class="col-8">
                                            <textarea class="form-control" name="message" placeholder="write a post"></textarea>
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-primary" style="height:auto;" type="submit" name="share">Share</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } else if (isset($_GET['share']) && $_GET['share'] == 'ok') { ?>
                            <h2>Odnoklassniki</h2>
                            <form action="" method="post">
                                <div class="form-group" style="margin-top:20px;width:360px;">
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <select class="form-select" name="users[]" id="selectbox">
                                                    <option value=" ">Select a user or group</option>
                                                    <option value="allaccounts">Share to active accounts and groups</option>
                                                    <option disabled>Users:</option>
                                                    <?php include(plugin_dir_path(dirname(__FILE__)) . '/controller/fetchData.php');
                                                    fetchDataToSelectBox(); ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="form-group">
                                                <select class="form-select" name="products" id="selectbox">
                                                    <option value="">Select a product</option>
                                                    <?php productSelectBox(wc_product_data());
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-2">OR</div>
                                        <div class="col-5">
                                            <div class="form-group">
                                                <select class="form-select" name="posts" id="selectbox">
                                                    <option value="">Select a post</option>
                                                    <?php
                                                    postSelectBox(wp_posts()); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group w-50">
                                    <div class="row mt-3">
                                        <div class="col-8">
                                            <textarea class="form-control" name="message" placeholder="write a post"></textarea>
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-primary" style="height:auto;" type="submit" name="share">Share</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div><?php if (!empty($error)) { ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $error; ?>
                        </div>
                    </div> <?php } ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php if (!empty($arrPostUrl)) {
                            foreach ($arrPostUrl as $url) {

                        ?>
                                <div>
                                    <h5><?php echo $url['first_name'] . " " . $url['last_name']; ?></h5>
                                    <p><a href="<?= $url['url']; ?>">Post Link</a></p>
                                </div>
                        <?php }
                        } ?>
                        <?php if (!empty($arrCommunityUrl)) {
                            foreach ($arrCommunityUrl as $url) {

                        ?>
                                <div>
                                    <h5><?php echo $url['community_name']; ?></h5>
                                    <p><a href="<?= $url['com_url']; ?>">Post Link</a></p>
                                </div>
                        <?php }
                        } ?>
                        <?php if (!empty($link)) {
                        ?>
                            <div>
                                <h5><a href="<?= $link; ?>">Post Link</a></h5>
                            </div>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>

</html>