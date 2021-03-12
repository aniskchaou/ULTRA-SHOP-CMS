<?php
class SNP_NHP_Options_builder extends SNP_NHP_Options
{	
	var	$fonts = array();

	function __construct($field = array(), $value ='', $parent)
    {
        parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

        $this->field = $field;
        
        if (is_array($value)) {
            $this->value = $value;
        } else {
            $this->value = unserialize(base64_decode($value));
        }
            
	    wp_enqueue_script('jquery-ui-draggable');
	    wp_enqueue_script('jquery-ui-dialog');
	    wp_enqueue_script('jquery-ui-droppable');
	    wp_enqueue_script('jquery-ui-resizable');
	    wp_enqueue_script('wp-color-picker');
	    wp_enqueue_style( 'wp-color-picker' ); 
	    wp_enqueue_media();
	    wp_enqueue_script(
		    'snp-nhp-opts-field-builder-js', SNP_NHP_OPTIONS_URL . 'fields/builder/field_builder.js', array('jquery'), time(), true
	    );
        
        if(snp_get_option('ml_manager') == 'mailchimp') {
            wp_enqueue_script(
                'snp-nhp-opts-mc-field-builder-js', SNP_NHP_OPTIONS_URL . 'fields/builder/snp_mc_fields.js', array('jquery'), time(), true
            );
            wp_enqueue_script(
                'snp-nhp-opts-mc-field-builder-groups-js', SNP_NHP_OPTIONS_URL . 'fields/builder/snp_mc_groups.js', array('jquery'), time(), true
            );
        }

        if (snp_get_option('ml_manager') == 'sharpspring') {
            wp_enqueue_script(
                'snp-nhp-opts-sharpspring-field-builder-js', SNP_NHP_OPTIONS_URL . 'fields/builder/snp_sharpspring_fields.js', array('jquery'), time(), true
            );
        }

	    wp_enqueue_script(
		    'snp-webfont-js', 'https://ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js', array(), time(), true
	    );
        wp_register_style( 'snp-reset-builder', SNP_URL . 'themes/reset-builder.css' );
        wp_enqueue_style( 'snp-reset-builder' );
            
        wp_register_style( 'snp-nhp-opts-field-builder-css', SNP_NHP_OPTIONS_URL . 'fields/builder/field_builder.css' );
        wp_enqueue_style( 'snp-nhp-opts-field-builder-css' );
            
        wp_register_style( 'snp-animate', SNP_URL . 'themes/animate.min.css' );
        wp_enqueue_style( 'snp-animate' );
            
        snp_init_fontawesome();
	}
	
	function render()
	{
		?>
        <div id="builder-loading"><div class="builder-loading-spinner"><i class="fa fa-cog fa-spin"></i> Loading...</div></div>
        <style>
            #builder-loading {
                position:   fixed;
                top:        0;
                left:       0;
                height:     100%;
                width:      100%;
                font-size: 30px;
                color: #000;
                text-align: center;
                opacity: 0.7;
                background-color: #eeeeee;
                z-index: 999999999;
            }
            
