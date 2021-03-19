<?php

$vkOptions = get_option('vkposter');
$okOptions = get_option('okposter');

if (isset($_POST['vk'])) {

    if (!empty($_POST['vkappid']) && !empty($_POST['vksecretkey'])) {

        $appId = $_POST['vkappid'];
        $secretKey  = $_POST['vksecretkey'];


        $url = "https://oauth.vk.com/access_token?client_id={$appId}&client_secret={$secretKey}&v=5.57&grant_type=client_credentials";

        $app_info = curl($url);
        if (isset($app_info['access_token'])) {
            $new_option = array($appId => $secretKey);
            $new_options = array();
            array_push($new_options, $new_option);

            if (empty($vkOptions)) {
                add_option('vkposter', $new_options);
            }
            $vkOptions[$appId] = $secretKey;
            update_option('vkposter', $vkOptions);
        }
    }
} else if (isset($_POST['ok'])) {

    if (!empty($_POST['okappid']) && !empty($_POST['oksecretkey']) && !empty($_POST['okpublickey'])) {

        $appId = $_POST['okappid'];
        $publicKey = $_POST['okpublickey'];
        $secretKey  = $_POST['oksecretkey'];

        $new_option = array($appId => array($publicKey, $secretKey));
        $new_options = array();
        array_push($new_options, $new_option);

        if (empty($okOptions)) {
            add_option('okposter', $new_options);
        }
        $okOptions[$appId] = array($publicKey, $secretKey);
        update_option('okposter', $okOptions);
    }
}


if (isset($_GET['settings']) && $_GET['settings'] == 'vk') { ?>

    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">App ID</th>
                <th scope="col">Secret Key</th>
                <th scope="col">Delete</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($vkOptions as $key => $option) : ?>
                <tr>
                    <td><?php echo $key ?></td>
                    <td><?php echo $option ?></td>
                    <td>
                        <form action="" method="POST"><button type="submit" name="<?= $key ?>" class="btn btn-danger">Delete</button></form>
                    </td>
                </tr>
                <?php if (isset($_POST[$key])) {
                    if (isset($vkOptions[$key])) {
                        unset($vkOptions[$key]);
                    }

                    header("Location:admin.php?page=poster_settings&settings=vk");
                }
                update_option('vkposter', $vkOptions); ?>
            <?php endforeach; ?>

        </tbody>
    </table>
    <?php if (empty(get_option('okposter'))) { ?>
    
        <div class="col">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#VKmodal">Add App</button>
        </div> <?php }

} else if (isset($_GET['settings']) && $_GET['settings'] == 'ok') { ?>

    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">App ID</th>
                <th scope="col">Public Key</th>
                <th scope="col">Secret Key</th>
                <th scope="col">Delete</th>

            </tr>
        </thead>
        <tbody>

            <?php foreach ($okOptions as $key => $option) : ?>
                <tr>

                    <td><?php echo $key; ?></td>
                    <td><?php echo $option[0]; ?></td>
                    <td><?php echo $option[1]; ?></td>
                    <td>
                        <form action="" method="POST"><button type="submit" name="<?= $key ?>" class="btn btn-danger">Delete</button></form>
                    </td>
                </tr>
                <?php if (isset($_POST[$key])) {
                    if (isset($okOptions[$key])) {
                        unset($okOptions[$key]);
                    }

                    header("Location:admin.php?page=poster_settings&settings=ok");
                }
                update_option('okposter', $okOptions); ?>

            <?php endforeach; ?>

        </tbody>
    </table>
    <?php if (empty(get_option('okposter'))) { ?>

        <div class="col">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#OdnoklassnikiModal">Add App</button>
        </div>
<?php }
}
function curl($url)
{
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            $app_json = curl_exec($handle);
            curl_close($handle);
            return json_decode($app_json, true);
}
