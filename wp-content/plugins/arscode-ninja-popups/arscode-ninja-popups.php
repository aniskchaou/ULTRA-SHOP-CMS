<?php
/*
  Plugin Name: Ninja Popups
  Plugin URI: http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=arscode
  Description: Awesome Popups for Your WordPress!
  Version: 4.7.2
  Author: ArsCode
  Author URI: http://www.arscode.pro/
 */
if (!defined('ABSPATH')) {
    die('-1');
}

define('SNP_VERSION', '4.7.2');
define('SNP_OPTIONS', 'snp');
define('SNP_DB_VER', '1.3');
define('SNP_LIBRARY_DIR', 'ninja-popups/');
define('SNP_DEMO_LIBRARY_URL', 'http://demo.arscode.pro/ninja-popups/wp-content/uploads/sites/2/ninja-popups/');
define('SNP_DEMO_LIBRARY_URL_2', '/ninja-popups/wp-content/uploads/sites/2/ninja-popups/');
define('SNP_DEMO_LIBRARY_URL_3', '/wp-content/uploads/sites/2/ninja-popups/');
define('SNP_URL', plugins_url('/', __FILE__));
define('SNP_DIR_PATH', plugin_dir_path(__FILE__));
define('SNP_PROMO_LINK', 'http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=');
DEFINE('SNP_API_URL', 'http://updates.arscode.pro/');

$snp_options = array();
$snp_popups = array();

if (is_admin()) {
    require_once(plugin_dir_path(__FILE__) . '/admin/options.php');
    require_once(plugin_dir_path(__FILE__) . '/admin/init.php');
    require_once(plugin_dir_path(__FILE__) . '/admin/updates.php');
    require_once(plugin_dir_path(__FILE__) . '/include/lists.inc.php');
}

require_once(plugin_dir_path(__FILE__) . '/include/Mobile_Detect.php');
require_once(plugin_dir_path(__FILE__) . '/include/country.inc.php');
require_once(plugin_dir_path(__FILE__) . '/include/fonts.inc.php');
require_once(plugin_dir_path(__FILE__) . '/include/functions.inc.php');
require_once(plugin_dir_path(__FILE__) . '/include/snp_links.inc.php');

/**
 * Autload our custom classes
 */
spl_autoload_register(function($className) {
    $directories = [
        plugin_dir_path(__FILE__) . '/src/'
    ];

    foreach ($directories as $directory) {
        $fileName = $directory . str_replace('\\', '/', $className) . '.php';

        if (file_exists($fileName)) {
            require_once $fileName;
        }
    }
});

/**
 * Detect Country, Zip Code and City Code for GeoIP pop-up's
 */
$countryCode = null;
$zipCode = null;
$cityCode = null;
if ((int)snp_get_option('geoip_popup') === 1) {
    $countryCode = getXwebCountryCode();
    $zipCode = getZipCode();
    $cityCode = getCityCode();
}

/**
 * Load filters that are used to subscribe client to mailing list manager
 */
foreach(glob(plugin_dir_path(__FILE__) . '/filters/*.php') as $file) {
    require_once $file;
}

$detect = new Snp_Mobile_Detect;

/**
 * Run common integration tasks
 */
$integration = new \Relio\Integration();
$integration->run();

/**
 * Setup front controller
 */


/**
 * @param $opt_name
 * @param null $default
 * @return |null
 */
function snp_get_option($opt_name, $default = null)
{
    global $snp_options;
    
    if (!$snp_options) {
        $snp_options = get_option(SNP_OPTIONS);
    }

    return (!empty($snp_options[$opt_name])) ? $snp_options[$opt_name] : $default;
}

global $snp_ignore_cookies;

$SNP_THEMES = array();
$SNP_THEMES_DIR_2 = apply_filters('snp_themes_dir_2', '');
$SNP_THEMES_DIR = apply_filters('snp_themes_dir', array(plugin_dir_path(__FILE__) . '/themes/', $SNP_THEMES_DIR_2));

function snp_popup_submit()
{
    global $wpdb;

    $result = array();
    $errors = array();

    $post_id = intval($_POST['popup_ID']);
    if ($post_id) {
        $POPUP_META = get_post_meta($post_id);
    }

    $cf_data = array();
    
    $POPUP_META['snp_theme'] = unserialize($POPUP_META['snp_theme'][0]);
    if (!isset($POPUP_META['snp_theme']['mode'])) {
        $POPUP_META['snp_theme']['mode'] = 0;
    }

    if ($POPUP_META['snp_theme']['mode'] == 0) {
        if (isset($_POST['email'])) {
            $_POST['email'] = snp_trim($_POST['email']);
        }

        if (isset($_POST['name'])) {
            $_POST['name'] = trim($_POST['name']);
        }

        if (!snp_is_valid_email($_POST['email'])) {
            $errors['email'] = __('This is not valid e-mail address', 'nhp-opts');
        }

        if (isset($_POST['email']) && !$_POST['email']) {
            $errors['email'] = __('This field is required', 'nhp-opts');
        }
    }

    require_once(plugin_dir_path(__FILE__) . '/include/recaptcha/src/autoload.php');

    if (snp_get_option('recaptcha_secret_key')) {
        $recaptcha = new \ReCaptcha\ReCaptcha(snp_get_option('recaptcha_secret_key'));
    }
	
    if (
        isset($POPUP_META['snp_bld_cf']) &&
        $POPUP_META['snp_theme']['mode'] == 1 &&
        $post_id
    ) {
        
        $POPUP_META['snp_bld_cf'] = unserialize($POPUP_META['snp_bld_cf'][0]);

        foreach ((array) $POPUP_META['snp_bld_cf'] as $f) {
            if ($f['type'] == 'captcha' && snp_get_option('recaptcha_secret_key')) {
                $resp = $recaptcha->verify($_POST['ninja_popup_recaptcha_response'], $_SERVER['REMOTE_ADDR']);
                if (!$resp->isSuccess()) {
                    if ($f['validation_message']) {
                        $errors['captcha'] = $f['validation_message'];
                    } else {
                        $errors['captcha'] = 1;
                    }
                }
            } else if ($f['type'] == 'file') {
                if (!empty($_FILES)) {
                    if (
                        isset($_FILES[$f['name']]) && 
                        ($uploadResult = snp_upload_file($_FILES[$f['name']]))
                    ) {
                        $cf_data[$f['name']] = $uploadResult;
                    }
                }

                if (
                    isset($f['required']) && 
                    $f['required'] == 1 && 
                    !$cf_data[$f['name']]
                ) {
                    if ($f['validation_message']) {
                        $errors[$f['name']] = $f['validation_message'];
                    } else {
                        $errors[$f['name']] = __('This field is required', 'nhp-opts');
                    }
                }
            } else {
                if ($f['name-type'] != '') {
                    $f['name'] = $f['name-type'];
                }

                if (isset($f['mailchimp_group']) && !empty($f['mailchimp_group'])) {
                    if (!empty($_POST['mcgroups'][$f['name']])) {
                        $cf_data['mcgroups'][$f['name']] = $_POST['mcgroups'][$f['name']];
                    }

                    if (
                        isset($f['required']) && 
                        $f['required'] == 1 && 
                        (
                            !isset($cf_data['mcgroups'][$f['name']]) || 
                            !$cf_data['mcgroups'][$f['name']]
                        )
                    ) {
                        if ($f['validation_message']) {
                            $errors[$f['name']] = $f['validation_message'];
                        } else {
                            $errors[$f['name']] = __('This field is required', 'nhp-opts');
                        }
                    }
                } else {
                    if (strpos($f['name'], '[')) {
                        $f['name'] = substr($f['name'], 0, strpos($f['name'], '['));
                    }

                    if (!empty($_POST[$f['name']])) {
                        $cf_data[$f['name']] = snp_trim($_POST[$f['name']]);
                    }

                    if (
                        isset($f['required']) && 
                        $f['required'] == 1 && 
                        !$cf_data[$f['name']]
                    ) {
                        if ($f['validation_message']) {
                            $errors[$f['name']] = $f['validation_message'];
                        } else {
                            $errors[$f['name']] = __('This field is required', 'nhp-opts');
                        }
                    }

                    if (
                        isset($f['required']) && 
                        $f['required'] == 1 && 
                        $f['name'] == 'email'
                    ) {
                        if (!snp_is_valid_email($_POST[$f['name']])) {
                            if ($f['validation_message']) {
                                $errors[$f['name']] = $f['validation_message'];
                            } else {
                                $errors[$f['name']] = __('This is not valid e-mail address', 'nhp-opts');
                            }
                        }
                    }
                }

                if (in_array($f['name'],array('email','name'))) {
                    unset($cf_data[$f['name']]);
                }
            }
        }
    }

    if (
        isset($POPUP_META['snp_cf']) && 
        $POPUP_META['snp_theme']['mode'] == 0 && 
        $post_id
    ) {
        $cf = unserialize($POPUP_META['snp_cf'][0]);

        if (isset($cf) && is_array($cf)) {
            foreach ($cf as $f) {
                if (mb_strtolower($f['type']) == 'file') {
                    if (!empty($_FILES)) {
                        if (
                            isset($_FILES[$f['name']]) && 
                            ($uploadResult = snp_upload_file($_FILES[$f['name']]))
                        ) {
                            $cf_data[$f['name']] = $uploadResult;
                        }
                    }

                    if (
                        isset($f['required']) && 
                        $f['required'] == 1 && 
                        !$cf_data[$f['name']]
                    ) {
                        $errors[$f['name']] = __('This field is required', 'nhp-opts');
                    }
                } else {
                    if (isset($f['name'])) {
                        if (strpos($f['name'], '[')) {
                            $f['name'] = substr($f['name'], 0, strpos($f['name'], '['));
                        }
						
                        if (!empty($_POST[$f['name']])) {
                            $cf_data[$f['name']] = snp_trim($_POST[$f['name']]);
                        }
                    }

                    if (isset($f['required']) && $f['required'] == 'Yes' && !$cf_data[$f['name']]) {
                        $errors[$f['name']] = __('This field is required', 'nhp-opts');
                    }
                }
            }
        }
    }

    if (isset($_POST['full_phone'])) {
        $cf_data['full_phone'] = $_POST['full_phone'];
    }

    if (isset($_POST['np_custom_name1']) && !empty($_POST['np_custom_name1'])) {
        $errors['np_custom_name1'] = __('Spam detected!', 'nhp-opts');
    }

    if (isset($_POST['np_custom_name2']) && $_POST['np_custom_name2'] !== '1') {
        $errors['np_custom_name2'] = __('Spam detected!', 'nhp-opts');
    }

    if (count($errors) > 0) {
        $result['Errors'] = $errors;
        $result['Ok'] = false;
    } else {
        $Done = 0;
        if (!empty($_POST['name'])) {
            $names = snp_detect_names($_POST['name']);
        } else {
            $names = array('first' => '', 'last' => '');
        }
        
        $api_error_msg = '';
        
        $ml_manager = snp_get_option('ml_manager');
        if(isset($POPUP_META['snp_ml_send_by_email'][0]) && $POPUP_META['snp_ml_send_by_email'][0] == 1) {
			$ml_manager = "email";	
        }

        $filterAndActionData = array(
            'popup_meta' => $POPUP_META,
            'data' => array(
                'post' => $_POST,
                'names' => $names,
                'cf' => $cf_data,
            )
        );

        do_action('ninja_popups_send_form', $filterAndActionData);

        $log_list_id = '';
        if ($ml_manager != 'email') {
            if (has_filter('ninja_popups_subscribe_by_' . $ml_manager)) {
            	$response = apply_filters('ninja_popups_subscribe_by_' . $ml_manager, $filterAndActionData);

            	if ($ml_manager != 'csv' && snp_get_option('ml_extra_csv') == '1') {
                    apply_filters('ninja_popups_subscribe_by_csv', array_merge($filterAndActionData, [
                        'filename' => 'subscribers.csv'
                    ]));
                }

            	$log_list_id = $response['log']['listId'];
            	if ($response['status'] === true) {
            		$Done = 1;
            		if (isset($response['drip'])) {
            			$result['drip'] = $response['drip'];
            		}

                    //Integration with Metrilo
                    $useMetrilo = isset($filterAndActionData['popup_meta']['snp_ml_use_metrilo'][0]) ? $filterAndActionData['popup_meta']['snp_ml_use_metrilo'][0] : false;
                    if (!$useMetrilo) {
                        $useMetrilo = snp_get_option('use_metrilo');
                    }

                    if ($useMetrilo) {
                        $metriloTagsSettings = isset($filterAndActionData['popup_meta']['snp_ml_metrilo_tags']) ? unserialize($filterAndActionData['popup_meta']['snp_ml_metrilo_tags']) : null;
                        if (!$metriloTagsSettings) {
                            $metriloTagsSettings = snp_get_option('ml_metrilo_tags');
                        }

                        $metriloTags = [];
                        if ($metriloTagsSettings) {
                            foreach ($metriloTagsSettings as $t) {
                                $metriloTags[] = $t;
                            }
                        }

                        $result['metrilo'] = [
                            'email' => snp_trim($filterAndActionData['data']['post']['email']),
                            'subscribed' => true,
                            'tags' => $metriloTags
                        ];

                        if (!empty($filterAndActionData['data']['post']['name'])) {
                            $result['metrilo'] = array_merge($result['metrilo'], [
                                'first_name' => $filterAndActionData['data']['names']['first'],
                                'last_name' => $filterAndActionData['data']['names']['last']
                            ]);
                        }

                        if (count($filterAndActionData['data']['cf']) > 0) {
                            $result['metrilo'] = array_merge($result['metrilo'], (array) $filterAndActionData['data']['cf']);
                        }

                        $result['metrilo'] = json_encode($result['metrilo']);

                    }
                    ///Integration with Metrilo

                    do_action('ninja_popups_send_form_success', $filterAndActionData);
            	} else {
            		$api_error_msg = $response['log']['errorMessage'];

                    do_action('ninja_popups_send_form_error', $filterAndActionData);
            	}
            } else {
            	$api_error_msg = 'Mailing List Manager with name ' . $ml_manager . ' is not defined';

                do_action('ninja_popups_send_form_error', $filterAndActionData);
        	}
        }
    	
        if ($ml_manager == 'email' || !$Done) {
            $Email = snp_get_option('ml_email');

            if (isset($POPUP_META['snp_ml_email']) && !empty($POPUP_META['snp_ml_email'])) {
                $Email = $POPUP_META['snp_ml_email'];
            }

            if (!$Email) {
                $Email = get_bloginfo('admin_email');
            }

            if (is_array($Email)) {
                $recipients = $Email;
            } else {
                $recipients = explode(',', $Email);
            }

            if (!isset($_POST['name'])) {
                $_POST['name'] = '--';
            }

            $error_mgs = '';
            if ($api_error_msg != '') {
                $error_mgs.="IMPORTANT! You have received this message because connection to your e-mail marketing software failed. Please check connection setting in the plugin configuration.\n";
                $error_mgs.=$api_error_msg . "\n";
            }

            $cf_msg = '';
            if (count($cf_data) > 0) {
                foreach ($cf_data as $k => $v) {
                    $cf_msg .= $k . ": " . $v . "\r\n";
                }
            }

            $msg = "New subscription on " . get_bloginfo() . "\r\n" .
                $error_mgs .
                "\r\n" .
                "E-mail: " . snp_trim($_POST['email']) . "\r\n" .
                "\r\n" .
                "Name: " . $_POST['name'] . "\r\n" .
                "\r\n" .
                $cf_msg .
                "\r\n" .
                "Form: " . get_the_title($_POST['popup_ID']) . " (" . $_POST['popup_ID'] . ")\r\n" .
                "\r\n" .
                "\r\n" .
                "Referer: " . $_SERVER['HTTP_REFERER'] . "\r\n" .
                "\r\n" .
                "Date: " . date('Y-m-d H:i') . "\r\n" .
                "\r\n" .
                "IP: " . $_SERVER['REMOTE_ADDR'] . "";
            
            if (isset($_POST['name']) && isset($_POST['email'])) {
            	$headers[]   = 'Reply-To: ' . $_POST['name'] . ' <'.$_POST['email'].'>';
            }

            $subject = "New subscription on " . get_bloginfo();
            if (snp_get_option('email_notify_subject')) {
                $subject = snp_get_option('email_notify_subject');
            }

            foreach ($recipients as $emailRecipient) {
                wp_mail($emailRecipient, $subject, $msg, $headers);
            }
        }

        if ((snp_get_option('enable_log_gathering') == 'yes') && (snp_get_option('enable_log_g_subscribe') == 'yes')) {
            snp_update_log_subscription($cf_data, $log_list_id, $api_error_msg);
        }
        
        $result['api_error_msg'] = $api_error_msg;
        $result['Ok'] = true;
    }

    echo json_encode($result);
    die('');
}

