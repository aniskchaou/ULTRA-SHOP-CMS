<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
?>

<section id="tabs-turbo-mode">
    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>

                <em><?php _e('Boost speed of products filtering. This mode allows to avoid generating big MySQL queries while products filtering on the site front what makes less loading to the server and getting filtering results more quick', 'woocommerce-products-filter') ?></em>

                <a href="https://products-filter.com/extencion/turbo-mode/" target="_blank" class="button-primary"><?php echo __('More about extension', 'woocommerce-products-filter') ?></a><br />

                <br />

                <div class="woof-control-section">

                    <h4><?php _e('Enable turbo mode', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $enable_turbo = array(
                                0 => __('No', 'woocommerce-products-filter'),
                                1 => __('Yes', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_turbo_mode']['enable'])) {
                                $woof_settings['woof_turbo_mode']['enable'] = 0;
                            }
                            $enable = $woof_settings['woof_turbo_mode']['enable'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_turbo_mode][enable]" class="chosen_select">
                                    <?php foreach ($enable_turbo as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($enable == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('After activation of Turbo Mode firstly what should be done is generating of the data file, see button below. And also do not forget to set cron mode.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
                <div class="woof-control-section" style="display: none;">

                    <h4><?php _e('Cron type', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $cron_systems = array(
                                0 => __('WordPress Cron', 'woocommerce-products-filter'),
                                    //1 => __('External Cron', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_turbo_mode']['cron_system'])) {
                                $woof_settings['woof_turbo_mode']['cron_system'] = 0;
                            }
                            $cron_system = $woof_settings['woof_turbo_mode']['cron_system'];
                            //$cron_system = -1; //hide  cron sys
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_turbo_mode][cron_system]" class="chosen_select woof_cron_system">
                                    <?php foreach ($cron_systems as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($cron_system == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <!-- <?php _e('External cron is more predictable with time of execution, but additional knowledge how to set it correctly is required (<i style="color: orange;">External cron will be ready in the next version of the extension</i>)', 'woocommerce-products-filter') ?> -->
                                <?php _e('Select acceptable for your hosting type of cron', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_external_cron_option" style="display: <?php echo($cron_system == 1 ? 'block' : 'none') ?>;">

                    <h4><?php _e('Secret key for external cron', 'woocommerce-products-filter') ?></h4>
                    <?php
                    if (!isset($woof_settings['woof_turbo_mode']['cron_secret_key']) OR empty($woof_settings['woof_turbo_mode']['cron_secret_key'])) {
                        $woof_settings['woof_turbo_mode']['cron_secret_key'] = 'woof_stat_updating';
                    }
                    $cron_secret_key = sanitize_title($woof_settings['woof_turbo_mode']['cron_secret_key']);
                    ?>
                    <div class="woof-control-container">
                        <div class="woof-control">
                            <input type="text" name="woof_settings[woof_turbo_mode][cron_secret_key]" value="<?php echo $cron_secret_key ?>" />
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('Enter any random text in the field and use it in the external cron with link like: http://mysite.com/?woof_stat_collection=__YOUR_SECRET_KEY_HERE__', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_wp_cron_option" style="display: <?php echo($cron_system == 0 ? 'block' : 'none') ?>;">

                    <h4><?php _e('WordPress Cron period', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $wp_cron_periods = array(
                                'daily' => __('daily', 'woocommerce-products-filter'),
                                'weekly' => __('weekly', 'woocommerce-products-filter'),
                                'twicemonthly' => __('twicemonthly', 'woocommerce-products-filter'),
                                'month' => __('monthly', 'woocommerce-products-filter'),
                                'no' => __('without update', 'woocommerce-products-filter'),
                                //'min1' => __('min1', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_turbo_mode']['wp_cron_period'])) {
                                $woof_settings['woof_turbo_mode']['wp_cron_period'] = 'weekly';
                            }
                            $wp_cron_period = $woof_settings['woof_turbo_mode']['wp_cron_period'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_turbo_mode][wp_cron_period]" class="chosen_select">
                                    <?php foreach ($wp_cron_periods as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($wp_cron_period == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="woof-description">
                            <p class="description">
                                <?php _e('How often update products data file', 'woocommerce-products-filter') ?>
                            </p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section woof_update_search_data_file">

                    <h4><?php _e('Reassemble products data now!', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">
                        <div class="woof-control">
                            <a id="woof_turbo_mode_update"  class="button" ><?php _e('Update now!', 'woocommerce-products-filter') ?></a>
                            <span class="woof_turbo_mode_product_load"><img src="<?php echo WOOF_LINK ?>ext\turbo_mode\img\load.gif"></span>
                            <div class="woof_turbo_mode_product_succes">
                               <img src="<?php echo WOOF_LINK ?>ext\turbo_mode\img\succes.png"> 
                               <p><?php _e('File updated!!!', 'woocommerce-products-filter')?></p>
                            </div>
                            <span class="woof_turbo_mode_messange"></span>
                            <span class="woof_turbo_mode_product_count"></span>
                            
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('You can always assemble products data for Turbo Mode by one click! While its assembling filtering on the site front is still possible with previous assembled products data until new data assembled!', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h4><?php _e('Where to keep products data file', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control woof-upload-style-wrap">

                            <?php
                            $storing = array(
                                0 => 'wp-uploads\uploads_woof_turbo_mode',
                                1 => 'plugins\woocommerce-products-filter\ext\turbo_mode\data'
                            );

                            if (!isset($woof_settings['woof_turbo_mode']['storing'])) {
                                $woof_settings['woof_turbo_mode']['storing'] = 0;
                            }
                            $allow = $woof_settings['woof_turbo_mode']['storing'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_turbo_mode][storing]" class="chosen_select">
                                    <?php foreach ($storing as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($allow == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php _e('It is better to keep products data file in wp-uploads folder to avoid data removing after the plugin update, but not all hosting providers allows to get json data from this folder, so if you select keep data in the plugin folder remember about it and reassemble products data using button above after the plugin update.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
            </section>

        </div>

    </div>
</section>

<style type="text/css">
    .woof_qt_succes{
        font-style: italic;
        color: green;
        margin-left: 20px;
        display: inline-block;
    }
    .woof_qt_fail{
        font-style: italic;
        color: red;
        margin-left: 20px;
        display: inline-block;
    }
    .woof_turbo_mode_product_load img{
        display: none;
        width: 30px;
        height: auto;
    }
    .woof_turbo_mode_product_succes{
        display: none;
    }
    .woof_turbo_mode_product_succes img{
        width: 40px;
    }  
    .woof_turbo_mode_product_succes p{
        display: inline-block;
        vertical-align: bottom;
    }      

</style>

<script type="text/javascript">
    jQuery('#woof_turbo_mode_update').click(function () {
        var turbo_mode_nonce = "<?php echo wp_create_nonce('woof-turbo-mode-nonce') ?>";
        woof_turbo_mode_create_search_file(turbo_mode_nonce, ajaxurl);
    });
</script>

