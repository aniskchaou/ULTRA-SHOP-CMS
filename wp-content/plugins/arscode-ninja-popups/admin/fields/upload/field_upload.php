<?php

class SNP_NHP_Options_upload extends SNP_NHP_Options
{

    public function __construct($field = array(), $value = '', $parent = '')
    {
    	$this->field = $field;
    	$this->value = $value;
    	$this->args = $parent->args;
    	$this->url = $parent->url;
    }

    public function render()
    {
        $path_info = pathinfo($this->value);

    	$class = (isset($this->field['class'])) ? $this->field['class'] : 'regular-text';

    	echo '<input type="hidden" id="' . $this->field['id'] . '" name="' . $this->args['opt_name'] . '[' . $this->field['id'] . ']" value="' . $this->value . '" class="' . $class . '" />';

        if (isset($path_info['extension']) && in_array($path_info['extension'], array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
    	   echo '<img class="snp-nhp-opts-screenshot" id="snp-nhp-opts-screenshot-' . $this->field['id'] . '" src="' . $this->value . '" />';
    	} else {
            echo '<a href="' . $this->value . '">' . $this->value . '</a>';
        }

        if ($this->value == '') {
    		$remove	= ' style="display:none;"';
    		$upload	= '';
    	} else {
    		$remove	= '';
    		$upload	= ' style="display:none;"';
    	}

    	echo ' <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="snp-nhp-opts-upload button-secondary"' . $upload . ' rel-id="' . $this->field['id'] . '">' . __('Upload', 'nhp-opts') . '</a>';
    	echo ' <a href="javascript:void(0);" class="snp-nhp-opts-upload-remove button-secondary"' . $remove . ' rel-id="' . $this->field['id'] . '">' . __('Remove Upload', 'nhp-opts') . '</a>';
    	echo (isset($this->field['desc']) && !empty($this->field['desc'])) ? '<br/><span class="description">' . $this->field['desc'] . '</span>' : '';
    }

    public function enqueue()
    {
    	$wp_version = floatval(get_bloginfo('version'));

    	if ($wp_version < "3.5") {
    		wp_enqueue_script(
    			'snp-nhp-opts-field-upload-js', SNP_NHP_OPTIONS_URL . 'fields/upload/field_upload_3_4.js', array('jquery', 'thickbox', 'media-upload'), time(), true
    		);
    		wp_enqueue_style('thickbox');
    	} else {
    		wp_enqueue_script(
    			'snp-nhp-opts-field-upload-js', SNP_NHP_OPTIONS_URL . 'fields/upload/field_upload.js', array('jquery'), time(), true
    		);
    		wp_enqueue_media();
    	}

    	wp_localize_script('snp-nhp-opts-field-upload-js', 'nhp_upload', array('url' => $this->url . 'fields/upload/blank.png'));
    }
}