            .builder-loading-spinner {
                display: block !important;
                position: relative;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        </style>
        <script>
            jQuery(document).ready(function($) {
                $('#builder-tpl').show();
                $('#builder-container').show();
                $('#builder-loading').hide();
            });    
        </script>
        <div id="builder-tpl-editboxes" style="display: none;">
            <?php
            $this->element_tpl_editbox('text', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('pointlist', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('img', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('video', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('box', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('button', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('input', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('textarea', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('select', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('map', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('hr', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('html', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('captcha', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('hidden', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('calendar', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('checkbox', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('radio', array('preset' => 1), 'RAND');
            $this->element_tpl_editbox('file', array('preset' => 1), 'RAND');
            ?>
        </div>
		<div id="builder-tpl" style="display: none;">
		    <?php
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('text', array(
                    'preset'  => 1, 
                    'width'   => 200,
                    'height'  => 75,
                    'content' => ''
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('pointlist', array(
                    'preset'  => 1,
                    'width'   => 200,
                    'height'  => 75,
                    'options' => array()
                ));
            echo '</div>';
            
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('img', array(
                    'preset'    => 1,
                    'textlabel' => 1,
                    'width'     => '165', 
                    'height'    => '100',
                    'img'       => SNP_URL . 'admin/img/img-placeholder.png'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('video', array(
                    'preset'    => 1,
                    'textlabel' => 1,
                    'width'     => '165',
                    'height'    => '100'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('box', array(
                    'preset'           => 1,
                    'width'            => '150',
                    'height'           => '150',
                    'background-color' => 'lightgrey'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('button', array(
                    'preset'              => 1,
                    'width'               => '200',
                    'height'              => '50',
                    'text'                => 'Send IT Now <i style="margin-left: 15px;" class="fa fa-arrow-right"></i>',
                    'loading-text'        => '<i class="fa fa-spinner fa-spin"></i>',
                    'border-style'        => 'none',
                    'font'                => 'Open Sans',
                    'font-size'           => '17',
                    'color'               => '#ffffff',
                    'bold'                => '1',
                    'background-color'    => '#197FD2',
                    'background-repeat'   => 'repeat',
                    'background-position' => 'center center',
                    'z-index'             => '100',
                    'custom-css'          => 'padding-left: 10px;'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('input',array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '50',
                    'icon'         => '',
                    'border-style' => 'solid',
                    'border-color' => '#999999',
                    'border-width' => '2'
                ));
            echo '</div>';
            
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('map', array(
                    'preset'    => 1,
                    'textlabel' => 1,
                    'width'     => '165',
                    'height'    => '100'
                ));
            echo '</div>';
            
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('textarea', array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '100',
                    'icon'         => '',
                    'border-style' => 'solid',
                    'border-color' => '#999999',
                    'border-width' => '2'
                ));
            echo '</div>';
                
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('select', array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '50',
                    'icon'         => '',
                    'border-style' => 'solid',
                    'border-color' => '#999999',
                    'border-width' => '2'
                )); 
            echo '</div>';
            
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('hr', array(
                    'preset' => 1,
                    'width'  => '200',
                    'height' => '3'
                )); 
            echo '</div>';
            
            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('html', array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '100',
                    'icon'         => '',
                    'border-style' => 'solid',
                    'border-color' => '#999999',
                    'border-width' => '2'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('captcha', array(
                    'preset'    => 1,
                    'textlabel' => 1,
                    'width'     => '165',
                    'height'    => '100'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('hidden',array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '20',
                    'icon'         => '',
                    'border-style' => 'dashed',
                    'border-color' => '#999999',
                    'border-width' => '2'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('calendar',array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '50',
                    'icon'         => '',
                    'border-style' => 'solid',
                    'border-color' => '#999999',
                    'border-width' => '2'
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('checkbox', array(
                    'preset' => 1
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('radio', array(
                    'preset' => 1
                ));
            echo '</div>';

            echo '<div class="builder-tpl-lib-group">';
                $this->element_tpl('file',array(
                    'preset'       => 1,
                    'width'        => '200',
                    'height'       => '50',
                    'icon'         => '',
                    'border-style' => 'solid',
                    'border-color' => '#999999',
                    'border-width' => '2'
                ));
            echo '</div>';

            echo '<div class="builder-clear"></div>';
		    $this->step_tpl('RAND',array('width' => 500, 'height' => 500));
		    ?>
		</div>
		<div id="builder-container" style="display: none;">
            <?php
            if (is_array($this->value)) {
                foreach ($this->value as $index => $step) {
                    $this->step_tpl($index, $step['args'], (isset($step['elements'])?$step['elements']:NULL));
                }
		    } else {
                $this->step_tpl(1,array('width' => 500, 'height' => 500));
		    }
		    ?>
        </div>
        <div id="builder-add-button">
            <a href="#" class="bld-button builder-add-step">Add New Step</a>
		</div>
		<script>
            jQuery(function($) {	
                <?php
                if(count($this->fonts)>0) {
                    echo 'WebFont.load({google: {';
                    echo 'families: [';
                    $fs = '';
                    foreach($this->fonts as $f) {
                        $fs .= "'".$f."',";
                    }
                    $fs =  substr($fs, 0, -1);
                    echo $fs;
                    echo ']';
                    echo '}});';
                }
                ?>
            });
		</script>
    <?php
	}

    function element_tpl_editbox($type, $args = array(), $RAND)
	{
        foreach ($args as $k => $v) {
            if (is_array($v)) {
                snp_stripslashes_array($v);
                $args[$k]=$v;
            } else {
                $args[$k]=htmlspecialchars($v);
            }
        }

        $input_disabled = '';
        if ($RAND == 'RAND') {
            $input_disabled = 'disabled="disabled"';
        }
        ?>
        <div class="bld-el-editbox bld-el-editbox-<?php echo $type ;?>" id="editbox-element-<?php echo $RAND ;?>" data-id="element-<?php echo $RAND ;?>">
            <ul class="bld-editbox-tabs-links">
                <li><a href="#" rel="bld-editbox-general" class="bld-editbox-tabs-link bld-editbox-tab-link-active">General</a></li>
                <?php
                if (in_array($type, array(
                    'text',
                    'pointlist',
                    'button',
                    'input',
                    'calendar',
                    'textarea',
                    'select',
                    'radio',
                    'checkbox'
                ))):
                ?>
                <li><a href="#" rel="bld-editbox-font" class="bld-editbox-tabs-link">Font</a></li>
                <?php endif; ?>
                <?php
                if (in_array($type, array(
                    'input',
                    'calendar',
                    'textarea',
                    'select'
                ))):
                ?>
                <li><a href="#" rel="bld-editbox-icon-tab" class="bld-editbox-tabs-link">Icon</a></li>
                <?php endif; ?>
                <?php
                if (in_array($type, array('text', 'pointlist','button','input','calendar','textarea','select','radio','checkbox','box'))):
                ?>
                        <li><a href="#" rel="bld-editbox-background" class="bld-editbox-tabs-link">Background</a></li>
                        <?php
                        endif;
                        if(in_array($type, array('text','pointlist','button','input','calendar','textarea','select','radio','checkbox','box'))):
                        ?>
                        <li><a href="#" rel="bld-editbox-border" class="bld-editbox-tabs-link">Border / Padding</a></li>
                        <?php
                        endif;
                        ?>
                        <li><a href="#" rel="bld-editbox-animate" class="bld-editbox-tabs-link">Animation</a></li>
                        <li><a href="#" rel="bld-editbox-advanced" class="bld-editbox-tabs-link">Advanced</a></li>
                    </ul>
		    <div class="bld-editbox-tab bld-editbox-tab-active bld-editbox-general">
                        <div class="bld-form-group">
                            <label>Width</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-width bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][width]" value="<?php echo (isset($args['width'])?$args['width']:'');?>" />px
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Height</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-height bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][height]" value="<?php echo (isset($args['height'])?$args['height']:'');?>"/>px
                            </div>
                        </div>
			<div class="bld-form-group">
                            <label>Top</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-top bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][top]" value="<?php echo (isset($args['top'])?$args['top']:'');?>"/>px
                            </div>
                        </div>
			<div class="bld-form-group">
                            <label>Left</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-left bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][left]" value="<?php echo (isset($args['left'])?$args['left']:'');?>"/>px
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Rotate</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-rotate bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][rotate]" value="<?php echo (isset($args['rotate'])?$args['rotate']:'');?>"/>deg
                            </div>
                        </div>
                        <?php
                        if(in_array($type, array('img'))):
                        ?>
                        <div class="bld-form-group">
                            <label>Image</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-img bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][img]" value="<?php echo (isset($args['img'])?$args['img']:'');?>"/>
                                <a href="#" class="button bld-editbox-img-button">Change</a>
                                <a href="#" class="button bld-editbox-imglibrary-button">Browse Image Library</a>      
                            </div>
                        </div>
                        <?php
                        endif;
                        if(in_array($type, array('button','input','file','calendar','hidden','textarea','select','radio','checkbox'))):  ?>

                            <?php if (in_array($type, array(
                                'radio',
                                'checkbox'
                            ))): ?>
                            <div class="bld-form-group">
                                <label>Label</label>
                                <div class="bld-inputs">
                                    <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-label bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][label]" value="<?php echo (isset($args['label'])?$args['label']:'');?>"/>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (in_array($type, array('radio', 'checkbox', 'select')) && snp_get_option('ml_manager')): ?>
                                <div class="bld-form-group">
                                    <label>Connect with MailChimp group</label>
                                    <div class="bld-inputs">
                                        <select <?php echo isset($input_disabled)?$input_disabled:''; ?> name="snp_bld[<?php echo $RAND; ?>][mailchimp_group]" class="bld-mc-select-groups" data-value="<?php echo (isset($args['mailchimp_group'])?$args['mailchimp_group']:''); ?>"></select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (in_array($type, array('button','input','file','calendar','hidden','textarea','select','radio','checkbox'))):  ?>
                            	<div class="bld-form-group">
                                	<label>Field Name</label>
                                	<div class="bld-inputs">
                                    	<input type="radio" <?php echo isset($input_disabled)?$input_disabled:''; ?> <?php checked( (isset($args['name-type'])?$args['name-type']:''), 'email' ); ?> name="snp_bld[<?php echo $RAND ;?>][name-type]" value="email" /> use as e-mail field<br />
                                    	<input type="radio" <?php echo isset($input_disabled)?$input_disabled:''; ?> <?php checked( (isset($args['name-type'])?$args['name-type']:''), 'name' ); ?> name="snp_bld[<?php echo $RAND ;?>][name-type]" value="name" /> use as name field<br />
                                    	<input type="radio" <?php echo isset($input_disabled)?$input_disabled:''; ?> <?php checked( (isset($args['name-type'])?$args['name-type']:''), '' ); ?> name="snp_bld[<?php echo $RAND ;?>][name-type]" value="" /> use as custom field:<br />
                                    	<input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-name" name="snp_bld[<?php echo $RAND ;?>][name]" value="<?php echo (isset($args['name'])?$args['name']:'');?>"/>
                                    	<?php if(snp_get_option('ml_manager') == 'mailchimp'): ?>
                                        	<select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-mc-select-name"></select>
                                    	<?php endif; ?>
                                        <?php if (snp_get_option('ml_manager') == 'sharpspring'): ?>
                                            <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-sharpspring-select-name"></select>
                                        <?php endif; ?>
                                	</div>
                            	</div>
                            <?php endif; ?>
                            
                            <?php if (in_array($type, array('input','calendar','hidden','textarea','select','radio','checkbox'))):  ?>
                            	<div class="bld-form-group">
                                	<label>Required</label>
                                	<div class="bld-inputs">
                                    	<input type="checkbox" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-required" <?php checked( (isset($args['required'])?$args['required']:''), 1 ); ?> name="snp_bld[<?php echo $RAND ;?>][required]" value="1" />
                                	</div>
                           		</div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(in_array($type, array('button', 'input', 'calendar', 'hidden', 'textarea'))): ?>
                            <div class="bld-form-group">
                                <label>Default Text</label>
                                <div class="bld-inputs">
                                    <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-text bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][text]" value="<?php echo (isset($args['text'])?$args['text']:'');?>"/>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (in_array($type, array('radio', 'checkbox', 'button'))): ?>
                            <div class="bld-form-group">
                                <label>Default Value</label>
                                <div class="bld-inputs">
                                    <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-value bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][value]" value="<?php echo (isset($args['value'])?$args['value']:'');?>"/>
                                </div>
                            </div>
                            <?php if (in_array($type, array('radio', 'checkbox'))): ?>
                            	<div class="bld-form-group">
                                	<label>Checked by default</label>
                                	<div class="bld-inputs">
                                    	<input type="checkbox" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-checked_default" <?php checked( (isset($args['checked_default'])?$args['checked_default']:''), 1 ); ?> name="snp_bld[<?php echo $RAND ;?>][checked_default]" value="1" />
                                	</div>
                            	</div>
                            <?php endif; ?>	
                        <?php endif; ?>
                        <?php if(in_array($type, array('button'))): ?>
                        <div class="bld-form-group">
                            <label>Loading Text</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-loading-text bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][loading-text]" value="<?php echo (isset($args['loading-text'])?$args['loading-text']:'');?>"/>
                                <span class="bld-tip">(only for submit buttons)</span>
                            </div>
                        </div>
                        <?php
                        endif;
                        if(in_array($type, array('select','pointlist'))):
                        ?>
                            <div class="bld-form-group bld-form-group-selectoptions">
                                <label><?php if($type=='pointlist') echo 'Points'; else echo 'Options';?></label>
                                <div class="bld-inputs">
			                        <?php
			                        if(!isset($args['options']) || count($args['options'])==0)
			                        {
				                        $args['options']=array('');
			                        }
			                        foreach($args['options'] as $option)
			                        {
				                        echo '<div class="bld-editbox-selectoptions-option"><input '.($type=='pointlist' ? 'class="bld-editbox-pointlis-points bld-editbox-pointlist-change"' : '').' type="text" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][options][]" value="'.$option.'"/><a href="#" class="bld-editbox-selectoptions-delete '.($type=='pointlist' ? 'bld-editbox-pointlist-change-btn' : '').' button">Delete</a></div>';
			                        }
			                        ?>
                                    <input class="<?php if($type=='pointlist') echo 'bld-editbox-pointlist-change-btn ';?>bld-editbox-selectoptions-add button button-large" type="button" value="Add">
                                </div>
                            </div>
                        <?php
                        endif;
                        if(in_array($type, array('pointlist'))):
                        ?>
                        <div class="bld-form-group">
                            <label>Point Image</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-pointimg bld-editbox-live-pointlist" name="snp_bld[<?php echo $RAND ;?>][pointimg]" value="<?php echo (isset($args['pointimg'])? $args['pointimg']:'');?>"/>
                                <a href="#" class="button bld-editbox-img-button">Change</a>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Point Image Margin</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-pointimg-padding bld-editbox-live-pointlist bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][pointimg-padding]" value="<?php echo (isset($args['pointimg-padding'])? $args['pointimg-padding']:'');?>"/>px
                            </div>
                        </div>
                        <?php
                        endif;
                        if(in_array($type, array('input','calendar','textarea','select'))):
                        ?>
                        <div class="bld-form-group">
                            <label>Placeholder</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-placeholder bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][placeholder]" value="<?php echo (isset($args['placeholder'])? $args['placeholder']:'');?>"/>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if(in_array($type, array('img'))): ?>
                            <div class="bld-form-group">
                                <label>ALT attribute</label>
                                <div class="bld-inputs">
                                    <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-placeholder bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][alt]" value="<?php echo (isset($args['alt'])? $args['alt']:'');?>"/>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if(in_array($type, array('button','img'))): ?>
                        <div class="bld-form-group">
                            <label>Click Action</label>
                            <div class="bld-inputs">
                                <?php 
                                foreach(array(
                                            'submit' => 'Send Opt-in Form and Close Popup',
                                            'submit-step' => 'Send Opt-in Form and Go to Step',
                                            'gotostep' => 'Go to Step',
                                            'link' => 'Open Url',
                                            'select_link' => 'Open Url based on select box value',
                                            'close' => 'Close Popup'
                                            ) as $s => $v)
                                {
                                    echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][action]" value="'.$s.'" '.((!isset($args['action']) && $s=='send') || (isset($args['action']) && $args['action']==$s) ? 'checked' : '').'>'.$v.'';
                                    if ($s == 'submit') {
                                        echo '<hr />';
                                    }

                                    if ($s == 'submit-step') {
                                        echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="bld-editbox-small bld-editbox-text-right" name="snp_bld['.$RAND.'][action-step-submit]" value="'.(isset($args['action-step-submit'])?$args['action-step-submit']:'').'"/>';
                                        echo '<hr />';
                                    }

                                    if ($s == 'gotostep') {
                                        echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="bld-editbox-small bld-editbox-text-right" name="snp_bld['.$RAND.'][action-step]" value="'.(isset($args['action-step'])?$args['action-step']:'').'"/>';
                                        echo '<hr />';
                                    }
                                    if ($s == 'link') {
                                        echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="bld-editbox-medium" name="snp_bld['.$RAND.'][action-link]" value="'.(isset($args['action-link'])?$args['action-link']:'').'"/><br />';
                                        echo '<input type="checkbox" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][open_type]" value="blank" '.((isset($args['open_type']) && $args['open_type']=='blank') ? 'checked' : '').'> Open in new window';
                                        echo '<br /><input type="checkbox" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][set_cookie]" value="yes" '.((isset($args['set_cookie']) && $args['set_cookie']=='yes') ? 'checked' : '').'> Do not open pop-up again (depends on value Cookie Time on Conversion)';
                                        echo '<br /><input type="checkbox" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][click_as_conversion]" value="yes" '.((isset($args['click_as_conversion']) && $args['click_as_conversion']=='yes') ? 'checked' : '').'> Count click action as conversion';
                                        echo '<hr />';
                                    }
                                    if ($s == 'select_link') {
                                        ?>
                                        <br />
	                                    <?php echo '<input type="checkbox" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][select_link_open_type]" value="blank" '.((isset($args['select_link_open_type']) && $args['select_link_open_type']=='blank') ? 'checked' : '').'> Open in new window'; ?>
                                        <br />
                                        <?php echo '<input type="checkbox" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][select_link_set_cookie]" value="yes" '.((isset($args['select_link_set_cookie']) && $args['select_link_set_cookie']=='yes') ? 'checked' : '').'> Do not open pop-up again (depends on value Cookie Time on Conversion)'; ?>
                                        <br />
                                        <div class="bld-form-group bld-form-group-select-link-options">
                                            <div class="bld-inputs">
			                                    <?php
			                                    if (!isset($args['select_link_options']) || count($args['select_link_options'])==0) {
				                                    $args['select_link_options'] = array( 'name' => '', 'url' => '' );
			                                    }

			                                    $i = 0;
			                                    foreach($args['select_link_options'] as $option) {
				                                    echo '<div class="bld-editbox-select-link-options-option">
                                                        Search for value: <input type="text" ' . $input_disabled . ' name="snp_bld[' . $RAND . '][select_link_options]['.$i.'][name]" value="' . (isset($option['name']) ? $option['name'] : '') . '"/><br />
                                                        Redirect to: <input type="text" ' . $input_disabled . ' name="snp_bld[' . $RAND . '][select_link_options]['.$i.'][url]" value="' . (isset($option['url']) ? $option['url'] : '') . '"/><a href="#" class="bld-editbox-select-link-options-delete button">Delete</a>
                                                    </div>';

				                                    $i++;
			                                    }
			                                    ?>
                                                <input class="bld-editbox-select-link-options-add button button-large" type="button" value="Add">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    echo '<br/>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        endif;
                        if(in_array($type, array('video'))):
                        ?>
                        <div class="bld-form-group">
                            <label>Video ID</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="" name="snp_bld['.$RAND.'][video-url]" value="'.(isset($args['video-url'])?$args['video-url']:'').'"/>';
                                echo '<br /><small>eg. for URL: https://www.youtube.com/watch?v=<b>YE7VzlLtp-4</b> type <b>YE7VzlLtp-4</b></small>';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Autoplay</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-autoplay]" value="1" '.(!isset($args['video-autoplay']) || (isset($args['video-autoplay']) && $args['video-autoplay']==1) ? 'checked' : '').'>Yes ';
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-autoplay]" value="0" '.(!isset($args['video-autoplay']) || (isset($args['video-autoplay']) && $args['video-autoplay']==0) ? 'checked' : '').'>No <br />';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Show controls</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-controls]" value="1" '.(!isset($args['video-controls']) || (isset($args['video-controls']) && $args['video-controls']==1) ? 'checked' : '').'>Yes ';
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-controls]" value="0" '.(!isset($args['video-controls']) || (isset($args['video-controls']) && $args['video-controls']==0) ? 'checked' : '').'>No <br />';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Show title</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-title]" value="1" '.(!isset($args['video-title']) || (isset($args['video-title']) && $args['video-title']==1) ? 'checked' : '').'>Yes ';
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-title]" value="0" '.(!isset($args['video-title']) || (isset($args['video-title']) && $args['video-title']==0) ? 'checked' : '').'>No <br />';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Show recomended videos</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-recommended]" value="1" '.(!isset($args['video-recommended']) || (isset($args['video-recommended']) && $args['video-recommended']==1) ? 'checked' : '').'>Yes ';
                                echo '<input type="radio" '.(isset($input_disabled)?$input_disabled:'').' name="snp_bld['.$RAND.'][video-recommended]" value="0" '.(!isset($args['video-recommended']) || (isset($args['video-recommended']) && $args['video-recommended']==0) ? 'checked' : '').'>No <br />';
                                ?>
                            </div>
                        </div>
                    <?php
                        endif;
                        if(in_array($type, array('map'))):
                        ?>
                        <div class="bld-form-group">
                            <label>API Key</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="" name="snp_bld['.$RAND.'][map-key]" value="'.(isset($args['map-key'])?$args['map-key']:'').'"/>';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Coordinates</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="" name="snp_bld['.$RAND.'][map-coordx]" value="'.(isset($args['map-coordx'])?$args['map-coordx']:'').'"/>';
                                echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="" name="snp_bld['.$RAND.'][map-coordy]" value="'.(isset($args['map-coordy'])?$args['map-coordy']:'').'"/>';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Zoom</label>
                            <div class="bld-inputs">
                                <?php
                                echo '<input type="text" '.(isset($input_disabled)?$input_disabled:'').' class="" name="snp_bld['.$RAND.'][map-zoom]" value="'.(isset($args['map-zoom'])?$args['map-zoom']:'').'"/>';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Map type</label>
                            <div class="bld-inputs">
                                <?php
                                echo    '<select name="snp_bld['.$RAND.'][map-type]">
                                            <option value="ROADMAP" '.(isset($args['map-type']) && $args['map-type'] == 'ROADMAP'?'selected':'') .'>Roadmap</option>
                                            <option value="SATELLITE" '.(isset($args['map-type']) && $args['map-type'] == 'SATELLITE'?'selected':'') .'>Satellite</option>
                                            <option value="HYBRID" '.(isset($args['map-type']) && $args['map-type'] == 'HYBRID'?'selected':'') .'>Hybrid</option>
                                            <option value="TERRAIN" '.(isset($args['map-type']) && $args['map-type'] == 'TERRAIN'?'selected':'') .'>Terrain</option>
                                        </select>';
                                ?>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Icon</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="" name="snp_bld[<?php echo $RAND ;?>][map-icon]" value="<?php echo (isset($args['map-icon'])?$args['map-icon']:'');?>"/>
                                <a href="#" class="button bld-editbox-img-button">Change</a>
                            </div>
                        </div>
                        <?php
                        endif;
                        ?>
                        
		    		<?php if (in_array($type, array('html'))): ?>
		    		<div class="bld-form-group">
		    			<label>Custom HTML</label>
		    			<div class="bld-inputs">
		    				<?php echo '<textarea name="snp_bld['.$RAND.'][custom_html]" style="height: 300px;">'.(isset($args['custom_html']) ? $args['custom_html'] : '').'</textarea>'; ?>
		    			</div>
		    		</div>
		    		<?php endif; ?>
    		    </div>
                    <?php
                    if(in_array($type, array('input','calendar','textarea','select'))):
                    ?>
                    <div class="bld-editbox-tab bld-editbox-icon-tab">
                        <div class="bld-form-group">
                            <label>Icon</label>
                            <div class="bld-inputs">
                                <div style="overflow: auto; width: 100%; height: 200px;">
                                <?php 
                                echo '<label><input '.(isset($input_disabled)?$input_disabled:'').' class="bld-editbox-icon bld-editbox-live-change" type="radio" name="snp_bld['.$RAND.'][icon]" value="" '.((!isset($args['icon']) || $args['icon']=='') ? 'checked' : '').'> Disabled</label>';
                                $icons = snp_get_font_awesome_list();
                                foreach($icons as $s)
                                {
                                    echo '<label><input '.(isset($input_disabled)?$input_disabled:'').' class="bld-editbox-icon bld-editbox-live-change" type="radio" name="snp_bld['.$RAND.'][icon]" value="'.$s.'" '.((isset($args['icon']) && $args['icon']==$s) ? 'checked' : '').'> <i class="fa fa-'.$s.'"></i></label>';
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Icon Color</label>
                            <div class="bld-inputs">
                                <input type="text" type="text" <?php echo (isset($input_disabled)?$input_disabled:''); ?> class="bld-editbox-icon-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][icon-color]" value="<?php echo isset($args['icon-color'])?$args['icon-color']:'';?>"/>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Icon Background</label>
                            <div class="bld-inputs">
                                <input type="text" type="text" <?php echo (isset($input_disabled)?$input_disabled:''); ?> class="bld-editbox-icon-bg-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][icon-bg-color]" value="<?php echo isset($args['icon-bg-color'])?$args['icon-bg-color']:'';?>"/>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Icon Size</label>
                            <div class="bld-inputs">
                                <select <?php echo (isset($input_disabled)?$input_disabled:''); ?> class="bld-editbox-icon-size bld-editbox-live-change bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][icon-size]">
                                    <?php 
                                    for($i=5;$i<=220;$i++)
                                    {
                                        echo '<option value="'.$i.'" '.((!isset($args['icon-size']) && $i==14) || (isset($args['icon-size']) && $args['icon-size']==$i) ? 'selected' : '').'>'.$i.'</option>';								//tutaj
                                    }
                                    ?>
                                </select>px
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Icon Field Width</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-icon-field-width bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][icon-field-width]" value="<?php echo isset($args['icon-field-width'])?$args['icon-field-width']:'';?>"/>px
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Right Border</label>
                            <div class="bld-inputs">
                                <input type="checkbox" class="bld-editbox-icon-right-border bld-editbox-live-change" <?php checked( isset($args['icon-right-border'])?$args['icon-right-border']:'', 1 ); ?> name="snp_bld[<?php echo $RAND ;?>][icon-right-border]" value="1" /> (border between icon field and input)
                                
                            </div>
                        </div>
                    </div>
                    <?php
                    endif;
                    if(in_array($type, array('text','pointlist','button','input','calendar','textarea','select','radio','checkbox','box'))):
                    ?>
		    <div class="bld-editbox-tab bld-editbox-border">
                        <div class="bld-form-group">
                            <label>Border Style</label>
                            <div class="bld-inputs">
                                <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-border-style bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][border-style]">
                                <?php 
                                foreach(array('','none', 'solid', 'dashed', 'dotted', 'double') as $s)
                                {
                                    echo '<option value="'.$s.'" '.(((!isset($args['border-style']) && $s=='') || (isset($args['border-style']) && ($args['border-style']==$s)) )? 'selected' : '').'>'.$s.'</option>';
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Border Width</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-border-width bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][border-width]" value="<?php echo isset($args['border-width'])?$args['border-width']:'';?>"/>px
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Border Radius</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-border-radius bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][border-radius]" value="<?php echo isset($args['border-radius'])?$args['border-radius']:'';?>"/>px
                            </div>
                        </div>
			<div class="bld-form-group">
                            <label>Border Color</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-border-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][border-color]" value="<?php echo isset($args['border-color'])?$args['border-color']:'';?>"/>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Padding</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-padding bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][padding]" value="<?php echo isset($args['padding'])?$args['padding']:'';?>"/>px
                            </div>
                        </div>
		    </div>
                    <?php
                    endif;
		    if(in_array($type, array('text','pointlist','button','input','calendar','textarea','select','radio','checkbox'))):
		    ?>
		    <div class="bld-editbox-tab bld-editbox-font">
			<div class="bld-form-group">
                            <label>Font</label>
                            <div class="bld-inputs">
                                <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-font bld-editbox-live-font" name="snp_bld[<?php echo $RAND ;?>][font]">
                                <?php 
                                foreach(snp_get_fonts() as $fg)
                                {
                                    echo '<optgroup label="'.$fg['label'].'">';
                                    foreach($fg['fonts'] as $f)
                                    {
                                        echo '<option value="'.$f.'" '.((!isset($args['font']) && $f=='Open Sans') || (isset($args['font']) && ($args['font']==$f))? 'selected' : '').'>'.$f.'</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                                </select>
                            </div>
                        </div>
			<?php
                        if(in_array($type, array('pointlist'))):
                        ?>
                        <div class="bld-form-group">
                            <label>Line Height</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-lineheight <?php if($type=='pointlist') echo 'bld-editbox-live-pointlist'; else echo 'bld-editbox-live'; ?> bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][lineheight]" value="<?php echo (isset($args['lineheight'])? $args['lineheight']:'');?>"/>px
                            </div>
                        </div>
                        <?php
                        endif;
			if($type!='text'):
			?>
			<div class="bld-form-group">
                            <label>Font Size</label>
                            <div class="bld-inputs">
                                <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-font-size bld-editbox-live-change bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][font-size]">
                                    <?php 
                                    for($i=5;$i<=220;$i++)
                                    {
                                        echo '<option value="'.$i.'" '.((!isset($args['font-size']) && $i==14) || (isset($args['font-size']) && $args['font-size']==$i) ? 'selected' : '').'>'.$i.'</option>';
                                    }
                                    ?>
                                </select>px
                            </div>
                        </div>
			<div class="bld-form-group">
                            <label>Text Color</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][color]" value="<?php echo (isset($args['color']) ? $args['color']:'');?>"/>
                                <br />
                                <input type="checkbox" class="bld-editbox-bold bld-editbox-live-change" <?php checked( (isset($args['bold'])?$args['bold']:''), 1 ); ?> name="snp_bld[<?php echo $RAND ;?>][bold]" value="1" /> Bold
                                <br />
                                <input type="checkbox" class="bld-editbox-italic bld-editbox-live-change" <?php checked( (isset($args['italic'])?$args['italic']:''), 1 ); ?> name="snp_bld[<?php echo $RAND ;?>][italic]" value="1" /> Italic
                                <br />
                                <input type="checkbox" class="bld-editbox-underline bld-editbox-live-change" <?php checked( (isset($args['underline'])?$args['underline']:''), 1 ); ?> name="snp_bld[<?php echo $RAND ;?>][underline]" value="1" /> Underline
                            </div>
                        </div>
                        <?php
                        endif;
                        if(in_array($type, array('input','calendar','textarea','select'))):
                        ?>
                        <div class="bld-form-group">
                            <label>Placeholder Color</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-placeholder-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][placeholder-color]" value="<?php echo isset($args['placeholder-color'])?$args['placeholder-color']:'';?>"/>
                            </div>
                        </div>
			<?php
			endif;
			?>
		    </div>
                    <?php
                    endif;
		    if(in_array($type, array('text','pointlist','button','input','calendar','textarea','select','radio','checkbox','box'))):
		    ?>
                    <div class="bld-editbox-tab bld-editbox-background">
                        <div class="bld-form-group">
                            <label>Background Color</label>
                            <div class="bld-inputs">
                                <input type="text" type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-background-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][background-color]" value="<?php echo isset($args['background-color'])?$args['background-color']:'';?>"/>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Background Image</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-background-image bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][background-image]" value="<?php echo isset($args['background-image'])?$args['background-image']:'';?>"/>
                                <a href="#" class="button bld-editbox-img-button">Change</a>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Background Repeat</label>
                            <div class="bld-inputs">
                                <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-background-repeat bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][background-repeat]">
                                <?php 
                                foreach(array('repeat','repeat-x','repeat-y','no-repeat') as $s)
                                {
                                    echo '<option value="'.$s.'" '.((!isset($args['background-repeat']) && $s=='repeat') || (isset($args['background-repeat']) && ($args['background-repeat']==$s)) ? 'selected' : '').'>'.$s.'</option>';
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Background Position</label>
                            <div class="bld-inputs">
                                <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-background-position bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][background-position]">
                                <?php 
                                foreach(array('left top','left center','left bottom','right top','right center','right bottom','center top','center center','center bottom') as $s)
                                {
                                    echo '<option value="'.$s.'" '.((!isset($args['background-position']) && $s=='center center') || (isset($args['background-position']) && ($args['background-position']==$s)) ? 'selected' : '').'>'.$s.'</option>';
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
		    <?php
		    endif;
		    ?>
                    <div class="bld-editbox-tab bld-editbox-animate">
			<div class="bld-form-group">
                            <label>Animation</label>
                            <div class="bld-inputs">
                                <select <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-animation bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][animation]">
                                    <?php 
                                    echo '<option value="" '.((isset($args['animation']) && ($args['animation']=='')) ? 'selected' : '').'></option>';
                                    foreach(snp_get_animations() as $fg)
                                    {
                                        echo '<optgroup label="'.$fg['label'].'">';
                                        foreach($fg['animations'] as $f)
                                        {
                                            echo '<option value="'.$f.'" '.((isset($args['animation']) && ($args['animation']==$f)) ? 'selected' : '').'>'.$f.'</option>';
                                        }
                                        echo '</optgroup>';
                                    }
                                    ?>
                                    
                                  </select>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>Animation Delay</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-animation-delay bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][animation-delay]" value="<?php echo (isset($args['animation-delay'])?$args['animation-delay']:'');?>"/>ms
                            </div>
                        </div>
                    </div>
		    <div class="bld-editbox-tab bld-editbox-advanced">
			<div class="bld-form-group">
                            <label>Opacity</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-opacity bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][opacity]" value="<?php echo (isset($args['opacity'])?$args['opacity']:'');?>"/>
                            </div>
                        </div>
			<div class="bld-form-group">
                            <label>Z-index</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-z-index bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][z-index]" value="<?php echo (isset($args['z-index'])?$args['z-index']:'');?>"/>
                            </div>
                        </div>
                        <div class="bld-form-group">
                            <label>CSS Class</label>
                            <div class="bld-inputs">
                                <input type="text" <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-css-class bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][css-class]" value="<?php echo (isset($args['css-class'])?$args['css-class']:'');?>"/>
                            </div>
                        </div>
			<div class="bld-form-group">
                            <label>Custom CSS</label>
                            <div class="bld-inputs">
                                <textarea <?php echo isset($input_disabled)?$input_disabled:''; ?> class="bld-editbox-custom-css bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][custom-css]"><?php echo (isset($args['custom-css'])?$args['custom-css']:'');?></textarea>
                            </div>
                        </div>
		    
            <?php if (in_array($type, array('select', 'textarea', 'input', 'calendar', 'radio', 'checkbox', 'button'))): ?>
                <div class="bld-form-group">
                    <label>Tabindex</label>
                    <div class="bld-inputs">
                        <input type="text" class="bld-editbox-tabindex bld-editbox-live" name="snp_bld[<?php echo $RAND; ?>][tabindex]" value="<?php echo (isset($args['tabindex'])?$args['tabindex']:''); ?>"/>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (in_array($type, array('input', 'captcha', 'textarea', 'select', 'radio', 'checkbox'))): ?>
                <div class="bld-form-group">
                    <label>Validation error message</label>
                    <div class="bld-inputs">
                        <input type="text" class="bld-editbox-validation_message bld-editbox-live" name="snp_bld[<?php echo $RAND; ?>][validation_message]" value="<?php echo (isset($args['validation_message'])?$args['validation_message']:''); ?>"/>
                    </div>
                </div>
            <?php endif; ?>
            </div>
		</div>
    <?php
    }

	function element_tpl($type, $args = array())
	{
	    if (count($args)>0 && !isset($args['preset'])) {
            $RAND = md5(uniqid(time()).time());
            $input_disabled='';
	    } else {
            $RAND = 'RAND';
            $input_disabled = 'disabled="disabled"';
	    }
	    
        if ($type == 'text' && isset($args['content']) && !$args['content']) {
            $args['content'] = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>';
	    }

        if ($type == 'pointlist' && isset($args['options']) && !$args['options']) {
            $args['options'] = array('Consectetur adipiscing elit','Lorem ipsum dolor sit amet');
	    }

	    if ($type=='button' && (!isset($args['text']) || !$args['text'])) {
            $args['text'] = 'Button Text';
	    }

        $cont_css_class = '';
        $cont_data = '';
        $cont_css = '';
        
        if (isset($args['animation']) && !empty($args['animation'])) {
            $cont_css_class .= ' animated '.$args['animation'];
            $cont_data .= 'data-animation="'.$args['animation'].'"';
        }
        
        if (isset($args['animation-delay']) && $args['animation-delay'] != '') {
            $cont_css .= '-webkit-animation-delay: '.$args['animation-delay'].'ms;';
            $cont_css .= 'animation-delay: '.$args['animation-delay'].'ms;';
        }
        ?>
	    <div 
		<?php 
                echo 'style="width: '.(isset($args['height'])?$args['width']:'').'px; height: '.(isset($args['height'])?$args['height']:'').'px;';
                if($RAND!='RAND') { 
                    echo 'top: '.(isset($args['top'])?$args['top']:'').'px; left: '.(isset($args['left'])?$args['left']:'').'px;z-index: '.(isset($args['z-index'])?$args['z-index']:'').'; ';
		} 
                echo $cont_css.'" ';
                ?>
		class="<?php if($RAND=='RAND') {echo 'bld-el-cont-tpl';}?> bld-el-cont bld-el-<?php echo $type; ?> <?php echo $cont_css_class;?>" <?php echo $cont_data; ?> id="element-<?php echo $RAND ;?>">
            <?php if (!isset($args['nolabel']) OR (!$args['nolabel'])) { ?>
            <div class="bld-el-tpl-desc">
                <?php
                if ($type == 'img' && !isset($args['textlabel'])) {
                    echo '<img src="'.(isset($args['img']) && $args['img']!='' ? $args['img'] : SNP_URL . '/admin/img/img-placeholder.png').'" />';
                } else {
                    echo ucfirst($type); 
                }
                ?>
            </div>
            <?php } ?>
            <input type="hidden" <?php if($RAND=='RAND') echo 'disabled';?> name="snp_bld[elements][]" class="bld-el-rand" value="<?php echo $RAND ;?>" />
            <input type="hidden" <?php if($RAND=='RAND') echo 'disabled';?> name="snp_bld[<?php echo $RAND ;?>][type]" class="bld-el-type" value="<?php echo $type; ?>" />
            <?php
            if ($type !='text') {
                echo '<div class="bld-el-handle-d bld-el-handle"></div>';
            }
            ?>
            <div class="bld-el-editbox-opts">
                <a class="bld-el-edit" href="#"><i class="fa fa-pencil"></i> Edit</a>
                <a class="bld-el-del" href="#"><i class="fa fa-times"></i> Delete</a>
                <a class="bld-el-handle-a bld-el-handle" href="#"><i class="fa fa-arrows"></i> Move</a>
            </div>
            <?php 
            if ($RAND != 'RAND') { 
                $this->element_tpl_editbox($type, $args, $RAND);
            } else {
                foreach ((array)$args as $k => $v) {
                    if ($k!='preset' && $k!='content' && $k!='options') {
                        echo '<input disabled class="editbox-args" type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'"/>';
                    }
                }
            }
            ?>
            <?php
            $css = '';
            $ph_css = '';

            if (isset($args['rotate']) && intval($args['rotate']) && $args['rotate'] != 0) {
                $css .= 'transform:rotate('.$args['rotate'].'deg); -webkit-transform:rotate('.$args['rotate'].'deg); -moz-transform:rotate('.$args['rotate'].'deg); -o-transform:rotate('.$args['rotate'].'deg);';
            }

            if (isset($args['color'])) {
                $css .= 'color: '.$args['color'].';';
            }

            if (isset($args['placeholder-color'])) {
                $ph_css .= '<style id="bld-ph-element-'.$RAND.'" type="text/css">#element-'.$RAND.' .bld-el::-webkit-input-placeholder { color: '.$args['placeholder-color'].'; }#element-'.$RAND.' .bld-el::-moz-placeholder { color: '.$args['placeholder-color'].'; }</style>';
            }

            if (isset($args['pointimg'])) {
                $ph_css .= '<style id="bld-ph-element-'.$RAND.'" type="text/css">#element-'.$RAND.' ul.bld-el li { line-height: '.($args['lineheight'] ? $args['lineheight'].'px' : 'normal').'; padding-left: '.((int)$args['pointimg-padding'] ? $args['pointimg-padding'] : 0).'px; background-image: url("'.$args['pointimg'].'");  }</style>';
            }

            if (isset($args['font'])) {
                $css .= 'font-family: '.$args['font'].';';
            }

            if (isset($args['font-size'])) {
                $css .= 'font-size: '.$args['font-size'].'px;';
            }

            if (isset($args['bold']) && ($args['bold']==1)) {
                $css .= 'font-weight: bold;';
            }
            
            if (isset($args['italic']) && ($args['italic']==1)) {
                $css .= 'font-style: italic;';
            }

            if (isset($args['underline']) && ($args['underline']==1)) {
                $css .= 'text-decoration: underline;';
            }

            if (!empty($args['border-style'])) {
                $css .= 'border-style: '.$args['border-style'].';';
            }

            if (isset($args['border-width']) && $args['border-width']!='') {
                $css .= 'border-width: '.$args['border-width'].'px;';
            }

            if (!empty($args['border-color'])) {
                $css .= 'border-color: '.$args['border-color'].';';
            }

            if (!empty($args['border-radius'])) {
                $css .= 'border-radius: '.$args['border-radius'].'px;';
            }

            if (isset($args['padding']) && $args['padding']!='') {
                $css .= 'padding: '.$args['padding'].'px;';
            }

            if (!empty($args['background-color'])) {
                $css .= 'background-color: '.$args['background-color'].';';
            }

            if (!empty($args['background-image'])) {
                $css .= 'background-image: url(\' '.$args['background-image'].'\');';
            }

            if (!empty($args['background-position'])) {
                $css .= 'background-position: '.$args['background-position'].';';
            }

            if (!empty($args['background-repeat'])) {
                $css .= 'background-repeat: '.$args['background-repeat'].';';
            }

            if (isset($args['opacity']) && $args['opacity']!='') {
                $css .= 'opacity: '.$args['opacity'].';';
            }

            if (isset($args['custom-css'])) {
                $css .= $args['custom-css'];
            }

            $css_class = '';
            $data = '';

            if (isset($args['css-class'])) {
                $css_class .= ' '.$args['css-class'];
            }

            if (!isset($css_class)) {
                $css_class = '';
            }

            if ($type == 'text') {
                echo '<div '.($css ? 'style="'.$css.'"' :'').' class="bld-el '.$css_class.'" '.$data.'>'.(isset($args['content']) ? $args['content'] : '').'</div>';
                echo '<input type="hidden" class="bld-el-content" name="snp_bld['.$RAND.'][content]" value="'.(isset($args['content'])?htmlspecialchars((string)$args['content']):'').'" />';
            } else if($type == 'pointlist') {
                echo '<ul '.($css ? 'style="'.$css.'"' :'').' class="bld-el '.$css_class.'" '.$data.'>';
                if (isset($args['options'])) {
                    foreach((array)$args['options'] as $point) {
                        echo '<li>'.$point.'</li>';
                    }
                }
                echo '</ul>';
            } else if($type == 'img') {
                echo '<img '.($css ? 'style="'.$css.'"' :'').' class="bld-el '.$css_class.'" '.$data.' src="'.(isset($args['img']) && $args['img']!='' ? $args['img'] : SNP_URL . '/admin/img/img-placeholder.png').'" />';
            } else if ($type == 'video') {
                echo '<iframe class="snp-bld-video" width="100%" height="100%" src="https://www.youtube.com/embed/'. (isset($args['video-url'])?trim($args['video-url'], ' /'):'YE7VzlLtp-4' ).'" frameborder="0" allowfullscreen></iframe>';        
    		} else if ($type == 'map') {
                echo '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6044.275637456805!2d-73.98346368325204!3d40.75899341147853!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x55194ec5a1ae072e!2sTimes+Square!5e0!3m2!1sen!2s!4v1392901318461" width="100%" height="100%"></iframe>';
            } else if ($type == 'captcha') {
                echo '<img src="/wp-content/plugins/arscode-ninja-popups/admin/fields/builder/img/captcha_icon.png" />';
            } else if ($type == 'button') {
    		    echo '<button '.($css ? 'style="'.$css.'"' :'').' class="bld-el '.$css_class.'" '.$data.'>'.(isset($args['text']) ? $args['text'] : '').'</button>';
    		} else if ($type == 'box') {
    		    echo '<div '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  class="bld-el '.$css_class.'"></div>';
    		} else if ($type=='hr') {
    		    echo '<hr '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  class="bld-el '.$css_class.'"/>';
    		} else if ($type == 'radio') {
                echo '<input type="radio" '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'" />';
            } else if ($type == 'checkbox') {
                echo '<input type="checkbox" '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'" />';
            }

            if (in_array($type, array('input', 'file', 'hidden', 'calendar', 'textarea', 'select', 'html'))) {
                $icon_css_class = '';
                $icon_css = '';

                if (!isset($args['icon']) || $args['icon'] == 'disabled' || $args['icon'] == '') {
                    $icon_css.="display: none;";
                    echo '<div class="bld-table">';
                } else {
                    echo '<div class="bld-table bld-icon">';
                }
                
                if (!isset($args['icon-right-border']) || !$args['icon-right-border']) {
                    //$icon_css.='border-right-width: 0 !important;';
                    $icon_css_class .= 'bld-input-icon-norborder';
                }
                  
                if (isset($args['icon-field-width'])) {
                    $icon_css .= 'width: '.$args['icon-field-width'].'px;';
                }
                
                if (isset($args['icon-size'])) {
                    $icon_css.= 'font-size: '.$args['icon-size'].'px;';
                }

                if (!empty($args['border-style'])) {
                    $icon_css .= 'border-style: '.$args['border-style'].';';
                }

                if (isset($args['border-width'])) {
                    $icon_css .= 'border-width: '.(int)$args['border-width'].'px;';
                }

                if (!empty($args['border-color'])) {
                    $icon_css .= 'border-color: '.$args['border-color'].';';
                }

                if (isset($args['border-radius']) && $args['border-radius']!='') {
                    $icon_css .= 'border-radius: '.$args['border-radius'].'px;';
                }

                if (!empty($args['background-color']) && empty($args['icon-bg-color'])) {
                    $icon_css .= 'background-color: '.$args['background-color'].';';
                }

                if (!empty($args['icon-color'])) {
                    $icon_css .= 'color: '.$args['icon-color'].';';
                }

                if (!empty($args['icon-bg-color'])) {
                    $icon_css .= 'background-color: '.$args['icon-bg-color'].';';
                }

                echo '<span '.($icon_css ? 'style="'.$icon_css.'"' :'').' class="bld-input-icon '.$icon_css_class.'"><i class="fa fa-'.(isset($args['icon'])?$args['icon']:'').'"></i></span>';

                if (!empty($args['height'])) {
                    $css .= 'height: '.($args['height']-2).'px;';
                }
            }

            if ($type == 'input') {
                echo '<div class="bld-table-cont"><input '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'" /></div>';
            } else if ($type == 'hidden') {
                echo '<div class="bld-table-cont"><input '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'" /></div>';
            } else if ($type == 'calendar') {
                echo '<div class="bld-table-cont"><input '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'" /></div>';
            } else if ($type == 'textarea') {
                echo '<div class="bld-table-cont"><textarea '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'">'.(isset($args['text']) ? $args['text'] : '').'</textarea></div>';
            } else if ($type == 'select') {
                echo '<div class="bld-table-cont"><select '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'"><option value="" disabled="" selected="">'.(isset($args['placeholder']) ? $args['placeholder'] : '').'</option></select></div>';
            } else if ($type == 'html') {
                echo '<div class="bld-table-cont"><textarea '.($css ? 'style="'.$css.'"' :'').'  '.$data.' class="bld-el '.$css_class.'"></textarea></div>';
            } else if ($type == 'file') {
                echo '<div class="bld-table-cont"><input type="file" '.($css ? 'style="'.$css.'"' :'').'  '.$data.'  '.(isset($args['text']) ? 'value="'.$args['text'].'"' : '').' '.(isset($args['placeholder']) ? 'placeholder="'.$args['placeholder'].'"' : '').' class="bld-el '.$css_class.'" /></div>';
            }

            if (in_array($type, array('input', 'file', 'hidden', 'calendar', 'textarea', 'select', 'html'))) {
                echo '</div>';
            }

            if ($ph_css!='') {
                echo $ph_css;
            }
		?>
	    </div>
	    <?php
	}

	function step_tpl($index, $args = array(), $elements = array())
	{
        $RAND = $index;
	?>
		<div class="builder-step bld-step-open" id="step-<?php echo $index; ?>" data-step="<?php echo $index; ?>">
            <input type="hidden" <?php if($index=='RAND') echo 'disabled';?> name="snp_bld[elements][]" class="bld-step-rand" value="<?php echo $index ;?>" />
		    <input type="hidden" name="snp_bld[<?php echo $index ;?>][type]" value="step" />
		    <div class="builder-step-opts">
                <strong class="builder-step-opts-nr">Step: <span class="builder-step-label"><?php echo $index; ?></span></strong>
                <div class="step-size">
                    Width: <input class="bld-width" type="text" value="<?php echo (int)$args['width'];?>" />px 
                </div>
                <div class="step-size">
                    Height: <input class="bld-height" type="text" value="<?php echo (int)$args['height'];?>" />px 
                </div>
                <a href="#" class="builder-step-toggle"><i class="fa fa-chevron-down"></i></a>
                <a href="#" class="bld-button builder-del-step"><i class="fa fa-trash"></i></a>
                <a href="#" class="bld-button builder-step-settings"><i class="fa fa-wrench"></i></a>
                <a href="#" class="bld-button builder-animations-test"><i class="fa fa-play"></i></a>
		    </div>
            <div class="bld-step-editbox bld-el-editbox" id="editbox-step-<?php echo $RAND ;?>" data-id="step-<?php echo $RAND ;?>">
                <ul class="bld-editbox-tabs-links">
                    <li><a href="#" rel="bld-editbox-general" class="bld-editbox-tabs-link bld-editbox-tab-link-active">General</a></li>
                    <li><a href="#" rel="bld-editbox-background" class="bld-editbox-tabs-link">Background</a></li>
                    <li><a href="#" rel="bld-editbox-border" class="bld-editbox-tabs-link">Border</a></li>
                    <li><a href="#" rel="bld-editbox-animate" class="bld-editbox-tabs-link">Animation</a></li>
                    <li><a href="#" rel="bld-editbox-advanced" class="bld-editbox-tabs-link">Advanced</a></li>
                </ul>
                <div class="bld-editbox-tab bld-editbox-tab-active bld-editbox-general">
                    <div class="bld-form-group">
                        <label>Width</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-width bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][width]" value="<?php echo (int)$args['width'];?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Height</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-height bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][height]" value="<?php echo (int)$args['height'];?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Position</label>
                        <div class="bld-inputs">
                            <?php 
                            foreach(array(
                                'snp-bld-center' => 'Center',
                                'snp-bld-top-left' => 'Top Left', 
                                'snp-bld-top-center' => 'Top Center', 
                                'snp-bld-top-right' => 'Top Right', 
                                'snp-bld-bottom-left' => 'Bottom Left', 
                                'snp-bld-bottom-center' => 'Bottom Center',
                                'snp-bld-bottom-right' => 'Bottom Right', 
                                'snp-bld-middle-left' => 'Middle Left', 
                                'snp-bld-middle-right' => 'Middle Right', 
                            ) as $s => $v) {
                                echo '<input type="radio" name="snp_bld['.$RAND.'][position]" value="'.$s.'" '.((!isset($args['position']) && $s=='snp-bld-center') || (isset($args['position']) && $args['position']==$s) ? 'checked' : '').'>'.$v.'<br/>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Margin Top</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-margin-top bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][margin-top]" value="<?php echo (isset($args['margin-top'])?$args['margin-top']:'');?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Margin Right</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-margin-right bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][margin-right]" value="<?php echo (isset($args['margin-right'])?$args['margin-right']:'');?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Margin Bottom</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-margin-bottom bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][margin-bottom]" value="<?php echo (isset($args['margin-bottom'])?$args['margin-bottom']:'');?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Margin Left</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-margin-left bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][margin-left]" value="<?php echo (isset($args['margin-left'])?$args['margin-left']:'');?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Disable Overlay</label>
                        <div class="bld-inputs">
                            <input type="checkbox" class="bld-editbox-disable-overlay" name="snp_bld[<?php echo $RAND ;?>][disable-overlay]" <?php echo (isset($args['disable-overlay']) && $args['disable-overlay']==1 ? 'checked' : '');?> value="1"/>
                                    (Overlay will be disabled on this Step)
                        </div>
                    </div>
                </div>
                <div class="bld-editbox-tab bld-editbox-border">
                    <div class="bld-form-group">
                        <label>Border Style</label>
                        <div class="bld-inputs">
                            <select class="bld-editbox-border-style bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][border-style]">
                                <?php
                                foreach(array(
                                    '',
                                    'none',
                                    'solid',
                                    'dashed',
                                    'dotted',
                                    'double'
                                ) as $s) {
                                    echo '<option value="'.$s.'" '.((!isset($args['border-style']) && $s=='') || (isset($args['border-style']) && $args['border-style']==$s) ? 'selected' : '').'>'.$s.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Border Width</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-border-width bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][border-width]" value="<?php echo isset($args['border-width'])?$args['border-width']:'';?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Border Radius</label>
                        <div class="bld-inputs">
                             <input type="text" class="bld-editbox-border-radius bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][border-radius]" value="<?php echo isset($args['border-radius'])?$args['border-radius']:'';?>"/>px
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Border Color</label>
                        <div class="bld-inputs">
                            <input type="text" type="text" class="bld-editbox-border-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][border-color]" value="<?php echo isset($args['border-color'])?$args['border-color']:'';?>"/>
                        </div>
                    </div>
                </div>
                <div class="bld-editbox-tab bld-editbox-animate">
                    <div class="bld-form-group">
                        <label>Open Animation</label>
                        <div class="bld-inputs">
                            <select class="bld-editbox-animation bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][animation]">
                                <?php 
                                echo '<option value="" '.(!isset($args['animation']) || $args['animation']=='' ? 'selected' : '').'></option>';
                                foreach(snp_get_animations() as $fg) {
                                    echo '<optgroup label="'.$fg['label'].'">';
                                    foreach($fg['animations'] as $f) {
                                        echo '<option value="'.$f.'" '.(isset($args['animation']) && $args['animation']==$f ? 'selected' : '').'>'.$f.'</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Close Animation</label>
                        <div class="bld-inputs">
                            <select class="bld-editbox-animation-close bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][animation-close]">
                                <?php 
                                echo '<option value="" '.(!isset($args['animation-close']) || $args['animation-close']=='' ? 'selected' : '').'></option>';
                                foreach(snp_get_animations() as $fg) {
                                    echo '<optgroup label="'.$fg['label'].'">';
                                    foreach($fg['animations'] as $f) {
                                        echo '<option value="'.$f.'" '.(isset($args['animation-close']) && $args['animation-close']==$f ? 'selected' : '').'>'.$f.'</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bld-editbox-tab bld-editbox-background">
                    <div class="bld-form-group">
                        <label>Background Color</label>
                        <div class="bld-inputs">
                            <input type="text" type="text" class="bld-editbox-background-color bld-editbox-live bld-run-colorpicker" name="snp_bld[<?php echo $RAND ;?>][background-color]" value="<?php echo isset($args['background-color'])?$args['background-color']:'';?>"/>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Background Image</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-background-image bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][background-image]" value="<?php echo isset($args['background-image'])?$args['background-image']:'';?>"/>
                            <a href="#" class="button bld-editbox-img-button">Change</a>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Background Video</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-background-video" name="snp_bld[<?php echo $RAND ;?>][background-video]" value="<?php echo isset($args['background-video'])?$args['background-video']:'';?>"/>
                            <span class="bld-tip">(YouTube video URL or ID)</span>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Background Repeat</label>
                        <div class="bld-inputs">
                            <select class="bld-editbox-background-repeat bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][background-repeat]">
                                <?php 
                                foreach(array(
                                    'repeat',
                                    'repeat-x',
                                    'repeat-y',
                                    'no-repeat'
                                ) as $s) {
                                    echo '<option value="'.$s.'" '.((!isset($args['background-repeat']) && $s=='repeat') || (isset($args['background-repeat']) && $args['background-repeat']==$s) ? 'selected' : '').'>'.$s.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Background Position</label>
                        <div class="bld-inputs">
                            <select class="bld-editbox-background-position bld-editbox-live-change" name="snp_bld[<?php echo $RAND ;?>][background-position]">
                                <?php 
                                foreach(array(
                                    'left top',
                                    'left center',
                                    'left bottom',
                                    'right top',
                                    'right center',
                                    'right bottom',
                                    'center top',
                                    'center center',
                                    'center bottom'
                                ) as $s) {
                                    echo '<option value="'.$s.'" '.((!isset($args['background-position']) && $s=='center center') || (isset($args['background-position']) && $args['background-position']==$s) ? 'selected' : '').'>'.$s.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bld-editbox-tab bld-editbox-advanced">
                    <div class="bld-form-group">
                        <label>Remove form</label>
                        <div class="bld-inputs">
                            <?php
                            if (!isset($args['remove_form'])) {
                                $args['remove_form'] = 0;
                            }
                            ?>
                            <select class="bld-editbox-remove-form bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][remove_form]">
                                <option value="1" <?php echo (isset($args['remove_form']) && $args['remove_form'] == '1' ? 'selected' : ''); ?>>Yes</option>
                                <option value="0" <?php echo (isset($args['remove_form']) && $args['remove_form'] == '0' ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Opacity</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-opacity bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][opacity]" value="<?php echo isset($args['opacity'])?$args['opacity']:'';?>"/>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Z-index</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-z-index bld-editbox-live bld-editbox-small bld-editbox-text-right" name="snp_bld[<?php echo $RAND ;?>][z-index]" value="<?php echo isset($args['z-index'])?$args['z-index']:'';?>"/>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>CSS Class</label>
                        <div class="bld-inputs">
                            <input type="text" class="bld-editbox-css-class bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][css-class]" value="<?php echo isset($args['css-class'])?$args['css-class']:'';?>"/>
                        </div>
                    </div>
                    <div class="bld-form-group">
                        <label>Custom CSS</label>
                        <div class="bld-inputs">
                            <textarea class="bld-editbox-custom-css bld-editbox-live" name="snp_bld[<?php echo $RAND ;?>][custom-css]"><?php echo isset($args['custom-css'])?$args['custom-css']:'';?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            
            $css = '';
            if (!empty($args['border-style'])) {
                $css .= 'border-style: '.$args['border-style'].';';
            }
            
            if (!empty($args['border-width'])) {
                $css .= 'border-width: '.$args['border-width'].'px;';
            }

            if (!empty($args['border-color'])) {
                $css .= 'border-color: '.$args['border-color'].';';
            }
            
            if (!empty($args['border-radius'])) {
                $css .= 'border-radius: '.$args['border-radius'].'px;';
            }

            if (!empty($args['background-color'])) {
                $css .= 'background-color: '.$args['background-color'].';';
            }
            
            if (!empty($args['background-image'])) {
                $css .= 'background-image: url(\' '.$args['background-image'].'\');';
            }
            
            if (!empty($args['background-position'])) {
                $css .= 'background-position: '.$args['background-position'].';';
            }
            
            if (!empty($args['background-repeat'])) {
                $css .= 'background-repeat: '.$args['background-repeat'].';';
            }
            
            if (isset($args['opacity']) && $args['opacity']!='') {
                $css .= 'opacity: '.$args['opacity'].';';
            }
            
            if (isset($args['custom-css'])) {
                $css .= $args['custom-css'];
            }
            
            $css_class = '';
            $data = '';
            
            if (isset($args['css-class'])) {
                $css_class.=' '.$args['css-class'];
            }
            
            if (isset($args['animation'])) {
                $css_class.=' animated '.$args['animation'];
                
                $data .= 'data-animation="'.$args['animation'].'"';
            }
            ?>
		    <div class="builder-popup snp-builder <?php echo $css_class;?>" <?php echo $data;?> style="<?php echo ($css ? $css.';' :'') ?>width: <?php echo (int)$args['width'];?>px;height: <?php echo (int)$args['height'];?>px;">
                <?php
                if (is_array($elements)) {
                    foreach($elements as $i => $el_args) {
                        $this->element_tpl($el_args['type'], $el_args);

                        if (isset($el_args['font'])) {
                            $this->fonts[$el_args['font']]=$el_args['font'];
                        }
                    }
                }
                ?>
		    </div>
		</div>
	    <?php
	}
}
