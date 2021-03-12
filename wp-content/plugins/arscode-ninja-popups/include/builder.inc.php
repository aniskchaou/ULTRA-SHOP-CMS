<?php
function snp_builder_create_element($type, $step_index, $el_index, $args)
{
    global $POPUP_META;

    $return = '';
    $cont_css_class = '';
    $cont_data = '';
    
    if (isset($args['animation']) && !empty($args['animation'])) {
        $cont_css_class.=' animated '.$args['animation'];
        $cont_data .= ' data-animation="'.$args['animation'].'"';
    }

    if (in_array($type, array('text','input','textarea','select'))) {
        if(isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $cont_css_class.=' bld-icon'; 
        }
    }

    $return .= '<div class="bld-el-cont bld-el-'.$type.' bld-step-'.$step_index.'-el-'.$el_index.' '.$cont_css_class.'"'.$cont_data.'>';
    $css_class = '';
    $data = '';
    $name = '';
    $inputName = '';

    if (isset($args['css-class'])) {
        $css_class.=' '.$args['css-class'];
    }
    
    if (isset($args['placeholder']) && $args['placeholder']!='') {
        $data .= ' placeholder="'.$args['placeholder'].'"';
    }

    if (isset($args['required']) && $args['required']==1) {
        $data .= ' required';
    }


    if (isset($args['mailchimp_group']) && !empty($args['mailchimp_group'])) {
        if (isset($args['name-type']) && $args['name-type']=='') {
            $name .= $args['name'];
        }

        if (isset($args['name-type']) && $args['name-type']!='') {
            $name .= $args['name-type'];
        }

        if ($type == 'checkbox') {
            $data .= ' name="mcgroups['.$name.'][]"';
        } else {
            $data .= ' name="mcgroups['.$name.']"';
        }
    } else {
        if (isset($args['name-type']) && $args['name-type']=='') {
            $data .= ' name="'.$args['name'].'"';
            $inputName = $args['name'];
        }

        if (isset($args['name-type']) && $args['name-type']!='') {
            $data .= ' name="'.$args['name-type'].'"';
            $inputName = $args['name-type'];
        }
    }


    if (isset($args['action']) && $args['action']!='') {
        if ($args['action']=='gotostep') {
            $css_class.=' snp-bld-gotostep snp-cursor-pointer';
            $data .= ' data-step="'.$args['action-step'].'"';
        } else if ($args['action']=='close') {
            $css_class.=' snp-close-link snp-cursor-pointer';
        } else if ($args['action']=='submit' || $args['action']=='submit-step') {
            $css_class.=' snp-submit snp-cursor-pointer';
            
            if ($args['action']=='submit-step') {
                $data .= ' data-step="'.$args['action-step-submit'].'"';
            }
        } else if($args['action']=='link') {
            $css_class.=' snp-cursor-pointer ';

            $data .= ' data-redirect="' . $args['action-link'] . '"';

            if (isset($args['click_as_conversion']) && $args['click_as_conversion'] == 'yes') {
                $data .= ' data-set-cookie="yes"';
            } else {
                if (isset($args['set_cookie']) && $args['set_cookie'] == 'yes') {
                    $data .= ' data-set-cookie="yes"';
                } else {
                    $data .= ' data-set-cookie="no"';
                }
            }

            $data .= ' data-url="' . $args['action-link'] . '"';

            if (isset($args['open_type']) && $args['open_type'] == 'blank') {
                $data .= ' onclick="snp_open_link(this, true); jQuery.event.trigger({type: \'ninja_popups_button_' . $inputName . '\'});"';
            } else {
                $data .= ' onclick="snp_open_link(this, false); jQuery.event.trigger({type: \'ninja_popups_button_' . $inputName . '\'});"';
            }
        } else if ($args['action']=='select_link') {
	        $css_class.=' snp-cursor-pointer';

            if ($args['set_cookie'] == 'yes') {
                $data .= ' data-set-cookie="yes"';
            } else {
                $data .= ' data-set-cookie="no"';
            }

	        if ($args['select_link_open_type'] == 'blank') {
		        $data .= ' onclick="snp_open_select_link(this, true); jQuery.event.trigger({type: \'ninja_popups_button_' . $inputName . '\'});"';
	        } else {
		        $data .= ' onclick="snp_open_select_link(this, false); jQuery.event.trigger({type: \'ninja_popups_button_' . $inputName . '\'});"';
	        }
        }

        if (isset($args['loading-text']) && $args['loading-text']) {
            $data .= ' data-loading="'.  htmlspecialchars($args['loading-text']).'"';
        }

        $jsonArray = array();
	    foreach((array)$args['select_link_options'] as $link) {
	    	$jsonArray[$link['name']] = $link['url'];
	    }

	    if (!empty($jsonArray)) {
	    	$data .= ' data-redirect-rules=\''.json_encode($jsonArray).'\'';
	    }
    }

    if ($type == 'text') {
        $return .=  '<div class="bld-el '.$css_class.'" '.$data.'>'.(isset($args['content']) ? do_shortcode($args['content']) : '').'</div>';
    } else if ($type=='pointlist') {
        $return .= '<ul class="bld-el '.$css_class.'" '.$data.'>';
        foreach((array)$args['options'] as $point) {
            $return .= '<li>'.$point.'</li>';
        }
        $return .= '</ul>';
    } else if ($type == 'img') {
        $return .=  '<img class="bld-el '.$css_class.'" '.$data.' src="'.(isset($args['img']) && $args['img'] != '' ? $args['img'] : SNP_URL . '/admin/img/img-placeholder.png').'" alt="'.$args['alt'].'" />';
    } else if ($type == 'video') {
        $return .= '<iframe class="snp-bld-video" width="100%" height="100%" src="https://www.youtube.com/embed/'. trim($args['video-url'], ' /').'?controls='. $args['video-controls'].'&amp;showinfo='. $args['video-title'].'&amp;rel='. $args['video-recommended'].'" data-src="https://www.youtube.com/embed/'. trim($args['video-url'], ' /').'?controls='. $args['video-controls'].'&amp;showinfo='. $args['video-title'].'&amp;rel='. $args['video-recommended'].'" data-autoplay="&amp;autoplay='. $args['video-autoplay'] .'" frameborder="0" allowfullscreen></iframe>';        
    } else if ($type == 'map') {
		wp_enqueue_script('snp-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.(isset($args['map-key']) ? $args['map-key'] : '').'&sensor=false');
        $return .= '<div class="snp-bld-googleMap" data-coordx="'.(!empty($args['map-coordx'])?$args['map-coordx']:'40.758737').'" data-coordy="'.( !empty($args['map-coordy'])?$args['map-coordy']:'-73.985195' ).'" data-zoom="'.(!empty($args['map-zoom'])?$args['map-zoom']:'5').'" data-icon="'.(!empty($args['map-icon'])?$args['map-icon']:'').'" data-mapType="'.$args['map-type'].'"></div>';
    } else if ($type == 'captcha') {
        if (!snp_get_option('js_disable_recaptcha')) {
            wp_enqueue_script('js-recaptcha-api', 'https://www.google.com/recaptcha/api.js?render=' . snp_get_option('recaptcha_api_key'));
        }

        $return .= '<div class="bld-el '.$css_class.'" '.$data.'>';
        $return .= '<input type="hidden" name="ninja_popup_recaptcha_response" class="ninja-popup-recaptcha3">';
        $return .= '</div>';
    } else if ($type == 'button') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="'.$args['tabindex'].'"';
        }

        if (isset($args['action']) && $args['action'] != '') {
            if($args['action']=='submit' || $args['action']=='submit-step') {
                $data .= ' type="submit"';
            } else {
                $data .= ' type="button"';
            }
        }
        
        if (isset($args['value']) && !empty($args['value'])) { 
        	$data .= ' value="' . $args['value'] . '"';
        }

        if ($inputName) {
            $data .= ' onclick="jQuery.event.trigger({type: \'ninja_popups_button_' . $inputName . '\'});"';
        }
        
        $return .=  '<button class="bld-el '.$css_class.'" '.$data.''.$tabindex.'>'.(isset($args['text']) ? do_shortcode($args['text']) : '').'</button>';
    } else if ($type == 'input') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="' . $args['tabindex'] . '"';
        }

        if (isset($args['icon']) && $args['icon'] != 'disabled' && $args['icon'] != '') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-' . $args['icon'] . '"></i></span>';
        }

        $return .= '<div class="bld-table-cont">';
        $return .= '<input  ' . $data . '  ' . (isset($args['text']) ? 'value="' . $args['text'] . '"' : '') . '  class="bld-el ' . $css_class . '"' . $tabindex . ' />';
        $return .= '</div>';
    } else if ($type == 'file') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="' . $args['tabindex'] . '"';
        }

        if (isset($args['icon']) && $args['icon'] != 'disabled' && $args['icon'] != '') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-' . $args['icon'] . '"></i></span>';
        }

        $return .= '<div class="bld-table-cont">';
        $return .= '<input type="file" ' . $data . ' class="bld-el ' . $css_class . '"' . $tabindex . ' />';
        $return .= '</div>';
    } else if ($type == 'radio') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="'.$args['tabindex'].'"';
        }

        if (isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-'.$args['icon'].'"></i></span>';
        }

        $return .= '<div class="bld-table-cont">';

        if (isset($args['mailchimp_group']) && !empty($args['mailchimp_group'])) {
            require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';

            $ml_mc_list = '';
            if (isset($POPUP_META['snp_ml_mc_list'][0])) {
                $ml_mc_list = $POPUP_META['snp_ml_mc_list'][0];
            }

            if (!$ml_mc_list) {
                $ml_mc_list = snp_get_option('ml_mc_list');
            }

            $rest = new MC_Lists(snp_get_option('ml_mc_apikey'));
            $fields = json_decode($rest->getGroupFields($ml_mc_list , $args['mailchimp_group']));

            foreach ($fields->interests as $v) {
                $return .= '<label>';
                $return .= '<input type="radio" '.$data.' value="' . $v->id . '" class="bld-el '.$css_class.'"'.$tabindex.' />';
                $return .= '<span>' . $v->name . '</span>';
                $return .= '</label>';
                $return .= '<span class="snp_clearfix"></span>';
            }
        } else {
            $return .= '<label>';
            $return .= '<input type="radio" '.$data.'  '.(isset($args['value']) ? 'value="'.$args['value'].'"' : '').'  class="bld-el '.$css_class.'"'.$tabindex.' />';
            $return .= '<span>' . $args['label'] . '</span>';
            $return .= '</label>';
        }
        $return .= '</div>';
        $return .= '<div class="snp_clearfix"></div>';
    } else if ($type == 'checkbox') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="'.$args['tabindex'].'"';
        }

        if (isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-'.$args['icon'].'"></i></span>';
        }

        $checked = '';
        if (isset($args['checked_default']) && $args['checked_default'] == 1) {
            $checked = ' checked="checked"';
        }

        $return .= '<div class="bld-table-cont">';

        if (isset($args['mailchimp_group']) && !empty($args['mailchimp_group'])) {
            require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';

            $ml_mc_list = '';
            if (isset($POPUP_META['snp_ml_mc_list'][0])) {
                $ml_mc_list = $POPUP_META['snp_ml_mc_list'][0];
            }

            if (!$ml_mc_list) {
                $ml_mc_list = snp_get_option('ml_mc_list');
            }

            $rest = new MC_Lists(snp_get_option('ml_mc_apikey'));
            $fields = json_decode($rest->getGroupFields($ml_mc_list , $args['mailchimp_group']));

            
            foreach ($fields->interests as $v) {
                $return .= '<label>';
                $return .= '<input type="checkbox" '.$data.' value="' . $v->id . '" class="bld-el '.$css_class.'"'.$tabindex.''.$checked.' />';
                $return .= '<span>' . $v->name . '</span>';
                $return .= '</label>';
                $return .= '<span class="snp_clearfix"></span>';
            }
        } else {
            $return .= '<label>';
            $return .= '<input type="checkbox" '.$data.'  '.(isset($args['value']) ? 'value="'.$args['value'].'"' : '').'  class="bld-el '.$css_class.'"'.$tabindex.''.$checked.' />';
            $return .= '<span>' . $args['label'] . '</span>';
            $return .= '</label>';
        }
        $return .= '</div>';
        $return .= '<div class="snp_clearfix"></div>';
    } else if ($type == 'calendar') {        
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="'.$args['tabindex'].'"';
        }

        if (isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-'.$args['icon'].'"></i></span>';
        }

        $return .= '<div class="bld-table-cont">';
        $return .= '<input  type="text" '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').'  class="bld-el snp-calendar-input '.$css_class.'"'.$tabindex.' />';
        $return .= '</div>';
    } else if ($type == 'hidden') {
        $return .= '<div class="bld-table-cont">';
        $return .= '<input type="hidden" '.$data.' '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' class="bld-el '.$css_class.'" />';
        $return .= '</div>';
    } else if ($type == 'textarea') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="'.$args['tabindex'].'"';
        }

        if (isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-'.$args['icon'].'"></i></span>';
        }

        $return .= '<div class="bld-table-cont">';
        $return .= '<textarea '.$data.'  class="bld-el '.$css_class.'"'.$tabindex.'>'.(isset($args['text']) ? $args['text'] : '').'</textarea>';
        $return .= '</div>';
    } else if ($type == 'select') {
        $tabindex = '';
        if (isset($args['tabindex']) && !empty($args['tabindex'])) {
            $tabindex = ' tabindex="'.$args['tabindex'].'"';
        }

        if(isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $return .= '<span class="bld-input-icon"><i class="fa fa-'.$args['icon'].'"></i></span>';
        }

        $return .= '<div class="bld-table-cont">';
        $return .= '<select '.$data.' class="snp-el-bld-select bld-el '.$css_class.'"'.$tabindex.'>';
        if ($args['placeholder']) {
            $return .= '<option value="" disabled selected>'.$args['placeholder'].'</option>';
        }

        if (isset($args['mailchimp_group']) && !empty($args['mailchimp_group'])) {
            require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';

            $ml_mc_list = '';
            if (isset($POPUP_META['snp_ml_mc_list'][0])) {
                $ml_mc_list = $POPUP_META['snp_ml_mc_list'][0];
            }

            if (!$ml_mc_list) {
                $ml_mc_list = snp_get_option('ml_mc_list');
            }

            $rest = new MC_Lists(snp_get_option('ml_mc_apikey'));
            $fields = json_decode($rest->getGroupFields($ml_mc_list , $args['mailchimp_group']));

            
            foreach ($fields->interests as $v) {
                $return .= '<option value="' . $v->id . '">' . $v->name . '</option>';
            }
        } else {
            foreach((array)$args['options'] as $option) {
                $val = explode('|||', $option);

                if (count($val) >= 2) {
                    $return .= '<option value="' . $val[0] . '">' . $val[1] . '</option>';
                } else {
                    $return .= '<option value="' . $option . '">' . $option . '</option>';
                }
            }
        }

        $return .= '</select>';
        $return .= '</div>';
    } else if ($type == 'box') {
        $return .=  '<div '.$data.'  class="bld-el '.$css_class.'"></div>';
    } else if ($type == 'hr') {
        $return .=  '<hr '.$data.'  class="bld-el '.$css_class.'"/>';
    } else if ($type == 'html') {
    	$return .= '<div class="bld-el '.$css_class.'" '.$data.'>'.(isset($args['custom_html']) ? do_shortcode($args['custom_html']) : '').'</div>';
    }

    $return .= '</div>';

    return $return;
}

