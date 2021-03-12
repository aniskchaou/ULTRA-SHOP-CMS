jQuery(function(){
    jQuery.fn.hasAttr = function(attribute){
        var attr = this.attr(attribute);
        if (typeof attr !== typeof undefined && attr !== false)
            return true;
        return false;
    }

    jQuery.fn.materialForm = function() {
        // Inputs
        this.find('input, textarea').each(function(i){
            if(isValidType(jQuery(this))){
                var name = jQuery(this).attr('name');
                jQuery(this).attr('id', name);
                var $wrap = jQuery(this).wrap("<div class='material-input'></div>").parent();
                $wrap.append("<span class='material-bar'></span>");

                var tagName = jQuery(this).prop("tagName").toLowerCase();
                $wrap.addClass(tagName);

                var placeholder = jQuery(this).attr('placeholder');
                if(placeholder){
                    $wrap.append("<label for='"+name+"'>"+placeholder+"</label>");
                    jQuery(this).removeAttr('placeholder');
                }

                addFilled(jQuery(this));
            }

            if(isType(jQuery(this), 'radio') || isType(jQuery(this), 'checkbox')){

                var name = jQuery(this).attr('name').replace('[]','');
                var group_id = 'material-group-' + name;
                var placeholder = jQuery(this).attr('placeholder');
                var item_id = name+ '-' + i;
                var $label = jQuery('<label for="'+item_id+'">'+placeholder+'</label>');
                var $group_item = jQuery('<div class="material-group-item"></div>');
                jQuery(this).attr('id', item_id);

                if(jQuery("#"+group_id).length){
                    var $group = jQuery('#'+group_id);
                    $group.append(jQuery(this));
                }
                else{
                    var $group = jQuery(this).wrap("<div class='material-group' id='"+group_id+"'></div>").parent();
                }

                if(isType(jQuery(this), 'radio'))
                    var $radio = jQuery('<div class="material-radio"></div>');
                else
                    var $radio = jQuery('<div class="material-checkbox"></div>');

                $group_item.append(jQuery(this));
                $group_item.append($label);
                $group_item.append($radio);

                $group.append($group_item);
            }
        });

        this.find('input, textarea').on('blur', function(){
            if(isValidType(jQuery(this)))
                addFilled(jQuery(this))
        });

        // Radio

        function isValidType($el){
            var type = $el.attr('type');
            return (type != 'hidden' && type != 'submit' && type != 'checkbox' && type != 'radio' && type != 'file' ? 1 : 0);
        }

        function isType($el, type){
            var el_type = $el.attr('type');
            return (el_type == type);
        }

        function addFilled($el){
            if($el.val())
                $el.addClass('filled');
            else
                $el.removeClass('filled');
        }

        // Selects
        this.find('select').each(function(i){
            var placeholder = jQuery(this).attr('placeholder');
            var type = (jQuery(this).attr('multiple') ? 'checkbox' : 'radio');
            var name = id = jQuery(this).attr('name');
            var $wrap = jQuery(this).wrap("<div class='material-select'></div>").parent();
            if(type == 'checkbox'){
                name += '[]';
                var $bar = jQuery('<span class="material-bar"></span>');
                $wrap.append($bar).addClass('checkbox');
            }
            else{
                var $title = jQuery('<span class="material-title">'+placeholder+'</span>');
                $wrap.prepend($title);
            }

            var $label = jQuery('<label for="select-'+i+'"><span>'+placeholder+'</span><strong></strong></label>');
            var $checkbox = jQuery('<input type="checkbox" id="select-'+i+'">');

            $wrap.prepend($checkbox);
            $wrap.prepend($label);

            var $list = jQuery('<ul class="'+type+'"></ul>');
            $wrap.append($list);

            var selected_length = 0;
            var length = jQuery(this).children('option').length;
            var $selected;
            jQuery(this).children('option').each(function(j){
                var title = jQuery(this).text();
                var value = jQuery(this).val();

                var selected = jQuery(this).hasAttr('selected');

                var $list_item = jQuery('<li></li>');
                $list.append($list_item);

                var $label = jQuery('<label for="'+id+'-'+j+'">'+title+'</label>');
                var $input = jQuery('<input type="'+type+'" value="'+value+'" name="'+name+'" id="'+id+'-'+j+'">');
                if(selected){
                    $selected = $input.prop('checked', true);
                    selected_length++;
                }

                $list_item.append($input);
                $list_item.append($label);
            });
            if($bar){
                var percentage = selected_length / length * 100;
                $bar.width(percentage + '%');
            } else{
                if(selected_length){
                    label.children('span').text($selected.next('label').text());
                    $wrap.addClass('filled');
                }
            }
            jQuery(this).remove();
        });

        jQuery(document).on('click', function(e) {
            if (jQuery(e.target).closest('.material-select').length === 0) {
                // cancel highlighting
                jQuery('.material-select > input').prop('checked', false);
            }
        });

        jQuery('.material-select > input').on('change', function(){
            var changed_id = jQuery(this).attr('id');
            jQuery('.material-select > input').each(function(){
                var this_id = jQuery(this).attr('id');
                if(changed_id != this_id)
                    jQuery(this).prop('checked', false);
            });
        });

        jQuery('.material-select ul input').on('change', function(){
            if(jQuery(this).closest('.material-select.checkbox').length){
                var $ul = jQuery(this).closest('ul')
                var length = $ul.find('input').length;
                var checked_length = $ul.find('input:checked').length;
                var percentage = checked_length / length * 100;
                jQuery(this).closest('.material-select').find('.material-bar').width(percentage + '%');
            }
            else{
                var $material_select = jQuery(this).closest('.material-select')
                var $label = $material_select.children('label').children('span');
                var $next = jQuery(this).next('label');
                $label.text($next.text());
                $material_select.children('input').prop('checked', false);
                $material_select.addClass('filled');
            }
        });

    };
});

jQuery(document).ready(function() {
    jQuery('form.material').materialForm();
});