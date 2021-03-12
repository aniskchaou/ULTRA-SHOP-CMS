<?php

$SNP_THEMES_theme5_SIZES = array();
for ($i = 14; $i <= 72; $i++)
{
    $SNP_THEMES_theme5_SIZES[$i]	 = $i . 'px';
}
$SNP_THEMES['theme5']		 = array(
    'NAME'	 => 'Theme 5',
    'STYLES' => 'style.css',
    'TYPES'	 => array(
	'optin' => array('NAME'	 => 'Opt-in'),
	'social' => array('NAME'	 => 'Social'),
    ),
    'COLORS' => array(
	'multicolors' => array('NAME'	 => 'Multicolors')
    ),
    'FIELDS' => array(
	array(
	    'id'	 => 'width',
	    'type'	 => 'text',
	    'title'	 => __('Width', 'nhp-opts'),
	    'desc'	 => __('px (default: 600)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '600'
	),
	array(
	    'id'	 => 'height',
	    'type'	 => 'text',
	    'title'	 => __('Height', 'nhp-opts'),
	    'desc'	 => __('px (default: 400)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '400'
	),
	array(
	    'id'	 => 'header_img',
	    'type'	 => 'upload',
	    'title'	 => __('Header Small Image', 'nhp-opts'),
	    'desc'	 => __('(max 120px x 100px)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header',
	    'type'	 => 'text',
	    'title'	 => __('Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header_font',
	    'type'	 => 'typo',
	    'title'	 => __('Header Font Size', 'nhp-opts'),
	    'desc'	 => __('(default size: 40, color: #ffffff)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme5_SIZES,
		'disable_fonts'	 => 1,
	    //'disable_colors' => 1,
	    ),
	    'std'		 => array('size'	 => '40', 'color'	 => '#ffffff'),
	),
	/* array(
	  'id' => 'header_color',
	  'type' => 'color',
	  'title' => __('Header color', 'nhp-opts'),
	  'std' => '#ffffff'
	  ),
	  array(
	  'id' => 'mainimg',
	  'type' => 'upload',
	  'title' => __('Main Image', 'nhp-opts'),
	  ), */
	array(
	    'id'	 => 'maintext',
	    'type'	 => 'textarea',
	    'title'	 => __('Main Text', 'nhp-opts'),
	),
	array(
	    'id'	 => 'maintext_font',
	    'type'	 => 'typo',
	    'title'	 => __('Main Text Size', 'nhp-opts'),
	    'desc'	 => __('(default size: 18, color: #ffffff)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme5_SIZES,
		'disable_fonts'	 => 1,
	    //'disable_colors' => 1,
	    ),
	    'std'		 => array('size'	 => '18', 'color'	 => '#ffffff'),
	),
	array(
	    'id'	 => 'bg_gradient',
	    'type'	 => 'color_gradient',
	    'title'	 => __('Background Color - Gradient', 'nhp-opts'),
	),
	array(
	    'id'		 => 'bg_image',
	    'type'		 => 'radio_img',
	    'title'		 => __('Background Image', 'nhp-opts'),
	    'sub_desc'	 => '',
	    'desc'		 => '',
	    'options'	 => array(
		'disabled' => array('title'	 => '', 'img'	 => SNP_URL . 'themes/theme5/gfx/bgdisabled.jpg'),
		'bg1'	 => array('title'	 => '', 'img'	 => SNP_URL . 'themes/theme5/gfx/bg1mini.jpg'),
		'bg2'	 => array('title'	 => '', 'img'	 => SNP_URL . 'themes/theme5/gfx/bg2mini.jpg'),
		'bg3'	 => array('title'		 => '', 'img'		 => SNP_URL . 'themes/theme5/gfx/bg3mini.jpg'),
		'uploaded'	 => array('title'	 => '', 'img'	 => SNP_URL . 'themes/theme5/gfx/bguploaded.jpg'),
	    ),
	    'std'	 => 'bg_1'
	),
	array(
	    'id'	 => 'bg_image_upload',
	    'type'	 => 'upload',
	    'title'	 => __('Background Image - Upload', 'nhp-opts'),
	),
	array(
	    'id'	 => 'email_placeholder',
	    'type'	 => 'text',
	    'std'	 => 'enter your e-mail...',
	    'title'	 => __('E-mail Placeholder', 'nhp-opts'),
	),
	array(
	    'id'	 => 'email_width',
	    'type'	 => 'text',
	    'std'	 => '360',
	    'class'	 => 'mini',
	    'title'	 => __('E-mail Field Width', 'nhp-opts'),
	    'desc'	 => __('px (default: 360)', 'nhp-opts'),
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
	    'std'	 => array('from'	 => '#345c9f', 'to'	 => '#24406f'),
	    'desc'	 => __('(leave empty for default: #345c9f, #24406f)', 'nhp-opts'),
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
	array(
	    'id'	 => 'closetext_color',
	    'type'	 => 'color',
	    'title'	 => __('Close Text Color', 'nhp-opts'),
	    'std'	 => '#979797'
	),
    )
);
?>