<?php

$action = 'post';
if (snp_get_option('form_action') == 'get' || $POPUP_META['snp_form_action'] == 'get') {
	$action = 'get';
}

$material = '';
if (isset($POPUP_META['snp_material']) && $POPUP_META['snp_material'] == 'yes') {
    $material = 'material';
}
?>


  <div  class="snp-fb snp-theme1<?php echo ' '.$POPUP_META['snp_layout'].''; ?> snp-theme1<?php echo '-'.$POPUP_META['snp_theme']['color'].''; ?>">
    <?php 
	if(!empty($POPUP_META['snp_header']) && !in_array($POPUP_META['snp_layout'],array('head_txt_list_img_2','head_img_txt_list_2','head_txt_list_video_2','head_video_txt_list_2')))
	{
		echo '<h1>'.$POPUP_META['snp_header'].'</h1>'; 
	}
	if(!empty($POPUP_META['snp_maintext']) || !empty($POPUP_META['snp_bulletlist']) || !empty($POPUP_META['snp_img']) || !empty($POPUP_META['snp_video']))
	{
		echo '<div class="snp-columns">';
		if(isset($POPUP_META['snp_header']) && in_array($POPUP_META['snp_layout'],array('head_txt_list_img_2','head_img_txt_list_2','head_txt_list_video_2','head_video_txt_list_2')))
		{
			echo '<h1>'.$POPUP_META['snp_header'].'</h1>'; 
		}
		if(isset($POPUP_META['snp_video']) && in_array($POPUP_META['snp_layout'],array('head_txt_video','head_list_video','head_video','head_video_list','head_video_txt','head_txt_list_video','head_video_txt_list','head_txt_list_video_2','head_video_txt_list_2')))
		{
		  echo '<p class="snp-video">'.$POPUP_META['snp_video'].'</p>'; 
		}
		if(isset($POPUP_META['snp_img']) && in_array($POPUP_META['snp_layout'],array('head_img_list','head_img_txt','head_list_img','head_txt_img','head_img','head_img_txt_list','head_txt_list_img','head_txt_list_img_2','head_img_txt_list_2')))
		{
		  echo '<p class="snp-img"><img src="'.$POPUP_META['snp_img'].'" alt=""></p>'; 
		}
		if(!empty($POPUP_META['snp_maintext']) && in_array($POPUP_META['snp_layout'],array('head_txt_list','head_img_txt','head_txt_img','head_list_txt','head_txt','head_txt_video','head_video_txt','head_img_txt_list','head_txt_list_img','head_txt_list_video','head_video_txt_list','head_txt_list_full','head_txt_list_img_2','head_img_txt_list_2','head_txt_list_video_2','head_video_txt_list_2')))
		{
		  echo '<p class="snp-info">'.nl2br($POPUP_META['snp_maintext']).'</p>'; 
		}	  
		if(!empty($POPUP_META['snp_bulletlist']) && in_array($POPUP_META['snp_layout'],array('head_txt_list','head_list_img','head_img_list','head_list_txt','head_list','head_video_list','head_list_video','head_img_txt_list','head_txt_list_img','head_txt_list_video','head_video_txt_list','head_txt_list_full','head_txt_list_img_2','head_img_txt_list_2','head_txt_list_video_2','head_video_txt_list_2')))
		{
			$POPUP_META['snp_bulletlist']=unserialize($POPUP_META['snp_bulletlist']);
			foreach($POPUP_META['snp_bulletlist'] as $k => $v)
			{
				if(!trim($v))
				{
					unset($POPUP_META['snp_bulletlist'][$k]);
				}
			}
			if(count($POPUP_META['snp_bulletlist'])>0)
			{
				echo '<ul class="snp-features">';
				foreach((array)$POPUP_META['snp_bulletlist'] as $v)
				{
					echo '<li>'.$v.'</li>';
				}
				echo '</ul>';  
			}
		}
		echo '</div>';
	}
	?>
      <div class="snp-subscribe<?php if($POPUP_META['snp_theme']['type']=='social'){ echo' snp-subscribe-social'; }?>">
		<?php
		if($POPUP_META['snp_theme']['type']=='optin')
		{
			/*<h2 style="text-align: center;">Thank you!</h2>*/
		?>
		  <form action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" class="<?php echo $material; ?> snp-subscribeform snp_subscribeform"<?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
		  	<input type="hidden" name="np_custom_name1" value="" />
        	<input type="hidden" name="np_custom_name2" value="" />
        	
		      <?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
			<fieldset>
				<?php
				if(!$POPUP_META['snp_name_disable'])
				{
					echo '<input type="text" name="'.((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_name')) ? snp_get_option('ml_html_name') : 'name').'" id="snp_name" placeholder="'.$POPUP_META['snp_name_placeholder'].'" class="snp-field snp-field-name" />';
				}
				?>
				<input type="text" name="<?php echo ((snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_email')) ? snp_get_option('ml_html_email') : 'email');?>" id="snp_email" placeholder="<?php echo $POPUP_META['snp_email_placeholder'];?>" class="snp-field snp-field-email" />			  
				<button type="submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" class="snp-subscribe-button"><?php echo $POPUP_META['snp_submit_button'];?></button>
			</fieldset>
		  </form>
		  <?php
		}
		elseif($POPUP_META['snp_theme']['type']=='social')
		{
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
		}
		?>      
    </div>

    <div class="snp-footer clearfix">
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
if(isset($POPUP_META['snp_header_size']))
{
	$POPUP_META['snp_header_size']=unserialize($POPUP_META['snp_header_size']);
}
if(isset($POPUP_META['snp_submit_button_color']))
{
	$POPUP_META['snp_submit_button_color']=unserialize($POPUP_META['snp_submit_button_color']);
}

echo '<style>';
if (!empty($POPUP_META['snp_name_disable']))
{
	echo '.snp-pop-'.$ID.' .snp-field-email {width: 350px;}';
}
if (!empty($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme1 { max-width: '.$POPUP_META['snp_width'].'px;}';
}
if (!empty($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme1 { min-height: '.$POPUP_META['snp_height'].'px;}';
}

if (!empty($POPUP_META['snp_header_size']['size']))
{
	echo '.snp-pop-'.$ID.' .snp-theme1 h1 {font-size: '.$POPUP_META['snp_header_size']['size'].'px;}';
}
if (!empty($POPUP_META['snp_submit_button_color']['from']))
{
	if(!$POPUP_META['snp_submit_button_color']['to'])
	{
		$POPUP_META['snp_submit_button_color']['to']=$POPUP_META['snp_submit_button_color']['from'];
	}
	echo '.snp-pop-'.$ID.' .snp-theme1 .snp-subscribe-button {
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
	echo '.snp-pop-'.$ID.' .snp-theme1 .snp-subscribe-button { color: '.$POPUP_META['snp_submit_button_text_color'].'; text-shadow: none;}';
}
if (!empty($POPUP_META['snp_left_column_size']) && !empty($POPUP_META['snp_right_column_size']))
{
	if(in_array($POPUP_META['snp_layout'],array('head_txt_list_img','head_txt_list_img_2','head_txt_list_video','head_txt_list_video_2')))
	{
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-info, .snp-pop-'.$ID.' .snp-theme1 .snp-features { width: '.$POPUP_META['snp_left_column_size'].'}';
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-img, .snp-pop-'.$ID.' .snp-theme1 .snp-video { width: '.$POPUP_META['snp_right_column_size'].'}';
	}
	if(in_array($POPUP_META['snp_layout'],array('head_txt_list_img_2','head_txt_list_video_2')))
	{
		echo '.snp-pop-'.$ID.' .snp-theme1 h1 { width: '.$POPUP_META['snp_left_column_size'].' !important}';
	}
	if(in_array($POPUP_META['snp_layout'],array('head_img_txt_list','head_img_txt_list_2','head_video_txt_list','head_video_txt_list_2')))
	{
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-info, .snp-pop-'.$ID.' .snp-theme1 .snp-features { width: '.$POPUP_META['snp_right_column_size'].'}';
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-img, .snp-pop-'.$ID.' .snp-theme1 .snp-video { width: '.$POPUP_META['snp_left_column_size'].'}';
	}
	if(in_array($POPUP_META['snp_layout'],array('head_img_txt_list_2','head_video_txt_list_2')))
	{
		echo '.snp-pop-'.$ID.' .snp-theme1 h1 { width: '.$POPUP_META['snp_right_column_size'].' !important}';
	}
	if(in_array($POPUP_META['snp_layout'],array('head_txt_list')))
	{
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-info { width: '.$POPUP_META['snp_left_column_size'].'; padding-right: 15px;}';
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-features { width: '.$POPUP_META['snp_right_column_size'].'}';
	}
	if(in_array($POPUP_META['snp_layout'],array('head_list_txt')))
	{
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-info { width: '.$POPUP_META['snp_right_column_size'].'}';
		echo '.snp-pop-'.$ID.' .snp-theme1 .snp-features { width: '.$POPUP_META['snp_left_column_size'].'; padding-right: 15px;}';
	}
}
echo '</style>';
?>