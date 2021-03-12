<?php

/**
 * Class SNP_NHP_Options_custom_fields
 */
class SNP_NHP_Options_custom_fields extends SNP_NHP_Options
{
    /**
     * SNP_NHP_Options_custom_fields constructor.
     * @param array $field
     * @param string $value
     * @param $parent
     */
    public function __construct($field = array(), $value = '', $parent)
    {
        parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);

        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @param $atts
     */
    public function field_tpl($atts)
    {
        if (is_array($atts))  {
            $RAND = uniqid().rand(100,999);
        } else if($atts == 'tpl') {
            $RAND = 'RAND';
        }

        if (is_array($atts) && isset($atts['type']) && $atts['type'] == 'email') {
            ?>
            <div class="snp-cf-field snp-cf-field-email">
                <input type="hidden" class="snp-rand" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][fields][]" value="email" />
                <input type="hidden" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][email][type]" value="email" />
                <div class="snp-cf-label">
                    E-mail
                </div>
                <div class="snp-cf-preview">
                    <input type="text" />
                </div>
                <div class="snp-cf-options">
                    <a class="snp-cf-move">Move</a>
                </div>
            </div>
            <?php
        } else if (is_array($atts) && isset($atts['type']) && $atts['type'] == 'name') {
            ?>
            <div class="snp-cf-field snp-cf-field-name">
                <input type="hidden" class="snp-rand" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][fields][]" value="name" />
                <input type="hidden" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][name][type]" value="name" />
                <div class="snp-cf-label">
                    Name
                </div>
                <div class="snp-cf-preview">
                    <input type="text" />
                </div>
                <div class="snp-cf-options">
                    <a class="snp-cf-move">Move</a>
                </div>
            </div>
            <?php
        } else {
            if ($atts == 'tpl') {
                $atts = array();
                $atts['label'] = 'New field';
                $atts['placeholder'] = '...';
                $atts['type'] = 'Text';
                $atts['name'] = 'fieldRAND';
                $atts['required'] = 'No';
                $atts['icon'] = '';
                $tpl = 1;
            }
            ?>
            <div class="snp-cf-field <?php if(isset($tpl) && $tpl==1) {echo 'snp-cf-field-tpl';} ?>">
                <div class="snp-cf-label"><?php echo $atts['label']; ?></div>
                <div class="snp-cf-preview">
                    <div class="snp-cf-preview-Text" <?php if($atts['type']=='Text') {echo 'style="display: block;"';} ?>>
                        <input disabled type="text" />
                    </div>
                    <div class="snp-cf-preview-Textarea" <?php if($atts['type']=='Textarea') {echo 'style="display: block;"';} ?>>
                        <textarea disabled></textarea>
                    </div>
                    <div class="snp-cf-preview-DropDown" <?php if($atts['type']=='DropDown') {echo 'style="display: block;"';} ?>>
                        <select disabled></select>
                    </div>
                    <div class="snp-cf-preview-Checkbox" <?php if($atts['type']=='Checkbox') {echo 'style="display: block;"';} ?>>
                        <input disabled type="checkbox" />
                    </div>
                </div>
                <?php
                /*
                 * Label: <span class="snp-label"><?php echo $atts['label']; ?></span><br />
                 * Placeholder: <span class="snp-placeholder"><?php echo $atts['placeholder']; ?></span><br />
                 * Name: <span class="snp-name"><?php echo $atts['name']; ?></span><br />
                 * Type: <span class="snp-type"><?php echo $atts['type']; ?></span><br />
                 * Required: <span class="snp-required"><?php echo $atts['required']; ?></span><br />
                 */
                ?>
                <div class="snp-cf-options">
                    <span class="snp-cf-move">Move</span>
                    <a class="snp-cf-delete">Delete</a>
                    <a class="snp-cf-edit">Settings</a>
                </div>
                <input type="hidden" class="snp-rand" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][fields][]" value="<?php echo $RAND; ?>" />
                <div class="snp-editbox">
                    <p>
                        <label>Label:</label>
                        <input class="snp-input-label" type="text" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][label]" value="<?php echo $atts['label']; ?>" />
                    </p>
                    <p>
                        <label class="snp-input-placeholder-label"><?php if($atts['type']=='Hidden') {echo 'Value:';} else { echo 'Placeholder:';}?></label>
                        <input class="snp-input-placeholder" type="text" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][placeholder]" value="<?php echo $atts['placeholder']; ?>" />
                    </p>
                    <p>
                        <label>Name:</label>
                        <input class="snp-input-name" type="text" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][name]" value="<?php echo $atts['name']; ?>" />
                    </p>
                    <p>
                        <label>Type:</label>
                        <select class="snp-input-type" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][type]">
                            <option <?php if($atts['type']=='Text') {echo 'selected';} ?> value="Text">Text</option>
                            <option <?php if($atts['type']=='Textarea') {echo 'selected';} ?> value="Textarea">Textarea</option>
                            <option <?php if($atts['type']=='DropDown') {echo 'selected';} ?> value="DropDown">DropDown</option>
                            <option <?php if($atts['type']=='Hidden') {echo 'selected';} ?> value="Hidden">Hidden</option>
                            <option <?php if($atts['type']=='Calendar') {echo 'selected';} ?> value="Calendar">Calendar</option>
                            <option <?php if($atts['type']=='File') {echo 'selected';} ?> value="File">File</option>
                            <option <?php if($atts['type']=='Checkbox') {echo 'selected';} ?> value="Checkbox">Checkbox</option>
                        </select>
                    </p>
                    <div class="snp-input-options" <?php if($atts['type']=='DropDown') {echo 'style="display: block;"';} else { echo 'style="display: none;"';}?>>
                        <label>Options:</label>
                        <div>
                            <?php
                            if (!empty($atts['options']) && count($atts['options'])) {
                                foreach($atts['options'] as $option) {
                                    ?>
                                    <div class="opt-input">
                                        <input type="text" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][options][]" value="<?php echo $option; ?>"/>
                                        <a class="snp-cf-opt-delete">Delete</a>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="opt-input">
                                    <input type="text" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][options][]"/>
                                    <a class="snp-cf-opt-delete">Delete</a>
                                </div>
                                <?php
                            }
                            ?>
                            <input class="snp-cf-add-option button button-large" type="button" value="Add">
                        </div>
                    </div>
                    <?php
                    if (isset($this->field['icons']) && $this->field['icons']) { ?>
                        <div class="snp-input-icons">
                            <label>Icon:</label>
                            <div>
                                <?php
                                foreach($this->field['icons'] as $k => $v) {
                                    echo '<span><input type="radio" name="'.$this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'].']['.$RAND.'][icon]" value="'.$k.'" '.checked($atts['icon'], $k, false).'/>';
                                    echo '<img src="'.$v.'"/></span>';

                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <p>
                        <label>Required:</label>
                        <input class="snp-input-required" <?php if($atts['required']=='Yes') {echo 'checked';} ?> type="radio" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][required]" value="Yes" /> Yes
                        <input class="snp-input-required" <?php if($atts['required']=='No') {echo 'checked';} ?> type="radio" name="<?php echo $this->args['opt_name'].''.$this->field['vcb'].'['.$this->field['id'];?>][<?php echo $RAND; ?>][required]" value="No" /> No
                    </p>
                </div>
            </div>
            <?php
        }
    }
    function render()
    {
        $id = $this->field['id'];
        ?>
        <div class="snp-cf" id="<?php echo $id; ?>">
            <?php $this->field_tpl('tpl'); ?>
            <div class="snp-cf-fields">
                <?php
                if (is_array($this->value)) {
                    foreach ($this->value as $field) {
                        if (isset($field['type'])) {
                            $this->field_tpl($field);
                        }
                    }
                } else {
                    $this->field_tpl(array('type' => 'email'));
                    $this->field_tpl(array('type' => 'name'));
                }
                ?>
            </div>
            <input class="snp-cf-add button button-primary button-large" type="button" value="Add field">
        </div>
        <script>
            jQuery(document).ready(function ($) {
                var cf = $('#<?php echo $id; ?>');
                if ($('input[name="snp[name_disable]"]').size() == 0) {
                    $('.snp-cf-field-name').hide();
                } else {
                    if ($('input[name="snp[name_disable]"]:checked').val() == 1) {
                        $('.snp-cf-field-name').hide();
                    }

                    $('input[name="snp[name_disable]"]').click(function () {
                        if ($(this).val() == 1) {
                            $('.snp-cf-field-name').hide();
                        } else {
                            $('.snp-cf-field-name').show();
                        }
                    });
                    if ($('input[name="snp[name_label]"]').size() != 0) {
                        var name_sel = 'input[name="snp[name_label]"]';
                    } else {
                        var name_sel = 'input[name="snp[name_placeholder]"]';
                    }
                    $(name_sel).keyup(function () {
                        $('.snp-cf-field-name').find('.snp-cf-label').text($(this).val());
                    }).keyup();
                }

                if ($('input[name="snp[email_label]"]').size() != 0) {
                    var email_sel = 'input[name="snp[email_label]"]';
                } else {
                    var email_sel = 'input[name="snp[email_placeholder]"]';
                }

                $(email_sel).keyup(function () {
                    $('.snp-cf-field-email').find('.snp-cf-label').text($(this).val());
                }).keyup();

                cf.find('.snp-cf-add').click(function () {
                    var new_item = cf.find('.snp-cf-field-tpl').clone(true).removeClass('snp-cf-field-tpl');
                    if (new_item) {
                        var RAND = Math.floor(Math.random() * 100000000);
                        new_item.find('.snp-rand').val(RAND);
                        new_item.find('.snp-input-name').val(new_item.find('.snp-input-name').val().replace('RAND', Math.floor(Math.random() * 1000)));
                        new_item.find('input, select, textarea').each(function (index) {
                            if ($(this).attr('name')) {
                                $(this).attr('name', $(this).attr('name').replace('RAND', RAND));
                            }
                        });
                        new_item.find('.snp-placeholder').text(new_item.find('.snp-input-placeholder').val());
                        new_item.find('.snp-label').text(new_item.find('.snp-input-label').val());
                        new_item.find('.snp-cf-label').html(new_item.find('.snp-input-label').val());
                        new_item.find('.snp-type').text(new_item.find('.snp-input-type option:selected').val());
                        new_item.find('.snp-required').text(new_item.find('.snp-input-required:checked').val());
                        new_item.find('.snp-name').text(new_item.find('.snp-input-name').val());
                        cf.find('.snp-cf-fields').append(new_item);
                    }
                });

                cf.find('.snp-cf-delete').click(function () {
                    var item = $(this).parents('.snp-cf-field');
                    var div_dialog = $("<div>");
                    div_dialog.attr('title', 'Confirmation');
                    div_dialog.css('width', '400px');
                    div_dialog.append('Are you sure you want to delete this field?');
                    div_dialog.dialog({
                        resizable: false,
                        height: 200,
                        draggable: false,
                        buttons: {
                            "Delete": function () {
                                $(this).dialog("close");
                                item.fadeOut(function () {
                                    item.remove();
                                });
                            },
                            Cancel: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                });

                cf.find('.opt-input').on('click', '.snp-cf-opt-delete', function () {
                    $(this).parents('.opt-input').remove();
                });

                cf.find('.snp-input-type').change(function () {
                    var editbox = $(this).parents('.snp-editbox');
                    if ($(this).val() == 'DropDown') {
                        editbox.find('.snp-input-options').show();
                    } else {
                        editbox.find('.snp-input-options').hide();
                    }
                    if ($(this).val() == 'Hidden') {
                        editbox.find('.snp-input-placeholder-label').text('Value:');
                    } else {
                        editbox.find('.snp-input-placeholder-label').text('Placeholder:');
                    }
                });

                cf.find('.snp-cf-add-option').click(function () {
                    var new_opt = $(this).prev('.opt-input').clone(true);
                    new_opt.find('input').val('');
                    $(this).before(new_opt);
                });

                cf.find('.snp-cf-edit').click(function () {
                    var item = $(this).parents('.snp-cf-field');
                    var editbox = item.find('.snp-editbox');
                    editbox.bind('mousedown', function (e) {
                        e.stopPropagation();
                    });

                    editbox.dialog({
                        height: "auto",
                        width: 750,
                        draggable: false,
                        title: 'Settings',
                        close: function (event, ui) {
                            $(this).dialog("destroy");
                        },
                        buttons: {
                            "Done": function () {
                                item.find('.snp-placeholder').text(editbox.find('.snp-input-placeholder').val());
                                item.find('.snp-label').text(editbox.find('.snp-input-label').val());
                                item.find('.snp-cf-label').text(editbox.find('.snp-input-label').val());
                                item.find('.snp-type').text(editbox.find('.snp-input-type option:selected').val());
                                item.find('.snp-required').text(editbox.find('.snp-input-required:checked').val());
                                item.find('.snp-cf-preview > div').hide();
                                item.find('.snp-cf-preview-' + editbox.find('.snp-input-type option:selected').val()).show();
                                $(this).dialog("destroy");
                            }
                        }
                    });
                });
                cf.find(".snp-cf-fields").sortable({items: '.snp-cf-field', handle: ".snp-cf-move"});
                cf.find(".snp-cf-fields").disableSelection();
            });
        </script>
        <?php
    }

    public function enqueue()
    {
        global $wp_scripts;

        $ui = $wp_scripts->query('jquery-ui-core');

        // tell WordPress to load the Smoothness theme from Google CDN

        $protocol = is_ssl() ? 'https' : 'http';
        $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css";

        wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
    }
}