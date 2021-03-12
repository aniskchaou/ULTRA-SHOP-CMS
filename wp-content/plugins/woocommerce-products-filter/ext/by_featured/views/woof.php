<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$woof_ext_featured_label=apply_filters('woof_ext_custom_title_by_featured',__('Featured product', 'woocommerce-products-filter'));
if (isset($WOOF->settings['by_featured']) AND $WOOF->settings['by_featured']['show'])
{
    ?>
    <div data-css-class="woof_checkbox_featured_container" class="woof_checkbox_featured_container woof_container woof_container_product_visibility">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
            <input type="checkbox" class="woof_checkbox_featured" id="woof_checkbox_featured" name="product_visibility" value="0" <?php checked('featured', $WOOF->is_isset_in_request_data('product_visibility') ? 'featured' : '', true) ?> />&nbsp;&nbsp;<label for="woof_checkbox_featured"><?php echo $woof_ext_featured_label ?></label><br />
        </div>
    </div>
    <?php
}