function snp_popup_stats()
{
    global $wpdb;

    $table_name = $wpdb->prefix . "snp_stats";
    $ab_id = intval($_POST['ab_ID']);
    $post_id = intval($_POST['popup_ID']);

    if (current_user_can('manage_options')) {
    //    die('');
    }
    
    if ($post_id > 0) {
        if ($_POST['type'] == 'view') {
            $count = get_post_meta($post_id, 'snp_views');
            if (!$count || !$count[0])
                $count[0] = 0;
            update_post_meta($post_id, 'snp_views', $count[0] + 1);
            if ($ab_id)
            {
                $count = get_post_meta($ab_id, 'snp_views');
                if (!$count || !$count[0])
                    $count[0] = 0;
                update_post_meta($ab_id, 'snp_views', $count[0] + 1);
            }
            if((snp_get_option('enable_log_gathering') == 'yes') && (snp_get_option('enable_log_g_view') == 'yes'))
            {
                snp_update_log_popup($post_id);
            }
            $wpdb->query("insert into $table_name (`date`,`ID`,`AB_ID`,imps) values (CURDATE(),$post_id,$ab_id,1) on duplicate key update imps = imps + 1;");
            echo 'ok: view';
        } else {
            $count = get_post_meta($post_id, 'snp_conversions');
            if (!$count || !$count[0])
                $count[0] = 0;
            update_post_meta($post_id, 'snp_conversions', $count[0] + 1);
            if ($ab_id)
            {
                $count = get_post_meta($ab_id, 'snp_conversions');
                if (!$count || !$count[0])
                    $count[0] = 0;
                update_post_meta($ab_id, 'snp_conversions', $count[0] + 1);
            }
            $wpdb->query("insert into $table_name (`date`,`ID`,`AB_ID`,convs) values (CURDATE(),$post_id,$ab_id,1) on duplicate key update convs = convs + 1;");
            echo 'ok: conversion';
        }
    }

    die('');
}

function snp_get_theme($theme)
{
    global $SNP_THEMES, $SNP_THEMES_DIR;

    if (!$theme) {
        return false;
    }
    $theme = basename($theme);
    foreach ($SNP_THEMES_DIR as $DIR) {
        if (is_dir($DIR . '/' . $theme . '') && is_file($DIR . '/' . $theme . '/theme.php')) {
            require_once( $DIR . '/' . $theme . '/theme.php' );
            $SNP_THEMES[$theme]['DIR'] = $DIR . '/' . $theme . '/';
            return $SNP_THEMES[$theme];
        }
    }

    return false;
}

function snp_get_themes_list()
{
    global $SNP_THEMES, $SNP_THEMES_DIR;

    if (count($SNP_THEMES) == 0) {
        $files = array();
        foreach ($SNP_THEMES_DIR as $DIR) {
            if (is_dir($DIR)) {
                if ($dh = opendir($DIR)) {
                    while (($file = readdir($dh)) !== false) {
                        if (is_dir($DIR . '/' . $file) && $file != '.' && $file != '..') {
                            $files[] = $file;
                        }
                    }
                    closedir($dh);
                }
            }
        }

        sort($files);

        foreach ($files as $file) {
            snp_get_theme($file);
        }
    }

    return $SNP_THEMES;
}

function snp_popup_fields_list($popup)
{
    global $SNP_THEMES;
    
    $popup = trim($popup);

    if (is_array($SNP_THEMES) && is_array($SNP_THEMES[$popup])) {
        return $SNP_THEMES[$popup]['FIELDS'];
    } else {
        return array();
    }
}

function snp_popup_fields()
{
    global $SNP_THEMES, $SNP_NHP_Options, $post;

    if (!$post) {
        $post = (object) array();
    }

    $post->ID = intval($_POST['snp_post_ID']);
    snp_get_themes_list();
    
    if ($SNP_THEMES[$_POST['popup']]) {
        $SNP_NHP_Options->_custom_fields_html('snp_popup_fields', $_POST['popup']);
    } else {
        echo 'Error...';
    }

    die();
}

