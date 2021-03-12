<?php
$Popups = snp_get_popups();
$ABTesting = snp_get_ab();
$Popups=(array)$Popups + (array)$ABTesting;	
$Popups['global'] = 'Use global settings';
$Popups['disabled'] = 'Disabled';

if ($mode == 'edit') {
    echo '<tr class="form-field">';
    echo '<th scope="row" valign="top"><label>' . __('Ninja Popups - Welcome', 'nhp-opts') . '</label></th>';
    echo '<td>';
    echo '<select name="snp_term_meta[welcome]">';
    foreach($Popups as $k => $v) {
        echo '<option ' . ((!isset($snp_term_meta['welcome']) && $k == 'global') || $snp_term_meta['welcome'] == $k ? 'selected' : '') . ' value="' . $k . '">' . $v . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    echo '<tr class="form-field">';
    echo '<th scope="row" valign="top"><label>' . __('Ninja Popups - Mobile Welcome', 'nhp-opts') . '</label></th>';
    echo '<td>';
    echo '<select name="snp_term_meta[mobile_welcome]">';
    foreach($Popups as $k => $v) {
        echo '<option ' . ((!isset($snp_term_meta['mobile_welcome']) && $k == 'global') || $snp_term_meta['mobile_welcome'] == $k ? 'selected' : '') . ' value="' . $k . '">' . $v . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    echo '<tr class="form-field">';
    echo '<th scope="row" valign="top"><label>' . __('Ninja Popups - Exit', 'nhp-opts') . '</label></th>';
    echo '<td>';
    echo '<select name="snp_term_meta[exit]">';
    foreach($Popups as $k => $v) {
        echo '<option '.((!isset($snp_term_meta['exit']) && $k=='global') || $snp_term_meta['exit']==$k ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    echo '<tr class="form-field">';
    echo '<th scope="row" valign="top"><label>' . __('Ninja Popups - Mobile Exit', 'nhp-opts') . '</label></th>';
    echo '<td>';
    echo '<select name="snp_term_meta[mobile_exit]">';
    foreach($Popups as $k => $v) {
        echo '<option ' . ((!isset($snp_term_meta['mobile_exit']) && $k == 'global') || $snp_term_meta['mobile_exit'] == $k ? 'selected' : '') . ' value="' . $k . '">' . $v . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';
} else {
    echo '<div class="form-field">';
    echo '<label>' . __('Ninja Popups - Welcome') . '</label>';
    echo '<select name="snp_term_meta[welcome]">';
    foreach($Popups as $k => $v)  {
        echo '<option '.((!isset($snp_term_meta['welcome']) && $k=='global') || $snp_term_meta['welcome']==$k ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="form-field">';
    echo '<label>' . __('Ninja Popups - Mobile Welcome') . '</label>';
    echo '<select name="snp_term_meta[mobile_welcome]">';
    foreach($Popups as $k => $v)  {
        echo '<option '.((!isset($snp_term_meta['mobile_welcome']) && $k=='global') || $snp_term_meta['mobile_welcome']==$k ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="form-field">';
	echo '<label>' . __('Ninja Popups - Exit', 'nhp-opts') . '</label>';
    echo '<select name="snp_term_meta[exit]">';
    foreach($Popups as $k => $v) {
        echo '<option ' . ((!isset($snp_term_meta['exit']) && $k == 'global') || $snp_term_meta['exit'] == $k ? 'selected' : '') . ' value="' . $k . '">' . $v . '</option>';
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="form-field">';
    echo '<label>' . __('Ninja Popups - Mobile Exit', 'nhp-opts') . '</label>';
    echo '<select name="snp_term_meta[mobile_exit]">';
    foreach($Popups as $k => $v) {
        echo '<option ' . ((!isset($snp_term_meta['mobile_exit']) && $k == 'global') || $snp_term_meta['mobile_exit'] == $k ? 'selected' : '') . ' value="' . $k . '">' . $v . '</option>';
    }
    echo '</select>';
    echo '</div>';
}