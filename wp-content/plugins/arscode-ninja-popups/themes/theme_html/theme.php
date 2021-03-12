<?php

$SNP_THEMES['theme_html'] = array(
	'NAME' => 'HTML',
	'STYLES' => 'style.css',
	'TYPES' => array(
		'html' => array('NAME' => 'HTML'),
	),
	'COLORS' => array(
		'html' => array('NAME' => '--')
	),
	'FIELDS' => array(
		array(
			'id' => 'width',
			'type' => 'text',
			'title' => __('Width', 'nhp-opts'),
			'desc' => __('px (default: 450)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '450'
		),
		array(
			'id' => 'height',
			'type' => 'text',
			'title' => __('Height', 'nhp-opts'),
			'desc' => __('px (default: 300)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '300'
		),
		array(
			'id' => 'bg_gradient',
			'type' => 'color_gradient',
			'title' => __('Background Color - Gradient', 'nhp-opts'),
		),
	)
);
?>