/*!
 * Ninja Popups for WordPress
 * http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=arscode
 *
 * Copyright 2019, ARSCode
 */
//Triggers list:
//ninja_popups_submit - Trigger that is called before submitting the form to AJAX url

var snp_timer;
var snp_timer_o;
var snp_is_internal_link;
var snpSpendTimeTimeout;
var snpIsPopupShowed = false;

var NinjaPopup = {
    snp_f: [],
    popupType: '',
    popupShowed: false,
	config: {
		ajaxUrl: '',
        optinRedirectUrl: '',
        webhookUrl: {
		    beforeOptin: '',
            afterOptin: ''
        },
        openMethod: '',
        closeMethod: ''
	},
    init: function () {},
    open: function() {},
    close: function() {},
    submit: function(submitterForm) {},
    collectFormData: function(submitterForm) {},
    prepareFormArray: function(inputElement, form_data, type) {},
    sendOptIn: function(formData) {},
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

function snp_ga(category, action, label, value)
{
    if (!snp_enable_analytics_events || typeof ga !== "function") {
        return;
    }
    
    ga('send', 'event', category, action, label, value);
}

function snp_set_cookie(name, value, expires)
{
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
        var args = {path: '/'};
    }

    if (snp_ignore_cookies === undefined || snp_ignore_cookies == false) {
        Cookies.set(snp_cookie_prefix + '' +name, value, args);
    }
}

function snp_close()
{
    snpIsPopupShowed = false;

    NinjaPopup.internalCloseEvents();

    if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen) {
        jQuery.fancybox2.close();
    } else {
        var popup = jQuery('#snp_popup').val();
        if (snp_f[popup + '-open'] !== undefined) {
            snp_f[popup + '-close']();
            snp_onclose_popup();
        }
    }
}

