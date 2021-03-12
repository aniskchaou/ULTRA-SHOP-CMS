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
<div class="snp-fb snp-newtheme1">
	<?php
    if ($POPUP_META['snp_show_cb_button'] == 'yes')
	{
		echo '<a class="snp-close snp_nothanks" href="#"></a>';
	}
	?>
    <div class="snp-newsletter-content">
	<?php
	if(!empty($POPUP_META['snp_header']))
	{
	    echo '<h2>'.$POPUP_META['snp_header'].'</h2>'; 
	}
	if(!empty($POPUP_META['snp_subheader']))
	{
	    echo '<p>'.$POPUP_META['snp_subheader'].'</p>'; 
	}
	?> 
	<form action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform"<?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
		<input type="hidden" name="np_custom_name1" value="" />
        <input type="hidden" name="np_custom_name2" value="" />
	<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
	    <div>
		<?php
		if(isset($POPUP_META['snp_cf']))
		{   
		    $name_field =  '<input type="text" name="'.((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_name')) ? snp_get_option('ml_html_name') : 'name').'" id="snp-name" placeholder="'.$POPUP_META['snp_name_placeholder'].'" '.($POPUP_META['snp_name_require']?'required':'').' class="snp-field snp-field-name" />';
		    $email_field = '<input type="text" name="'.((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_email')) ? snp_get_option('ml_html_email') : 'email').'" id="snp_email" placeholder="'.$POPUP_META['snp_email_placeholder'].'"  class="snp-field snp-field-email" />';
		    $tpl_field = '%FIELD%';
		     snp_custom_fields(unserialize($POPUP_META['snp_cf']),array(
			 'email_field' => $email_field,
			 'name_field' => $name_field,
			 'tpl_field' => $tpl_field,
			 'snp_name_disable' => $POPUP_META['snp_name_disable'], 
		     )); 
		}
		?>
	    </div>
	    <input type="submit" class="snp-subscribe-button snp-submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" value="<?php echo $POPUP_META['snp_submit_button'];?>">
	</form>
	<?php
	if (!empty($POPUP_META['snp_security_note']))
	{
		echo '<p><small><img src="'.SNP_URL.'themes/newtheme1/img/lock.png" alt="">'.$POPUP_META['snp_security_note'].'</small></p>';
	}
	?>
	
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
if(isset($POPUP_META['snp_header_font']))
{
	$POPUP_META['snp_header_font']=unserialize($POPUP_META['snp_header_font']);
}
if(isset($POPUP_META['snp_subheader_font']))
{
	$POPUP_META['snp_subheader_font']=unserialize($POPUP_META['snp_subheader_font']);
}
echo '<style>';
if (!empty($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme1 { width: '.$POPUP_META['snp_width'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme1 { min-height: '.$POPUP_META['snp_height'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_header_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme1 h2 {font-size: '.$POPUP_META['snp_header_font']['size'].'px; color: '.format_hex($POPUP_META['snp_header_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_subheader_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme1 .snp-newsletter-content p {font-size: '.$POPUP_META['snp_subheader_font']['size'].'px;}'."\n";
	echo '.snp-pop-'.$ID.' .snp-newtheme1 .snp-newsletter-content p {color: '.format_hex($POPUP_META['snp_subheader_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_text_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme1 .snp-submit { color: '.$POPUP_META['snp_submit_button_text_color'].';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme1 .snp-submit { background-color: '.format_hex($POPUP_META['snp_submit_button_color']).';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-newtheme1 .snp-newsletter-content h2:before { background-color: '.format_hex($POPUP_META['snp_submit_button_color']).';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-newtheme1 form input[type="text"],.snp-pop-'.$ID.' .snp-newtheme1 form select,.snp-pop-'.$ID.' .snp-newtheme1 form textarea,.snp-pop-'.$ID.' .snp-newtheme1 form input[type="text"]:focus,.snp-pop-'.$ID.' .snp-newtheme1 form select:focus,.snp-pop-'.$ID.' .snp-newtheme1 form textarea:focus {border: 1px solid '.format_hex($POPUP_META['snp_submit_button_color']).';}';
}
if (!empty($POPUP_META['snp_submit_button_hover_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme1 form input[type="submit"]:hover { background-color: '.format_hex($POPUP_META['snp_submit_button_hover_color']).';}'."\n";
}

if (!empty($POPUP_META['snp_bg_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme1 .snp-newsletter-content { background: rgba('.snp_hex2rgb($POPUP_META['snp_bg_color']).', 0.7);}'."\n";
}
if (!empty($POPUP_META['snp_bg_img']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme1 { background: url(\''.$POPUP_META['snp_bg_img'].'\') no-repeat scroll 50% 50% transparent; }'."\n";
}

echo '</style>';
