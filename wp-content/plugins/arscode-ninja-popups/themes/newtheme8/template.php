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

<?php $themeid = 'newtheme8'; ?>
<div class="snp-fb snp-<?php echo $themeid ?>">

<?php if ($POPUP_META['snp_show_cb_button'] == 'yes'): ?>
    <a class="snp-close snp_nothanks" href="#"></a>
<?php endif ?>

    <div class="snp-newsletter-content snp-clearfix">
        <header>

<?php if ($POPUP_META['snp_header_img_position']==0 && !empty($POPUP_META['snp_header_img'])) : ?>
            <div class="snp-banner1"><div class="snp-image"><img src="<?php echo $POPUP_META['snp_header_img'] ?>" alt="" /></div></div>
<?php endif ?>
			
            <h2><?php echo $POPUP_META['snp_header'] ?></h2>
            <h3><?php echo $POPUP_META['snp_maintext'] ?></h3>

<?php if ($POPUP_META['snp_header_img_position']==1 && !empty($POPUP_META['snp_header_img'])) : ?>
            <div class="snp-banner2"><div class="snp-image"><img src="<?php echo $POPUP_META['snp_header_img'] ?>" alt="" /></div></div>
<?php endif ?>

        </header>
        <form class="<?php echo $material; ?> snp-subscribeform snp_subscribeform" action="<?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_url');}else{echo '#';}?>" method="<?php echo $action; ?>" <?php if(snp_get_option('ml_manager') == 'html' && snp_get_option('ml_html_blank')){echo ' target="_blank"';}?>>
            <?php if(snp_get_option('ml_manager') == 'html'){echo snp_get_option('ml_html_hidden');}?>
            <input type="hidden" name="np_custom_name1" value="" />
            <input type="hidden" name="np_custom_name2" value="" />

            <fieldset>

<?php if (!empty($POPUP_META['snp_img'])) : ?>
                <div class="snp-half snp-left"><div class="snp-image"><img src="<?php echo $POPUP_META['snp_img'] ?>" alt="" /></div></div>
                <div class="snp-half snp-right">
<?php endif ?>

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

                    <input type="submit" class="snp-subscribe-button snp-submit" data-loading="<?php echo $POPUP_META['snp_submit_button_loading'];?>" data-success="<?php echo $POPUP_META['snp_submit_button_success'];?>" value="<?php echo $POPUP_META['snp_submit_button'];?>">

<?php if (!empty($POPUP_META['snp_img'])) : ?>
                </div>
<?php endif ?>

            </fieldset>
        </form>
    </div>

<?php if (!empty($POPUP_META['snp_security_note'])) : ?>
    <p class="snp-security-note"><small><?php echo $POPUP_META['snp_security_note']?></small></p>
<?php endif ?>

</div>

<?php
if(isset($POPUP_META['snp_header_font']))
{
    $POPUP_META['snp_header_font']=unserialize($POPUP_META['snp_header_font']);
}
if(isset($POPUP_META['snp_maintext_font']))
{
    $POPUP_META['snp_maintext_font']=unserialize($POPUP_META['snp_maintext_font']);
}
if(isset($POPUP_META['snp_fields_font']))
{
    $POPUP_META['snp_fields_font']=unserialize($POPUP_META['snp_fields_font']);
}
if(isset($POPUP_META['snp_submit_button_font']))
{
    $POPUP_META['snp_submit_button_font']=unserialize($POPUP_META['snp_submit_button_font']);
}
if(isset($POPUP_META['snp_security_note_font']))
{
    $POPUP_META['snp_security_note_font']=unserialize($POPUP_META['snp_security_note_font']);
}
?>
<style>
<?php
if (!empty($POPUP_META['snp_width']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' { width: '.$POPUP_META['snp_width'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_height']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' { min-height: '.$POPUP_META['snp_height'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_border_size']) && $POPUP_META['snp_border_size']>0 && !empty($POPUP_META['snp_border_color'])) {
    $margin = (40-$POPUP_META['snp_border_size']);
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' { border: '.$POPUP_META['snp_border_size'].'px solid '.format_hex($POPUP_META['snp_border_color']).';}'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-banner2 { margin-left: '.$margin.'px; margin-right: '.$margin.'px; }'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-newsletter-content header h2 { margin-left: '.$margin.'px; margin-right: '.$margin.'px; }'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-newsletter-content header h3 { margin-left: '.$margin.'px; margin-right: '.$margin.'px; }'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-subscribeform { margin-left: '.$margin.'px; margin-right: '.$margin.'px; }'."\n";
}

if (!empty($POPUP_META['snp_header_font']['size']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-newsletter-content header h2 {font-size: '.$POPUP_META['snp_header_font']['size'].'px; color: '.format_hex($POPUP_META['snp_header_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_maintext_font']['size']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-newsletter-content h3 {font-size: '.$POPUP_META['snp_maintext_font']['size'].'px; color: '.$POPUP_META['snp_maintext_font']['color'].';}'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-newsletter-content p small {color: '.format_hex($POPUP_META['snp_maintext_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_fields_font']['size']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' form input[type="text"],'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' form select,'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' form textarea { font-size: '.$POPUP_META['snp_fields_font']['size'].'px; color: '.format_hex($POPUP_META['snp_fields_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_font']['size']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-submit { font-size: '.$POPUP_META['snp_submit_button_font']['size'].'px; color: '.format_hex($POPUP_META['snp_submit_button_font']['color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_color']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-submit { background-color: '.format_hex($POPUP_META['snp_submit_button_color']).';}'."\n";
}
if (!empty($POPUP_META['snp_submit_button_padding']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-submit { padding-top: '.$POPUP_META['snp_submit_button_padding'].'px; padding-bottom: '.$POPUP_META['snp_submit_button_padding'].'px;}'."\n";
}
if (!empty($POPUP_META['snp_bg_color1']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' { background-color: '.format_hex($POPUP_META['snp_bg_color1']).';}'."\n";
}
if (!empty($POPUP_META['snp_fields_border_color']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' form input[type="text"],'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' form select,'."\n";
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' form textarea { border-color: '.format_hex($POPUP_META['snp_fields_border_color']).';}'."\n";
}
if (!empty($POPUP_META['snp_lock_img'])) {
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-security-note small{ background-image: url('. $POPUP_META['snp_lock_img'] .');}'."\n";
}
if (!empty($POPUP_META['snp_security_note_font']['size']))
{
    echo '.snp-pop-'.$ID.' .snp-'. $themeid .' .snp-security-note small { font-size: '.$POPUP_META['snp_security_note_font']['size'].'px; color: '.format_hex($POPUP_META['snp_security_note_font']['color']).';}'."\n";
}

?>
</style>