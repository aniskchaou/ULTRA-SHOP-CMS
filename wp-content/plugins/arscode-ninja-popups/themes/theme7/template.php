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
<div class="snp-fb snp-theme7" >
    <div class="snp-inner">
	<div class="snp-inner2">
	    <?php
	    if(!empty($POPUP_META['snp_header1']) || !empty($POPUP_META['snp_header2']))
	    {
		echo '<header class="snp-header">';
		if(!empty($POPUP_META['snp_header1']))
		{
		    echo '<span>'.$POPUP_META['snp_header1'].'</span>'; 
		}
		if(!empty($POPUP_META['snp_header2']))
		{
		    echo '<h1>'.$POPUP_META['snp_header2'].'</h1>'; 
		}
		echo '</header>';
	    }
	    if(!empty($POPUP_META['snp_maintext']))
	    {
		echo '<p class="snp-info">'.$POPUP_META['snp_maintext'].'</p>'; 
	    }
	    ?> 
	    <div class="snp-form">
		 <form action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform"<?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
		 	<input type="hidden" name="np_custom_name1" value="" />
        	<input type="hidden" name="np_custom_name2" value="" />

		     <?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
		    <fieldset>
			<div class="snp-field">
			    <input type="text" name="<?php echo ((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_email')) ? snp_get_option('ml_html_email') : 'email');?>" id="snp_email" placeholder="<?php echo $POPUP_META['snp_email_placeholder'];?>" class="snp-field snp-field-email" />		
			</div>
			<button type="submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" class="snp-submit"><?php echo $POPUP_META['snp_submit_button'];?></button>
		    </fieldset>
		</form>
	    </div>
	    <div class="snp-close">
	    <?php
	    if ($POPUP_META['snp_show_cb_button'] == 'yes')
	    {  
		echo '<a href="#" class="snp_nothanks">'.$POPUP_META['snp_cb_text'].'</a>';
	    }
	    ?>
	    </div>
	</div>
    </div>
    <?php
    if((snp_get_option('PROMO_ON') && snp_get_option('PROMO_REF')) && SNP_PROMO_LINK!='')
    {
	    $PROMO_LINK=SNP_PROMO_LINK.snp_get_option('PROMO_REF');
	    echo '<div class="snp-powered-b">';
	    echo '<a href="'.$PROMO_LINK.'" target="_blank">Powered by <strong>Ninja Popups</strong></a>';
	    echo '</div>';
    }
    ?>
</div>
<?php
if(isset($POPUP_META['snp_header1_font']))
{
	$POPUP_META['snp_header1_font']=unserialize($POPUP_META['snp_header1_font']);
}
if(isset($POPUP_META['snp_header2_font']))
{
	$POPUP_META['snp_header2_font']=unserialize($POPUP_META['snp_header2_font']);
}
if(isset($POPUP_META['snp_maintext_font']))
{
	$POPUP_META['snp_maintext_font']=unserialize($POPUP_META['snp_maintext_font']);
}
echo '<style>';
if (!empty($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme7 { max-width: '.$POPUP_META['snp_width'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme7 { min-height: '.$POPUP_META['snp_height'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_header1_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-theme7 span {font-size: '.$POPUP_META['snp_header1_font']['size'].'px; color: '.$POPUP_META['snp_header1_font']['color'].';}'."\n";
}
if (!empty($POPUP_META['snp_header2_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-theme7 h1 {font-size: '.$POPUP_META['snp_header2_font']['size'].'px; color: '.$POPUP_META['snp_header2_font']['color'].';}'."\n";
}
if (!empty($POPUP_META['snp_maintext_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-theme7 .snp-info {font-size: '.$POPUP_META['snp_maintext_font']['size'].'px; color: '.$POPUP_META['snp_maintext_font']['color'].';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_text_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-theme7 .snp-submit { color: '.$POPUP_META['snp_submit_button_text_color'].';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-theme7 .snp-submit { background-color: '.$POPUP_META['snp_submit_button_color'].';}'."\n";
}
echo '</style>';
?>