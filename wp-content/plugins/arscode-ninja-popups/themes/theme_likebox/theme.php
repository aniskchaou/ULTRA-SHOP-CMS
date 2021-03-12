<?php
$FB_Locales=array();
$FB_Locales['af_ZA'] = 'Afrikaans';
$FB_Locales['ar_AR'] = 'Arabic';
$FB_Locales['az_AZ'] = 'Azeri';
$FB_Locales['be_BY'] = 'Belarusian';
$FB_Locales['bg_BG'] = 'Bulgarian';
$FB_Locales['bn_IN'] = 'Bengali';
$FB_Locales['bs_BA'] = 'Bosnian';
$FB_Locales['ca_ES'] = 'Catalan';
$FB_Locales['cs_CZ'] = 'Czech';
$FB_Locales['cy_GB'] = 'Welsh';
$FB_Locales['da_DK'] = 'Danish';
$FB_Locales['de_DE'] = 'German';
$FB_Locales['el_GR'] = 'Greek';
$FB_Locales['en_GB'] = 'English (UK)';
$FB_Locales['en_PI'] = 'English (Pirate)';
$FB_Locales['en_UD'] = 'English (Upside Down)';
$FB_Locales['en_US'] = 'English (US)';
$FB_Locales['eo_EO'] = 'Esperanto';
$FB_Locales['es_ES'] = 'Spanish (Spain)';
$FB_Locales['es_LA'] = 'Spanish';
$FB_Locales['et_EE'] = 'Estonian';
$FB_Locales['eu_ES'] = 'Basque';
$FB_Locales['fa_IR'] = 'Persian';
$FB_Locales['fb_LT'] = 'Leet Speak';
$FB_Locales['fi_FI'] = 'Finnish';
$FB_Locales['fo_FO'] = 'Faroese';
$FB_Locales['fr_CA'] = 'French (Canada)';
$FB_Locales['fr_FR'] = 'French (France)';
$FB_Locales['fy_NL'] = 'Frisian';
$FB_Locales['ga_IE'] = 'Irish';
$FB_Locales['gl_ES'] = 'Galician';
$FB_Locales['he_IL'] = 'Hebrew';
$FB_Locales['hi_IN'] = 'Hindi';
$FB_Locales['hr_HR'] = 'Croatian';
$FB_Locales['hu_HU'] = 'Hungarian';
$FB_Locales['hy_AM'] = 'Armenian';
$FB_Locales['id_ID'] = 'Indonesian';
$FB_Locales['is_IS'] = 'Icelandic';
$FB_Locales['it_IT'] = 'Italian';
$FB_Locales['ja_JP'] = 'Japanese';
$FB_Locales['ka_GE'] = 'Georgian';
$FB_Locales['ko_KR'] = 'Korean';
$FB_Locales['ku_TR'] = 'Kurdish';
$FB_Locales['la_VA'] = 'Latin';
$FB_Locales['lt_LT'] = 'Lithuanian';
$FB_Locales['lv_LV'] = 'Latvian';
$FB_Locales['mk_MK'] = 'Macedonian';
$FB_Locales['ml_IN'] = 'Malayalam';
$FB_Locales['ms_MY'] = 'Malay';
$FB_Locales['nb_NO'] = 'Norwegian (bokmal)';
$FB_Locales['ne_NP'] = 'Nepali';
$FB_Locales['nl_NL'] = 'Dutch';
$FB_Locales['nn_NO'] = 'Norwegian (nynorsk)';
$FB_Locales['pa_IN'] = 'Punjabi';
$FB_Locales['pl_PL'] = 'Polish';
$FB_Locales['ps_AF'] = 'Pashto';
$FB_Locales['pt_BR'] = 'Portuguese (Brazil)';
$FB_Locales['pt_PT'] = 'Portuguese (Portugal)';
$FB_Locales['ro_RO'] = 'Romanian';
$FB_Locales['ru_RU'] = 'Russian';
$FB_Locales['sk_SK'] = 'Slovak';
$FB_Locales['sl_SI'] = 'Slovenian';
$FB_Locales['sq_AL'] = 'Albanian';
$FB_Locales['sr_RS'] = 'Serbian';
$FB_Locales['sv_SE'] = 'Swedish';
$FB_Locales['sw_KE'] = 'Swahili';
$FB_Locales['ta_IN'] = 'Tamil';
$FB_Locales['te_IN'] = 'Telugu';
$FB_Locales['th_TH'] = 'Thai';
$FB_Locales['tl_PH'] = 'Filipino';
$FB_Locales['tr_TR'] = 'Turkish';
$FB_Locales['uk_UA'] = 'Ukrainian';
$FB_Locales['vi_VN'] = 'Vietnamese';
$FB_Locales['zh_CN'] = 'Simplified Chinese (China)';
$FB_Locales['zh_HK'] = 'Traditional Chinese (Hong Kong)';
$FB_Locales['zh_TW'] = 'Traditional Chinese (Taiwan)';
$SNP_THEMES['theme_likebox'] = array(
	'NAME' => 'Facebook Likebox',
	'STYLES' => 'style.css',
	'TYPES' => array(
		'likebox' => array('NAME' => 'Facebook Likebox'),
	),
	'COLORS' => array(
		'likebox' => array('NAME' => '--')
	),
	'FIELDS' => array(
		array(
			'id' => 'width',
			'type' => 'text',
			'title' => __('Width', 'nhp-opts'),
			'desc' => __('px (default: 415)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '415'
		),
		array(
			'id' => 'height',
			'type' => 'text',
			'title' => __('Height', 'nhp-opts'),
			'desc' => __('px (default: 345)', 'nhp-opts'),
			'class' => 'mini',
			'std' => '345'
		),
		array(
			'id' => 'facebook_url',
			'type' => 'text',
			'title' => __('Facebook Page URL', 'nhp-opts'),
			'desc' => __('', 'nhp-opts')
		),
		array(
			'id' => 'lb_show_faces',
			'type' => 'radio',
			'title' => __('LikeBox - Show Faces', 'nhp-opts'),
			'options' => array(0 => 'No',1 => 'Yes'),
			'std' => 1
		),
		array(
			'id' => 'lb_show_stream',
			'type' => 'radio',
			'title' => __('LikeBox - Show Stream', 'nhp-opts'),
			'options' => array(0 => 'No',1 => 'Yes'),
			'std' => 0
		),
		array(
			'id' => 'lb_small_header',
			'type' => 'radio',
			'title' => __('LikeBox - Use Small Header', 'nhp-opts'),
			'options' => array(0 => 'No',1 => 'Yes'),
			'std' => 0
		),
		array(
			'id' => 'lb_hidecoverfoto',
			'type' => 'radio',
			'title' => __('LikeBox - Hide Cover Photo', 'nhp-opts'),
			'options' => array(0 => 'No',1 => 'Yes'),
			'std' => 1
		),
		/*array(
			'id' => 'lb_locale',
			'type' => 'select',
			'title' => __('LikeBox - Language', 'nhp-opts'),
			'options' => $FB_Locales,
			'std' => 'en_US'
		),*/
		array(
			'id' => 'bg_gradient',
			'type' => 'color_gradient',
			'title' => __('Background Color - Gradient', 'nhp-opts'),
		),
	)
);
?>