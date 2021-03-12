<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_Widget extends WP_Widget {

//Widget Setup
    public function __construct() {
        parent::__construct(__CLASS__, __('WOOF - WooCommerce Products Filter', 'woocommerce-products-filter'), array(
            'classname' => __CLASS__,
            'description' => __('WooCommerce Products Filter by realmag777', 'woocommerce-products-filter')
                )
        );
    }

//Widget view
    public function widget($args, $instance) {
        $args['instance'] = $instance;
        $args['sidebar_id'] = (isset($args['id'])) ? $args['id'] : 0;
        $args['sidebar_name'] = (isset($args['name'])) ? $args['name'] : "";
        //+++
        global $WOOF;
        $price_filter = 0;
        if (isset($WOOF->settings['by_price']['show'])) {
            $price_filter = (int) $WOOF->settings['by_price']['show'];
        }



        if (isset($args['before_widget'])) {
            echo $args['before_widget'];
        }
        ?>
        <div class="widget widget-woof">
        <?php
        if (!empty($instance['title'])) {
            $instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
            if (isset($args['before_title'])) {
                echo $args['before_title'];
                echo $instance['title'];
                echo $args['after_title'];
            } else {
                ?>
                    <<?php echo apply_filters('woof_widget_title_tag', 'h3'); ?> class="widget-title"><?php echo $instance['title'] ?></<?php echo apply_filters('woof_widget_title_tag', 'h3'); ?>>
                    <?php
                }
            }
            ?>


            <?php
            if (isset($instance['additional_text_before'])) {
                echo do_shortcode($instance['additional_text_before']);
            }

            $redirect = '';
            if (isset($instance['redirect'])) {
                $redirect = $instance['redirect'];
            }

            //+++

            $woof_start_filtering_btn = 0;
            if (isset($instance['woof_start_filtering_btn'])) {
                $woof_start_filtering_btn = (int) $instance['woof_start_filtering_btn'];
            }

            //+++

            $ajax_redraw = '';
            if (isset($instance['ajax_redraw'])) {
                $ajax_redraw = $instance['ajax_redraw'];
            }

            $dynamic_recount = -1;
            if (isset($instance['dynamic_recount'])) {
                $dynamic_recount = $instance['dynamic_recount'];
            }
            $btn_position = 'b';
            if (isset($instance['btn_position'])) {
                $btn_position = $instance['btn_position'];
            }
            $autosubmit = -1;
            if (isset($instance['autosubmit'])) {
                $autosubmit = $instance['autosubmit'];
            }
            ?>

            <?php echo do_shortcode('[woof sid="widget" autosubmit="' . $autosubmit . '" start_filtering_btn=' . $woof_start_filtering_btn . ' price_filter=' . $price_filter . ' redirect="' . $redirect . '" ajax_redraw="' . $ajax_redraw . '" btn_position="' . $btn_position . '" dynamic_recount="' . $dynamic_recount . '" ]'); ?>
        </div>
            <?php
            if (isset($args['after_widget'])) {
                echo $args['after_widget'];
            }
        }

//Update widget
        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['additional_text_before'] = $new_instance['additional_text_before'];
            $instance['redirect'] = $new_instance['redirect'];
            $instance['woof_start_filtering_btn'] = $new_instance['woof_start_filtering_btn'];
            $instance['ajax_redraw'] = $new_instance['ajax_redraw'];
            $instance['btn_position'] = $new_instance['btn_position'];
            $instance['dynamic_recount'] = $new_instance['dynamic_recount'];
            $instance['autosubmit'] = $new_instance['autosubmit'];
            return $instance;
        }

