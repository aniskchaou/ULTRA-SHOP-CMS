<?php

class SNP_NHP_Options_tags_multi_select extends SNP_NHP_Options
{
	public function __construct($field = array(), $value ='', $parent)
	{
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

		$this->field = $field;
		$this->value = $value;		
	}

	public function render()
	{	
		$class = (isset($this->field['class']))?'class="'.$this->field['class'].'" ':'';
		
		echo '<select id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].'][]" '.$class.'multiple="multiple" >';
		$args = wp_parse_args($this->field['args'], array());
		$tags = get_tags($args); 
		foreach ( $tags as $tag ) {
			$selected = (is_array($this->value) && in_array($tag->term_id, $this->value))?' selected="selected"':'';
			echo '<option value="'.$tag->term_id.'"'.$selected.'>'.$tag->name.'</option>';
		}
		echo '</select>';

		echo (isset($this->field['desc']) && !empty($this->field['desc']))?'<br/><span class="description">'.$this->field['desc'].'</span>':'';
		
	}
}