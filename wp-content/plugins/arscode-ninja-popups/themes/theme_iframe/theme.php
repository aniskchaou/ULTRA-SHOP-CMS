<?php
$SNP_THEMES['theme_iframe'] = array(
	'NAME' => 'Iframe',
	'STYLES' => 'style.css',
	'TYPES' => array(
		'iframe' => array('NAME' => 'Iframe'),
	),
	'COLORS' => array(
		'iframe' => array('NAME' => '--')
	),
	'FIELDS' => array(
		array(
			'id' => 'width',
			'type' => 'text',
			'title' => __('Width', 'nhp-opts'),
			'desc' => __('px (default: 800)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '800'
		),
		array(
			'id' => 'height',
			'type' => 'text',
			'title' => __('Height', 'nhp-opts'),
			'desc' => __('px (default: 600)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '600'
		),
		array(
			'id' => 'iframe_url',
			'type' => 'text',
			'title' => __('Source Url', 'nhp-opts')
		),
	)
);
?>