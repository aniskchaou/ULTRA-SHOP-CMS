<?php

$SNP_THEMES_SIZES = array();
for ($i = 10; $i <= 72; $i++) {
    $SNP_THEMES_SIZES[$i] = $i . 'px';
}
$SNP_THEMES['newtheme8'] = array(
    'NAME' => 'New Theme 8',
    'STYLES' => 'css/newtheme8.css',
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
            'desc' => __('px (default: 800)', 'nhp-opts'),
            'class' => 'mini',
            'std' => '800'
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
            'id' => 'bg_color1',
            'type' => 'color',
            'std' => '#1ea2ed',
            'desc' => __('(default: #1ea2ed)', 'nhp-opts'),
            'title' => __('Background Color', 'nhp-opts'),
        ),
        array(
            'id' => 'border_color',
            'type' => 'color',
            'std' => '#cc9a02',
            'desc' => __('(default: #cc9a02)', 'nhp-opts'),
            'title' => __('Border Color', 'nhp-opts'),
        ),
        array(
            'id' => 'border_size',
            'type' => 'select',
            'std' => '0',
//		'args'	 => array(
            'options' => array(0 => '0px', 5 => '5px', 10 => '10px', 15 => '15px', 20 => '20px'),
//	    ),
            'desc' => __('(default: 0px)', 'nhp-opts'),
            'title' => __('Border Size', 'nhp-opts'),
        ),
        array(
            'id' => 'header',
            'type' => 'text',
            'title' => __('Header', 'nhp-opts'),
        ),
        array(
            'id' => 'header_font',
            'type' => 'typo',
            'title' => __('Header Font', 'nhp-opts'),
            'desc' => __('(default: 24px, #ffffff)', 'nhp-opts'),
            'args' => array(
                'sizes' => $SNP_THEMES_SIZES,
                'disable_fonts' => 1,
                'disable_color' => 1,
            ),
            'std' => array('size' => '24', 'color' => '#ffffff'),
        ),
        array(
            'id' => 'maintext',
            'type' => 'textarea',
            'title' => __('Text', 'nhp-opts'),
        ),
        array(
            'id' => 'maintext_font',
            'type' => 'typo',
            'title' => __('Text Font Size', 'nhp-opts'),
            'desc' => __('(default: 15px, #ffffff)', 'nhp-opts'),
            'args' => array(
                'sizes' => $SNP_THEMES_SIZES,
                'disable_fonts' => 1,
            ),
            'std' => array('size' => '15', 'color' => '#ffffff'),
        ),
        array(
            'id' => 'header_img',
            'type' => 'upload',
            'desc' => __('', 'nhp-opts'),
            'title' => __('Header image', 'nhp-opts'),
        ),
        array(
            'id' => 'header_img_position',
            'type' => 'radio',
            'title' => __('Header image position', 'nhp-opts'),
            'options' => array(0 => 'Above header', 1 => 'Under header'),
            'std' => 0
        ),
        array(
            'id' => 'img',
            'type' => 'upload',
            'desc' => __('', 'nhp-opts'),
            'title' => __('Image', 'nhp-opts'),
        ),
        array(
            'id' => 'name_placeholder',
            'type' => 'text',
            'std' => 'Your Name...',
            'title' => __('Name Placeholder', 'nhp-opts'),
        ),
        array(
            'id' => 'name_disable',
            'type' => 'radio',
            'title' => __('Disable Name Field', 'nhp-opts'),
            'options' => array(0 => 'No', 1 => 'Yes'),
            'std' => 0
        ),
        array(
            'id' => 'email_placeholder',
            'type' => 'text',
            'std' => 'Your Email...',
            'title' => __('E-mail Placeholder', 'nhp-opts'),
        ),
        array(
            'id' => 'cf',
            'type' => 'custom_fields',
            'std' => '',
            'title' => __('Custom Fields', 'nhp-opts'),
            'desc' => __('', 'nhp-opts'),
        ),
        array(
            'id' => 'fields_font',
            'type' => 'typo',
            'title' => __('Input Fields Font', 'nhp-opts'),
            'desc' => __('(default: 14px, #666666)', 'nhp-opts'),
            'args' => array(
                'sizes' => $SNP_THEMES_SIZES,
                'disable_fonts' => 1,
                'disable_color' => 1,
            ),
            'std' => array('size' => '14', 'color' => '#666666'),
        ),
        array(
            'id' => 'fields_border_color',
            'type' => 'color',
            'std' => '#0f7dbb',
            'desc' => __('(default: #0f7dbb)', 'nhp-opts'),
            'title' => __('Input Fields Border Color', 'nhp-opts'),
        ),
        array(
            'id' => 'submit_button',
            'type' => 'text',
            'std' => 'Subscribe Now!',
            'title' => __('Submit Button Text', 'nhp-opts'),
        ),
        array(
            'id' => 'submit_button_loading',
            'type' => 'text',
            'std' => '',
            'title' => __('Submit Button Loading Text', 'nhp-opts'),
            'desc' => __('(ex: Please wait...)', 'nhp-opts'),
        ),
        array(
            'id' => 'submit_button_success',
            'type' => 'text',
            'std' => '',
            'title' => __('Submit Button Success Text', 'nhp-opts'),
            'desc' => __('(ex: Thank You!)', 'nhp-opts'),
        ),
        array(
            'id' => 'submit_button_font',
            'type' => 'typo',
            'title' => __('Submit Button Font', 'nhp-opts'),
            'desc' => __('(default: 15px, #0969a0)', 'nhp-opts'),
            'args' => array(
                'sizes' => $SNP_THEMES_SIZES,
                'disable_fonts' => 1,
                'disable_color' => 1,
            ),
            'std' => array('size' => '15', 'color' => '#0969a0'),
        ),
        array(
            'id' => 'submit_button_color',
            'type' => 'color',
            'std' => '#ffd303',
            'desc' => __('(default: #ffd303)', 'nhp-opts'),
            'title' => __('Submit Button Background Color', 'nhp-opts'),
        ),
        array(
            'id' => 'submit_button_padding',
            'type' => 'select',
            'std' => '12',
//		'args'	 => array(
            'options' => array(0 => '0px', 6 => '6px', 12 => '12px', 18 => '18px', 24 => '24px'),
//	    ),
            'desc' => __('(default: 12px)', 'nhp-opts'),
            'title' => __('Submit Button Top/Bottom Padding', 'nhp-opts'),
        ),
        array(
            'id' => 'security_note',
            'type' => 'text',
            'title' => __('Security Note', 'nhp-opts'),
            'std' => 'This information will never be shared for third part',
        ),
        array(
            'id' => 'security_note_font',
            'type' => 'typo',
            'title' => __('Security Note Font', 'nhp-opts'),
            'desc' => __('(default: 11px, #ffffff)', 'nhp-opts'),
            'args' => array(
                'sizes' => $SNP_THEMES_SIZES,
                'disable_fonts' => 1,
                'disable_color' => 1,
            ),
            'std' => array('size' => '11', 'color' => '#ffffff'),
        ),
        array(
            'id' => 'lock_img',
            'type' => 'upload',
            'desc' => __('(max. dimensions: 18x18)', 'nhp-opts'),
            'title' => __('Security Note Icon Image', 'nhp-opts'),
        ),
    )
);
