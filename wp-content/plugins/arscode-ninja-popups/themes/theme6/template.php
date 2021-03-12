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
<div class="snp-fb snp-theme6">
    <div class="snp-subscribe-inner">
	<?php
	if(!empty($POPUP_META['snp_header']))
	{
	    echo '<h1 class="snp-header">'.$POPUP_META['snp_header'].'</h1>'; 
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
		    <button type="submit" class="snp-submit">Submit</button>
		</fieldset>
	    </form>
	</div>
	<?php
	if ($POPUP_META['snp_show_cb_button'] == 'yes')
	{  
	    echo '<a href="#" class="snp_nothanks snp-close">'.$POPUP_META['snp_cb_text'].'</a>';
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
if(isset($POPUP_META['snp_header_size']))
{
	$POPUP_META['snp_header_size']=unserialize($POPUP_META['snp_header_size']);
}
echo '<style>';
if (!empty($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme6 { max-width: '.$POPUP_META['snp_width'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme6 { min-height: '.$POPUP_META['snp_height'].'px;}'."\n";
}

if (!empty($POPUP_META['snp_header_size']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-theme6 h1 {font-size: '.$POPUP_META['snp_header_size']['size'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_text_color']))
{
	echo '.snp-pop-'.$ID.' .snp-theme6 { color: '.$POPUP_META['snp_text_color'].';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-theme6 .snp-field ::-webkit-input-placeholder { color: '.$POPUP_META['snp_text_color'].';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-theme6 .snp-field :-moz-placeholder { color: '.$POPUP_META['snp_text_color'].';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-theme6 .snp-field :-ms-input-placeholder { color: '.$POPUP_META['snp_text_color'].';}'."\n";
	echo '.snp-pop-'.$ID.'  .snp-theme6 .snp-field input { border: 1px solid '.$POPUP_META['snp_text_color'].';}'."\n";
}
if (!empty($POPUP_META['snp_input_text_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-theme6 .snp-field { color: '.$POPUP_META['snp_input_text_color'].';}'."\n";
}
if (!empty($POPUP_META['snp_bg_color']))
{
    	echo '.snp-pop-'.$ID.' .snp-theme6 { background: '.$POPUP_META['snp_bg_color'].';}'."\n";
}
if (!empty($POPUP_META['snp_submit_color']) && $POPUP_META['snp_submit_color']!='#0095ca')
{
    	echo '.snp-pop-'.$ID.' .snp-theme6::before { background: '.$POPUP_META['snp_submit_color'].';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-theme6::after { border-left: 9px solid '.$POPUP_META['snp_submit_color'].';}'."\n";
	echo '.snp-pop-'.$ID.' .snp-theme6 .snp-submit { 
	    background-color: '.$POPUP_META['snp_submit_color'].';
	    border-top: 1px solid '.$POPUP_META['snp_submit_color'].';
	    border-bottom: 1px solid '.$POPUP_META['snp_submit_color'].';
	    border-left: 1px solid '.$POPUP_META['snp_submit_color'].';
	    border-right: 1px solid '.$POPUP_META['snp_submit_color'].';
	    box-shadow: inset 0 1px '.$POPUP_META['snp_submit_color'].', inset 0 -1px '.$POPUP_META['snp_submit_color'].';
	 }';
}
echo '</style>';
?>
