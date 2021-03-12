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

<div class="snp-fb snp-theme5">
	<h1><?php 
	if(!empty($POPUP_META['snp_header_img']))
	{
		echo '<img src="'.$POPUP_META['snp_header_img'].'" alt="" class="snp-header-icon">'; 
	}
	if(!empty($POPUP_META['snp_header']))
	{
		echo $POPUP_META['snp_header']; 
	}
	?></h1>
	<?php 
	if(!empty($POPUP_META['snp_maintext']))
	{
	  echo '<p class="snp-text">'.nl2br($POPUP_META['snp_maintext']).'</p>'; 
	}
	?>
	<div class="snp-subscribe<?php if($POPUP_META['snp_theme']['type']=='social'){ echo' snp-subscribe-social'; }?>">
		<?php
		if($POPUP_META['snp_theme']['type']=='optin')
		{
			?>
			 <form action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform"<?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
				<input type="hidden" name="np_custom_name1" value="" />
        		<input type="hidden" name="np_custom_name2" value="" />


			     <?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
				<fieldset>
  				    <input type="text" name="<?php echo ((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_email')) ? snp_get_option('ml_html_email') : 'email');?>" id="email" placeholder="<?php echo $POPUP_META['snp_email_placeholder'];?>" class="snp-field snp-field-email" />
					<button type="submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" class="snp-subscribe-button"><?php echo $POPUP_META['snp_submit_button'];?></button>
				</fieldset>
			</form>
			<?php
		}
		elseif($POPUP_META['snp_theme']['type']=='social')
		{
			if ($POPUP_META['snp_show_like_button'] == 'yes')
			{
				echo '<div class="snp-share snp-share-lb"><div class="fb-like" data-href="' . (!empty($POPUP_META['snp_fb_url']) ? $POPUP_META['snp_fb_url'] : $CURRENT_URL) . '" data-layout="standard" data-action="like" data-size="small" data-show-faces="false" data-share="false" data-width="450"></div></div>';
			}
			if ($POPUP_META['snp_show_gp_button'] == 'yes')
			{
				echo '<div class="snp-share snp-share-gp"><div class="g-plusone" data-size="medium" data-callback="snp_onshare_gp" data-href="' . (!empty($POPUP_META['snp_gp_url']) ? $POPUP_META['snp_gp_url'] : $CURRENT_URL) . '"></div></div>';
			}
			if ($POPUP_META['snp_show_tweet_button'] == 'yes')
			{
				echo '<div class="snp-share snp-share-tw"><a href="https://twitter.com/share" data-url="' . (!empty($POPUP_META['snp_tweet_url']) ? $POPUP_META['snp_tweet_url'] : $CURRENT_URL) . '" ' . (!empty($POPUP_META['snp_tweet_text']) ? 'data-text="' . $POPUP_META['snp_tweet_text'] . '"' : '') . ' class="twitter-share-button" data-lang="en">Tweet</a></div>';
			}
			if ($POPUP_META['snp_show_follow_button'] == 'yes')
			{
				echo '<div class="snp-share snp-share-ftw"><a href="https://twitter.com/' . $POPUP_META['snp_twitter_username'] . '" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @' . $POPUP_META['snp_twitter_username'] . '</a></div>';
			}
			if ($POPUP_META['snp_show_li_button'] == 'yes')
			{
				echo '<div class="snp-share snp-share-li"><script type="IN/Share" data-showzero="true" data-url="' . (!empty($POPUP_META['snp_li_url']) ? $POPUP_META['snp_li_url'] : $CURRENT_URL) . '" data-onsuccess="snp_onshare_li" data-counter="right"></script></div>';
			}
			if ($POPUP_META['snp_show_pi_button'] == 'yes')
			{
				echo '<div class="snp-share snp-share-pi"><a href="http://pinterest.com/pin/create/button/?url=' . urlencode(!empty($POPUP_META['snp_pi_url']) ? $POPUP_META['snp_pi_url'] : $CURRENT_URL) . '&media=' . urlencode(!empty($POPUP_META['snp_pi_image_url']) ? $POPUP_META['snp_pi_image_url'] : $CURRENT_URL) . '&description=' . urlencode(!empty($POPUP_META['snp_pi_description']) ? $POPUP_META['snp_pi_description'] : $CURRENT_URL) . '" class="pin-it-button" target="_blank" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>';
			}
			echo '<br style="clear: both;" />';
		}		
		?>
	</div>

	<div class="snp-footer snp-clearfix">
		<?php
		if (!empty($POPUP_META['snp_security_note']))
		{
			echo '<p class="snp-privacy"><span>'.$POPUP_META['snp_security_note'].'</span></p>';
		}
		if ($POPUP_META['snp_show_cb_button'] == 'yes')
		{
			echo '<a href="#" class="snp_nothanks snp-no-thx">'.$POPUP_META['snp_cb_text'].'</a>';
		}
		?>
	</div>
	<?php
	if((snp_get_option('PROMO_ON') && snp_get_option('PROMO_REF')) && SNP_PROMO_LINK!='')
	{
		$PROMO_LINK=SNP_PROMO_LINK.snp_get_option('PROMO_REF');
		echo '<div class="snp-powered">';
		echo '<a href="'.$PROMO_LINK.'" target="_blank">Powered by <strong>Ninja Popups</strong></a>';
		echo '</div>';
	}
	?>
