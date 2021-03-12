<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
?>

<section id="tabs-slideout">

    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>


                <a href="https://products-filter.com/extencion/slideout/" target="_blank" class="button-primary"><?php echo __('About extension', 'woocommerce-products-filter') ?></a><br />
                <br />


                <div class="woof-control-section">

                    <h5><?php _e("Global visibility", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $slideout_show = array(
                                0 => __("Hide", 'woocommerce-products-filter'),
                                1 => __("Show", 'woocommerce-products-filter'),
                            );
                            ?>

                            <?php
                            if (!isset($woof_settings['woof_slideout_show']) OR empty($woof_settings['woof_slideout_show'])) {
                                $woof_settings['woof_slideout_show'] = 0;
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_slideout_show]" class="chosen_select slideout_value" data-name="woof_slideout_show">
                                    <?php foreach ($slideout_show as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_show'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Show/Hide slideout on shop pages", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->   

                <div class="woof-control-section">

                    <h5><?php _e("Button as", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $slideout_type_btn = array(
                                0 => __("as Image", 'woocommerce-products-filter'),
                                1 => __("as Text", 'woocommerce-products-filter'),
                            );
                            ?>

                            <?php
                            if (!isset($woof_settings['woof_slideout_type_btn']) OR empty($woof_settings['woof_slideout_type_btn'])) {
                                $woof_settings['woof_slideout_type_btn'] = 0;
                            }
                            ?>
                            <div class="select-wrap ">
                                <select name="woof_settings[woof_slideout_type_btn]" class="chosen_select slideout_value" data-name="woof_slideout_type_btn">
                                    <?php foreach ($slideout_type_btn as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_type_btn'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Image or text, button-image can be selected below of the option, instead of default one in Slideout image field. If text - you can below of the option insert Slideout text", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->                     
                <?php
                if (!isset($woof_settings['woof_slideout_img'])) {
                    $woof_settings['woof_slideout_img'] = '';
                }
                ?>
                <div class="woof-control-section" <?php echo ($woof_settings['woof_slideout_type_btn'] == 1) ? "style='display:none'" : "" ?> >
                    <h4><?php _e('Slideout image', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">
                        <div class="woof-control woof-upload-style-wrap">
                            <input type="text" name="woof_settings[woof_slideout_img]" class="slideout_value" data-name="woof_slideout_img" value="<?php echo $woof_settings['woof_slideout_img'] ?>" />
                            <a href="#" class="woof-button woof_select_image"><?php _e('Select Image', 'woocommerce-products-filter') ?></a>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Url of the custom image for the button', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
                <?php
                if (!isset($woof_settings['woof_slideout_txt'])) {
                    $woof_settings['woof_slideout_txt'] = __('Filter', 'woocommerce-products-filter');
                }
                ?>
                <div class="woof-control-section" <?php echo ($woof_settings['woof_slideout_type_btn'] == 0) ? "style='display:none'" : "" ?>  >
                    <h4><?php _e('Slideout text', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_slideout_txt]" class="slideout_value" data-name="woof_slideout_txt" value="<?php echo $woof_settings['woof_slideout_txt'] ?>" />

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Custom text of the button', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->                     

                <div class="woof-control-section" <?php echo ($woof_settings['woof_slideout_type_btn'] == 1) ? "style='display:none'" : "" ?>>

                    <h4><?php _e('Button image size', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container z">
                        <div class="woof-control  woof_slideout_img_h_w">

                            <input type="number" name="woof_settings[woof_slideout_img_w]" class="slideout_value" data-name="woof_slideout_img_w" value="<?php echo(isset($woof_settings['woof_slideout_img_w']) ? $woof_settings['woof_slideout_img_w'] : '50') ?>" />                              
                            <span>X</span>
                            <input type="number" name="woof_settings[woof_slideout_img_h]" class="slideout_value" data-name="woof_slideout_img_h" value="<?php echo(isset($woof_settings['woof_slideout_img_h']) ? $woof_settings['woof_slideout_img_h'] : '50') ?>" />



                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Width X Height (px) of the button image', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->
                <div class="woof-control-section ">

                    <h5><?php _e("Position", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $slideout_position = array(
                                "right" => __("Right", 'woocommerce-products-filter'),
                                "left" => __("Left", 'woocommerce-products-filter'),
                                "top" => __("Top", 'woocommerce-products-filter'),
                                "bottom" => __("Bottom", 'woocommerce-products-filter'),
                            );
                            ?>

                            <?php
                            if (!isset($woof_settings['woof_slideout_position']) OR empty($woof_settings['woof_slideout_position'])) {
                                $woof_settings['woof_slideout_position'] = "right";
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_slideout_position]" class="chosen_select slideout_value" data-name="woof_slideout_position">
                                    <?php foreach ($slideout_position as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_position'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Where to show slideout button: right, left, top", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->                    
                <div class="woof-control-section">

                    <h4><?php _e('Speed', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container z">
                        <div class="woof-control ">
                            <input type="number" name="woof_settings[woof_slideout_speed]" class="slideout_value" data-name="woof_slideout_speed" value="<?php echo(isset($woof_settings['woof_slideout_speed']) ? $woof_settings['woof_slideout_speed'] : '100') ?>" />                               
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('popup animation speed in (ms)', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->                    
                <div class="woof-control-section ">

                    <h5><?php _e("Action", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $slideout_action = array(
                                "click" => __("Click", 'woocommerce-products-filter'),
                                "hover" => __("Hover", 'woocommerce-products-filter'),
                            );
                            ?>

                            <?php
                            if (!isset($woof_settings['woof_slideout_action']) OR empty($woof_settings['woof_slideout_action'])) {
                                $woof_settings['woof_slideout_action'] = "click";
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_slideout_action]" class="chosen_select slideout_value" data-name="woof_slideout_action">
                                    <?php foreach ($slideout_action as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_action'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Way to show of slideout popup out: click or hover", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->    
                <div class="woof-control-section">
                    <?php
                    $slideout_offset_t = array(
                        "px" => __("px", 'woocommerce-products-filter'),
                        "%" => __("%", 'woocommerce-products-filter'),
                    );
                    if (!isset($woof_settings['woof_slideout_offset_t']) OR empty($woof_settings['woof_slideout_offset_t'])) {
                        $woof_settings['woof_slideout_offset_t'] = "px";
                    }
                    ?>        
                    <h4><?php _e('Offset', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container woof_slideout_offset">
                        <div class="woof-control ">
                            <input type="number" name="woof_settings[woof_slideout_offset]" class="slideout_value" data-name="woof_slideout_offset" value="<?php echo(isset($woof_settings['woof_slideout_offset']) ? $woof_settings['woof_slideout_offset'] : '100') ?>" />                               
                            <span>
                                <select name="woof_settings[woof_slideout_offset_t]" class="slideout_value" style="margin-bottom: 1px; width: 75px !important; min-width: 75px !important;" data-name="woof_slideout_offset_t">
                                    <?php foreach ($slideout_offset_t as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_offset_t'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>    
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Button offset (px) from top or left from the window edge', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->                      
                <div class="woof-control-section">
                    <?php
                    $slideout_wh_t = array(
                        "px" => __("px", 'woocommerce-products-filter'),
                        "%" => __("%", 'woocommerce-products-filter'),
                    );
                    if (!isset($woof_settings['woof_slideout_width_t']) OR empty($woof_settings['woof_slideout_width_t'])) {
                        $woof_settings['woof_slideout_width_t'] = "px";
                    }
                    if (!isset($woof_settings['woof_slideout_height_t']) OR empty($woof_settings['woof_slideout_height_t'])) {
                        $woof_settings['woof_slideout_height_t'] = "px";
                    }
                    ?>        
                    <h4><?php _e('Сontainer size', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container woof_slideout_сontainer_size">
                        <div class="woof-control ">
                            <input type="number" placeholder="auto" name="woof_settings[woof_slideout_width]" class="slideout_value" data-name="woof_slideout_width" value="<?php echo(isset($woof_settings['woof_slideout_width']) ? $woof_settings['woof_slideout_width'] : '') ?>" />                               
                            <span style="display:none;">
                                <select name="woof_settings[woof_slideout_width_t]" class="chosen_select slideout_value" data-name="woof_slideout_width_t">
                                    <?php foreach ($slideout_wh_t as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_width_t'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span> 
                            <span>X</span>
                            <input type="number" placeholder="auto" name="woof_settings[woof_slideout_height]" class="slideout_value" data-name="woof_slideout_height" value="<?php echo(isset($woof_settings['woof_slideout_height']) ? $woof_settings['woof_slideout_height'] : '') ?>" />                               
                            <span style="display:none;">
                                <select name="woof_settings[woof_slideout_height_t]" class="chosen_select slideout_value" data-name="woof_slideout_height_t">
                                    <?php foreach ($slideout_wh_t as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_height_t'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>                                 
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Width and height of the popup html container size in (px)', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->      
                <div class="woof-control-section">

                    <h5><?php _e("State after init", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $slideout_open = array(
                                "true" => __("Yes", 'woocommerce-products-filter'),
                                "false" => __("No", 'woocommerce-products-filter'),
                            );
                            ?>

                            <?php
                            if (!isset($woof_settings['woof_slideout_open']) OR empty($woof_settings['woof_slideout_open'])) {
                                $woof_settings['woof_slideout_open'] = "false";
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_slideout_open]" class="chosen_select slideout_value" data-name="woof_slideout_open">
                                    <?php foreach ($slideout_open as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_open'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("how to show slideout popup after page loaded: opened or closed", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->      
                <div class="woof-control-section">

                    <h5><?php _e("Mobile behavior", 'woocommerce-products-filter') ?></h5>

                    <div class="woof-control-container">
                        <div class="woof-control">

                            <?php
                            $slideout_mobile = array(
                                "0" => __("Show on all devices", 'woocommerce-products-filter'),
                                "1" => __("Show only on mobile", 'woocommerce-products-filter'),
                                "2" => __("Show only on desktop", 'woocommerce-products-filter'),
                            );
                            ?>

                            <?php
                            if (!isset($woof_settings['woof_slideout_mobile']) OR empty($woof_settings['woof_slideout_mobile'])) {
                                $woof_settings['woof_slideout_mobile'] = "0";
                            }
                            ?>
                            <div class="select-wrap">
                                <select name="woof_settings[woof_slideout_mobile]" class="chosen_select slideout_value" data-name="woof_slideout_mobile">
                                    <?php foreach ($slideout_mobile as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($woof_settings['woof_slideout_mobile'] == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e("Should the slideout button be visible on mobile devices", 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->  
                <div class="woof-control-section">

                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="button" class="woof-button" style="margin: 0;" id="woow_slideout_generate" value="<?php _e("Generate shortcode", 'woocommerce-products-filter') ?>">
                        </div>
                        <div class="woof-description">
                            <span class="woof_slideout_shortcode_res" style="color: cornflowerblue;"></span>
                            <p class="description"><?php _e("By this button you can set settings and generate shortcode for your needs for on single pages. After using, if you do not want the settings became globals, refresh the page to avoid settings saving!", 'woocommerce-products-filter') ?></p>

                        </div>
                    </div>
                </div><!--/ .woof-control-section-->                      
            </section>

        </div>

    </div>
</section>

<style>
    #tabs-slideout .woof_slideout_img_h_w input,
    [name="woof_settings[woof_slideout_speed]"],
    [name="woof_settings[woof_slideout_offset]"],
    [name="woof_settings[woof_slideout_width]"],
    [name="woof_settings[woof_slideout_height]"]{
        width: 70px !important;
    }
    #tabs-slideout .woof_slideout_offset span{
        width: 80px !important;
    }
    .woof_slideout_сontainer_size span{
        display: inline-block;
    }
</style>
<script>
    jQuery("#woow_slideout_generate").click(function () {
        var data = {};
        data.action = 'woof_slideout_shortcode_gen';
        var values = jQuery(".slideout_value");
        jQuery.each(values, function (i, item) {
            var key = jQuery(item).data("name");
            if (key) {
                data[key] = jQuery(item).val();
            }
        });
        console.log(data);
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: data,
            success: function (res) {
                // console.log(res);
                jQuery(".woof_slideout_shortcode_res").text(res);
            }
        });
    });
    jQuery('select[name="woof_settings[woof_slideout_type_btn]"]').change(function () {
        var type = jQuery(this).val();
        if (type == 0) {
            jQuery('input[name="woof_settings[woof_slideout_img]"]').parents(".woof-control-section").show();
            jQuery('input[name="woof_settings[woof_slideout_txt]"]').parents(".woof-control-section").hide();
            jQuery('input[name="woof_settings[woof_slideout_img_w]"]').parents(".woof-control-section").show();
        } else {
            jQuery('input[name="woof_settings[woof_slideout_img]"]').parents(".woof-control-section").hide();
            jQuery('input[name="woof_settings[woof_slideout_img_w]"]').parents(".woof-control-section").hide();
            jQuery('input[name="woof_settings[woof_slideout_txt]"]').parents(".woof-control-section").show();
        }
    });
</script>