function snp_ml_list()
{
    require_once( plugin_dir_path(__FILE__) . '/include/lists.inc.php' );

    if ($_POST['ml_manager'] == 'mailchimp') {
        echo json_encode(snp_ml_get_mc_lists($_POST['ml_mc_apikey']));
    } elseif ($_POST['ml_manager'] == 'sendgrid') {
        echo json_encode(snp_ml_get_sendgrid_lists($_POST['ml_sendgrid_username'], $_POST['ml_sendgrid_password']));
    } elseif ($_POST['ml_manager'] == 'sendinblue') {
        echo json_encode(snp_ml_get_sendinblue_lists($_POST['ml_sendinblue_apikey']));
    } elseif ($_POST['ml_manager'] == 'getresponse') {
        echo json_encode(snp_ml_get_gr_lists($_POST['ml_gr_apikey']));
    } elseif ($_POST['ml_manager'] == 'freshmail') {
        echo json_encode(snp_ml_get_freshmail_lists($_POST['ml_freshmail_apikey'], $_POST['ml_freshmail_apisecret']));
    } elseif ($_POST['ml_manager'] == 'sendlane') {
        echo json_encode(snp_ml_get_sendlane_lists($_POST['ml_sendlane_apikey'], $_POST['ml_sendlane_hash'], $_POST['ml_sendlane_subdomain']));
    } elseif ($_POST['ml_manager'] == 'mailrelay') {
        echo json_encode(snp_ml_get_mailrelay_lists($_POST['ml_mailrelay_apikey'], $_POST['ml_mailrelay_address']));
    } elseif ($_POST['ml_manager'] == 'mailup') {
        echo json_encode(snp_ml_get_mailup_lists($_POST['ml_mailup_clientid'], $_POST['ml_mailup_clientsecret'], $_POST['ml_mailup_login'], $_POST['ml_mailup_password']));
    } elseif ($_POST['ml_manager'] == 'ontraport') {
        echo json_encode(snp_ml_get_ontraport_lists($_POST['ml_ontraport_apiid'], $_POST['ml_ontraport_apikey']));
    } elseif ($_POST['ml_manager'] == 'sendreach') {
        echo json_encode(snp_ml_get_sendreach_lists($_POST['ml_sendreach_pubkey'], $_POST['ml_sendreach_privkey']));
    } elseif ($_POST['ml_manager'] == 'sendpulse') {
        echo json_encode(snp_ml_get_sendpulse_lists($_POST['ml_sendpulse_id'], $_POST['ml_sendpulse_apisecret']));
    } elseif ($_POST['ml_manager'] == 'mailjet') {
        echo json_encode(snp_ml_get_mailjet_lists($_POST['ml_mailjet_apikey'], $_POST['ml_mailjet_apisecret']));
    } elseif ($_POST['ml_manager'] == 'elasticemail') {
        echo json_encode(snp_ml_get_elasticemail_lists($_POST['ml_elasticemail_apikey']));
    } elseif ($_POST['ml_manager'] == 'benchmarkemail') {
        echo json_encode(snp_ml_get_benchmarkemail_lists($_POST['ml_benchmarkemail_apikey']));
    } elseif ($_POST['ml_manager'] == 'myemma') {
        echo json_encode(snp_ml_get_myemma_lists($_POST['ml_myemma_account_id'], $_POST['ml_myemma_pubkey'], $_POST['ml_myemma_privkey']));
    } elseif ($_POST['ml_manager'] == 'mailerlite') {
        echo json_encode(snp_ml_get_mailerlite_lists($_POST['ml_mailerlite_apikey']));
    } elseif ($_POST['ml_manager'] == 'rocketresponder') {
		echo json_encode(snp_ml_get_rocketresponder_lists($_POST['ml_rocketresponder_apipublic'], $_POST['ml_rocketresponder_apiprivate']));
	} elseif ($_POST['ml_manager'] == 'activecampaign') {
        echo json_encode(snp_ml_get_activecampaign_lists($_POST['ml_activecampaign_apiurl'], $_POST['ml_activecampaign_apikey']));
    } elseif ($_POST['ml_manager'] == 'campaignmonitor') {
        echo json_encode(snp_ml_get_cm_lists($_POST['ml_cm_clientid'], $_POST['ml_cm_apikey']));
    } elseif ($_POST['ml_manager'] == 'icontact') {
        echo json_encode(snp_ml_get_ic_lists($_POST['ml_ic_username'], $_POST['ml_ic_addid'], $_POST['ml_ic_apppass']));
    } elseif ($_POST['ml_manager'] == 'constantcontact') {
        echo json_encode(snp_ml_get_cc_lists($_POST['ml_cc_username'], $_POST['ml_cc_pass']));
    } elseif ($_POST['ml_manager'] == 'aweber_auth') {
        echo json_encode(snp_ml_get_aw_auth($_POST['ml_aw_auth_code']));
    } elseif ($_POST['ml_manager'] == 'aweber_remove_auth') {
        echo json_encode(snp_ml_get_aw_remove_auth());
    } elseif ($_POST['ml_manager'] == 'aweber') {
        echo json_encode(snp_ml_get_aw_lists());
    } elseif ($_POST['ml_manager'] == 'wysija') {
        echo json_encode(snp_ml_get_wy_lists());
    } elseif ($_POST['ml_manager'] == 'madmimi') {
        echo json_encode(snp_ml_get_madm_lists($_POST['ml_madm_username'], $_POST['ml_madm_apikey']));
    } elseif ($_POST['ml_manager'] == 'infusionsoft') {
        echo json_encode(snp_ml_get_infusionsoft_lists($_POST['ml_inf_subdomain'], $_POST['ml_inf_apikey']));
    } elseif ($_POST['ml_manager'] == 'mymail') {
        echo json_encode(snp_ml_get_mm_lists());
    } elseif ($_POST['ml_manager'] == 'mailster') {
        echo json_encode(snp_ml_get_mailster_lists());
    } elseif ($_POST['ml_manager'] == 'sendpress') {
        echo json_encode(snp_ml_get_sp_lists());
    } elseif ($_POST['ml_manager'] == 'egoi') {
        echo json_encode(snp_ml_get_egoi_lists($_POST['ml_egoi_apikey']));
    } elseif ($_POST['ml_manager'] == 'hubspot') {
        echo json_encode(snp_ml_get_hubspot_lists($_POST['ml_hubspot_apikey']));
    } elseif ($_POST['ml_manager'] == 'convertkit') {
        echo json_encode(snp_ml_get_convertkit_lists($_POST['ml_convertkit_apikey']));
    } elseif ($_POST['ml_manager'] == 'enewsletter') {
        echo json_encode(snp_ml_get_enewsletter_lists());
    } elseif ($_POST['ml_manager'] == 'campaigner') {
        echo json_encode(snp_ml_get_campaigner_lists($_POST['ml_campaigner_username'], $_POST['ml_campaigner_password']));
    } elseif ($_POST['ml_manager'] == 'sgautorepondeur') {
        echo json_encode(snp_ml_get_sgautorepondeur_lists($_POST['ml_sgautorepondeur_id'], $_POST['ml_sgautorepondeur_code']));
    } else if ($_POST['ml_manager'] == 'kirim') {
        echo json_decode(snp_ml_get_kirim_lists($_POST['ml_kirim_username'], $_POST['ml_kirim_token']));
    } else if ($_POST['ml_manager'] == 'mautic_auth') {
        echo json_encode(snp_ml_get_mautic_auth($_POST['ml_mautic_url'], $_POST['ml_mautic_key'], $_POST['ml_mautic_secret']));
    } else if ($_POST['ml_manager'] == 'mautic_remove_auth') {
        echo json_encode(snp_ml_get_mautic_remove_auth());
    } else if ($_POST['ml_manager'] == 'zoho_auth') {
        echo json_encode(snp_ml_get_zoho_auth($_POST['ml_zoho_email'], $_POST['ml_zoho_password'], $_POST['ml_zoho_application']));
    } else if ($_POST['ml_manager'] == 'zoho_remove_auth') {
        echo json_encode(snp_ml_get_zoho_remove_auth());
    } else if ($_POST['ml_manager'] == 'zoho_campaign') {
        echo json_encode(snp_ml_get_zoho_campaigns());
    } else if ($_POST['ml_manager'] == 'mailpoet') {
        echo json_encode(snp_ml_get_mailpoet_lists());
    } else if ($_POST['ml_manager'] == 'drip') {
        echo json_encode(snp_ml_get_drip_campaigns($_POST['ml_drip_account'], $_POST['ml_drip_token']));
    } else if ($_POST['ml_manager'] == 'apsis') {
        echo json_encode(snp_ml_get_apsis_lists($_POST['ml_apsis_key']));
    } else if ($_POST['ml_manager'] == 'klayvio') {
        echo json_encode(snp_ml_get_klayvio_lists($_POST['ml_klayvio_api_key']));
    } else if ($_POST['ml_manager'] == 'moosend') {
        echo json_encode(snp_ml_get_moosend_lists($_POST['ml_moosend_api_key']));
    } else if ($_POST['ml_manager'] == 'mailfit') {
        echo json_encode(snp_ml_get_mailfit_lists($_POST['ml_mailfit_endpoint'], $_POST['ml_mailfit_apitoken']));
    } else if ($_POST['ml_managger'] == 'ngpvan') {
        echo json_encode(snp_ml_get_ngpvan_contacts($_POST['ml_ngpvan_username'], $_POST['ml_ngpvan_password']));
    } else {
        echo json_encode(array());
    }

    die();
}

function snp_popup_colors()
{
    global $SNP_THEMES, $SNP_NHP_Options, $post;

    snp_get_themes_list();
    echo json_encode($SNP_THEMES[$_POST['popup']]['COLORS']);
    die();
}

function snp_popup_types()
{
    global $SNP_THEMES, $SNP_NHP_Options, $post;

    snp_get_themes_list();
    echo json_encode($SNP_THEMES[$_POST['popup']]['TYPES']);
    die();
}

function snp_init()
{
    $wp_scripts = wp_scripts();

    if (!snp_get_option('js_disable_jqueryuitheme')) {
        wp_enqueue_style('plugin_name-admin-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css',
            false,
            SNP_VERSION,
            false);
    }

    if (!snp_get_option('js_disble_jquery_ui_calendar')) {
        wp_enqueue_script('jquery-ui-datepicker');
    }

    wp_enqueue_script(
        'js-cookie', plugins_url('/assets/js/cookie.js', __FILE__), array('jquery'), false, true
    );

    if (
        !snp_get_option('js_disable_tooltipster') || 
        is_admin()
    ) {
        wp_register_style('tooltipster-css', plugins_url('/tooltipster/tooltipster.bundle.min.css', __FILE__));
        wp_enqueue_style('tooltipster-css');
        
        wp_register_style('tooltipster-css-theme', plugins_url('/tooltipster/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css', __FILE__));
        wp_enqueue_style('tooltipster-css-theme');
        
        wp_enqueue_script(
            'jquery-np-tooltipster', plugins_url('/assets/js/tooltipster.bundle.min.js', __FILE__), array('jquery'), false, true
        );
    }

    if (!snp_get_option('js_disable_material')) {
        wp_register_style('material-design-css', plugins_url('/themes/jquery.material.form.css', __FILE__));
        wp_enqueue_style('material-design-css');

        wp_enqueue_script(
            'material-design-js', plugins_url('/assets/js/jquery.material.form.min.js', __FILE__), array('jquery'), false, true
        );
    }

    if (snp_get_option('enable_jquery_accordion')) {
        wp_enqueue_script('jquery-ui-accordion');
    }

    if (!snp_get_option('js_disable_phone_input')) {
        wp_register_style('jquery-intl-phone-input-css', plugins_url('/assets/vendor/intl-tel-input/css/intlTelInput.min.css', __FILE__));
        wp_enqueue_style('jquery-intl-phone-input-css');

        wp_enqueue_script(
            'jquery-intl-phone-input-js', plugins_url('/assets/vendor/intl-tel-input/js/intlTelInput-jquery.min.js', __FILE__), array('jquery'), false, true
        );
    }

    wp_enqueue_script(
        'js-dialog_trigger', plugins_url('/assets/js/dialog_trigger.js', __FILE__), array('jquery'), false, true
    );
    
    if (snp_get_option('js_use_nonminify')) {
        wp_enqueue_script(
            'js-ninjapopups', plugins_url('/assets/js/ninjapopups.js', __FILE__), array('jquery'), false, true
        );
    } else {
        wp_enqueue_script(
            'js-ninjapopups', plugins_url('/assets/js/ninjapopups.min.js', __FILE__), array('jquery'), false, true
        );
    }

    //Ninja Popup JavaScript triggers fired after ajax response is received
    //Mostly used for tracking tools like drip, learnq, metrilo and others
    if (snp_get_option('js_use_drip')) {
        wp_enqueue_script(
            'js-ninjapopups-drip', plugins_url('/assets/js/ninjapopups.drip.min.js', __FILE__), array('jquery'), false, true
        );
    }

    if (snp_get_option('js_use_learnq')) {
        wp_enqueue_script(
            'js-ninjapopups-learnq', plugins_url('/assets/js/ninjapopups.learnq.min.js', __FILE__), array('jquery'), false, true
        );
    }

    if (snp_get_option('js_use_metrilo')) {
        wp_enqueue_script(
            'js-ninjapopups-metrilo', plugins_url('/assets/js/ninjapopups.metrilo.min.js', __FILE__), array('jquery'), false, true
        );
    }
}

function snp_init_fancybox()
{
    if (
        !snp_get_option('js_disable_fancybox') ||
        is_admin()
    ) {
        // Fancybox 2
        wp_register_style('fancybox2', plugins_url('/fancybox2/jquery.fancybox.min.css', __FILE__));
        wp_enqueue_style('fancybox2');
        wp_enqueue_script(
            'fancybox2', plugins_url('/fancybox2/jquery.fancybox.min.js', __FILE__), array('jquery'), false, true
        );
    }
}