//Widget form
        public function form($instance) {
//Defaults
            $defaults = array(
                'title' => __('WooCommerce Products Filter', 'woocommerce-products-filter'),
                'additional_text_before' => '',
                'redirect' => '',
                'woof_start_filtering_btn' => 0,
                'ajax_redraw' => 0,
                'dynamic_recount' => -1,
                'btn_position' => 'b',
                'autosubmit' => -1
            );
            $instance = wp_parse_args((array) $instance, $defaults);
            $args = array();
            $args['instance'] = $instance;
            $args['widget'] = $this;
            ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'woocommerce-products-filter') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('additional_text_before'); ?>"><?php _e('Additional text before', 'woocommerce-products-filter') ?>:</label>
            <textarea class="widefat" type="text" id="<?php echo $this->get_field_id('additional_text_before'); ?>" name="<?php echo $this->get_field_name('additional_text_before'); ?>"><?php echo $instance['additional_text_before']; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('redirect'); ?>"><?php _e('Redirect to', 'woocommerce-products-filter') ?>:</label>
            <input class="widefat" type="text" id="<?php echo $this->get_field_id('redirect'); ?>" name="<?php echo $this->get_field_name('redirect'); ?>" value="<?php echo $instance['redirect']; ?>" /><br />
            <i><?php _e('Redirect to any page - use it by your own logic. Leave it empty for default behavior.', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('woof_start_filtering_btn'); ?>"><?php _e('Hide search form by default and show one button instead', 'woocommerce-products-filter') ?>:</label>
        <?php
        $options = array(
            0 => __('No', 'woocommerce-products-filter'),
            1 => __('Yes', 'woocommerce-products-filter')
        );
        ?>
            <select class="widefat" id="<?php echo $this->get_field_id('woof_start_filtering_btn') ?>" name="<?php echo $this->get_field_name('woof_start_filtering_btn') ?>">
        <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['woof_start_filtering_btn'], $k) ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
        <?php endforeach; ?>
            </select>
            <i><?php _e('User on the site front will have to press button like "Show products filter form" to load search form by ajax and start filtering. Good feature when search form is quite big and page loading takes more time because of it!', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('dynamic_recount'); ?>"><?php _e('Dynamic recount', 'woocommerce-products-filter') ?>:</label>
        <?php
        $options = array(
            -1 => __('Default', 'woocommerce-products-filter'),
            0 => __('No', 'woocommerce-products-filter'),
            1 => __('Yes', 'woocommerce-products-filter')
        );
        ?>
            <select class="widefat" id="<?php echo $this->get_field_id('dynamic_recount') ?>" name="<?php echo $this->get_field_name('dynamic_recount') ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['dynamic_recount'], $k) ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php _e('Dynamic recount for current search form', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('autosubmit'); ?>"><?php _e('Autosubmit', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                -1 => __('Default', 'woocommerce-products-filter'),
                0 => __('No', 'woocommerce-products-filter'),
                1 => __('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo $this->get_field_id('autosubmit') ?>" name="<?php echo $this->get_field_name('autosubmit') ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['autosubmit'], $k) ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php _e('Yes - filtering starts immediately if user changed any item in the search form. No - user can set search data and then should press Filter button', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('btn_position'); ?>"><?php _e('Submit button position', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                'b' => __('Bottom', 'woocommerce-products-filter'),
                't' => __('Top', 'woocommerce-products-filter'),
                'tb' => __('Top AND Bottom', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo $this->get_field_id('btn_position') ?>" name="<?php echo $this->get_field_name('btn_position') ?>">
                <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['btn_position'], $k) ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php _e('The submit and reset buttons position in current search form', 'woocommerce-products-filter') ?></i>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('ajax_redraw'); ?>"><?php _e('Form AJAX redrawing', 'woocommerce-products-filter') ?>:</label>
            <?php
            $options = array(
                0 => __('No', 'woocommerce-products-filter'),
                1 => __('Yes', 'woocommerce-products-filter')
            );
            ?>
            <select class="widefat" id="<?php echo $this->get_field_id('ajax_redraw') ?>" name="<?php echo $this->get_field_name('ajax_redraw') ?>">
            <?php foreach ($options as $k => $val) : ?>
                    <option <?php selected($instance['ajax_redraw'], $k) ?> value="<?php echo $k ?>" class="level-0"><?php echo $val ?></option>
                <?php endforeach; ?>
            </select>
            <i><?php _e('Redraws search form by AJAX, and to start filtering "Filter" button should be pressed. Useful when uses hierarchical drop-down for example', 'woocommerce-products-filter') ?></i>
        </p>
        <?php
    }

}
