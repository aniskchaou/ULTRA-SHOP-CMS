<?php

$SNP_THEMES_theme3_SIZES = array();
for ($i = 14; $i <= 72; $i++)
{
    $SNP_THEMES_theme3_SIZES[$i]	 = $i . 'px';
}
$SNP_THEMES['theme3']		 = array(
    'NAME'	 => 'Theme 3',
    'STYLES' => 'css/style.css',
    'TYPES'	 => array(
	'optin' => array('NAME'	 => 'Opt-in'),
	'social' => array('NAME'	 => 'Social'),
    ),
    'COLORS' => array(
	'lightblue' => array('NAME'		 => 'Light Blue'),
	'darkblue'	 => array('NAME'		 => 'Dark Blue'),
	'lightgreen'	 => array('NAME'		 => 'Light Green'),
	'darkgreen'	 => array('NAME'		 => 'Dark Green'),
	'lightorange'	 => array('NAME'		 => 'Light Orange'),
	'darkorange'	 => array('NAME'		 => 'Dark Orange'),
	'lightred'	 => array('NAME'		 => 'Light Red'),
	'darkred'	 => array('NAME'		 => 'Dark Red'),
	'lightyellow'	 => array('NAME'		 => 'Light Yellow'),
	'darkyellow'	 => array('NAME'	 => 'Dark Yellow'),
    ),
    'FIELDS' => array(
	array(
	    'id'	 => 'width',
	    'type'	 => 'text',
	    'title'	 => __('Width', 'nhp-opts'),
	    'desc'	 => __('px (default: 740)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '740'
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
	    'desc'	 => __('px (default: 26)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme3_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size' => '26'),
	),
	array(
	    'id'	 => 'sub_header',
	    'type'	 => 'text',
	    'title'	 => __('Sub Header (bold)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'sub_header_2',
	    'type'	 => 'text',
	    'title'	 => __('Sub Header (normal)', 'nhp-opts'),
	),
	array(
	    'id'	 => 'sub_header_size',
	    'type'	 => 'typo',
	    'title'	 => __('Header Font Size', 'nhp-opts'),
	    'desc'	 => __('px (default: 20)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme3_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size' => '20'),
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
	    'std'	 => 'Enter Your Name...',
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
	    'std'	 => 'Enter Your E-mail...',
	    'title'	 => __('E-mail Placeholder', 'nhp-opts'),
	),
	array(
	    'id'	 => 'cf',
	    'type'	 => 'custom_fields',
	    'std'	 => '',
	    'title'	 => __('Custom Fields', 'nhp-opts'),
	    'desc'	 => __('', 'nhp-opts'),
	    'icons'	 => array(
		// class => img
		'snp-field-name' => plugin_dir_url( __FILE__ ).'css/gfx/input-name.png',
		'snp-field-email' => plugin_dir_url( __FILE__ ).'css/gfx/input-email.png',
		'snp-field-phone' => plugin_dir_url( __FILE__ ).'css/gfx/input-phone.png',
		'snp-field-address' => plugin_dir_url( __FILE__ ).'css/gfx/input-address.png',
		'snp-field-website' => plugin_dir_url( __FILE__ ).'css/gfx/input-website.png',
	    )
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
	    'desc'	 => __('(leave empty for default: #F2E205, #E5AE08)', 'nhp-opts'),
	    'title'	 => __('Submit Button Background - Gradient', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_text_color',
	    'type'	 => 'color',
	    'desc'	 => __('(leave empty for default: #43261A)', 'nhp-opts'),
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