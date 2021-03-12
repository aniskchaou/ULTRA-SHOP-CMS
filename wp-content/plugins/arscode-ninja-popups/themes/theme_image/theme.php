<?php
$SNP_THEMES['theme_image'] = array(
	'NAME' => 'Image',
	'STYLES' => 'style.css',
	'TYPES' => array(		
		'optin' => array('NAME' => 'Opt-in'),
		'social' => array('NAME' => 'Social'),
		'iframe' => array('NAME' => 'Only Image'),
	),
	'COLORS' => array(
		'image' => array('NAME' => '--')
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
			'id' => 'image',
			'type' => 'upload',
			'title' => __('Image', 'nhp-opts')
		),
                array(
			'id' => 'img_url',
			'type' => 'text',
			'title' => __('Image link URL', 'nhp-opts'),
			'desc' => __('', 'nhp-opts'),
			'class' => 'regular-text',
		),
                array(
                    'id'		 => 'image_target',
                    'type'		 => 'radio',
                    'title'		 => __('Where to open image link?', 'nhp-opts'),
                    'options'	 => array(1	 => 'Blank Page', 0	 => 'Current Page'),
                    'std'	 => 0
                ),
		array(
			'id' => 'bg_gradient',
			'type' => 'color_gradient',
			'title' => __('Background Color - Gradient', 'nhp-opts'),
		),
		array(
			'id' => 'email_placeholder',
			'type' => 'text',
			'std' => 'enter your e-mail...',
			'title' => __('E-mail Placeholder', 'nhp-opts'),
		),
		array(
			'id' => 'email_width',
			'type' => 'text',
			'std' => '360',
			'class' => 'mini',
			'title' => __('E-mail Field Width', 'nhp-opts'),
			'desc' => __('px (default: 360)', 'nhp-opts'),
		),
		array(
			'id' => 'submit_button',
			'type' => 'text',
			'std' => 'Subscribe Now!',
			'title' => __('Submit Button', 'nhp-opts'),
		),
		array(
			'id' => 'submit_button_color',
			'type' => 'color_gradient',
			'std' => array('from' => '#345c9f', 'to' => '#24406f'),
			'desc' => __('(leave empty for default: #345c9f, #24406f)', 'nhp-opts'),
			'title' => __('Submit Button Background - Gradient', 'nhp-opts'),
		),
		array(
			'id' => 'submit_button_text_color',
			'type' => 'color',
			'desc' => __('(leave empty for default: #fff)', 'nhp-opts'),
			'title' => __('Submit Button Text Color', 'nhp-opts'),
		),
		array(
			'id' => 'security_note',
			'type' => 'text',
			'title' => __('Security Note', 'nhp-opts'),
		),
		array(
			'id' => 'closetext_color',
			'type' => 'color',
			'title' => __('Close Text Color', 'nhp-opts')
		),
	)
);
?>