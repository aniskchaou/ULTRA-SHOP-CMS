<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
  $structure=WOOF_EXT_QUICK_TEXT::parse_template_structure($template_structure);
?>
    <div class="woof_qs_result  woof_qs_list_<?php echo $template_result?>  text_res_page_0 ">
        <div class=" woof_qs_table_<?php echo $template_result?>_header"><?php echo $header_text?></div>
        <?php echo WOOF_EXT_QUICK_TEXT::show_sort_html_select();?>
        __PAGINATION__
        <div class="woof_qs_container">
            <div class="woof_qs_item blog-card">
                <div class="woof_qs_<?php echo $template_result?>_img photo"> 
                    <a href="__URL__" target="__TARGET__"><img __SRC__ alt="__TITLE__" /></a>
                </div>
                <ul class="details"></ul>
                <div class="description">
                     <?php foreach ($structure as $item): 
                        if($item['key']=='img'){
                            continue;
                        }elseif($item['key']=='title'){
                            ?>
                                <div class="woof_qs_<?php echo $template_result ?>_<?php echo $item['key']?>">
                                    <a href="__URL__" target="__TARGET__">__TITLE__</a>
                                </div>
                            <?php
                        }else{
                            ?>
                                <div class="woof_qs_<?php echo $template_result ?>_<?php echo $item['key']?>">
                                    <?php echo ($item['key']=="price")?"":$item['title'].":";?><?php echo $item['alias']?>
                                </div>
                            <?php
                        }
                        endforeach;?>  
                    <a href="__URL__" target="_blank"><?php _e('View product', 'woocommerce-products-filter')?></a>
                </div>
            </div>
            <div class="woof_qs_no_products_item">
                <div class='woof_qs_no_products woof_qt_no_products_<?php echo $template_result ?>'>
                    <?php _e('Product not found', 'woocommerce-products-filter')?>
                </div>
            </div>
        </div>
           
        __PAGINATION__
    </div>
            