function snp_onsubmit(form)
{
    var popup = jQuery('#snp_popup').val();
    var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    if (!popup_ID) {
        popup_ID = form.parents('.snppopup').find('.snp_popup_id').val();
    }
    var ab_ID = form.parents('.snppopup').find('.snp_popup_ab_id').val();
    if (ab_ID === undefined) {
        ab_ID = false;
    }
    var ninja_popups_submit = jQuery.event.trigger({
        type: "ninja_popups_submit",
        popup_id: popup_ID
    });
    if (ninja_popups_submit === false) {
        return false;
    }

    var snp_optin_redirect_url = form.parents('.snppopup').find('.snp_optin_redirect_url').val();
    var snp_optin_form_submit = form.parents('.snppopup').find('.snp_optin_form_submit').val();
    if (form.attr('action') == '#') {
        var submit_button = jQuery('input[type="submit"], button[type="submit"]', form);
        var submit_button_width = submit_button.outerWidth();
        var text_loading = submit_button.data('loading');
        var text_success = submit_button.data('success');
        var nextstep = submit_button.data('nextstep');
        var bld_nextstep = submit_button.data('step');
        var text_submit = submit_button.html() ? submit_button.html() : submit_button.val();
        if (text_loading) {
            if (!submit_button.hasClass('snp-nomw')) {
                submit_button.css('min-width', submit_button_width);
            }
            submit_button.html(text_loading);
            submit_button.val(text_loading);
        }
        
        if (snp_optin_form_submit == 'single') {
        	if (typeof FormData != 'undefined') {
                console.log('FormData');
                console.log(form);

                var formArray = [];
            	var form_data = new FormData();

				form_data.append('action', 'snp_popup_submit');
				form_data.append('popup_ID', popup_ID);
				form_data.append('_wpnonce', snp_ajax_nonce);

				form.find('button, input, select, textarea').each(function(key) {
				    if (this.name) {
				        console.log(this.type);
				        if (this.type === 'checkbox' || this.type === 'radio') {
				            if (this.checked) {
                                formArray.push(new Array(this.name, this.value));
				                form_data.append(this.name, this.value);
				            }
				        } else if (this.name == 'np_custom_name2') {
                            formArray.push(new Array(this.name, '1'));
                            form_data.append(this.name, '1');
                        } else if (this.type === 'file') {
				            console.log(jQuery('input[name="'+this.name+'"]')[0].files[0]);
                            formArray.push(new Array(this.name, this.value));
				            form_data.append(this.name, jQuery('input[name="'+this.name+'"]')[0].files[0]);
				        } else {
				            formArray.push(new Array(this.name, this.value));
				            form_data.append(this.name, this.value);
				        }
				    }
				});

                console.log(form_data);
                console.log(formArray);
        	} else {
                console.log('NO FormData');
                console.log(form);

            	var form_data = {};

				form_data['action'] = 'snp_popup_submit';
				form_data['popup_ID'] = popup_ID;

				form.find('button, input, select, textarea').each(function(key) {
                    if (this.type === 'checkbox' || this.type === 'radio') {
                        if (this.checked) {
                            form_data[this.name] = this.value;
                        }
                    } else if (this.name == 'np_custom_name2') {
                        form_data[this.name] = 1;
                    } else {
                        form_data[this.name] = this.value;
                    }
                });
                
                console.log(form_data);
        	}
        } else if (snp_optin_form_submit == 'all') {
	        if (typeof FormData != 'undefined') {
		        var form_data = new FormData();

				form.parents('.snppopup').find('.snp_subscribeform').each(function(e) {
				    var eachForm = jQuery(this);
                    eachForm.find('button, input, select, textarea').each(function (key) {
	            		if (this.name) {
                            if (this.type === 'checkbox' || this.type === 'radio') {
                                if (this.checked) {
                                    form_data.append(this.name, this.value);
                                }
                            } else if (this.name == 'np_custom_name2') {
                                form_data.append(this.name, '1');
                            } else if (this.type === 'file') {
				                form_data.append(this.name, jQuery('input[name="'+this.name+'"]').files[0]);
                            } else {
                                form_data.append(this.name, this.value);
                            }
	                	}
	            	});
		        });
		        
				form_data.append('action', 'snp_popup_submit');
				form_data.append('popup_ID', popup_ID);
				form_data.append('_wpnonce', snp_ajax_nonce);
		    } else {
		        var form_data = {};
				form_data['action'] = 'snp_popup_submit';
				form_data['popup_ID'] = popup_ID;
		        form.parents('.snppopup').find('.snp_subscribeform').each(function(e) {
                    var eachForm = jQuery(this);
                    eachForm.find('button, input, select, textarea').each(function (key) {
	            		if (this.name) {
	            		    if (this.type === 'checkbox' || this.type === 'radio') {
	            		        if (this.checked) {
                                    form_data[this.name] = this.value;
                                }
                            } else if (this.name == 'np_custom_name2') {
                                form_data[this.name] = 1;
                            } else {
                                form_data[this.name] = this.value;
                            }
	                	}
	            	});
		        });
	        }
        }

        //Before opt-in webhook
        if (form.parents('.snppopup').find('.snp_ajax_before_optin').val()) {
            jQuery.ajax({
                url: form.parents('.snppopup').find('.snp_ajax_before_optin').val(),
                type: 'POST',
                dataType: 'json',
                data: form_data
            });
        }

        if (form.parents('.snppopup').find('.snp_ajax_url').val()) {
            snp_ajax_url = form.parents('.snppopup').find('.snp_ajax_url').val();
        }
        
        jQuery.ajax({
            url: snp_ajax_url,
            type: 'POST',
            'dataType': 'json',
            'data': form_data,
            'contentType': false,
            'processData': false,
            success: function (data) {
                jQuery("input, textarea, select", form).removeClass('snp-error');
                NinjaPopup.internalCloseEvents();

                if (data.api_error_msg) {
                	alert(data.api_error_msg);
                } else if (data.Ok == true) {
                    if (data.drip !== undefined) {
                        _dcq.push(["identify", data.drip]);
                    }

                    if (data.learnq !== undefined) {
                        _learnq.push(["identify", data.learnq]);
                    }

                    if (data.metrilo !== undefined) {
                        var metriloJson = jQuery.parseJSON(data.metrilo);
                        console.log(metriloJson);
                        console.log(metriloJson.email);
                        metrilo.identify(metriloJson.email, metriloJson);
                        metrilo.event('apply_tags', {
                            tags: metriloJson.tags
                        });
                    }


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
                        window.open(snp_optin_redirect_url, "_blank");
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

        jQuery.event.trigger({
            type: "ninja_popups_submit_after",
            popup_id: popup_ID
        });

        //After opt-in webhook
        if (form.parents('.snppopup').find('.snp_ajax_after_optin').val()) {
            jQuery.ajax({
                url: form.parents('.snppopup').find('.snp_ajax_after_optin').val(),
                type: 'POST',
                dataType: 'json',
                data: form_data
            });
        }

        return false;
    } else {
        var Error = 0;
        jQuery('input[type="text"]', form).each(function (key) {
            if (!this.value) {
                Error = 1;
                jQuery(this).addClass('snp-error');
            } else {
                jQuery(this).removeClass('snp-error');
            }
        });

        if (Error == 1) {
            return false;
        }

        if (form.attr('target') == '_blank') {
            snp_close();
        }

        if (snp_optin_redirect_url) {
            console.log('Redirect URL:' + snp_optin_redirect_url);
            window.open(snp_optin_redirect_url, "_blank");
        }

        if (form.data('analyzed') === true) {
        } else {
            snp_onconvert('optin', popup_ID, ab_ID);
        }

        form.data('analyzed', true);
        
        return true;
    }
}

function snp_onconvert(type, popup_ID, ab_ID, close)
{
    var popup = jQuery('#snp_popup').val();
    if (!popup_ID) {
        var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    }

    if (popup) {
        var sufix = '';
        if (snp_separate_cookies) {
            sufix = popup_ID;
        }

        var cookie_conversion = jQuery('#' + popup + ' .snp_cookie_conversion').val();
        if (!cookie_conversion) {
            cookie_conversion = 30;
        }

        snp_set_cookie('snp_' + popup + sufix, '1', cookie_conversion);
    }

    jQuery.event.trigger({
        type: "ninja_popups_convert",
        popup_id: popup_ID
    });

    if (jQuery('#' + popup).find('.snp_ajax_url').val()) {
        snp_ajax_url = jQuery('#' + popup).find('.snp_ajax_url').val();
    }
    
    jQuery.post(snp_ajax_url, {
        'action': 'snp_popup_stats',
        'type': type,
        'popup_ID': popup_ID,
        'ab_ID': ab_ID
    });

    if (type != 'optin') {
        var snp_optin_redirect_url = jQuery('#' + popup).find('.snp_optin_redirect_url').val();
        if (snp_optin_redirect_url) {
            document.location.href = snp_optin_redirect_url;
        }
    }

    snp_ga('popup', 'subscribe', popup_ID);
    
    if (close == true) {
        snp_close();
    }
}

function snp_onshare_li()
{
    snp_onconvert('li', 0, false, true);
}

function snp_onshare_gp()
{
    snp_onconvert('gp', 0, false, true);
}

function snp_onclose_popup()
{
    snpIsPopupShowed = false;
    
    var popup = jQuery('#snp_popup').val();
    var popup_ID = parseInt(jQuery('#snp_popup_id').val());

    var sufix = '';
    if (snp_separate_cookies) {
        sufix = popup_ID;
    }

    if (jQuery('#snp_popup').val()) {
        var cookie_close = jQuery('#' + jQuery('#snp_popup').val() + ' .snp_cookie_close').val();
    } else {
        cookie_close = -1;
    }

    if (!Cookies.get(snp_cookie_prefix + 'snp_' + popup + sufix)) {
        if (!cookie_close) {
            cookie_close = -1;
        }

        snp_set_cookie('snp_' + popup + sufix, '1', cookie_close);
    }

    if (jQuery('#snp_exithref').val()) {
        document.location.href = jQuery('#snp_exithref').val();
    }

    jQuery.event.trigger({
        type: "ninja_popups_close",
        popup_id: jQuery('#snp_popup_id').val()
    });

    if (snp_is_mobile == true) {
        jQuery('body').removeClass('ninja-popup-open');
    }
    jQuery('.fancybox-overlay').removeClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-overlay');
    jQuery('.snp-wrap').removeClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-wrap');
    jQuery('#snp_popup_theme').val('');
    jQuery('#snp_popup').val('');
    jQuery('#snp_popup_id').val('');
    jQuery('#snp_exithref').val('');
    jQuery('#snp_exittarget').val('');
}

function snp_onstart_popup()
{
    if (snp_is_mobile == true) {
        jQuery('body').addClass('ninja-popup-open');
    }
    jQuery('.fancybox-overlay').addClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-overlay');
    jQuery('.snp-wrap').addClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-wrap');
    jQuery('.snp-wrap').addClass('snp-pop-' + jQuery('#snp_popup_theme').val() + '-wrap');
    var ab_ID = jQuery('.snp-pop-' + jQuery('#snp_popup_id').val()).find('.snp_popup_ab_id').val();
    if (ab_ID === undefined) {
        ab_ID = false;
    }

    if (jQuery('.snp-pop-' + jQuery('#snp_popup_id').val()).find('.snp_ajax_url').val()) {
        snp_ajax_url = jQuery('.snp-pop-' + jQuery('#snp_popup_id').val()).find('.snp_ajax_url').val();
    }
    jQuery.post(snp_ajax_url, {
        'action': 'snp_popup_stats',
        'type': 'view',
        'popup_ID': jQuery('#snp_popup_id').val(),
        'ab_ID': ab_ID
    });
}

function  snp_open_popup(href, target, popup, type, options)
{
    //if (snpIsPopupShowed) {
    //    return;
    //}

    if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen) {
        return;
    }

    if (snp_enable_mobile == false && type != 'content' && snp_is_mobile == true) {
        return;
    }

    if ((snp_ignore_cookies !== undefined && snp_ignore_cookies == true) || type == 'content') {

    } else {
        var sufix = '';
        if(snp_separate_cookies) {
            sufix = jQuery('#' + popup + ' >  .snp_popup_id').val();
        }

        if (Cookies.get(snp_cookie_prefix + 'snp_' + popup + sufix) == 1) {
            return true;
        }
    }

    var snp_autoclose = parseInt(jQuery('#' + popup + ' .snp_autoclose').val());
    var snp_show_cb_button = jQuery('#' + popup + ' .snp_show_cb_button').val();
    if (snp_autoclose) {
        snp_timer = setTimeout("snp_close()", snp_autoclose * 1000);
        jQuery('#' + popup + ' input').focus(function () {
            clearTimeout(snp_timer);
        });
    }

    var snp_overlay = jQuery('#' + popup + ' .snp_overlay').val();
    jQuery('#snp_popup').val(popup);
    jQuery('#snp_popup_id').val(jQuery('#' + popup + ' >  .snp_popup_id').val());
    jQuery('#snp_popup_theme').val(jQuery('#' + popup + ' >  .snp_popup_theme').val());
    jQuery('#snp_exithref').val(href);
    jQuery('#snp_exittarget').val(target);
    snp_ga('popup', 'open', jQuery('#snp_popup_id').val());

    if (type == 'iframe') {
        var iframe = jQuery('#' + popup).find('.ninja-popup-external-link-iframe');
        iframe.prop('src', options.src);
    }

    if (snp_f[popup + '-open'] !== undefined) {
        jQuery('#' + popup).appendTo("body");
        snp_f[popup + '-open']();
        snp_onstart_popup();
    } else {
        var overlay_css = {};
        if (snp_overlay == 'disabled') {
            overlay_css.background = 'none';
        }
             
        jQuery.fancybox2({
            'href': '#' + popup,
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
            'closeBtn': (snp_show_cb_button == 'yes' ? true : false),
            'keys': {
                'close': (snp_show_cb_button == 'yes' ? [27] : '')
            },
            'showNavArrows': false,
            'wrapCSS': 'snp-wrap',
            'afterClose': function () {
                return snp_onclose_popup()
            },
            'beforeShow': function () {
                return snp_onstart_popup()
            }
        });
    }

    if (jQuery('#' + popup + ' .snp-subscribe-social').length > 0) {
        if (typeof FB != 'undefined') {
            FB.Event.subscribe('edge.create', function () {
                snp_onconvert('fb', 0, false, true);
            });
        }

        if (typeof twttr != 'undefined') {
            twttr.events.bind('tweet', function (event) {
                snp_onconvert('tw_t', 0, false, true);
            });
            twttr.events.bind('follow', function (event) {
                snp_onconvert('tw_f', 0, false, true);
            });
        }

        jQuery("#" + popup + " a.pin-it-button").click(function () {
            snp_onconvert('pi', 0, false, true);
        });
    }

    if (type != 'content') {
        snpIsPopupShowed = true;
    }

    jQuery.event.trigger({
        type: "ninja_popups_open",
        popup_id: jQuery('#snp_popup_id').val()
    });
    
    return false;
}

function snp_bld_gotostep(popup_id, nextstep)
{
    var p = jQuery('.snp-pop-' + popup_id).find('.snp-builder');
    var cur_step = p.find('.snp-bld-showme');
    var next_step = p.find('.snp-bld-step-' + nextstep);

    if (cur_step.data('animation-close') !== undefined) {
        cur_step.removeClass('animated ' + cur_step.attr('data-animation'));
        cur_step.off('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend').addClass('animated ' + cur_step.attr('data-animation-close')).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            cur_step.removeClass('animated ' + cur_step.attr('data-animation-close'));
            cur_step.removeClass('snp-bld-showme');
        });
    } else {
        cur_step.removeClass('snp-bld-showme');
    }

    if (next_step.attr('data-animation') !== undefined) {
        next_step.off('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend').addClass('animated ' + next_step.attr('data-animation')).addClass('snp-bld-showme').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            jQuery(this).removeClass('animated ' + jQuery(this).attr('data-animation'));
        });
    } else {
        next_step.addClass('snp-bld-showme');
    }

    snp_resp(nextstep);
    
    if (next_step.attr('data-overlay') == 'disabled') {
        p.next('.snp-overlay').removeClass('snp-overlay-show');
    } else {
        p.next('.snp-overlay').addClass('snp-overlay-show');
    }
    
    if (jQuery("#snp-bld-step-bg-" + nextstep).length > 0) {
        jQuery("#snp-bld-step-bg-" + nextstep).mb_YTPlayer();
    }

    snp_stop_video(cur_step);
    snp_start_video(next_step);
    snp_init_map(next_step);
    
    snp_ga('popup', 'step' + nextstep, jQuery('#snp_popup_id').val());

    jQuery.event.trigger({
        'type': "ninja_popups_gotostep",
        'popup_id': popup_id,
        'step': nextstep
    });
}

