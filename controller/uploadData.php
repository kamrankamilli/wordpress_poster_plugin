<?php

$message = "";

if (isset($_GET['account']) && $_GET['account'] == 'vk') {
    $vkOptions = get_option('vkposter');

    foreach ($vkOptions as $key => $val) {
        $appid = $key;
    }


    $url = "https://oauth.vk.com/authorize?client_id=" . $appid . "&display=popup&redirect_uri=https://oauth.vk.com/blank.html&scope=wall,offline&response_type=token&v=5.130";
} else if (isset($_GET['account']) && $_GET['account'] == 'ok') {
    $okOptions = get_option('okposter');
    foreach ($okOptions as $key => $option) {
        $appid = $key;
        $publicKey = $option[0];
        $secretKey = $option[1];
    }

    $url = "https://connect.ok.ru/oauth/authorize?client_id=" . $appid . "&scope=VALUABLE_ACCESS,LONG_ACCESS_TOKEN,PHOTO_CONTENT,GROUP_CONTENT,VIDEO_CONTENT,APP_INVITE,GET_EMAIL,PUBLISH_TO_STREAM&response_type=token&redirect_uri=https://apiok.ru/oauth_callback";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    //get url from form input
    if (isset($_GET['account']) && $_GET['account'] == 'vk') {



        $url = $_POST['url'];

        //match user id and token in url
        $patternUserId = "/user_id=([0-9]{9})/i";
        $patternToken = '/\s*access_token=(.*?)&/i';
        preg_match_all($patternUserId, $url, $user_id);
        preg_match_all($patternToken, $url, $token);

        //get json user data
        $userInfo = "https://api.vk.com/method/users.get?user_id=" . $user_id[1][0] . "&access_token=" . $token[1][0] . "&v=5.52";
        $json = file_get_contents($userInfo);
        $user_data = json_decode($json, true);

        $id = $user_data['response'][0]['id'];
        $first_name = $user_data['response'][0]['first_name'];
        $last_name = $user_data['response'][0]['last_name'];
        $token = $token[1][0];

        $userCommunity = "https://api.vk.com/method/groups.get?user_id=" . $user_id[1][0] . "&extended=1&filter=admin,editor,moder,advertiser&access_token=" . $token . "&v=5.52";
        $json = file_get_contents($userCommunity);
        $user_community = json_decode($json, true);

        $userData = array(

            'user_id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'is_active' => '1',
            'token' => $token

        );


        if (userExist($id)) {

            $message = "<p style='color:red'>This user is already exists!</p>";
        } else {

            insertData($userData);

            foreach ($user_community['response']['items'] as $community) {

                $community_id = $community['id'];
                $community_name = $community['name'];
                $community_type = $community['type'];

                $communityData  = array(
                    'user_id' => $id,
                    'community_id' => $community_id,
                    'community_name' => $community_name,
                    'community_type' => $community_type,
                    'is_active' => '1'
                );
                insertCommunity($communityData);
            }
        }
    } else if (isset($_GET['account']) && $_GET['account'] == 'ok') {
        $url = $_POST['url'];

        $patternToken = '/\s*access_token=(.*?)&/i';
        $patternSessionSecretKey = '/\s*session_secret_key=(.*?)&/i';

        preg_match_all($patternToken, $url, $token);
        preg_match_all($patternSessionSecretKey, $url, $sessionSecretKey);
        $token = $token[1][0];
        $sessionSecretKey = $sessionSecretKey[1][0];


        $okOptions = get_option('okposter');
        foreach ($okOptions as $key => $option) {
            $appid = $key;
            $publicKey = $option[0];
            $secretKey = $option[1];
        }

        $sig = md5("application_key={$publicKey}format=jsonmethod=users.getCurrentUser{$sessionSecretKey}");
        $userInfo = "https://api.ok.ru/fb.do?application_key={$publicKey}&format=json&method=users.getCurrentUser&sig={$sig}&access_token={$token}";
        $json = file_get_contents($userInfo);
        $data = json_decode($json, true);
        $user_id = $data['uid'];
        $first_name  = $data['first_name'];
        $last_name = $data['last_name'];

        if (!empty($data)) {

            $userData = array(
                'user_id' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'is_active' => '1',
                'token' => $token,
                'session_key' => $sessionSecretKey
            );

            if (userExist($userData['user_id'])) {

                $message = "<p style='color:red'>This user is already exists!</p>";
            } else {

                insertData($userData);

                $sig = md5("application_key={$publicKey}format=jsonmethod=group.getUserGroupsV2{$sessionSecretKey}");
                $userGroupsId = "https://api.ok.ru/fb.do?application_key={$publicKey}&format=json&method=group.getUserGroupsV2&sig={$sig}&access_token={$token}";
                $json = file_get_contents($userGroupsId);
                $data = json_decode($json, true);

                if (!empty($data)) {

                    foreach ($data['groups'] as $val) {
                        if (
                            $val['status'] == "ADMIN" ||
                            $val['status'] == "ANALYST" ||
                            $val['status'] == "EDITOR" ||
                            $val['status'] == "MODERATOR" || $val['status'] == "SUPER_MODERATOR"
                        ) {
                            $groupId = $val['groupId'];
                            $sig = md5("application_key={$publicKey}fields=name,uidformat=jsonmethod=group.getInfouids={$groupId}{$sessionSecretKey}");
                            $userGroups = "https://api.ok.ru/fb.do?application_key={$publicKey}&fields=name,uid&format=json&method=group.getInfo&uids={$groupId}&sig={$sig}&access_token={$token}";
                            $json = file_get_contents($userGroups);
                            $data = json_decode($json, true);

                            $groupsData = array('user_id' => $user_id, 'group_id' => $data[0]['uid'], 'group_name' => $data[0]['name'], 'is_active' => '1');
                            insertCommunity($groupsData);
                        }
                    }
                } else {
                    $message = "groups not available";
                }
            }
        } else {

            $message = "Something went wrong!";
        }
    }
}

