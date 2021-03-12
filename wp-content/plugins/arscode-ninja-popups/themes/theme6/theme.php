<?php
$SNP_THEMES_theme6_SIZES = array();
for($i=10;$i<=72;$i++)
{
	$SNP_THEMES_theme6_SIZES[$i] = $i.'px';
}
$SNP_THEMES['theme6'] = array(
	'NAME' => 'Theme 6',
	'STYLES' => 'css/theme6.css',
	'TYPES' => array(
		'optin' => array('NAME' => 'Opt-in')
	),
	'COLORS' => array(
		'multicolors' => array('NAME' => 'Multicolors')
	),
	'FIELDS' => array(
		array(
			'id' => 'width',
			'type' => 'text',
			'title' => __('Width', 'nhp-opts'),
			'desc' => __('px (default: 700)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '700'
		),
		array(
			'id' => 'height',
			'type' => 'text',
			'title' => __('Height', 'nhp-opts'),
			'desc' => __('px (optional, leave empty for auto-height)', 'nhp-opts'),
			'class' => 'mini',
			'std' => ''
		),
		array(
			'id' => 'bg_color',
			'type' => 'color',
			'title' => __('Background Color', 'nhp-opts'),
			'std' => '#f2f2f2'
		),
		array(
			'id' => 'text_color',
			'type' => 'color',
			'title' => __('Text Color', 'nhp-opts'),
			'std' => '#a0a4a9',
		),
		array(
			'id' => 'input_text_color',
			'type' => 'color',
			'title' => __('Input Text Color', 'nhp-opts'),
			'std' => '#000000',
		),
		array(
			'id' => 'submit_color',
			'type' => 'color',
			'title' => __('Submit Button Color', 'nhp-opts'),
			'std' => '#0095ca'
		),
		array(
			'id' => 'header',
			'type' => 'textarea',
			'title' => __('Header Text', 'nhp-opts'),
		),
		array(
			'id' => 'header_size',
			'type' => 'typo',
			'title' => __('Header Text Font Size', 'nhp-opts'),
			'desc' => __('px (default: 15px)', 'nhp-opts'),
			'args' => array(
				'sizes' => $SNP_THEMES_theme6_SIZES,
				'disable_fonts' => 1,
				'disable_colors' => 1,
			),
			'std' => array('size' => '15'),
		),
		array(
			'id' => 'email_placeholder',
			'type' => 'text',
			'std' => 'Your E-mail...',
			'title' => __('E-mail Placeholder', 'nhp-opts'),
		),
	)
);
?>