function snp_start_video(obj)
{
    obj.find('.snp-bld-video').each(function(){
        var url = jQuery(this).attr('data-src') + jQuery(this).attr('data-autoplay');
        jQuery(this).attr('src', url);
    });
}

function snp_stop_video(obj)
{
    obj.find('.snp-bld-video').each(function(){
        var url = jQuery(this).attr('data-src');
        jQuery(this).attr('src', url);
    });
}

function snp_init_map(obj)
{
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
}

function snp_spend_time() {    
    snpSpendTimeTimeout = setTimeout(function() {

        var data = {};
        data['action'] = 'snp_popup_spend_time';

        jQuery.ajax({
            url: snp_ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (data) {
                if (data.time >= jQuery('#snppopup-welcome .snp_open_spend_time').val()) {
                    snp_open_popup('', '', 'snppopup-welcome', 'welcome');
                    clearTimeout(snpSpendTimeTimeout);
                }
            }
        });

        snp_spend_time();
    }, snp_ajax_ping_time);
}

jQuery(document).ready(function ($) {
    jQuery(window).resize(function(){
        snp_resp(false);
    });

    jQuery(document).keyup(function(e) {
        if (snp_close_on_esc_key && e.keyCode === 27) {
            snp_close();
            return false;
        }
    });

    jQuery(".snp_nothanks, .snp_closelink, .snp-close-link").click(function (e) {
        e.preventDefault();
        
        snp_close();
        return false;
    });
    
    jQuery(".snp_subscribeform").on('submit', function(e) {
        return snp_onsubmit(jQuery(this));
    });

    if (jQuery('.snp-calendar-input').length) {
        jQuery('.snp-calendar-input').datepicker({
            dateFormat: "dd-mm-yy"
        });
    }

    jQuery('.ninja-popup-document-title-text').each(function() {
        jQuery(this).text(document.title);
    });

    jQuery('.ninja-popup-document-url-text').each(function() {
        jQuery(this).text(document.documentURI);
    });

    jQuery('.ninja-popup-document-title-input').each(function() {
        jQuery(this).val(document.title);
    });

    jQuery('.ninja-popup-document-url-input').each(function() {
        jQuery(this).val(document.documentURI);
    });

    if (jQuery('.snp-european-phone-input').length) {
        var telInputErrorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
        jQuery('.snp-european-phone-input').intlTelInput({
            hiddenInput: "full_phone",
            onlyCountries: [
                "al", "ad", "at", "by", "be", "ba", "bg", "hr", "cz", "dk",
                "ee", "fo", "fi", "fr", "de", "gi", "gr", "va", "hu", "is", "ie", "it", "lv",
                "li", "lt", "lu", "mk", "mt", "md", "mc", "me", "nl", "no", "pl", "pt", "ro",
                "ru", "sm", "rs", "sk", "si", "es", "se", "ch", "ua", "gb"
            ],
            utilsScript: "/wp-content/plugins/arscode-ninja-popups/assets/vendor/intl-tel-input/js/utils.js?1562189064761"
        });

        jQuery('.snp-european-phone-input').on('blur', function () {
            NinjaPopup.internalCloseEvents();

            if (jQuery(this).val()) {
                if (!jQuery(this).intlTelInput("isValidNumber")) {
                    var errorCode = jQuery(this).intlTelInput("getValidationError");

                    jQuery(this)
                        .tooltipster({
                            theme: 'tooltipster-light',
                            side: 'right'
                        })
                        .tooltipster('content', telInputErrorMap[errorCode])
                        .tooltipster('open');
                }
            }
        });

        jQuery('.snp-european-phone-input').on('change keyup', function () {
            NinjaPopup.internalCloseEvents();
        });
    }

    var snp_close_by = jQuery('#snppopup-welcome .snp_close').val();
    
    if (jQuery('#snppopup-welcome').length > 0) {
        var snp_open = jQuery('#snppopup-welcome .snp_open').val();
        var snp_open_after = jQuery('#snppopup-welcome .snp_open_after').val();
        var snp_open_inactivity = jQuery('#snppopup-welcome .snp_open_inactivity').val();
        var snp_open_scroll = jQuery('#snppopup-welcome .snp_open_scroll').val();
        var snp_close_scroll = jQuery('#snppopup-welcome .snp_close_scroll').val();
        var snp_op_welcome = false;
        if (snp_open === 'inactivity') {
            var snp_idletime = 0;
            function snp_timerIncrement() {
                snp_idletime++;
                if (snp_idletime > snp_open_inactivity) {
                    window.clearTimeout(snp_idleInterval);
                    snp_open_popup('', '', 'snppopup-welcome', 'welcome');
                }
            }
            
            var snp_idleInterval = setInterval(snp_timerIncrement, 1000);
            jQuery(this).mousemove(function (e) {
                snp_idletime = 0;
            });

            jQuery(this).on('touchmove', function() {
                snp_idletime = 0;
            });

            jQuery(this).keypress(function (e) {
                snp_idletime = 0;
            });
        } else if (snp_open === 'scroll') {
            jQuery(window).scroll(function () {
                var h = jQuery(document).height() - jQuery(window).height();
                var sp = jQuery(window).scrollTop();
                var p = parseInt(sp / h * 100);
                if (p >= snp_open_scroll && snp_op_welcome == false) {
                    snp_open_popup('', '', 'snppopup-welcome', 'welcome');
                    snp_op_welcome = true;
                }
            });
        } else if (snp_open === 'spend_time') {
            snp_spend_time();
        } else {
            if (snp_open_after) {
                snp_timer_o = setTimeout("snp_open_popup('','','snppopup-welcome','welcome');", snp_open_after * 1000);
            } else {
                snp_open_popup('', '', 'snppopup-welcome', 'welcome');
            }
        }
    }

    if (jQuery('#snppopup-exit').length > 0) {
        var snp_show_on_exit = jQuery('#snppopup-exit .snp_show_on_exit').val();
        var snp_woocommerce_cart_contents = parseInt(jQuery('#snp_woocommerce_cart_contents').val());
        if (snp_show_on_exit == 2) {
            jQuery("a").click(function () {
                if(jQuery(this).hasClass('noexitpopup')) {
                    snp_is_internal_link = true;
                } else if (jQuery(this).hasClass('snppopup')) {
                    return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                } else {
                    var url = jQuery(this).attr("href");
                    if(url === undefined) {
                        snp_is_internal_link = true;
                    } else { 
                        if (url.slice(0, 1) == "#") {
                            return;
                        }

                        if (url.length > 0 && !snp_hostname.test(url) && snp_http.test(url)) {
                            if (jQuery.inArray(url, snp_excluded_urls) == -1) {
                                snp_is_internal_link = false;
                            } else {
                                snp_is_internal_link = true;
                            }
                        } else {
                            snp_is_internal_link = true;
                        }
                    }
                }
            });

            jQuery(window).bind('beforeunload', function (e) {
                if (Cookies.get(snp_cookie_prefix + 'snp_snppopup-exit') == 1 && snp_ignore_cookies == false) {
                    return;
                }
                
                if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen) {
                    return;
                }

                if (snp_is_internal_link == true) {
                    return;
                }
                
                setTimeout("snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'snppopup-exit','exit');", 1000);
                
                var e = e || window.event;
                if (e) {
                    e.returnValue = jQuery('#snppopup-exit .snp_exit_js_alert_text').val().replace(/\\n/g, "\n");
                }
                return jQuery('#snppopup-exit .snp_exit_js_alert_text').val().replace(/\\n/g, "\n");
            });
        } else if (snp_show_on_exit == 3) {
            var snp_op_exit = false;
            jQuery(document).bind('mouseleave', function (e) {
                var rightD = jQuery(window).width() - e.pageX;
                if (snp_op_exit == false && e.clientY <= 0) {
                    snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                    snp_op_exit = true;
                }
            });
        } else if (snp_show_on_exit == 5) {
            var snp_op_exit = false;
            jQuery(document).bind('mouseleave', function (e) {
                var rightD = jQuery(window).width() - e.pageX;
                if (snp_op_exit == false && e.clientY <= 0 && snp_woocommerce_cart_contents > 0) {
                    snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                    snp_op_exit = true;
                }
            });
        } else if (snp_show_on_exit == 4) {
            var snp_exit_scroll_down = jQuery('#snppopup-exit .snp_exit_scroll_down').val();
            var snp_exit_scroll_up = jQuery('#snppopup-exit .snp_exit_scroll_up').val();

            var dtPercentDown = new DialogTrigger(function() {
                var dtPercentUp = new DialogTrigger(snp_open_exit_popup, {
                    trigger: 'scrollUp',
                    percentUp: snp_exit_scroll_up
                });
            }, {
                trigger: 'scrollDown',
                percentDown: snp_exit_scroll_down
            });
        } else {
            if (snp_use_in_all) {
                jQuery("a:not(.snppopup)").click(function () {
                    if (jQuery(this).hasClass('snppopup')) {
                        return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                    } else {
                        var url = jQuery(this).attr("href");
                        if (!snp_hostname.test(url) && url.slice(0, 1) != "#" && snp_http.test(url)) {
                            if (jQuery.inArray(url, snp_excluded_urls) == -1) {
                                return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                            }
                        }
                    }
                });
            }

            jQuery("a.snppopup").click(function () {
                return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
            });
        }
    }

    jQuery(window).scroll(function () {
        var h = jQuery(document).height() - jQuery(window).height();
        var sp = jQuery(window).scrollTop();
        var p = parseInt(sp / h * 100);

        if (snp_close_by == 'scroll' && snp_close_scroll && p >= snp_close_scroll) {
            snp_close();
        }
    });

    jQuery('.snp-submit').click(function () {
        jQuery(this).blur();
    });

    jQuery('.snp_nextstep').click(function () {
        var nextstep = jQuery(this).data('nextstep');
        var p = jQuery(this).parents('.snp-fb');
        p.find('.snp-step-show').fadeOut(function () {
            jQuery(this).removeClass('snp-step-show');
            p.find('.snp-step-' + nextstep).fadeIn(function () {
                jQuery(this).addClass('snp-step-show');
            })
        });

        snp_ga('popup', 'step' + nextstep, jQuery('#snp_popup_id').val());

        jQuery.event.trigger({
            'type': "ninja_popups_gotostep",
            'popup_id': jQuery('#snp_popup_id').val(),
            'step': nextstep
        });
        
        return false;
    });

    jQuery('.snp-overlay').click(function () {
        if (jQuery(this).attr('data-close') == 'yes') {
            snp_close();
        }
    });

    jQuery('.snp-bld-gotostep').click(function () {
        var nextstep = jQuery(this).data('step');
        var popup_ID = jQuery(this).parents('.snppopup').find('.snp_popup_id').val();
        snp_bld_gotostep(popup_ID, nextstep);
        return false;
    });

    jQuery(document).on('click', "a.snppopup-content, a[href^='#ninja-popup-']", function(e) {
        e.preventDefault();
        
        var id = jQuery(this).attr('rel');
        if (!id) {
            id = jQuery(this).attr('href').replace('#ninja-popup-', '');
        }

        if (id) {
            return snp_open_popup('', '', 'snppopup-content-' + id, 'content');
        }
    });

    //Open as iframe
    jQuery(document).on('click', "a.snppopup-iframe-content, a[href^='#ninja-popup-iframe-']", function(e) {
        e.preventDefault();

        var id = jQuery(this).attr('rel');
        if (!id) {
            id = jQuery(this).attr('href').replace('#ninja-popup-iframe-', '');
        }

        var src = jQuery(this).data('ninja-popup-href');

        if (id) {
            return snp_open_popup('', '', 'snppopup-content-' + id, 'iframe', {
                'src': src
            });
        }
    });

    jQuery(document).on("theme_image_image_link", function(event) {
        snp_onconvert('optin', false, false, false);
    });
});

