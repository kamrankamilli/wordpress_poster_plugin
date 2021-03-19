<?php
include(plugin_dir_path(dirname(__FILE__)) . '/controller/wc_products.php');
include(plugin_dir_path(dirname(__FILE__)) . '/controller/wp_posts.php');
include(plugin_dir_path(dirname(__FILE__)) . '/controller/uploadData.php');

$patternUserID = "/(.*?),/i";
$patternUserToken = "/\s*,(.*?)$/";
$message = "";
$post_url = "";
$community_url = "";
$is_communityPattern = "/(com),/i";


$json = file_get_contents("https://connect.ok.ru/oauth/authorize?client_id=512000974893&scope=VALUABLE_ACCESS,SET_STATUS,PHOTO_CONTENT,LONG_ACCESS_TOKEN,PUBLISH_TO_STREAM,GROUP_CONTENT,VIDEO_CONTENT&response_type=token&redirect_uri=http://localhost/wordpress/wp-admin/admin.php?page=poster_plugin");
$data = json_decode($json, true);

$error = "";

$patternID = "/{id}/mi";
$patternTitle = "/{title}/mi";
$patternContent = "/{content_short_40}/mi";
$patternContentFull = "/{content_full}/mi";
$patternLink = "/{link}/mi";
$patternProductRegularPrice = "/{product_regular_price}/mi";
$patternProductSalePrice = "/{product_sale_price}/mi";
$patternUniqueID = "/{uniq_id}/mi";
$patternTags  = "/{tags}/mi";
$patternCategories = "/{categories}/mi";
$patternImadeUrl = "/{featured_image_url}/mi";
$patternPostExcerpt = "/{excerpt}/mi";
$patternPostAuthorName = "/{author}/mi";

$productPatterns = array(
  $patternID,
  $patternTitle,
  $patternContent,
  $patternContentFull,
  $patternLink,
  $patternProductRegularPrice,
  $patternProductSalePrice,
  $patternUniqueID,
  $patternTags,
  $patternCategories,
  $patternImadeUrl
);
$postPatterns = array(
  $patternID,
  $patternTitle,
  $patternContent,
  $patternPostExcerpt,
  $patternPostAuthorName,
  $patternLink,
  $patternUniqueID,
  $patternImadeUrl,
  $patternCategories,
  $patternTags


);
$arrPostUrl = array();
$arrCommunityUrl = array();
$arrProducts = wc_product_data();
$arrPosts = wp_posts();

