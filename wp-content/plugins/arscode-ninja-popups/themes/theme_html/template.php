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
<div class="snp-fb snp-theme-html">
	<div class="snp-content">
		<div class="snp-content-inner">
			<?php
			
			if($ID==-1)
			{
				$content=stripslashes($_POST['content']);
				echo apply_filters('the_content', do_shortcode($content));
			}
			else
			{
				$content_post = get_post($ID);
				echo apply_filters('the_content', do_shortcode($content_post->post_content));
			}
			if($POPUP_META['snp_theme']['type']=='optin')
			{
				echo '<div class="snp-subscribe">';
				?>
				<form action="#" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform">
					<input type="hidden" name="np_custom_name1" value="" />
        			<input type="hidden" name="np_custom_name2" value="" />

					<fieldset>
						<input type="text" name="email" placeholder="<?php echo $POPUP_META['snp_email_placeholder'];?>" class="snp-field snp-field-email" />
						<button type="submit" class="snp-subscribe-button"><?php echo $POPUP_META['snp_submit_button'];?></button>
					</fieldset>
				</form>
				<?php
				if ($POPUP_META['snp_security_note'])
				{
					echo '<p class="snp-privacy"><span>'.$POPUP_META['snp_security_note'].'</span></p>';
				}
				echo '</div>';
			}
			elseif($POPUP_META['snp_theme']['type']=='social')
			{
				echo '<div class="snp-subscribe snp-subscribe-social">';
				if ($POPUP_META['snp_show_like_button'] == 'yes')
				{
					echo '<div class="snp-share snp-share-lb"><div class="fb-like" data-href="' . ($POPUP_META['snp_fb_url'] ? $POPUP_META['snp_fb_url'] : $CURRENT_URL) . '" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div></div>';
				}
				if ($POPUP_META['snp_show_gp_button'] == 'yes')
				{
					echo '<div class="snp-share snp-share-gp"><div class="g-plusone" data-size="medium" data-callback="snp_onshare_gp" data-href="' . ($POPUP_META['snp_gp_url'] ? $POPUP_META['snp_gp_url'] : $CURRENT_URL) . '"></div></div>';
				}
				if ($POPUP_META['snp_show_tweet_button'] == 'yes')
				{
					echo '<div class="snp-share snp-share-tw"><a href="https://twitter.com/share" data-url="' . $POPUP_META['snp_tweet_url'] . '" ' . ($POPUP_META['snp_tweet_text'] ? 'data-text="' . $POPUP_META['snp_tweet_text'] . '"' : '') . ' class="twitter-share-button" data-lang="en">Tweet</a></div>';
				}
				if ($POPUP_META['snp_show_follow_button'] == 'yes')
				{
					echo '<div class="snp-share snp-share-ftw"><a href="https://twitter.com/' . $POPUP_META['snp_twitter_username'] . '" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @' . $POPUP_META['snp_twitter_username'] . '</a></div>';
				}
				if ($POPUP_META['snp_show_li_button'] == 'yes')
				{
					echo '<div class="snp-share snp-share-li"><script type="IN/Share" data-showzero="true" data-url="' . $POPUP_META['snp_li_url'] . '" data-onsuccess="snp_onshare_li" data-counter="right"></script></div>';
				}
				if ($POPUP_META['snp_show_pi_button'] == 'yes')
				{
					echo '<div class="snp-share snp-share-pi"><a href="http://pinterest.com/pin/create/button/?url=' . urlencode($POPUP_META['snp_pi_url'] ? $POPUP_META['snp_pi_url'] : $CURRENT_URL) . '&media=' . urlencode($POPUP_META['snp_pi_image_url']) . '&description=' . urlencode($POPUP_META['snp_pi_description']) . '" class="pin-it-button" target="_blank" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>';
				}
				if ($POPUP_META['snp_show_cb_button'] == 'yes' && $POPUP_META['snp_cb_text'])
				{
					echo '<div><a href="#" class="snp_nothanks snp-no-thx">' . $POPUP_META['snp_cb_text'] . '</a></div>';
				}
				echo '</div>';
			}
			?>
        </div>
	</div>
	<?php
	if($POPUP_META['snp_width']>260 && snp_get_option('PROMO_ON') && snp_get_option('PROMO_REF') && SNP_PROMO_LINK!='')
	{
		$PROMO_LINK=SNP_PROMO_LINK.snp_get_option('PROMO_REF');
		echo '<div class="snp-powered">';
		echo '<a href="'.$PROMO_LINK.'" target="_blank">Powered by <strong>Ninja Popups</strong></a>';
		echo '</div>';
	}
	?>
</div>
<?php
echo '<style>';
//echo '.snp-pop-'.$ID.' { border: 5px red solid;}';
if (intval($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-html { max-width: '.$POPUP_META['snp_width'].'px;}';
}
if (intval($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-html { height: '.$POPUP_META['snp_height'].'px;}';
}
if ($POPUP_META['snp_bg_gradient'])
{
	$POPUP_META['snp_bg_gradient']=unserialize($POPUP_META['snp_bg_gradient']);
	if(!$POPUP_META['snp_bg_gradient']['to'])
	{
		$POPUP_META['snp_bg_gradient']['to']=$POPUP_META['snp_bg_gradient']['from'];
	}
	if($POPUP_META['snp_bg_gradient']['from'])
	{
	?>
		.snp-pop-<?php echo $ID; ?> .snp-theme-html{
		  background: <?php echo $POPUP_META['snp_bg_gradient']['to'];?>;
		  background-image: -moz-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo format_hex($POPUP_META['snp_bg_gradient']['to']);?> 500%);
		  background-image: -webkit-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo format_hex($POPUP_META['snp_bg_gradient']['to']);?> 500%);
		  background-image: -o-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo format_hex($POPUP_META['snp_bg_gradient']['to']);?> 500%);
		  background-image: -ms-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo format_hex($POPUP_META['snp_bg_gradient']['to']);?> 500%);
		  background-image: radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo format_hex($POPUP_META['snp_bg_gradient']['to']);?> 500%);
		}
		.snp-pop-<?php echo $ID; ?> .snp-theme-html .snp-powered {
		  <?php if ($POPUP_META['snp_bg_gradient']['from']) {echo 'background:'.format_hex($POPUP_META['snp_bg_gradient']['from']).';';}?>
		  <?php if (!empty($POPUP_META['snp_closetext_color'])) {echo 'color: '.format_hex($POPUP_META['snp_closetext_color']).';';}?>
		}
	<?php
	}
}
if (!empty($POPUP_META['snp_closetext_color']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-html .snp-no-thx, .snp-pop-'.$ID.' .snp-theme-html .snp-powered , .snp-pop-'.$ID.' .snp-theme-html .snp-privacy { color: '.format_hex($POPUP_META['snp_closetext_color']).';}';
}
echo '</style>';
?>