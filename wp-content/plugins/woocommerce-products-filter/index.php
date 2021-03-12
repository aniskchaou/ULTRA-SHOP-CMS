<?php
/*
  Plugin Name: WOOF - WooCommerce Products Filter
  Plugin URI: https://products-filter.com/
  Description: WOOF - WooCommerce Products Filter. Flexible, easy and robust products filter for WooCommerce store site!
  Requires at least: WP 4.1.0
  Tested up to: WP 5.4
  Author: realmag777
  Author URI: https://pluginus.net/
  Version: 2.2.4.2
  Requires PHP: 5.4
  Tags: filter,search,woocommerce,woocommerce filter,woocommerce product filter,woocommerce products filter,products filter,product filter,filter of products,filter for products,filter for woocommerce
  Text Domain: woocommerce-products-filter
  Domain Path: /languages
  Forum URI: https://pluginus.net/support/forum/woof-woocommerce-products-filter/
  WC requires at least: 3.6.0
  WC tested up to: 4.8
 */

//update_option('woof_settings', '');//nearly absolute reset of the plugin settings
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/*
  ini_set('post_max_size', '100M');
  ini_set('upload_max_filesize', '100M');
  ini_set('max_input_vars', 10000);
 */

//for testing on clients sites
if ($_SERVER['REMOTE_ADDR'] != '2.xxx.83.xxx') {
    //return;
}

//***
if (defined('DOING_AJAX')) {
    if (isset($_REQUEST['action'])) {
        //for wp plugin WP Live Chat Support http://wp-livechat.com/
        if ($_REQUEST['action'] == 'wplc_call_to_server_visitor') {
            return;
        }
    }
}
//***

define('WOOF_PATH', plugin_dir_path(__FILE__));
define('WOOF_LINK', plugin_dir_url(__FILE__));
define('WOOF_PLUGIN_NAME', plugin_basename(__FILE__));
define('WOOF_EXT_PATH', WOOF_PATH . 'ext/');
define('WOOF_VERSION', '2.2.4.2');
define('WOOF_MIN_WOOCOMMERCE_VERSION', '3.6');
//classes
include WOOF_PATH . 'classes/storage.php';
include WOOF_PATH . 'classes/helper.php';
include WOOF_PATH . 'classes/cron.php';
include WOOF_PATH . 'classes/hooks.php';
include WOOF_PATH . 'classes/ext.php';
//***
include WOOF_PATH . 'classes/counter.php';
include WOOF_PATH . 'classes/widgets.php';
//***
include WOOF_PATH . 'classes/alert.php';
include WOOF_PATH . 'classes/rate_alert.php';
/*
  try {
  //include the WPML installer in the theme
  include WOOF_PATH . '/installer/loader.php';
  WP_Installer_Setup($wp_installer_instance, array(
  'plugins_install_tab' => 1, // optional, default value: 0
  'affiliate_id:wpml' => '180271', // optional, default value: empty
  'affiliate_key:wpml' => 'ykFQUy0inySN', // optional, default value: empty
  'src_name' => '', // optional, default value: empty, needed for coupons
  'src_author' => '', // optional, default value: empty, needed for coupons
  'repositories_include' => array('wpml') // optional, default to empty (show all)
  )
  );
  } catch (Exeption $e) {

  } */

//***
//09-12-2020
final class WOOF {

    public $settings = array();
    public $html_types = array(
        'radio' => 'Radio',
        'checkbox' => 'Checkbox',
        'select' => 'Drop-down',
        'mselect' => 'Multi drop-down'
    );
    public $items_keys = array(
        'by_price'
    );
    public static $query_cache_table = 'woof_query_cache';
    public $is_activated = true;
    private $session_rct_key = 'woof_really_current_term';
    public $storage = null;
    //public $storage_type = 'session'; //transient
    public $storage_type = 'transient'; //session
    public $rate_alert = null;
    public $is_free_ver = false; //just for notices, do not think that this is hack for free version :)

    public function __construct() {
        global $wpdb;
        self::$query_cache_table = $wpdb->prefix . self::$query_cache_table;
        //new  feature
        add_action('woocommerce_init', array($this, 'replacing_template_loop_product_thumbnail'));
        //***
        add_action('wp_ajax_woof_upload_ext', array($this, 'woof_upload_ext'));
        add_action('wp_ajax_nopriv_woof_upload_ext', array($this, 'woof_upload_ext'));

        $this->init_settings();
        if (!$this->is_should_init()) {
            $this->is_activated = false;
            return NULL;
        }
        //extensions initializating
        $this->init_extensions();

        //***

        if (isset($this->settings['storage_type'])) {
            //this option is removed from the plugin settings
            //$this->storage_type = $this->settings['storage_type'];
        }

        $this->storage = new WOOF_STORAGE($this->storage_type);

        //+++

        if (!defined('DOING_AJAX')) {
            global $wp_query;
            if (isset($wp_query->query_vars['taxonomy']) AND in_array($wp_query->query_vars['taxonomy'], get_object_taxonomies('product'))) {
                //unset($_SESSION['woof_really_current_term']);
                $this->set_really_current_term();
            }
        }

        //+++
        global $wpdb;

        /*
          //doesn matter for woo >= v.2.6.0 and will be removed in the future
          $attribute_taxonomies = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies");
          //set_transient('wc_attribute_taxonomies', $attribute_taxonomies);
          if (!empty($attribute_taxonomies) AND is_array($attribute_taxonomies)) {
          foreach ($attribute_taxonomies as $att) {
          //fixing for woo >= 2.3.2
          //https://wordpress.org/support/topic/filtering-by-attributes-stopped-working-after-update-to-232
          add_filter("woocommerce_taxonomy_args_pa_{$att->attribute_name}", array($this, 'change_woo_att_data'));
          }
          }
         */

        //add_filter("woocommerce_taxonomy_args_pa_color", array($this, 'change_woo_att_data'));
        //add_action('init', array($this, 'price_filter_init'));
        //add_action('init', array($this, 'init_marketig_woocs')); //add link to install WOOCS
        add_action('init', array($this, 'init_marketig_woof'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('widgets_init', array($this, 'widgets_init'));
        
        $this->rate_alert = new WOOF_RATE_ALERT($this->is_free_ver);
    }

    public function init() {

        if (!class_exists('WooCommerce')) {
            return;
        }

        //***
        $first_init = (int) get_option('woof_first_init', 0);
        if ($first_init != 1) {
            update_option('woof_first_init', 1);
            update_option('woof_set_automatically', 0);
            update_option('woof_autosubmit', 1);
            update_option('woof_show_count', 1);
            update_option('woof_show_count_dynamic', 0);
            update_option('woof_hide_dynamic_empty_pos', 0);
            update_option('woof_try_ajax', 0);
            update_option('woof_checkboxes_slide', 1);
            update_option('woof_hide_red_top_panel', 0);
            update_option('woof_sort_terms_checked', 0);
            update_option('woof_filter_btn_txt', '');
            update_option('woof_reset_btn_txt', '');
            //***
            $first_options = array(
                'use_chosen' => 1,
                    //'non_latin_mode' => 1//always true from v.2.2.1
            );
            update_option('woof_settings', $first_options);
            $this->settings = $first_options;
            //***
            update_option('image_default_link_type', 'file'); //http://wordpress.stackexchange.com/questions/9727/link-to-file-url-by-default
        }

        //***
        load_plugin_textdomain('woocommerce-products-filter', false, dirname(plugin_basename(__FILE__)) . '/languages');
        add_filter('plugin_action_links_' . WOOF_PLUGIN_NAME, array($this, 'plugin_action_links'), 50);
        add_action('woocommerce_settings_tabs_array', array($this, 'woocommerce_settings_tabs_array'), 50);
        add_action('woocommerce_settings_tabs_woof', array($this, 'print_plugin_options'), 50);
        //add_action('woocommerce_update_options_settings_tab_woof', array($this, 'update_settings'), 50);
        add_action('admin_head', array($this, 'admin_head'), 1);

        //+++
        //Optimize
        if (isset($this->settings['optimize_js_files']) AND $this->settings['optimize_js_files']) {
            add_action('wp_head', array($this, 'wp_head'), 999);
            add_action('wp_footer', array($this, 'wp_load_js'), 11);
        } else {
            add_action('wp_head', array($this, 'wp_head'), 999);
            add_action('wp_head', array($this, 'wp_load_js'), 999);
        }
        add_action('wp_footer', array($this, 'wp_footer'), 999);
        //+++
        add_shortcode('woof', array($this, 'woof_shortcode'));

        //+++
        add_action('wp_ajax_woof_save_options', array($this, 'woof_save_options'), 1);
        add_action('wp_ajax_woof_draw_products', array($this, 'woof_draw_products'));
        add_action('wp_ajax_nopriv_woof_draw_products', array($this, 'woof_draw_products'));
        add_action('wp_ajax_woof_redraw_woof', array($this, 'woof_redraw_woof'));
        add_action('wp_ajax_nopriv_woof_redraw_woof', array($this, 'woof_redraw_woof'));
        //+++
        add_filter('widget_text', 'do_shortcode');
        add_action('parse_query', array($this, "parse_query"), 9999);
        add_filter('woocommerce_product_query', array($this, "woocommerce_product_query"), 9999);
        add_action('body_class', array($this, 'body_class'), 9999);
        //+++
        add_action('woocommerce_before_shop_loop', array($this, 'woocommerce_before_shop_loop'), 2);
        add_action('woocommerce_after_shop_loop', array($this, 'woocommerce_after_shop_loop'));

        add_shortcode('woof_products', array($this, 'woof_products'));
        //special shortcode to get all products ids. Really it is cutted [woof_products]. USE BEFORE [woof]
        add_shortcode('woof_products_ids_prediction', array($this, 'woof_products_ids_prediction'));
        add_shortcode('woof_price_filter', array($this, 'woof_price_filter'));

        add_shortcode('woof_search_options', array($this, 'woof_search_options'));
        add_shortcode('woof_found_count', array($this, 'woof_found_count'));


        //add_filter('woocommerce_pagination_args', array($this, 'woocommerce_pagination_args'));
        add_action('wp_ajax_woof_cache_count_data_clear', array($this, 'cache_count_data_clear'));
        add_action('wp_ajax_woof_cache_terms_clear', array($this, 'woof_cache_terms_clear'));

        //***
        add_action('wp_ajax_woof_remove_ext', array($this, 'woof_remove_ext'));



        add_filter('sidebars_widgets', array($this, 'sidebars_widgets'));
        //own filters of WOOF
        add_filter('woof_modify_query_args', array($this, 'woof_modify_query_args'), 1);
        //sheduling
        if ($this->get_option('cache_count_data_auto_clean')) {
            add_action('woof_cache_count_data_auto_clean', array($this, 'cache_count_data_clear'));
            if (!wp_next_scheduled('woof_cache_count_data_auto_clean')) {
                wp_schedule_event(time(), $this->get_option('cache_count_data_auto_clean'), 'woof_cache_count_data_auto_clean');
            }
        }


        //for pagination while searching is going only
        //http://docs.woothemes.com/document/change-number-of-products-displayed-per-page/
        if ($this->get_option('per_page') > 0 AND $this->is_isset_in_request_data($this->get_swoof_search_slug())) {
            if (version_compare(PHP_VERSION, '5.3.0', '<=')) {
                add_filter('loop_shop_per_page', create_function('$cols', "return {$this->get_option('per_page')};"), 9999);
            } else {
                add_filter('loop_shop_per_page', function($cols) {
                    return $this->get_option('per_page');
                }, 9999);
            }
        }

        //cron
        add_filter('cron_schedules', array($this, 'cron_schedules'), 10, 1);
        //custom filters
        //add_filter('woof_before_term_name', array($this, 'woof_before_term_name'));
        //***
        if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
            $this->storage->set_val('woof_last_search_request', $this->get_request_data());
        }
        //sort terms. First if is checked
        if (get_option('woof_sort_terms_checked', 0)) {
            add_filter('woof_sort_terms_before_out', array($this, 'woof_sort_terms_is_checked'), 10, 2);
        }

        //compatibility with woo catalog
        add_filter('woocommerce_is_filtered', array($this, 'woocommerce_is_filtered'), 20);
        //meta filter
        add_action('woocommerce_product_query', array($this, 'woocommerce_parse_query'));
        //no found  template
        add_filter('woocommerce_locate_template', array($this, 'woof_overide_template'), 99, 3);
        add_filter('wc_get_template_part', array($this, 'woof_overide_template'), 99, 3);

        //compatibility with woo shortcode
        add_filter('woocommerce_shortcode_products_query', array($this, 'woocommerce_shortcode_products_query'), 99, 3);
        //ajax native shortcode shortcode
        $this->activate_woo_shortcodes();
        //sort options
        add_filter('woof_sort_terms_before_out', array($this, "sort_terms_before_out"), 5, 2);
        // AND OR logic
        add_filter('woof_main_query_tax_relations', array($this, 'change_query_tax_relations'), 5, 1);

        //Elemrntor compatibility to  disable  widget
//        add_action('elementor/widgets/widgets_registered', function($widgets_manager) {
//            $widgets_manager->unregister_widget_type('wp-widget-woof_widget');
//        });
        //woopd compatibility
        add_filter('woopt_get_query_args', array($this, 'woopt_set_query_args'), 5, 1);
    }

    public function admin_enqueue_scripts() {
        if (isset($_GET['tab']) AND $_GET['tab'] == 'woof') {
            /*
              if (version_compare(WOOF_VERSION, '2.1.3', '>=') OR version_compare(WOOF_VERSION, '1.1.3', '>='))
              {
              WOOF_HELPER::add_notice('woof_notice_update_213');
              }
             */
            WOOF_HELPER::hide_admin_notices();
            //***

            wp_enqueue_style('open_sans_font', 'https://fonts.googleapis.com/css?family=Open+Sans');
            wp_enqueue_style('woof', WOOF_LINK . 'css/plugin_options.css', array(), WOOF_VERSION);
            wp_enqueue_style('woof_fontello', WOOF_LINK . 'css/fontello.css', array(), WOOF_VERSION);
            wp_enqueue_script('SimpleAjaxUploader', WOOF_LINK . 'lib/simple-ajax-uploader/SimpleAjaxUploader.js', array(), WOOF_VERSION);
            wp_enqueue_script('SimpleAjaxUploader-action', WOOF_LINK . 'lib/simple-ajax-uploader/action.js', array(), WOOF_VERSION);
        }
    }

    public function woof_save_options() {

        //save options can admin only <notifications@pluginvulnerabilities.com>
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        //***

        $data = array();
        parse_str($_REQUEST['formdata'], $data);

        //if (wp_verify_nonce($data['_wpnonce']))
        {
            if (isset($data['woof_settings'])) {
                $_POST = $data; //for WC_Admin_Settings
                WC_Admin_Settings::save_fields($this->get_options());
                //+++
                if (class_exists('SitePress') OR class_exists("Polylang")) {
                    if (class_exists('SitePress')) {
                        $lang = ICL_LANGUAGE_CODE;
                    }
                    if (class_exists('Polylang')) {
                        $lang = get_locale();
                    }
                    if (isset($data['woof_settings']['wpml_tax_labels']) AND ! empty($data['woof_settings']['wpml_tax_labels'])) {
                        $translations_string = $data['woof_settings']['wpml_tax_labels'];
                        $translations_string = explode(PHP_EOL, $translations_string);
                        $translations = array();
                        if (!empty($translations_string) AND is_array($translations_string)) {
                            foreach ($translations_string as $line) {
                                if (empty($line)) {
                                    continue;
                                }

                                $line = explode(':', $line);
                                if (!isset($translations[$line[0]])) {
                                    $translations[$line[0]] = array();
                                }
                                $tmp = explode('^', $line[1]);
                                $translations[$line[0]][$tmp[0]] = $tmp[1];
                            }
                        }

                        $data['woof_settings']['wpml_tax_labels'] = $translations;
                    }
                }
                //+++
                if (is_array($data['woof_settings'])) {
                    $data['woof_settings']['default_overlay_skin_word'] = WOOF_HELPER::escape($data['woof_settings']['default_overlay_skin_word']);
                    //print_r($data['woof_settings']);

                    update_option('woof_settings', $data['woof_settings']);
                }
                wp_cache_flush();
            }
        }

        die('done');
    }

    public function print_plugin_options() {

        wp_enqueue_script('media-upload');
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_enqueue_script('woof_modernizr', WOOF_LINK . 'js/modernizr.js', array(), WOOF_VERSION);
        wp_enqueue_script('woof', WOOF_LINK . 'js/plugin_options.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), WOOF_VERSION);

        //*** for woocommerce >= v.2.6.0
        //to avoid https://products-filter.com/filtering-by-product-attribute-doesn-work/
        global $wpdb;
        $data_sql = array(
            array(
                'type' => 'int',
                'val' => 1,
            )
        );
        $wpdb->query(WOOF_HELPER::woof_prepare("UPDATE {$wpdb->prefix}woocommerce_attribute_taxonomies SET attribute_public=%d", $data_sql));
        flush_rewrite_rules();
        delete_transient('wc_attribute_taxonomies');

        //***

        $args = array(
            "woof_settings" => $this->settings,
            "extensions" => $this->get_ext_directories()
        );

        if (is_admin()) {            
            $this->rate_alert->show_alert();
        }

        echo $this->render_html(WOOF_PATH . 'views/plugin_options.php', $args);
    }

    public function enqueue_scripts_styles() {
        //enqueue styles
        if (isset($this->settings['custom_front_css']) AND ! empty($this->settings['custom_front_css'])) {
            wp_enqueue_style('woof', $this->settings['custom_front_css']);
        } else {
            wp_enqueue_style('woof', WOOF_LINK . 'css/front.css', array(), WOOF_VERSION);
        }

        //***

        if ($this->is_woof_use_chosen()) {
            wp_enqueue_style('chosen-drop-down', WOOF_LINK . 'js/chosen/chosen.min.css', array(), WOOF_VERSION);
        }

        if (isset($this->settings['overlay_skin']) AND $this->settings['overlay_skin'] != 'default') {
            wp_enqueue_style('plainoverlay', WOOF_LINK . 'css/plainoverlay.css', array(), WOOF_VERSION);
        }

        if ($this->get_option('use_beauty_scroll', 0)) {
            wp_enqueue_style('malihu-custom-scrollbar', WOOF_LINK . 'js/malihu-custom-scrollbar/jquery.mCustomScrollbar.css', array(), WOOF_VERSION);
        }

        $icheck_skin = 'none';
        if (isset($this->settings['icheck_skin'])) {
            $icheck_skin = $this->settings['icheck_skin'];
        }
        if ($icheck_skin != 'none') {
            if (!$icheck_skin) {
                $icheck_skin = 'square_green';
            }

            if ($icheck_skin != 'none') {
                $icheck_skin = explode('_', $icheck_skin);
                wp_enqueue_style('icheck-jquery-color', WOOF_LINK . 'js/icheck/skins/' . $icheck_skin[0] . '/' . $icheck_skin[1] . '.css', array(), WOOF_VERSION);
            }
        }

        //***
        //for extensions
        if (!empty(WOOF_EXT::$includes['css'])) {
            foreach (WOOF_EXT::$includes['css'] as $css_key => $css_link) {
                wp_enqueue_style($css_key, $css_link, array(), WOOF_VERSION);
            }
        }
    }

    public function body_class($classes) {
        if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
            $classes[] = 'woof_search_is_going';
        }

        return $classes;
    }

    //compatibility  with woo catalog
    public function woocommerce_is_filtered($is_filtered) {
        if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
            $is_filtered = true;
        }

        return $is_filtered;
    }

