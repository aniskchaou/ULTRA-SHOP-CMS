<?php

if (!class_exists('SNP_NHP_Options')) {
    if (!defined('SNP_NHP_OPTIONS_DIR')) {
        define('SNP_NHP_OPTIONS_DIR', trailingslashit(plugin_dir_path(__FILE__)));
    }

    if (!defined('SNP_NHP_OPTIONS_URL')) {
        define('SNP_NHP_OPTIONS_URL', plugins_url('/', __FILE__));
    }

    class SNP_NHP_Options
    {
        public $framework_url        = 'http://arscode.pro/';
        public $framework_version    = '1.0.5';
        public $dir          = SNP_NHP_OPTIONS_DIR;
        public $url          = SNP_NHP_OPTIONS_URL;
        public $page             = '';
        public $args             = array();
        public $sections = array();
        public $customfields = array();
        public $VCB_Sizes = array();
        public $VCB_Elements = array();
        public $extra_tabs = array();
        public $errors = array();
        public $warnings = array();
        public $options = array();

        /**
         * Class Constructor. Defines the args for the theme options class
         *
         * @since NHP_Options 1.0
         *
         * @param $array $args Arguments. Class constructor arguments.
         */
        function __construct($sections = array(), $args = array(), $extra_tabs = array(), $customfields = array(), $VCB_Sizes = array(), $VCB_Elements = array())
        {
            $defaults = array();

            $defaults['opt_name'] = ''; //must be defined by theme/plugin

            $defaults['menu_icon']       = SNP_NHP_OPTIONS_URL . '/img/menu_icon.png';
            $defaults['menu_title']      = __('Options', 'nhp-opts');
            $defaults['page_icon']       = 'icon-themes';
            $defaults['page_title']      = __('Options', 'nhp-opts');
            $defaults['page_slug']       = '_options';
            $defaults['page_cap']        = 'manage_options';
            $defaults['page_type']       = 'menu';
            $defaults['page_parent']     = '';
            $defaults['page_position']   = 100;

            $defaults['show_import_export']  = true;
            $defaults['dev_mode']        = true;
            $defaults['stylesheet_override'] = false;

            $defaults['footer_credit'] = '';

            $defaults['help_tabs'] = array();
            $defaults['help_sidebar'] = __('', 'nhp-opts');

            //get args
            $this->args = wp_parse_args($args, $defaults);
            $this->args = apply_filters('nhp-opts-args', $this->args);
            $this->args = apply_filters('nhp-opts-args-' . $this->args['opt_name'], $this->args);

            //get sections
            $this->sections = apply_filters('nhp-opts-sections', $sections);
            $this->sections = apply_filters('nhp-opts-sections-' . $this->args['opt_name'], $this->sections);

            //get extra tabs
            $this->extra_tabs = apply_filters('nhp-opts-extra-tabs', $extra_tabs);
            $this->extra_tabs = apply_filters('nhp-opts-extra-tabs-' . $this->args['opt_name'], $this->extra_tabs);

            //get customfields
            $this->customfields = apply_filters('nhp-opts-customfields', $customfields);
            $this->customfields = apply_filters('nhp-opts-customfields-' . $this->args['opt_name'], $this->customfields);

            $this->VCB_Sizes = $VCB_Sizes;
            $this->VCB_Elements = $VCB_Elements;

            //set option with custom fields
            add_action('admin_menu', array(&$this, '_set_custom_fields'));
            add_action('save_post', array(&$this, '_save_custom_fields_postdata'));

            //set option with defaults
            add_action('init', array(&$this, '_set_default_options'));

            add_action('admin_init', array(&$this, '_enqueue_css_js'));
            //add_action('admin_init', array(&$this, '_enqueue'));
            add_action( 'admin_enqueue_scripts', array(&$this, '_enqueue'), 10, 1 );
            //options page
            add_action('admin_menu', array(&$this, '_options_page'));

            //register setting
            add_action('admin_init', array(&$this, '_register_setting'));

            //add the js for the error handling before the form
            add_action('nhp-opts-page-before-form', array(&$this, '_errors_js'), 1);

            //add the js for the warning handling before the form
            add_action('nhp-opts-page-before-form', array(&$this, '_warnings_js'), 2);

            //hook into the wp feeds for downloading the exported settings
            add_action('do_feed_nhpopts', array(&$this, '_download_options'), 1, 1);

            //get the options for use later on
            $this->options = get_option($this->args['opt_name']);
        }

        /**
         * ->get(); This is used to return and option value from the options array
         *
         * @since NHP_Options 1.0.1
         *
         * @param $array $args Arguments. Class constructor arguments.
         */
        function get($opt_name, $default = null)
        {
            return (!empty($this->options[$opt_name])) ? $this->options[$opt_name] : $default;
        }

        /**
         * ->set(); This is used to set an arbitrary option in the options array
         *
         * @since NHP_Options 1.0.1
         * 
         * @param string $opt_name the name of the option being added
         * @param mixed $value the value of the option being added
         */
        function set($opt_name, $value)
        {
            $this->options[$opt_name] = $value;

            update_option($this->args['opt_name'], $this->options);
        }

        /**
         * ->show(); This is used to echo and option value from the options array
         *
         * @since NHP_Options 1.0.1
         *
         * @param $array $args Arguments. Class constructor arguments.
         */
        function show($opt_name)
        {
            $option = $this->get($opt_name);
            if (!is_array($option)) {
                echo $option;
            }
        }

        /**
         * Get default options into an array suitable for the settings API
         *
         * @since NHP_Options 1.0
         *
         */
        function _default_values()
        {
            $defaults = array();

            foreach ($this->sections as $k => $section) {
                if (isset($section['fields'])) {
                    foreach ($section['fields'] as $fieldk => $field) {
                        if (!isset($field['std'])) {
                            $field['std'] = '';
                        }

                        $defaults[$field['id']]  = $field['std'];
                    }
                }
            }

            $defaults['last_tab'] = 0;

            return $defaults;
        }

        /**
         * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
         *
         * @since NHP_Options 1.0
         *
         */
        function _set_default_options()
        {
            if (!get_option($this->args['opt_name'])) {
                add_option($this->args['opt_name'], $this->_default_values());
            }

            $this->options = get_option($this->args['opt_name']);
        }

        /**
         * Class Theme Options Page Function, creates main options page.
         *
         * @since NHP_Options 1.0
         */
        function _options_page()
        {
            if ($this->args['page_type'] == 'submenu') {
                if (!isset($this->args['page_parent']) || empty($this->args['page_parent'])) {
                    $this->args['page_parent']   = 'themes.php';
                }

                $this->page = add_submenu_page(
                    $this->args['page_parent'],
                    $this->args['page_title'],
                    $this->args['menu_title'],
                    $this->args['page_cap'],
                    $this->args['page_slug'],
                    array(&$this, '_options_page_html')
                );
            } else {
                $this->page = add_menu_page(
                    $this->args['page_title'],
                    $this->args['menu_title'],
                    $this->args['page_cap'],
                    $this->args['page_slug'],
                    array(&$this, '_options_page_html'),
                    $this->args['menu_icon'],
                    $this->args['page_position']
                );
            }

            add_action('load-' . $this->page, array(&$this, '_load_page'));
        }

        function _enqueue_css_js() {
            wp_register_style(
                'snp-nhp-opts-css', $this->url . 'css/options.css', array('farbtastic'), time(), 'all'
            );
            wp_register_style(
                'snp-nhp-opts-css-snp', $this->url . 'css/snp.css', '', time(), 'all'
            );
            wp_register_style(
                'snp-nhp-opts-jquery-ui-css', apply_filters('nhp-opts-ui-theme', $this->url . 'css/jquery-ui-aristo/aristo.css'), '', time(), 'all'
            );      
            wp_register_style(
                'snp-nhp-opts-colorpicker-css', apply_filters('nhp-opts-ui-theme', $this->url . 'css/colorpicker.css'), '', time(), 'all'
            );
            wp_enqueue_style('snp-nhp-opts-colorpicker-css');

            if (false === $this->args['stylesheet_override']) {
                wp_enqueue_style('snp-nhp-opts-css');
            }
            wp_enqueue_style('snp-nhp-opts-css-snp');

            wp_enqueue_script(
                'snp-nhp-opts-js', $this->url . 'js/options.js', array('jquery'), time(), true
            );
            wp_enqueue_script(
                'snp-nhp-opts-js-colorpicker', $this->url . 'js/colorpicker.js', array('jquery'), time(), true
            );
        }

        /**
         * enqueue styles/js for theme page
         *
         * @since NHP_Options 1.0
         */
        function _enqueue($hook)
        {
            global $post;

            $ex = array(
                'sendlane_lists',
                'customerio_lists',
                'mailrelay_lists',
                'mailup_lists',
                'ontraport_lists',
                'newsletter_lists',
                'sendreach_lists',
                'sendpulse_lists',
                'salesmanago_lists',
                'mailjet_lists',
                'benchmarkemail_lists',
                'sendgrid_lists',
                'agilecrm_lists',
                'elasticemail_lists',
                'salesautopilot_lists',
                'myemma_lists',
                'mailerlite_lists',
                'activecampaign_fields',
                'rocketresponder_lists',
                'freshmail_lists',
                'infusionsoft_lists',
                'icontact_lists',
                'mailchimp_lists',
                'sendinblue_lists',
                'sendpress_lists',
                'egoi_lists',
                'getresponse_lists',
                'wysija_lists',
                'campaignmonitor_lists',
                'aweber_lists',
                'mymail_lists',
                'mailster_lists',
                'madmimi_lists',
                'constantcontact_lists',
                'aweber_auth',
                'hubspot_lists',
                'convertkit_lists',
                'enewsletter_lists',
                'campaignercsv_lists',
                'campaigner_lists',
                'sgautorepondeur_lists',
                'kirim_lists',
                'mautic_auth',
                'mautic_owner',
                'mautic_stage',
                'mautic_segment',
                'drip_campaigns',
                'apsis_lists',
                'zoho_auth',
                'zoho_campaigns',
                'zoho_fields',
                'mailfit_lists',
                'ngpvan_contacts',
            );

            if ($hook == 'post-new.php' || $hook == 'post.php' || $hook == 'snp_popups_page_snp_opt') {
                if ($hook == 'snp_popups_page_snp_opt' || ($post && 'snp_popups' === $post->post_type)) {
                    $fields_dir  = @ opendir($this->dir . 'fields/');
                    while (($field_dir   = readdir($fields_dir)) !== false) {
                        if ($field_dir[0] == '.') {
                            continue;
                        }
                        
                        if (($hook == 'post-new.php' || $hook == 'post.php') && in_array($field_dir, $ex)) {
                            continue;   
                        }

                        if (is_dir($this->dir . 'fields/' . $field_dir) && is_readable($this->dir . 'fields/' . $field_dir)) {
                            $field_class = 'SNP_NHP_Options_' . $field_dir;
                            if (!class_exists($field_class)) {
                                require_once($this->dir . 'fields/' . $field_dir . '/field_' . $field_dir . '.php');
                            }//if

                            if (class_exists($field_class) && method_exists($field_class, 'enqueue')) {
                                $enqueue = new $field_class('', '', $this);
                                $enqueue->enqueue();
                            }
                        }
                    }
                }
            }
        }

        /**
         * Download the options file, or display it
         *
         * @since NHP_Options 1.0.1
         */
        function _download_options()
        {
            if (!isset($_GET['secret']) || $_GET['secret'] != md5(AUTH_KEY . SECURE_AUTH_KEY)) {
                wp_die('Invalid Secret for options use');
                exit;
            }

            if (!isset($_GET['option'])) {
                wp_die('No Option Defined');
                exit;
            }

            $backup_options = get_option($_GET['option']);
            $backup_options['nhp-opts-backup']   = '1';
            $content = '###' . serialize($backup_options) . '###';

            if (isset($_GET['action']) && $_GET['action'] == 'download_options') {
                header('Content-Description: File Transfer');
                header('Content-type: application/txt');
                header('Content-Disposition: attachment; filename="' . $_GET['option'] . '_options_' . date('d-m-Y') . '.txt"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                echo $content;
                exit;
            } else {
                echo $content;
                exit;
            }
        }

        /**
         * show page help
         *
         * @since NHP_Options 1.0
         */
        function _load_page()
        {
            //do admin head action for this page
            add_action('admin_head', array(&$this, 'admin_head'));

            //do admin footer text hook
            add_filter('admin_footer_text', array(&$this, 'admin_footer_text'));

            $screen = get_current_screen();

            if (is_array($this->args['help_tabs'])) {
                foreach ($this->args['help_tabs'] as $tab) {
                    $screen->add_help_tab($tab);
                }
            }

            if ($this->args['help_sidebar'] != '') {
                $screen->set_help_sidebar($this->args['help_sidebar']);
            }

            do_action('nhp-opts-load-page', $screen);

            do_action('nhp-opts-load-page-' . $this->args['opt_name'], $screen);
        }

        /**
         * do action nhp-opts-admin-head for theme options page
         *
         * @since NHP_Options 1.0
         */
        function admin_head()
        {
            do_action('nhp-opts-admin-head', $this);
            do_action('nhp-opts-admin-head-' . $this->args['opt_name'], $this);
        }

        function admin_footer_text($footer_text)
        {
            return $this->args['footer_credit'];
        }

        /**
         * Register Option for use
         *
         * @since NHP_Options 1.0
         */
        function _register_setting()
        {
            register_setting($this->args['opt_name'] . '_group', $this->args['opt_name'], array(&$this, '_validate_options'));

            foreach ($this->sections as $k => $section) {
                add_settings_section($k . '_snp_section', $section['title'], array(&$this, '_section_desc'), $k . '_snp_section_group');

                if (isset($section['fields'])) {
                    foreach ($section['fields'] as $fieldk => $field) {
                        if (isset($field['title'])) {
                            $th = (isset($field['sub_desc'])) ? $field['title'] . '<span class="description">' . $field['sub_desc'] . '</span>' : $field['title'];
                        } else {
                            $th = '';
                        }

                        add_settings_field($fieldk . '_field', $th, array(&$this, '_field_input'), $k . '_snp_section_group', $k . '_snp_section', $field);
                    }
                }
            }

            do_action('nhp-opts-register-settings');

            do_action('nhp-opts-register-settings-' . $this->args['opt_name']);
        }

        /**
         * Validate the Options options before insertion
         *
         * @since NHP_Options 1.0
         */
        function _validate_options($plugin_options)
        {
            set_transient('nhp-opts-saved', '1', 1000);

            if (!empty($plugin_options['import'])) {
                if ($plugin_options['import_code'] != '') {
                    $import = $plugin_options['import_code'];
                } else if ($plugin_options['import_link'] != '') {
                    $import = wp_remote_retrieve_body(wp_remote_get($plugin_options['import_link']));
                }

                $imported_options = unserialize(trim($import, '###'));
                if (is_array($imported_options) && isset($imported_options['nhp-opts-backup']) && $imported_options['nhp-opts-backup'] == '1') {
                    $imported_options['imported'] = 1;

                    return $imported_options;
                }
            }

            if (!empty($plugin_options['defaults'])) {
                $plugin_options = $this->_default_values();

                return $plugin_options;
            }

            $plugin_options = $this->_validate_values($plugin_options, $this->options);

            if ($this->errors) {
                set_transient('nhp-opts-errors', $this->errors, 1000);
            }

            if ($this->warnings) {
                set_transient('nhp-opts-warnings', $this->warnings, 1000);
            }

            do_action('nhp-opts-options-validate', $plugin_options, $this->options);
            do_action('nhp-opts-options-validate-' . $this->args['opt_name'], $plugin_options, $this->options);

            unset($plugin_options['defaults']);
            unset($plugin_options['import']);
            unset($plugin_options['import_code']);
            unset($plugin_options['import_link']);

            return $plugin_options;
        }

        /**
         * Validate values from options form (used in settings api validate function)
         * calls the custom validation class for the field so authors can override with custom classes
         *
         * @since NHP_Options 1.0
         */
        function _validate_values($plugin_options, $options)
        {
            foreach ($this->sections as $k => $section) {
                if (isset($section['fields'])) {
                    foreach ($section['fields'] as $fieldk => $field) {
                        $field['section_id'] = $k;

                        if (isset($field['type']) && $field['type'] == 'multi_text') {
                            continue;
                        }

                        if (!isset($plugin_options[$field['id']]) || $plugin_options[$field['id']] == '') {
                            continue;
                        }

                        if (isset($field['type']) && !isset($field['validate'])) {
                            if ($field['type'] == 'color' || $field['type'] == 'color_gradient') {
                                $field['validate'] = 'color';
                            } elseif ($field['type'] == 'date') {
                                $field['validate'] = 'date';
                            }
                        }

                        if (isset($field['validate'])) {
                            $validate = 'SNP_NHP_Validation_' . $field['validate'];

                            if (!class_exists($validate)) {
                                require_once($this->dir . 'validation/' . $field['validate'] . '/validation_' . $field['validate'] . '.php');
                            }

                            if (class_exists($validate)) {
                                $validation = new $validate($field, $plugin_options[$field['id']], $options[$field['id']]);
                                $plugin_options[$field['id']] = $validation->value;

                                if (isset($validation->error)) {
                                    $this->errors[] = $validation->error;
                                }

                                if (isset($validation->warning)) {
                                    $this->warnings[] = $validation->warning;
                                }

                                continue;
                            }
                        }

                        if (isset($field['validate_callback']) && function_exists($field['validate_callback'])) {
                            $callbackvalues = call_user_func($field['validate_callback'], $field, $plugin_options[$field['id']], $options[$field['id']]);
                            $plugin_options[$field['id']] = $callbackvalues['value'];

                            if (isset($callbackvalues['error'])) {
                                $this->errors[] = $callbackvalues['error'];
                            }

                            if (isset($callbackvalues['warning'])) {
                                $this->warnings[] = $callbackvalues['warning'];
                            }
                        }
                    }
                }
            }

            return $plugin_options;
        }

        /**
         * HTML OUTPUT.
         *
         * @since NHP_Options 1.0
         */
        function _options_page_html()
        {
            echo '<div class="wrap">';
            echo '<div id="' . $this->args['page_icon'] . '" class="icon32"><br/></div>';
            echo '<h2 id="nhp-opts-heading">' . get_admin_page_title() . '</h2>';
            echo (isset($this->args['intro_text'])) ? $this->args['intro_text'] : '';

            do_action('nhp-opts-page-before-form');

            do_action('nhp-opts-page-before-form-' . $this->args['opt_name']);

            //if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            //    echo '<span style="color: red; text-align: center;"><strong>You have installed PHP ' . PHP_VERSION . ' Ninja Pop-ups plugin requires minimum version of PHP 5.6.0. Please upgrade your PHP version</strong></span>';
            //} else {
                echo '<form method="post" action="options.php" enctype="multipart/form-data" id="nhp-opts-form-wrapper">';
                settings_fields($this->args['opt_name'] . '_group');
                if(isset($_GET['tab'])) {
                    echo '<input type="hidden" id="last_tab" name="' . $this->args['opt_name'] . '[last_tab]" value="'.$_GET['tab'].'" />';
                } else {
                    echo '<input type="hidden" id="last_tab" name="' . $this->args['opt_name'] . '[last_tab]" value="0" />';
                }

                echo '<div id="nhp-opts-header">';
                echo '<input type="submit" name="submit" value="' . __('Save Changes', 'nhp-opts') . '" class="button-primary" />';
                echo '<div class="clear"></div><!--clearfix-->';
                echo '</div>';

                if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-saved') == '1') {
                    if (isset($this->options['imported']) && $this->options['imported'] == 1) {
                        echo '<div id="nhp-opts-imported">' . __('<strong>Settings Imported!</strong>', 'nhp-opts') . '</div>';
                    } else {
                        echo '<div id="nhp-opts-save">' . __('<strong>Settings Saved!</strong>', 'nhp-opts') . '</div>';
                    }
                    delete_transient('nhp-opts-saved');
                }

                echo '<div id="nhp-opts-save-warn">' . __('<strong>Settings have changed!, you should save them!</strong>', 'nhp-opts') . '</div>';
                echo '<div id="nhp-opts-field-errors">' . __('<strong><span></span> error(s) were found!</strong>', 'nhp-opts') . '</div>';

                echo '<div id="nhp-opts-field-warnings">' . __('<strong><span></span> warning(s) were found!</strong>', 'nhp-opts') . '</div>';

                echo '<div class="clear"></div><!--clearfix-->';

                echo '<div id="nhp-opts-sidebar">';
                echo '<ul id="snp-nhp-opts-group-menu">';
                foreach ($this->sections as $k => $section) {
                    $icon = (!isset($section['icon'])) ? '<img src="' . $this->url . 'img/glyphicons/glyphicons_019_cogwheel.png" /> ' : '<img src="' . $section['icon'] . '" /> ';
                    echo '<li id="' . $k . '_snp_section_group_li" class="snp-nhp-opts-group-tab-link-li">';
                    echo '<a href="javascript:void(0);" id="' . $k . '_snp_section_group_li_a" class="snp-nhp-opts-group-tab-link-a" data-rel="' . $k . '">' . $icon . $section['title'] . '</a>';
                    echo '</li>';
                }

                echo '<li class="divide">&nbsp;</li>';

                do_action('nhp-opts-after-section-menu-items', $this);

                do_action('nhp-opts-after-section-menu-items-' . $this->args['opt_name'], $this);

                if (true === $this->args['show_import_export']) {
                    echo '<li id="import_export_default_section_group_li" class="snp-nhp-opts-group-tab-link-li">';
                    echo '<a href="javascript:void(0);" id="import_export_default_section_group_li_a" class="snp-nhp-opts-group-tab-link-a" data-rel="import_export_default"><img src="' . $this->url . 'img/glyphicons/glyphicons_082_roundabout.png" /> ' . __('Import / Export', 'nhp-opts') . '</a>';
                    echo '</li>';
                    echo '<li class="divide">&nbsp;</li>';
                }

                foreach ((array) $this->extra_tabs as $k => $tab) {
                    $icon = (!isset($tab['icon'])) ? '<img src="' . $this->url . 'img/glyphicons/glyphicons_019_cogwheel.png" /> ' : '<img src="' . $tab['icon'] . '" /> ';
                    echo '<li id="' . $k . '_snp_section_group_li" class="snp-nhp-opts-group-tab-link-li">';
                    echo '<a href="javascript:void(0);" id="' . $k . '_snp_section_group_li_a" class="snp-nhp-opts-group-tab-link-a custom-tab" data-rel="' . $k . '">' . $icon . $tab['title'] . '</a>';
                    echo '</li>';
                }

                if (true === $this->args['dev_mode']) {
                    echo '<li id="dev_mode_default_section_group_li" class="snp-nhp-opts-group-tab-link-li">';
                    echo '<a href="javascript:void(0);" id="dev_mode_default_section_group_li_a" class="snp-nhp-opts-group-tab-link-a custom-tab" data-rel="dev_mode_default"><img src="' . $this->url . 'img/glyphicons/glyphicons_195_circle_info.png" /> ' . __('Dev Mode Info', 'nhp-opts') . '</a>';
                    echo '</li>';
                }

                echo '</ul>';
                echo '</div>';

                echo '<div id="nhp-opts-main">';

                foreach ($this->sections as $k => $section) {
                    echo '<div id="' . $k . '_snp_section_group' . '" class="snp-nhp-opts-group-tab">';
                    do_settings_sections($k . '_snp_section_group');
                    echo '</div>';
                }

                if (true === $this->args['show_import_export']) {
                    echo '<div id="import_export_default_section_group' . '" class="snp-nhp-opts-group-tab">';
                    echo '<h3>' . __('Import / Export Options', 'nhp-opts') . '</h3>';

                    echo '<h4>' . __('Import Options', 'nhp-opts') . '</h4>';

                    echo '<p><a href="javascript:void(0);" id="nhp-opts-import-code-button" class="button-secondary">Import from file</a> <a href="javascript:void(0);" id="nhp-opts-import-link-button" class="button-secondary">Import from URL</a></p>';

                    echo '<div id="nhp-opts-import-code-wrapper">';

                    echo '<div class="snp-nhp-opts-section-desc">';

                    echo '<p class="description" id="import-code-description">' . apply_filters('nhp-opts-import-file-description', __('Input your backup file below and hit Import to restore your sites options from a backup.', 'nhp-opts')) . '</p>';

                    echo '</div>';


                    echo '<textarea id="import-code-value" name="' . $this->args['opt_name'] . '[import_code]" class="large-text" rows="8"></textarea>';

                    echo '</div>';

                    echo '<div id="nhp-opts-import-link-wrapper">';

                    echo '<div class="snp-nhp-opts-section-desc">';

                    echo '<p class="description" id="import-link-description">' . apply_filters('nhp-opts-import-link-description', __('Input the URL to another sites options set and hit Import to load the options from that site.', 'nhp-opts')) . '</p>';

                    echo '</div>';

                    echo '<input type="text" id="import-link-value" name="' . $this->args['opt_name'] . '[import_link]" class="large-text" value="" />';

                    echo '</div>';

                    echo '<p id="nhp-opts-import-action"><input type="submit" id="nhp-opts-import" name="' . $this->args['opt_name'] . '[import]" class="button-primary" value="' . __('Import', 'nhp-opts') . '"> <span>' . apply_filters('nhp-opts-import-warning', __('WARNING! This will overwrite any existing options, please proceed with caution!', 'nhp-opts')) . '</span></p>';
                    echo '<div id="import_divide"></div>';

                    echo '<h4>' . __('Export Options', 'nhp-opts') . '</h4>';
                    echo '<div class="snp-nhp-opts-section-desc">';
                    echo '<p class="description">' . apply_filters('nhp-opts-backup-description', __('Here you can copy/download your themes current option settings. Keep this safe as you can use it as a backup should anything go wrong. Or you can use it to restore your settings on this site (or any other site). You also have the handy option to copy the link to yours sites settings. Which you can then use to duplicate on another site', 'nhp-opts')) . '</p>';
                    echo '</div>';

                    echo '<p><a href="javascript:void(0);" id="nhp-opts-export-code-copy" class="button-secondary">Copy</a> <a href="' . snp_add_query_arg(array('feed'                  => 'nhpopts', 'action'              => 'download_options', 'secret'                 => md5(AUTH_KEY . SECURE_AUTH_KEY), 'option'                => $this->args['opt_name']), site_url()) . '" id="nhp-opts-export-code-dl" class="button-primary">Download</a> <a href="javascript:void(0);" id="nhp-opts-export-link" class="button-secondary">Copy Link</a></p>';
                    $backup_options              = $this->options;
                    $backup_options['nhp-opts-backup']   = '1';
                    $encoded_options             = '###' . serialize($backup_options) . '###';
                    echo '<textarea class="large-text" id="nhp-opts-export-code" rows="8">';
                    print_r($encoded_options);
                    echo '</textarea>';
                    echo '<input type="text" class="large-text" id="nhp-opts-export-link-value" value="' . snp_add_query_arg(array('feed'    => 'nhpopts', 'secret' => md5(AUTH_KEY . SECURE_AUTH_KEY), 'option' => $this->args['opt_name']), site_url()) . '" />';

                    echo '</div>';
                }

                foreach ((array) $this->extra_tabs as $k => $tab) {
                    echo '<div id="' . $k . '_snp_section_group' . '" class="snp-nhp-opts-group-tab">';
                    echo '<h3>' . $tab['title'] . '</h3>';
                    echo $tab['content'];
                    echo '</div>';
                }

                if (true === $this->args['dev_mode']) {
                    echo '<div id="dev_mode_default_section_group' . '" class="snp-nhp-opts-group-tab">';
                    echo '<h3>' . __('Dev Mode Info', 'nhp-opts') . '</h3>';
                    echo '<div class="snp-nhp-opts-section-desc">';
                    echo '<textarea class="large-text" rows="24">' . print_r($this, true) . '</textarea>';
                    echo '</div>';
                    echo '</div>';
                }

                do_action('nhp-opts-after-section-items', $this);

                do_action('nhp-opts-after-section-items-' . $this->args['opt_name'], $this);

                echo '<div class="clear"></div><!--clearfix-->';
                echo '</div>';
                echo '<div class="clear"></div><!--clearfix-->';

                echo '<div id="nhp-opts-footer">';

                if (isset($this->args['share_icons'])) {
                    echo '<div id="nhp-opts-share">';
                    foreach ($this->args['share_icons'] as $link) {
                        echo '<a href="' . $link['link'] . '" title="' . $link['title'] . '" target="_blank"><img src="' . $link['img'] . '"/></a>';
                    }
                    echo '</div>';
                }

                echo '<input type="submit" name="submit" value="' . __('Save Changes', 'nhp-opts') . '" class="button-primary" />';

                echo '<div class="clear"></div><!--clearfix-->';
                echo '</div>';

                echo '</form>';
            //}

            do_action('nhp-opts-page-after-form');
            do_action('nhp-opts-page-after-form-' . $this->args['opt_name']);

            echo '<div class="clear"></div><!--clearfix-->';
            echo '</div><!--wrap-->';
        }

        /**
         * JS to display the errors on the page
         *
         * @since NHP_Options 1.0
         */
        function _errors_js()
        {
            if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-errors')) {
                $errors = get_transient('nhp-opts-errors');
                $section_errors = array();
                foreach ($errors as $error) {
                    $section_errors[$error['section_id']] = (isset($section_errors[$error['section_id']])) ? $section_errors[$error['section_id']] : 0;
                    $section_errors[$error['section_id']]++;
                }

                echo '<script type="text/javascript">';
                echo 'jQuery(document).ready(function(){';
                echo 'jQuery("#nhp-opts-field-errors span").html("' . count($errors) . '");';
                echo 'jQuery("#nhp-opts-field-errors").show();';

                foreach ($section_errors as $sectionkey => $section_error) {
                    echo 'jQuery("#' . $sectionkey . '_section_group_li_a").append("<span class=\"nhp-opts-menu-error\">' . $section_error . '</span>");';
                }

                foreach ($errors as $error) {
                    echo 'jQuery("#' . $error['id'] . '").addClass("nhp-opts-field-error");';
                    echo 'jQuery("#' . $error['id'] . '").closest("td").append("<span class=\"nhp-opts-th-error\">' . $error['msg'] . '</span>");';
                }
                echo '});';
                echo '</script>';
                delete_transient('nhp-opts-errors');
            }
        }

        /**
         * JS to display the warnings on the page
         *
         * @since NHP_Options 1.0.3
         */
        function _warnings_js()
        {
            if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-warnings')) {
                $warnings = get_transient('nhp-opts-warnings');
                $section_warnings = array();
                foreach ($warnings as $warning) {
                    $section_warnings[$warning['section_id']] = (isset($section_warnings[$warning['section_id']])) ? $section_warnings[$warning['section_id']] : 0;
                    $section_warnings[$warning['section_id']]++;
                }

                echo '<script type="text/javascript">';
                echo 'jQuery(document).ready(function(){';
                echo 'jQuery("#nhp-opts-field-warnings span").html("' . count($warnings) . '");';
                echo 'jQuery("#nhp-opts-field-warnings").show();';

                foreach ($section_warnings as $sectionkey => $section_warning) {
                    echo 'jQuery("#' . $sectionkey . '_section_group_li_a").append("<span class=\"nhp-opts-menu-warning\">' . $section_warning . '</span>");';
                }

                foreach ($warnings as $warning) {
                    echo 'jQuery("#' . $warning['id'] . '").addClass("nhp-opts-field-warning");';
                    echo 'jQuery("#' . $warning['id'] . '").closest("td").append("<span class=\"nhp-opts-th-warning\">' . $warning['msg'] . '</span>");';
                }
                echo '});';
                echo '</script>';

                delete_transient('nhp-opts-warnings');
            }
        }

        /**
         * Section HTML OUTPUT.
         *
         * @since NHP_Options 1.0
         */
        function _section_desc($section)
        {
            $id = rtrim($section['id'], '_section');

            if (isset($this->sections[$id]['desc']) && !empty($this->sections[$id]['desc'])) {
                echo '<div class="snp-nhp-opts-section-desc">' . $this->sections[$id]['desc'] . '</div>';
            }
        }

        /**
         * Field HTML OUTPUT.
         *
         * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
         *
         * @since NHP_Options 1.0
         */
        function _field_input($field, $metabox = 0, $vcb = 0)
        {
            if (isset($field['callback']) && function_exists($field['callback'])) {
                $value = (isset($this->options[$field['id']])) ? $this->options[$field['id']] : '';

                do_action('nhp-opts-before-field', $field, $value);
                do_action('nhp-opts-before-field-' . $this->args['opt_name'], $field, $value);

                call_user_func($field['callback'], $field, $value);

                do_action('nhp-opts-after-field', $field, $value);
                do_action('nhp-opts-after-field-' . $this->args['opt_name'], $field, $value);

                return;
            } 

            if (isset($field['type'])) {
                $field_class = 'SNP_NHP_Options_' . $field['type'];

                if (!class_exists($field_class)) {
                    require_once($this->dir . 'fields/' . $field['type'] . '/field_' . $field['type'] . '.php');
                }

                if (class_exists($field_class)) {
                    if ($vcb == 1) {
                        $value = $field['value'];
                        if (!$value) {
                            $value = $field['std'];
                        }
                    } else if ($metabox == 1) {
                        global $post;

                        $value = get_post_meta($post->ID, $this->args['opt_name'] . '_' . $field['id'], true);
                        if (!isset($value) || $value === '') {
                            $value = (isset($field['std']) ? $field['std'] : '');
                        }
                    } else {
                        $value = (isset($this->options[$field['id']])) ? $this->options[$field['id']] : '';
                    }

                    if (!isset($field['desc'])) {
                        $field['desc'] = '';
                    }

                    if (!isset($field['vcb_id'])) {
                        $field['vcb_id'] = '';
                    }

                    if (!isset($field['vcb'])) {
                        $field['vcb'] = '';
                    }

                    do_action('nhp-opts-before-field', $field, $value);
                    do_action('nhp-opts-before-field-' . $this->args['opt_name'], $field, $value);

                    $render = '';
                    $render = new $field_class($field, $value, $this);
                    $render->render();

                    do_action('nhp-opts-after-field', $field, $value);
                    do_action('nhp-opts-after-field-' . $this->args['opt_name'], $field, $value);
                }
            }
        }

        function _set_custom_fields()
        {
            if (function_exists('add_meta_box')) {
                foreach ((array) $this->customfields as $key => $c) {
                    if (!is_array($c['post_type'])) {
                        $c['post_type'][] = $c['post_type'];
                    }
                    $c['context'] = isset($c['context']) ? $c['context'] : 'normal';
                    $c['priority'] = isset($c['priority']) ? $c['priority'] : 'default';

                    foreach ($c['post_type'] as $post_type) {
                        add_meta_box(
                            $c['id'],
                            $c['title'],
                            array(&$this, '_custom_fields_html'),
                            $post_type,
                            $c['context'],
                            $c['priority'],
                            array(
                                'id' => $c['id'],
                                'key' => $key
                            )
                        );
                    }
                }
            }
        }

        function _save_custom_fields_postdata($post_id)
        {
            global $post;

            // Stop WP from clearing custom fields on autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Prevent quick edit from clearing custom fields
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return;
            }

            // check permissions
            if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } else if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }

            if (!isset($_POST[$this->args['opt_name']])) {
                $_POST[$this->args['opt_name']] = array();
            }

            if (isset($_POST['snp_bld']) && is_array($_POST['snp_bld'])) {
                $snp_sanitize_builder_data = snp_sanitize_builder_data($_POST['snp_bld']);

                $_POST[$this->args['opt_name']]['builder']=$snp_sanitize_builder_data['builder'];
                $_POST[$this->args['opt_name']]['bld_cf']=$snp_sanitize_builder_data['bld_cf'];
            }
        
            foreach ((array) $_POST[$this->args['opt_name']] as $k => $v) {
                if (strpos($k, 'cf') !== FALSE && $k!='bld_cf') {
                    $elements = array();
                    foreach ($v['fields'] as $k2 => $v2) {
                        if ($v2 != 'RAND') {
                            $elements[]  = $v[$v2];
                        }
                    }
                    $data = $elements;
                } else {
                    $data = $v;
                }

                if (get_post_meta($post_id, $this->args['opt_name'] . '_' . $k) == "") {
                    add_post_meta($post_id, $this->args['opt_name'] . '_' . $k, $data, true);
                } else if ($data != get_post_meta($post_id, $this->args['opt_name'] . '_' . $k, true)) {
                    update_post_meta($post_id, $this->args['opt_name'] . '_' . $k, $data);
                } else if ($data === "") {
                    delete_post_meta($post_id, $this->args['opt_name'] . '_' . $k, get_post_meta($post_id, $this->args['opt_name'] . '_' . $k, true));
                }
            }
        }

        function _custom_fields_html($post, $metabox)
        {
            $fields = array();
            if ($post == 'snp_popup_fields') {
                $fields = (array) snp_popup_fields_list($metabox);
            } else {
                $fields = (array) $this->customfields[$metabox['args']['key']]['fields'];
            }

            if ($post != 'snp_popup_fields' && $this->customfields[$metabox['args']['key']]['id'] == 'snp-cf-cnt') {
                echo '<div class="snp-nhp-opts-group-tab" id="select-theme-theme" style="display: block !important;">';
                global $post;
                $value = get_post_meta($post->ID, $this->args['opt_name'] . '_theme', true);
                if ($value) {
                    $this->_custom_fields_html('snp_popup_fields', $value['theme']);
                }
                echo '</div>';
            } else {
                echo '<div class="snp-nhp-opts-group-tab" style="display: block !important;">';
                echo '<table class="form-table">';
                foreach ($fields as $k => $field) {
                    echo '<tr ' . (isset($field['trclass']) ? 'class="' . $field['trclass'] . '"' : '' ) . ' valign="top">';
                    if (isset($field['disable_title']) && $field['disable_title'] == 1) {

                    } else {
                        echo '<th scope="row">' . ($field['type'] == 'divide' ? '<span style="font-size: 24px; font-weight: strong;">': '') .$field['title']. ($field['type'] == 'divide' ? '</span>': '');
                        if (!empty($field['sub_desc'])) {
                            echo '<span class="description">' . $field['sub_desc'] . '</span>';
                        }
                        echo '</th>';
                    }

                    if (isset($field['disable_title']) && ($field['disable_title'] == 1)) {
                        echo '<td colspan="2">';
                    } else {
                        echo '<td' . (isset($field['disable_title']) && $field['disable_title'] == 1 ? ' rowspan="2" style="text-align: center;"' : '') . '>';
                    }

                    $this->_field_input($field, 1);
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';
            }
        }
    }
}