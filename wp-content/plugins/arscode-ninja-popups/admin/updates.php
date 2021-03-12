<?php
if (get_transient('snp_update_response'))
{
    function snp_update_message() {
        echo "<div id=\"snp_upd\" style=\"padding: 5px 20px 20px 20px; background-color: #ef9999; margin: 40px; border: 1px solid #cc0000; \">";
        echo "<h2>Ninja Popups Update Warning!</h2>";
        echo "<p><b>".get_transient('snp_update_response')."</b></p>";
        echo '<a class="button" id="snp_upd_d" href="#">Dismiss this notice</a>';
        echo "</div>";
        echo "<script>jQuery(document).ready(function($){ $('#snp_upd_d').click(function(){ jQuery.ajax({type: 'POST',  url: 'admin-ajax.php', data: {  action: 'snp_dismiss_update_message'}}); $('#snp_upd').hide(); return false;});});</script>";
    }

    add_action('admin_notices', 'snp_update_message');
}

$snp_plugin_slug = basename('arscode-ninja-popups');

add_filter('pre_set_site_transient_update_plugins', 'snp_check_for_plugin_update');
add_filter('plugins_api', 'snp_plugin_api_call', 10, 3);

function snp_check_for_plugin_update($checked_data)
{
    global $snp_plugin_slug, $wp_version;

    if (!function_exists('snp_get_option') || snp_get_option('autoupdates')!=1)	{
	   return $checked_data;
    }
     
    if (empty($checked_data->checked)) {
    	return $checked_data;
    }

    $args = array(
        'slug'    => $snp_plugin_slug,
        'version' => isset($checked_data->checked[$snp_plugin_slug . '/' . $snp_plugin_slug . '.php'])
            ? $checked_data->checked[$snp_plugin_slug . '/' . $snp_plugin_slug . '.php']
            : SNP_VERSION
        ,
    );

    $request_string	= array(
        'body' => array(
            'action' => 'basic_check',
            'request' => serialize($args),
            'site' => get_bloginfo('url'),
            'purchasecode' => snp_get_option('purchasecode')
        ),
        'user-agent'	 => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
    );

    $raw_response = wp_remote_post(SNP_API_URL, $request_string);

    if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
	   $response = unserialize($raw_response['body']);
    }

    if (!isset($response->package)) {
        if (isset($response->response)) {
            set_transient('snp_update_response', $response->response, 1000);
        }

        return $checked_data;
    }
    
    if (is_object($response) && !empty($response)) {
    	$checked_data->response[$snp_plugin_slug . '/' . $snp_plugin_slug . '.php'] = $response;
    }

    return $checked_data;
}

function snp_plugin_api_call($def, $action, $args)
{
    global $snp_plugin_slug, $wp_version;

    if (!isset($args->slug) || ($args->slug != $snp_plugin_slug) || !function_exists('snp_get_option') || snp_get_option('autoupdates')!=1) {
        return false;
    }

    $plugin_info = get_site_transient('update_plugins');
    $current_version = $plugin_info->checked[$snp_plugin_slug . '/' . $snp_plugin_slug . '.php'];
    $args->version = $current_version;

    $request_string = array(
        'body' => array(
            'action' => $action,
            'request' => serialize($args),
            'site' =>  get_bloginfo('url'),
            'purchasecode' => snp_get_option('purchasecode')
        ),
        'user-agent'	 => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
    );

    $request = wp_remote_post(SNP_API_URL, $request_string);

    if (is_wp_error($request)) {
        $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
    } else {
        $res = unserialize($request['body']);

        if ($res === false) {
            $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
        }
    }

    return $res;
}