if (isset($_POST['share'])) {

  $message = $_POST['message'];
  $pickedProductID = $_POST['products'];
  $pickedPostID = $_POST['posts'];

  //check if user exist in selectbox
  if ($_POST['users'][0] != " ") {

    if (isset($_GET['share']) && $_GET['share'] == 'vk') { //Vkontakte

      if ($_POST['users'][0] == 'allaccounts') {
        if (!empty($message)) {
          global $wpdb;
          $activeusers = $wpdb->get_results("SELECT id, first_name, last_name, token FROM {$wpdb->prefix}vkusers WHERE is_active=1");
          foreach ($activeusers as $activeuser) {

            $user_id = $activeuser->id;
            $first_name = $activeuser->first_name;
            $last_name = $activeuser->last_name;
            $token = $activeuser->token;

            if (!empty($pickedProductID) && empty($pickedPostID)) {
              $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
              $post_url = VKapi($user_id, $postMessage, $token);
              array_push($arrPostUrl, array('first_name' => $first_name, 'last_name' => $last_name, 'url' => $post_url));
              insert_shared_products($arrProducts, $user_id, $community_id = NULL, $pickedProductID, $post_url, $postMessage);
            } else if (!empty($pickedPostID) && empty($pickedProductID)) {
              $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
              $post_url = VKapi($user_id, $postMessage, $token);
              array_push($arrPostUrl, array('first_name' => $first_name, 'last_name' => $last_name, 'url' => $post_url));
              insert_shared_posts($arrPosts, $user_id, $community_id = NULL, $pickedPostID, $post_url, $postMessage);
            } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
              $error = "<p style='color:red'>Select a product or post</p>";
            } else {
              $post_url = VKapi($user_id, $message, $token);
              array_push($arrPostUrl, array('first_name' => $first_name, 'last_name' => $last_name, 'url' => $post_url));
            }
          }
          $activecommunities = $wpdb->get_results("SELECT community_id, community_name, token FROM {$wpdb->prefix}vkusers t1,  {$wpdb->prefix}vkcommunities t2 WHERE t2.is_active =1 AND t1.id = t2.user_id");
          foreach ($activecommunities as $activecommunity) {

            $community_id = $activecommunity->community_id;
            $community_name = $activecommunity->community_name;
            $token = $activecommunity->token;
            if (!empty($pickedProductID) && empty($pickedPostID)) {
              $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
              $com_url = vkCommunityPost($community_id, $postMessage, $token);
              insert_shared_products($arrProducts, $user_id = NULL, $community_id, $pickedProductID, $com_url, $postMessage);
              array_push($arrCommunityUrl, array('community_name' => $community_name, 'com_url' => $com_url));
            } else if (!empty($pickedPostID) && empty($pickedProductID)) {
              $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
              $com_url = vkCommunityPost($community_id, $postMessage, $token);
              insert_shared_posts($arrPosts, $user_id = NULL, $community_id, $pickedPostID, $com_url, $postMessage);
              array_push($arrCommunityUrl, array('community_name' => $community_name, 'com_url' => $com_url));
            } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
              $error = "<p style='color:red'>Select a product or post</p>";
            } else {
              $com_url = vkCommunityPost($community_id, $message, $token);
              array_push($arrCommunityUrl, array('community_name' => $community_name, 'com_url' => $com_url));
            }
          }
        } else {
          $error = "<p style='color:red'>Write something to post, cant be empty.</p>";
        }
      } else {

        //foreach selected selectbox get value
        foreach ($_POST['users'] as $selected) {

          if (preg_match_all($is_communityPattern, $selected)) {

            preg_match_all('/(.*?),/i', $selected, $id);
            $community_id = $id[1][1];

            preg_match_all('/(?<=,)(?:(?!,).)*$/i', $selected, $token);
            $token = $token[0][0];

            if (empty($message)) {
              $error =  "<p style='color:red'>Write something to post, cant be empty.</p>";
            } else {

              if (!empty($pickedProductID) && empty($pickedPostID)) {
                $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
                $link = vkCommunityPost($community_id, $postMessage, $token);
                insert_shared_products($arrProducts, $user_id = NULL, $community_id, $pickedProductID, $link, $postMessage);
              } else if (!empty($pickedPostID) && empty($pickedProductID)) {
                $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
                $link = vkCommunityPost($community_id, $postMessage, $token);
                insert_shared_posts($arrPosts, $user_id = NULL, $community_id, $pickedPostID, $link, $postMessage);
              } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
                $error = "<p style='color:red'>Select a product or post</p>";
              } else {
                $link = vkCommunityPost($community_id, $message, $token);
              }
            }
          } else {

            //with pattern match id and token
            preg_match_all($patternUserID, $selected, $id);
            preg_match_all($patternUserToken, $selected, $token);
            $user_id = $id[1][0];
            $token = $token[1][0];



            $pickedUser  = $_POST['users'][0];


            if (empty($message)) {
              $error =  "<p style='color:red'>Write something to post, cant be empty.</p>";
            } else {

              if (!empty($pickedProductID) && empty($pickedPostID)) {
                $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
                $link = VKapi($user_id, $postMessage, $token);
                insert_shared_products($arrProducts, $user_id, $community_id = NULL, $pickedProductID, $link, $postMessage);
              } else if (!empty($pickedPostID) && empty($pickedProductID)) {
                $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
                $link = VKapi($user_id, $postMessage, $token);
                insert_shared_posts($arrPosts, $user_id, $community_id = NULL, $pickedPostID, $link, $postMessage);
              } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
                $error = "<p style='color:red'>Select a product or post</p>";
              } else {
                $link = VKapi($user_id, $message, $token);
              }
            }
          }
        }
      }
    } else if (isset($_GET['share']) && $_GET['share'] == 'ok') { // Odnoklassniki

      $okOptions = get_option('okposter');
      foreach ($okOptions as $key => $option) {
        $appid = $key;
        $publicKey = $option[0];
        $secretKey = $option[1];
      }

      if ($_POST['users'][0] == 'allaccounts') {

        if (empty($message)) {

          $error =  "<p style='color:red'>Write something to post, cant be empty.</p>";
        } else {

          global $wpdb;
          $activeusers = $wpdb->get_results("SELECT id, first_name, last_name, token, session_key FROM {$wpdb->prefix}okusers WHERE is_active=1");
          foreach ($activeusers as $activeuser) {

            $user_id = $activeuser->id;
            $first_name = $activeuser->first_name;
            $last_name = $activeuser->last_name;
            $token = $activeuser->token;
            $sessionSecretKey = $activeuser->session_key;

            if (!empty($pickedProductID) && empty($pickedPostID)) {
              $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
              $umessage = json_encode(array(
                'media' =>
                array(
                  0 =>
                  array(
                    'type' => 'text',
                    'text' => $postMessage,
                  ),
                ),
              ));
              $post_url = okUserPost($user_id, $publicKey, $umessage, $sessionSecretKey, $token);
              array_push($arrPostUrl, array('first_name' => $first_name, 'last_name' => $last_name, 'url' => $post_url));
              insert_shared_products($arrProducts, $user_id, $group_id = NULL, $pickedProductID, $post_url, $postMessage);
            } else if (!empty($pickedPostID) && empty($pickedProductID)) {
              $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
              $umessage = json_encode(array(
                'media' =>
                array(
                  0 =>
                  array(
                    'type' => 'text',
                    'text' => $postMessage,
                  ),
                ),
              ));
              $post_url = okUserPost($user_id, $publicKey, $umessage, $sessionSecretKey, $token);
              array_push($arrPostUrl, array('first_name' => $first_name, 'last_name' => $last_name, 'url' => $post_url));
              insert_shared_posts($arrPosts, $user_id, $group_id = NULL, $pickedPostID, $post_url, $postMessage);
            } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
              $error = "<p style='color:red'>Select a product or post</p>";
            } else {
              $post_url = okUserPost($user_id, $publicKey, $message, $sessionSecretKey, $token);
              array_push($arrPostUrl, array('first_name' => $first_name, 'last_name' => $last_name, 'url' => $post_url));
            }
          }
          $activegroups = $wpdb->get_results("SELECT group_id, group_name, token, session_key FROM {$wpdb->prefix}okusers t1,  {$wpdb->prefix}okgroups t2 WHERE t2.is_active =1 AND t1.id = t2.user_id");
          foreach ($activegroups as $activegroup) {

            $group_id = $activegroup->group_id;
            $group_name = $activegroup->group_name;
            $token = $activegroup->token;
            $sessionSecretKey = $activegroup->session_key;

            if (!empty($pickedProductID) && empty($pickedPostID)) {
              $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
              $message = json_encode(array(
                'media' =>
                array(
                  0 =>
                  array(
                    'type' => 'text',
                    'text' => $postMessage,
                  ),
                ),
              ));
              $com_url = okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token);
              insert_shared_products($arrProducts, $user_id = NULL, $group_id, $pickedProductID, $com_url, $postMessage);
              array_push($arrCommunityUrl, array('community_name' => $group_name, 'com_url' => $com_url));
            } else if (!empty($pickedPostID) && empty($pickedProductID)) {

              $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
              $message = json_encode(array(
                'media' =>
                array(
                  0 =>
                  array(
                    'type' => 'text',
                    'text' => $postMessage,
                  ),
                ),
              ));
              $com_url = okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token);
              insert_shared_posts($arrPosts, $user_id = NULL, $group_id, $pickedPostID, $com_url, $postMessage);
              array_push($arrCommunityUrl, array('community_name' => $group_name, 'com_url' => $com_url));
            } else if (!empty($pickedPostID) && !empty($pickedProductID)) {

              $error = "<p style='color:red'>Select a product or post</p>";
            } else {
              $com_url = okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token);
              array_push($arrCommunityUrl, array('community_name' => $group_name, 'com_url' => $com_url));
            }
          }
        }
      } else {
        foreach ($_POST['users'] as $selected) {

          preg_match_all('/(?<=,)(?:(?!,).)*$/i', $selected, $token);
          $token = $token[0][0];



          if (preg_match_all($is_communityPattern, $selected)) {
            preg_match_all('/(.*?),/i', $selected, $val);
            $group_id  = $val[1][1];
            $sessionSecretKey = $val[1][2];
            if (empty($message)) {

              $error =  "<p style='color:red'>Write something to post, cant be empty.</p>";
            } else {

              if (!empty($pickedProductID) && empty($pickedPostID)) {
                $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
                $message = json_encode(array(
                  'media' =>
                  array(
                    0 =>
                    array(
                      'type' => 'text',
                      'text' => $postMessage,
                    ),
                  ),
                ));
                $link = okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token);
                insert_shared_products($arrProducts, $user_id = NULL, $group_id, $pickedProductID, $link, $postMessage);
              } else if (!empty($pickedPostID) && empty($pickedProductID)) {
                $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
                $message = json_encode(array(
                  'media' =>
                  array(
                    0 =>
                    array(
                      'type' => 'text',
                      'text' => $postMessage,
                    ),
                  ),
                ));
                $link = okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token);
                insert_shared_posts($arrPosts, $user_id = NULL, $group_id, $pickedPostID, $link, $postMessage);
              } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
                $error = "<p style='color:red'>Select a product or post</p>";
              } else {
                $message = json_encode(array(
                  'media' =>
                  array(
                    0 =>
                    array(
                      'type' => 'text',
                      'text' => $postMessage,
                    ),
                  ),
                ));
                $link = okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token);
              }
            }
          } else {

            preg_match_all('/(.*?),/i', $selected, $id);
            $user_id = $id[1][0];
            $sessionSecretKey = $id[1][1];


            if (empty($message)) {

              $error =  "<p style='color:red'>Write something to post, cant be empty.</p>";
            } else {
              if (!empty($pickedProductID) && empty($pickedPostID)) {
                $postMessage = postProduct($arrProducts, $productPatterns, $message, $pickedProductID);
                $message = json_encode(array(
                  'media' =>
                  array(
                    0 =>
                    array(
                      'type' => 'text',
                      'text' => $postMessage,
                    ),
                  ),
                ));
                $link = okUserPost($user_id, $publicKey, $message, $sessionSecretKey, $token);
                insert_shared_products($arrProducts, $user_id, $group_id == NULL, $pickedProductID, $link, $postMessage);
              } else if (!empty($pickedPostID) && empty($pickedProductID)) {
                $postMessage = post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID);
                $message = json_encode(array(
                  'media' =>
                  array(
                    0 =>
                    array(
                      'type' => 'text',
                      'text' => $postMessage,
                    ),
                  ),
                ));
                $link = okUserPost($user_id, $publicKey, $message, $sessionSecretKey, $token);
                insert_shared_posts($arrPosts, $user_id, $group_id = NULL, $pickedPostID, $link, $postMessage);
              } else if (!empty($pickedPostID) && !empty($pickedProductID)) {
                $error = "<p style='color:red'>Select a product or post</p>";
              } else {
                $message = json_encode(array(
                  'media' =>
                  array(
                    0 =>
                    array(
                      'type' => 'text',
                      'text' => $postMessage,
                    ),
                  ),
                ));
                $link = okUserPost($user_id, $publicKey, $message, $sessionSecretKey, $token);
              }
            }
          }
        }
      }
    }
  } else {
    $error =  "<p style='color:red'>Underfined user or community, select a user or community if exists</p>";
  }
}



