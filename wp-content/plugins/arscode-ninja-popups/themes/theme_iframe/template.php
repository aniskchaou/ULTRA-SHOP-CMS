<div class="snp-fb snp-theme-iframe">
	<?php
	if(!empty($POPUP_META['snp_iframe_url']))
	{
		echo '<iframe src="'.$POPUP_META['snp_iframe_url'].'"></iframe>'; 
	}
	?>
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
echo '<style>';
if (intval($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-iframe { width: '.$POPUP_META['snp_width'].'px;}';
}
if (intval($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-iframe { height: '.$POPUP_META['snp_height'].'px;}';
}
echo '</style>';
?>