function snp_init_mbYTPlayer()
{
    if (
        !snp_get_option('js_disable_mbYTPlayer') || 
        is_admin()
    ) {
        wp_enqueue_script('mbYTPlayer', SNP_URL . 'assets/js/jquery.mb.YTPlayer.min.js', array('jquery'), false, true);
    }
}

function snp_init_fontawesome()
{
    if (
        !snp_get_option('js_disable_fontawesome') || 
        is_admin()
    ) {
        wp_register_style('font-awesome', plugins_url('/assets/font-awesome/css/font-awesome.min.css', __FILE__));
        wp_enqueue_style('font-awesome');
    }
}

function snp_run_popup($ID, $type, $is_preview = false)
{
    global $snp_popups, $PREVIEW_POPUP_META, $detect, $countryCode, $cityCode, $zipCode;
    
    if (!$ID && $ID != -1) {
        return;
    }
    
    snp_init();
    
    if ($ID == -1) {
        $POPUP_META = $PREVIEW_POPUP_META;
        foreach ($POPUP_META as $k => $v) {
            if (is_array($v) || $k == 'theme') {
                $v = serialize($v);
            } else {
                $v = stripslashes($v);
            }

            $POPUP_META[$k] = $v;
            $PREVIEW_POPUP_META[$k] = $v;
        }
    } else {
        if (strpos($ID, 'ab_') === 0) {
            $AB_ID = str_replace('ab_', '', $ID);
            $AB_META = get_post_meta($AB_ID);
            
            if (!isset($AB_META['snp_forms'])) {
                return;
            }

            $AB_META['snp_forms'] = array_keys(unserialize($AB_META['snp_forms'][0]));
            
            if (!is_array($AB_META['snp_forms']) || count($AB_META['snp_forms']) == 0) {
                return;
            }

            $ID = $AB_META['snp_forms'][array_rand($AB_META['snp_forms'])];
        }

        if (get_post_status($ID) != 'publish') {
            return;
        }

        $POPUP_META = get_post_meta($ID);
        foreach ((array) $POPUP_META as $k => $v) {
            $POPUP_META[$k] = $v[0];
        }
    }

    if ($is_preview==true) {
        $PREVIEW_POPUP_META['is_preview'] = true;   
    }

    if (!isset($POPUP_META['snp_theme'])) {
        $POPUP_META['snp_theme'] = '';
    }

    if (isset($POPUP_META['snp_theme']) && !is_array($POPUP_META['snp_theme'])) {
        $POPUP_META['snp_theme'] = unserialize($POPUP_META['snp_theme']);
    }

    $POPUP_START_DATE = isset($POPUP_META['snp_start_date']) ? $POPUP_META['snp_start_date'] : '';
    $POPUP_END_DATE = isset($POPUP_META['snp_end_date']) ? $POPUP_META['snp_end_date'] : '';

    if (isset($POPUP_META['snp_start_hour']) && $POPUP_META['snp_start_hour']) {
    	$POPUP_START_HOUR = $POPUP_META['snp_start_hour'];
    } else {
    	$POPUP_START_HOUR = '';
    }
    
    if (isset($POPUP_META['snp_end_hour']) && $POPUP_META['snp_end_hour']) {
    	$POPUP_END_HOUR = $POPUP_META['snp_end_hour'];
    } else {
    	$POPUP_END_HOUR = '';
    }

    if ($detect->isMobile() && !(snp_get_option('enable_mobile') == 'enabled')) {
        return;
    }

    $POPUP_START_TIME = $POPUP_START_DATE;
    if ($POPUP_START_HOUR) {
        $POPUP_START_TIME .= ' ' . $POPUP_START_HOUR;
    }

    $POPUP_END_TIME = $POPUP_END_DATE;
    if ($POPUP_END_HOUR) {
        $POPUP_END_TIME .= ' ' . $POPUP_END_HOUR;
    }

    if ($POPUP_START_TIME && !(time() >= strtotime($POPUP_START_TIME))) {
        return;
    }

    if ($POPUP_END_TIME && !(strtotime($POPUP_END_TIME) > time())) {
        return;
    }

    //Option to show pop-up based on referer
    if (isset($POPUP_META['snp_show_by_referer']) && $POPUP_META['snp_show_by_referer'] == 'yes') {
        $found = false;
        if (($refs = unserialize($POPUP_META['snp_show_by_referer_urls'])) && isset($_SERVER['HTTP_REFERER'])) {
            foreach ($refs as $it) {
                if (preg_match($it, $_SERVER['HTTP_REFERER'], $matches)) {
                    $found = true;
                }
            }
        }

        if (!$found) {
            return;
        }
    }

    //Option to hide pop-up based on referer
    if (isset($POPUP_META['snp_hide_by_referer']) && $POPUP_META['snp_hide_by_referer'] == 'yes') {
        $found = false;
        if (($refs = unserialize($POPUP_META['snp_hide_by_referer_urls'])) && isset($_SERVER['HTTP_REFERER'])) {
            foreach ($refs as $it) {
                if (preg_match($it, $_SERVER['HTTP_REFERER'], $matches)) {
                    $found = true;
                }
            }
        }

        if ($found) {
            return;
        }
    }

    if ($countryCode) {
        if (isset($POPUP_META['snp_show_by_country']) && $POPUP_META['snp_show_by_country'] == 'yes') {
            $found = false;
            if (($countries = unserialize($POPUP_META['snp_show_by_country_countries']))) {
                $countries = array_map('strtolower', $countries);
                if (in_array(strtolower($countryCode), $countries)) {
                    $found = true;
                }
            }

            if (!$found) {
                return;
            }
        }

        if (isset($POPUP_META['snp_hide_by_country']) && $POPUP_META['snp_hide_by_country'] == 'yes') {
            $found = false;
            if (($countries = unserialize($POPUP_META['snp_hide_by_country_countries']))) {
                $countries = array_map('strtolower', $countries);
                if (in_array(strtolower($countryCode), $countries)) {
                    $found = true;
                }
            }

            if ($found) {
                return;
            }
        }
    }

    if ($zipCode) {
        if (isset($POPUP_META['snp_show_by_zip']) && $POPUP_META['snp_show_by_zip'] == 'yes') {
            $found = false;
            if (($codes = unserialize($POPUP_META['snp_show_by_zip_codes']))) {
                foreach ($codes as $it) {
                    if ($it === $zipCode) {
                        $found = true;
                    }
                }
            }

            if ($found) {
                return;
            }
        }

        if (isset($POPUP_META['snp_hide_by_zip']) && $POPUP_META['snp_hide_by_zip'] == 'yes') {
            $found = false;
            if (($codes = unserialize($POPUP_META['snp_hide_by_zip_codes']))) {
                foreach ($codes as $it) {
                    if ($it === $zipCode) {
                        $found = true;
                    }
                }
            }

            if ($found) {
                return;
            }
        }
    }

    if ($cityCode) {
        if (isset($POPUP_META['snp_show_by_city']) && $POPUP_META['snp_show_by_city'] == 'yes') {
            $found = false;
            if (($codes = unserialize($POPUP_META['snp_show_by_city_codes']))) {
                foreach ($codes as $it) {
                    if ($it === $cityCode) {
                        $found = true;
                    }
                }
            }

            if ($found) {
                return;
            }
        }

        if (isset($POPUP_META['snp_hide_by_city']) && $POPUP_META['snp_hide_by_city'] == 'yes') {
            $found = false;
            if (($codes = unserialize($POPUP_META['snp_hide_by_city_codes']))) {
                foreach ($codes as $it) {
                    if ($it === $cityCode) {
                        $found = true;
                    }
                }
            }

            if ($found) {
                return;
            }
        }
    }
	
    if ($type == 'exit') {
        $use_in = snp_get_option('use_in');
        if (isset($use_in['the_content']) && $use_in['the_content'] == 1) {
            add_filter('the_content', array('snp_links', 'search'), 100);
        }

        if (isset($use_in['the_excerpt']) && $use_in['the_excerpt'] == 1) {
            add_filter('the_excerpt', array('snp_links', 'search'), 100);
        }

        if (isset($use_in['widget_text']) && $use_in['widget_text'] == 1) {
            add_filter('widget_text', array('snp_links', 'search'), 100);
        }

        if (isset($use_in['comment_text']) && $use_in['comment_text'] == 1) {
            add_filter('comment_text', array('snp_links', 'search'), 100);
        }
    }

    if (snp_get_option('run_hook_footer') == 'snp_run_footer') {
        add_action('snp_run_footer', 'snp_footer');
    } else  {
        add_action('wp_footer', 'snp_footer');
    }
    wp_register_style('snp_styles_reset', plugins_url('/themes/reset.min.css', __FILE__));
    wp_enqueue_style('snp_styles_reset');

    if (isset($POPUP_META['snp_theme']['mode']) && $POPUP_META['snp_theme']['mode'] == '1') {
        $POPUP_META['snp_theme']['theme'] = 'builder';
    }

    if ($POPUP_META['snp_theme']['theme'] != 'builder') {
        snp_init_fancybox();
    }

    if (isset($POPUP_META['snp_theme']['theme']) && $POPUP_META['snp_theme']['theme']) {
        $THEME_INFO = snp_get_theme($POPUP_META['snp_theme']['theme']);
    }

    if (isset($THEME_INFO['STYLES']) && $THEME_INFO['STYLES']) {
        wp_register_style('snp_styles_' . $POPUP_META['snp_theme']['theme'], plugins_url($POPUP_META['snp_theme']['theme'] . '/' . $THEME_INFO['STYLES'], realpath($THEME_INFO['DIR'])));
        wp_enqueue_style('snp_styles_' . $POPUP_META['snp_theme']['theme']);
    }

    if (isset($POPUP_META['snp_theme']['theme']) && function_exists('snp_enqueue_' . $POPUP_META['snp_theme']['theme'])) {
        call_user_func('snp_enqueue_' . $POPUP_META['snp_theme']['theme'], $POPUP_META);
    }

    if ($type == 'inline') {
        
    } elseif ($type == 'content') {
        $snp_popups[$type][] = array(
            'ID' => $ID,
            'AB_ID' => isset($AB_ID) ? $AB_ID : false
        );
    } else {
        $snp_popups[$type] = array(
            'ID' => $ID,
            'AB_ID' => isset($AB_ID) ? $AB_ID : false
        );
    }
}