    public function widgets_init() {
        if ($this->is_should_init()) {
            register_widget('WOOF_Widget');
        } else {
            $this->is_activated = false;
        }
    }

    //fix for woo 2.3.2 and higher with attributes filtering
    public function change_woo_att_data($taxonomy_data) {
        $taxonomy_data['query_var'] = true;
        return $taxonomy_data;
    }

    public function sidebars_widgets($sidebars_widgets) {
        $price_filter = 0;
        if (isset($this->settings['by_price']['show'])) {
            $price_filter = (int) $this->settings['by_price']['show'];
        }

        if ($price_filter) {
            $sidebars_widgets['sidebar-woof'] = array('woocommerce_price_filter');
        }

        return $sidebars_widgets;
    }

    public function cron_schedules($schedules) {
        //$schedules stores all recurrence schedules within WordPress
        for ($i = 2; $i <= 7; $i++) {
            //https://wordpress.org/support/topic/cron-schedules-break-due-to-missing-check-for-isset?replies=1
            /*
              if (!isset($schedules['days' . $i]))
              {
              continue;
              }
             */
            //***

            $schedules['days' . $i] = array(
                'interval' => $i * DAY_IN_SECONDS,
                'display' => sprintf(__("each %s days", 'woocommerce-products-filter'), $i)
            );
        }

        return (array) $schedules;
    }

    /**
     * Show action links on the plugin screen
     */
    public function plugin_action_links($links) {
        return array_merge(array(
            '<a href="' . admin_url('admin.php?page=wc-settings&tab=woof') . '">' . __('Settings', 'woocommerce-products-filter') . '</a>',
            '<a target="_blank" href="' . esc_url('https://products-filter.com/') . '">' . __('Documentation', 'woocommerce-products-filter') . '</a>'
                ), $links);
    }

    public function get_swoof_search_slug() {
        $slug = 'swoof';

        if (isset($this->settings['swoof_search_slug']) AND ! empty($this->settings['swoof_search_slug'])) {
            $slug = $this->settings['swoof_search_slug'];
        }

        return $slug;
    }

    public function woocommerce_product_query($q) {
        //http://docs.woothemes.com/wc-apidocs/class-WC_Query.html
        //wp-content\plugins\woocommerce\includes\class-wc-query.php -> public function product_query( $q )
        $meta_query = $q->get('meta_query');

        //for extensions
        if (!empty(WOOF_EXT::$includes['html_type_objects'])) {
            foreach (WOOF_EXT::$includes['html_type_objects'] as $obj) {
                if (method_exists($obj, 'assemble_query_params')) {
                    $q->set('meta_query', $obj->assemble_query_params($meta_query, $q));
                }
            }
        }


        return $q;
    }

    function woocommerce_parse_query($q) {
        $meta_query = $q->get('meta_query');
        $meta_query = apply_filters('woof_get_meta_query', $meta_query);

        $q->set('meta_query', $meta_query);

        $tax_query = $q->get('tax_query');
        $tax_query = $this->parse_tax_query($tax_query);

        $q->set('tax_query', $tax_query);
    }

    public function parse_query($wp_query) {
        //on single page works [woof_products] shortcode, so we doesn need parse query there!!!
        if (!defined('DOING_AJAX')) {
            /*
              if ((is_page() AND ! is_shop()))
              {
              return $wp_query;
              }
             */
        }
        //***
        $_REQUEST['woof_parse_query'] = 1;

        if (!defined('DOING_AJAX')) {
            if (isset($_REQUEST['woof_products_doing'])) {
                return $wp_query;
            }
        }

        $request = $this->get_request_data();


        //+++
        if ($wp_query->is_main_query()) {
            if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {

                if (!isset($wp_query->query['post_type']) OR $wp_query->query['post_type'] != 'product') {
                    global $wp;
                    if (home_url($wp->request) != home_url()) {
                        // return $wp_query;
                    }
                }

                //var_dump($wp_query->is_tax);
                if (!empty($wp_query->tax_query) AND isset($wp_query->tax_query->queries)) {
                    /*
                      $relations = array(
                      'product_cat' => 'AND'
                      );
                     *
                     */

                    $tax_relations = apply_filters('woof_main_query_tax_relations', array());


                    if (!empty($tax_relations)) {

                        $tax_query = $wp_query->tax_query->queries;
                        foreach ($tax_query as $key => $value) {
                            if (in_array($value['taxonomy'], array_keys($tax_relations))) {
                                if (count($tax_query[$key]['terms'])) {
                                    $tax_query[$key]['operator'] = $tax_relations[$value['taxonomy']];
                                    $tax_query[$key]['include_children'] = 0;
                                }
                            }
                        }


                        // fix visibility
                        if (version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
                            // $tax_query = $this->listen_catalog_visibility($tax_query, true);
                            // $tax_query = $this->product_visibility_not_in($tax_query, $this->generate_visibility_keys(true));
                            $this->product_visibility_for_parse_query();
                        }
                        // end fix

                        $wp_query->set('tax_query', $tax_query);
                    }
                }

                //***
                $disable_swoof_influence = false;
                if (isset($this->settings['disable_swoof_influence'])) {
                    $disable_swoof_influence = (bool) $this->settings['disable_swoof_influence'];
                }

                if (!$disable_swoof_influence) {
                    if (!is_page()) {
                        $wp_query->set('post_type', 'product');
                        $wp_query->is_post_type_archive = true;
                    }

                    $wp_query->is_tax = false;
                    $wp_query->is_tag = false;

                    $wp_query->is_home = false;
                    $wp_query->is_single = false;
                    $wp_query->is_posts_page = false;
                    $wp_query->is_search = false; //!!!
                } else {
                    //enable it if its really will help to your site filtering - for support
                    //$wp_query->is_search = true;
                }

                //+++
                $meta_query = array();

                if (isset($wp_query->query_vars['meta_query'])) {
                    $meta_query = $wp_query->query_vars['meta_query'];
                }

                //+++
                //for extensions
                if (!empty(WOOF_EXT::$includes['html_type_objects'])) {
                    foreach (WOOF_EXT::$includes['html_type_objects'] as $obj) {
                        if (method_exists($obj, 'assemble_query_params')) {
                            //lock adding meta params for single page while shortcode start to work when searching is going
                            if (is_page()) {
                                if (!isset($_REQUEST['woof_products_doing'])) {
                                    // For meta filter bone
                                    //$meta_query = apply_filters('woof_get_meta_query', $meta_query);
                                    $wp_query->set('meta_query', $meta_query);
                                    return $wp_query;
                                }
                            }
                            $obj->assemble_query_params($meta_query, $wp_query);
                        }
                    }
                }

                //***
                //fix visibility
                if (version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) {
                    $meta_query = $this->listen_catalog_visibility($meta_query, true);
                }
                //***
                // For meta filter
                //$meta_query = apply_filters('woof_get_meta_query', $meta_query);
                $wp_query->set('meta_query', $meta_query);
                //compatibility for woocommerce-products-per-page
                if (class_exists('Woocommerce_Products_Per_Page')) {
                    $wp_query->set('posts_per_page', $this->get_wppp_per_page());
                }
            }
        }

        return $wp_query;
    }

