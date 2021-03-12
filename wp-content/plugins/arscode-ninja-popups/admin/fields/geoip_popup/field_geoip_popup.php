<?php

class SNP_NHP_Options_geoip_popup extends SNP_NHP_Options
{
    public function __construct($field = array(), $value ='', $parent)
    {
        parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

        $this->field = $field;
        $this->value = $value;
    }

    public function render()
    {
        $countryList = snp_get_countries();

        $class = (isset($this->field['class'])) ? $this->field['class'] : 'regular-text';

        echo '<ul id="'.$this->field['id'].'-ul">';

        if (isset($this->value) && is_array($this->value)) {
            foreach($this->value as $key => $value) {
                if (is_numeric($key)) {
                    echo '<li>';

                    echo '<div>';
                    echo 'Country:<br/><select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']['.$key.'][country]" class="' . $class . '">';
                    echo '<option value="">--</option>';
                    foreach ($countryList as $countryListKey => $cl) {
                        echo '<option value="' . $cl['alpha2'] . '" ' . selected($value['country'], $cl['alpha2'], false) . '>' . $cl['name'] . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';

                    echo '<div>';
                    echo 'City:<br/><input type="text" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']['.$key.'][city]" value="' . $value['city'] . '" class="' . $class . '">';
                    echo '</div>';

                    echo '<div>';
                    echo 'Zip Code:<br/><input type="text" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']['.$key.'][zip]" value="' . $value['zip'] . '" class="' . $class . '">';
                    echo '</div>';

                    echo '<div>';
                    echo 'Pop-up:<br/><select id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']['.$key.'][popup]" class="' . $class . '">';
                    foreach ($this->field['options'] as $k => $v) {
                        echo '<option value="' . $k . '" ' . selected($value['popup'], $k, false) . '>' . $v . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';

                    echo '<input type="button" class="nhp-opts-geoip-popup-remove button" value="' . __('Remove', 'nhp-opts') . '" />';
                    echo '</li>';
                }
            }
        } else {
            echo '<li>';

            echo '<div>';
            echo 'Country:<br/><select id="' . $this->field['id'] . '" name="'.$this->args['opt_name'].'['.$this->field['id'].'][0][country]" class="'.$class.'">';
            echo '<option value="" selected="selected">--</option>';
            foreach($countryList as $key => $cl) {
                echo '<option value="' . $cl['alpha2'] . '">' . $cl['name'] . '</option>';
            }
            echo '</select>';
            echo '</div>';

            echo '<div>';
            echo 'City:<br/><input type="text" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][0][city]" value="" class="' . $class . '">';
            echo '</div>';

            echo '<div>';
            echo 'Zip Code:<br/><input type="text" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][0][zip]" value="" class="' . $class . '">';
            echo '</div>';

            echo '<div>';
            echo 'Pop-up:<br/><select id="' . $this->field['id'] . '" name="'.$this->args['opt_name'].'['.$this->field['id'].'][0][popup]" class="'.$class.'">';
            foreach($this->field['options'] as $k => $v) {
                echo '<option value="' . $k . '">' . $v . '</option>';
            }
            echo '</select>';
            echo '</div>';

            echo '<input type="button" class="nhp-opts-geoip-popup-remove button" value="'.__('Remove', 'nhp-opts').'" />';
            echo '</li>';
        }

        echo '</ul>';

        echo '<div id="repeater-template" style="display: none;">';
        echo '<li>';
        echo '<div>';
        echo 'Country:<br/><select id="' . $this->field['id'] . '" name="'.$this->args['opt_name'].'['.$this->field['id'].'][{COUNT}][country]" class="'.$class.'" placeholder="Country">';
        echo '<option value="" selected="selected">--</option>';
        echo '<option value=""></option>';
        foreach ($countryList as $countryListKey => $cl) {
            echo '<option value="' . $cl['alpha2'] . '">' . $cl['name'] . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<div>';
        echo 'City:<br/><input type="text" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][{COUNT}][city]" value="" class="' . $class . '">';
        echo '</div>';

        echo '<div>';
        echo 'Zip Code:<br/><input type="text" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . '][{COUNT}][zip]" value="" class="' . $class . '">';
        echo '</div>';

        echo '<div>';
        echo 'Pop-up:<br/><select id="' . $this->field['id'] . '" name="'.$this->args['opt_name'].'['.$this->field['id'].'][{COUNT}][popup]" class="'.$class.'" placeholder="Pop-up">';
        echo '<option value=""></option>';
        foreach($this->field['options'] as $k => $v) {
            echo '<option value="' . $k . '">' . $v . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<input type="button" class="nhp-opts-geoip-popup-remove button" value="'.__('Remove', 'nhp-opts').'" />';
        echo '</li>';
        echo '</div>';

        echo '<input type="button" class="nhp-opts-geoip-popup-add button" rel-id="'.$this->field['id'].'-ul" rel-name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" value="'.__('Add More', 'nhp-opts').'" />';
        echo '<br/>';

        echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
    }

    public function enqueue()
    {
        wp_enqueue_script('nhp-opts-field-geoip-popup-js', SNP_NHP_OPTIONS_URL.'fields/geoip_popup/field_geoip_popup.js', array('jquery'), time(), true);
    }
}
