<?php

$SNP_THEMES_theme7_SIZES = array();
for ($i = 10; $i <= 72; $i++)
{
    $SNP_THEMES_theme7_SIZES[$i]	 = $i . 'px';
}
$SNP_THEMES['theme7']		 = array(
    'NAME'	 => 'Theme 7',
    'STYLES' => 'css/theme7.css',
    'TYPES'	 => array(
	'optin' => array('NAME'	 => 'Opt-in')
    ),
    'COLORS' => array(
	'multicolors' => array('NAME'	 => 'Multicolors')
    ),
    'FIELDS' => array(
	array(
	    'id'	 => 'width',
	    'type'	 => 'text',
	    'title'	 => __('Width', 'nhp-opts'),
	    'desc'	 => __('px (default: 500)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '500'
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
	    'id'	 => 'header1',
	    'type'	 => 'text',
	    'title'	 => __('Header Line 1', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header1_font',
	    'type'	 => 'typo',
	    'title'	 => __('Header Line 1 Font', 'nhp-opts'),
	    'desc'	 => __('px (default: 17px, #0d7bd6)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme7_SIZES,
		'disable_fonts'	 => 1,
	    ),
	    'std'		 => array('size'	 => '17', 'color'	 => '#0d7bd6'),
	),
	array(
	    'id'	 => 'header2',
	    'type'	 => 'text',
	    'title'	 => __('Header Line 2', 'nhp-opts'),
	),
	array(
	    'id'	 => 'header2_font',
	    'type'	 => 'typo',
	    'title'	 => __('Header Line 2 Font', 'nhp-opts'),
	    'desc'	 => __('px (default: 17px, #979ea3)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme7_SIZES,
		'disable_fonts'	 => 1,
	    ),
	    'std'		 => array('size'	 => '17', 'color'	 => '#979ea3'),
	),
	array(
	    'id'	 => 'maintext',
	    'type'	 => 'textarea',
	    'title'	 => __('Main Text', 'nhp-opts'),
	),
	array(
	    'id'	 => 'maintext_font',
	    'type'	 => 'typo',
	    'title'	 => __('Main Text Font Size', 'nhp-opts'),
	    'desc'	 => __('px (default: 12px, #7b7d7f)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_theme7_SIZES,
		'disable_fonts'	 => 1,
	    ),
	    'std'		 => array('size'	 => '12', 'color'	 => '#7b7d7f'),
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
	    'type'	 => 'color',
	    'std'	 => '#0d7bd6',
	    'desc'	 => __('(default: #0d7bd6)', 'nhp-opts'),
	    'title'	 => __('Submit Button Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_text_color',
	    'type'	 => 'color',
	    'std'	 => '#ffffff',
	    'desc'	 => __('(default: #ffffff)', 'nhp-opts'),
	    'title'	 => __('Submit Button Text Color', 'nhp-opts'),
	),
    )
);
?>