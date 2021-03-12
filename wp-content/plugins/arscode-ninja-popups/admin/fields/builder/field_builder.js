jQuery(function($) {	
    //var edited_el = null;
    //var editbox = null;
    var mce_opts = {
        selector: ".builder-popup > .bld-el-text > .bld-el",
        fontsize_formats: "6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 31px 32px 36px 38px 40px 42px 44px 46px 48px 50px 52px 54px 56px 58px 60px 62px 64px 66px 68px 70px 72px 74px 76px 78px 80px 82px 84px 86px 88px 90px 92px 94px 96px 98px 100px 102px 104px 106px 108px 110px 112px 114px 116px 118px 120px 130px 140px 150px 160px 170px 180px 190px 200px 210px 220px 230px 240px 250px 260px",
        inline: true,
        theme: "modern",
        schema: "html5",
        resize: false,
        width: 500,
        menubar: false,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        plugins: [
            "colorpicker,hr,lists,paste,textcolor,wordpress,wplink"
        ],
        toolbar: [
            "bold italic strikethrough underline forecolor fontsizeselect",
            "bullist numlist outdent indent alignleft aligncenter alignright alignjustify link unlink pastetext removeformat"
        ],
        setup : function(ed) {
                ed.onChange.add(function(ed) {
                    jQuery('#'+ed.id).parents('.bld-el-cont').find('.bld-el-content').val(ed.getContent());
                });
         }
    };
    var colorpicker_opts = {
        change:		function( event, ui ) {
            $(this).trigger( 'keyup' );
        }
    };
    var step_droppable_opts = {
        accept: ".bld-el-cont-tpl",
        scope:  "bld-el",
        toleranceType: 'touch',
        drop: function( event, ui ) {
            var RAND = Math.floor(Math.random()*100000000);
            var parent_pos = $(this).offset();
            var bld_section_pos = $('#builder-container').offset();
            var border=parseInt($(this).css('border-width'));   
            if(isNaN(border))
            {
                border = 0;
            }
            var new_el = $(ui.helper).clone();
            new_el.append($('#builder-tpl-editboxes').children('.bld-el-editbox-'+new_el.find('.bld-el-type').val()).clone());
            new_el.children('input.editbox-args').each(function(index) {
                if($(this).attr('name'))
                {
                    if(new_el.children('.bld-el-editbox').find('.bld-editbox-'+$(this).attr('name')).is(':checkbox'))
                    {
                        if($( this ).val()==1)
                        {
                            new_el.children('.bld-el-editbox').find('.bld-editbox-'+$(this).attr('name')+'[value='+$( this ).val()+']').attr("checked", true);
                        }
                    }
                    else if(new_el.children('.bld-el-editbox').find('.bld-editbox-'+$(this).attr('name')).is(':radio'))
                    {
                       new_el.children('.bld-el-editbox').find('.bld-editbox-'+$(this).attr('name')+'[value="'+$( this ).val()+'"]').attr("checked", true);
                    }
                    else
                    {
                        new_el.children('.bld-el-editbox').find('.bld-editbox-'+$(this).attr('name')).val($( this ).val());
                    }
                }
            });
            new_el.children('input.editbox-args').remove();
            new_el.find('.bld-el-rand').val(RAND).attr('disabled',false);
            new_el.find('.bld-el-editbox').attr('data-id','element-'+RAND).attr('id','editbox-element-'+RAND);
            new_el.attr('id','element-'+RAND);
            new_el.find('input, select, textarea').each(function(index) {
                if($(this).attr('name'))
                {
                    $(this).attr('name',$(this).attr('name').replace('RAND',RAND));
                    $(this).attr('disabled',false);
                }
            });
            new_el.find('.bld-mc-select-name').attr('disabled',false);
            new_el.find('input, img, div, select, textarea, table, span, a').each(function(index) {
                if($(this).attr('id'))
                {
                    $(this).attr('id',$(this).attr('id').replace('RAND',RAND));
                }
            });
            new_el.find('a').each(function(index) {
                if($(this).attr('href').indexOf('RAND'))
                {
                    $(this).attr('href',$(this).attr('href').replace('RAND',RAND));
                }
            });
            
            new_el.css('top', '-='+(parent_pos.top-bld_section_pos.top+border)+'px');
            new_el.css('left', '-='+(parent_pos.left-bld_section_pos.left+border)+'px');
            new_el.css('z-index', 100);
            new_el.find('.bld-editbox-z-index').val(100);
            new_el.removeClass('bld-el-cont-tpl snp-builder hover ui-draggable ui-draggable-dragging');
            new_el.find('.bld-run-colorpicker').wpColorPicker(colorpicker_opts);
            $(this).append(new_el);
            if(new_el.hasClass('bld-el-text'))
            {
              //mce_opts.selector = "#element-"+RAND+" > .bld-el";
              //tinymce.EditorManager.createEditor(90, mce_opts);
              tinymce.init(mce_opts);
            }
            bld_dd_update_el(new_el);
        }
    };
    $( '.bld-width').on({
        keyup: function() {
            if(parseInt($(this).val()))
            {
                $(this).parents('.builder-step').find('.bld-step-editbox').find('.bld-editbox-width').val(parseInt($(this).val()));
                $(this).parents('.builder-step').find('.builder-popup').width(parseInt($(this).val()));
            }
        }
    });
    $( '.bld-height').on({
        keyup: function() {
            if(parseInt($(this).val()))
            {
                $(this).parents('.builder-step').find('.bld-step-editbox').find('.bld-editbox-height').val(parseInt($(this).val()));
                $(this).parents('.builder-step').find('.builder-popup').height(parseInt($(this).val()));
            }
        }
    });
    $('#builder-tpl').on('mouseenter', '.bld-el-cont', function() {
        if(!$(this).hasClass('ui-draggable'))
        {
            $(this).draggable({ 
                helper: "clone",
                distance: 0,
                cursor: "pointer",
                scope:  "bld-el",
                connectWith: '.builder-popup',
                appendTo: "#builder-container",
                containment: "#snp-cf-bld",
                start: function( event, ui ) { 
                    ui.helper.addClass('snp-builder');
                    $(this).draggable("option", "cursorAt", {
                        left: Math.floor(ui.helper.width() / 2),
                        top: Math.floor(ui.helper.height() / 2)
                    }); 
                    $('.builder-tpl-lib').hide(); 
                }
            });
        }
    });
    $('#builder-container').find( ".builder-popup" ).droppable(step_droppable_opts);
    $('.builder-popup').on('mouseenter', '.bld-el-cont', 
        function() {
            //$(this).attr('data-zindex', $(this).css('z-index'));
            //$(this).css('z-index', 9999999);
            if(!$(this).hasClass('ui-draggable'))
            {
                $(this).draggable({ 
                    handle: ".bld-el-handle",
                    containment: "#snp-cf-bld",
                    start: function( event, ui ) { ui.helper.addClass('hover'); },
                    stop: function( event, ui ) { 
                        ui.helper.removeClass('hover'); 
                        bld_dd_update_el(ui.helper);
                    },
                    drag: function( event, ui ) {

                    }
                });
            }
            if(!$(this).hasClass('ui-resizable'))
            {
                var aspectRatio = false;
                if($(this).hasClass('bld-el-img'))
                {
                    aspectRatio = true;
                }
                $(this).resizable({
                    handles: 'all',
                    autoHide: true,
                    aspectRatio: aspectRatio,
                    start: function( event, ui ) { ui.helper.addClass('hover'); },
                    stop: function( event, ui ) { 
                        ui.helper.removeClass('hover'); 
                        bld_dd_update_el(ui.helper);
                    },
                    resize: function( event, ui ) { 
                        $('#'+ui.helper.attr('id')).find('.bld-table-cont').find('.bld-el').css('height',ui.helper.height());
                    }
                });
                $(this).find('.ui-resizable-handle').show();
            }
        }
    );
    $('.builder-popup').on('mouseleave', '.bld-el-cont', 
        function() {
            //$(this).css('z-index', $(this).attr('data-zindex'));
        }
    );
    $( '.builder-del-step').on({
        click : function() {
            if (confirm('Are you sure?')) 
            {
                $(this).parents('.builder-step').remove();
            }
            return false;
        }
    });
    $( '.builder-step-copy').on({
        click : function() {
            var edited_el = $(this).parents('.builder-step');
            
            return false;
        }
    });
    $( '.builder-step-settings').on({
        click : function() {
            var edited_el = $(this).parents('.builder-step');
            var editbox = edited_el.find('.bld-step-editbox');
            $('#publish').hide();
            editbox.dialog({ 
                width: 500, 
                height: 500,
                dialogClass: "bld-dialog",
                title: 'Step Settings',
                position: { my: "left top", at: "right top", of: edited_el },
                close: function( event, ui ) {
                    $( this ).dialog( "destroy" );
                    $('#publish').show();
                     bld_save_update_step($( this ).data('id'));
                },
                buttons: {
                    "Done": function() {
                        $( this ).dialog( "destroy" );
                        $('#publish').show();
                        bld_save_update_step($( this ).data('id'));
                    }
                }
            });
            return false;
        }
    });
    $( '.builder-step-toggle').on({
        click : function() {
            var edited_el = $(this).parents('.builder-step');
            var toggle_button = $(this);
            edited_el.find('.builder-popup').slideToggle(0, function() {
                if ($(this).is(':visible')) 
                {
                    edited_el.removeClass('bld-step-closed').addClass('bld-step-open');
                    toggle_button.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                } 
                else 
                {
                    edited_el.removeClass('bld-step-open').addClass('bld-step-closed'); 
                    toggle_button.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }        
            });
            return false;
        }
    });
    $( '.builder-grid-toggle').on({
        click : function() {
            $('#builder-container').toggleClass('bld-enable-grid');
            return false;
        }
    });
    $( '.builder-animations-test').on({
        click : function() {
            var edited_el = $(this).parents('.builder-step');
            if(edited_el.find('.builder-popup').attr('data-animation')!='')
            {
                edited_el.find('.builder-popup').removeClass(edited_el.find('.builder-popup').attr('data-animation')).animate({'nothing':null}, 1, function () {
                    edited_el.find('.builder-popup').addClass( edited_el.find('.builder-popup').attr('data-animation')).addClass("animated");
                });
            }
            edited_el.find(".bld-el-cont").each(function() {
                if($( this ).attr('data-animation')!='')
                {
                    $( this ).removeClass($( this ).attr('data-animation')).animate({'nothing':null}, 1, function () {
                        $( this ).addClass( $( this ).attr('data-animation')).addClass( "animated");
                    });
                }
            }); 
            
            return false;
        }
    });
    $('#builder-container').on('click', '.bld-el-del', function() 
    {
        if (confirm('Are you sure?')) 
        {
            $(this).parents('.bld-el-cont').remove();
        }
        return false;
    });
    function bld_parseCSSBlock(css) { 
        var rule = {};
        var declarations = css.split(';');
        declarations.pop();
        var len = declarations.length;
        for (var i = 0; i < len; i++)
        {
            var loc = declarations[i].indexOf(':');
            var property = $.trim(declarations[i].substring(0, loc));
            var value = $.trim(declarations[i].substring(loc + 1));

            if (property != "" && value != "")
                rule[property] = value;
        }
        return rule;
    }
    function bld_save_update_step(id)
    {
        //$('#'+id).find('.builder-popup').attr('style', $('#editbox-'+id).find('.bld-editbox-custom-css').val());
        
        $('#'+id).find('.builder-popup').css('width',parseInt($('#editbox-'+id).find('.bld-editbox-width').val()));
        $('#'+id).find('.builder-popup').css('height',parseInt($('#editbox-'+id).find('.bld-editbox-height').val()));       
        $('#'+id).find('.builder-step-opts').find('.bld-width').val(parseInt($('#editbox-'+id).find('.bld-editbox-width').val()));
        $('#'+id).find('.builder-step-opts').find('.bld-height').val(parseInt($('#editbox-'+id).find('.bld-editbox-height').val()));
        
        if($('#editbox-'+id).find('.bld-editbox-z-index').val()!='')
        {
            $('#'+id).find('.builder-popup').css('z-index', parseInt($('#editbox-'+id).find('.bld-editbox-z-index').val()));
        }
        if($('#editbox-'+id).find('.bld-editbox-opacity').val()!='')
        {
            $('#'+id).find('.builder-popup').css('opacity', $('#editbox-'+id).find('.bld-editbox-opacity').val());
        }
        // border
        if($('#editbox-'+id).find('.bld-editbox-border-style').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-border-style').val()!='')
            {
                $('#'+id).find('.builder-popup').css('border-style', $('#editbox-'+id).find('.bld-editbox-border-style').val());
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-border-width').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-border-width').val()!='')
            {
                $('#'+id).find('.builder-popup').css('border-width', $('#editbox-'+id).find('.bld-editbox-border-width').val());
            }
            else
            {
                //$('#'+id).find('.builder-popup').css('border-width', '0');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-border-color').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-border-color').val()!='')
            {
                $('#'+id).find('.builder-popup').css('border-color', $('#editbox-'+id).find('.bld-editbox-border-color').val());
            }
            else
            {
                //$('#'+id).find('.builder-popup').css('border-color', null);
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-border-radius').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-border-radius').val())
            {
                $('#'+id).find('.builder-popup').css('border-radius', $('#editbox-'+id).find('.bld-editbox-border-radius').val()+'px');
            }
            else
            {
                 $('#'+id).find('.builder-popup').css('border-radius', '0');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-background-image').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-background-image').val())
            {
                $('#'+id).find('.builder-popup').css('background-image', 'url(\'' + $('#editbox-'+id).find('.bld-editbox-background-image').val() + '\')');
            }
            else
            {
                 $('#'+id).find('.builder-popup').css('background-image', 'none');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-background-color').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-background-color').val())
            {
                $('#'+id).find('.builder-popup').css('background-color', $('#editbox-'+id).find('.bld-editbox-background-color').val());
            }
            else
            {
                 $('#'+id).find('.builder-popup').css('background-color', '');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-background-repeat').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-background-repeat').val()!='')
            {
                $('#'+id).find('.builder-popup').css('background-repeat', $('#editbox-'+id).find('.bld-editbox-background-repeat').val());
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-background-position').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-background-position').val()!='')
            {
                $('#'+id).find('.builder-popup').css('background-position', $('#editbox-'+id).find('.bld-editbox-background-position').val());
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-animation').length>0)
        {
            var animation = $('#editbox-'+id).find('.bld-editbox-animation').find('option:selected').val();
            
            if(!animation || animation=='')
            {
                $('#'+id).find('.builder-popup').removeClass('animated');
                $('#'+id).find('.builder-popup').removeClass($('#'+id).find('.builder-popup').attr('data-animation'));
            }
            else if($('#'+id).find('.builder-popup').attr('data-animation')!=animation)
            {
                $('#'+id).find('.builder-popup').removeClass($('#'+id).find('.builder-popup').attr('data-animation'));
                $('#'+id).find('.builder-popup').attr('data-animation',animation);
                $('#'+id).find('.builder-popup').addClass(animation).addClass('animated');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-custom-css').val()!='')
        {
            $('#'+id).find('.builder-popup').css(bld_parseCSSBlock($('#editbox-'+id).find('.bld-editbox-custom-css').val()));   
        }
    }
    function bld_dd_update_el(el)
    {
        $('#editbox-'+el.attr('id')).find('.bld-editbox-width').val(el.outerWidth());
        $('#editbox-'+el.attr('id')).find('.bld-editbox-height').val(el.outerHeight());
        $('#editbox-'+el.attr('id')).find('.bld-editbox-top').val(el.position().top);
        $('#editbox-'+el.attr('id')).find('.bld-editbox-left').val(el.position().left);
    }
    function bld_save_update_pointlist(id)
    {
        $('#'+id).find('.bld-el').html('');
        $('#editbox-'+id).find('input.bld-editbox-pointlis-points').each(function() {
            $('#'+id).find('.bld-el').append('<li>'+$( this ).val()+'</li>');
        });
    }
    function bld_save_update_el(id)
    {
        //$('#'+id).find('.bld-el').attr('style', $('#editbox-'+id).find('.bld-editbox-custom-css').val());
        $('#'+id).css('width', parseInt($('#editbox-'+id).find('input.bld-editbox-width').val()));
        $('#'+id).css('height', parseInt($('#editbox-'+id).find('input.bld-editbox-height').val()));
        $('#'+id).find('.bld-table-cont').find('.bld-el').css('height', parseInt($('#editbox-'+id).find('input.bld-editbox-height').val())-2);
        $('#'+id).css('top', parseInt($('#editbox-'+id).find('input.bld-editbox-top').val()));
        $('#'+id).css('left', parseInt($('#editbox-'+id).find('input.bld-editbox-left').val()));
        if($('#editbox-'+id).find('input.bld-editbox-z-index').val()!='')
        {
            $('#'+id).css('z-index', parseInt($('#editbox-'+id).find('input.bld-editbox-z-index').val()));
        }
        if($('#editbox-'+id).find('input.bld-editbox-opacity').val()!='')
        {
            $('#'+id).find('.bld-el').css('opacity', $('#editbox-'+id).find('.bld-editbox-opacity').val());
        }
        if($('#editbox-'+id).find('input.bld-editbox-rotate').val()!='')
        {
            $('#'+id).find('.bld-el').css('transform', 'rotate('+$('#editbox-'+id).find('.bld-editbox-rotate').val()+'deg)');
            $('#'+id).find('.bld-el').css('-webkit-transform', 'rotate('+$('#editbox-'+id).find('.bld-editbox-rotate').val()+'deg)');
            $('#'+id).find('.bld-el').css('-moz-transform', 'rotate('+$('#editbox-'+id).find('.bld-editbox-rotate').val()+'deg)');
            $('#'+id).find('.bld-el').css('-o-transform', 'rotate('+$('#editbox-'+id).find('.bld-editbox-rotate').val()+'deg)');
        }
        if($('#editbox-'+id).find('.bld-editbox-font').length>0)
        {
            $('#'+id).find('.bld-el').css('font-family', $('#editbox-'+id).find('.bld-editbox-font').find('option:selected').val());
        }
        if($('#editbox-'+id).find('.bld-editbox-font-size').length>0)
        {
            $('#'+id).find('.bld-el').css('font-size', $('#editbox-'+id).find('.bld-editbox-font-size').val() + 'px');
        }
        if($('#editbox-'+id).find('input.bld-editbox-color').length>0)
        {
            $('#'+id).find('.bld-el').css('color', $('#editbox-'+id).find('input.bld-editbox-color').val());
        }
        if($('#editbox-'+id).find('input.bld-editbox-bold').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-bold').is(':checked'))
            {
                $('#'+id).find('.bld-el').css('font-weight', 'bold');
            }
            else
            {
               $('#'+id).find('.bld-el').css('font-weight', 'normal'); 
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-italic').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-italic').is(':checked'))
            {
                $('#'+id).find('.bld-el').css('font-style', 'italic');
            }
            else
            {
               $('#'+id).find('.bld-el').css('font-style', 'normal'); 
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-underline').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-underline').is(':checked'))
            {
                $('#'+id).find('.bld-el').css('text-decoration', 'underline');
            }
            else
            {
               $('#'+id).find('.bld-el').css('text-decoration', 'none'); 
            }
        }
        // img
         if($('#editbox-'+id).find('input.bld-editbox-img').length>0)
        {
            $('#'+id).find('.bld-el').attr('src', $('#editbox-'+id).find('input.bld-editbox-img').val());
        }
        // button & inputs
        if($('#editbox-'+id).find('input.bld-editbox-text').length>0)
        {
            $('#'+id).find('.bld-el').val($('#editbox-'+id).find('input.bld-editbox-text').val());
            $('#'+id).find('.bld-el').html($('#editbox-'+id).find('input.bld-editbox-text').val());
        }
        if($('#editbox-'+id).find('input.bld-editbox-placeholder').length>0)
        {
            $('#'+id).find('.bld-el').attr('placeholder',$('#editbox-'+id).find('input.bld-editbox-placeholder').val());
            $('#'+id).find('.bld-el').find('option').text($('#editbox-'+id).find('input.bld-editbox-placeholder').val());
        }
        // border
        if($('#editbox-'+id).find('.bld-editbox-border-style').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-border-style').val()!='')
            {
                $('#'+id).find('.bld-el').css('border-style', $('#editbox-'+id).find('.bld-editbox-border-style').val());
                $('#'+id).find('.bld-input-icon').css('border-style', $('#editbox-'+id).find('.bld-editbox-border-style').val());
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-border-width').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-border-width').val()!='')
            {
                $('#'+id).find('.bld-el').css('border-width', $('#editbox-'+id).find('input.bld-editbox-border-width').val());
                $('#'+id).find('.bld-input-icon').css('border-width', $('#editbox-'+id).find('input.bld-editbox-border-width').val());
            }
            else
            {
                //$('#'+id).find('.bld-el').css('border-width', '0');
            }
        }

        if($('#editbox-'+id).find('input.bld-editbox-border-color').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-border-color').val()!='')
            {
                $('#'+id).find('.bld-el').css('border-color', $('#editbox-'+id).find('input.bld-editbox-border-color').val());
                $('#'+id).find('.bld-input-icon').css('border-color', $('#editbox-'+id).find('input.bld-editbox-border-color').val());
            }
            else
            {
                //$('#'+id).find('.bld-el').css('border-color', null);
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-border-radius').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-border-radius').val())
            {
                $('#'+id).find('.bld-el').css('border-radius', $('#editbox-'+id).find('input.bld-editbox-border-radius').val()+'px');
                $('#'+id).find('.bld-input-icon').css('border-radius', $('#editbox-'+id).find('input.bld-editbox-border-radius').val()+'px');
            }
            else
            {
                 $('#'+id).find('.bld-el').css('border-radius', '0');
                 $('#'+id).find('.bld-input-icon').css('border-radius', '0');
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-background-image').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-background-image').val())
            {
                $('#'+id).find('.bld-el').css('background-image', 'url(\'' + $('#editbox-'+id).find('input.bld-editbox-background-image').val() + '\')');
            }
            else
            {
                 $('#'+id).find('.bld-el').css('background-image', 'none');
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-background-color').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-background-color').val())
            {
                $('#'+id).find('.bld-el').css('background-color', $('#editbox-'+id).find('input.bld-editbox-background-color').val());
            }
            else
            {
                 $('#'+id).find('.bld-el').css('background-color', '');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-background-repeat').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-background-repeat').val()!='')
            {
                $('#'+id).find('.bld-el').css('background-repeat', $('#editbox-'+id).find('.bld-editbox-background-repeat').val());
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-background-position').length>0)
        {
            if($('#editbox-'+id).find('.bld-editbox-background-position').val()!='')
            {
                $('#'+id).find('.bld-el').css('background-position', $('#editbox-'+id).find('.bld-editbox-background-position').val());
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-padding').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-padding').val()!='')
            {
                $('#'+id).find('.bld-el').css('padding', $('#editbox-'+id).find('input.bld-editbox-padding').val());
            }
            else
            {
                $('#'+id).find('.bld-el').css('padding', '0');
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-lineheight').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-lineheight').val()!='')
            {
                $('#'+id).find('.bld-el').css('line-height', $('#editbox-'+id).find('input.bld-editbox-lineheight').val()+'px');
            }
            else
            {
                $('#'+id).find('.bld-el').css('line-height', 'normal');
            }
        }
        // icon
        if($('#editbox-'+id).find('input.bld-editbox-icon').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-icon:checked').val()!='')
            {
                $('#'+id).find('.bld-input-icon > i').removeClass().addClass('fa fa-'+$('#editbox-'+id).find('input.bld-editbox-icon:checked').val());
                $('#'+id).find('.bld-input-icon').show();
                $('#'+id).find('.bld-table').addClass('bld-icon');
            }
            else
            {
                $('#'+id).find('.bld-input-icon').hide();
                $('#'+id).find('.bld-table').removeClass('bld-icon');
            }
            if($('#editbox-'+id).find('input.bld-editbox-icon-right-border').is(':checked'))
            {
                $('#'+id).find('.bld-input-icon').removeClass('bld-input-icon-norborder');
            }
            else
            {
                $('#'+id).find('.bld-input-icon').addClass('bld-input-icon-norborder'); 
            }
            if($('#editbox-'+id).find('.bld-editbox-icon-size').length>0)
            {
                $('#'+id).find('.bld-input-icon').css('font-size', $('#editbox-'+id).find('.bld-editbox-icon-size').val() + 'px');
            }
            if($('#editbox-'+id).find('.bld-editbox-icon-field-width').length>0)
            {
                $('#'+id).find('.bld-input-icon').css('width', $('#editbox-'+id).find('.bld-editbox-icon-field-width').val() + 'px');
            }
            if($('#editbox-'+id).find('input.bld-editbox-icon-color').length>0)
            {
                $('#'+id).find('.bld-input-icon').css('color', $('#editbox-'+id).find('input.bld-editbox-icon-color').val());
            }
            if($('#editbox-'+id).find('input.bld-editbox-icon-bg-color').length>0)
            {
                $('#'+id).find('.bld-input-icon').css('background-color', $('#editbox-'+id).find('input.bld-editbox-icon-bg-color').val());
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-animation').length>0)
        {
            var animation = $('#editbox-'+id).find('.bld-editbox-animation').find('option:selected').val();
            if(!animation || animation=='')
            {
                $('#'+id)./*find('.bld-el').*/removeClass('animated');
                $('#'+id)./*find('.bld-el').*/removeClass($('#'+id)./*find('.bld-el').*/attr('data-animation'));
            }
            else if($('#'+id).find('.bld-el').attr('data-animation')!=animation)
            {
                $('#'+id)./*find('.bld-el').*/removeClass($('#'+id)./*find('.bld-el').*/attr('data-animation'));
                $('#'+id)./*find('.bld-el').*/attr('data-animation',animation);
                $('#'+id)./*find('.bld-el').*/addClass(animation).addClass('animated');
            }
        }
        if($('#editbox-'+id).find('input.bld-editbox-animation-delay').length>0)
        {
            if($('#editbox-'+id).find('input.bld-editbox-animation-delay').val()!='')
            {
                $('#'+id)./*find('.bld-el').*/css('animation-delay', $('#editbox-'+id).find('input.bld-editbox-animation-delay').val() + 'ms');
                $('#'+id)./*find('.bld-el').*/css('-webkit-animation-delay', $('#editbox-'+id).find('input.bld-editbox-animation-delay').val() + 'ms');
            }
            else
            {
                $('#'+id)./*find('.bld-el').*/css('animation-delay', '0');
                $('#'+id)./*find('.bld-el').*/css('-webkit-animation-delay', '0');
            }
        }
        if($('#editbox-'+id).find('.bld-editbox-custom-css').val()!='')
        {
            $('#'+id).find('.bld-el').css(bld_parseCSSBlock($('#editbox-'+id).find('.bld-editbox-custom-css').val()));   
        }
    }
    function bld_save_update_el_font(id)
    {
        if($('#editbox-'+id).find('.bld-editbox-font').length>0)
        {
            $('#'+id).find('.bld-el').css('font-family', $('#editbox-'+id).find('.bld-editbox-font').find('option:selected').val());
            if($('#editbox-'+id).find('.bld-editbox-font').find('option:selected').parent().attr('label')==='Google Fonts')
            {
                WebFont.load({
                google: {
                families: [$('#editbox-'+id).find('.bld-editbox-font').find('option:selected').val()]
                }});
            }
        }
    }
    function bld_save_update_el_save(id)
    {
        // placeholder
        if($('#editbox-'+id).find('input.bld-editbox-placeholder-color').length>0 && $('#editbox-'+id).find('input.bld-editbox-placeholder-color').val()!='')
        {
            $("style[id='bld-ph-"+id+"']").remove();
            $("<style>")
            .prop('id','bld-ph-'+id)
            .prop("type", "text/css")
            .html("#"+id+" .bld-el::-webkit-input-placeholder { color: "+$('#editbox-'+id).find('input.bld-editbox-placeholder-color').val()+"; }" +
            "#"+id+" .bld-el::-moz-placeholder { color: "+$('#editbox-'+id).find('input.bld-editbox-placeholder-color').val()+"; }")
            .appendTo("head");
        }
    }
    function bld_save_update_el_pointlist(id)
    {
        // placeholder
        if($('#editbox-'+id).find('input.bld-editbox-pointimg').length>0 && $('#editbox-'+id).find('input.bld-editbox-pointimg').val()!='')
        {
            $("style[id='bld-ph-"+id+"']").remove();
            $("<style>")
            .prop('id','bld-ph-'+id)
            .prop("type", "text/css")
            .html("#"+id+" ul.bld-el li { line-height: "+($('#editbox-'+id).find('input.bld-editbox-lineheight').val()!='' ? $('#editbox-'+id).find('input.bld-editbox-lineheight').val()+'px' : 'normal')+"; padding-left: "+($('#editbox-'+id).find('input.bld-editbox-pointimg-padding').val()!='' ? $('#editbox-'+id).find('input.bld-editbox-pointimg-padding').val() : '0')+"px; background-image: url('"+$('#editbox-'+id).find('input.bld-editbox-pointimg').val()+"'); }")
            .appendTo("head");
        }
    }
    $(document).on('keyup', '.bld-editbox-live', function (){
        if($( this ).parents('.bld-el-editbox').hasClass('bld-step-editbox'))
        {
            bld_save_update_step($( this ).parents('.bld-el-editbox').data('id'));
        }
        else
        {
            bld_save_update_el($( this ).parents('.bld-el-editbox').data('id'));
        }
    });
    $(document).on('change', '.bld-editbox-live-font', function (){
            bld_save_update_el_font($( this ).parents('.bld-el-editbox').data('id'));
    });
    $(document).on('keyup', '.bld-editbox-live-pointlist', function (){
            bld_save_update_el_pointlist($( this ).parents('.bld-el-editbox').data('id'));
    });
    $(document).on('change', '.bld-editbox-live-change', function (){
        if($( this ).parents('.bld-el-editbox').hasClass('bld-step-editbox'))
        {
            bld_save_update_step($( this ).parents('.bld-el-editbox').data('id'));
        }
        else
        {
            bld_save_update_el($( this ).parents('.bld-el-editbox').data('id'));
        }
    });
    $('.builder-tpl-lib-group').mouseenter(function() 
    {
        $( this ).children('.builder-tpl-lib').show();
    });
    $('.builder-tpl-lib-group').mouseleave(function() 
    {
        $( this ).children('.builder-tpl-lib').hide();
    });
    $('.builder-tpl-lib-cat-label').click(function() 
    {
        $('.builder-tpl-lib-cat-label').removeClass('active');
        $(this).addClass('active');
        $('.builder-tpl-lib-cat-cont').hide();
        $('#builder-images-category-'+$(this).attr('rel-id')).show();
        return false;
    });
    $('#builder-container').on('click', '.bld-el-edit', function() 
    {
        //if(editbox!==null)
        //{
        //	editbox.find('.bld-el-save').click();
        //}
        var edited_el = $(this).parents('.bld-el-cont');
        var editbox = edited_el.find('.bld-el-editbox');
        //editbox.appendTo( $(this).parents('.builder-step') );
        //editbox.show();
        $('#publish').hide();
        editbox.dialog({ 
                    width: 500, 
                    height: 'auto',
                    dialogClass: "bld-dialog",
                    position: { my: "left center", at: "right center", of: edited_el },
                    title: 'Element Settings',
                    close: function( event, ui ) {
                        $( this ).dialog( "destroy" );
                        $('#publish').show();
                        bld_save_update_el($( this ).data('id'));
                        bld_save_update_el_save($( this ).data('id'));
                    },
                    buttons: {
                        "Done": function() {
                            $( this ).dialog( "destroy" );
                            $('#publish').show();
                            bld_save_update_el($( this ).data('id'));
                            bld_save_update_el_save($( this ).data('id'));
                        },
                        /*"Export": function() {
                            console.log('EXPORT:');
                            $( this ).find('input[type=text],input[type=radio]:checked,input[type=checkbox]:checked, textarea, select').each(function(){
                                if($( this ).val()!='')
                                {
                                    console.log($( this ).attr('name').match(/\]\[([^']+)\]/)[1] +' => ' + $( this ).val());
                                }
                            })
                        }*/
                    }
                });
        return false;
    });
    $('#builder-container').on('click', '.bld-el-handle-a', function() 
    {
        return false;
    });
    $('body').on('click', '.bld-editbox-tabs-link', function() 
    {
        var editbox = $(this).parents('.bld-el-editbox');
        editbox.find('.bld-editbox-tabs-link').removeClass('bld-editbox-tab-link-active');
        $(this).addClass('bld-editbox-tab-link-active');
        editbox.find('.bld-editbox-tab').removeClass('bld-editbox-tab-active');
        editbox.find('.' + $(this).attr('rel')).addClass('bld-editbox-tab-active');
        return false;
    });
    $('body').on('click', '.bld-editbox-img-button', function(e) 
    {
        var activeFileUploadContext = jQuery(this).parent();
        e.preventDefault();
        var custom_file_frame = null;
        custom_file_frame = wp.media.frames.customHeader = wp.media({
            title: 'Choose',
            library: {
                type: 'image'
            }
        });
        custom_file_frame.on("select", function() {
            var attachment = custom_file_frame.state().get("selection").first();
            var img = new Image();
            img.onload = function() {
                if(activeFileUploadContext.parents('.bld-el-editbox').hasClass('bld-el-editbox-img'))
                {
                    activeFileUploadContext.parents('.bld-el-editbox').find('.bld-editbox-width').val(this.width);
                    activeFileUploadContext.parents('.bld-el-editbox').find('.bld-editbox-height').val(this.height);
                }
                activeFileUploadContext.find('input[type="text"]').val(attachment.attributes.url).trigger('keyup');
                img = null;
            }
            img.src = attachment.attributes.url;
        });
        custom_file_frame.open();
        return false;
    });
    $('body').on('click', '.bld-editbox-imglibrary-button', function(e) 
    {
        var textField = jQuery(this).parent().children('.bld-editbox-img').first();
        var style = "position: fixed; top: 50%; left: 50%; width: 800px; height: 600px; margin-top: -300px; margin-left: -400px; padding: 20px; background-color: white; z-index: 99999; border: 1px solid gray; border-radius: 2px;"
        var save = '<a href="#" data-src="" data-textField="'+ textField.attr('name') +'" class="bld-button snp-library-save">Save</a>';
        var exit = '<a href="#" class="bld-button snp-library-close" style="background-color: red;">Close</a>';
        var modal = '<div class="snp-modal" style="'+style+'"><div class="snp-modal-content" style="background-color: #eee; padding:10px; height:540px; overflow: auto; overflow-y:scroll;"></div><hr /><div style="float:right;">'+save+exit+'</div></div>';
        $('body').append(modal);
        var container = $('.snp-modal .snp-modal-content');
        $.ajax({
            url: ajaxurl,
            dataType: 'json',
            type: 'POST',
            data: {action: 'snp_load_library_to_modal'},
            success: function(response){
                $.each(response, function(index, category){
                    $.each(category.imgs, function(i, image){
                        container.append('<img class="snp-library-image" style="margin: 1px; border: 3px solid #eee;" src="'+ image.img +'" data-name="'+ image.name +'" width="100px" height="100px" />');
                    });
                });
            }
        });
        return false;
    });
    $('body').on('click', '.snp-library-image', function(){
        $(this).parent().children().css('border', '3px solid #eee');
        $(this).css('border', '3px solid blue');
        $('body .snp-modal .snp-library-save').attr('data-src', $(this).attr('src') );
    });
    $('body').on('click', '.snp-library-close', function(){
        $('body .snp-modal').remove();
        return false;
    });
    $('body').on('click', '.snp-library-save', function(){
        var field = $('body').find('[name="'+ $(this).attr('data-textField') +'"]');
        var src = $(this).attr('data-src');
        if(src != ''){
            field.val(src);
        }
        $('body .snp-modal').remove();
        return false;
    });
    $( '.builder-add-step').click(function() {
        var new_step_el = $('#builder-tpl').find('.builder-step').clone(true);
        var index = 1 ;
        if($('#builder-container').find('.builder-step:last').length>0)
        {
            index = index + parseInt($('#builder-container').find('.builder-step:last').attr('data-step'));
        }
        new_step_el.attr('data-step',index);
        new_step_el.attr('id','step-'+index);
        new_step_el.find('.builder-step-label').text(index);
        new_step_el.find('.bld-step-rand').val(index).attr('disabled', false);
        new_step_el.find('input, select, textarea').each(function() {
            if($(this).attr('name'))
            {
                $(this).attr('name',$(this).attr('name').replace('RAND',index));
            }
        });
        new_step_el.find('input, img, div, select, textarea, table, span, a').each(function() {
            if($(this).attr('id'))
            {
                $(this).attr('id',$(this).attr('id').replace('RAND',index));
            }
        });
        new_step_el.find('a').each(function() {
            if($(this).attr('href').indexOf('RAND'))
            {
                $(this).attr('href',$(this).attr('href').replace('RAND',index));
            }
        });
        new_step_el.find('.bld-step-editbox').attr('data-id','step-'+index).attr('id','editbox-step-'+index);
        new_step_el.find('.bld-run-colorpicker').wpColorPicker(colorpicker_opts);
        $('#builder-container').append(new_step_el);
        new_step_el.find( ".builder-popup" ).droppable(step_droppable_opts);
        return false;
    });
    $(document).on('keyup', '.bld-editbox-pointlist-change', function (){
        bld_save_update_pointlist($( this ).parents('.bld-el-editbox').data('id'));
    });
    $('body').on('click', '.bld-editbox-selectoptions-delete', function(){
        if($(this).hasClass('bld-editbox-pointlist-change-btn'))
        {
            var element_id = $( this ).parents('.bld-el-editbox').data('id');
        }
        $(this).parents('.bld-editbox-selectoptions-option').remove();
        if(element_id!==undefined)
        {
            bld_save_update_pointlist(element_id);
        }
        return false;
    });
    $('body').on('click', '.bld-editbox-selectoptions-add',function(){
        var new_opt = $(this).prev('.bld-editbox-selectoptions-option').clone(true);
        new_opt.find('input').val('');
        $(this).before(new_opt);
        if($(this).hasClass('bld-editbox-pointlist-change-btn'))
        {
            bld_save_update_pointlist($( this ).parents('.bld-el-editbox').data('id'));
        }
        return false;
    });

    //Otwieranie linku na podstawie znalezionego wyboru selectbox
    $('body').on('click', '.bld-editbox-select-link-options-add',function() {
        var num = 0;
        $(this).parent().find('.bld-editbox-select-link-options-option').each(function(){
            num++;
        });
        console.log((num-1)+'. '+num);

        var new_opt = $(this).prev('.bld-editbox-select-link-options-option').clone(true);
        new_opt.find('input').each(function(i) {
            this.name= this.name.replace('['+(num-1)+']', '['+(num)+']');
            this.value = '';
        });
        $(this).before(new_opt);

        return false;
    });
    $('body').on('click', '.bld-editbox-select-link-options-delete', function(){
        $(this).parents('.bld-editbox-select-link-options-option').remove();

        return false;
    });

    $('body').on('change', '.bld-editbox-name', function(){
        var str = $(this).val();
        $(this).val(str.replace(/ /g, ""))  
    });
    tinymce.init(mce_opts);
    $("#builder-container").find('.bld-run-colorpicker').wpColorPicker(colorpicker_opts);
});
