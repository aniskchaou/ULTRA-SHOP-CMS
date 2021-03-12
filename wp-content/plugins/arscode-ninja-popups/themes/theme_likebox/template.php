<?php
if (!intval($POPUP_META['snp_width']))
{
	$POPUP_META['snp_width']='415';
}
if (!intval($POPUP_META['snp_height']))
{
	$POPUP_META['snp_height']='345';
}
$POPUP_META['snp_bg_gradient']=unserialize($POPUP_META['snp_bg_gradient']);
?>
<div class="snp-fb snp-theme-likebox">
	<div class="snp-content">
		<div class="snp-content-inner">
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.11";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<?php
				if(!empty($POPUP_META['snp_facebook_url']))
				{
				echo '<div class="fb-page" data-href="'.($POPUP_META['snp_facebook_url']).'" data-tabs="'.($POPUP_META['snp_lb_show_stream'] ? 'timeline' : '').'" data-width="'.($POPUP_META['snp_width']-10).'" data-height="'.($POPUP_META['snp_height']-10).'" data-small-header="'.($POPUP_META['snp_lb_small_header']? 'true' : 'false').'" data-hide-cover="'.($POPUP_META['snp_lb_hidecoverfoto']? 'true' : 'false').'" data-show-facepile="'.($POPUP_META['snp_lb_show_faces'] ? 'true' : 'false').'"><div class="fb-xfbml-parse-ignore"><blockquote cite="'.($POPUP_META['snp_facebook_url']).'"><a href="'.($POPUP_META['snp_facebook_url']).'">Facebook</a></blockquote></div></div>';
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
if (intval($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-likebox { min-height: '.$POPUP_META['snp_height'].'px;}';
}
if (intval($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-likebox { min-width: '.$POPUP_META['snp_width'].'px;}';
}
if ($POPUP_META['snp_bg_gradient'])
{
	if(!$POPUP_META['snp_bg_gradient']['to'])
	{
		$POPUP_META['snp_bg_gradient']['to']=$POPUP_META['snp_bg_gradient']['from'];
	}
	?>
		.snp-pop-<?php echo $ID; ?> .snp-theme-likebox{
		  background: <?php echo $POPUP_META['snp_bg_gradient']['to'];?>;
		  background-image: -moz-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: -webkit-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: -o-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: -ms-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		}
		.snp-pop-<?php echo $ID; ?> .snp-theme-likebox .snp-powered {
		  <?php if ($POPUP_META['snp_bg_gradient']['from']) {echo 'background:'.$POPUP_META['snp_bg_gradient']['from'].';';}?>
		}
	<?php
}
echo '</style>';

echo '<script>';

echo '</script>';
?>