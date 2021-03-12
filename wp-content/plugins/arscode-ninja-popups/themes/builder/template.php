<?php
$action = 'post';
if (snp_get_option('form_action') == 'get' || (isset($POPUP_META['snp_form_action']) && $POPUP_META['snp_form_action'] == 'get')) {
    $action = 'get';
}

$material = '';
if (isset($POPUP_META['snp_material']) && $POPUP_META['snp_material'] == 'yes') {
    $material = 'material';
}
?>

<?php 
require_once SNP_DIR_PATH.'include/builder.inc.php';
$bld_tmp = base64_decode($POPUP_META['snp_builder']);
if($bld_tmp===false)
{
    $bld = unserialize($POPUP_META['snp_builder']);
}
else
{
    $bld = unserialize($bld_tmp);
}
$fonts = array();
$mbYTPlayer = false;
?>
<div class="snp-builder<?php if($type=='inline') echo ' snp-bld-showme';?>">
    <?php
    foreach((array)$bld as $step_index => $step)
    {
        ?>
        <div class="snp-bld-step-cont snp-bld-step-cont-<?php echo $step_index; ?><?php if($type!='inline') echo ' '.(!empty($step['args']['position']) ? $step['args']['position'] : 'snp-bld-center').'';?>">
            <?php 
            echo '<div id="snp-bld-step-'.$step_index.'" data-width="'.$step['args']['width'].'" data-height="'.$step['args']['height'].'" '.(isset($step['args']['disable-overlay']) && $step['args']['disable-overlay']==1 ? 'data-overlay="disabled"' : '').' class="snp-bld-step snp-bld-step-'.$step_index.' '.($type=='inline' && $step_index==1 ? 'snp-bld-showme' : '').' '.($step_index==1 && $step['args']['animation']!='' ? 'animated '.$step['args']['animation'] : '').' '.$step['args']['css-class'].'" '.($step['args']['animation'] ? 'data-animation="'.$step['args']['animation'].'"' : '').' '.($step['args']['animation-close'] ? 'data-animation-close="'.$step['args']['animation-close'].'"' : '').'>';
            if($step['args']['background-video'])
            {
                $mbYTPlayer = true;
                echo '<a id="snp-bld-step-bg-'.$step_index.'" class="snp-bld-step-bg" data-property="{videoURL:\''.$step['args']['background-video'].'\',optimizeDisplay: true, showYTLogo: false, containment:\'#snp-bld-step-'.$step_index.'\', showControls:false, stopMovieOnBlur: false, autoPlay:true, loop:true, mute:true, startAt:0, opacity:1, addRaster:false, quality:\'default\'}"></a>';
            }
            
            if (!isset($step['args']['remove_form']) || $step['args']['remove_form'] == 0) {
                echo '<form action="'.(snp_get_option('ml_manager') == 'html' ? snp_get_option('ml_html_url') : '#').'" method="' . $action .'" class="'. $material.' snp-subscribeform snp_subscribeform"'.(snp_get_option('ml_manager')  == 'html' && snp_get_option('ml_html_blank') ? ' target="_blank"': '').'>';

                echo '<input type="hidden" name="np_custom_name1" value="" />';
                echo '<input type="hidden" name="np_custom_name2" value="" />';
            }

            if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}
            ?>
                <?php
                foreach((array)$step['elements'] as $el_index => $el)
                {
                    echo snp_builder_create_element($el['type'], $step_index, $el_index, $el);
                    if(isset($el['font']) && snp_is_google_font($el['font']))
                    {
                        $fonts[$el['font']]=$el['font'];
                    }
                }
                ?>

            <?php
            if (!isset($step['args']['remove_form']) || $step['args']['remove_form'] == 0) {
                echo '</form>';
            }
            echo '</div>'; 
            ?>
        </div>
        <?php
    }
    ?>
