<?php

include(plugin_dir_path(dirname(__FILE__)) . '/controller/uploadData.php');

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
                <h1>Accounts</h1>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="row mt-5">
                    <div class="col-md-2">
                        <a href="admin.php?page=poster_accounts&account=vk"><i class="fab fa-vk fa-3x"></i>VKontakte</a>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-2">
                        <a href="admin.php?page=poster_accounts&account=ok"><i class="fab fa-odnoklassniki fa-3x"></i>Odnoklassniki</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row mt-3">
                    <div class="col">
                        <?php if (isset($_GET['account']) && $_GET['account'] == 'vk') { ?>
                            <h2>VKontakte</h2>
                            <div class="row mt-3">
                                <div class="col">
                                    <?php if (!empty($message)) {
                                        echo $message;
                                    } ?></div>
                            </div>
                            <div class="row" style="margin-top:10px;">
                                <div class="col">
                                    <ul>
                                        <?php include(plugin_dir_path(dirname(__FILE__)) . 'controller/fetchData.php');
                                        fetchData(); ?>
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                ADD ACCOUNT
                            </button>
                        <?php } else if (isset($_GET['account']) && $_GET['account'] == 'ok') { ?>
                            <h2>Odnoklassniki</h2>
                            <div class="row mt-3">
                                <div class="col">
                                    <?php if (!empty($message)) {
                                        echo $message;
                                    } ?></div>
                            </div>
                            <div class="row" style="margin-top:10px;">
                                <div class="col">
                                    <ul>
                                        <?php include(plugin_dir_path(dirname(__FILE__)) . 'controller/fetchData.php');
                                        fetchData(); ?>
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                ADD ACCOUNT
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ADD ACCOUNT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>First click "AUTHORIZE ACCOUNT" button. Autorize your account and copy URL</h5>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 offset-4">
                            <a class="btn btn-primary" onclick="popup();" href=<?= $url ?> target="popup">AUTHORIZE ACCOUNT</a>
                        </div>
                    </div>
                    <form action="" method="POST">
                        <div class="row mt-3 mb-3">
                            <div class="col">
                                <input type="text" class="form-control" name='url' placeholder="paste url">
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src="./js/popup.js"></script>
</body>

</html>