<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit ();

$causalf_date = date('Y-m-d H:i:s');
$causalf_host = str_replace("http://", "", sanitize_url($_SERVER['SERVER_NAME']));
$causalf_store_url = $_SERVER['HTTP_HOST'];


if(strrpos($causalf_host, "."))
    $causalf_shop_name = trim(str_replace(".", "-", str_replace("www.", "", substr($causalf_host, 0, strrpos($causalf_host, ".")))), "/");
else 
    $causalf_shop_name = $causalf_host;


$current_user = wp_get_current_user();
$user_login = $current_user->user_login;
$user_email = $current_user->user_email;
$first_name = $current_user->first_name;
$last_name = $current_user->last_name;

$name = $first_name . " " . $last_name;


$causalf_url = "https://www.scripts.causalfunnel.com/assets/cfCKYv1_".$causalf_shop_name."_ProdV1.js";

$causalf_api_url = 'https://us-central1-causalfunnel-21.cloudfunctions.net/WordPressPluginWebHook/';
$causalf_plugin_status = "Uninstalled";
$causalf_response = wp_remote_post( $causalf_api_url, array(
    'method'      => 'POST',
    'timeout'     => 45,
    'redirection' => 5,
    'httpversion' => '1.0',
    'blocking'    => true,
    'headers'     => array(),
    'body'        => array("platform" => "WordPress", "plugin_status" => $causalf_plugin_status, "shop_name" => $causalf_shop_name, 
                    "date" => $causalf_date, "plugin_script_url" => $causalf_url, "store_username" => $user_login, 
                    "store_email" => $user_email, "username" => $name, "store_url" => $causalf_store_url),
    'cookies'     => array()
));