function getData($url)
{
  $json = file_get_contents($url);
  return json_decode($json, true);
}
function okUserPost($user_id, $publicKey, $message, $sessionSecretKey, $token)
{

  $sig  = md5("application_key={$publicKey}attachment={$message}format=jsonmethod=mediatopic.post{$sessionSecretKey}");
  $url = "https://api.ok.ru/fb.do?application_key={$publicKey}&attachment=" . urlencode($message) . "&format=json&method=mediatopic.post&sig={$sig}&access_token={$token}";

  $response = getData($url);

  if (!empty($response)) {

    $post_url = "https://ok.ru/profile/{$user_id}/statuses/{$response}";

    return $post_url;
  } else {
    $error =  "<p style='color:red'>Something went wrong!</p>";
  }
}
function okGroupPost($group_id, $publicKey, $message, $sessionSecretKey, $token)
{

  $sig  = md5("application_key={$publicKey}attachment={$message}format=jsongid={$group_id}method=mediatopic.posttype=GROUP_THEME{$sessionSecretKey}");
  $url = "https://api.ok.ru/fb.do?application_key={$publicKey}&attachment=" . urlencode($message) . "&format=json&gid={$group_id}&method=mediatopic.post&type=GROUP_THEME&sig={$sig}&access_token={$token}";

  $response = getData($url);
  if (!empty($response)) {

    $post_url = "https://ok.ru/group/{$group_id}/topic/{$response}";

    return $post_url;
  } else {
    $error =  "<p style='color:red'>Something went wrong!</p>";
  }
}
function VKapi($id, $message, $token)
{
  $url = "https://api.vk.com/method/wall.post?owner_id=" . $id . "&friends_only=0&from_group=0&message=" . urlencode($message) . "&access_token=" . $token . "&v=5.52";

  $response = getData($url);

  //check curl response
  if (!empty($response['response'])) {

    $post_url = "https://vk.com/id" . $id . "?w=wall" . $id . "_" . $response['response']['post_id'] . "";

    return $post_url;
  } else {
    $error =  "<p style='color:red'>Something went wrong!</p>";
  }
}

