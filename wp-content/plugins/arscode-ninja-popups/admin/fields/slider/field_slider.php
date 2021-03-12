<?php
class SNP_NHP_Options_slider extends SNP_NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since SNP_NHP_Options 1.0
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since SNP_NHP_Options 1.0
	*/
	function render(){
		
		$class = (isset($this->field['class']))?$this->field['class']:'';
		
		if(!$this->value)
		{
			$this->value=isset($this->field['std']) ? $this->field['std'] : '';
		}
		echo '<div style="display: inline-block; width: 40%; margin-right: 25px;" id="'.$this->field['id'].'-div"></div>';
		echo '<input type="text" id="'.$this->field['id'].'" name="'.$this->args['opt_name'].'['.$this->field['id'].']" value="'.$this->value.'" class="'.$class.'" style="width:40px; text-align:center;"/>';
		echo (isset($this->field['desc']) && !empty($this->field['desc']))?' <span class="description">'.$this->field['desc'].'</span>':'';
		?>
		<script>
		jQuery(document).ready(function(){
			jQuery( "#<?php echo $this->field['id']; ?>-div" ).slider({
			value: jQuery( "#<?php echo $this->field['id']; ?>" ).val(),
			min: <?php echo $this->field['min']; ?>,
			max: <?php echo $this->field['max']; ?>,
			step: <?php echo $this->field['step']; ?>,
			    slide: function( event, ui ) {
			      jQuery( "#<?php echo $this->field['id']; ?>" ).val( ui.value );
			    }
			});
			jQuery( "#<?php echo $this->field['id']; ?>" ).keyup( function(){
			     jQuery( "#<?php echo $this->field['id']; ?>-div" ).slider( "value", jQuery(this).val() );
			});
		});
		</script>
		 <?php
		
	}//function
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since SNP_NHP_Options 1.0
	*/
	function enqueue(){
		wp_enqueue_script(
			'nhp-opts-field-slider-js', 
			SNP_NHP_OPTIONS_URL.'fields/slider/field_slider.js', 
			array('jquery', 'jquery-ui-slider'),
			time(),
			true
		);
		global $wp_scripts;
		$ui = $wp_scripts->query('jquery-ui-core');
		// tell WordPress to load the Smoothness theme from Google CDN
		$protocol = is_ssl() ? 'https' : 'http';
		$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";
		wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
		
	}//function
	
}//class
