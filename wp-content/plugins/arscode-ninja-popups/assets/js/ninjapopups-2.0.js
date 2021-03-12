/*!
 * Ninja Popups for WordPress
 * http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=arscode
 *
 * Copyright 2019, ARSCode
 */
//Triggers list:
//ninja_popups_submit - Trigger that is called before submitting the form to AJAX url

var NinjaPopupMain = {
    config: {
        cookies: {
            ignoreCookies: false
        }
    },
    debugLevel: false,
    isDebugEnabled: function() {
        return NinjaPopupMain.debugLevel;
    },
    setCookie: function(name, value, expires) {
        if (expires == -2) {
            return
        }

        if (expires != -1) {
            expires = expires * 1;

            var args = {
                path: '/',
                expires: expires
            };
        } else {
            var args = {
                path: '/'
            };
        }

        if (this.config.cookies.ignoreCookies) {
            Cookies.set(snp_cookie_prefix + '' +name, value, args);
        }
    },
    startResponsive: function(step, popupClass) {
        var p = jQuery('#' + popupClass).find('.snp-builder');
        if (step === false) {
            var cur_step = p.find('.snp-bld-showme');
        } else {
            var cur_step = p.find('#snp-bld-step-' + step);
        }

        var maxHeight = jQuery(window).height();
        var maxWidth = jQuery(window).width();
        var scaleX = maxWidth / cur_step.data('width');
        var scaleY = maxHeight / cur_step.data('height');
        var scale = ((scaleX > scaleY) ? scaleY : scaleX) - 0.01;
        if (scale > 1) {
            scale = 1;
        }
        
        var parent = cur_step.parent('.snp-bld-step-cont');
        if (scale < 1) {
            parent.css('transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('-webkit-transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('-moz-transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('-ms-transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
        } else {
            parent.css('transform', "");
            parent.css('-webkit-transform', "");
            parent.css('-moz-transform', "");
            parent.css('-ms-transform', "");
        }
    },
    startVideo: function(obj) {
        obj.find('.snp-bld-video').each(function(){
            var url = jQuery(this).attr('data-src') + jQuery(this).attr('data-autoplay');
            jQuery(this).attr('src', url);
        });
    },
    stopVideo: function(obj) {
        obj.find('.snp-bld-video').each(function(){
            var url = jQuery(this).attr('data-src');
            jQuery(this).attr('src', url);
        });
    },
    initMap: function(obj) {
        obj.find('.snp-bld-googleMap').each(function(){
            jQuery(this).height( jQuery(this).parent().height() );
            jQuery(this).width( jQuery(this).parent().width() );
            var mapType;
            switch(jQuery(this).attr('data-mapType')){
                case 'ROADMAP':
                    mapType = google.maps.MapTypeId.ROADMAP;
                    break;
                case 'SATELLITE':
                    mapType = google.maps.MapTypeId.SATELLITE;
                    break;
                case 'HYBRID':
                    mapType = google.maps.MapTypeId.HYBRID;
                    break;
                case 'TERRAIN':
                    mapType = google.maps.MapTypeId.TERRAIN;
                    break;
            }
            var mapProp = {
                center: new google.maps.LatLng(parseFloat(jQuery(this).attr('data-coordx')),parseFloat(jQuery(this).attr('data-coordy'))),
                zoom: parseInt(jQuery(this).attr('data-zoom')),
                mapTypeId: mapType,
            };
            var element = jQuery(this);
            var map = new google.maps.Map(element[0], mapProp);
            var point = new google.maps.LatLng(parseFloat(jQuery(this).attr('data-coordx')), parseFloat(jQuery(this).attr('data-coordy')));
            var opts = {
                position: point,
                map: map,
                icon: jQuery(this).attr('data-icon'),
            };
            var marker = new google.maps.Marker(opts);
        });
    },
    callWebhook: function(uri, formData) {
        if (uri && formData) {
            jQuery.ajax({
                url: uri,
                type: 'POST',
                dataType: 'json',
                data: formData
            });
        }
    }
};

var NinjaPopup = {
    popupId: '',
    popupType: '',
    popupShowed: false,
    config: {},
    exitPopup: {
        href: '',
        target: '',
    },
    init: function (popupID, type, jsonConfig) {
        var jsonParseConfig = jQuery.parseJSON(jsonConfig);

        this.popupId = popupID;
        this.popupType = type;
        this.config = jsonParseConfig;

        jQuery(document).ready(function() {

        });
    },
    open: function(popupType, options) {
        if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen) {
            return;
        }

        var $this = this;

        this.exitPopup.href = options.exitPopup.href;
        this.exitPopup.target = options.exitPopup.target;

        if (this.config.popupTheme === 'builder') {
            jQuery('#' + this.config.popupDivId).appendTo("body");

            var step1 = jQuery('.snp-pop-' + this.config.popupId + ' .snp-builder')
                .not('.snp-pos-static')
                .addClass('snp-bld-showme')
                .find('.snp-bld-step-1');
            step1.addClass('snp-bld-showme');

            NinjaPopupMain.startVideo(step1);
            NinjaPopupMain.initMap(step1);

            if (step1.attr('data-overlay') != 'disabled') {
                jQuery('.snp-pop-' + this.config.popupId + ' .snp-overlay').addClass('snp-overlay-show');
            }

            if (jQuery("#snp-bld-step-bg-1").length > 0) {
                jQuery("#snp-bld-step-bg-1").mb_YTPlayer();
            }

            NinjaPopupMain.startResponsive(1, this.config.popupDivId);

            $this.onOpenEvents();
        } else {
            var overlay_css = {};
            if (this.config.overlayType == 'disabled') {
                overlay_css.background = 'none';
            }

            jQuery.fancybox2({
                'href': '#' + $this.config.popupDivId,
                'helpers': {
                    'overlay': {
                        'locked': false,
                        'closeClick': false,
                        'showEarly': false,
                        'speedOut': 5,
                        'css': overlay_css
                    }
                },
                'padding': 0,
                'autoCenter': snp_is_mobile == true ? false : true,
                'autoDimensions': true,
                'titleShow': false,
                'closeBtn': ($this.config.showReadyThemeCloseButton == 'yes' ? true : false),
                'keys': {
                    'close': ($this.config.showReadyThemeCloseButton == 'yes' ? [27] : '')
                },
                'showNavArrows': false,
                'wrapCSS': 'snp-wrap',
                'afterClose': function () {
                    return $this.onCloseEvents();
                },
                'beforeShow': function () {
                    return $this.onOpenEvents();
                }
            });
        }
    },
    close: function() {
        this.internalCloseEvents();

        if (this.config.popupTheme === 'builder') {
            var p = jQuery('.snp-pop-' + this.config.popupId + ' .snp-builder').not('.snp-pos-static');
            var cur_step = p.find('.snp-bld-showme');
            NinjaPopupMain.stopVideo(cur_step);
            if (cur_step.data('animation-close') !== undefined) {
                cur_step.removeClass('animated ' + cur_step.attr('data-animation'));
                cur_step.addClass('animated ' + cur_step.attr('data-animation-close')).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    jQuery(this).removeClass('animated ' + jQuery(this).attr('data-animation-close'));
                    jQuery(this).removeClass('snp-bld-showme');
                });
            } else {
                cur_step.removeClass('snp-bld-showme');
            }
            p.removeClass('snp-bld-showme');
            jQuery('.snp-pop-' + this.config.popupId + ' .snp-overlay').removeClass('snp-overlay-show');
            this.onCloseEvents();
        } else {
            if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen) {
                jQuery.fancybox2.close();
            }
        }
    },
    submit: function(submitterForm) {
        //Trigger that is called before submitting the form to AJAX url
        var ninja_popups_submit = jQuery.event.trigger({
            type: "ninja_popups_submit",
            popup_id: popup_ID
        });

        if (ninja_popups_submit === false) {
            return false;
        }

        //Collect form data that will be sent
        var formDataAsArray = this.collectFormData(submitterForm);

        //Before opt-in webhook
        NinjaPopupMain.callWebhook(this.config.beforeOptinWebhookUrl, formDataAsArray);

        //SEND OPT-IN HERE
        this.sendOptIn(formDataAsArray);

        //After opt-in webook
        NinjaPopupMain.callWebhook(this.config.afterOptinWebhookUrl, formDataAsArray);

        //Trigger
        jQuery.event.trigger({
            type: "ninja_popups_submit_after",
            popup_id: popup_ID
        });
    },
    collectFormData: function(submitterForm) {
        var submitType = this.config.formDataCollectType;

        if (typeof FormData != 'undefined') {
            console.log('FormData is enabled');

            var form_type = 'form-object';
            var form_data = new FormData();
            form_data.append('action', 'snp_popup_submit');
            form_data.append('popup_ID', this.config.popupId);
            form_data.append('_wpnonce', snp_ajax_nonce);
        } else {
            console.log('FormData is disabled');

            var form_type = 'array';
            var form_data = {};
            form_data['action'] = 'snp_popup_submit';
            form_data['popup_ID'] = this.config.popupId;
        }

        if (submitType === 'single') {
            submitterForm.find('button, input, select, textarea').each(function() {
                this.prepareFormArray(this, form_data, form_type);
            });
        } else {
            submitterForm.parents('.snppopup').find('.snp_subscribeform').each(function() {
                var eachForm = jQuery(this);
                eachForm.find('button, input, select, textarea').each(function () {
                    this.prepareFormArray(this, form_data, form_type);
                });
            });
        }

        return form_data;
    },
    prepareFormArray: function(inputElement, form_data, type) {
        if (type === 'array') {
            if (inputElement.name) {
                if (inputElement.type === 'checkbox' || inputElement.type === 'radio') {
                    if (inputElement.checked) {
                        form_data[inputElement.name] = inputElement.value;
                    }
                } else if (inputElement.name === 'np_custom_name2') {
                    form_data[inputElement.name] = 1;
                } else {
                    form_data[inputElement.name] = inputElement.value;
                }
            }
        } else if (type === 'form-object') {
            if (inputElement.name) {
                if (inputElement.type === 'checkbox' || inputElement.type === 'radio') {
                    if (inputElement.checked) {
                        form_data.append(inputElement.name, inputElement.value);
                    }
                } else if (inputElement.name === 'np_custom_name2') {
                    form_data.append(inputElement.name, '1');
                } else {
                    form_data.append(inputElement.name, inputElement.value);
                }
            }
        }
    },
    sendOptIn: function(formData) {
        jQuery.ajax({
            url: this.config.ajaxUrl,
            type: 'POST',
            'dataType': 'json',
            'data': formData,
            'contentType': false,
            'processData': false,
            success: function (data) {
                jQuery("input, textarea, select", form).removeClass('snp-error');
                this.internalCloseEvents();

                if (data.api_error_msg) {
                    alert(data.api_error_msg);
                } else if (data.Ok == true) {
                    //Trigger that will fire some addons like: drip, learnq, metrilo
                    jQuery.event.trigger({
                        type: 'ninja_popups_ajax_response',
                        response: data
                    });

                    if (form.data('analyzed') === true) {
                    } else {
                        snp_onconvert('optin', popup_ID, ab_ID, (!nextstep && !bld_nextstep ? true : false));
                    }

                    form.data('analyzed', true);

                    jQuery.event.trigger({
                        type: "ninja_popups_submit_success",
                        popup_id: popup_ID
                    });

                    if (snp_optin_redirect_url) {
                        console.log('Redirect URL:' + snp_optin_redirect_url);
                        window.open(snp_optin_redirect_url, "_self");
                    }

                    if (bld_nextstep) {
                        snp_bld_gotostep(popup_ID, bld_nextstep);
                    } else if (nextstep) {
                        var p = submit_button.parents('.snp-fb');
                        p.find('.snp-step-show').fadeOut(function () {
                            jQuery(this).removeClass('snp-step-show');
                            p.find('.snp-step-' + nextstep).fadeIn(function () {
                                jQuery(this).addClass('snp-step-show');
                            });
                        });
                    } else if (text_success) {
                        submit_button.text(text_success);
                        submit_button.val(text_success);
                        setTimeout("snp_close();", 800);
                    } else {
                        snp_close();
                    }
                } else {
                    if (data.Errors) {
                        jQuery.each(data.Errors, function (index, value) {
                            if (index == 'captcha') {
                                if (value == '1') {
                                    alert('Wrong captcha response!');
                                } else {
                                    alert(value);

                                }
                            } else {
                                jQuery("input[name='" + index + "'], textarea[name='" + index + "'], select[name='" + index + "']", form).addClass('snp-error');

                                if (typeof jQuery.fn.tooltipster !== 'undefined') {
                                    jQuery("input[name='" + index + "'], textarea[name='" + index + "'], select[name='" + index + "']", form)
                                        .tooltipster({
                                            theme: 'tooltipster-light',
                                            side: 'right'
                                        })
                                        .tooltipster('content', value)
                                        .tooltipster('open');
                                }
                            }
                        });
                    }

                    if (text_loading) {
                        submit_button.html(text_submit);
                        submit_button.val(text_submit);
                    }

                    jQuery.event.trigger({
                        type: "ninja_popups_submit_error",
                        popup_id: popup_ID
                    });
                }
            }
        });
    },
    onOpenEvents: function() {
        jQuery('.fancybox-overlay').addClass('snp-pop-' + this.config.popupId + '-overlay');
        jQuery('.snp-wrap').addClass('snp-pop-' + this.config.popupId + '-wrap');
        jQuery('.snp-wrap').addClass('snp-pop-' + this.config.popupTheme + '-wrap');

        jQuery.post(this.config.ajaxUrl, {
            'action': 'snp_popup_stats',
            'type': 'view',
            'popup_ID': this.config.popupId,
            'ab_ID': this.config.abId
        });
    },
    onCloseEvents: function() {},
    internalCloseEvents: function () {
        //Close all tooltipster instances
        if (typeof jQuery.fn.tooltipster !== 'undefined') {
            var instances = jQuery.tooltipster.instances();
            if (instances) {
                jQuery.each(instances, function (i, instance) {
                    if (instance  !== 'undefined') {
                        try {
                            instance.close();
                        } catch (err) {}
                    }
                });
            }
        }
    }
};