</div>
<?php
if($POPUP_META['snp_overlay_bld']=='enabled' && $type!='inline')
{
    echo '<div class="snp-overlay" id="snp-pop-'.$ID.'-overlay"'.($POPUP_META['snp_overlay_bld_close']=='yes' ? ' data-close="yes"': '').'>';
    if(isset($POPUP_META['snp_overlay_bld_link']) && $POPUP_META['snp_overlay_bld_link']!='')
    {
        echo '<a href="'.$POPUP_META['snp_overlay_bld_link'].'"'.(isset($POPUP_META['snp_overlay_bld_link_blank']) && $POPUP_META['snp_overlay_bld_link_blank']=='yes' ? ' target="_blank"':'').'></a>';
    }
    echo '</div>';
}
if($type!='inline')
{
?>
<script>
    var snp_bld_open<?php echo abs($ID); ?>=function(){_snp_bld_open(<?php echo $ID; ?>);};
    var snp_bld_close<?php echo abs($ID); ?>=function(){_snp_bld_close(<?php echo $ID; ?>);};
</script>
<?php
}
?>
<style>
<?php
if (!snp_get_option('js_disable_googlefonts')) {
    if(count($fonts)>0)
    {
        foreach($fonts as $f)
        {
            wp_register_style( 'font-'.  urlencode($f), '//fonts.googleapis.com/css?family='.urlencode($f) );
            wp_enqueue_style( 'font-'.  urlencode($f) );
        }
    }
}
if($mbYTPlayer==true)
{
    snp_init_mbYTPlayer();
}
snp_init_fontawesome();
$css = '';
foreach((array)$bld as $step_index => $step)
{
    $css.= '.snp-pop-'.$ID.' .snp-bld-step-cont-'.$step_index.' {';
    if(isset($step['args']['margin-top']) && $step['args']['margin-top']!='')
    {
         $css.='margin-top: '.$step['args']['margin-top'].'px;';
    }
    if(isset($step['args']['margin-bottom']) && $step['args']['margin-bottom']!='')
    {
         $css.='margin-bottom: '.$step['args']['margin-bottom'].'px;';
    }
    if(isset($step['args']['margin-left']) && $step['args']['margin-left']!='')
    {
         $css.='margin-left: '.$step['args']['margin-left'].'px;';
    }
    if(isset($step['args']['margin-right']) && $step['args']['margin-right']!='')
    {
         $css.='margin-right: '.$step['args']['margin-right'].'px;';
    }
    $css.= '}';
    $css.= '.snp-pop-'.$ID.' .snp-bld-step-'.$step_index.' {';
    $css.= 'width: '.$step['args']['width'].'px;';
    $css.= 'height: '.$step['args']['height'].'px;';
    if($step['args']['opacity'])
    {
        $css.='opacity: '.$step['args']['opacity'].';';
    }
    if($step['args']['z-index'])
    {
        $css.='z-index: '.$step['args']['z-index'].';';
    }
    if(!empty($step['args']['border-style']))
    {
         $css.='border-style: '.$step['args']['border-style'].';';
    }
    if(isset($step['args']['border-width']) && $step['args']['border-width']!='')
    {
         $css.='border-width: '.$step['args']['border-width'].'px;';
    }
    if(!empty($step['args']['border-color']))
    {
         $css.='border-color: '.$step['args']['border-color'].';';
    }
    if(isset($step['args']['border-radius']) && $step['args']['border-radius']!='')
    {
         $css.='border-radius: '.$step['args']['border-radius'].'px;';
    }
    if(!empty($step['args']['background-color']))
    {
         $css.='background-color: '.$step['args']['background-color'].';';
    }
    if(!empty($step['args']['background-image']))
    {
         $css.='background-image: url(\''.$step['args']['background-image'].'\');';
    }
    if(!empty($step['args']['background-position']))
    {
         $css.='background-position: '.$step['args']['background-position'].';';
    }
    if(!empty($step['args']['background-repeat']))
    {
         $css.='background-repeat: '.$step['args']['background-repeat'].';';
    }
     if($step['args']['custom-css'])
    {
        $css.=''.$step['args']['custom-css'].'';
    }
    $css.= '}';
    foreach((array)$step['elements'] as $el_index => $el)
    {
        $css .= snp_builder_create_element_css($el['type'], $ID, $step_index, $el_index, $el);
    } 
}
if($POPUP_META['snp_overlay_bld']=='enabled')
{
    $css.='#snp-pop-'.$ID.'-overlay {';
    if (isset($POPUP_META['snp_overlay_bld_opacity']))
    {
        $css .= 'opacity: '.$POPUP_META['snp_overlay_bld_opacity'].';';
    }
    if (isset($POPUP_META['snp_overlay_bld_color']))
    {
        $css .= 'background-color: '.$POPUP_META['snp_overlay_bld_color'].';';
    }
    if (isset($POPUP_META['snp_overlay_bld_image']))
    {
        $css .= 'background-image: url(\''.$POPUP_META['snp_overlay_bld_image'].'\');';
    }
    $css.='}';
}
echo $css;
?>    
</style>