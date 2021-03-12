<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if (!isset($additional_taxes))
{
    $additional_taxes = '';
}
$price2_filter_data = WOOF_HELPER::get_price2_filter_data($additional_taxes);
$price_filter2_1opt_txt = __('filter by price', 'woocommerce-products-filter');
if (isset($this->settings['by_price']['first_option_text']))
{
    if (!empty($this->settings['by_price']['first_option_text']))
    {
        $price_filter2_1opt_txt = WOOF_HELPER::wpml_translate(null, $this->settings['by_price']['first_option_text']);
    }
}

if (isset($placeholder))
{
    $price_filter2_1opt_txt = WOOF_HELPER::wpml_translate(null, $placeholder);
}

$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos', 0);
$opt_count = 0;
?>


<div class="woof_price_filter_dropdown_container">
    <select class="woof_price_filter_dropdown">
        <option value="-1"><?php echo $price_filter2_1opt_txt ?></option>
        <?php if (!empty($price2_filter_data)): ?>

            <?php foreach ($price2_filter_data['ranges']['options'] as $k => $value): $value = trim($value); ?>

                <?php
                $c = 0;
                $cs = '';
                if ($show_count)
                {
                    $c = (int) $price2_filter_data['ranges']['count'][$k];
                    $cs = '(' . $c . ')';
                }

                if ($show_count_dynamic AND $c == 0)
                {
                    if ($hide_dynamic_empty_pos)
                    {
                        continue;
                    }
                }
                $opt_count++;
                ?>

                <option <?php if ($c == 0 AND $show_count): ?>disabled=""<?php endif; ?> <?php echo selected($price2_filter_data['selected'], $k); ?> value="<?php echo $k ?>"><?php echo($value) ?> <?php echo $cs ?></option>
            <?php endforeach; ?>

<?php else: ?>

            <option value="0"><?php _e('Not possible. Enter options ranges in the plugin settings -> tab Structure -> Search by price -> additional options', 'woocommerce-products-filter') ?></option>

<?php endif; ?>

    </select>
</div>
<?php
if(!$opt_count){
?>
<style>
    .woof_price2_search_container{
        display: none;
    }
</style>
<?php
}
