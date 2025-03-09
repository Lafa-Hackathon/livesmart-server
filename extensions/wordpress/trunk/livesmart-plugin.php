<?php

/*
  Plugin Name: LiveSmart Server Video
  Plugin URI: https://livesmart.video
  Description: LiveSmart Widget HTML and JavaScript.
  Version: 2.1
  Author: LiveSmart
  Author URI: https://livesmart.video
 */

add_action('admin_menu', 'livesmart_plugin_settings');


function livesmart_insert_user($username, $password, $email, $firstName, $lastName, $lsRepUrl) {
    $posts = http_build_query(array('type' => 'addagent', 'username' => $username, 'password' => $password, 'firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'tenant' => $username));
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $lsRepUrl . 'server/script.php',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $posts,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST=> false,
        CURLOPT_TIMEOUT => 10
    ));
    $response = @curl_exec($ch);
    if (curl_errno($ch) > 0) {
        curl_close($ch);
        return false;
    } else {

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseCode !== 200) {
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        $posts = http_build_query(array('type' => 'addroom', 'lsRepUrl' => $lsRepUrl, 'agentId' => $username, 'agentName' => $firstName . ' ' . $lastName, 'visitorName' => '', 'agentShortUrl' => $username . '_a', 'visitorShortUrl' => $username, 'is_active' => true));
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $lsRepUrl . 'server/script.php',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $posts,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST=> false,
            CURLOPT_TIMEOUT => 10
        ));

        $response = @curl_exec($ch);
        if (curl_errno($ch) > 0) {
            curl_close($ch);
            return false;
        } else {

            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($responseCode !== 200) {
                curl_close($ch);
                return false;
            }
            curl_close($ch);
            return true;
        }
    }
}

function livesmart_check_user($username, $password, $email, $firstName, $lastName, $lsRepUrl) {
    $posts = http_build_query(array('type' => 'loginagent', 'username' => $username, 'password' => $password));
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $lsRepUrl . 'server/script.php',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $posts,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST=> false,
        CURLOPT_TIMEOUT => 10
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    if (!$response) {
        livesmart_insert_user($username, $password, $email, $firstName, $lastName, $lsRepUrl);
    }
}

function livesmart_delete_user($username, $lsRepUrl) {
    $posts = http_build_query(array('type' => 'deleteagentbyusername', 'username' => $username));
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $lsRepUrl . 'server/script.php',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $posts,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST=> false,
        CURLOPT_TIMEOUT => 10
    ));

    curl_exec($ch);
    curl_close($ch);
    $posts = http_build_query(array('type' => 'deleteroombyagent', 'agentId' => $username));
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $lsRepUrl . 'server/script.php',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $posts,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST=> false,
        CURLOPT_TIMEOUT => 10
    ));

    curl_exec($ch);
    curl_close($ch);
}


function livesmart_login( $user_login, $user ) {
    if ($user_login !== 'admin') {
        livesmart_check_user($user_login, '123456', $user->data->user_email, $user->data->display_name, '', get_option('livesmart_server_url'));
    }
}

add_action('wp_login', 'livesmart_login', 10, 2);

function livesmart_plugin_settings() {
    add_menu_page('LiveSmart Settings', 'LiveSmart Settings', 'administrator', 'fwds_settings', 'livesmart_display_settings');
    add_submenu_page('fwds_settings', 'LiveSmart Dashboard', 'LiveSmart Dashboard',  'publish_pages', 'fwds_visitors', 'livesmart_display_dash');
}

function livesmart_display_dash() {
    $current_user = wp_get_current_user();
    $livesmart_server_url = (get_option('livesmart_server_url') != '') ? get_option('livesmart_server_url') : '';
    if ($livesmart_server_url) {
        echo '<iframe src="'.$livesmart_server_url.'dash/integration.php?wplogin='.$current_user->user_login.'&url='.base64_encode($livesmart_server_url).'" style="background-color:#ffffff; padding: 0; margin:0" width="100%" height="605" ></iframe>';
    } else {
        echo 'Please define server URL from the settings page';
    }
}


function livesmart_display_settings() {

    $livesmart_server_url = (get_option('livesmart_server_url') != '') ? get_option('livesmart_server_url') : '';
    $html = '<div class="wrap">
            <form method="post" name="options" action="options.php">

            <h2>Select Your Settings</h2>' . wp_nonce_field('update-options') . '
            <table width="300" cellpadding="2" class="form-table">
                <tr>
                    <td align="left" scope="row">
                    <label>Server URL</label>
                    </td>
                    <td><input type="text" style="width: 400px;" name="livesmart_server_url"
                    value="' . $livesmart_server_url . '" /></td>
                </tr>

            </table>
            <p class="submit">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="livesmart_server_url" />
                <input type="submit" name="Submit" value="Update" />
            </p>
            </form>
        </div>';
    echo $html;
}

?>