function vkCommunityPost($id, $message, $token)
{
  $url = "https://api.vk.com/method/wall.post?owner_id=-" . $id . "&friends_only=0&from_group=1&message=" . urlencode($message) . "&access_token=" . $token . "&v=5.52";

  $response = getData($url);

  //check curl response
  if (!empty($response['response'])) {

    $community_url = "https://vk.com/public" . $id . "?w=wall-" . $id . "_" . $response['response']['post_id'] . "";
    return $community_url;
  } else {
    $error =  "<p style='color:red'>Something went wrong!</p>";
  }
}
function postProduct($arrProducts, $productPatterns, $message, $pickedProductID)
{


  $key = searchForId($arrProducts, $pickedProductID);


  for ($i = 0; $i < count($productPatterns); $i++) {

    preg_match_all($productPatterns[$i], $message, $result, PREG_OFFSET_CAPTURE);

    if (!empty($result[0])) {

      for ($j = 0; $j < count($result); $j++) {
        switch ($i) {
          case 0:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['id'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 1:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['title'],
              $result[0][0][1],
              strlen($result[0][$j][0])
            );
            break;
          case 2:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['content'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 3:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['full_content'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 4:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['product_link'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 5:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['regular_price'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 6:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['sale_price'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 7:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['unique_id'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 8:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['tag'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 9:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['categories'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 10:
            $message = substr_replace(
              $message,
              $arrProducts[$key]['image_url'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          default:
            $error = "Something went wrong!";
        }
      }
    }
    $result = array();
  }

  $message = $message . "\n" . $arrProducts[$key]['product_link'];

  return $message;
}

function post_WP_Post($arrPosts, $postPatterns, $message, $pickedPostID)
{

  $key = searchForId($arrPosts, $pickedPostID);

  for ($i = 0; $i < count($postPatterns); $i++) {

    preg_match_all($postPatterns[$i], $message, $result, PREG_OFFSET_CAPTURE);

    if (!empty($result[0])) {

      for ($j = 0; $j < count($result); $j++) {

        switch ($i) {
          case 0:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['id'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 1:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['title'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 2:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['content'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 3:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['excerpt'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 4:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['author'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 5:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['link'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 6:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['uniq_id'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 7:
            $message = substr_replace(
              $message,
              $arrPosts[$key]['image_url'],
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 8:
            $conc = "";
            for ($k = 0; $k < count($arrPosts[$key]['categories']); $k++) {

              $conc = $arrPosts[$key]['categories'][$k]->cat_name . ", " . $conc;
            }
            $message = substr_replace(
              $message,
              $conc,
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          case 9:
            $conc = "";
            for ($k = 0; $k < count($arrPosts[$key]['tags']); $k++) {

              $conc = $arrPosts[$key]['tags'][$k]->name . ", " . $conc;
            }
            $message = substr_replace(
              $message,
              $conc,
              $result[0][$j][1],
              strlen($result[0][$j][0])
            );
            break;
          default:
            $error = "Something went wrong!";
        }
      }
    }
  }

  $message = $message . "\n" . $arrPosts[$key]['link'];


  return $message;
}

function insert_shared_posts($arrPosts, $user_id, $community_id, $pickedPostID, $post_url, $message)
{
  global $wpdb;
  $key  = searchForId($arrPosts, $pickedPostID);
  if (isset($_GET['share']) && $_GET['share'] == 'vk') {
    $wpdb->insert("{$wpdb->prefix}vksharedposts", array(
      'shared_post_id' => $arrPosts[$key]['id'],
      'user_id' => $user_id,
      'community_id' => $community_id,
      'title' => $arrPosts[$key]['title'],
      'content' => $message,
      'link' => $post_url
    ));
  } else if (isset($_GET['share']) && $_GET['share'] == 'ok') {
    $wpdb->insert("{$wpdb->prefix}oksharedposts", array(
      'shared_post_id' => $arrPosts[$key]['id'],
      'user_id' => $user_id,
      'group_id' => $community_id,
      'title' => $arrPosts[$key]['title'],
      'content' => $message,
      'link' => $post_url
    ));
  }
}
function insert_shared_products($arrProducts, $user_id, $community_id, $pickedProductID, $post_url, $message)
{
  global $wpdb;
  $key  = searchForId($arrProducts, $pickedProductID);

  if (isset($_GET['share']) && $_GET['share'] == 'vk') {
    $wpdb->insert("{$wpdb->prefix}vksharedproducts", array(
      'shared_product_id' => $arrProducts[$key]['id'],
      'user_id' => $user_id,
      'community_id' => $community_id,
      'title' => $arrProducts[$key]['title'],
      'content' => $message,
      'link' => $post_url
    ));
  } else if (isset($_GET['share']) && $_GET['share'] == 'ok') {
    $wpdb->insert("{$wpdb->prefix}oksharedproducts", array(
      'shared_product_id' => $arrProducts[$key]['id'],
      'user_id' => $user_id,
      'group_id' => $community_id,
      'title' => $arrProducts[$key]['title'],
      'content' => $message,
      'link' => $post_url
    ));
  }
}

function searchForId($arrProducts, $pickedProductID)
{
  foreach ($arrProducts as $key => $val) {
    if ($val['id'] == $pickedProductID) {
      return $key;
    }
  }
  return null;
}
