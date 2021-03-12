<?php

if (!defined('ABSPATH'))
{
	die('-1');
}

class snp_links
{
    public static function is_local_url($url)
    {
        $home_url = strtolower(home_url());
        $url = strtolower($url);
        $home_url = (strpos($home_url, 'https://') === 0) ? ('http' . substr($home_url, 5)) : $home_url;
        $url = (strpos($url, 'https://') === 0) ? ('http' . substr($url, 5)) : $url;
        if (!($return = (strpos($url, $home_url) === 0))) {
            $scheme = substr($url, 0, strpos($url, ':'));
            $return = !$scheme || ($scheme && !preg_match('/^[a-z0-9.]{2,16}$/iu', $scheme));
        }
        return $return;
    }

    public static function is_image_url($url)
    {
        $images_ext = array('bmp', 'gif', 'jpg', 'png');
        $explodedUrl = explode('.', $url);

        if (in_array(end($explodedUrl), $images_ext)) {
            return true;
        } else {
            return false;
        }
    }

    public static function search_callback($match)
    {
        if (!trim($match[2])) {
            return $match[0];
        }

        $quot = '"';
        if (strpos($match[1], '"') !== FALSE) {
            $quot = '"';
        } elseif (strpos($match[1], '\'') !== FALSE) {
            $quot = '\'';
        }
        $match['attr'] = shortcode_parse_atts($match[1]);
        
        // has href?
        if (!$match['attr']['href']) {
            return $match[0];
        }
        
        // is local?
        if (snp_links::is_local_url($match['attr']['href'])) {
            return $match[0];
        }

        // is excluded?
        if (empty($match['attr']['class'])) {
            $match['attr']['class'] = '';
        }

        if (strpos($match['attr']['class'], snp_get_option('class_no_popup', 'nosnppopup')) !== FALSE) {
            return $match[0];
        }

        // is graphic?
        if (snp_links::is_image_url($match['attr']['href'])) {
            return $match[0];
        }

        $attrs = '';
        if (!$match['attr']['class']) {
            $match['attr']['class'] = '';
        }

        foreach ($match['attr'] as $k => $v) {
            if ($k == 'class') {
                $v .= ' ' . snp_get_option('class_popup', 'snppopup');
            }
            $attrs .= $k . '=' . $quot . trim($v) . $quot . '';
        }

        return '<a ' . $attrs . '>' . $match[2] . '</a>';
    }

    public static function search($txt)
    {
        if (is_feed()) {
            return $txt;
        }

        $txt = preg_replace_callback("/
			<\s*a\s+
			([^<>]+)
			>
			(.*?)
			<\s*\/\s*a\s*>
			/isx", array('snp_links', 'search_callback'), $txt);
        
        return $txt;
    }
}