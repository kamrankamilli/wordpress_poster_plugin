<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
</head>

<body>
    <div class="container">
        <div class="row mt-3">
            <div class="col-md-6">
                <h1>Settings</h1>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="row mt-5">
                    <div class="col-md-2">
                        <a style="text-decoration:none; outline: none; color:black;" href="admin.php?page=poster_settings&settings=vk"><i style="color:blue;" class="fab fa-vk fa-3x"></i>VKontakte</a>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-2">
                        <a style="text-decoration:none; outline: none; color:black;" href="admin.php?page=poster_settings&settings=ok"> <i style="color: orange;" class="fab fa-odnoklassniki fa-3x"></i>Odnoklassniki</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row mt-5">
                    <div class="col">
                        <?php
                        include(plugin_dir_path(dirname(__FILE__)) . 'controller/settings.php');
                        ?>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="VKmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add new App</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="vkappid" class="col-form-label">App ID</label>
                                <input type="text" class="form-control" name="vkappid" id="vkappid">
                            </div>
                            <div class="mb-3">
                                <label for="vksecretkey" class="col-form-label">Secret Key</label>
                                <input type="text" class="form-control" name="vksecretkey" id="vksecretkey">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="vk" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="OdnoklassnikiModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add new App</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="okappid" class="col-form-label">App ID</label>
                                <input type="text" class="form-control" name="okappid" id="okappid">
                            </div>
                            <div class="mb-3">
                                <label for="okpublickey" class="col-form-label">App Public Key</label>
                                <input type="text" class="form-control" name="okpublickey" id="okpublickey">
                            </div>
                            <div class="mb-3">
                                <label for="oksecretkey" class="col-form-label">App Secret Key</label>
                                <input type="text" class="form-control" name="oksecretkey" id="oksecretkey">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="ok" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

</body>

</html>