function snp_open_exit_popup()
{
    snp_open_popup('', '', 'snppopup-exit', 'exit');
}

function snp_resp(nextstep)
{
    var popup = jQuery('#snp_popup').val();
    if (popup !== undefined) {
        var p = jQuery('#' + popup).find('.snp-builder');
        if (nextstep === false) {
            var cur_step = p.find('.snp-bld-showme');
        } else {
            var cur_step = p.find('#snp-bld-step-' + nextstep);
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
    }
}

function snp_open_select_link(el, blank) {
    var popup = jQuery('#snp_popup').val();
    var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    var ab_ID = jQuery('.snp-pop-' + jQuery('#snp_popup_id').val()).find('.snp_popup_ab_id').val();
    if (ab_ID === undefined) {
        ab_ID = false;
    }
    var selectValues = [];
    var cleanRedirectRules = jQuery(el).data('redirect-rules');
    var url = '';

    var values = jQuery.map(jQuery('.snp-el-bld-select option:selected'), function(e) {
        if (cleanRedirectRules[e.value]) {
            url = cleanRedirectRules[e.value];
        }
        return e.value;
    });

    if (jQuery(el).data('set-cookie') == 'yes') {
        snp_onconvert('redirect', false, ab_ID, true);
    }

    if (url) {
        if (blank) {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    }

    jQuery.event.trigger({
        'type': "ninja_popups_open_link",
        'popup_id': popup_ID
    });

    return false;
}

function snp_open_link(el, blank) {
    var popup = jQuery('#snp_popup').val();
    var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    var ab_ID = jQuery('.snp-pop-' + jQuery('#snp_popup_id').val()).find('.snp_popup_ab_id').val();
    if (ab_ID === undefined) {
        ab_ID = false;
    }

    if (jQuery(el).data('set-cookie') == 'yes') {
        snp_onconvert('optin', false, ab_ID, true);
    }

    if (blank) {
        window.open(jQuery(el).data('url'), '_blank');
    } else {
        window.location.href = jQuery(el).data('url');
    }

    jQuery.event.trigger({
        'type': "ninja_popups_open_link",
        'popup_id': popup_ID
    });
    
    return false;
}

function _snp_bld_open(ID)
{
    var step1 = jQuery('.snp-pop-' + ID + ' .snp-builder').not('.snp-pos-static').addClass('snp-bld-showme').find('.snp-bld-step-1');
    step1.addClass('snp-bld-showme');
    
    snp_start_video(step1);
    snp_init_map(step1);

    if (step1.attr('data-overlay') != 'disabled') {
        jQuery('.snp-pop-' + ID + ' .snp-overlay').addClass('snp-overlay-show');
    }
    
    if (jQuery("#snp-bld-step-bg-1").length > 0) {
        jQuery("#snp-bld-step-bg-1").mb_YTPlayer();
    }

    snp_resp(1);
}

function _snp_bld_close(ID)
{
    var p = jQuery('.snp-pop-' + ID + ' .snp-builder').not('.snp-pos-static');
    var cur_step = p.find('.snp-bld-showme');
    snp_stop_video(cur_step);
    if (cur_step.data('animation-close') !== undefined) {
        cur_step.removeClass('animated ' + cur_step.attr('data-animation'));
        cur_step.addClass('animated ' + cur_step.attr('data-animation-close')).off('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            jQuery(this).removeClass('animated ' + jQuery(this).attr('data-animation-close'));
            jQuery(this).removeClass('snp-bld-showme');
        });
    } else {
        cur_step.removeClass('snp-bld-showme');
    }
    p.removeClass('snp-bld-showme');
    jQuery('.snp-pop-' + ID + ' .snp-overlay').removeClass('snp-overlay-show');
}
