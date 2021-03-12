<?php

$SNP_THEMES_newtheme6_SIZES = array();
for ($i = 10; $i <= 72; $i++)
{
    $SNP_THEMES_newtheme6_SIZES[$i]	 = $i . 'px';
}
$SNP_THEMES['newtheme6']		 = array(
    'NAME'	 => 'New Theme 6',
    'STYLES' => 'css/newtheme6.css',
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
	    'desc'	 => __('px (default: 890)', 'nhp-opts'),
	    'class'	 => 'mini',
	    'std'	 => '890'
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
	    'id'	 => 'bg_color1',
	    'type'	 => 'color',
	    'std'	 => '#e7e7e7',
	    'desc'	 => __('(default: #e7e7e7)', 'nhp-opts'),
	    'title'	 => __('Background Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 'divide',
	    'type'	 => 'divide',
	    'title'	 => __('Step 1', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1header',
	    'type'	 => 'text',
	    'title'	 => __('Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1header_font',
	    'type'	 => 'typo',
	    'title'	 => __('Header Font', 'nhp-opts'),
	    'desc'	 => __('px (default: 44px, #363636)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_newtheme6_SIZES,
		'disable_fonts'	 => 1,
		'disable_color'	 => 1,
	    ),
	    'std'		 => array('size'	 => '44', 'color'	 => '#363636'),
	),
	array(
	    'id'	 => 's1subheader',
	    'type'	 => 'text',
	    'title'	 => __('Subheader', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1subheader_font',
	    'type'	 => 'typo',
	    'title'	 => __('Subheader Font Size', 'nhp-opts'),
	    'desc'	 => __('px (default: 70px, #45b7af)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_newtheme6_SIZES,
		'disable_fonts'	 => 1,
	    ),
	    'std'		 => array('size'	 => '70', 'color'	 => '#45b7af'),
	),
	array(
	    'id'	 => 's1img',
	    'type'	 => 'upload',
	    'desc'	 => __('', 'nhp-opts'),
	    'title'	 => __('Image', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1left_button',
	    'type'	 => 'text',
	    'std'	 => 'Yes',
	    'title'	 => __('Left Button Text 1', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1left_button2',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Left Button Text 2', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1left_button_color',
	    'type'	 => 'color',
	    'std'	 => '#4ecdc4',
	    'desc'	 => __('(default: #ff6b6b)', 'nhp-opts'),
	    'title'	 => __('Left Submit Button Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1left_button_text_color',
	    'type'	 => 'color',
	    'std'	 => '#ffffff',
	    'desc'	 => __('(default: #ffffff)', 'nhp-opts'),
	    'title'	 => __('Left Submit Button Text Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1left_action',
	    'type'	 => 'select',
	    'options'	 => array(
		1   =>	'Go to Next Step',
		2   =>	'Redirect',
		3   =>	'Close',
	    ),
	    'std'	 => '',
	    'title'	 => __('Left Button Action', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1left_action_url',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Left Button Redirect Url', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1disable_right',
	    'type'	 => 'select',
	    'options'	 => array(
		1   =>	'No',
		2   =>	'Yes',
	    ),
	    'std'	 => '1',
	    'title'	 => __('Disable Right Button', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1between_text',
	    'type'	 => 'text',
	    'std'	 => 'OR',
	    'title'	 => __('Text Between Buttons', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1right_button',
	    'type'	 => 'text',
	    'std'	 => 'No',
	    'title'	 => __('Right Button Text 1', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1right_button2',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Right Button Text 2', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1right_button_color',
	    'type'	 => 'color',
	    'std'	 => '#ff6b6b',
	    'desc'	 => __('(default: #ff6b6b)', 'nhp-opts'),
	    'title'	 => __('Right Submit Button Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1right_button_text_color',
	    'type'	 => 'color',
	    'std'	 => '#ffffff',
	    'desc'	 => __('(default: #ffffff)', 'nhp-opts'),
	    'title'	 => __('Right Submit Button Text Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1right_action',
	    'type'	 => 'select',
	    'options'	 => array(
		1   =>	'Go to Next Step',
		2   =>	'Redirect',
		3   =>	'Close',
	    ),
	    'std'	 => '',
	    'title'	 => __('Right Button Action', 'nhp-opts'),
	),
	array(
	    'id'	 => 's1right_action_url',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Right Button Redirect Url', 'nhp-opts'),
	),
	array(
	    'id'	 => 'divide',
	    'type'	 => 'divide',
	    'title'	 => __('Step 2', 'nhp-opts'),
	),
	array(
	    'id'	 => 's2header',
	    'type'	 => 'text',
	    'title'	 => __('Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 's2header_font',
	    'type'	 => 'typo',
	    'title'	 => __('Header Font', 'nhp-opts'),
	    'desc'	 => __('px (default: 44px, #363636)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_newtheme6_SIZES,
		'disable_fonts'	 => 1,
		'disable_color'	 => 1,
	    ),
	    'std'		 => array('size'	 => '44', 'color'	 => '#363636'),
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
	    'std'	 => 'Your Email...',
	    'title'	 => __('E-mail Placeholder', 'nhp-opts'),
	),
	array(
	    'id'	 => 'cf',
	    'type'	 => 'custom_fields',
	    'std'	 => '',
	    'title'	 => __('Custom Fields', 'nhp-opts'),
	    'desc'	 => __('', 'nhp-opts'),
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
	    'std'	 => '#4ecdc4',
	    'desc'	 => __('(default: #4ecdc4)', 'nhp-opts'),
	    'title'	 => __('Submit Button Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 'submit_button_text_color',
	    'type'	 => 'color',
	    'std'	 => '#ffffff',
	    'desc'	 => __('(default: #ffffff)', 'nhp-opts'),
	    'title'	 => __('Submit Button Text Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 'divide',
	    'type'	 => 'divide',
	    'title'	 => __('Step 3', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3header',
	    'type'	 => 'text',
	    'title'	 => __('Header', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3header_font',
	    'type'	 => 'typo',
	    'title'	 => __('Header Font', 'nhp-opts'),
	    'desc'	 => __('px (default: 44px, #363636)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_newtheme6_SIZES,
		'disable_fonts'	 => 1,
		'disable_color'	 => 1,
	    ),
	    'std'		 => array('size'	 => '44', 'color'	 => '#363636'),
	),
	array(
	    'id'	 => 's3subheader',
	    'type'	 => 'text',
	    'title'	 => __('Subheader', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3subheader_font',
	    'type'	 => 'typo',
	    'title'	 => __('Subheader Font Size', 'nhp-opts'),
	    'desc'	 => __('px (default: 70px, #45b7af)', 'nhp-opts'),
	    'args'	 => array(
		'sizes'		 => $SNP_THEMES_newtheme6_SIZES,
		'disable_fonts'	 => 1,
	    ),
	    'std'		 => array('size'	 => '70', 'color'	 => '#45b7af'),
	),
	array(
	    'id'	 => 's3img',
	    'type'	 => 'upload',
	    'desc'	 => __('', 'nhp-opts'),
	    'title'	 => __('Image', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3button',
	    'type'	 => 'text',
	    'std'	 => 'Close',
	    'title'	 => __('Button Text', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3button_color',
	    'type'	 => 'color',
	    'std'	 => '#4ecdc4',
	    'desc'	 => __('(default: #ff6b6b)', 'nhp-opts'),
	    'title'	 => __('Button Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3button_text_color',
	    'type'	 => 'color',
	    'std'	 => '#ffffff',
	    'desc'	 => __('(default: #ffffff)', 'nhp-opts'),
	    'title'	 => __('Button Text Color', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3button_action',
	    'type'	 => 'select',
	    'options'	 => array(
		2   =>	'Redirect',
		3   =>	'Close',
	    ),
	    'std'	 => '',
	    'title'	 => __('Button Action', 'nhp-opts'),
	),
	array(
	    'id'	 => 's3button_action_url',
	    'type'	 => 'text',
	    'std'	 => '',
	    'title'	 => __('Button Redirect Url', 'nhp-opts'),
	),
    )
);
?>