    private function assemble_price_params(&$meta_query) {
        $request = $this->get_request_data();
        if (isset($request['min_price']) AND isset($request['max_price'])) {

            if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
                $tax_classes = array_merge(array(''), WC_Tax::get_tax_classes());
                $class_min = $request['min_price'];
                foreach ($tax_classes as $tax_class) {
                    if ($tax_rates = WC_Tax::get_rates($tax_class)) {
                        $class_min = $request['min_price'] - WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($request['min_price'], $tax_rates));
                    }
                }
                $request['min_price'] = $class_min;
            }
            if ($request['min_price'] <= $request['max_price']) {
                $meta_query[] = array(
                    'key' => '_price',
                    'value' => array(floatval($request['min_price']), floatval($request['max_price'])),
                    'type' => 'DECIMAL',
                    'compare' => 'BETWEEN'
                );
            }
        }

        return $meta_query;
    }

    public function woocommerce_settings_tabs_array($tabs) {
        $tabs['woof'] = __('Products Filter', 'woocommerce-products-filter');
        return $tabs;
    }

    public function admin_head() {
        if (isset($_GET['tab']) AND $_GET['tab'] == 'woof') {
            ?>
            <script type="text/javascript">
                var woof_save_link = "<?php echo admin_url('admin.php?page=wc-settings&tab=woof&settings_saved=1'); ?>";
                var woof_lang_saving = "<?php _e('WOOF settings saving ...', 'woocommerce-products-filter') ?>";
            </script>
            <?php
        }
    }

    public function wp_head() {
        global $wp_query;
        //***
        //var_dump(get_queried_object());
        $request_data = $this->get_request_data();
        if (!isset($wp_query->query_vars['taxonomy']) AND ! defined('DOING_AJAX')) {
            // $this->set_really_current_term(/* get_queried_object() */);
        }
        //***
        ?>
        <script type="text/javascript">
            var woof_is_permalink =<?php echo intval((bool) $this->is_permalink_activated()) ?>;

            var woof_shop_page = "";
        <?php if (!$this->is_permalink_activated()): ?>
                woof_shop_page = "<?php echo home_url('/?post_type=product'); ?>";
        <?php endif; ?>

            var woof_really_curr_tax = {};
            var woof_current_page_link = location.protocol + '//' + location.host + location.pathname;
            /*lets remove pagination from woof_current_page_link*/
            woof_current_page_link = woof_current_page_link.replace(/\page\/[0-9]+/, "");
        <?php
        if (!isset($wp_query->query_vars['taxonomy'])) {
            $page_id = get_option('woocommerce_shop_page_id');
            if ($page_id > 0) {
                if (!$this->is_permalink_activated()) {
                    $link = home_url('/?post_type=product');
                } else {
                    $link = get_permalink($page_id);
                }
            }

            if (isset($link) AND ! empty($link) AND is_string($link)) {
                ?>
                    woof_current_page_link = "<?php echo $link ?>";
                <?php
            }
        }


/*code bone when filter child categories on the category page of parent*/
/*like here: http://demo.pluginus.net/product-category/clothing/?swoof=1&product_cat=hoo1*/

        ?>
            var woof_link = '<?php echo WOOF_LINK ?>';

        <?php
        if (!defined('DOING_AJAX') AND ! is_page()) {
            $queried_obj = get_queried_object();
            if (isset($wp_query->query_vars['taxonomy']) AND is_object($queried_obj) AND get_class(get_queried_object()) == 'WP_Term' AND ! isset($request_data['really_curr_tax'])) {

                if (is_object($queried_obj)) {
                    //$_SESSION['woof_really_current_term'] = $queried_obj;
                    $this->set_really_current_term($queried_obj);
                    ?>
                        woof_really_curr_tax = {term_id:<?php echo intval($queried_obj->term_id) ?>, taxonomy: "<?php echo $queried_obj->taxonomy ?>"};
                    <?php
                }
            } elseif (isset($request_data['really_curr_tax'])) {
                $tmp = explode('-', $request_data['really_curr_tax'], 2);
                $res = get_term($tmp[0], $tmp[1]);
                $this->set_really_current_term($res);
                ?>
                    woof_really_curr_tax = {term_id:<?php echo intval($res->term_id) ?>, taxonomy: "<?php echo $res->taxonomy ?>"};
                <?php
            } else {
                $this->set_really_current_term();
            }
        } else {
            if ($this->is_really_current_term_exists()) {
                //unset($_SESSION['woof_really_current_term']);

                $this->set_really_current_term();
            }
        }

//***
        ?>
        </script>

        <?php
        //var_dump($this->get_really_current_term());
    }

    public function wp_load_js() {
        global $is_edge, $is_gecko;
        ?>
        <script>

            var woof_ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

            var woof_lang = {
                'orderby': "<?php _e('orderby', 'woocommerce-products-filter') ?>",
                'date': "<?php _e('date', 'woocommerce-products-filter') ?>",
                'perpage': "<?php _e('per page', 'woocommerce-products-filter') ?>",
                'pricerange': "<?php _e('price range', 'woocommerce-products-filter') ?>",
                'menu_order': "<?php _e('menu order', 'woocommerce-products-filter') ?>",
                'popularity': "<?php _e('popularity', 'woocommerce-products-filter') ?>",
                'rating': "<?php _e('rating', 'woocommerce-products-filter') ?>",
                'price': "<?php _e('price low to high', 'woocommerce-products-filter') ?>",
                'price-desc': "<?php _e('price high to low', 'woocommerce-products-filter') ?>"
            };

            if (typeof woof_lang_custom == 'undefined') {
                var woof_lang_custom = {};/*!!important*/
            }


            var woof_is_mobile = 0;
        <?php if (WOOF_HELPER::is_mobile_device()): ?>
                woof_is_mobile = 1;
        <?php endif; ?>



            var woof_show_price_search_button = 0;
            var woof_show_price_search_type = 0;
        <?php
        $show_price_search_button = 0;
        $show_price_search_type = 0;
        if (isset($this->settings['by_price']['show_button'])) {
            $show_price_search_button = (int) $this->settings['by_price']['show_button'];
        }

        if (isset($this->settings['by_price']['show'])) {
            $show_price_search_type = (int) $this->settings['by_price']['show'];
        }

        if ($show_price_search_button == 1):
            ?>
                woof_show_price_search_button = 1;
        <?php endif; ?>

            var woof_show_price_search_type = <?php echo $show_price_search_type ?>;

            var swoof_search_slug = "<?php echo $this->get_swoof_search_slug(); ?>";

        <?php
        $icheck_skin = 'none';
        if (isset($this->settings['icheck_skin'])) {
            $icheck_skin = $this->settings['icheck_skin'];
        }
        ?>

            var icheck_skin = {};
        <?php if ($icheck_skin != 'none'): ?>
            <?php $icheck_skin = explode('_', $icheck_skin); ?>
                icheck_skin.skin = "<?php echo $icheck_skin[0] ?>";
                icheck_skin.color = "<?php echo $icheck_skin[1] ?>";
                if (window.navigator.msPointerEnabled && navigator.msMaxTouchPoints > 0) {
                    //icheck_skin = 'none';
                }
        <?php else: ?>
                icheck_skin = 'none';
        <?php endif; ?>

            var is_woof_use_chosen =<?php echo intval($this->is_woof_use_chosen()); ?>;

        <?php $woof_use_beauty_scroll = $this->get_option('use_beauty_scroll', 0); ?>


            var woof_current_values = '<?php echo json_encode($this->get_request_data()); ?>';

            var woof_lang_loading = "<?php _e('Loading ...', 'woocommerce-products-filter') ?>";

        <?php if (isset($this->settings['default_overlay_skin_word']) AND ! empty($this->settings['default_overlay_skin_word'])): ?>
                woof_lang_loading = "<?php echo __($this->settings['default_overlay_skin_word'], 'woocommerce-products-filter') ?>";
        <?php endif; ?>

            var woof_lang_show_products_filter = "<?php _e('show products filter', 'woocommerce-products-filter') ?>";
            var woof_lang_hide_products_filter = "<?php _e('hide products filter', 'woocommerce-products-filter') ?>";
            var woof_lang_pricerange = "<?php _e('price range', 'woocommerce-products-filter') ?>";

            var woof_use_beauty_scroll =<?php echo $woof_use_beauty_scroll ?>;

            var woof_autosubmit =<?php echo (int) get_option('woof_autosubmit', 0) ?>;
            var woof_ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            /*var woof_submit_link = "";*/
            var woof_is_ajax = 0;
            var woof_ajax_redraw = 0;
            var woof_ajax_page_num =<?php echo ((get_query_var('page')) ? get_query_var('page') : 1) ?>;
            var woof_ajax_first_done = false;
            var woof_checkboxes_slide_flag = <?php echo(((int) get_option('woof_checkboxes_slide') == 1 ? 'true' : 'false')); ?>;


            /*toggles*/
            var woof_toggle_type = "<?php _e((isset($this->settings['toggle_type']) AND ! empty($this->settings['toggle_type'])) ? $this->settings['toggle_type'] : 'text') ?>";

            var woof_toggle_closed_text = "<?php _e((isset($this->settings['toggle_closed_text']) AND ! empty($this->settings['toggle_closed_text'])) ? trim(WOOF_HELPER::wpml_translate(null, $this->settings['toggle_closed_text'])) : '-') ?>";
            var woof_toggle_opened_text = "<?php _e((isset($this->settings['toggle_opened_text']) AND ! empty($this->settings['toggle_opened_text'])) ? trim(WOOF_HELPER::wpml_translate(null, $this->settings['toggle_opened_text'])) : '+') ?>";

            var woof_toggle_closed_image = "<?php _e((isset($this->settings['toggle_closed_image']) AND ! empty($this->settings['toggle_closed_image'])) ? $this->settings['toggle_closed_image'] : WOOF_LINK . 'img/plus3.png') ?>";
            var woof_toggle_opened_image = "<?php _e((isset($this->settings['toggle_opened_image']) AND ! empty($this->settings['toggle_opened_image'])) ? $this->settings['toggle_opened_image'] : WOOF_LINK . 'img/minus3.png') ?>";


            /*indexes which can be displayed in red buttons panel*/
        <?php
        $taxonomies = $this->get_taxonomies();
        $taxonomies_keys = array_keys($taxonomies);
        if (version_compare(PHP_VERSION, '5.3.0', '<=')) {
            array_walk($taxonomies_keys, create_function('&$str', '$str = "\"$str\"";'));
        } else {
            array_walk($taxonomies_keys, function(&$str) {
                $str = "\"$str\"";
            });
        }
        $taxonomies_keys = implode(',', $taxonomies_keys);
        $extensions_html_type_indexes = array();

        if (!empty(WOOF_EXT::$includes['html_type_objects'])) {
            foreach (WOOF_EXT::$includes['html_type_objects'] as $obj) {
                if ($obj->index !== NULL) {
                    $extensions_html_type_indexes[] = $obj->index;
                }
            }
        }
        $extensions_html_type_indexes[] = "min_rating";
        if (version_compare(PHP_VERSION, '5.3.0', '<=')) {
            array_walk($extensions_html_type_indexes, create_function('&$str', '$str = "\"$str\"";'));
        } else {
            array_walk($extensions_html_type_indexes, function(&$str) {
                $str = "\"$str\"";
            });
        }


        $extensions_html_type_indexes = implode(',', apply_filters('woof_extensions_type_index', $extensions_html_type_indexes));
        ?>
            var woof_accept_array = ["min_price", "orderby", "perpage", <?php echo $extensions_html_type_indexes ?>,<?php echo $taxonomies_keys ?>];

        <?php if (isset($request_data['really_curr_tax'])): ?>
            <?php
            $tmp = explode('-', $request_data['really_curr_tax']);
            ?>
                woof_really_curr_tax = {term_id:<?php echo intval($tmp[0]) ?>, taxonomy: "<?php echo WOOF_HELPER::escape($tmp[1]) ?>"};
        <?php endif; ?>
            /*for extensions*/
            var woof_ext_init_functions = null;
        <?php if (!empty(WOOF_EXT::$includes['js_init_functions'])) : ?>
                woof_ext_init_functions = '<?php echo json_encode(WOOF_EXT::$includes['js_init_functions']); ?>';

        <?php endif; ?>


        <?php
        if ($is_gecko AND (int) get_option('woof_try_ajax', 0) === 0 AND isset($this->settings['overlay_skin']) AND $this->settings['overlay_skin'] != 'default') {
            $this->settings['overlay_skin'] = 'plainoverlay';  //Compatibility SVG with firefox
        }
        ?>

            var woof_overlay_skin = "<?php echo(isset($this->settings['overlay_skin']) ? $this->settings['overlay_skin'] : 'default') ?>";

            jQuery(function () {
                try
                {
                    woof_current_values = jQuery.parseJSON(woof_current_values);
                } catch (e)
                {
                    woof_current_values = null;
                }
                if (woof_current_values == null || woof_current_values.length == 0) {
                    woof_current_values = {};
                }

            });

            function woof_js_after_ajax_done() {
                jQuery(document).trigger('woof_ajax_done');
        <?php echo(isset($this->settings['js_after_ajax_done']) ? stripcslashes($this->settings['js_after_ajax_done']) : ''); ?>
            }
        </script>
        <?php
        //tooltip

        if (!isset($this->settings['use_tooltip'])) {
            $show_tooltip = 1;
        } else {
            $show_tooltip = $this->settings['use_tooltip'];
        }
        if ($show_tooltip) {
            wp_enqueue_style('woof_tooltip-css', WOOF_LINK . 'js/tooltip/css/tooltipster.bundle.min.css', array(), WOOF_VERSION);
            wp_enqueue_style('woof_tooltip-css-noir', WOOF_LINK . 'js/tooltip/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-noir.min.css', array(), WOOF_VERSION);
            wp_enqueue_script('woof_tooltip-js', WOOF_LINK . 'js/tooltip/js/tooltipster.bundle.min.js', array('jquery'), WOOF_VERSION);
        }
        if ($icheck_skin != 'none') {
            wp_enqueue_script('icheck-jquery', WOOF_LINK . 'js/icheck/icheck.min.js', array('jquery'), WOOF_VERSION);
            //wp_enqueue_style('icheck-jquery', self::get_application_uri() . 'js/icheck/all.css');
        }
        /*
          if (is_shop())
          {
          add_action('woocommerce_before_shop_loop', array($this, 'woocommerce_before_shop_loop'));
          }
         */
        //***
        //wp_enqueue_script('md5', WOOF_LINK . 'js/jquery.md5.js', array('jquery'));
        //wp_enqueue_script('masonry', WOOF_LINK . 'js/jquery.masonry.min.js', array('jquery'));
        if (isset($this->settings['optimize_js_files']) AND $this->settings['optimize_js_files']) {
            wp_enqueue_script('woof_front', WOOF_LINK . 'js/front_comprssd.js', array(), WOOF_VERSION);
        } else {
            wp_enqueue_script('woof_front', WOOF_LINK . 'js/front.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_script('woof_radio_html_items', WOOF_LINK . 'js/html_types/radio.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_script('woof_checkbox_html_items', WOOF_LINK . 'js/html_types/checkbox.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_script('woof_select_html_items', WOOF_LINK . 'js/html_types/select.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_script('woof_mselect_html_items', WOOF_LINK . 'js/html_types/mselect.js', array('jquery'), WOOF_VERSION);
        }


        //for extensions
        if (!empty(WOOF_EXT::$includes['js'])) {
            foreach (WOOF_EXT::$includes['js'] as $js_key => $js_link) {
                wp_enqueue_script($js_key, $js_link, array('jquery'), WOOF_VERSION);
            }
        }



        //+++
        if ($this->is_woof_use_chosen()) {
            wp_enqueue_script('chosen-drop-down', WOOF_LINK . 'js/chosen/chosen.jquery.min.js', array('jquery'), WOOF_VERSION);
        }

        if (isset($this->settings['overlay_skin']) AND $this->settings['overlay_skin'] != 'default') {
            wp_enqueue_script('plainoverlay', WOOF_LINK . 'js/plainoverlay/jquery.plainoverlay.min.js', array('jquery'), WOOF_VERSION);
        }

        if ($woof_use_beauty_scroll) {
            wp_enqueue_script('mousewheel', WOOF_LINK . 'js/malihu-custom-scrollbar/jquery.mousewheel.min.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_script('malihu-custom-scrollbar', WOOF_LINK . 'js/malihu-custom-scrollbar/jquery.mCustomScrollbar.min.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_script('malihu-custom-scrollbar-concat', WOOF_LINK . 'js/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js', array('jquery'), WOOF_VERSION);
        }

        $price_filter = 0;
        if (isset($this->settings['by_price']['show'])) {
            $price_filter = (int) $this->settings['by_price']['show'];
        }

        if ($price_filter == 1) {
            wp_enqueue_script('jquery-ui-core', array('jquery'));
            wp_enqueue_script('jquery-ui-slider', array('jquery-ui-core'));
            wp_enqueue_script('wc-jquery-ui-touchpunch', array('jquery-ui-core', 'jquery-ui-slider'));
            wp_enqueue_script('wc-price-slider', array('jquery-ui-slider', 'wc-jquery-ui-touchpunch'));
        }
    }

    public function wp_footer() {
        ?>

        <?php //if (isset($this->settings['custom_css_code'])):                                                                         ?>
        <style type="text/css">
        <?php
        if (isset($this->settings['custom_css_code'])) {
            echo stripcslashes($this->settings['custom_css_code']);
        }
        ?>

        <?php
        if (isset($this->settings['overlay_skin_bg_img'])) {
            if (!empty($this->settings['overlay_skin_bg_img'])) {
                ?>
                    .plainoverlay {
                        background-image: url('<?php echo $this->settings['overlay_skin_bg_img'] ?>');
                    }
                <?php
            }
        }



//***
        if (isset($this->settings['plainoverlay_color'])) {
            if (!empty($this->settings['plainoverlay_color'])) {
                ?>
                    .jQuery-plainOverlay-progress {
                        border-top: 12px solid <?php echo $this->settings['plainoverlay_color'] ?> !important;
                    }
                <?php
            }
        }


//***

        if (isset($this->settings['woof_auto_subcats_plus_img'])) {
            if (!empty($this->settings['woof_auto_subcats_plus_img'])) {
                ?>
                    .woof_childs_list_opener span.woof_is_closed{
                        background: url(<?php echo $this->settings['woof_auto_subcats_plus_img'] ?>);
                    }
                <?php
            }
        }

        if (isset($this->settings['woof_auto_subcats_minus_img'])) {
            if (!empty($this->settings['woof_auto_subcats_minus_img'])) {
                ?>
                    .woof_childs_list_opener span.woof_is_opened{
                        background: url(<?php echo $this->settings['woof_auto_subcats_minus_img'] ?>);
                    }
                <?php
            }
        }
        ?>



        <?php
        $show_price_search_button = 0;
        if (isset($this->settings['by_price']['show_button'])) {
            $show_price_search_button = (int) $this->settings['by_price']['show_button'];
        }
        if (isset($this->settings['by_price']['show']) AND (int) $this->settings['by_price']['show'] == 1):
            if ($show_price_search_button == 1):
                ?>


            <?php else: ?>


                    /***** START: hiding submit button of the price slider ******/
                    .woof_price_search_container .price_slider_amount button.button{
                        display: none;
                    }

                    .woof_price_search_container .price_slider_amount .price_label{
                        text-align: left !important;
                    }

                    .woof .widget_price_filter .price_slider_amount .button {
                        float: left;
                    }

                    /***** END: hiding submit button of the price slider ******/


            <?php endif; ?>
        <?php endif; ?>




        <?php if (!current_user_can('create_users')): ?>
                .woof_edit_view{
                    display: none;
                }
        <?php endif; ?>

        </style>
        <?php //endif;        ?>


        <?php
        if (isset($this->settings['overlay_skin']) AND ( $this->settings['overlay_skin'] != 'default' AND $this->settings['overlay_skin'] != 'plainoverlay')) {
            ?>

            <img  style="display: none;" src="<?php echo WOOF_LINK ?>img/loading-master/<?php echo $this->settings['overlay_skin'] ?>.svg" alt="preloader" />

            <?php
        }
    }

    private function init_settings() {
        $this->settings = get_option('woof_settings', array());
    }

    public function get_taxonomies() {
        static $taxonomies = array();
        if (empty($taxonomies)) {
            $taxonomies = get_object_taxonomies('product', 'objects');
            unset($taxonomies['product_shipping_class']);
            unset($taxonomies['product_type']);
        }
        return $taxonomies;
    }

    public function get_options() {
        $options = array
            (array(
                'name' => '',
                'type' => 'title',
                'desc' => '',
                'id' => 'woof_general_settings'
            ),
            array(
                'name' => __('Set filter automatically', 'woocommerce-products-filter'),
                'desc' => __('Set filter automatically on the shop page', 'woocommerce-products-filter'),
                'id' => 'woof_set_automatically',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    1 => __('Yes', 'woocommerce-products-filter'),
                    2 => __('Yes, but only for mobile devices', 'woocommerce-products-filter'),
                    3 => __('Yes, but only for desktop', 'woocommerce-products-filter'),
                    0 => __('No', 'woocommerce-products-filter'),
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Autosubmit', 'woocommerce-products-filter'),
                'desc' => __('Start searching just after changing any of the elements on the search form', 'woocommerce-products-filter'),
                'id' => 'woof_autosubmit',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Show count', 'woocommerce-products-filter'),
                'desc' => __('Show count of items near taxonomies terms on the front', 'woocommerce-products-filter'),
                'id' => 'woof_show_count',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Dynamic recount', 'woocommerce-products-filter'),
                'desc' => __('Show count of items near taxonomies terms on the front dynamically. Must be switched on "Show count". In turbo mode if filter is very big better select "Yes, only for PC"', 'woocommerce-products-filter'),
                'id' => 'woof_show_count_dynamic',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Hide empty terms', 'woocommerce-products-filter'),
                'desc' => __('Hide empty terms in "Dynamic recount" mode', 'woocommerce-products-filter'),
                'id' => 'woof_hide_dynamic_empty_pos',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Try to ajaxify the shop', 'woocommerce-products-filter'),
                'desc' => __('Select "Yes" if you want to TRY make filtering in your shop by AJAX. Not compatible for 100% of all wp themes, so test it well if you are going to buy premium version of the plugin because incompatibility is not fixable!', 'woocommerce-products-filter'),
                'id' => 'woof_try_ajax',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Hide childs in checkboxes and radio', 'woocommerce-products-filter'),
                'desc' => __('Hide childs in checkboxes and radio. Near checkbox/radio which has childs will be plus icon to show childs.', 'woocommerce-products-filter'),
                'id' => 'woof_checkboxes_slide',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Hide woof top panel buttons', 'woocommerce-products-filter'),
                'desc' => __('Red buttons on the top of the shop page when searching done', 'woocommerce-products-filter'),
                'id' => 'woof_hide_red_top_panel',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Lets checked terms will be on the top', 'woocommerce-products-filter'),
                'desc' => __('Selected terms will always be displayed on the top (for parent-terms only, child will be on the top but under parent-term as it was)', 'woocommerce-products-filter'),
                'id' => 'woof_sort_terms_checked',
                'type' => 'select',
                'class' => 'chosen_select',
                'css' => 'min-width:300px;',
                'options' => array(
                    0 => __('No', 'woocommerce-products-filter'),
                    1 => __('Yes', 'woocommerce-products-filter')
                ),
                'desc_tip' => true
            ),
            array(
                'name' => __('Filter button text', 'woocommerce-products-filter'),
                'desc' => __('Filter button text in the search form', 'woocommerce-products-filter'),
                'id' => 'woof_filter_btn_txt',
                'type' => 'text',
                'class' => 'text',
                'css' => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'name' => __('Reset button text', 'woocommerce-products-filter'),
                'desc' => __('Reset button text in the search form. Write "none" to hide this button on front.', 'woocommerce-products-filter'),
                'id' => 'woof_reset_btn_txt',
                'type' => 'text',
                'class' => 'text',
                'css' => 'min-width:300px;',
                'desc_tip' => true
            ),
            array('type' => 'sectionend', 'id' => 'woof_general_settings')
        );

        return apply_filters('wc_settings_tab_woof_settings', $options);
    }

    //for dynamic count
    //$type - none,single,multi
    public function dynamic_count($curr_term, $type, $additional_taxes = '', $meta_term = array()) {
        //global $wp_query;
        $request = $this->get_request_data();
        $_REQUEST['woof_current_recount'] = $curr_term;
        $opposition_terms = array();
        global $wp_query;
        //+++
        if (!is_array($curr_term)) {
            $curr_term = array();
        }
        if (!isset($curr_term['taxonomy'])) {
            $curr_term['taxonomy'] = "";
        }
        //+++
        if (!empty($additional_taxes)) {
            $opposition_terms = $this->_expand_additional_taxes_string($additional_taxes);
        }
        if (!empty($opposition_terms)) {
            $tmp = array();
            foreach ($opposition_terms as $t) {
                $tmp[$t['taxonomy']] = $t['terms'];
            }
            $opposition_terms = $tmp;
            unset($tmp);
        }

        //***
        if ($this->is_really_current_term_exists()) {
            //we need this when for dynamic recount on taxonomy page
            $o = $this->get_really_current_term();
            $opposition_terms[$o->taxonomy] = array($o->slug);
        }
        //$opposition_terms - all terms from $additional_taxes or/and from really_current_term
        //it is always in opposition
        $in_query_terms = array(); //terms from request
        static $product_taxonomies = null;
        if (!$product_taxonomies) {
            $product_taxonomies = $this->get_taxonomies();
            $product_taxonomies = array_keys($product_taxonomies);
        }
        if (!empty($request) AND is_array($request)) {
            foreach ($request as $tax_slug => $terms_string) {
                $tax_slug_t = $this->uncheck_slug($tax_slug);
                if ($tax_slug_t != $tax_slug) {
                    $request[$tax_slug_t] = $terms_string;
                    unset($request[$tax_slug]);
                    $tax_slug = $tax_slug_t;
                }

                if (in_array($tax_slug, $product_taxonomies)) {
                    $in_query_terms[$tax_slug] = explode(',', $terms_string);
                }
            }
        }


        //$in_query_terms - terms we have in search query!!
        //***

        $term_is_in_query = false;
        if (empty($meta_term)) {
            if ($curr_term AND isset($in_query_terms[$curr_term['taxonomy']])) {
                if (in_array($curr_term['slug'], $in_query_terms[$curr_term['taxonomy']])) {
                    $term_is_in_query = true;
                }
            }
        }

        //any way we not display count for the selected terms
        if ($term_is_in_query) {
            return 0;
        }

        //***

        $term_is_in_opposition = false;
        if (empty($meta_term)) {
            if ($curr_term AND isset($opposition_terms[$curr_term['taxonomy']])) {
                if (in_array($curr_term['slug'], $opposition_terms[$curr_term['taxonomy']])) {
                    $term_is_in_opposition = true;
                }
            }
        }
        //***

        $terms_to_query = array();
        if (empty($meta_term)) {
            $default_types = array('radio', 'select', 'price2', 'checkbox', 'mselect');
            //for extensions
            if (!in_array($type, $default_types)) {
                if (isset(WOOF_EXT::$includes['taxonomy_type_objects'][$type])) {
                    $obj = WOOF_EXT::$includes['taxonomy_type_objects'][$type];
                    $type = $obj->html_type_dynamic_recount_behavior;
                }
            }
            //***
            //price2 -> 'none' (default)
            //radio, select -> 'single'
            //checkbox, mselect -> 'multi'
            switch ($type) {
                case 'single':

                    if (isset($in_query_terms[$curr_term['taxonomy']])) {
                        $in_query_terms[$curr_term['taxonomy']] = array($curr_term['slug']);
                    } else {
                        $terms_to_query[$curr_term['taxonomy']] = array($curr_term['slug']);
                    }


                    break;

                case 'multi':

                    if (isset($in_query_terms[$curr_term['taxonomy']])) {
                        $in_query_terms[$curr_term['taxonomy']] = array($curr_term['slug']);
                    } else {
                        $terms_to_query[$curr_term['taxonomy']][] = $curr_term['slug'];
                    }


                    break;

                default:
                    //leave it empty
                    break;
            }
        }
        //***

        $taxonomies = array();
        if (!empty($opposition_terms)) {
            foreach ($opposition_terms as $tax_slug => $terms) {
                if (!empty($terms)) {
                    $taxonomies[] = array(
                        'taxonomy' => $tax_slug,
                        'terms' => $terms,
                        'field' => 'slug',
                        'operator' => 'IN',
                        'include_children' => 1
                    );
                }
            }
        }


        if (!empty($in_query_terms)) {
            foreach ($in_query_terms as $tax_slug => $terms) {
                if (!empty($terms)) {
                    $logic_arr = array();
                    if (isset($this->settings['comparison_logic'])) {
                        $logic_arr = $this->settings['comparison_logic'];
//                       if($type!='multi'){
//                         $logic_arr[$tax_slug] == "OR";
//                       }
                    }
                    // var_dump($logic_arr);
                    if (isset($logic_arr[$tax_slug]) AND $logic_arr[$tax_slug] == "AND") {
                        $request = $this->get_request_data();
                        $terms_t = array();
                        if (isset($request[$tax_slug])) {
                            $terms_t = explode(",", $request[$tax_slug]);
                        }
                        $terms = array_merge($terms, $terms_t);
                        $taxonomies[] = array(
                            'taxonomy' => $tax_slug,
                            'terms' => $terms,
                            'field' => 'slug',
                            "operator" => "AND",
                            //"include_children" => true,
                            "include_children" => false
                        );
                    } elseif (isset($logic_arr[$tax_slug]) AND $logic_arr[$tax_slug] == "NOT IN" AND ( !$curr_term OR $curr_term['taxonomy'] != $tax_slug)) {

                        $taxonomies[] = array(
                            'taxonomy' => $tax_slug,
                            'terms' => $terms,
                            'field' => 'slug',
                            "operator" => "NOT IN",
                            //"include_children" => true,
                            "include_children" => false
                        );
                    } else {
                        $taxonomies[] = array(
                            'taxonomy' => $tax_slug,
                            'terms' => $terms,
                            'field' => 'slug',
                            'operator' => 'IN',
                            'include_children' => 1
                        );
                    }
                }
            }
        }

        if (!empty($terms_to_query)) {
            foreach ($terms_to_query as $tax_slug => $terms) {
                if (!empty($terms)) {
                    $taxonomies[] = array(
                        'taxonomy' => $tax_slug,
                        'terms' => $terms,
                        'field' => 'slug',
                        'operator' => 'IN',
                        'include_children' => 1
                    );
                }
            }
        }

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        if (!empty($taxonomies)) {
            $taxonomies['relation'] = 'AND';
        }
        //***
        $args = array(
            'nopaging' => true,
            'fields' => 'ids',
            'post_type' => 'product',
            'post_status' => 'publish'
        );

        $args['tax_query'] = $taxonomies;
        $args['meta_query'] = array();
        if (isset($wp_query->meta_query->queries)) {
            //$args['meta_query'] = $wp_query->meta_query->queries;
        }

        //check for price
        if ($this->is_isset_in_request_data('min_price') AND $this->is_isset_in_request_data('max_price')) {
            $this->assemble_price_params($args['meta_query']);
            $args['meta_query']['relation'] = 'AND';
        }

        //check for rating
        if ($this->is_isset_in_request_data('min_rating')) {
            $min = $request['min_rating'];
            if ($min == 4) {
                $max = 10;
            } else {
                $max = $min + 1 - 0.001;
            }
            $args['meta_query'][] = array(
                'key' => '_wc_average_rating',
                'value' => array($min, $max),
                'type' => 'DECIMAL',
                'compare' => 'BETWEEN'
            );
            $args['meta_query']['relation'] = 'AND';
        }
        //meta filter
        $args['meta_query'] = apply_filters('woof_get_meta_query', $args['meta_query']);
        if (!empty($meta_term)) {
            switch ($type) {
                case 'select':
                    foreach ($args['meta_query'] as $k => $v) {
                        if (isset($v['key']) AND $v['key'] = $meta_term['key']) {
                            unset($args['meta_query'][$k]);
                        }
                    }
                    $args['meta_query'][] = array(
                        'key' => $meta_term['key'],
                        'value' => $meta_term['value'],
                        'compare' => '='
                    );

                    break;
                case 'mselect':


                    if ($meta_term['relation'] == "OR") {

                        WOOF_HELPER::recursiveRemoval($args['meta_query'], $meta_term['key']); //this is a more elegant solution
                    }

                    $args['meta_query'][] = array(
                        'key' => $meta_term['key'],
                        'value' => $meta_term['value'],
                        'compare' => '='
                    );
                    break;
                case 'checkbox_ex':
                    $args['meta_query'][] = $meta = array(
                        'key' => $meta_term['key'],
                        'compare' => 'EXISTS'
                    );
                    break;
                case 'checkbox':
                    $args['meta_query'][] = $meta = array(
                        'key' => $meta_term['key'],
                        'compare' => 'EXISTS'
                    );
                    break;
                case 'slider':
                    $args['meta_query'][] = $meta = array(
                        'key' => $meta_term['key'],
                        'value' => $meta_term['value'],
                        'type' => 'numeric',
                        'compare' => 'BETWEEN',
                    );
                    break;
                default:
                    //leave it empty
                    break;
            }
        }
        //WPML compatibility
        if (class_exists('SitePress')) {
            $args['lang'] = ICL_LANGUAGE_CODE;
        }

        /*
          //for dynamic recount cache working with title search
          if ($this->is_isset_in_request_data('woof_text'))
          {
          $args['woof_text'] = WOOF_HELPER::strtolower($request['woof_text']);
          }
         */
        //***
        $atts = array();
        if (!isset($args['meta_query'])) {
            $args['meta_query'] = array();
        }
        //for extensions
        if (!empty(WOOF_EXT::$includes['html_type_objects'])) {
            foreach (WOOF_EXT::$includes['html_type_objects'] as $obj) {
                if (method_exists($obj, 'assemble_query_params')) {
                    $obj->assemble_query_params($args['meta_query'], $args);
                }
            }
        }

        if (version_compare(WOOCOMMERCE_VERSION, '3.0.8', '>=')) {
            // $args = apply_filters('woocommerce_shortcode_products_query', $args, $atts, null);
        } else {
            //$args = apply_filters('woocommerce_shortcode_products_query', $args, $atts);
        }

        //***
        $_REQUEST['woof_dyn_recount_going'] = 1;
        remove_filter('posts_clauses', array(WC()->query, 'order_by_popularity_post_clauses'));
        remove_filter('posts_clauses', array(WC()->query, 'order_by_rating_post_clauses'));

        // fix visibility
        if (version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
            if ($this->get_option('listen_catalog_visibility')) {
                $args['tax_query'] = $this->product_visibility_not_in($args['tax_query'], $this->generate_visibility_keys(true));
//                $args['tax_query'][] = array(
//                        'taxonomy' => 'product_visibility',
//                        'field'    => 'slug',
//                        'terms'    => array('exclude-from-search' ),
//                        'operator' => 'NOT IN',
//                );
            }
        } elseif (version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) {
            if ($this->get_option('listen_catalog_visibility')) {
                $args['meta_query'][] = array(
                    'key' => '_visibility',
                    'value' => array('search', 'visible'),
                    'compare' => 'IN'
                );
            }
        }
        //***

        $query = new WP_QueryWoofCounter($args);
        unset($_REQUEST['woof_current_recount']);
        unset($_REQUEST['woof_dyn_recount_going']);
        return $query->found_posts;
    }

    public function woocommerce_shortcode_products_query($query_args, $attr, $type = "") {
        if (isset($_REQUEST['override_no_products']) AND $_REQUEST['override_no_products']) {
            return $query_args;
        }
        if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
            $_REQUEST['woof_products_doing'] = 1;
            $query_args['tax_query'] = array_merge($query_args['tax_query'], $this->get_tax_query(''));
            $query_args['meta_query'] = array_merge($query_args['meta_query'], $this->get_meta_query());
            $query_args = apply_filters('woof_products_query', $query_args);

            if (isset($_GET['paged'])) {
                $query_args['paged'] = intval($_GET['paged']);
            }
            // @codingStandardsIgnoreStart
            if (isset($_GET['orderby'])) {
                $ordering_args = WC()->query->get_catalog_ordering_args();
            } else {
                $ordering_args = WC()->query->get_catalog_ordering_args($query_args['orderby'], $query_args['order']);
            }
            $query_args['orderby'] = $ordering_args['orderby'];
            $query_args['order'] = $ordering_args['order'];
            if ($ordering_args['meta_key']) {
                $query_args['meta_key'] = $ordering_args['meta_key'];
            }
        }

        return $query_args;
    }

    public function is_woof_use_chosen() {
        $is = $this->get_option('use_chosen', 1);
        $is = apply_filters('woof_use_chosen', $is);
        return $is;
    }

    public function woocommerce_before_shop_loop() {

        $woof_set_automatically = 0;
        //for mobile devices
        $mobile_behavior = intval(get_option('woof_set_automatically', 0));
        if (($mobile_behavior == 1) OR ( $mobile_behavior == 2 AND wp_is_mobile()) OR ( $mobile_behavior == 3 AND ! wp_is_mobile())) {
            $woof_set_automatically = 1;
        }
        //$_REQUEST['woof_before_shop_loop_done'] - is just key lock


        if ($woof_set_automatically === 1 AND ! isset($_REQUEST['woof_before_shop_loop_done'])) {
            $_REQUEST['woof_before_shop_loop_done'] = true;
            $shortcode_hide = false;
            if (isset($this->settings['woof_auto_hide_button'])) {
                $shortcode_hide = intval($this->settings['woof_auto_hide_button']);
            }

            $price_filter = 0;
            if (isset($this->settings['by_price']['show'])) {
                $price_filter = (int) $this->settings['by_price']['show'];
            }
            $shortcode_id = "auto_shortcode";
            if (isset($this->settings['woof_auto_filter_skins']) AND $this->settings['woof_auto_filter_skins']) {
                $shortcode_id = $this->settings['woof_auto_filter_skins'];
            }

            echo do_shortcode('[woof sid="' . $shortcode_id . '" autohide=' . $shortcode_hide . ' price_filter=' . $price_filter . ']');
        }
        ?>



        <?php
        // woo3.3
        $is_wc_shortcode = false;
        if (version_compare(WOOCOMMERCE_VERSION, '3.3', '>=')) {
            $is_wc_shortcode = wc_get_loop_prop('is_shortcode');
            if ($is_wc_shortcode) {//To deactivate the initialization of JS scripts
                //echo "<span class='is_woo_native_shortcode'></span>";
            }
        }
        //++++
        //for ajax output
        //if (get_option('woof_try_ajax', 0) AND ! isset($_REQUEST['woof_products_doing'])AND ! $is_wc_shortcode) {// woo3.3
        if (get_option('woof_try_ajax', 0) AND ! isset($_REQUEST['woof_products_doing'])AND ! $is_wc_shortcode) {
            //$_REQUEST['woocommerce_before_shop_loop_done']=true;
            echo '<div class="woocommerce woocommerce-page woof_shortcode_output">';
            $shortcode_txt = "woof_products is_ajax=1";
            if ($this->is_really_current_term_exists()) {
                $o = $this->get_really_current_term();
                $shortcode_txt = "woof_products taxonomies={$o->taxonomy}:{$o->term_id} is_ajax=1 predict_ids_and_continue=1";
                $_REQUEST['WOOF_IS_TAX_PAGE'] = $o->taxonomy;
            }
            echo '<div id="woof_results_by_ajax" data-shortcode="' . $shortcode_txt . '">';
        }

        if (get_option('woof_hide_red_top_panel', 0) == 0) {
            echo do_shortcode('[woof_search_options]');
        }
    }

    public function woocommerce_after_shop_loop() {
        // woo3.3
        $is_wc_shortcode = false;
        if (version_compare(WOOCOMMERCE_VERSION, '3.3', '>=')) {
            $is_wc_shortcode = wc_get_loop_prop('is_shortcode');
        }
        //for ajax output
        if (get_option('woof_try_ajax', 0) AND ! isset($_REQUEST['woof_products_doing'])AND ! $is_wc_shortcode) {// woo3.3
            echo '</div>';
            echo '</div>';
        }
    }

    public function get_request_data($apply_filters = true) {

        $data = $_GET;
        // fix for special simbols
        $woof_text_urlencode = apply_filters('woof_text_urlencode', 0);
        if (isset($data['gclid'])) {
            //google tracking id removing from search link
            unset($data['gclid']);
        }

        //secure data filtrating
        if (!empty($data) AND is_array($data)) {
            $tmp = array();
            foreach ($data as $key => $value) {
                if (!is_string($key) OR ! is_string($value)) {
                    continue;
                }
                if ($woof_text_urlencode) {
                    $tmp[WOOF_HELPER::escape($key)] = urlencode(WOOF_HELPER::escape($value));
                } else {
                    $tmp[WOOF_HELPER::escape($key)] = WOOF_HELPER::escape($value);
                }
            }
            $data = $tmp;
        }


        if ($apply_filters) {
            $data = apply_filters('woof_get_request_data', $data);
        }

        return $data;
    }

    public function is_isset_in_request_data($key, $apply_filters = true) {
        $request = $this->get_request_data($apply_filters);
        return isset($request[$key]);
    }

    public function get_catalog_orderby($orderby = '', $order = 'ASC') {
        if (empty($orderby) OR $orderby == 'no') {
            $orderby = get_option('woocommerce_default_catalog_orderby');
        }
        //echo $orderby;exit;
        //wp-content\plugins\woocommerce\includes\class-wc-query.php#588
        //$orderby_array = array('menu_order', 'popularity', 'rating',
        //'date', 'price', 'price-desc','rand');
        $meta_key = '';
        global $wpdb;
        switch ($orderby) {
            case 'price-desc':
                $orderby = "meta_value_num {$wpdb->posts}.ID";
                $order = 'DESC';
                $meta_key = '_price';
                break;
            case 'price':
                $orderby = "meta_value_num {$wpdb->posts}.ID";
                $order = 'ASC';
                $meta_key = '_price';
                break;
            case 'popularity' :
                // Sorting handled later though a hook
                add_filter('posts_clauses', array(WC()->query, 'order_by_popularity_post_clauses'));
                $meta_key = 'total_sales';
                //$orderby = "meta_value_num {$wpdb->posts}.ID";
                //$order = 'DESC';
                //$meta_key = 'total_sales';
                break;
            case 'rating' :
                //$orderby = '';
                //$meta_key = '';
                //add_filter('posts_clauses', array(WC()->query, 'order_by_rating_post_clauses'));

                $orderby = "meta_value_num {$wpdb->posts}.ID";
                $order = 'DESC';
                $meta_key = '_wc_average_rating';
                break;
            case 'title' :
                $orderby = 'title';
                break;
            case 'title-desc':
                $orderby = "title";
                $order = 'DESC';
                break;
            case 'title-asc':
                $orderby = "title";
                $order = 'ASC';
                break;
            case 'rand' :
                $orderby = 'rand';
                break;
            case 'date' :
                $order = 'DESC';
                $orderby = 'date';
                break;
            default:
                $orderby = 'menu_order title';
                //$orderby = 'menu_order date';
                break;
        }

        //print_r(compact('order', 'orderby', 'meta_key'));
        return apply_filters('woof_order_catalog', compact('order', 'orderby', 'meta_key'));
    }

    public function get_tax_query($additional_taxes = '') {
        $data = $this->get_request_data();
        $res = array();
        //static $woo_taxonomies = NULL;
        $woo_taxonomies = NULL;
        //if (!$woo_taxonomies)
        {
            $woo_taxonomies = get_object_taxonomies('product');
        }

        //+++

        if (!empty($data) AND is_array($data)) {
            foreach ($data as $tax_slug => $value) {
                if (in_array($tax_slug, $woo_taxonomies)) {
                    $value = explode(',', $value);
                    $res[] = array(
                        'taxonomy' => $tax_slug,
                        'field' => 'slug',
                        'terms' => $value,
                            //'operator'=>'OR'
                    );
                }
            }
        }
        //+++
        //for shortcode
        //[woof_products is_ajax=1 per_page=8 taxonomies=product_cat:9,12+locations:30,31]
        $res = $this->_expand_additional_taxes_string($additional_taxes, $res);
        //+++
        if (!empty($res)) {
            $res = array_merge(array('relation' => 'AND'), $res);
        }

        $res = $this->parse_tax_query($res);

        //woof_get_tax_query works in ajax mode if describe in themes functions.php
        return apply_filters('woof_get_tax_query', $res);
    }

    private function _expand_additional_taxes_string($additional_taxes, $res = array()) {
        if (!empty($additional_taxes)) {
            $t = explode('+', $additional_taxes);
            if (!empty($t) AND is_array($t)) {
                foreach ($t as $string) {
                    $tmp = explode(':', $string);
                    $tax_slug = $tmp[0];
                    $tax_terms = explode(',', $tmp[1]);
                    $slugs = array();
                    foreach ($tax_terms as $term_id) {
                        $term = get_term(intval($term_id), $tax_slug);
                        if (is_object($term)) {
                            $slugs[] = $term->slug;
                        }
                    }

                    //***
                    if (!empty($slugs)) {
                        $res[] = array(
                            'taxonomy' => $tax_slug,
                            'field' => 'slug', //id
                            'terms' => $slugs
                        );
                    }
                }
            }
        }

        return $res;
    }

    private function listen_catalog_visibility($meta_query, $is_search = false) {
        if ($this->get_option('listen_catalog_visibility')) {
            //if search is going
            if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
                if (!empty($meta_query)) {
                    foreach ($meta_query as $key => $value) {
                        if (isset($value['key'])) {
                            if ($value['key'] == '_visibility') {
                                unset($meta_query[$key]);
                                $meta_query = array_values($meta_query);
                                break;
                            }
                        }
                        if (version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
                            if (isset($value['taxonomy'])) {
                                if ($value['taxonomy'] == 'product_visibility') {
                                    unset($meta_query[$key]);
                                    $meta_query = array_values($meta_query);
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($is_search) {
                    global $wp_query;
                    $wp_query->is_search = true;
                }
                if (version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {

//                    $args['tax_query'][] = array(
//                            'relation' => 'OR',
//                            array(
//                                'taxonomy' => 'product_visibility',
//                                'field' => 'name',
//                                'terms' => 'exclude-from-catalog',
//                                'operator' => 'NOT IN',
//                            ),
//                            array(
//                                'taxonomy' => 'product_visibility',
//                                'field'    => 'slug',
//                                'terms'    => array('exclude-from-search' ),
//                                'operator' => 'NOT IN',
//                            ),
//                        );
                } elseif (version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) {
                    $meta_query[] = array(
                        'key' => '_visibility',
                        'value' => array('search', 'visible'),
                        'compare' => 'IN'
                    );
                }
            }
        }

        return $meta_query;
    }

    //works only in shortcode [woof_products]
    public function get_meta_query($args = array()) {
        //print_r(WC()->query); - will think about it
        $meta_query = WC()->query->get_meta_query();
        $meta_query = array_merge(array('relation' => 'AND'), $meta_query);
        //+++
        $this->assemble_price_params($meta_query);
        //for extensions
        if (!empty(WOOF_EXT::$includes['html_type_objects'])) {
            foreach (WOOF_EXT::$includes['html_type_objects'] as $obj) {
                if (method_exists($obj, 'assemble_query_params')) {
                    $obj->assemble_query_params($meta_query);
                }
            }
        }
        // fix visibility
        if (version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) {
            $meta_query = $this->listen_catalog_visibility($meta_query);
        }
        // end fix
        // For meta filter
        $meta_query = apply_filters('woof_get_meta_query', $meta_query);
        return $meta_query;
    }

    public function woof_products_ids_prediction($atts) {
        return $this->woof_products($atts, true);
    }

    //plugins\woocommerce\includes\class-wc-shortcodes.php#295
    //[woof_products is_ajax=1 per_page=8 taxonomies=product_cat:9,12+locations:30,31]
    public function woof_products($atts, $is_prediction = false) {
        $_REQUEST['woof_products_doing'] = 1;
        //add_filter('posts_where', array($this, 'woof_post_text_filter'), 9999);
        //add_filter('posts_where', array($this, 'woof_post_author_filter'), 9999);
        $shortcode_txt = 'woof_products';
        if (!empty($atts)) {
            foreach ($atts as $key => $value) {
                $shortcode_txt .= ' ' . $key . '=' . $value;
            }
        }
        //***
        $data = $this->get_request_data();
        $catalog_orderby = $this->get_catalog_orderby(isset($data['orderby']) ? $data['orderby'] : '');

        //https://gist.github.com/mikejolley/1622323
        extract(shortcode_atts(array(
            'columns' => apply_filters('loop_shop_columns', 4),
            'orderby' => 'no',
            'order' => 'no',
            'page' => 1,
            'per_page' => 0,
            'is_ajax' => 0,
            'taxonomies' => '',
            'sid' => '',
            'behavior' => '', //recent
            'custom_tpl' => '', //path like: wp-content/themes/my_theme/woo_tpl_1.php
            'tpl_index' => '', //index of any template extension
            'predict_ids_and_continue' => false,
            'get_args_only' => false,
            'shortcode' => '',
            'product_ids' => "",
            'post__in' => "",
            'display_on_search' => 0,
                        ), $atts));


        $order_by_defined_in_atts = false;
        if ($orderby == 'no') {
            $orderby = $catalog_orderby['orderby'];
            $order = $catalog_orderby['order'];
        } elseif ($orderby == 'price' OR $orderby == 'price-desc') {
            $catalog_orderby = $this->get_catalog_orderby(isset($orderby) ? $orderby : '');
        } else {
            $order_by_defined_in_atts = true;
        }

        //***
        //this needs just for AJAX mode for shortcode [woof] in woof_draw_products()
        $_REQUEST['woof_additional_taxonomies_string'] = $taxonomies;
        // fix visibility
        $tax_query = array();
        if (empty($product_ids)) {
            $tax_query = $this->get_tax_query($taxonomies);
        }
        if (version_compare(WOOCOMMERCE_VERSION, '3.0', '>=') AND version_compare(WOOCOMMERCE_VERSION, '3.3', '<')) {
            $tax_query = $this->listen_catalog_visibility($tax_query);
        } elseif (version_compare(WOOCOMMERCE_VERSION, '3.3', '>=')) {         // woo3.3
            $tax_query = $this->product_visibility_not_in($tax_query, $this->generate_visibility_keys($this->is_isset_in_request_data($this->get_swoof_search_slug())));
        }
        //+++
        //+++

        $args = array(
            'post_type' => array('product'/* ,'product_variation' */),
            'post_status' => 'publish',
            'orderby' => $orderby,
            'order' => $order,
            'tax_query' => $tax_query
        );
        if (empty($product_ids)) {
            $args['meta_query'] = $this->get_meta_query();
            if ($post__in) {
                $args['post__in'] = explode(",", $post__in);
            }
        } else {
            $args['post__in'] = explode(",", $product_ids);
        }

        if ($per_page > 0) {
            $args['posts_per_page'] = $per_page;
        } else {
            if (intval($this->settings['per_page']) > 0) {
                $args['posts_per_page'] = intval($this->settings['per_page']);
            }

            //compatibility for woocommerce-products-per-page
            if (class_exists('Woocommerce_Products_Per_Page')) {
                $args['posts_per_page'] = $this->get_wppp_per_page();
            }
        }

        //Display Product for WooCommerce compatibility
        if (isset($_REQUEST['perpage'])) {
            //if (is_integer($_REQUEST['perpage']))
            {
                $args['posts_per_page'] = $_REQUEST['perpage'];
            }
        }

        //if smth wrong, set default per page option
        if (!isset($args['posts_per_page']) OR empty($args['posts_per_page'])) {
            if ($this->get_option('per_page') > 0) {
                $args['posts_per_page'] = $this->get_option('per_page');
            } else {
                $args['posts_per_page'] = get_option('posts_per_page');
            }
        }

        //***
        if (!$order_by_defined_in_atts) {
            if (!empty($catalog_orderby['meta_key'])) {
                $args['meta_key'] = $catalog_orderby['meta_key'];
                $args['orderby'] = $catalog_orderby['orderby'];
                if (!empty($catalog_orderby['order'])) {
                    $args['order'] = $catalog_orderby['order'];
                }
            } else {
                $args['orderby'] = $catalog_orderby['orderby'];
                if (!empty($catalog_orderby['order'])) {
                    $args['order'] = $catalog_orderby['order'];
                }
            }
        }
        //print_r($args);
        //+++
        $pp = $page;
        if (get_query_var('page')) {
            $pp = get_query_var('page');
        }
        if (get_query_var('paged')) {
            $pp = get_query_var('paged');
        }

        if ($pp > 1) {
            $args['paged'] = $pp;
        } else {
            $args['paged'] = ((get_query_var('page')) ? get_query_var('page') : $page);
        }
        //+++

        if (!empty($behavior)) {
            switch ($behavior) {
                case 'recent':
                    $args['orderby'] = 'date';
                    $args['order'] = 'desc';
                    break;

                default:
                    break;
            }
        }
        //***
        if (version_compare(WOOCOMMERCE_VERSION, '3.0.8', '>=')) {
            //$wr = apply_filters('woocommerce_shortcode_products_query', $args, $atts, null);
        } else {
            //$wr = apply_filters('woocommerce_shortcode_products_query', $args, $atts);
        }
        $wr = $args;

        global $products, $wp_query;
        //***
        $tax_relations = apply_filters('woof_main_query_tax_relations', array());
        if (!empty($tax_relations)) {
            $tax_query = $wr['tax_query'];
            foreach ($tax_query as $key => $value) {
                if (isset($value['taxonomy'])) {
                    if (in_array($value['taxonomy'], array_keys($tax_relations))) {
                        if (count($tax_query[$key]['terms'])) {
                            $tax_query[$key]['operator'] = $tax_relations[$value['taxonomy']];
                            $tax_query[$key]['include_children'] = 0;
                        }
                    }
                }
            }

            $wr['tax_query'] = $tax_query;
        }
        //***

        $wr = apply_filters('woof_products_query', $wr);

        //***

        if ($get_args_only) {
            $_REQUEST['woof_query_args'] = $wr;
            return $wr;
        }
        $hide_products = false;
        if ($display_on_search) {
            $hide_products = true;
            $get_array = $this->get_request_data();
            //var_dump($real_query);
            if (isset($this->settings['items_order'])) {
                $key_array = explode(',', $this->settings['items_order']);
                $by_only_array = array('woof_text', 'stock', 'onsales', 'woof_sku', 'product_visibility', 'min_price', 'max_price');
                $tax_array = array_keys($this->settings['excluded_terms']);
                $key_array = array_merge($by_only_array, $key_array, $tax_array);
                $real_query = array_intersect(array_keys($get_array), $key_array);
                if (count($real_query)) {
                    $hide_products = false;
                }
                //var_dump($key_array);
                if (!is_array($this->settings['meta_filter'])) {
                    $this->settings['meta_filter'] = array();
                }
                foreach ($this->settings['meta_filter'] as $item) {
                    $key = $item['search_view'] . "_" . $item['meta_key'];
                    if (in_array($key, array_keys($get_array))) {
                        $hide_products = false;
                    }
                }
            }
        }

        if (!$is_prediction) {
            $_REQUEST['woof_wp_query'] = $wp_query = $products = new WP_Query($wr);
            $_REQUEST['woof_wp_query_found_posts'] = $wp_query->found_posts;
            if ($predict_ids_and_continue) {
                $_REQUEST['predict_ids_and_continue'] = 1;
                $_REQUEST['woof_wp_query_ids'] = new WP_Query(array_merge($wr, array('fields' => 'ids')));
                $_REQUEST['woof_wp_query_ids'] = $_REQUEST['woof_wp_query_ids']->posts;
            }
        } else {
            $_REQUEST['woof_wp_query_ids'] = new WP_Query(array_merge($wr, array('fields' => 'ids')));
            $_REQUEST['woof_wp_query_ids'] = $_REQUEST['woof_wp_query_ids']->posts;
            return;
        }


        if ($this->get_option('listen_catalog_visibility')
                AND $this->is_isset_in_request_data($this->get_swoof_search_slug())) {
            //for wp-content\plugins\woocommerce\includes\class-wc-query.php -> $in = array( 'visible', 'search' )
            $wp_query->is_search = true;
        }

        $wp_query->is_post_type_archive = true; //we need it to display top panel with counter and order drop-down
        $_REQUEST['woof_wp_query_args'] = $wr;
        //***
        //  echo "<pre>";
        //var_dump($wp_query->request);
        // echo "</pre>";
        ob_start();
        global $woocommerce_loop;
        $woocommerce_loop['columns'] = $columns;
        $woocommerce_loop['loop'] = 0;
        // woo3.3
        if (version_compare(WOOCOMMERCE_VERSION, '3.3', '>=')) {
            $this->set_loop_properties($products, $columns);
        }
        ?>

        <?php if ($is_ajax == 1): ?>
            <?php //if (!get_option('woof_try_ajax')):                                                                                                                                       ?>
            <div id="woof_results_by_ajax" data-count="<?php echo $products->found_posts ?>"  class="woof_results_by_ajax_shortcode" data-shortcode="<?php echo $shortcode_txt ?>">
                <?php
                //endif; 
                //ajax compatibility
                $_REQUEST["woof_redraw_elements"] = array();
                if (isset($this->settings['result_count_redraw']) AND $this->settings['result_count_redraw']) {
                    ob_start();
                    woocommerce_result_count();
                    $_REQUEST["woof_redraw_elements"][$this->settings['result_count_redraw']] = ob_get_contents();
                    ob_end_clean();
                }
                if (isset($this->settings['order_dropdown_redraw']) AND $this->settings['order_dropdown_redraw']) {
                    ob_start();
                    woocommerce_catalog_ordering();
                    $_REQUEST["woof_redraw_elements"][$this->settings['order_dropdown_redraw']] = ob_get_contents();
                    ob_end_clean();
                }
                if (isset($this->settings['per_page_redraw']) AND $this->settings['per_page_redraw']) {
                    ob_start();
                    woocommerce_pagination();
                    $_REQUEST["woof_redraw_elements"][$this->settings['per_page_redraw']] = ob_get_contents();
                    ob_end_clean();
                }
                $_REQUEST["woof_redraw_elements"] = apply_filters('woof_redraw_elements_after_ajax', $_REQUEST["woof_redraw_elements"], $products);
                //+++
                ?>           
            <?php endif; ?>
            <?php
            if ($products->have_posts()) :
                add_filter('post_class', array($this, 'woo_post_class'));
                $_REQUEST['woof_before_shop_loop_done'] = true;
                ?>

                <div class="woocommerce columns-<?php echo $columns ?> woocommerce-page woof_shortcode_output">

                    <?php
                    $show_loop_filters = true; //for attribute behavior of the shortcode
                    if (!empty($behavior)) {
                        if ($behavior == 'recent') {
                            $show_loop_filters = false;
                        }
                    }
                    if (!$hide_products) {
                        if ($show_loop_filters) {
                            do_action('woocommerce_before_shop_loop');
                        }

                        //***



                        if (function_exists('woocommerce_product_loop_start')) {
                            woocommerce_product_loop_start();
                        }
                        ?>

                        <?php
                        global $woocommerce_loop;
                        $woocommerce_loop['columns'] = $columns;
                        $woocommerce_loop['loop'] = 0;

                        //+++
                        //wc_get_template('loop/loop-start.php');
                        ?>

                        <?php
                        $template_part = apply_filters('woof_template_part', 'product');
                        //products output
                        if (empty($custom_tpl) AND empty($tpl_index)) {
                            while ($products->have_posts()) : $products->the_post();
                                wc_get_template_part('content', $template_part);
                            endwhile; // end of the loop.
                        } else {
                            if (!empty($tpl_index)) {
                                //template extension drawing
                                if (isset(WOOF_EXT::$includes['applications'][$tpl_index])) {
                                    WOOF_EXT::$includes['applications'][$tpl_index]->draw($products);
                                }
                            } else {
                                $custom_tpl = str_replace('.' . pathinfo($custom_tpl, PATHINFO_EXTENSION), '', str_replace("..", "", $custom_tpl));
                                //$custom_tpl=str_replace("..","",$custom_tpl);
                                //$custom_tpl = preg_replace('/\.' . preg_quote(pathinfo($custom_tpl, PATHINFO_EXTENSION), '/') . '$/', '', $custom_tpl);
                                echo $this->render_html(get_theme_file_path($custom_tpl . ".php"), array(
                                    'the_products' => $products
                                ));
                            }
                        }
                        ?>

                        <?php //wc_get_template('loop/loop-end.php');                ?>

                        <?php
                        //woo_pagenav(); - for wp theme canvas
                        if (function_exists('woocommerce_product_loop_end')) {
                            woocommerce_product_loop_end();
                        }
                        ?>

                        <?php
                        if ($show_loop_filters) {
                            do_action('woocommerce_after_shop_loop');
                        }
                    }
                    ?>
                </div>


                <?php
            else:
                if ($is_ajax == 1) {
                    //if (!get_option('woof_try_ajax'))
                    {
                        ?>
                        <div id="woof_results_by_ajax" class="woof_results_by_ajax_shortcode" data-shortcode="<?php echo $shortcode_txt ?>">
                            <?php
                        }
                    }
                    ?>
                    <div class="woocommerce woocommerce-page woof_shortcode_output">

                        <?php
                        if (!$is_ajax) {
                            wc_get_template('loop/no-products-found.php');
                        } else {
                            ?>
                            <div id="woof_results_by_ajax" class="woof_results_by_ajax_shortcode" data-shortcode="<?php echo $shortcode_txt ?>">
                                <?php
                                wc_get_template('loop/no-products-found.php');
                                ?>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                    <?php
                    if ($is_ajax == 1) {
                        if (!get_option('woof_try_ajax', 0)) {
                            echo '</div>';
                        }
                    }

                endif;
                ?>

                <?php if ($is_ajax == 1): ?>
                    <?php if (!get_option('woof_try_ajax', 0)): ?>
                    </div>

                <?php endif; ?>
            <?php endif; ?>
            <?php
            wp_reset_postdata();
            wp_reset_query();
            // woo3.3
            if (version_compare(WOOCOMMERCE_VERSION, '3.3', '>=')) {
                wc_reset_loop();
            }
            //+++

            unset($_REQUEST['woof_products_doing']);

            return ob_get_clean();
        }

        //for shortcode woof_products
        public function woo_post_class($classes) {
            global $post;
            $classes[] = 'product';
            $classes[] = 'type-product';
            $classes[] = 'status-publish';
            $classes[] = 'has-post-thumbnail';
            $classes[] = 'post-' . $post->ID;
            return $classes;
        }

        //shortcode, works when ajax mode only for shop/category page
        public function woof_draw_products() {
            if (isset($_REQUEST['link'])) {
                $link = parse_url($_REQUEST['link'], PHP_URL_QUERY);
                parse_str($link, $_GET); //$_GET data init
            }
            $product_ids = "";
            if (isset($_REQUEST['turbo_mode_ids'])) {
                $product_ids = " product_ids='" . $_REQUEST['turbo_mode_ids'] . "' ";
            }
            //add_filter('posts_where', array($this, 'woof_post_text_filter'), 9999);
            $shortcode_str = $this->check_shortcode("woof_products", "[" . $_REQUEST['shortcode'] . " page=" . $_REQUEST['page'] . $product_ids . "]");

            $products = do_shortcode($shortcode_str);

            $additional_fields = array();
//            if(isset($this->settings['result_count_redraw']) AND $this->settings['result_count_redraw'] AND $_REQUEST["woof_redraw_elements"][$this->settings['result_count_redraw']]){
//                $additional_fields[$this->settings['result_count_redraw']] = $_REQUEST["woof_redraw_elements"][$this->settings['result_count_redraw']];
//            }
//            if(isset($this->settings['order_dropdown_redraw']) AND $this->settings['order_dropdown_redraw'] AND $_REQUEST["woof_redraw_elements"][$this->settings['order_dropdown_redraw']]){             
//                $additional_fields[$this->settings['order_dropdown_redraw']]=$_REQUEST["woof_redraw_elements"][$this->settings['order_dropdown_redraw']];
//            }
//            if(isset($this->settings['per_page_redraw']) AND $this->settings['per_page_redraw'] AND $_REQUEST["woof_redraw_elements"][$this->settings['per_page_redraw']]){
//                $additional_fields[$this->settings['per_page_redraw']]=$_REQUEST["woof_redraw_elements"][$this->settings['per_page_redraw']];
//
//            }              


            if (isset($_REQUEST["woof_redraw_elements"]) AND $_REQUEST["woof_redraw_elements"]) {
                $additional_fields = $_REQUEST["woof_redraw_elements"];
            }
            //+++
            $form = '';
            if (isset($_REQUEST['woof_shortcode'])) {//if search form on the page exists
                $text = "";
                $shortcode_str = "";

                if (empty($_REQUEST['woof_additional_taxonomies_string'])) {
                    $text = "[" . stripslashes($_REQUEST['woof_shortcode']) . "]";
                } else {
                    $text = "[" . stripslashes($_REQUEST['woof_shortcode'] . " taxonomies={$_REQUEST['woof_additional_taxonomies_string']}]");
                }
                $shortcode_str = $this->check_shortcode("woof", $text);
                //for data-shortcode in woof.php in ajax mode
                if (!empty($shortcode_str)) {
                    $_REQUEST['woof_shortcode_txt'] = $_REQUEST['woof_shortcode'];
                }
                //***
                $form = do_shortcode($shortcode_str);
            }


            wp_die(json_encode(compact('products', 'form', 'additional_fields')));
        }

        //[woof taxonomies="product_cat:9" sid="auto_shortcode"]
        public function woof_shortcode($atts) {
            $args = array();
            //this for synhronizating shortcode woof_products if its has attribute taxonomies
            if (isset($atts['taxonomies'])) {
                //$args['additional_taxes'] = $this->get_tax_query($atts['taxonomies']);
                //$args['additional_taxes'] = $atts['taxonomies'];
                $args['additional_taxes'] = apply_filters('woof_set_shortcode_taxonomyattr_behaviour', $atts['taxonomies']);
            } else {
                $args['additional_taxes'] = '';
            }

            //by hands excluded terms directly in [woof] shortcode
            unset($_REQUEST['woof_shortcode_excluded_terms']);
            if (isset($atts['excluded_terms'])) {
                $_REQUEST['woof_shortcode_excluded_terms'] = $atts['excluded_terms'];
            }


            //+++
            $taxonomies = $this->get_taxonomies();
            $allow_taxonomies = (array) (isset($this->settings['tax']) ? $this->settings['tax'] : array());
            $args['taxonomies'] = array();
            $hide_empty = (bool) get_option('woof_hide_dynamic_empty_pos', 0);
            if (!empty($taxonomies)) {
                foreach ($taxonomies as $tax_key => $tax) {
                    if (!in_array($tax_key, array_keys($allow_taxonomies))) {
                        continue;
                    }
                    //+++

                    $args['taxonomies_info'][$tax_key] = $tax;
                    $args['taxonomies'][$tax_key] = WOOF_HELPER::get_terms($tax_key, $hide_empty);
                    //show only subcategories if is_really_current_term_exists
                    if ($this->is_really_current_term_exists()) {
                        $t = $this->get_really_current_term();
                        if ($tax_key == $t->taxonomy) {
                            if (isset($args['taxonomies'][$tax_key][$t->term_id])) {
                                $args['taxonomies'][$tax_key] = $args['taxonomies'][$tax_key][$t->term_id]['childs'];
                            } else {
                                if ($t->parent != 0) {
                                    $parent = get_term($t->parent, $tax_key);
                                    $parents_ids = array();
                                    $parents_ids[] = $parent->term_id;
                                    while ($parent->parent != 0) {
                                        $parent = get_term_by('id', $parent->parent, $tax_key);
                                        $parents_ids[] = $parent->term_id;
                                    }
                                    $parents_ids = array_reverse($parents_ids);
                                    //***
                                    $tmp = $args['taxonomies'][$tax_key];
                                    foreach ($parents_ids as $id) {
                                        $tmp = $tmp[$id]['childs'];
                                    }
                                    $args['taxonomies'][$tax_key] = $tmp[$t->term_id]['childs'];
                                }
                            }
                        }
                    }
                }
            } else {
                $args['taxonomies'] = array();
            }
            //***
            if (isset($atts['skin'])) {
                wp_enqueue_style('woof_skin_' . $atts['skin'], WOOF_LINK . 'css/shortcode_skins/' . $atts['skin'] . '.css', array(), WOOF_VERSION);
            }
            //***

            if (isset($atts['sid'])) {
                $args['sid'] = $atts['sid'];
                wp_enqueue_script('woof_sid', WOOF_LINK . 'js/woof_sid.js', array(), WOOF_VERSION);
            }


            if (isset($atts['autohide'])) {
                $args['autohide'] = $atts['autohide'];
            } else {
                $args['autohide'] = 0;
            }

            if (isset($atts['redirect'])) {
                $args['redirect'] = $atts['redirect'];
            } else {
                $args['redirect'] = '';
            }

            if (isset($atts['start_filtering_btn'])) {
                $args['start_filtering_btn'] = $atts['start_filtering_btn'];
            } else {
                $args['start_filtering_btn'] = 0;
            }

            if (isset($atts['start_filtering_btn_txt'])) {
                $args['woof_start_filtering_btn_txt'] = $atts['start_filtering_btn_txt'];
            } else {
                $args['woof_start_filtering_btn_txt'] = apply_filters('woof_start_filtering_btn_txt', __('Show products filter form', 'woocommerce-products-filter'));
            }


            if (isset($atts['tax_only'])) {
                $args['tax_only'] = explode(',', $atts['tax_only']);
                $args['tax_only'] = array_map('trim', $args['tax_only']);
            } else {
                $args['tax_only'] = array();
            }

            if (isset($atts['tax_exclude'])) {
                $args['tax_exclude'] = explode(',', $atts['tax_exclude']);
                $args['tax_exclude'] = array_map('trim', $args['tax_exclude']);
            } else {
                $args['tax_exclude'] = array();
            }

            if (isset($atts['by_only'])) {
                $args['by_only'] = explode(',', $atts['by_only']);
                $args['by_only'] = array_map('trim', $args['by_only']);
            } else {
                $args['by_only'] = array();
            }


            if (isset($atts['autosubmit']) AND $atts['autosubmit'] != -1) {
                $args['autosubmit'] = $atts['autosubmit'];
            } else {
                $args['autosubmit'] = get_option('woof_autosubmit', 0);
            }

            $_REQUEST['hide_terms_count_txt_short'] = -1;
            if (isset($atts['hide_terms_count'])) {
                $_REQUEST['hide_terms_count_txt_short'] = (int) $atts['hide_terms_count'];
            }

            if (isset($atts['ajax_redraw'])) {
                $args['ajax_redraw'] = $atts['ajax_redraw'];
            } else {
                $args['ajax_redraw'] = 0;
            }
            if (isset($atts['btn_position'])) {
                $args['btn_position'] = $atts['btn_position'];
            } else {
                $args['btn_position'] = 'b';
            }
            if (isset($atts['dynamic_recount'])) {
                $args['dynamic_recount'] = $atts['dynamic_recount'];
            } else {
                $args['dynamic_recount'] = -1;
            }

            $args['price_filter'] = 0;
            if (isset($this->settings['by_price']['show'])) {
                $args['price_filter'] = (int) $this->settings['by_price']['show'];
            }

            if (isset($atts['by_step'])) {
                $args['by_step'] = $atts['by_step'];
            }

            //***
            $args['show_woof_edit_view'] = 1;
            if (current_user_can('create_users')) {
                $args['show_woof_edit_view'] = isset($this->settings['show_woof_edit_view']) ? (int) $this->settings['show_woof_edit_view'] : 1;
            }




            //lets assemble shortcode txt for ajax mode for data-shortcode in woof.php
            $_REQUEST['woof_shortcode_txt'] = 'woof ';
            if (!empty($atts)) {
                foreach ($atts as $key => $value) {
                    if (($key == 'tax_only' OR $key == 'by_only' OR $key == 'tax_exclude') AND empty($value)) {
                        continue;
                    }

                    $_REQUEST['woof_shortcode_txt'] .= $key . "='" . (is_array($value) ? explode(',', $value) : $value) . "' ";
                }
            }
            $args['woof_settings'] = get_option('woof_settings', array());
            //$_REQUEST['woof_shortcode_txt'] = "woof tax_only='pa_color' by_only='none'";

            return $this->render_html(WOOF_PATH . 'views/woof.php', $args);
        }

        //shortcode
        public function woof_price_filter($args = array()) {
            $type = 'slider';
            if (isset($args['type']) AND $args['type'] == 'select') {
                $type = 'select';
            }
            if (isset($args['type']) AND $args['type'] == 'text') {
                $type = 'text';
            }
            if (isset($args['type']) AND $args['type'] == 'radio') {
                $type = 'radio';
            }
            return $this->render_html(WOOF_PATH . 'views/shortcodes/woof_price_filter_' . $type . '.php', $args);
        }

        //shortcode
        public function woof_search_options($args = array()) {
            return $this->render_html(WOOF_PATH . 'views/shortcodes/woof_search_options.php', array());
        }

        //shortcode
        public function woof_found_count($args = array()) {
            return $this->render_html(WOOF_PATH . 'views/shortcodes/woof_found_count.php', array());
        }

        //redraw search form
        public function woof_redraw_woof() {
            $_REQUEST['woof_shortcode_txt'] = $_REQUEST['shortcode'];
            $shortcode_str = $this->check_shortcode("woof", "[" . $_REQUEST['shortcode'] . "]");
            wp_die(do_shortcode($shortcode_str));
        }

        public function woocommerce_pagination_args($args) {
            return $args;
        }

        //if we are on the category products page, or any another product taxonomy page
        public function get_really_current_term() {
            $res = NULL;
            $key = $this->session_rct_key;
            $request = $this->get_request_data(FALSE);


            if ($this->storage->is_isset($key)) {
                $res = $this->storage->get_val($key);
            }

            if (!$res) {
                if (isset($request['really_curr_tax'])) {
                    $tmp = explode('-', $request['really_curr_tax']);
                    $res = get_term($tmp[0], $tmp[1]);
                }
            }


            return $res;
        }

        public function is_really_current_term_exists() {
            return (bool) $this->get_really_current_term();
        }

        //we need it when making search on the category page == any taxonomy term page
        private function set_really_current_term($queried_obj = NULL) {
            if (defined('DOING_AJAX')) {
                return false;
            }


            $request = $this->get_request_data();
            if (!$queried_obj) {
                if (isset($request['really_curr_tax'])) {
                    return false;
                }
            }

            $key = $this->session_rct_key;
            /*
              if ($queried_obj === NULL)
              {
              //unset($_SESSION['woof_really_current_term']);
              WC()->session->__unset($key);
              } else
              {
              //$_SESSION['woof_really_current_term'] = $queried_obj;
              WC()->session->set($key, $queried_obj);
              }
             *
             */

            if ($queried_obj === NULL) {
                $this->storage->unset_val($key);
            } else {
                $this->storage->set_val($key, $queried_obj);
            }

            return $queried_obj;
        }

        //ajax + wp_cron
        public function cache_count_data_clear() {
            //WOOF_HELPER::log('cache_count_data_clear ' . date('d-m-Y H:i:s'));
            global $wpdb;
            $wpdb->query("TRUNCATE TABLE " . self::$query_cache_table);
            //wp_die('done');
        }

        //ajax only
        public function woof_cache_terms_clear() {
            global $wpdb;
            $res = $wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE '_transient_woof_terms_cache_%'");

            if (!empty($res)) {
                foreach ($res as $transient) {
                    delete_transient(str_replace('_transient_', '', $transient->option_name));
                }
            }

            //wp_die('done');
        }

        //Display Product for WooCommerce compatibility
        public function woof_modify_query_args($query_args) {

            if (isset($_REQUEST[$this->get_swoof_search_slug()])) {
                if (isset($_REQUEST['woof_wp_query_args'])) {
                    $query_args['meta_query'] = $_REQUEST['woof_wp_query_args']['meta_query'];
                    $query_args['tax_query'] = $_REQUEST['woof_wp_query_args']['tax_query'];
                    $query_args['paged'] = $_REQUEST['woof_wp_query_args']['paged'];
                }
            }

            return $query_args;
        }

        public function get_custom_ext_path($relative = '') {
            if (!isset($this->settings['custom_extensions_path'])) {
                return null;
            }

            //***

            if (!empty($relative)) {
                $relative = trim($relative, DIRECTORY_SEPARATOR);
                $relative .= DIRECTORY_SEPARATOR;
            }
            return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->settings['custom_extensions_path'] . DIRECTORY_SEPARATOR . $relative;
        }

        public function get_custom_ext_link($relative = '') {
            if (!empty($relative)) {
                $relative = trim($relative, DIRECTORY_SEPARATOR);
                $relative .= DIRECTORY_SEPARATOR;
            }
            return WP_CONTENT_URL . DIRECTORY_SEPARATOR . $this->settings['custom_extensions_path'] . DIRECTORY_SEPARATOR . $relative;
        }

        public function get_ext_directories() {
            $directories = array();
            $directories['default'] = glob(WOOF_EXT_PATH . '*', GLOB_ONLYDIR);
            $directories['custom'] = array();
            if (isset($this->settings['custom_extensions_path']) AND ! empty($this->settings['custom_extensions_path'])) {
                if ($this->get_custom_ext_path()) {
                    $directories['custom'] = glob($this->get_custom_ext_path() . '*', GLOB_ONLYDIR);
                }
            }
            return $directories;
        }

        //initialization of extensions
        public function init_extensions() {

            $directories = $this->get_ext_directories();
            if (isset($this->settings['activated_extensions'])) {
                $activated = $this->settings['activated_extensions'];
            } else {
                $activated = array();
            }


            //for extensions defined by user (custom ext)
            if (!empty($directories['custom']) AND is_array($directories['custom'])) {
                if (!is_array($activated)) {
                    $activated = array();
                }

                foreach ($directories['custom'] as $path) {
                    //if (in_array(md5($path), $activated))
                    if (WOOF_EXT::is_ext_activated($path)) {
                        include_once $path . DIRECTORY_SEPARATOR . 'index.php';
                    }
                }
            }


            //*** default exts
            if (!empty($directories['default']) AND is_array($directories['default'])) {
                if (!is_array($activated)) {
                    $activated = array();
                }

                foreach ($directories['default'] as $path) {
                    //if (in_array(md5($path), $activated))
                    if (WOOF_EXT::is_ext_activated($path)) {
                        include_once $path . DIRECTORY_SEPARATOR . 'index.php';
                    }
                }
            }



            //hooked feature for extensions
            //remove_all_filters('woof_add_html_types');
            $this->html_types = apply_filters('woof_add_html_types', $this->html_types);
            //hooked feature for extensions
            //remove_all_filters('woof_add_items_keys');
            $this->items_keys = apply_filters('woof_add_items_keys', $this->items_keys);
        }

        //ajax
        public function woof_remove_ext() {
            if (!current_user_can('manage_woocommerce') OR ! current_user_can('activate_plugins')) {
                return;
            }
            check_ajax_referer('rm-ext-nonce', 'rm_ext_nonce');

            if (!wp_verify_nonce($_REQUEST['rm_ext_nonce'], 'rm-ext-nonce'))
                die('Stop!');
            //***

            $idx = $_REQUEST['idx'];


            $directories = array();
            if ($this->get_custom_ext_path()) {
                $directories = glob($this->get_custom_ext_path() . '*', GLOB_ONLYDIR);
            }
            if (!empty($directories)) {
                foreach ($directories as $dir) {

                    if (WOOF_EXT::get_ext_idx($dir) == $idx) {

                        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                        foreach ($files as $file) {
                            if ($file->isDir()) {
                                rmdir($file->getRealPath());
                            } else {
                                unlink($file->getRealPath());
                            }
                        }
                        rmdir($dir);
                    }
                }
                die('done');
            }
            die('fail');
        }

        function woof_upload_ext() {
            if (!current_user_can('manage_woocommerce') OR ! current_user_can('activate_plugins')) {
                return;
            }

            check_ajax_referer('add-ext-nonce', 'extnonce');

            if (!wp_verify_nonce($_REQUEST['extnonce'], 'add-ext-nonce'))
                die('Stop!');

            require(WOOF_PATH . 'lib/simple-ajax-uploader/extras/Uploader.php');

            $upload_dir = $_SERVER['HTTP_LOCATION'];
            $valid_extensions = array('zip');

            $Upload = new FileUpload('uploadfile');
            //$ext = $Upload->getExtension(); // Get the extension of the uploaded file
            //$Upload->newFileName = 'customFileName.'.$ext;
            $result = $Upload->handleUpload($upload_dir, $valid_extensions);

            //***

            $zipArchive = new ZipArchive();
            $zip_result = $zipArchive->open($Upload->getSavedFile());
            $ext_info = array();
            if ($zip_result === TRUE) {
                $zipArchive->extractTo($upload_dir);
                $zipArchive->close();
                $dir = $upload_dir . str_replace('.zip', '', $Upload->getFileName());
                //$ext_info = parse_ini_file($dir . '/info.dat');
                $ext_info = WOOF_HELPER::parse_ext_data($dir . '/info.dat');
                $ext_info['idx'] = md5($dir);
                unlink($Upload->getSavedFile());
            }

            if (!$result) {
                die(json_encode(array('success' => false, 'msg' => $Upload->getErrorMsg())));
            } else {
                die(json_encode(array('success' => true, 'ext_info' => $ext_info)));
            }
        }

        public function is_permalink_activated() {
            return get_option('permalink_structure', '');
        }

        public function get_option($key, $default = 0) {
            $res = $default;
            if (isset($this->settings[$key])) {
                $res = $this->settings[$key];
            }

            return $res;
        }

        private function is_should_init() {

            if (is_admin()) {
                return true;
            }

            //stop loading the plugins filters and its another functionality on all pages of the site
            if (isset($this->settings['init_only_on']) AND ! empty($this->settings['init_only_on'])) {
                $links = explode(PHP_EOL, trim($this->settings['init_only_on']));
                $server_link = '';
                //print_r($_SERVER);
                if (isset($_SERVER['SCRIPT_URI'])) {
                    $server_link = $_SERVER['SCRIPT_URI'];
                } else {
                    if (isset($_SERVER['REQUEST_URI'])) {
                        $server_link = site_url() . $_SERVER['REQUEST_URI'];
                    }
                }
                //***
                $removeChar = ["https://", "http://", "/"];
                if (!empty($server_link)) {
                    $server_link_mask = str_replace($removeChar, '', trim(stripcslashes($server_link), " /"));

                    //echo stripcslashes($server_link_mask . '<br />');
                    foreach ($links as $key => $pattern_url) {
                        $pattern_url = str_replace($removeChar, '', trim(stripcslashes($pattern_url), " /"));
                        preg_match('/(.+)?' . trim($pattern_url) . '(.+)?/', $server_link_mask, $matches);
                        //print_r($matches);
                        //exit;
                        $init = !empty($matches);
                        if (isset($this->settings['init_only_on_reverse']) AND $this->settings['init_only_on_reverse']) {
                            $init = !$init;
                        }
//                        if($_SERVER['REQUEST_URI']=="/"){
//                            $this->is_activated = true;
//                            return true;
//                        }
                        if ($init) {
                            $this->is_activated = true;
                            return true;
                        }
                    }
                }
            } else {
                return true;
            }


            return false;
        }

        public function render_html($pagepath, $data = array()) {
            if (isset($data['pagepath'])) {
                unset($data['pagepath']);
            }
            if (is_array($data) AND ! empty($data)) {
                extract($data);
            }

            $pagepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath);
            ob_start();
            include($pagepath);
            return ob_get_clean();
        }

        //******************************************* FEATURES SUGGESTIONS ***************************************************
        public function init_marketig_woof() {
            $alert = new WOOF_ADV();
            $alert->init();
        }

        //**********************************************************************************************

        public function woof_sort_terms_is_checked($terms = array(), $type = "checkbox") {
            if (!is_array($terms)) {
                $terms = array();
            }
            $not_sort_terms = apply_filters('woof_not_sort_checked_terms', array('slider'));

            if (in_array($type, $not_sort_terms)) {
                return $terms;
            }

            $request = $this->get_request_data();
            $temp_term = current($terms);
            if (!is_array($temp_term))
                return $terms;
            if ($this->is_isset_in_request_data($temp_term['taxonomy'])) {
                $current_request = $request[$temp_term['taxonomy']];
                $current_request = explode(',', urldecode($current_request));
            } else {
                return $terms;
            }
            $temp_array = array();
            foreach ($terms as $key => $val) {

                if (in_array($val['slug'], $current_request)) {
                    $temp_array[$key] = $val;
                }
            }
            foreach ($temp_array as $key => $val) {
                unset($terms[$key]);
            }
            return array_merge($temp_array, $terms);
        }

        //***
        // woo3.3
        public function set_loop_properties($query, $columns) {
            wc_set_loop_prop('is_paginated', true);
            wc_set_loop_prop('total_pages', $query->max_num_pages);
            wc_set_loop_prop('current_page', (int) max(1, $query->get('paged', 1)));
            wc_set_loop_prop('per_page', (int) $query->get('posts_per_page'));
            wc_set_loop_prop('total', (int) $query->found_posts);
            wc_set_loop_prop('columns', $columns);
            wc_set_loop_prop('is_filtered', true);
            // wc_set_loop_prop( 'name','products');
            //wc_set_loop_prop( 'is_shortcode',true);
        }

        public function product_visibility_not_in($tax_query, $keys) {
            $arr_ads = wc_get_product_visibility_term_ids();
            $product_not_in = array();
            if (!is_array($keys)) {
                $keys = array($keys);
            }
            foreach ($keys as $key) {
                if (isset($arr_ads[$key]) OR ! empty($arr_ads[$key])) {
                    $product_not_in[] = $arr_ads[$key];
                }
            }
            if (!empty($product_not_in)) {
                $tax_query[] = array(
                    'taxonomy' => 'product_visibility',
                    'field' => 'term_taxonomy_id',
                    'terms' => $product_not_in,
                    'operator' => 'NOT IN',
                );
            }

            return $tax_query;
        }

        public function woof_overide_template($template, $template_name, $template_path) {
            if ($template_name == 'loop/no-products-found.php') {
                if (isset($this->settings['override_no_products']) AND ! empty($this->settings['override_no_products'])) {
                    $_REQUEST['override_no_products'] = 1;
                    $template = WOOF_PATH . 'views/no-products-found.php';
                }
            }
            return $template;
        }

        public function generate_visibility_keys($search = false) {
            $keys = array();
            if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
                $keys[] = 'outofstock';
            }
            if ($this->get_option('listen_catalog_visibility')) {
                $keys[] = 'exclude-from-search';
                if (!$search) {
                    $keys[] = 'exclude-from-catalog';
                }
            }
            return $keys;
        }

        public function product_visibility_for_parse_query() {
            add_filter('woocommerce_product_query_tax_query', function($tax_query, $_this) {
                foreach ($tax_query as $key => $tax) {
                    if (isset($tax['taxonomy']) AND $tax['taxonomy'] == 'product_visibility') {
                        unset($tax_query[$key]);
                    }
                }
                $tax_query = $this->product_visibility_not_in($tax_query, $this->generate_visibility_keys(true));
                return $tax_query;
            }, 10, 2);
            add_filter('woocommerce_product_is_visible', function($visible, $id) {
                return true;
            }, 10, 2);
        }

        //+++

        public function check_shortcode($tag = "", $text = "") {
            $tags = array(
                'products_woof',
                'recent_products_woof',
                'sale_products_woof',
                'best_selling_products_woof',
                'top_rated_products_woof',
                'featured_products_woof',
                $tag
            );

            $pattern = get_shortcode_regex($tags);
            preg_match_all("/$pattern/", $text, $matches);
            if (isset($matches[0][0]) AND ! empty($matches[0][0])) {
                return $matches[0][0];
            } else {
                return "";
            }
        }

        public function get_wppp_per_page() {
            $per_page = 12;
            if (isset($_REQUEST['wppp_ppp'])) {
                $per_page = intval($_REQUEST['wppp_ppp']);
            } elseif (isset($_REQUEST['ppp'])) {
                $per_page = intval($_REQUEST['ppp']);
            } elseif (isset($_COOKIE['woocommerce_products_per_page'])) {
                $per_page = $_COOKIE['woocommerce_products_per_page'];
            } else {
                $per_page = intval(get_option('wppp_default_ppp', '12'));
            }
            return $per_page;
        }

        public function activate_woo_shortcodes() {
            $shortcodes = array(
                'products',
                'recent_products',
                'sale_products',
                'best_selling_products',
                'top_rated_products',
                'featured_products',
            );
            foreach ($shortcodes as $tag) {
                add_shortcode($tag . "_woof", array($this, 'woof_ajax_shortcode'));
                add_action('woocommerce_shortcode_' . $tag . '_loop_no_results', function() {
                    do_action('woocommerce_no_products_found');
                }, 10, 1);
            }
        }

        public function woof_ajax_shortcode($atts, $content, $tag) {
            $attr_str = "";
            if (is_array($atts)) {
                foreach ($atts as $key => $val) {
                    if (is_int($key)) {
                        $attr_str .= " " . $val;
                    } else {
                        $attr_str .= sprintf(" %s='%s'", $key, $val);
                    }
                }
            }
            $shortcode = str_replace("_woof", "", $tag);
            ob_start();
            ?>

            <div id="woof_results_by_ajax" class="woof_results_by_ajax_shortcode" data-shortcode="<?php echo $tag . $attr_str ?>" >
                <?php
                echo do_shortcode("[" . $shortcode . $attr_str . " ]");
                ?>
            </div>
            <?php
            return ob_get_clean();
        }

        public function sort_terms_before_out($terms, $type) {
            //var_dump(count($terms));
            // return $terms;
            if (!is_array($terms)) {
                $terms = array();
            }
            $term = reset($terms);
            if (!$term) {
                return $terms;
            }
            $tax = $term["taxonomy"];
            $orberby = -1;
            $order = "ASC";
            if (isset($this->settings['orderby'][$tax])) {
                $orberby = $this->settings['orderby'][$tax];
            }
            if (isset($this->settings['order'][$tax])) {
                $orber = $this->settings['order'][$tax];
            }
            if ($orberby != -1) {
                switch ($orberby) {
                    case'id':
                        if ($orber == 'ASC') {
                            uasort($terms, function($a, $b) {
                                if ((int) $a['term_id'] == (int) $b['term_id']) {
                                    return 0;
                                }
                                return ((int) $a['term_id'] < (int) $b['term_id']) ? -1 : 1;
                            });
                        } else {
                            uasort($terms, function($a, $b) {
                                if ((int) $a['term_id'] == (int) $b['term_id']) {
                                    return 0;
                                }
                                return ((int) $a['term_id'] > (int) $b['term_id']) ? -1 : 1;
                            });
                        }
                        break;
                    case'name':
                        if ($orber == 'ASC') {
                            uasort($terms, function($a, $b) {
                                return strnatcasecmp($a['name'], $b['name']);
                            });
                        } else {
                            uasort($terms, function($a, $b) {

                                return strnatcasecmp($b['name'], $a['name']);
                            });
                        }

                        break;
                    case'numeric':
                        if ($orber == 'ASC') {
                            uasort($terms, function($a, $b) {
                                if ((int) $a['slug'] == (int) $b['slug']) {
                                    return 0;
                                }
                                return ((int) $a['slug'] < (int) $b['slug']) ? -1 : 1;
                            });
                        } else {
                            uasort($terms, function($a, $b) {
                                if ((int) $a['slug'] == (int) $b['slug']) {
                                    return 0;
                                }
                                return ((int) $a['slug'] > (int) $b['slug']) ? -1 : 1;
                            });
                        }

                        break;
                }
            }

            return $terms;
        }

        public function change_query_tax_relations($logic_array) {
            $logic_arr = array();
            if (isset($this->settings['comparison_logic'])) {
                $logic_arr = $this->settings['comparison_logic'];
            }
            foreach ($logic_arr as $cat => $logic) {
                if ($logic == 'AND' OR $logic == 'NOT IN') {
                    $logic_array[$cat] = $logic;
                }
            }
            return $logic_array;
        }

        public function replacing_template_loop_product_thumbnail() {
            $show = 0;
            if (isset($this->settings['show_images_by_attr_show'])) {
                $show = $this->settings['show_images_by_attr_show'];
            }
            if ($show) {
                // Remove product images from the shop loop
                remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
                // Adding something instead
                add_action('woocommerce_before_shop_loop_item_title', array($this, 'wc_template_loop_product_replaced_thumb'), 10);
            }
        }

        public function wc_template_loop_product_replaced_thumb() {
            global $product;
            $needed = array();
            if (isset($this->settings['show_images_by_attr'])) {
                $needed = $this->settings['show_images_by_attr'];
            }

            if (is_array($needed) AND count($needed)) {
                if ($this->is_isset_in_request_data($this->get_swoof_search_slug()) AND $product->is_type("variable")) {

                    $need_array = array();
                    $request = $this->get_request_data();

                    $need_array = array_intersect_key($request, array_flip($needed));
                    $rate = array();
                    if (count($need_array)) {
                        $variations = $product->get_available_variations();

                        foreach ($variations as $key => $variant) {
                            if (isset($variant['attributes'])) {
                                $rate[$key] = 0;
                                foreach ($need_array as $attr_name => $values) {
                                    if (isset($variant['attributes']["attribute_" . $attr_name]) AND in_array($variant['attributes']["attribute_" . $attr_name], explode(",", $values))) {
                                        $rate[$key] ++;
                                    }
                                }
                            }
                        }

                        arsort($rate);

                        $attr_key = array_key_first($rate);
                        if (array_shift($rate)) {
                            if (isset($variations[$attr_key]["image_id"]) AND $variations[$attr_key]["image_id"]) {
                                $image_size = apply_filters('single_product_archive_thumbnail_size', 'woocommerce_thumbnail');
                                $image = wp_get_attachment_image($variations[$attr_key]["image_id"], $image_size, false, array());
                                if ($image) {
                                    echo $image;
                                    return;
                                }
                            }
                        }
                    }
                }
            }
            echo woocommerce_get_product_thumbnail();
        }

        public function woopt_set_query_args($query_args) {
            if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
                $_REQUEST['woof_products_doing'] = 1;
                $query_args['tax_query'] = array_merge($query_args['tax_query'], $this->get_tax_query(''));
                $query_args['meta_query'] = array_merge($query_args['meta_query'], $this->get_meta_query());
                $query_args = apply_filters('woof_products_query', $query_args);

                if (isset($_GET['paged'])) {
                    $query_args['paged'] = intval($_GET['paged']);
                }
                // @codingStandardsIgnoreStart
                /*
                  if (isset($_GET['orderby'])) {
                  $ordering_args = WC()->query->get_catalog_ordering_args();
                  } else {
                  $ordering_args = WC()->query->get_catalog_ordering_args($query_args['orderby'], $query_args['order']);
                  }
                  $query_args['orderby'] = $ordering_args['orderby'];
                  $query_args['order'] = $ordering_args['order'];
                  if ($ordering_args['meta_key']) {
                  $query_args['meta_key'] = $ordering_args['meta_key'];
                  }
                 */
            }
            return $query_args;
        }

        public function parse_tax_query($tax_query) {
            $request = $this->get_request_data();
            $array_logic = $this->change_query_tax_relations(array());
            foreach ($array_logic as $key => $logic) {
                if ($logic == "NOT IN" AND isset($request[$this->check_slug($key)])) {
                    $terms = explode(",", $request[$this->check_slug($key)]);
                    $tax_query[] = array(
                        "taxonomy" => $key,
                        "terms" => $terms,
                        "field" => "slug",
                        "operator" => "NOT IN"
                    );
                }
            }
            return $tax_query;
        }

        public function check_slug($slug) {
            $array_logic = $this->change_query_tax_relations(array());
            if (isset($array_logic[$slug]) AND $array_logic[$slug] == 'NOT IN') {
                $slug = 'rev_' . $slug;
            }
            return $slug;
        }

        public function uncheck_slug($slug) {
            $slug = preg_replace("@^rev_@", '', $slug);
            return $slug;
        }

    }

//***

    if (isset($_GET['P3_NOCACHE'])) {
        //stupid trick for that who believes in P3
        return;
    }

//***

    $init_the_plugin = true;

//there is no reason to activate the plugin in wp-admin area, exept of the plugin settings page
    if (is_admin()) {
        $init_the_plugin = false;
    }

//    //compatybility  with elementor
    if (isset($_GET['action']) AND $_GET['action'] == 'elementor') {
        $init_the_plugin = true;
    }

    if (defined('DOING_AJAX')) {
        $init_the_plugin = true;
    }

    if (isset($_GET['page']) AND $_GET['page'] == 'wc-settings') {
        $init_the_plugin = true;
    }

//***
    if (isset($_SERVER['SCRIPT_URI']) AND function_exists('basename')) {
        $init_pages = array('plugins.php', 'widgets.php', 'term.php', 'edit-tags.php');
        //http://stackoverflow.com/questions/7395049/get-last-part-of-url-php
        $lastSegment = basename(parse_url($_SERVER['SCRIPT_URI'], PHP_URL_PATH));
        if (in_array($lastSegment, $init_pages)) {
            $init_the_plugin = true;
        }
    } else {
        $init_the_plugin = true;
    }
//***

    if ($init_the_plugin OR isset($_GET['woof_cron_key'])) {
        $WOOF = new WOOF();
        if ($WOOF->is_activated) {

            $GLOBALS['WOOF'] = $WOOF;
            add_action('init', array($WOOF, 'init'), 1);
        }
    }


