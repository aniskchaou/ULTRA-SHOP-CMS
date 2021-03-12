<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$woof_ext_backorder_label=apply_filters('woof_ext_custom_title_by_backorder',__('Exclude: On backorder', 'woocommerce-products-filter'));
if (isset($WOOF->settings['by_backorder']) AND $WOOF->settings['by_backorder']['show'])
{
    ?>
    <div data-css-class="woof_checkbox_onbackorder_container" class="woof_checkbox_onbackorder_container woof_container woof_container_backorder">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
            <input type="checkbox" class="woof_checkbox_onbackorder" id="woof_checkbox_onbackorder" name="backorder" value="0" <?php checked('onbackorder', $WOOF->is_isset_in_request_data('backorder') ? 'onbackorder' : '', true) ?> />&nbsp;&nbsp;<label for="woof_checkbox_onbackorder"><?php echo $woof_ext_backorder_label ?></label><br />
        </div>
    </div>
    <?php
}


