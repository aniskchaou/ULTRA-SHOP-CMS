<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_SLIDEOUT extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'slideout';
    public $html_type_dynamic_recount_behavior = 'none';
    //public $woof_settings = array();
    public $options = array();

    public function __construct() {
        parent::__construct();
        //$this->woof_settings = get_option('woof_settings', array());
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->folder_name . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {
        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);
        add_action('wp_footer', array($this, 'wp_footer'), 10);
        add_shortcode("woof_slideout", array($this, "woof_slideout"));

        add_action('wp_ajax_woof_slideout_shortcode_gen', array($this, 'generate_shortcode_ajax'));
//jquery.tabSlideOut
        // self::$includes['js']['woof_' . $this->folder_name] = $this->get_ext_link() . 'js/' . $this->folder_name . '.js';
        // self::$includes['js_init_functions'][$this->folder_name] = 'woof_init_' . $this->folder_name;

        $this->options = array(
            'slideout_img' => array(
                'type' => 'textinput',
                'default' => 'woo2',
                'title' => __('Image', 'woocommerce-products-filter'),
                'placeholder' => 'Select image',
                'description' => __('', 'woocommerce-products-filter')
            )
        );
    }

    public function wp_head() {
        
    }

    public function wp_footer() {
        wp_enqueue_script('woof-slideout-js', $this->get_ext_link() . 'js/jquery.tabSlideOut.js', array('jquery'));
        wp_enqueue_style('woof-slideout-tab-css', $this->get_ext_link() . 'css/jquery.tabSlideOut.css');
        wp_enqueue_style('woof-slideout-css', $this->get_ext_link() . 'css/slideout.css');
        wp_enqueue_script('woof-slideout-init', $this->get_ext_link() . 'js/slideout.js', array('jquery'));

        if (isset($this->woof_settings['woof_slideout_show']) AND $this->woof_settings['woof_slideout_show']) {
            $this->woof_settings['woof_slideout_class'] = 'woof_slideout_default';
            if (!isset($this->woof_settings['woof_slideout_width']) OR ! $this->woof_settings['woof_slideout_width']) {
                $this->woof_settings['woof_slideout_width'] = "350";
                $this->woof_settings['woof_slideout_width_t'] = "px";
            }
            $shortcode = $this->generate_shortcode($this->woof_settings, "[woof]");
            echo do_shortcode($shortcode);
        }
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-slideout">
                <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                <svg viewBox="0 0 80 60" preserveAspectRatio="none"><use xlink:href="#tabshape"></use></svg>
                <span><?php _e("Slideout", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        //***
        global $WOOF;
        $data = array();

        $data['woof_settings'] = $this->woof_settings;

        echo $WOOF->render_html($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function woof_slideout($atts, $content) {
        $image = $this->get_ext_link() . 'img' . DIRECTORY_SEPARATOR . 'filter.png';
        if (isset($this->woof_settings['woof_slideout_img']) AND $this->woof_settings['woof_slideout_img']) {
            $image = $this->woof_settings['woof_slideout_img'];
        }

        $atts = shortcode_atts(array(
            'image' => $image,
            'image_h' => (isset($this->woof_settings['woof_slideout_img_h'])) ? $this->woof_settings['woof_slideout_img_h'] : 50,
            'image_w' => (isset($this->woof_settings['woof_slideout_img_w'])) ? $this->woof_settings['woof_slideout_img_w'] : 50,
            'action' => 'click',
            'location' => 'right',
            'speed' => '100',
            'offset' => '100px',
            'onloadslideout' => true,
            'mobile_behavior' => '0',
            'width' => 'auto',
            'height' => 'auto',
            'text' => __('Filter', 'woocommerce-products-filter'),
            'class' => ""
                ), $atts);

        if (!empty($content)) {
            $atts['content'] = $content;
        } else {
            $atts['content'] = "[woof]";
        }

        global $WOOF;
        $show = true;
        if ($atts['mobile_behavior'] == 1 AND ! wp_is_mobile()) {
            $show = false;
        }
        if ($atts['mobile_behavior'] == 2 AND wp_is_mobile()) {
            $show = false;
        }
        if ($show) {

            if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_slideout.php')) {
                return $WOOF->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_slideout.php', $atts);
            }
            return $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_slideout.php', $atts);
        } else {
            return "";
        }
    }

    public function generate_shortcode($attr, $content = "") {
        if (empty($content)) {
            //$content=__('Content...', 'woocommerce-products-filter'); 
        }

        $deff_attr = array(
            'woof_slideout_img' => "image=",
            'woof_slideout_img_h' => "image_h=",
            'woof_slideout_img_w' => "image_w=",
            'woof_slideout_position' => "location=",
            'woof_slideout_speed' => "speed=",
            'woof_slideout_action' => "action=",
            'woof_slideout_offset' => "offset=",
            'woof_slideout_open' => "onloadslideout=",
            'woof_slideout_mobile' => "mobile_behavior=",
            'woof_slideout_height' => "height=",
            'woof_slideout_width' => "width=",
            'woof_slideout_class' => "class="
        );

        if (isset($attr['woof_slideout_type_btn']) AND $attr['woof_slideout_type_btn'] == 1) {
            $attr['woof_slideout_img'] = 'null';
            $deff_attr['woof_slideout_txt'] = "text=";
        }

        foreach ($deff_attr as $key => $data) {
            if (isset($attr[$key]) AND ! empty($attr[$key])) {
                $deff_attr[$key] .= $attr[$key];
                if ($key == "woof_slideout_offset") {
                    $type = "px";
                    if (isset($attr[$key . "_t"]) AND ! empty($attr[$key] . "_t")) {
                        $type = $attr[$key . "_t"];
                    }
                    $deff_attr[$key] .= $type;
                }
                if ($key == "woof_slideout_width") {
                    $type = "px";
                    if (isset($attr[$key . "_t"]) AND ! empty($attr[$key] . "_t")) {
                        $type = $attr[$key . "_t"];
                    }
                    $deff_attr[$key] .= $type;
                }
                if ($key == "woof_slideout_height") {
                    $type = "px";
                    if (isset($attr[$key . "_t"]) AND ! empty($attr[$key] . "_t")) {
                        $type = $attr[$key . "_t"];
                    }
                    $deff_attr[$key] .= $type;
                }
            } else {
                unset($deff_attr[$key]);
            }
        }

        return "[woof_slideout " . implode(" ", $deff_attr) . " ]" . $content . "[/woof_slideout]";
    }

    public function generate_shortcode_ajax() {

        $shortcode = $this->generate_shortcode($_POST);
        die($shortcode);
    }

}

WOOF_EXT::$includes['applications']['slideout'] = new WOOF_SLIDEOUT();