function snp_builder_create_element_css($type, $ID, $step_index, $el_index, $args)
{
    $css = '';
    $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' {   border: 1px solid transparent;';
    $css.= 'width: '.$args['width'].'px;';
    $css.= 'height: '.$args['height'].'px;';
    $css.= 'top: '.$args['top'].'px;';
    $css.= 'left: '.$args['left'].'px;';
    
    if ($args['z-index']) {
        $css.='z-index: '.$args['z-index'].';';
    }

    if (isset($args['animation-delay']) && $args['animation-delay']!='') {
         $css.='-webkit-animation-delay: '.$args['animation-delay'].'ms;';
         $css.='animation-delay: '.$args['animation-delay'].'ms;';
    }
    $css.= '}';
    
    $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el,';
    $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el p,';
    $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el:focus,';
    $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el:active,';
    $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el:hover';
    $css.= '{';
    $css.= 'outline: 0;';
    
    if (in_array($type, array('input','textarea','select'))) {
        $css.= 'height: '.($args['height']-2).'px;';
    }

    if (isset($args['rotate']) && intval($args['rotate']) && $args['rotate']!=0) {
        $css.='transform:rotate('.$args['rotate'].'deg); -webkit-transform:rotate('.$args['rotate'].'deg); -moz-transform:rotate('.$args['rotate'].'deg); -o-transform:rotate('.$args['rotate'].'deg);;';
    }

    if (isset($args['color']) && $args['color']!='') {
        $css.='color: '.$args['color'].';';
    }

    if (isset($args['font'])) {
        if (snp_is_google_font($args['font'])) {
            $css .= 'font-family: \'' . $args['font'] . '\';';
        } else {
            $css .= 'font-family: ' . $args['font'] . ';';
        }
    }

    if (isset($args['font-size'])) {
        $css.='font-size: '.$args['font-size'].'px;';
    }

    if (isset($args['bold']) && $args['bold']==1) {
        $css.='font-weight: bold;';
    }

    if (isset($args['italic']) && $args['italic']==1) {
        $css.='font-style: italic;';
    }

    if (isset($args['underline']) && $args['underline']==1) {
        $css.='text-decoration: underline;';
    }

    if (!empty($args['border-style'])) {
        $css.='border-style: '.$args['border-style'].';';
    }

    if (isset($args['border-width'])) {
        $css.='border-width: '.(int)$args['border-width'].'px;';
    }

    if (!empty($args['border-color'])) {
        $css.='border-color: '.$args['border-color'].';';
    }

    if (isset($args['border-radius']) && $args['border-radius']!='') {
        $css.='border-radius: '.$args['border-radius'].'px;';
    }

    if (isset($args['padding']) && $args['padding']!='') {
        $css.='padding: '.$args['padding'].'px;';
    }

    if (!empty($args['background-color'])) {
        $css.='background-color: '.$args['background-color'].' !important;';
    }

    if (!empty($args['background-image'])) {
        $css.='background-image: url(\' '.$args['background-image'].'\');';
    }

    if (!empty($args['background-position'])) {
        $css.='background-position: '.$args['background-position'].';';
    }

    if (!empty($args['background-repeat'])) {
        $css.='background-repeat: '.$args['background-repeat'].';';
    }

    if (isset($args['opacity']) && $args['opacity']!='') {
        $css.='opacity: '.$args['opacity'].';';
    }

    if (isset($args['custom-css'])) {
        $css.=$args['custom-css'];
    }
    
    $css.= '}';

    if (in_array($type, array('pointlist'))) {
         $css.='.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' ul.bld-el li {';
         $css.='line-height: '.($args['lineheight'] ? $args['lineheight'].'px' : 'normal').';';
         $css.='padding-left: '.((int)$args['pointimg-padding'] ? $args['pointimg-padding'] : 0).'px;';
         $css.='background-image: url("'.$args['pointimg'].'");';
         $css.='}';
    }

    if (in_array($type, array('text','input','calendar','radio','checkbox','textarea','select'))) {
        if (in_array($type, array('radio', 'checkbox'))) {
            $css .= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' label {display: block; float: left; padding-right: 10px; white-space: nowrap;}';
            $css .= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' input {vertical-align: middle;  width: 13px; height: 13px; padding: 0; margin:0 10px 0 0;}';
            $css .= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' label span {vertical-align: middle;}';
        }

        if (isset($args['placeholder-color'])) {        
            $css.='.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el::-webkit-input-placeholder { color: '.$args['placeholder-color'].'; }';
            $css.='.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.' .bld-el::-moz-placeholder { color: '.$args['placeholder-color'].'; }';
        }

        if (isset($args['icon']) && $args['icon']!='disabled' && $args['icon']!='') {
            $css.= '.snp-pop-'.$ID.' .bld-step-'.$step_index.'-el-'.$el_index.'.bld-icon .bld-input-icon';
            $css.= '{';
            if (!isset($args['icon-right-border']) || !$args['icon-right-border']) {
                $css.='border-right-width: 0 !important;';
            }

            if (isset($args['icon-field-width'])) {
                $css.='width: '.$args['icon-field-width'].'px;';
            }

            if (isset($args['icon-size'])) {
                $css.='font-size: '.$args['icon-size'].'px;';
            }

            if (!empty($args['border-style'])) {
                 $css.='border-style: '.$args['border-style'].';';
            }

            if (isset($args['border-width'])) {
                $css.='border-width: '.(int)$args['border-width'].'px;';
            }

            if (!empty($args['border-color'])) {
                $css.='border-color: '.$args['border-color'].';';
            }

            if (isset($args['border-radius']) && $args['border-radius']!='') {
                $css.='border-radius: '.$args['border-radius'].'px;';
            }

            if (!empty($args['background-color']) && empty($args['icon-bg-color'])) {
                $css.='background-color: '.$args['background-color'].';';
            }

            if (!empty($args['icon-color'])) {
                $css.='color: '.$args['icon-color'].';';
            }

            if (!empty($args['icon-bg-color'])) {
                $css.='background-color: '.$args['icon-bg-color'].';';
            }

            $css.= '}';
        }
    }
    
    return $css;
}
