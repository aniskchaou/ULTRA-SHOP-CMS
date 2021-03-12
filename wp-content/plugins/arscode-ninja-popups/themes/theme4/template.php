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

<div class="snp-fb snp-theme4 snp-theme4<?php echo '-'.$POPUP_META['snp_theme']['color'].''; ?>">
	<div class="snp-clearfix">
		<div class="snp-left">
			<?php
			if (!empty($POPUP_META['snp_header']))
			{
				echo '<h1>' . $POPUP_META['snp_header'] . '</h1>';
			}
			if(!empty($POPUP_META['snp_leftimg']))
			{
			  echo '<div class="snp-img-div"><img src="'.$POPUP_META['snp_leftimg'].'" alt="" class="snp-image"></div>'; 
			} 
			if (!empty($POPUP_META['snp_lefttext']))
			{
				echo '<p>' . nl2br($POPUP_META['snp_lefttext']) . '</p>';
			}
			if (!empty($POPUP_META['snp_bulletlist']))
			{
				$POPUP_META['snp_bulletlist'] = unserialize($POPUP_META['snp_bulletlist']);
				foreach ($POPUP_META['snp_bulletlist'] as $k => $v)
				{
					if (!trim($v))
					{
						unset($POPUP_META['snp_bulletlist'][$k]);
					}
				}
				if (count($POPUP_META['snp_bulletlist']) > 0)
				{
					echo '<ul class="snp-features">';
					foreach ((array) $POPUP_META['snp_bulletlist'] as $v)
					{
						echo '<li>' . $v . '</li>';
					}
					echo '</ul>';
				}
			}
			if ($POPUP_META['snp_show_cb_button'] == 'yes')
			{
				echo '<a href="#" class="snp_nothanks snp-no-thx">' . $POPUP_META['snp_cb_text'] . '</a>';
			}
			?>
		</div>
		<div class="snp-subscribe<?php if($POPUP_META['snp_theme']['type']=='social'){ echo' snp-subscribe-social'; }?>">
			<div class="snp-subscribe-outer">
				<div class="snp-subscribe-inner">
					<?php
					if (!empty($POPUP_META['snp_rightheader']))
					{
						echo '<h2><b>' . $POPUP_META['snp_rightheader'] . '</b></h2>';
					}
					if(!empty($POPUP_META['snp_rightimg']))
					{
						echo '<img src="'.$POPUP_META['snp_rightimg'].'" alt="" class="snp-image">'; 
					}
					if (!empty($POPUP_META['snp_righttext']))
					{
						echo '<p>' . nl2br($POPUP_META['snp_righttext']) . '</p>';
					}
					if($POPUP_META['snp_theme']['type']=='optin')
					{
					?>
					  <form action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform"<?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
					  	<input type="hidden" name="np_custom_name1" value="" />
        				<input type="hidden" name="np_custom_name2" value="" />

					      <?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
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
						    else
						    {
						      if(!$POPUP_META['snp_name_disable'])
						      {
							    echo '<input type="text" name="'.((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_name')) ? snp_get_option('ml_html_name') : 'name').'" id="name" placeholder="'.$POPUP_META['snp_name_placeholder'].'" class="snp-field snp-field-name" />';
						      }
						      ?>
						      <input type="text" name="<?php echo ((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_email')) ? snp_get_option('ml_html_email') : 'email');?>" id="email" placeholder="<?php echo $POPUP_META['snp_email_placeholder'];?>" class="snp-field snp-field-email" />						  
						      <?php
						    }
						    ?> 
						  <button type="submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" class="snp-subscribe-button"><?php echo $POPUP_META['snp_submit_button'];?></button>
					</form>
					<?php
						if (!empty($POPUP_META['snp_security_note']))
						{
							echo '<p class="snp-privacy">'.$POPUP_META['snp_security_note'].'</p>';
						}
					}
					elseif($POPUP_META['snp_theme']['type']=='social')
					{
						if ($POPUP_META['snp_show_like_button'] == 'yes')
						{
							echo '<div class="snp-share snp-share-lb"><div class="fb-like" data-href="' . (isset($POPUP_META['snp_fb_url']) ? $POPUP_META['snp_fb_url'] : $CURRENT_URL) . '" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div></div>';
						}
						if ($POPUP_META['snp_show_gp_button'] == 'yes')
						{
							echo '<div class="snp-share snp-share-gp"><div class="g-plusone" data-size="medium" data-callback="snp_onshare_gp" data-href="' . (isset($POPUP_META['snp_gp_url']) ? $POPUP_META['snp_gp_url'] : $CURRENT_URL) . '"></div></div>';
						}
						if ($POPUP_META['snp_show_tweet_button'] == 'yes')
						{
							echo '<div class="snp-share snp-share-tw"><a href="https://twitter.com/share" data-url="' . (isset($POPUP_META['snp_tweet_url']) ? $POPUP_META['snp_tweet_url'] : '') . '" ' . (isset($POPUP_META['snp_tweet_text']) ? 'data-text="' . $POPUP_META['snp_tweet_text'] . '"' : '') . ' class="twitter-share-button" data-lang="en">Tweet</a></div>';
						}
						if ($POPUP_META['snp_show_follow_button'] == 'yes')
						{
							echo '<div class="snp-share snp-share-ftw"><a href="https://twitter.com/' . $POPUP_META['snp_twitter_username'] . '" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @' . $POPUP_META['snp_twitter_username'] . '</a></div>';
						}
						if ($POPUP_META['snp_show_li_button'] == 'yes')
						{
							echo '<div class="snp-share snp-share-li"><script type="IN/Share" data-showzero="true" data-url="' . (isset($POPUP_META['snp_li_url']) ? $POPUP_META['snp_li_url'] : '') . '" data-onsuccess="snp_onshare_li" data-counter="right"></script></div>';
						}
						if ($POPUP_META['snp_show_pi_button'] == 'yes')
						{
							echo '<div class="snp-share snp-share-pi"><a href="http://pinterest.com/pin/create/button/?url=' . urlencode(isset($POPUP_META['snp_pi_url']) ? $POPUP_META['snp_pi_url'] : $CURRENT_URL) . '&media=' . urlencode(isset($POPUP_META['snp_tweet_text']) ? $POPUP_META['snp_tweet_text'] : '') . '&description=' . urlencode(isset($POPUP_META['snp_tweet_text']) ? $POPUP_META['snp_tweet_text'] : '') . '" class="pin-it-button" target="_blank" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>';
						}
					}
					?>
				</div>
			</div>
        </div>
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
//if (!empty($POPUP_META['snp_height']))
{/*
?>
<script>
	function snp_th4_ad()
	{
		if(parseInt(jQuery("<?php echo '.snp-pop-' . $ID . ''; ?>").height())>0)
		{
			jQuery("<?php echo '.snp-pop-' . $ID . ''; ?> .snp-subscribe").height(jQuery("<?php echo '.snp-pop-' . $ID . ''; ?>").height());
		}
	}
	jQuery(document).ready(function($){
		snp_th4_ad();
		setTimeout('snp_th4_ad',5000);
	});
</script>
<?php*/
}
$POPUP_META['snp_header_font'] = unserialize($POPUP_META['snp_header_font']);
$POPUP_META['snp_lefttext_font'] = unserialize($POPUP_META['snp_lefttext_font']);
$POPUP_META['snp_rightheader_font'] = unserialize($POPUP_META['snp_rightheader_font']);
$POPUP_META['snp_righttext_font'] = unserialize($POPUP_META['snp_righttext_font']);
if(isset($POPUP_META['snp_submit_button_color']))
{
	$POPUP_META['snp_submit_button_color']=unserialize($POPUP_META['snp_submit_button_color']);
}
echo '<style>';
if (intval($POPUP_META['snp_width']))
{
	echo '.snp-pop-' . $ID . ' .snp-theme4 {width: ' . $POPUP_META['snp_width'] . 'px;}';
}
if (!empty($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme4 {height: '.$POPUP_META['snp_height'].'px;}';
}
if ($POPUP_META['snp_header_font'])
{
	echo '.snp-pop-' . $ID . ' .snp-theme4 h1 { font-size: ' . $POPUP_META['snp_header_font']['size'] . 'px;}';
}
if ($POPUP_META['snp_lefttext_font'])
{
	echo '.snp-pop-' . $ID . ' .snp-theme4 .snp-left p { font-size: ' . $POPUP_META['snp_lefttext_font']['size'] . 'px;}';
}
if ($POPUP_META['snp_rightheader_font'])
{
	echo '.snp-pop-' . $ID . ' .snp-theme4 .snp-subscribe h2 { font-size: ' . $POPUP_META['snp_rightheader_font']['size'] . 'px;}';
}
if ($POPUP_META['snp_righttext_font'])
{
	echo '.snp-pop-' . $ID . ' .snp-theme4 .snp-subscribe p { font-size: ' . $POPUP_META['snp_righttext_font']['size'] . 'px;}';
}
if ($POPUP_META['snp_submit_button_color']['from'])
{
	if(!$POPUP_META['snp_submit_button_color']['to'])
	{
		$POPUP_META['snp_submit_button_color']['to']=$POPUP_META['snp_submit_button_color']['from'];
	}
	echo '.snp-pop-'.$ID.' .snp-theme4 .snp-subscribe-button {
	  background: '.$POPUP_META['snp_submit_button_color']['from'].';
	  background-image: -webkit-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: -moz-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: -ms-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: -o-linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  background-image: linear-gradient(top, '.$POPUP_META['snp_submit_button_color']['from'].' 0, '.$POPUP_META['snp_submit_button_color']['to'].' 100%);
	  color:#fff;	
	}';
}
if (!empty($POPUP_META['snp_submit_button_text_color']))
{
	echo '.snp-pop-'.$ID.' .snp-theme4 .snp-subscribe-button { color: '.$POPUP_META['snp_submit_button_text_color'].'; text-shadow: none;}';
}
echo '</style>';
?>