</div>
<?php
$POPUP_META['snp_maintext_font']=unserialize($POPUP_META['snp_maintext_font']);
$POPUP_META['snp_header_font']=unserialize($POPUP_META['snp_header_font']);
$POPUP_META['snp_submit_button_color']=unserialize($POPUP_META['snp_submit_button_color']);
	$POPUP_META['snp_bg_gradient']=unserialize($POPUP_META['snp_bg_gradient']);
echo '<style>';
if (intval($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme5 { width: '.$POPUP_META['snp_width'].'px;}';
}
if (intval($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme5 { height: '.$POPUP_META['snp_height'].'px;}';
}
if (intval($POPUP_META['snp_email_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme5 .snp-field-email { width: '.$POPUP_META['snp_email_width'].'px;}';
}

if ($POPUP_META['snp_bg_gradient']['from'])
{
    if(!$POPUP_META['snp_bg_gradient']['to'])
	{
		$POPUP_META['snp_bg_gradient']['to']=$POPUP_META['snp_bg_gradient']['from'];
	}
	?>
		.snp-pop-<?php echo $ID; ?> .snp-theme5 {
		  background: <?php echo $POPUP_META['snp_bg_gradient']['to'];?>;
		  background-image: -moz-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 120%);
		  background-image: -webkit-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 120%);
		  background-image: -o-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 120%);
		  background-image: -ms-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 120%);
		  background-image: radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 120%);
		}
		.snp-pop-<?php echo $ID; ?> .snp-theme5 .snp-powered {
		  background: <?php echo $POPUP_META['snp_bg_gradient']['to'];?>;
		  color: <?php if ($POPUP_META['snp_header_font']['color']) {echo $POPUP_META['snp_header_font']['color'];}else{echo '#fff';}?>;
		}
	<?php
}
if ($POPUP_META['snp_bg_image'] && $POPUP_META['snp_bg_image']=='uploaded')
{
	echo '.snp-pop-'.$ID.' .snp-theme5 { background-image: url('.$POPUP_META['snp_bg_image_upload'].'); background-position: center center; background-repeat: no-repeat;}';
}
elseif($POPUP_META['snp_bg_image']!='uploaded' && $POPUP_META['snp_bg_image']!='disabled' && $POPUP_META['snp_bg_image'])
{
	echo '.snp-pop-'.$ID.' .snp-theme5 { background-image: url('.SNP_URL . 'themes/theme5/gfx/'.$POPUP_META['snp_bg_image'].'.jpg'.'); background-position: center center; background-repeat: no-repeat;}';
}
if ($POPUP_META['snp_closetext_color'])
{
	echo '.snp-pop-'.$ID.' .snp-theme5 .snp-no-thx, .snp-pop-'.$ID.' .snp-theme5 .snp-privacy { color: '.$POPUP_META['snp_closetext_color'].';}';
}
if ($POPUP_META['snp_header_font'])
{
	echo '.snp-pop-'.$ID.' .snp-theme5 h1 { font-size: '.$POPUP_META['snp_header_font']['size'].'px; color: '.$POPUP_META['snp_header_font']['color'].';}';
}
if ($POPUP_META['snp_maintext_font'])
{
	echo '.snp-pop-'.$ID.' .snp-theme5 .snp-text { font-size: '.$POPUP_META['snp_maintext_font']['size'].'px; color: '.$POPUP_META['snp_maintext_font']['color'].';}';
}
if ($POPUP_META['snp_submit_button_color']['from'])
{
	if(!$POPUP_META['snp_submit_button_color']['to'])
	{
		$POPUP_META['snp_submit_button_color']['to']=$POPUP_META['snp_submit_button_color']['from'];
	}
	echo '.snp-pop-'.$ID.' .snp-theme5 .snp-subscribe-button {
	  background: '.$POPUP_META['snp_submit_button_color']['from'].';
	  background-image: -webkit-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: -moz-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: -ms-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: -o-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  color:#fff;	
	}';
}
if ($POPUP_META['snp_submit_button_text_color'])
{
	echo '.snp-pop-'.$ID.' .snp-theme5 .snp-subscribe-button { color: '.$POPUP_META['snp_submit_button_text_color'].'; text-shadow: none;}';
}
echo '</style>';
?>