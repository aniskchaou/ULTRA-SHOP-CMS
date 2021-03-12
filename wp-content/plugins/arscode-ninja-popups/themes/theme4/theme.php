<?php

$SNP_THEMES_theme4_SIZES = array();
for ($i = 10; $i <= 72; $i++)
{
    $SNP_THEMES_theme4_SIZES[$i]	 = $i . 'px';
}
$SNP_THEMES['theme4']		 = array(
    'NAME'	 => 'Theme 4',
    'STYLES' => 'css/style.css',
    'TYPES'	 => array(
	'social' => array('NAME'	 => 'Social'),
	'optin'	 => array('NAME'	 => 'Opt-in'),
    ),
    'COLORS' => array(
	'lightgreen' => array('NAME'		 => 'Light Green'),
	'darkgreen'	 => array('NAME'		 => 'Dark Green'),
	'lightblue'	 => array('NAME'		 => 'Light Blue'),
	'darkblue'	 => array('NAME'		 => 'Dark Blue'),
	'lightred'	 => array('NAME'		 => 'Light Red'),
	'darkred'	 => array('NAME'		 => 'Dark Red'),
	'lightorange'	 => array('NAME'		 => 'Light Orange'),
	'darkorange'	 => array('NAME'		 => 'Dark Orange'),
	'lightyellow'	 => array('NAME'		 => 'Light Yellow'),
	'darkyellow'	 => array('NAME'	 => 'Dark Yellow'),
    ),
    'FIELDS' => array(
	array(
	    'id'	 => 'width',
	    'type'	 => 'text',
	    'title'	 => __('Width', 'nhp-opts'),
	    'desc'	 => __('px (default: 805)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '805'
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
	    'id'	 => 'header',
	    'type'	 => 'text',
	    'title'	 => __('Left Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header_font',
	    'type'	 => 'typo',
	    'title'	 => __('Left Header Font Size', 'nhp-opts'),
	    'desc'	 => __('(default size: 25)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme4_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size'	 => '25', 'color'	 => '#ffffff'),
	),
	array(
	    'id'	 => 'leftimg',
	    'type'	 => 'upload',
	    'title'	 => __('Left Image', 'nhp-opts'),
	),
	array(
	    'id'	 => 'lefttext',
	    'type'	 => 'textarea',
	    'title'	 => __('Left Text', 'nhp-opts'),
	),
	array(
	    'id'	 => 'lefttext_font',
	    'type'	 => 'typo',
	    'title'	 => __('Left Text Size', 'nhp-opts'),
	    'desc'	 => __('(default size: 13)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme4_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size' => '13'),
	),
	array(
	    'id'	 => 'bulletlist',
	    'type'	 => 'multi_text',
	    'title'	 => __('Left Bullet List', 'nhp-opts'),
	),
	array(
	    'id'	 => 'rightheader',
	    'type'	 => 'text',
	    'title'	 => __('Right Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 'rightimg',
	    'type'	 => 'upload',
	    'title'	 => __('Right Image', 'nhp-opts'),
	),
	array(
	    'id'	 => 'rightheader_font',
	    'type'	 => 'typo',
	    'title'	 => __('Right Header Font Size', 'nhp-opts'),
	    'desc'	 => __('(default size: 14)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme4_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size' => '14'),
	),
	array(
	    'id'	 => 'righttext',
	    'type'	 => 'textarea',
	    'title'	 => __('Right Text', 'nhp-opts'),
	),
	array(
	    'id'	 => 'righttext_font',
	    'type'	 => 'typo',
	    'title'	 => __('Right Text Size', 'nhp-opts'),
	    'desc'	 => __('(default size: 13)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme4_SIZES,
		'disable_fonts'	 => 1,
		'disable_colors' => 1,
	    ),
	    'std'		 => array('size' => '13'),
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