<?php
/**
 * Plugin Name: CausalFunnel DataScience
 * Description: Increase Conversion using AI and Data Science
 * Version: 1.0.14
 * Author: CausalFunnel
 * Author URI: https://www.causalfunnel.com
 * Developer: CausalFunnel Dev
 * Developer URI: https://www.causalfunnel.com
 * Text Domain: causalfunnel
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


function causalf_make_api_call() {
    $causalf_date = gmdate('Y-m-d H:i:s');
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
    $causalf_plugin_status = "Installed";

    
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
}
register_activation_hook( __FILE__, 'causalf_make_api_call' );


add_action( 'wp_enqueue_scripts', 'causalf_wpdd_load_my_scripts' ); 
function causalf_wpdd_load_my_scripts() {
    $causalf_host = str_replace("http://", "", sanitize_url($_SERVER['SERVER_NAME']));

    if(strrpos($causalf_host, "."))
        $causalf_shop_name = trim(str_replace(".", "-", str_replace("www.", "", substr($causalf_host, 0, strrpos($causalf_host, ".")))), "/");
    else 
        $causalf_shop_name = $causalf_host;
    
    $causalf_random_value = mt_rand(); // generate a random value for the query parameter
    wp_enqueue_script(
        'causalf-script',
        'https://www.scripts.causalfunnel.com/assets/cfCKYv1_'.$causalf_shop_name.'_ProdV1.js?rand=' . $causalf_random_value, // add the random query parameter
        array(),
        '1.0.11',
        false
    );
}


add_filter( 'script_loader_tag', 'causalf_add_async_attribute', 10, 3 );
function causalf_add_async_attribute( $tag, $handle, $src ) {
    if ( 'causalf-script' === $handle ) {
        $tag = str_replace( ' src', ' data-minify="0" data-cfasync="false" nitro-exclude="" data-no-optimize="1" async="true" src', $tag );
    }

    return $tag;
}

add_filter( 'wp_resource_hints', 'causalf_remove_dns_prefetch', 10, 2 );
function causalf_remove_dns_prefetch( $urls, $relation ) {
    if ( $relation === 'dns-prefetch' ) {
        $urls = array_fill( 0, count( $urls ), '' );
    }

    return $urls;
}