function snp_create_popup($ID, $AB_ID, $type, $args = array())
{
    global $PREVIEW_POPUP_META;
    
    $return = '';
    if ($ID == -1) {
        $POPUP_META = $PREVIEW_POPUP_META;
    } else {
        $POPUP = get_post($ID);
        $POPUP_META = get_post_meta($ID);
        foreach ($POPUP_META as $k => $v) {
            $POPUP_META[$k] = $v[0];
        }
    }

    if (isset($PREVIEW_POPUP_META['is_preview'])) {
        $POPUP_META['snp_open']='load';
        $POPUP_META['snp_open_after']='';
    }

    if (!is_array($POPUP_META['snp_theme'])) {
        $POPUP_META['snp_theme'] = unserialize($POPUP_META['snp_theme']);
    }

    if (!isset($POPUP_META['snp_theme']['mode']) || $POPUP_META['snp_theme']['mode'] == '0') {
        if (!$POPUP_META['snp_theme']['theme']) {
            return;
        }

        if ($POPUP_META['snp_theme']['type'] == 'social' || $POPUP_META['snp_theme']['type'] == 'likebox') {
            snp_enqueue_social_script();
        }
    } else {
        $POPUP_META['snp_theme']['theme'] = 'builder';
    }

    $dataJsonConfig = [];

    $dataJsonConfig['popupType'] = $type;
    $dataJsonConfig['popupId'] = $ID;
    $dataJsonConfig['abId'] = false;
    if ($AB_ID != false) {
        $dataJsonConfig['abId'] = $AB_ID;
    }
    $dataJsonConfig['popupDivId'] = 'snppopup-' . $type . ($type == 'content' || $type == 'inline' || $type == 'widget' ? '-' . $ID : '');

    $dataJsonConfig['lock_id'] = (isset($args['lock_id']) && $args['lock_id'] ? $args['lock_id'] : '');
    $dataJsonConfig['openMethod'] = (isset($POPUP_META['snp_open']) ? $POPUP_META['snp_open'] : 'load');
    $dataJsonConfig['closeMethod'] = (isset($POPUP_META['snp_close']) ? $POPUP_META['snp_close'] : 'close_manual');
    $dataJsonConfig['snp_optin_form_submit'] = 'single';
    $dataJsonConfig['showReadyThemeCloseButton'] = $POPUP_META['snp_show_cb_button'];
    $dataJsonConfig['popupTheme'] = $POPUP_META['snp_theme']['theme'];
    $dataJsonConfig['overlayType'] = $POPUP_META['snp_popup_overlay'];
    $dataJsonConfig['snp_cookie_conversion'] = (is_numeric($POPUP_META['snp_cookie_conversion']) ? $POPUP_META['snp_cookie_conversion'] : '30');
    $dataJsonConfig['snp_cookie_close'] = (is_numeric($POPUP_META['snp_cookie_close']) && $POPUP_META['snp_cookie_close'] ? $POPUP_META['snp_cookie_close'] : '-1');
    $dataJsonConfig['snp_autoclose'] = (isset($POPUP_META['snp_autoclose']) && $POPUP_META['snp_autoclose'] ? $POPUP_META['snp_autoclose'] : '');
    $dataJsonConfig['snp_show_on_exit'] = (isset($POPUP_META['snp_show_on_exit']) ? $POPUP_META['snp_show_on_exit'] : '1');
    $dataJsonConfig['snp_exit_js_alert_text'] = (isset($POPUP_META['snp_exit_js_alert_text'])?str_replace("\r\n", '\n', htmlspecialchars((string) $POPUP_META['snp_exit_js_alert_text'])):'');
    $dataJsonConfig['snp_exit_scroll_down'] = (isset($POPUP_META['snp_exit_scroll_down']) ? $POPUP_META['snp_exit_scroll_down'] : '');
    $dataJsonConfig['snp_exit_scroll_up'] = (isset($POPUP_META['snp_exit_scroll_up']) ? $POPUP_META['snp_exit_scroll_up'] : '');
    $dataJsonConfig['snp_open_after'] = (isset($POPUP_META['snp_open_after']) && $POPUP_META['snp_open_after'] ? $POPUP_META['snp_open_after'] : '');
    $dataJsonConfig['snp_open_inactivity'] = (isset($POPUP_META['snp_open_inactivity']) && $POPUP_META['snp_open_inactivity'] ? $POPUP_META['snp_open_inactivity'] : '');
    $dataJsonConfig['snp_open_scroll'] = (isset($POPUP_META['snp_open_scroll']) && $POPUP_META['snp_open_scroll'] ? $POPUP_META['snp_open_scroll'] : '');
    $dataJsonConfig['snp_open_spend_time'] = (isset($POPUP_META['snp_open_spend_time']) && $POPUP_META['snp_open_spend_time'] ? $POPUP_META['snp_open_spend_time'] : '');
    $dataJsonConfig['snp_close_scroll'] = (isset($POPUP_META['snp_close_scroll']) && $POPUP_META['snp_close_scroll'] ? $POPUP_META['snp_close_scroll'] : '');
    $dataJsonConfig['formDataCollectType'] = (isset($POPUP_META['snp_optin_form_submit']) && $POPUP_META['snp_optin_form_submit'] ? $POPUP_META['snp_optin_form_submit'] : '');

    $dataJsonConfig['snp_optin_redirect_url'] = '';
    if (isset($POPUP_META['snp_optin_redirect']) && $POPUP_META['snp_optin_redirect'] == 'yes' && !empty($POPUP_META['snp_optin_redirect_url'])) {
        $dataJsonConfig['snp_optin_redirect_url'] = $POPUP_META['snp_optin_redirect_url'];
    }

    $dataJsonConfig['afterOptinWebhookUrl'] = '';
    if (isset($POPUP_META['snp_ajax_before_optin']) && $POPUP_META['snp_ajax_before_optin']) {
        $dataJsonConfig['afterOptinWebhookUrl'] = $POPUP_META['snp_ajax_before_optin'];
    } else if (snp_get_option('ajax_before_optin')) {
        $dataJsonConfig['afterOptinWebhookUrl'] = snp_get_option('ajax_before_optin');
    }

    $dataJsonConfig['beforeOptinWebhookUrl'] = '';
    if (isset($POPUP_META['snp_ajax_after_optin']) && $POPUP_META['snp_ajax_after_optin']) {
        $dataJsonConfig['beforeOptinWebhookUrl'] = $POPUP_META['snp_ajax_after_optin'];
    } else if (snp_get_option('ajax_after_optin')) {
        $dataJsonConfig['beforeOptinWebhookUrl'] = snp_get_option('ajax_after_optin');
    }

    $dataJsonConfig['ajaxUrl'] = admin_url('admin-ajax.php');
    if (isset($POPUP_META['snp_ajax_request_handler']) && $POPUP_META['snp_ajax_request_handler']) {
        $dataJsonConfig['ajaxUrl'] = $POPUP_META['snp_ajax_request_handler'];
    } else if (snp_get_option('ajax_request_handler')) {
        $dataJsonConfig['ajaxUrl'] = snp_get_option('ajax_request_handler');
    }

    $CURRENT_URL = snp_get_current_url();
    $return .='	<div id="' . $dataJsonConfig['popupDivId'] . '" class="snp-pop-' . $ID . ' snppopup' . ($type == 'inline' ? ' snp-pop-inline' : '') . ($type == 'widget' ? ' snp-pop-widget' : '') . '">';
    
    if (isset($args['lock_id']) && $args['lock_id']) {
        $return .= '<input type="hidden" class="snp_lock_id" value="' . $args['lock_id'] . '" />';
    }

    if (isset($POPUP_META['snp_cb_close_after']) && $POPUP_META['snp_cb_close_after']) {
        $return .= '<input type="hidden" class="snp_autoclose" value="' . $POPUP_META['snp_cb_close_after'] . '" />';
    }

    if (isset($POPUP_META['snp_open']) && $POPUP_META['snp_open']) {
        $return .= '<input type="hidden" class="snp_open" value="' . $POPUP_META['snp_open'] . '" />';
    } else {
        $return .= '<input type="hidden" class="snp_open" value="load" />';
    }

    if (isset($POPUP_META['snp_close']) && $POPUP_META['snp_close']) {
        $return .= '<input type="hidden" class="snp_close" value="' . $POPUP_META['snp_close'] . '" />';
    } else {
        $return .= '<input type="hidden" class="snp_close" value="close_manual" />';
    }

    $return .= '<input type="hidden" class="snp_show_on_exit" value="' . (isset($POPUP_META['snp_show_on_exit']) ? $POPUP_META['snp_show_on_exit'] : '1') . '" />';
    $return .= '<input type="hidden" class="snp_exit_js_alert_text" value="' . (isset($POPUP_META['snp_exit_js_alert_text'])?str_replace("\r\n", '\n', htmlspecialchars((string) $POPUP_META['snp_exit_js_alert_text'])):'') . '" />';
    $return .= '<input type="hidden" class="snp_exit_scroll_down" value="' . (isset($POPUP_META['snp_exit_scroll_down']) ? $POPUP_META['snp_exit_scroll_down'] : '') . '" />';
    $return .= '<input type="hidden" class="snp_exit_scroll_up" value="' . (isset($POPUP_META['snp_exit_scroll_up']) ? $POPUP_META['snp_exit_scroll_up'] : '') . '" />';
    
    if (isset($POPUP_META['snp_open_after']) && $POPUP_META['snp_open_after']) {
        $return .= '<input type="hidden" class="snp_open_after" value="' . $POPUP_META['snp_open_after'] . '" />';
    }

    if (isset($POPUP_META['snp_open_inactivity']) && $POPUP_META['snp_open_inactivity']) {
        $return .= '<input type="hidden" class="snp_open_inactivity" value="' . $POPUP_META['snp_open_inactivity'] . '" />';
    }

    if (isset($POPUP_META['snp_open_scroll']) && $POPUP_META['snp_open_scroll']) {
        $return .= '<input type="hidden" class="snp_open_scroll" value="' . $POPUP_META['snp_open_scroll'] . '" />';
    }
	
	if (isset($POPUP_META['snp_open_spend_time']) && $POPUP_META['snp_open_spend_time']) {
		$return .= '<input type="hidden" class="snp_open_spend_time" value="' . $POPUP_META['snp_open_spend_time'] . '" />';
	}
	
    if (isset($POPUP_META['snp_close_scroll']) && $POPUP_META['snp_close_scroll']) {
        $return .= '<input type="hidden" class="snp_close_scroll" value="' . $POPUP_META['snp_close_scroll'] . '" />';
    }

    if (isset($POPUP_META['snp_optin_redirect']) && $POPUP_META['snp_optin_redirect'] == 'yes' && !empty($POPUP_META['snp_optin_redirect_url'])) {
        $return .= '<input type="hidden" class="snp_optin_redirect_url" value="' . $POPUP_META['snp_optin_redirect_url'] . '" />';
    } else {
        $return .= '<input type="hidden" class="snp_optin_redirect_url" value="" />';
    }
    
    if (isset($POPUP_META['snp_optin_form_submit']) && !empty($POPUP_META['snp_optin_form_submit'])) {
	    $return .= '<input type="hidden" class="snp_optin_form_submit" value="' . $POPUP_META['snp_optin_form_submit'] . '" />';
    } else {
	    $return .= '<input type="hidden" class="snp_optin_form_submit" value="single" />';
    }

    if (!isset($POPUP_META['snp_popup_overlay'])) {
        $POPUP_META['snp_popup_overlay'] = '';
    }

    $return .= '<input type="hidden" class="snp_show_cb_button" value="' . $POPUP_META['snp_show_cb_button'] . '" />';
    $return .= '<input type="hidden" class="snp_popup_id" value="' . $ID . '" />';
    if ($AB_ID != false) {
        $return .= '<input type="hidden" class="snp_popup_ab_id" value="' . $AB_ID . '" />';
    }
    $return .= '<input type="hidden" class="snp_popup_theme" value="' . $POPUP_META['snp_theme']['theme'] . '" />';
    $return .= '<input type="hidden" class="snp_overlay" value="' . $POPUP_META['snp_popup_overlay'] . '" />';
    $return .= '<input type="hidden" class="snp_cookie_conversion" value="' . (is_numeric($POPUP_META['snp_cookie_conversion']) ? $POPUP_META['snp_cookie_conversion'] : '30') . '" />';
    $return .= '<input type="hidden" class="snp_cookie_close" value="' . (is_numeric($POPUP_META['snp_cookie_close']) && $POPUP_META['snp_cookie_close'] ? $POPUP_META['snp_cookie_close'] : '-1') . '" />';
    
    if (isset($POPUP_META['snp_ajax_before_optin']) && $POPUP_META['snp_ajax_before_optin']) {
        $return .= '<input type="hidden" class="snp_ajax_before_optin" value="' . $POPUP_META['snp_ajax_before_optin'] . '" />';
    } else if (snp_get_option('ajax_before_optin')) {
        $return .= '<input type="hidden" class="snp_ajax_before_optin" value="' . snp_get_option('ajax_before_optin') . '" />';
    }

    if (isset($POPUP_META['snp_ajax_after_optin']) && $POPUP_META['snp_ajax_after_optin']) {
        $return .= '<input type="hidden" class="snp_ajax_after_optin" value="' . $POPUP_META['snp_ajax_after_optin'] . '" />';
    } else if (snp_get_option('ajax_after_optin')) {
        $return .= '<input type="hidden" class="snp_ajax_after_optin" value="' . snp_get_option('ajax_after_optin') . '" />';
    }

    if (isset($POPUP_META['snp_ajax_request_handler']) && $POPUP_META['snp_ajax_request_handler']) {
        $return .= '<input type="hidden" class="snp_ajax_url" value="' . $POPUP_META['snp_ajax_request_handler'] . '" />';
    } else if (snp_get_option('ajax_request_handler')) {
        $return .= '<input type="hidden" class="snp_ajax_url" value="' . snp_get_option('ajax_request_handler') . '" />';
    }

    $THEME_INFO = snp_get_theme($POPUP_META['snp_theme']['theme']);
    ob_start();
    include($THEME_INFO['DIR'] . '/template.php');
    $return .= ob_get_clean();
    if (!isset($POPUP_META['snp_cb_img'])) {
        $POPUP_META['snp_cb_img'] = '';
    }

    if (!isset($POPUP_META['snp_custom_css'])) {
        $POPUP_META['snp_custom_css'] = '';
    }

    if (!isset($POPUP_META['snp_custom_js'])) {
        $POPUP_META['snp_custom_js'] = '';
    }

    if (!isset($POPUP_META['snp_theme']['mode']) || $POPUP_META['snp_theme']['mode'] == '0') {
        if ($POPUP_META['snp_popup_overlay'] == 'image' && $POPUP_META['snp_overlay_image']) {
            $return .= '<style>.snp-pop-' . $ID . '-overlay { background: url(\'' . $POPUP_META['snp_overlay_image'] . '\');}</style>';
        }

        if ($POPUP_META['snp_cb_img'] != 'close_default' && $POPUP_META['snp_cb_img'] != '') {
            $return .= '<style>';
            switch ($POPUP_META['snp_cb_img']) {
                case 'close_1':
                    $return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 31px; height: 31px; top: -15px; right: -15px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
                    break;
                case 'close_2':
                    $return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 19px; height: 19px; top: -8px; right: -8px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
                    break;
                case 'close_3':
                    $return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 33px; height: 33px; top: -16px; right: -16px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
                    break;
                case 'close_4':
                case 'close_5':
                    $return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 20px; height: 20px; top: -10px; right: -10px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
                    break;
                case 'close_6':
                    $return .= '.snp-pop-' . $ID . '-wrap .fancybox-close { width: 24px; height: 24px; top: -12px; right: -12px; background: url(\'' . SNP_URL . 'img/' . $POPUP_META['snp_cb_img'] . '.png\');}';
                    break;
            }
            $return .= '</style>';
        }
    }

    if ($POPUP_META['snp_custom_css'] != '') {
        $return .= '<style>';
        $return .= $POPUP_META['snp_custom_css'];
        $return .= '</style>';
    }

    if ($POPUP_META['snp_custom_js'] != '') {
        $return .= '<script>';
        $return .= $POPUP_META['snp_custom_js'];
        $return .= '</script>';
    }

    //Hooks
    if (
            (isset($POPUP_META['snp_js_np_submit']) && !empty($POPUP_META['snp_js_np_submit'])) ||
            (isset($POPUP_META['snp_js_np_submit_success']) && !empty($POPUP_META['snp_js_np_submit_success'])) ||
            (isset($POPUP_META['snp_js_np_submit_error']) && !empty($POPUP_META['snp_js_np_submit_error'])) ||
            (isset($POPUP_META['snp_js_np_submit_after']) && !empty($POPUP_META['snp_js_np_submit_after'])) ||
            (isset($POPUP_META['snp_js_np_convert']) && !empty($POPUP_META['snp_js_np_convert'])) ||
            (isset($POPUP_META['snp_js_np_open']) && !empty($POPUP_META['snp_js_np_open'])) ||
            (isset($POPUP_META['snp_js_np_gotostep']) && !empty($POPUP_META['snp_js_np_gotostep'])) ||
            (isset($POPUP_META['snp_js_np_open_link']) && !empty($POPUP_META['snp_js_np_open_link'])) ||
            (isset($POPUP_META['snp_sound_on_open']) && !empty($POPUP_META['snp_sound_on_open']))
    ) {
        $return .= '<script>' . "\n";
        $return .= 'jQuery(document).ready(function() {' . "\n";

        if (isset($POPUP_META['snp_js_np_submit']) && !empty($POPUP_META['snp_js_np_submit'])) {
            $return .= 'jQuery(document).on("ninja_popups_submit", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_submit'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        if (isset($POPUP_META['snp_js_np_submit_success']) && !empty($POPUP_META['snp_js_np_submit_success'])) {
            $return .= 'jQuery(document).on("ninja_popups_submit_success", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_submit_success'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }


        if (isset($POPUP_META['snp_js_np_submit_error']) && !empty($POPUP_META['snp_js_np_submit_error'])) {
            $return .= 'jQuery(document).on("ninja_popups_submit_error", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_submit_error'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        if (isset($POPUP_META['snp_js_np_submit_after']) && !empty($POPUP_META['snp_js_np_submit_after'])) {
            $return .= 'jQuery(document).on("ninja_popups_submit_after", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_submit_after'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }


        if (isset($POPUP_META['snp_js_np_convert']) && !empty($POPUP_META['snp_js_np_convert'])) {
            $return .= 'jQuery(document).on("ninja_popups_convert", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_convert'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        if (isset($POPUP_META['snp_js_np_open']) && !empty($POPUP_META['snp_js_np_open'])) {
            $return .= 'jQuery(document).on("ninja_popups_open", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_open'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        if (isset($POPUP_META['snp_js_np_gotostep']) && !empty($POPUP_META['snp_js_np_gotostep'])) {
            $return .= 'jQuery(document).on("ninja_popups_gotostep", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_gotostep'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        if (isset($POPUP_META['snp_js_np_open_link']) && !empty($POPUP_META['snp_js_np_open_link'])) {
            $return .= 'jQuery(document).on("ninja_popups_open_link", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= $POPUP_META['snp_js_np_open_link'] . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        if (isset($POPUP_META['snp_sound_on_open']) && !empty($POPUP_META['snp_sound_on_open'])) {
            $return .= 'jQuery(document).on("ninja_popups_open", function(event, data) {' . "\n";
            $return .= 'if (event.popup_id == ' . $ID . ') {' . "\n";
            $return .= 'var audio = new Audio("' . $POPUP_META['snp_sound_on_open'] . '");' . "\n";
            $return .= 'audio.play();' . "\n";
            $return .= '}' . "\n";
            $return .= "});" . "\n";
        }

        $return .= '});' . "\n";
        $return .= '</script>' . "\n";
    }

    if ((isset($THEME_INFO['OPEN_FUNCTION']) || isset($THEME_INFO['CLOSE_FUNCION'])) && $type != 'inline') {
        $return .= '<script>'."\n";
        $return .= 'snp_f[\'snppopup-' . $type . ($type == 'content' || $type == 'inline' ? '-' . $ID : '') . '-open\']=' . $THEME_INFO['OPEN_FUNCTION'] . ($POPUP_META['snp_theme']['theme'] == 'builder' ? abs($ID) : '') . ';'."\n";
        $return .= 'snp_f[\'snppopup-' . $type . ($type == 'content' || $type == 'inline' ? '-' . $ID : '') . '-close\']=' . $THEME_INFO['CLOSE_FUNCION'] . ($POPUP_META['snp_theme']['theme'] == 'builder' ? abs($ID) : '') .';'."\n";
        $return .= '</script>'."\n";
    }
    $return .= '</div>';

    //$return .= '<script>';
    //$return .= 'NinjaPopup.init("' . $ID . '", "' . $type . '", ' . json_encode($dataJsonConfig) . ');';
    //$return .= '</script>';

    return $return;
}

function snp_footer()
{
    global $snp_popups, $snp_ignore_cookies, $post, $detect;
    ?>
    <script>
        var snp_f = [];
        var snp_hostname = new RegExp(location.host);
        var snp_http = new RegExp("^(http|https)://", "i");
        var snp_cookie_prefix = '<?php echo (string)snp_get_option('cookie_prefix') ?>';
        var snp_separate_cookies = <?php echo (snp_get_option('separate_popup_cookies') == 'yes') ? 'true' : 'false' ?>;
        var snp_ajax_url = '<?php echo admin_url('admin-ajax.php') ?>';
        var snp_domain_url = '<?php echo site_url(); ?>';
		var snp_ajax_nonce = '<?php echo wp_create_nonce('snp_popup_submit'); ?>';
		var snp_ajax_ping_time = <?php echo (snp_get_option('wp_ajax_ping_time')) ? (snp_get_option('wp_ajax_ping_time')*1000) : 1000 ?>;
        var snp_ignore_cookies = <?php if (!$snp_ignore_cookies) echo 'false'; else echo 'true'; ?>;
        var snp_enable_analytics_events = <?php if (snp_get_option('enable_analytics_events') == 'yes' && !is_admin()) echo 'true'; else echo 'false'; ?>;
        var snp_is_mobile = <?php if ($detect->isMobile()) { echo 'true'; } else { echo 'false'; } ?>;
        var snp_enable_mobile = <?php if (snp_get_option('enable_mobile') == 'enabled' && !is_admin()) echo 'true'; else echo 'false'; ?>;
        var snp_use_in_all = <?php $use_in = snp_get_option('use_in'); if (isset($use_in['all']) && $use_in['all'] == 1) echo 'true'; else echo 'false'; ?>;
        var snp_excluded_urls = [];
        var snp_close_on_esc_key = <?php echo (snp_get_option('close_esc_key') == '1') ? 'true' : 'false' ?>;
        <?php
        $exit_excluded_urls = snp_get_option('exit_excluded_urls');
        if (is_array($exit_excluded_urls)) {
            foreach ($exit_excluded_urls as $url) {
                echo "snp_excluded_urls.push('" . $url . "');";
            }
        }
        ?>
    </script>
    <div class="snp-root">
        <input type="hidden" id="snp_popup" value="" />
        <input type="hidden" id="snp_popup_id" value="" />
        <input type="hidden" id="snp_popup_theme" value="" />
        <input type="hidden" id="snp_exithref" value="" />
        <input type="hidden" id="snp_exittarget" value="" />
        <?php if (function_exists('is_woocommerce') && function_exists('WC')) { ?>
            <input type="hidden" id="snp_woocommerce_cart_contents" value="<?php echo WC()->cart->cart_contents_count; ?>" />
        <?php } ?>
        <?php
        $addedPopupContent = array();

        // welcome popup
        if (!empty($snp_popups['welcome']['ID']) && intval($snp_popups['welcome']['ID'])) {
            if (!in_array($snp_popups['welcome']['ID'], $addedPopupContent)) {
                echo snp_create_popup($snp_popups['welcome']['ID'], $snp_popups['welcome']['AB_ID'], 'welcome');

                $addedPopupContent[] = $snp_popups['welcome']['ID'];
            }
        }

        // exit popup
        if (!empty($snp_popups['exit']['ID']) && intval($snp_popups['exit']['ID'])) {
            if (!in_array($snp_popups['exit']['ID'], $addedPopupContent)) {
                echo snp_create_popup($snp_popups['exit']['ID'], $snp_popups['exit']['AB_ID'], 'exit');

                $addedPopupContent[] = $snp_popups['exit']['ID'];
            }
        }

        // popups from content
        if (isset($snp_popups['content']) && is_array($snp_popups['content'])) {
            foreach ($snp_popups['content'] as $popup_id) {
                if (!in_array($popup_id['ID'], $addedPopupContent)) {
                    echo snp_create_popup($popup_id['ID'], $popup_id['AB_ID'], 'content');

                    $addedPopupContent[] = $popup_id['ID'];
                }
            }
        }
        ?>
        <?php if (snp_get_option('recaptcha_api_key')) { ?>
            <script type="text/javascript">
                grecaptcha.ready(function () {
                    grecaptcha.execute('<?php echo snp_get_option('recaptcha_api_key'); ?>', { action: 'NinjaPopUp' }).then(function (token) {
                        jQuery('.ninja-popup-recaptcha3').val(token);
                    });
                });
            </script>
        <?php } ?>
    </div>
    <?php
}

function snp_enqueue_social_script()
{
    if (!snp_get_option('js_disable_fb') || is_admin()) {
        // Facebook
        wp_enqueue_script('fbsdk', 'https://connect.facebook.net/' . snp_get_option('fb_locale', 'en_GB') . '/all.js#xfbml=1', array());
        wp_localize_script('fbsdk', 'fbsdku', array(
            'xfbml' => 1,
        ));
    }

    if (!snp_get_option('js_disable_gp') || is_admin()) {
        // Google Plus
        // Google Plus
        wp_enqueue_script('plusone', 'https://apis.google.com/js/plusone.js', array());
    }

    if (!snp_get_option('js_disable_tw') || is_admin()) {
        // Twitter
        wp_enqueue_script('twitter', 'https://platform.twitter.com/widgets.js', array());
    }

    if (!snp_get_option('js_disable_li') || is_admin()) {
        // Linkedin
        wp_enqueue_script('linkedin', 'https://platform.linkedin.com/in.js', array());
    }
}

function snp_ninja_popup_shortcode($attr, $content = null)
{
    global $detect;

    extract(shortcode_atts(array('id' => '', 'mobile_id' => '', 'autoopen' => false, 'enablemobile' => true, 'disable_logged_for_logged' => false), $attr));

    if ($enablemobile == false && $detect->isMobile()) {
        return;
    }

    if ($mobile_id && $detect->isMobile()) {
        snp_run_popup($mobile_id, 'content');
    } else {
        snp_run_popup($id, 'content');
    }

    if ($disable_logged_for_logged == true && is_user_logged_in()) {
        return;
    }

    if (isset($autoopen) && $autoopen == true) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var snp_open_after = jQuery('#snppopup-content-<?php echo $id; ?> .snp_open_after').val();
                if (snp_open_after) {
                    snp_timer_o = setTimeout("snp_open_popup('','','snppopup-content-<?php echo $id; ?>','content');", snp_open_after * 1000);
                } else {
                    snp_open_popup('', '', 'snppopup-content-<?php echo $id; ?>', 'content');
                }
            });
        </script>
        <?php
    }

    if ($content) {
        return '<a href="#ninja-popup-' . $id . '" class="snppopup-content" rel="' . $id . '">' . $content . ' </a>';
    }

    return '';
}
add_shortcode('ninja-popup', 'snp_ninja_popup_shortcode');

function snp_ninja_popup_external_link()
{
    return '<iframe src="" style="width: 100%; height: 100%;" class="ninja-popup-external-link-iframe"></iframe>';
}
add_shortcode('ninja-popup-external-link', 'snp_ninja_popup_external_link');

function snp_run()
{
    global $post, $detect, $countryCode, $cityCode, $zipCode;

    if (is_404()) {
        return;
    }
    
    if (snp_get_option('enable') == 'disabled') {
        return;
    }

    if ((isset($_REQUEST['nphide']) && $_REQUEST['nphide'] == 1) || isset($_COOKIE['nphide']) && $_COOKIE['nphide'] == 1) {
        setcookie('nphide', 1, 0, COOKIEPATH, COOKIE_DOMAIN, false);
        return;
    }

    $welcome_phrase = 'welcome';
    $exit_phrase = 'exit';
    
    if ($detect->isMobile()) {
    	$welcome_phrase = 'mobile_welcome';
    	$exit_phrase = 'mobile_exit';
    }

    $WELCOME_ID = 'global';
    $EXIT_ID = 'global';
    $POST_CATS = [];

    $postId = false;
    if (isset($post->ID)) {
        $postId = $post->ID;
    }

    if (function_exists('is_woocommerce') && is_shop()) {
        $postId = get_option( 'woocommerce_shop_page_id' );
    }

    if (function_exists('is_woocommerce') && is_cart()) {
        $postId = get_option( 'woocommerce_cart_page_id' );
    }

    if (function_exists('is_woocommerce') && is_checkout()) {
        $postId = get_option( 'woocommerce_checkout_page_id' );
    }

    if (function_exists('is_woocommerce') && is_account_page()) {
        $postId = get_option( 'woocommerce_myaccount_page_id' );
    }

    if (
        $postId && (
            is_page() || is_single() || (
                function_exists('is_woocommerce') &&
                is_woocommerce()
            )
        )
    ) {
        $WELCOME_ID = get_post_meta($postId, 'snp_p_'.$welcome_phrase.'_popup', true);
    	$WELCOME_ID = ($WELCOME_ID ? $WELCOME_ID : 'global');
        
        $EXIT_ID = get_post_meta($postId, 'snp_p_'.$exit_phrase.'_popup', true);
        $EXIT_ID = ($EXIT_ID ? $EXIT_ID : 'global');

        if ($WELCOME_ID == 'global' || $EXIT_ID == 'global') {
            if ($post->post_type == 'post') {
                $POST_CATS = wp_get_post_categories($post->ID);
            }

            $enable_taxs = snp_get_option('enable_taxs');
            if (is_array($enable_taxs)) {
                foreach ((array) $enable_taxs as $k => $v) {
                    $POST_CATS = array_merge((array) $POST_CATS, (array) wp_get_object_terms($post->ID, $k, array('fields' => 'ids')));
                }
            }

            if (isset($POST_CATS) && is_array($POST_CATS)) {
                foreach ($POST_CATS as $term_id) {
                    $term_meta = get_option("snp_taxonomy_" . $term_id);
                    if (isset($term_meta[$welcome_phrase]) && $WELCOME_ID == 'global') {
                        $WELCOME_ID = $term_meta[$welcome_phrase];
                    }

                    if (isset($term_meta[$exit_phrase]) && $EXIT_ID == 'global') {
                        $EXIT_ID = $term_meta[$exit_phrase];
                    }
                }
            }
        }
    } elseif (is_category() || is_tax() || is_tag() || is_archive()) {
        $category = get_queried_object();
        if (isset($category->term_id)) {
            $term_meta = get_option("snp_taxonomy_" . $category->term_id);
        } else {
            $term_meta = [];
        }
        if (isset($term_meta[$welcome_phrase])) {
            $WELCOME_ID = $term_meta[$welcome_phrase];
        } else {
            $WELCOME_ID = 'global';
        }

        if (isset($term_meta[$exit_phrase])) {
            $EXIT_ID = $term_meta[$exit_phrase];
        } else {
            $EXIT_ID = 'global';
        }
    }

    if (defined('ICL_LANGUAGE_CODE')) {
        $snp_var_sufix = '_' . ICL_LANGUAGE_CODE;
    } else {
        $snp_var_sufix = '';
    }

    // WELCOME
    if (snp_get_option('welcome_disable_for_logged') == 1 && is_user_logged_in()) {
        //Nothing to do here
    } else if (snp_get_option('welcome_enabled_for_logged') == 1 && !is_user_logged_in()) {
        //Nothing to do here
    } else {
        $WELCOME_ID = apply_filters('ninjapopups_welcome_id', $WELCOME_ID);
        if ($WELCOME_ID !== 'disabled' && $WELCOME_ID !== 'global') {
            snp_run_popup($WELCOME_ID, 'welcome');
        } elseif ($WELCOME_ID === 'global') {

        	$WELCOME_ID = snp_get_option($welcome_phrase.'_popup' . $snp_var_sufix);
            if ((int)snp_get_option('geoip_popup') === 1) {
                if (($welcomeGeoipPopups = snp_get_option($welcome_phrase.'_geoip_popup'))) {
                    foreach ($welcomeGeoipPopups as $key => $value) {
                        if (
                            is_numeric($key) && (
                                !empty($value['country']) ||
                                !empty($value['city']) ||
                                !empty($value['zip'])
                            )
                        ) {
                            $countryPassed = false;
                            if (empty($value['country'])) {
                                $countryPassed = true;
                            } else if (!empty($value['country']) && strtolower($countryCode) === strtolower($value['country'])) {
                                $countryPassed = true;
                            }

                            $cityPassed = false;
                            if (empty($value['city'])) {
                                $cityPassed = true;
                            } else if (!empty($value['city']) && strtolower($cityCode) == strtolower($value['city'])) {
                                $cityPassed = true;
                            }

                            $zipPassed = false;
                            if (empty($value['zip'])) {
                                $zipPassed = true;
                            } else if (!empty($value['zip']) && strtolower($zipCode) == strtolower($value['zip'])) {
                                $zipPassed = true;
                            }

                            if ($countryPassed && $cityPassed && $zipPassed) {
                                $WELCOME_ID = $value['popup'];
                                break;
                            }
                        }
                    }
                }
            }

            if ($WELCOME_ID === 'global' && defined('ICL_LANGUAGE_CODE')) {
                $WELCOME_ID = snp_get_option('welcome_popup');
            }
            
            if ($WELCOME_ID !== 'disabled') {
                $welcome_display_in = snp_get_option('welcome_display_in');
                if (
                    is_front_page() &&
                    isset($welcome_display_in['home']) &&
                    $welcome_display_in['home'] == 1
                ) {
                    //home
                    snp_run_popup($WELCOME_ID, 'welcome');
                }
                elseif (
                    is_page() &&
                    isset($welcome_display_in['pages']) &&
                    $welcome_display_in['pages'] == 1
                ) {
                    //page
                    snp_run_popup($WELCOME_ID, 'welcome');
                } elseif (
                    is_single() && 
                    isset($welcome_display_in['posts']) && 
                    $welcome_display_in['posts'] == 1
                ) {
                    //post
                    snp_run_popup($WELCOME_ID, 'welcome');
                } elseif (
                    isset($welcome_display_in['others']) && 
                    $welcome_display_in['others'] == 1 && 
                    !is_front_page() && 
                    !is_page() &&
                    !is_single()
                ) {
                    // other
                    snp_run_popup($WELCOME_ID, 'welcome');
                } elseif (
                    isset($welcome_display_in['others']) &&
                    $welcome_display_in['others'] == 1 &&
                    function_exists('is_woocommerce') &&
                    is_woocommerce()
                ) {
                    // woocomerce
                    snp_run_popup($WELCOME_ID, 'welcome');
                }
            }
        }
    }

    // EXIT
    if (snp_get_option('exit_disable_for_logged') == 1 && is_user_logged_in()) {
        //Nothing to do here
    } else if (snp_get_option('exit_enabled_for_logged') == 1 && !is_user_logged_in()) {
        //Nothing to do here    
    } else {
        $EXIT_ID = apply_filters('ninjapopups_exit_id', $EXIT_ID);
        if ($EXIT_ID != 'disabled' && $EXIT_ID != 'global') {
            snp_run_popup($EXIT_ID, 'exit');
        } elseif ($EXIT_ID === 'global') {
        	$EXIT_ID = snp_get_option($exit_phrase.'_popup' . $snp_var_sufix);
            if (snp_get_option('geoip_popup') === 1) {
                if (($exitGeoipPopups = snp_get_option($exit_phrase.'_geoip_popup'))) {
                    foreach ($exitGeoipPopups as $key => $value) {
                        if (
                            is_numeric($key) && (
                                !empty($value['country']) ||
                                !empty($value['city']) ||
                                !empty($value['zip'])
                            )
                        ) {
                            $countryPassed = false;
                            if (empty($value['country'])) {
                                $countryPassed = true;
                            } else if (!empty($value['country']) && strtolower($countryCode) === strtolower($value['country'])) {
                                $countryPassed = true;
                            }

                            $cityPassed = false;
                            if (empty($value['city'])) {
                                $cityPassed = true;
                            } else if (!empty($value['city']) && strtolower($cityCode) == strtolower($value['city'])) {
                                $cityPassed = true;
                            }

                            $zipPassed = false;
                            if (empty($value['zip'])) {
                                $zipPassed = true;
                            } else if (!empty($value['zip']) && strtolower($zipCode) == strtolower($value['zip'])) {
                                $zipPassed = true;
                            }

                            if ($countryPassed && $cityPassed && $zipPassed) {
                                $EXIT_ID = $value['popup'];
                                break;
                            }
                        }
                    }
                }
            }

            if ($EXIT_ID === 'global' && defined('ICL_LANGUAGE_CODE')) {
                $EXIT_ID = snp_get_option('exit_popup');
            }
            
            if ($EXIT_ID != 'disabled') {
                $exit_display_in = snp_get_option('exit_display_in');
                if (
                    is_front_page() && 
                    isset($exit_display_in['home']) && 
                    $exit_display_in['home'] == 1
                ) {
                    //home
                    snp_run_popup($EXIT_ID, 'exit');
                } elseif (
                    is_page() && 
                    isset($exit_display_in['pages']) && 
                    $exit_display_in['pages'] == 1
                ) {
                    //page
                    snp_run_popup($EXIT_ID, 'exit');
                } elseif (
                    is_single() && 
                    isset($exit_display_in['posts']) && 
                    $exit_display_in['posts'] == 1
                ) {
                    //post
                    snp_run_popup($EXIT_ID, 'exit');
                } elseif (
                    isset($exit_display_in['others']) && 
                    $exit_display_in['others'] == 1 && 
                    !is_front_page() && 
                    !is_page() && 
                    !is_single()
                ) {
                    // other
                    snp_run_popup($EXIT_ID, 'exit');
                }
            }
        }
    }

    add_filter('wp_nav_menu_objects', 'snp_wp_nav_menu_objects');
}

function snp_wp_nav_menu_objects($items)
{
    $parents = array();
    foreach ($items as $item) {
        if (strpos($item->url, '#ninja-popup-') !== FALSE) {
            $ID = str_replace('#ninja-popup-', '', $item->url);
            if (intval($ID)) {
                snp_run_popup(intval($ID), 'content');
            }
        }
    }

    return $items;
}

function snp_setup()
{

    register_post_type('snp_popups', array(
        'label' => 'Ninja Popups',
        'labels' => array(
            'name' => 'Ninja Popups',
            'menu_name' => 'Ninja Popups',
            'singular_name' => 'Popup',
            'add_new' => 'Add New Popup',
            'all_items' => 'Popups',
            'add_new_item' => 'Add New Popup',
            'edit_item' => 'Edit Popup',
            'new_item' => 'New Popup',
            'view_item' => 'View Popup',
            'search_items' => 'Search Popups',
            'not_found' => 'No popups found',
            'not_found_in_trash' => 'No popups found in Trash'
        ),
        'hierarchical' => false,
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => false,
        'show_in_menu' => true,
        'capability_type' => 'page',
        'supports' => array('title', 'editor'),
        'menu_position' => 207,
        'menu_icon' => ''
    ));

    register_post_type('snp_ab', array(
        'label' => 'A/B Testing',
        'labels' => array(
            'name' => 'A/B Testing',
            'menu_name' => 'A/B Testing',
            'singular_name' => 'A/B Testing',
            'add_new' => 'Add New',
            'all_items' => 'A/B Testing',
            'add_new_item' => 'Add New',
            'edit_item' => 'Edit',
            'new_item' => 'New',
            'view_item' => 'View',
            'search_items' => 'Search',
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash'
        ),
        'hierarchical' => false,
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => false,
        'show_in_menu' => 'edit.php?post_type=snp_popups',
        'capability_type' => 'page',
        'supports' => array('title')
    ));

    register_post_type('snp_mail_log', array(
        'label' => 'WP-Mail Logs',
        'labels' => array(
            'name' => __('WP-Mail Records', 'nhp-opts'),
            'singular_name' => __('WP-Mail Record', 'nhp-opts'),
            'menu_name' => __('WP-Mail Log', 'nhp-opts'),
        ),
        'hierarchical' => false,
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => false,
        'show_in_menu' => 'edit.php?post_type=snp_popups',
        'capability_type' => 'page',
        'supports' => array('title', 'editor'),
        'has_archive' => false,
        'query_var' => 'snp_mail_log',
        'capabilities'         => array(
            'read_post'        => 'manage_options',
            'delete_post'      => 'do_not_allow',
            'edit_post'        => 'manage_options',
            'edit_other_posts' => 'manage_options',
            'read_posts'       => 'manage_options',
            'edit_posts'       => 'manage_options',
            'delete_posts'     => 'do_not_allow',
            'create_posts'     => 'do_not_allow',
        ),
    ));

    add_action('wp_ajax_nopriv_snp_popup_stats', 'snp_popup_stats');
    add_action('wp_ajax_snp_popup_stats', 'snp_popup_stats');
    add_action('wp_ajax_nopriv_snp_popup_submit', 'snp_popup_submit');
    add_action('wp_ajax_snp_popup_submit', 'snp_popup_submit');
    add_action('wp_ajax_nopriv_snp_popup_spend_time', 'snp_popup_spend_time');
    add_action('wp_ajax_snp_popup_spend_time', 'snp_popup_spend_time');
    //add_filter('wp_mail', 'snp_log_wp_mail');
    wp_enqueue_script('jquery');
}

function snp_remove_meta_boxes()
{
    remove_meta_box('linktargetdiv', 'snp_mail_log', 'normal');
    remove_meta_box('linkxfndiv', 'snp_mail_log', 'normal');
    remove_meta_box('linkadvanceddiv', 'snp_mail_log', 'normal');
    remove_meta_box('postexcerpt', 'snp_mail_log', 'normal');
    remove_meta_box('trackbacksdiv', 'snp_mail_log', 'normal');
    remove_meta_box('postcustom', 'snp_mail_log', 'normal');
    remove_meta_box('commentstatusdiv', 'snp_mail_log', 'normal');
    remove_meta_box('commentsdiv', 'snp_mail_log', 'normal');
    remove_meta_box('revisionsdiv', 'snp_mail_log', 'normal');
    remove_meta_box('authordiv', 'snp_mail_log', 'normal');
    remove_meta_box('slugdiv', 'snp_mail_log', 'normal');
    remove_meta_box('sqpt-meta-tags', 'snp_mail_log', 'normal');
    remove_meta_box('submitdiv', 'snp_mail_log', 'side');
}

function remove_bulk_actions($actions)
{
    unset( $actions['inline'] );
    return $actions;
}

add_action( 'admin_menu', 'snp_remove_meta_boxes' );

add_filter('bulk_actions-snp_mail_log', 'remove_bulk_actions');

if (!is_admin() && !defined('DOING_CRON')) {
    add_action('init', 'snp_session_start', 1);
}

add_action('init', 'snp_setup', 15);

if (is_admin()) {
    add_action('init', 'snp_setup_admin', 15);
}

if (!is_admin()) {
    if (snp_get_option('run_hook') == 'wp') {
        add_action('wp', 'snp_run');
    } else {
        add_action('get_header', 'snp_run');
    }
}

function snp_session_start() {
    if (!session_id()) {
        session_start();
    }
}

function snp_popup_spend_time()
{
    if (!session_id()) {
        session_start();
    }
    
    $time = 1;
    if (!isset($_SESSION['snp_time_on_page_counter'])) {
        $_SESSION['snp_time_on_page_counter'] = $time;
    } else {
        $time = $_SESSION['snp_time_on_page_counter'];
        
        $_SESSION['snp_time_on_page_counter'] = $time + 1;
    }
    
    echo json_encode(array(
        'time' => $time
    ));
    exit;
}

function snp_update_log_subscription($cf_data, $log_list_id, $errors = null)
{
    global $wpdb;

    if (isset($_POST['name'])) {
        $cf_data['name'] = $_POST['name'];
    }

    $data = array(
        'action'        => 'subscribtion',
        'email'         => snp_trim($_POST['email']),
        'ip'            => $_SERVER['REMOTE_ADDR'],
        'browser'       => $_SERVER['HTTP_USER_AGENT'],       
        'list'          => $log_list_id,
        'popup_id'      => $_POST['popup_ID'],
        'custom_fields' => json_encode($cf_data),
        'referer'       => $_SERVER['HTTP_REFERER'],
        'errors'        => $errors,
    );

    $result = $wpdb->insert($wpdb->prefix.'snp_log', $data);
}

function snp_update_log_popup($popup_id, $errors = null)
{
    global $wpdb;

    $data = array(
        'action'   => 'popup_view',
        'ip'       => $_SERVER['REMOTE_ADDR'],
        'browser'  => $_SERVER['HTTP_USER_AGENT'],
        'popup_id' => $popup_id,
        'referer'  => $_SERVER['HTTP_REFERER'],
        'errors'   => $errors,
    );

    $wpdb->insert($wpdb->prefix.'snp_log', $data, array('%s','%s','%s','%s','%s','%s'));
}

function snp_get_mc_fields($apikey, $list_id)
{
    require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';
    
    $rest = new MC_Lists($apikey);
    $fields = json_decode($rest->mergeFields($list_id));

    $result = array();
    foreach($fields->merge_fields as $v) {
        $result[$v->merge_id]['field'] = $v->tag;
        $result[$v->merge_id]['name'] = $v->name;
        $result[$v->merge_id]['required'] = $v->required;
    }

    return json_encode($result, true);
}

function snp_get_mc_groups($apikey, $list_id)
{
    require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';

    $rest = new MC_Lists($apikey);
    $groups = json_decode($rest->getGroups($list_id));

    $result = array();
    foreach ($groups->categories as $v) {
        $result[$v->id]['field'] = $v->id;
        $result[$v->id]['name'] = $v->title;
    }

    return json_encode($result, true);
}

function snp_get_sharpspring_fields($account_id, $secret_key)
{
    if (!defined('SHARPSPRING_ACCOUNTID')) {
        define('SHARPSPRING_ACCOUNTID', snp_get_option('ml_sharpspring_account_id'));
    }

    if (!defined('SHARPSPRING_SECRETKEY')) {
        define('SHARPSPRING_SECRETKEY', snp_get_option('ml_sharpspring_secret_key'));
    }

    require_once SNP_DIR_PATH . '/include/sharpspring/sharpspring.php';

    $api = new \CollingMedia\SharpSpring(SHARPSPRING_ACCOUNTID, SHARPSPRING_SECRETKEY);

    $response = $api->call('getFields', array(
        'where' => ''
    ));

    $result = array();
    if (isset($response['result']['field'])) {
        foreach ($response['result']['field'] as $field) {
            $result[$field['id']]['field'] = $field['systemName'];
            $result[$field['id']]['name'] = $field['label'];
            $result[$field['id']]['required'] = $field['isRequired'];
        }
    }

    return json_encode($result, true);
}

function snp_log_wp_mail($mail)
{
    $logger = new \Relio\Entity\MailLogger();
    $logger->log($mail['to'], $mail['subject'], $mail['message'], $mail['headers'], $mail['attachments']);
    $logger->save();
}