function insertData($alldata)
{
    global $wpdb;
    if (isset($_GET['account']) && $_GET['account'] == 'vk') {

        $wpdb->insert("{$wpdb->prefix}vkusers", array('id' => $alldata['user_id'], 'first_name' => $alldata['first_name'], 'last_name' => $alldata['last_name'], 'is_active' => $alldata['is_active'], 'token' => $alldata['token']));
    } else if (isset($_GET['account']) && $_GET['account'] == 'ok') {

        $wpdb->insert("{$wpdb->prefix}okusers", array('id' => $alldata['user_id'], 'first_name' => $alldata['first_name'], 'last_name' => $alldata['last_name'], 'is_active' => $alldata['is_active'], 'token' => $alldata['token'], 'session_key' => $alldata['session_key']));
    }
}

function insertCommunity($alldata)
{
    global $wpdb;
    if (isset($_GET['account']) && $_GET['account'] == 'vk') {

        $wpdb->insert("{$wpdb->prefix}vkcommunities", array('community_id' => $alldata['community_id'], 'user_id' => $alldata['user_id'], 'community_name' => $alldata['community_name'], 'community_type' => $alldata['community_type'], 'is_active' => $alldata['is_active']));
    } else if (isset($_GET['account']) && $_GET['account'] == 'ok') {

        $wpdb->insert("{$wpdb->prefix}okgroups", array('group_id' => $alldata['group_id'], 'user_id' => $alldata['user_id'], 'group_name' => $alldata['group_name'], 'is_active' => $alldata['is_active']));
    }
}

function userExist($id)
{
    global $wpdb;

    $userExist = false;

    if (isset($_GET['account']) && $_GET['account'] == 'vk') {

        $row = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "vkusers
            WHERE id = %d",
                $id
            )
        );
        if ($row > 0) {
            $userExist = true;
        }
    } else if (isset($_GET['account']) && $_GET['account'] == 'ok') {
        $row = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . "okusers
            WHERE id = %d",
                $id
            )
        );
        if ($row > 0) {
            $userExist = true;
        }
    }
    return $userExist;
}
