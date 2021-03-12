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
<div class="snp-fb snp-newtheme3">
    <header>
	<?php
	if(!empty($POPUP_META['snp_header']))
	{
	    echo '<h2>'.$POPUP_META['snp_header'].'</h2>'; 
	}
	if(!empty($POPUP_META['snp_img']))
	{
	    echo '<img src="'.$POPUP_META['snp_img'].'" alt="">'; 
	}
	if(!empty($POPUP_META['snp_subheader']))
	{
	    echo '<h3>'.$POPUP_META['snp_subheader'].'</h3>'; 
	}
	if(!empty($POPUP_META['snp_maintext']))
	{
	    echo '<p>'.$POPUP_META['snp_maintext'].'</p>'; 
	}
	?> 
    </header>
    <?php
    if ($POPUP_META['snp_show_cb_button'] == 'yes')
	{
		echo '<a class="snp-close snp_nothanks" href="#"></a>';
	}
	?>
    <div class="snp-newsletter-content">
	<form action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform"<?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
		<input type="hidden" name="np_custom_name1" value="" />
        <input type="hidden" name="np_custom_name2" value="" />
        
	    <?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
            <div>
		<?php
		if(isset($POPUP_META['snp_cf']))
		{
		    $name_field =  '<input type="text" name="'.((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_name')) ? snp_get_option('ml_html_name') : 'name').'" id="snp-name" placeholder="'.$POPUP_META['snp_name_placeholder'].'" class="snp-field snp-field-name" />';
		    $email_field = '<input type="text" name="'.((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_email')) ? snp_get_option('ml_html_email') : 'email').'" id="snp_email" placeholder="'.$POPUP_META['snp_email_placeholder'].'"  class="snp-field snp-field-email" />';
		    $tpl_field = '%FIELD%';
		     snp_custom_fields(unserialize($POPUP_META['snp_cf']),array(
			 'email_field' => $email_field,
			 'name_field' => $name_field,
			 'tpl_field' => $tpl_field,
			 'snp_name_disable' => $POPUP_META['snp_name_disable']
		     )); 
		}
		?>
            </div>
            <input type="submit" class="snp-subscribe-button snp-submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" value="<?php echo $POPUP_META['snp_submit_button'];?>">
	</form>
    </div>
    <?php
    if (!empty($POPUP_META['snp_security_note']))
    {
	    echo '<footer><small><img src="'.SNP_URL.'themes/newtheme3/img/lock.png" alt="">'.$POPUP_META['snp_security_note'].'</small></footer>';
    }
    ?>
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
if(isset($POPUP_META['snp_maintext_font']))
{
	$POPUP_META['snp_maintext_font']=unserialize($POPUP_META['snp_maintext_font']);
}
echo '<style>';
if (!empty($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme3 { width: '.$POPUP_META['snp_width'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme3 { min-height: '.$POPUP_META['snp_height'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_header_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme3 h2 {font-size: '.$POPUP_META['snp_header_font']['size'].'px; color: '.format_hex($POPUP_META['snp_header_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_subheader_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme3 h3 {font-size: '.$POPUP_META['snp_subheader_font']['size'].'px;}'."\n";
	echo '.snp-pop-'.$ID.' .snp-newtheme3 h3 {color: '.format_hex($POPUP_META['snp_subheader_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_maintext_font']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme3 p {font-size: '.$POPUP_META['snp_maintext_font']['size'].'px;}'."\n";
	echo '.snp-pop-'.$ID.' .snp-newtheme3 p {color: '.format_hex($POPUP_META['snp_maintext_font']['color']).';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-newtheme3 footer {color: '.format_hex($POPUP_META['snp_maintext_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_text_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme3 .snp-submit { color: '.format_hex($POPUP_META['snp_submit_button_text_color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_border_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme3 .snp-submit { border: 4px solid '.format_hex($POPUP_META['snp_submit_button_border_color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_hover_color']))
{
	echo '.snp-pop-'.$ID.' .snp-newtheme3 form input[type="submit"]:hover {  background-color: '.format_hex($POPUP_META['snp_submit_button_hover_color']).';  border-color: '.format_hex($POPUP_META['snp_submit_button_hover_color']).';  color: '.format_hex($POPUP_META['snp_submit_button_hover_text_color']).';}'."\n";
}

if (!empty($POPUP_META['snp_bg_color1']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme3 { background: '.format_hex($POPUP_META['snp_bg_color1']).';}'."\n";
}
if (!empty($POPUP_META['snp_bg_color2']))
{
    	echo '.snp-pop-'.$ID.' .snp-newtheme3 .snp-newsletter-content { background: '.format_hex($POPUP_META['snp_bg_color2']).';}'."\n";
}
echo '</style>';