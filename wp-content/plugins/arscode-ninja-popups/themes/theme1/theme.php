<?php

$SNP_THEMES_theme1_SIZES = array();
for ($i = 26; $i <= 72; $i++)
{
    $SNP_THEMES_theme1_SIZES[$i]	 = $i . 'px';
}
$SNP_THEMES['theme1']		 = array(
    'NAME'	 => 'Theme 1',
    'STYLES' => 'style.css',
    'TYPES'	 => array(
	'optin' => array('NAME'	 => 'Opt-in'),
	'social' => array('NAME'	 => 'Social'),
    ),
    'COLORS' => array(
	'darkgreen' => array('NAME'		 => 'Dark Green'),
	'lightgreen'	 => array('NAME'		 => 'Light Green'),
	'darkblue'	 => array('NAME'		 => 'Dark Blue'),
	'lightblue'	 => array('NAME'		 => 'Light Blue'),
	'darkred'	 => array('NAME'		 => 'Dark Red'),
	'lightred'	 => array('NAME'		 => 'Light Red'),
	'darkorange'	 => array('NAME'		 => 'Dark Orange'),
	'lightorange'	 => array('NAME'		 => 'Light Orange'),
	'darkyellow'	 => array('NAME'		 => 'Dark Yellow'),
	'lightyellow'	 => array('NAME'	 => 'Light Yellow'),
    ),
    'FIELDS' => array(
	array(
	    'id'	 => 'width',
	    'type'	 => 'text',
	    'title'	 => __('Width', 'nhp-opts'),
	    'desc'	 => __('px (default: 782)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '782'
	),
	array(
	    'id'	 => 'height',
	    'type'	 => 'text',
	    'title'	 => __('Height', 'nhp-opts'),
	    'desc'	 => __('px (optional, leave empty for auto-height)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => ''
	),
	array(
	    'id'		 => 'layout',
	    'type'		 => 'radio_img',
	    'title'		 => __('Layout', 'nhp-opts'),
	    'sub_desc'	 => '',
	    'desc'		 => '',
	    'options'	 => array(
		'head_txt_list' => array('title'		 => '', 'img'		 => SNP_URL . 'themes/theme1/gfx/head_txt_list.png'),
		'head_list_txt'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_list_txt.png'),
		'head_txt_list_full'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_txt_list_full.png'),
		'head_txt_list_img'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_txt_list_img.png'),
		'head_img_txt_list'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_img_txt_list.png'),
		'head_txt_list_img_2'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_txt_list_img_2.png'),
		'head_img_txt_list_2'	 => array('title'		 => '', 'img'		 => SNP_URL . 'themes/theme1/gfx/head_img_txt_list_2.png'),
		'head_img'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_img.png'),
		'head_txt_list_video'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_txt_list_video.png'),
		'head_video_txt_list'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_video_txt_list.png'),
		'head_txt_list_video_2'	 => array('title'			 => '', 'img'			 => SNP_URL . 'themes/theme1/gfx/head_txt_list_video_2.png'),
		'head_video_txt_list_2'	 => array('title'		 => '', 'img'		 => SNP_URL . 'themes/theme1/gfx/head_video_txt_list_2.png'),
		'head_video'	 => array('title'	 => '', 'img'	 => SNP_URL . 'themes/theme1/gfx/head_video.png'),
	    ),
	    'std'	 => 'head_txt_list'
	),
	array(
	    'id'	 => 'left_column_size',
	    'type'	 => 'text',
	    'class'	 => 'mini',
	    'std'	 => '50%',
	    'title'	 => __('Left Column Size', 'nhp-opts'),
	    'desc'	 => __('px or % (default: 50%)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'right_column_size',
	    'type'	 => 'text',
	    'class'	 => 'mini',
	    'std'	 => '50%',
	    'title'	 => __('Right Column Size', 'nhp-opts'),
	    'desc'	 => __('px or % (default: 50%)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header',
	    'type'	 => 'text',
	    'title'	 => __('Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header_size',
	    'type'	 => 'typo',
	    'title'	 => __('Header Font Size', 'nhp-opts'),
	    'desc'	 => __('px (default: 26px)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme1_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size' => '26'),
	),
	array(
	    'id'	 => 'maintext',
	    'type'	 => 'textarea',
	    'title'	 => __('Main Text', 'nhp-opts'),
	),
	array(
	    'id'	 => 'bulletlist',
	    'type'	 => 'multi_text',
	    'title'	 => __('Bullet List', 'nhp-opts'),
	),
	array(
	    'id'	 => 'img',
	    'type'	 => 'upload',
	    'title'	 => __('Image', 'nhp-opts'),
	),
	array(
	    'id'	 => 'video',
	    'type'	 => 'textarea',
	    'title'	 => __('Video Embed Code ', 'nhp-opts'),
	),
	array(
	    'id'	 => 'name_placeholder',
	    'type'	 => 'text',
	    'std'	 => 'Your Name...',
	    'title'	 => __('Name Placeholder', 'nhp-opts'),
	),
	array(
	    'id'		 => 'name_disable',
	    'type'		 => 'radio',
	    'title'		 => __('Disable Name Field', 'nhp-opts'),
	    'options'	 => array(0	 => 'No', 1	 => 'Yes'),
	    'std'	 => 0
	),
	array(
	    'id'	 => 'email_placeholder',
	    'type'	 => 'text',
	    'std'	 => 'Your E-mail...',
	    'title'	 => __('E-mail Placeholder', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button',
	    'type'	 => 'text',
	    'std'	 => 'Subscribe Now!',
	    'title'	 => __('Submit Button', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_loading',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Submit Button Loading Text', 'nhp-opts'),
	    'desc'	 => __('(ex: Please wait...)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_success',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Submit Button Success Text', 'nhp-opts'),
	    'desc'	 => __('(ex: Thank You!)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_color',
	    'type'	 => 'color_gradient',
	    //'std' => array('from' => '#42424C', 'to' => '#25262B'),
	    'desc'	 => __('(leave empty for default: #42424C, #25262B)', 'nhp-opts'),
	    'title'	 => __('Submit Button Background - Gradient', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_text_color',
	    'type'	 => 'color',
	    'desc'	 => __('(leave empty for default: #fff)', 'nhp-opts'),
	    'title'	 => __('Submit Button Text Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 'security_note',
	    'type'	 => 'text',
	    'title'	 => __('Security Note', 'nhp-opts'),
	),
    )
);
?>