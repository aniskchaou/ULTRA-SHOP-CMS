var isLocalStorageSupported = function () {
    var t = "test", e = window.sessionStorage;
    try {
        return e.setItem(t, "1"), e.removeItem(t), !0
    } catch (t) {
        return !1
    }
};
!function (t) {
    t.WR = t.WR || {}, t.function_rotate_device = {}, t.fn.WR_ImagesLoaded = function (e) {
        var a = this.find("img").toArray().map(function (t) {
            return t.src
        });
        if (a.length) {
            var i = 0;
            t(a).each(function (t, o) {
                var s, n, r;
                s = o, n = function () {
                    ++i == a.length && e()
                }, (r = new Image).onload = n, r.src = s
            })
        } else e()
    }, t.fn.WR_ImagesLazyload = function (e, a) {
        var i, o = t(window), s = e || 0, n = window.devicePixelRatio > 1 ? "data-src-retina" : "data-src-lazyload",
            r = this;

        function c() {
            var e = r.filter(function () {
                var e = t(this);
                if (!e.is(":hidden")) {
                    var a = o.scrollTop(), i = a + o.height(), n = e.offset().top;
                    return n + e.height() >= a - s && n <= i + s
                }
            });
            i = e.trigger("WR_ImagesLazyload"), r = r.not(i)
        }

        return this.one("WR_ImagesLazyload", function () {
            var t = this.getAttribute(n);
            (t = t || this.getAttribute("data-src-lazyload")) && (this.setAttribute("src", t), "function" == typeof a && a.call(this))
        }), o.on("scroll.WR_ImagesLazyload resize.WR_ImagesLazyload lookup.WR_ImagesLazyload", c), c(), this
    };
    var e = function () {
        return /Android|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent || navigator.vendor || window.opera)
    };

    function a(t) {
        if ("string" != typeof t || -1 == t.search("&")) return !1;
        var e, a, i, o = {};
        for (e = t.split("&"), i = 0; i < e.length; i++) o[(a = e[i].split("="))[0]] = decodeURIComponent(a[1]);
        return o
    }

    function i() {
        var e;
        t(".hb-cart.sidebar").click(function () {
            var e = t(this), a = e.find(".cart-control-sidebar"), i = e.find(".hb-minicart")[0].outerHTML;
            if (t("body > .hb-cart-outer").length || t("body").append('<div class="hb-cart-outer"></div>'), t("body > .hb-cart-outer").html('<span class="wr-close-mobile"><span></span></span>' + i), !t("body > .overlay-sidebar").length) {
                var o = t('<div class="overlay-sidebar"></div>').click(function () {
                    !function (e) {
                        e.removeClass("active");
                        var a = e.attr("data-animation"), i = e.attr("data-position"),
                            o = t(".hb-cart-outer .hb-minicart .widget_shopping_cart_content"),
                            s = (t(".active-icon-cart-sidebar"), t(".hb-cart-outer .hb-minicart")),
                            n = t("body > .wrapper-outer");
                        e.animate({opacity: 0}, function () {
                            e.hide()
                        }), setTimeout(function () {
                            t(".hb-cart.sidebar .hb-minicart").html(t("body > .hb-cart-outer .hb-minicart").html()).find("input.edit-number").each(function (e, a) {
                                parseInt(t(a).data("value-old")) && t(a).attr("value") != t(a).data("value-old") && t(a).attr("value", t(a).data("value-old"))
                            }), t("body > .hb-cart-outer").remove(), e.remove(), t("html").removeClass("no-scroll"), n.removeAttr("style")
                        }, 500);
                        var r = s[0].getBoundingClientRect();
                        switch (i) {
                            case"position-sidebar-right":
                                switch (s.animate({right: "-" + r.width + "px"}), "sidebar-push" != a && "sidebar-fall-down" != a && "sidebar-fall-up" != a || n.animate({right: "0px"}), a) {
                                    case"sidebar-slide-in-on-top":
                                    case"sidebar-push":
                                        break;
                                    case"sidebar-fall-down":
                                        o.animate({top: "-300px"});
                                        break;
                                    case"sidebar-fall-up":
                                        o.animate({top: "300px"})
                                }
                                break;
                            case"position-sidebar-left":
                                switch (s.animate({left: "-" + r.width + "px"}), "sidebar-push" != a && "sidebar-fall-down" != a && "sidebar-fall-up" != a || n.animate({left: "0px"}), a) {
                                    case"sidebar-slide-in-on-top":
                                    case"sidebar-push":
                                        break;
                                    case"sidebar-fall-down":
                                        o.animate({top: "-300px"});
                                        break;
                                    case"sidebar-fall-up":
                                        o.animate({top: "300px"})
                                }
                                break;
                            case"position-sidebar-top":
                                switch (t(".hb-cart-outer .hb-minicart .action-top-bottom").remove(), "sidebar-slide-in-on-top" != a && "sidebar-push" != a && "sidebar-fall-down" != a || s.animate({top: "-120px"}), "sidebar-push" != a && "sidebar-fall-down" != a && "sidebar-fall-up" != a || n.animate({top: "0px"}), a) {
                                    case"sidebar-slide-in-on-top":
                                    case"sidebar-push":
                                        break;
                                    case"sidebar-fall-down":
                                        o.animate({top: "-150px", opacity: 0});
                                        break;
                                    case"sidebar-fall-up":
                                        s.css("overflow", "hidden").animate({top: "-120px"}, function () {
                                            t(this).css("overflow", "")
                                        }), o.animate({top: "150px", opacity: 0})
                                }
                                break;
                            case"position-sidebar-bottom":
                                switch (t(".hb-cart-outer .hb-minicart .action-top-bottom").remove(), "sidebar-slide-in-on-top" != a && "sidebar-push" != a && "sidebar-fall-up" != a || s.animate({bottom: "-120px"}), "sidebar-push" != a && "sidebar-fall-down" != a && "sidebar-fall-up" != a || n.animate({bottom: "0px"}), a) {
                                    case"sidebar-slide-in-on-top":
                                    case"sidebar-push":
                                        break;
                                    case"sidebar-fall-down":
                                        s.css("overflow", "hidden").animate({bottom: "-120px"}, function () {
                                            t(this).css("overflow", "initial")
                                        }), o.animate({top: "-150px", opacity: 0});
                                        break;
                                    case"sidebar-fall-up":
                                        o.animate({top: "150px", opacity: 0})
                                }
                        }
                        setTimeout(function () {
                            n.removeAttr("style"), s.removeAttr("style"), t(".hb-cart-outer .hb-minicart").removeAttr("style"), o.removeAttr("style"), e.removeAttr("style")
                        }, 500)
                    }(t(this))
                });
                t("body").append(o)
            }
            t("html").addClass("no-scroll");
            var s = a.attr("data-animation"), n = a.attr("data-position"), r = t("body > .overlay-sidebar"),
                c = t("body > .wrapper-outer"), l = t(".hb-cart-outer .hb-minicart"),
                d = t(".hb-cart-outer .hb-minicart .widget_shopping_cart_content");
            r.addClass("active").attr("data-animation", s).attr("data-position", n), l.attr("style", ""), c.attr("style", ""), r.attr("style", ""), d.attr("style", ""), r.css({display: "block"}).animate({opacity: 1}), l.addClass(n), l.css("opacity", 1);
            var u = l[0].getBoundingClientRect(), p = function () {
                var e = t(".hb-cart-outer .widget_shopping_cart_content > .cart_list-outer"),
                    a = t(".hb-cart-outer .hb-minicart").width(), i = e.width(),
                    o = t(".hb-cart-outer .widget_shopping_cart_content > .price-checkout").outerWidth(!0);
                if (a < i + o) {
                    var s = t(".hb-cart-outer"), n = t(".hb-cart-outer .cart_list-outer");
                    e = t(".hb-cart-outer .cart_list");
                    s.addClass("cart-slider");
                    var r = a - o, c = e.width(), l = parseInt((c - r + 50) / 80) + 1;
                    s.attr("data-items", l), n.width(r), n.prepend('<div class="control"><div class="prev control-item"><div class="prev-inner control-inner"></div></div><div class="disabled next control-item"><div class="next-inner control-inner"></div></div></div>')
                }
            };
            switch (n) {
                case"position-sidebar-right":
                    switch (l.css({
                        visibility: "visible",
                        right: "-" + u.width + "px"
                    }).animate({right: "0px"}), "sidebar-push" != s && "sidebar-fall-down" != s && "sidebar-fall-up" != s || c.css({
                        position: "relative",
                        right: "0px"
                    }).animate({right: u.width + "px"}), s) {
                        case"sidebar-slide-in-on-top":
                        case"sidebar-push":
                            break;
                        case"sidebar-fall-down":
                            d.css({position: "relative", top: "-300px"}).animate({top: "0px"});
                            break;
                        case"sidebar-fall-up":
                            d.css({position: "relative", top: "300px"}).animate({top: "0px"})
                    }
                    break;
                case"position-sidebar-left":
                    switch (l.css({
                        visibility: "visible",
                        left: "-" + u.width + "px"
                    }).animate({left: "0px"}), "sidebar-push" != s && "sidebar-fall-down" != s && "sidebar-fall-up" != s || c.css({
                        position: "relative",
                        left: "0px"
                    }).animate({left: u.width + "px"}), s) {
                        case"sidebar-slide-in-on-top":
                        case"sidebar-push":
                            break;
                        case"sidebar-fall-down":
                            d.css({position: "relative", top: "-300px"}).animate({top: "0px"});
                            break;
                        case"sidebar-fall-up":
                            d.css({position: "relative", top: "300px"}).animate({top: "0px"})
                    }
                    break;
                case"position-sidebar-top":
                    switch (l.addClass("active"), "sidebar-slide-in-on-top" != s && "sidebar-push" != s && "sidebar-fall-down" != s || l.css({
                        visibility: "visible",
                        transform: "translate(0%, -100%)"
                    }).animate({transform: "translate(0%, 0%)"}), p(), s) {
                        case"sidebar-slide-in-on-top":
                        case"sidebar-push":
                            break;
                        case"sidebar-fall-down":
                            d.css({position: "relative", top: "-150px", opacity: 0}).animate({top: "0px", opacity: 1});
                            break;
                        case"sidebar-fall-up":
                            l.css({
                                overflow: "hidden",
                                visibility: "visible",
                                transform: "translate(0%, -100%)"
                            }).animate({transform: "translate(0%, 0%)"}, function () {
                                t(this).css("overflow", "")
                            }), d.css({position: "relative", top: "150px", opacity: 0}).animate({
                                top: "0px",
                                opacity: 1
                            })
                    }
                    break;
                case"position-sidebar-bottom":
                    switch ("sidebar-slide-in-on-top" != s && "sidebar-push" != s && "sidebar-fall-up" != s || l.css({
                        visibility: "visible",
                        transform: "translate(0%, 100%)"
                    }).animate({transform: "translate(0%, 0%)"}), p(), s) {
                        case"sidebar-slide-in-on-top":
                        case"sidebar-push":
                            break;
                        case"sidebar-fall-down":
                            l.css({
                                overflow: "hidden",
                                visibility: "visible",
                                transform: "translate(0%, 100%)"
                            }).animate({transform: "translate(0%, 0%)"}, function () {
                                l.css("overflow", "")
                            }), d.css({position: "relative", top: "-150px", opacity: 0}).animate({
                                top: "0px",
                                opacity: 1
                            });
                            break;
                        case"sidebar-fall-up":
                            d.css({position: "relative", top: "150px", opacity: 0}).animate({top: "0px", opacity: 1})
                    }
            }
        }), t.fn.hoverIntent && t("body").hoverIntent({
            over: function () {
                var e = t(this), a = e.find(".hb-minicart-outer:first"),
                    i = e.find(".link-cart:first")[0].getBoundingClientRect();
                a.removeAttr("style");
                var o = a[0].getBoundingClientRect(), s = t(window).width(), n = t(window).height(),
                    r = s > 1024 ? parseInt(WR_Data_Js.offset) : 0;
                if (s < o.right + 5 + r) {
                    var c = o.right + 5 - s + r;
                    a.css("left", -c + "px")
                } else o.left < 5 + r && a.css("left", "5px");
                e.addClass("active-dropdown");
                var l = "empty" == e.attr("data-margin-top") ? e.attr("data-margin-top") : parseInt(e.attr("data-margin-top"));
                if (e.closest(".sticky-row-scroll").length || "empty" == l) {
                    e[0].getBoundingClientRect();
                    var d = e.closest(e.closest(".sticky-row-scroll").length ? ".sticky-row" : ".hb-section-outer")[0].getBoundingClientRect(),
                        u = parseInt(d.bottom - i.bottom), p = parseInt(d.bottom - i.top);
                    0 == e.find(".hover-area").length && e.append('<span class="hover-area" style="height:' + u + 'px"></span>'), a.css("top", p)
                } else if (l > 0) {
                    0 == e.find(".hover-area").length && e.append('<span class="hover-area" style="height:' + l + 'px"></span>');
                    e[0].getBoundingClientRect();
                    a.css("top", l + i.height)
                }
                if ((o = a[0].getBoundingClientRect()).bottom > n) {
                    var h = o.height - (o.bottom - n) - 5;
                    a.css({overflowY: "scroll", height: h})
                }
            }, out: function () {
                var e = t(this);
                e.removeClass("active-dropdown"), e.find(".hover-area").remove()
            }, timeout: 0, sensitivity: 1, interval: 0, selector: ".hb-cart.dropdown"
        }), t("body").on("click", ".hb-cart-outer.cart-slider .control .prev", function () {
            var e = t(this), a = e.closest(".hb-cart-outer"), i = parseInt(a.attr("data-items"));
            if (!(a.attr("data-item") >= i)) {
                var o = void 0 == a.attr("data-item") ? 1 : parseInt(a.attr("data-item")) + 1, s = a.find(".cart_list");
                a.attr("data-item", o), s.css("right", -80 * o), i == o && e.addClass("disabled"), t(".hb-cart-outer.cart-slider .control .next").removeClass("disabled")
            }
        }), t("body").on("click", ".hb-cart-outer.cart-slider .control .next", function () {
            var e = t(this), a = e.closest(".hb-cart-outer");
            if (void 0 != a.attr("data-item") && 0 != a.attr("data-item")) {
                var i = parseInt(a.attr("data-item")) - 1, o = a.find(".cart_list");
                a.attr("data-item", i), 0 == i && e.addClass("disabled"), t(".hb-cart-outer.cart-slider .control .prev").removeClass("disabled"), o.css("right", -80 * i)
            }
        }), t("body").on("click", ".widget_shopping_cart_content .remove-item .remove", function (e) {
            e.preventDefault();
            var a, i = t(this), o = i.closest(".hb-minicart"), s = i.attr("data-product_id");

            function n() {
                t.ajax({
                    type: "POST",
                    url: WRAjaxURL,
                    data: {action: "wr_product_remove", cart_item_key: s},
                    success: function (e) {
                        if (e) {
                            if (e = t.parseJSON(e), o.hasClass("position-sidebar-top") || o.hasClass("position-sidebar-bottom")) {
                                var a = i.closest(".cart-slider");
                                if (a.length) {
                                    var n = parseInt(a.attr("data-items"));
                                    a.attr("data-items", n - 1), 1 == n && (a.removeClass("cart-slider"), a.find(".cart_list-outer").removeAttr("style"))
                                }
                                t('li[data-key="' + s + '"]').hide(300, function () {
                                    t('li[data-key="' + s + '"]').remove()
                                })
                            } else t('li[data-key="' + s + '"]').slideUp(300, function () {
                                t('li[data-key="' + s + '"]').remove()
                            });
                            t(".mini-price").length && t(".mini-price").html(e.price_total), t(".hb-cart .cart-control .count").length && t(".hb-cart .cart-control .count").html(e.count_product), 0 == e.count_product && (t(".hb-minicart .total").length && t(".hb-minicart .total").hide(), t(".hb-minicart .buttons").length && t(".hb-minicart .buttons").hide(), t(".hb-minicart .product_list_widget .empty").length || t(".hb-minicart .product_list_widget").append('<li class="empty">' + e.empty + "</li>"))
                        }
                    }
                })
            }

            i.addClass("loading"), a = setTimeout(n, 500), t(document).ajaxComplete(function (t, e, i) {
                i.url.search("wc-ajax=remove_from_cart") > -1 && (a && clearTimeout(a), n())
            })
        }), t(document).ajaxComplete(function (e, i, o) {
            var s = o.url;
            void 0 !== o.data && o.data;
            if (-1 != s.search("wc-ajax=add_to_cart")) {
                if (!isLocalStorageSupported()) return window.location.reload();
                if (void 0 != o.data && void 0 != i.responseJSON && void 0 != i.responseJSON.cart_hash) {
                    var n = a(o.data);
                    t.ajax({
                        type: "POST",
                        url: WRAjaxURL,
                        data: {action: "wr_add_to_cart_message", product_id: n.product_id},
                        success: function (e) {
                            if (void 0 == e.message) return !1;
                            t("body > .wr-notice-cart-outer").remove();
                            var a = '<div class="wr-notice-cart-outer pf ptr"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + e.message + "</div></div></div>";
                            t("body").append(a);
                            var i = t('<span class="close-notice"></span>').click(function () {
                                t(this).closest(".wr-notice-cart-outer").removeClass("active")
                            });
                            t("body .wr-notice-cart").prepend(i), setTimeout(function () {
                                t("body > .wr-notice-cart-outer").addClass("active")
                            }, "10"), setTimeout(function () {
                                t("body > .wr-notice-cart-outer").removeClass("active")
                            }, "5000")
                        }
                    })
                } else void 0 != o.data && void 0 != i.responseJSON && 1 == i.responseJSON.error && t.ajax({
                    type: "POST",
                    url: WRAjaxURL,
                    data: {action: "wr_add_to_cart_error"},
                    success: function (e) {
                        if (void 0 == e.message) return !1;
                        t("body > .wr-notice-cart-outer").remove();
                        var a = '<div class="wr-notice-cart-outer pf ptr error"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + e.message + "</div></div></div>";
                        t("body").append(a);
                        var i = t('<span class="close-notice"></span>').click(function () {
                            t(this).closest(".wr-notice-cart-outer").removeClass("active")
                        });
                        t("body .wr-notice-cart").prepend(i), setTimeout(function () {
                            t("body > .wr-notice-cart-outer").addClass("active")
                        }, "10"), setTimeout(function () {
                            t("body > .wr-notice-cart-outer").removeClass("active")
                        }, "5000")
                    }
                })
            }
            if (void 0 != o.data && "add_to_wishlist" == (n = a(o.data)).action) {
                t("body > .wr-notice-cart-outer").remove();
                var r = '<div class="wr-notice-cart-outer pf ptr"><div class="wr-notice-cart"><div class="icon-notice"><i class="fa fa-heart-o"></i></div><div class="text-notice"><div> ' + i.responseJSON.message + ' </div><a class="db" href="' + i.responseJSON.wishlist_url + '">' + WR_Data_Js["View Wishlist"] + "</a></div></div></div>";
                t("body").append(r);
                var c = t('<span class="close-notice"></span>').click(function () {
                    t(this).closest(".wr-notice-cart-outer").removeClass("active")
                });
                t("body .wr-notice-cart").prepend(c), setTimeout(function () {
                    t("body > .wr-notice-cart-outer").addClass("active")
                }, "10"), setTimeout(function () {
                    t("body > .wr-notice-cart-outer").removeClass("active")
                }, "5000")
            }
            if (void 0 != o.data) {
                n = a(o.data);
                -1 != s.search("wc-ajax=add_to_cart") && void 0 != n.remove_from_wishlist_after_add_to_cart && (t(".woocommerce-message").hide(), setTimeout(function () {
                    t(".wishlist_table tbody tr").length <= 1 && (t(".wishlist_table").remove(), t("#yith-wcwl-form").addClass("empty"))
                }, 1e3))
            }
        }), t(document.body).on("wc_fragments_loaded wc_fragments_refreshed added_to_cart", function () {
            if (isLocalStorageSupported() && void 0 !== window.wc_cart_fragments_params && void 0 !== wc_cart_fragments_params.fragment_name) {
                var e = t.parseJSON(sessionStorage.getItem(wc_cart_fragments_params.fragment_name));
                e && void 0 !== e.wr_total_price && void 0 !== e.wr_count_item && (t(".hb-cart .cart-control .count").html(e.wr_count_item), t(".mini-price").html(e.wr_total_price))
            }
        }), t("body").on("blur change", ".widget_shopping_cart_content .edit-number", function () {
            var a = t(this), i = a.closest(".mini_cart_item"), o = i.attr("data-key"), s = a.val(),
                n = (a.closest(".hb-minicart").attr("data-slidebar-position"), a.attr("data-max")),
                r = a.attr("data-value-old"), c = i.find(".multiplication");
            c.removeClass("loading"), e && clearTimeout(e), e = setTimeout(function () {
                if ("" != s && 0 != s && r != s) if (n && parseInt(s) > parseInt(n)) {
                    var e = (e = WR_Data_Js.wr_error_cannot_add).replace(/%d/g, n);
                    a.focus();
                    a.val(r);
                    alert(e);
                } else c.addClass("loading"), t.ajax({
                    type: "POST",
                    url: WR_CART_URL,
                    data: {"wr-action-cart": "update_cart", cart_item_key: o, cart_item_number: s},
                    success: function (e) {
                        0 == e.count_product && (t(".hb-minicart .total").length && t(".hb-minicart .total").hide(), t(".hb-minicart .buttons").length && t(".hb-minicart .buttons").hide(), t(".hb-minicart .product_list_widget .empty").length || t(".hb-minicart .product_list_widget").append('<li class="empty">' + e.empty + "</li>")), t(".mini-price").length && t(".mini-price").html(e.price_total), t(".hb-cart .cart-control .count").length && t(".hb-cart .cart-control .count").html(e.count_product), c.removeClass("loading"), a.attr("data-value-old", s)
                    }
                })
            }, 50)
        }), ("no" != WR_Data_Js.ajax_add_to_cart_single || parseInt(WR_Data_Js.buy_now_button_enabled)) && (t(window).load(function () {
            var e, a = document.querySelector("form.cart");
            if (a) {
                e = t._data(a, "events"), t.WR.form_add_to_cart_events = {};
                for (var i in e) if (!(["click", "submit"].indexOf(i) < 0)) {
                    t.WR.form_add_to_cart_events[i] = [];
                    for (var o = 0; o < e[i].length; o++) {
                        if ("click" == i) {
                            var s = t(a).find(e[i][o].selector);
                            if (s[0] && "submit" != s[0].type) continue
                        }
                        t.WR.form_add_to_cart_events[i].push({handler: e[i][o].handler, selector: e[i][o].selector})
                    }
                    o = 0;
                    for (var n = t.WR.form_add_to_cart_events[i].length; o < n; o++) t("form.cart").off(i, t.WR.form_add_to_cart_events[i][o].selector, t.WR.form_add_to_cart_events[i][o].handler)
                }
                t("form.cart").on("submit", function (e) {
                    t.WR.form_add_to_cart_processing && e.preventDefault()
                })
            }
        }), t("body").on("click", ".quickview-modal form.cart .wr_single_add_to_cart_ajax, .product-type-subscription .cart .single_add_to_cart_button", function (e) {
            e.preventDefault(), e.stopPropagation();
            var a = t(this);
            window.wr_add_to_cart_ajax(a, e)
        }), t("form.cart .wr_single_add_to_cart_ajax, .product-type-subscription .cart .single_add_to_cart_button").click(function (e) {
            var a = t(this);
            e.preventDefault(), t.WR.form_add_to_cart_processing ? e.stopPropagation() : (t.WR.form_add_to_cart_processing = !0, "undefined" != typeof yith_wapo_general ? setTimeout(function () {
                yith_wapo_general.do_submit && window.wr_add_to_cart_ajax(a, e)
            }, 100) : window.wr_add_to_cart_ajax(a, e))
        }), t(".floating-add-to-cart .floating_button").click(function (e) {
            e.preventDefault(), e.stopPropagation();
            t(this);
            t("form.cart .single_add_to_cart_button").trigger("click")
        }), window.wr_add_to_cart_ajax = function (e, a, i) {
            var o = e.closest("form"), s = o.serializeArray(), n = t(".floating-add-to-cart .floating_button");
            e.prop("disabled", !0), e.addClass("loading"), e.removeClass("added error"), n.addClass("loading"), n.removeClass("added error"), o.find('input[name="wr-action-cart"]').length || o.append('<input type="hidden" name="wr-action-cart" value="add_to_cart" />');
            for (var r in t.WR.form_add_to_cart_events) for (var c = 0; c < t.WR.form_add_to_cart_events[r].length; c++) if (t.WR.form_add_to_cart_events[r][c].handler) {
                var l = e.closest("form");
                t.WR.form_add_to_cart_events[r][c].selector && (l = l.find(t.WR.form_add_to_cart_events[r][c].selector)), l.length && t.WR.form_add_to_cart_events[r][c].handler.call(l[0], a)
            }
            if (isLocalStorageSupported()) {
                var d = t("iframe#wr_nitro_add_to_cart_iframe");
                d.length || (d = t("<iframe />", {
                    id: "wr_nitro_add_to_cart_iframe",
                    name: "wr_nitro_add_to_cart_iframe",
                    src: "about:blank"
                }).css({
                    position: "absolute",
                    top: e.offset().top + "px",
                    left: e.offset().left + "px",
                    width: e.outerWidth() + "px",
                    height: e.outerHeight() + "px",
                    opacity: 0,
                    visibility: "hidden"
                }), t(document.body).append(d)), d.show().off("load").on("load", function (a) {
                    !function (a) {
                        var o = new RegExp('<script id="tp-notice-html"[^>]*>(\\{"status":[^\\r\\n]+\\})<\/script>');
                        if (a = a.match(o)) {
                            if (a = t.parseJSON(a[1]), "function" == typeof i) return i(a);
                            if ("true" == a.status) {
                                if (void 0 != a.redirect) return void(window.location = a.redirect);
                                t("body > .wr-notice-cart-outer").remove();
                                var r = a.notice,
                                    c = (r = r.replace(/&quot;/g, '"')).match(/<a[^>]+>.?[^<]*<\/a[^>]*>/)[0];
                                r.replace(/(<a[^>]+>.?[^<]*<\/a[^>]*>)(.*$)/), r = (r = r.replace(/(<a[^>]+>.?[^<]*<\/a[^>]*>)(.*$)/, "$2")).replace(/(".?[^"]*")(.*)/, "<div><b>$1</b>$2</div>"), a.notice = c + r;
                                var l = '<div class="wr-notice-cart-outer pf ptr"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + a.notice + "</div></div></div>";
                                t("body").append(l);
                                var d = t('<span class="close-notice"></span>').click(function () {
                                    t(this).closest(".wr-notice-cart-outer").removeClass("active")
                                });
                                t("body .wr-notice-cart").prepend(d), setTimeout(function () {
                                    t("body > .wr-notice-cart-outer").addClass("active")
                                }, 10), setTimeout(function () {
                                    t("body > .wr-notice-cart-outer").removeClass("active")
                                }, 5e3), e.addClass("added"), n.addClass("added"), t(document.body).trigger("updated_wc_div")
                            } else "false" == a.status && (t("body > .wr-notice-cart-outer").remove(), l = '<div class="wr-notice-cart-outer pf ptr error"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + a.notice + "</div></div></div>", t("body").append(l), d = t('<span class="close-notice"></span>').click(function () {
                                t(this).closest(".wr-notice-cart-outer").removeClass("active")
                            }), t("body .wr-notice-cart").prepend(d), setTimeout(function () {
                                t("body > .wr-notice-cart-outer").addClass("active")
                            }, 10), setTimeout(function () {
                                t("body > .wr-notice-cart-outer").removeClass("active")
                            }, 5e3), e.addClass("error"), n.addClass("error"));
                            e.removeClass("loading"), e.prop("disabled", !1), n.removeClass("loading"), n.prop("disabled", !1)
                        } else {
                            for (var u, p = 0; p < s.length; p++) if ("add-to-cart" == s[p].name) {
                                u = s[p].value;
                                break
                            }
                            u && t.ajax({
                                type: "POST",
                                url: WRAjaxURL,
                                data: {action: "wr_add_to_cart_message", url_only: "true", product_id: u},
                                success: function (t) {
                                    void 0 == t || (window.location = t)
                                }
                            })
                        }
                    }(void 0 !== a.target.contentDocument.documentElement.outerHTML ? a.target.contentDocument.documentElement.outerHTML : a.target.contentDocument.documentElement.innerHTML), d.hide()
                }), o.attr("target", "wr_nitro_add_to_cart_iframe")
            } else o.append('<input type="hidden" name="add_to_cart_normally" value="1" />');
            t.WR.form_add_to_cart_processing = !1, o.submit()
        }), t("body").on("show_variation", "form.cart.variations_form", function (e, a, i) {
            e.preventDefault();
            var o = t(this);
            i ? (o.find(".single_buy_now").removeAttr("disabled").removeClass("disabled"), o.find(".single_add_to_cart_button").removeAttr("disabled").removeClass("disabled"), o.find(".woocommerce-variation-add-to-cart").removeAttr("disabled").removeClass("disabled"), t(".floating-add-to-cart button").removeAttr("disabled"), o.find(".single_buy_now").removeClass("wr-notice-tooltip"), o.find(".single_buy_now .notice-tooltip").remove(), o.find(".single_add_to_cart_button").removeClass("wr-notice-tooltip"), o.find(".single_add_to_cart_button .notice-tooltip").remove(), o.find(".woocommerce-variation-add-to-cart").removeClass("wr-notice-tooltip"), o.find(".woocommerce-variation-add-to-cart .notice-tooltip").remove(), t(".floating-add-to-cart button").removeClass("wr-notice-tooltip"), t(".floating-add-to-cart button .notice-tooltip").remove()) : (o.find(".single_buy_now").attr("disabled", "disabled").addClass("disabled"), o.find(".single_add_to_cart_button").attr("disabled", "disabled").addClass("disabled"), o.find(".woocommerce-variation-add-to-cart").attr("disabled", "disabled").addClass("disabled"), t(".floating-add-to-cart button").attr("disabled", "disabled").addClass("disabled"), 0 == o.find(".single_buy_now .notice-tooltip").length && (o.find(".single_buy_now").addClass("wr-notice-tooltip"), o.find(".single_buy_now").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")), 0 == o.find(".single_add_to_cart_button .notice-tooltip").length && (o.find(".single_add_to_cart_button").addClass("wr-notice-tooltip"), o.find(".single_add_to_cart_button").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")), 0 == o.find(".woocommerce-variation-add-to-cart .notice-tooltip").length && (o.find(".woocommerce-variation-add-to-cart").addClass("wr-notice-tooltip"), o.find(".woocommerce-variation-add-to-cart").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")), 0 == t(".floating-add-to-cart button .notice-tooltip").length && (t(".floating-add-to-cart button").addClass("wr-notice-tooltip"), t(".floating-add-to-cart button").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")))
        }), t("body").on("hide_variation", "form.cart.variations_form", function (e, a, i) {
            e.preventDefault();
            var o = t(this);
            o.find(".single_buy_now").attr("disabled", "disabled").addClass("disabled"), o.find(".single_add_to_cart_button").attr("disabled", "disabled").addClass("disabled"), o.find(".woocommerce-variation-add-to-cart").attr("disabled", "disabled").addClass("disabled"), t(".floating-add-to-cart button").attr("disabled", "disabled").addClass("disabled"), 0 == o.find(".single_buy_now .notice-tooltip").length && (o.find(".single_buy_now").addClass("wr-notice-tooltip"), o.find(".single_buy_now").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")), 0 == o.find(".single_add_to_cart_button .notice-tooltip").length && (o.find(".single_add_to_cart_button").addClass("wr-notice-tooltip"), o.find(".single_add_to_cart_button").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")), 0 == o.find(".woocommerce-variation-add-to-cart .notice-tooltip").length && (o.find(".woocommerce-variation-add-to-cart").addClass("wr-notice-tooltip"), o.find(".woocommerce-variation-add-to-cart").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>")), 0 == t(".floating-add-to-cart button .notice-tooltip").length && (t(".floating-add-to-cart button").addClass("wr-notice-tooltip"), t(".floating-add-to-cart button").append('<span class="notice-tooltip">' + WR_Data_Js.wr_noice_tooltip + "</span>"))
        });
        var i = function (e) {
            var a = function (e) {
                if ("true" == e.status) {
                    var a = WR_Data_Js.checkout_url;
                    2 == parseInt(WR_Data_Js.buy_now_button_action) ? window.location.href = a : (a.indexOf("?") > -1 ? a += "&wr-buy-now=check-out" : a += "?wr-buy-now=check-out", void 0 !== t.fn.magnificPopup && (t.magnificPopup.close(), setTimeout(function () {
                        t.magnificPopup.open({items: {src: a}, type: "iframe", mainClass: "mfp-fade wr-buy-now"})
                    }, 300))), t(document.body).trigger("updated_wc_div")
                } else if ("false" == e.status) {
                    t("body > .wr-notice-cart-outer").remove();
                    var o = '<div class="wr-notice-cart-outer pf ptr error"><div class="wr-notice-cart"><div class="icon-notice"><i class="nitro-icon-cart-9"></i></div><div class="text-notice">' + e.notice + "</div></div></div>";
                    t("body").append(o);
                    var n = t('<span class="close-notice"></span>').click(function () {
                        t(this).closest(".wr-notice-cart-outer").removeClass("active")
                    });
                    t("body .wr-notice-cart").prepend(n), setTimeout(function () {
                        t("body > .wr-notice-cart-outer").addClass("active")
                    }, 10), setTimeout(function () {
                        t("body > .wr-notice-cart-outer").removeClass("active")
                    }, 5e3), i.addClass("error"), s.addClass("error")
                }
                i.removeClass("loading"), i.prop("disabled", !1), s.removeClass("loading"), s.prop("disabled", !1)
            };
            if (!0 === e) return a({status: "true"});
            e.preventDefault(), e.stopPropagation();
            var i = t(this), o = i.closest("form"), s = t(".floating-add-to-cart .floating_button"), n = function () {
                isLocalStorageSupported() || o.append('<input type="hidden" name="buy_now" value="1" />'), window.wr_add_to_cart_ajax(i, e, a)
            };
            1 == parseInt(WR_Data_Js.buy_now_checkout_type) ? (i.prop("disabled", !0), i.addClass("loading"), i.removeClass("added error"), s.addClass("loading"), s.removeClass("error"), t.ajax({
                url: WRAjaxURL,
                data: {action: "wr_clear_cart", _nonce: _nonce_wr_nitro},
                complete: function (t) {
                    n()
                }
            })) : n()
        };
        parseInt(WR_Data_Js.in_buy_now_process) && i(!0), t("form.cart .single_buy_now").click(i), t("body").on("click", ".quickview-modal form.cart .single_buy_now", i), t(".floating-add-to-cart .single_buy_now").click(function (e) {
            e.preventDefault(), e.stopPropagation(), t("form.cart .single_buy_now").trigger("click")
        }), t(document).on("gform_pre_conditional_logic", function (e, a) {
            setTimeout(function () {
                if (e = t("#gform_submit_button_" + a).is(":visible")) {
                    var e = t("#gform_submit_button_" + a).is(":disabled");
                    i = t('[id="gform_submit_button_' + a + '"]');
                    e ? i.show().prop("disabled", !1) : i.show().prop("disabled", !0)
                } else {
                    var i;
                    (i = t('[id="gform_submit_button_' + a + '"]')).show().prop("disabled", !0)
                }
            })
        }), t("body").on("click", ".woocommerce-cart .cart-table .product-remove a", function () {
            var e = t(this).closest("tr").find(".product-name > a").text().trim();
            t(document).ajaxComplete(function (a, i, o) {
                var s = o.url;
                void 0 !== o.data && o.data;
                if (-1 != s.indexOf("?remove_item=")) {
                    var n = t(".woocommerce-message").find("a")[0].outerHTML,
                        r = WR_Data_Js.removed_notice.replace("%s", '"' + e + '"');
                    t(".woocommerce-message").html(r + n)
                }
            })
        })
    }

    function o() {
        t(".header .sticky-row").length && setTimeout(function () {
            t(".header").WR_ImagesLoaded(function () {
                !function () {
                    var e = t(".header .sticky-row"), a = e.closest(".hb-section-outer"), i = a.height(),
                        o = a.offset(), s = o.top + i, n = t(window).scrollTop(), r = 0, c = 0, l = t("#wpadminbar"),
                        d = t(".header .sticky-row .hb-search.dropdown"),
                        u = t(".header .sticky-row .hb-cart.dropdown"),
                        p = t(".wr-desktop .header .sticky-row .menu-more"),
                        h = t(".wr-desktop .header.horizontal-layout .sticky-row .hb-menu.text-layout .menu-item");
                    l.length && t(window).width() > 600 && (c = l.height(), e.css("top", c + "px")), a.height(i + "px"), n > s && e.addClass("sticky-row-scroll"), t.function_rotate_device.sticky_row = function () {
                        e.removeClass("sticky-row-scroll").removeClass("sticky-row-scroll-down").removeClass("sticky-row-scroll-up"), a.removeAttr("style"), i = a.height(), a.height(i + "px"), o = a.offset(), s = o.top + i
                    };
                    var m = 106;
                    t(".header .sticky-row").hasClass("sticky-normal") ? (t(window).scroll(function () {
                        (n = t(window).scrollTop()) > o.top - c ? e.addClass("sticky-row-scroll") : e.removeClass("sticky-row-scroll")
                    }), a.find(".hb-section").height(i + "px")) : t(window).scroll(function () {
                        n = t(window).scrollTop();
                        var a = e.hasClass("sticky-row-scroll-up"), i = e.hasClass("sticky-row-scroll");
                        !a && !i && n > s - c && (d.removeClass("active-dropdown"), u.removeClass("active-dropdown"), p.removeClass("active-more"), h.trigger("mouseleave")), n > s ? (i || e.addClass("sticky-row-scroll"), n < s + 150 ? m < 106 && (m += 6, e.css("transform", "translateY(-" + m + "%)")) : n > r ? (m < 106 && (m += 6, e.css("transform", "translateY(-" + m + "%)")), e.hasClass("sticky-row-scroll-down") || e.addClass("sticky-row-scroll-down").removeClass("sticky-row-scroll-up")) : (m > 0 && (m -= 6, e.css("transform", "translateY(-" + m + "%)")), a || e.addClass("sticky-row-scroll-up").removeClass("sticky-row-scroll-down"))) : i && (e.removeClass("sticky-row-scroll").removeClass("sticky-row-scroll-up").removeClass("sticky-row-scroll-down"), e.css("transform", ""), m = 106), r = n
                    })
                }()
            })
        }, 50)
    }

    function s(e, a, i) {
        t(".wrapper-outer").on("mousedown vmousedown", function (o) {
            t(a).index(e.closest(a)) != t(a).index(t(o.target).closest(a)) && (t("body").off("mousedown vmousedown"), i.call(o))
        })
    }

    function n() {
        if (void 0 === t.fn.magnificPopup) return setTimeout(n, 100);
        t(".sc-video").length > 0 && (t(".sc-video-popup").each(function (e, a) {
            var i = t(this).data("popup");
            if (void 0 !== i) {
                var o = "true" == i.control ? "controls=1" : "controls=0";
                t(a).magnificPopup({
                    type: "iframe",
                    mainClass: "mfp-fade",
                    removalDelay: 300,
                    iframe: {
                        markup: '<div class="mfp-iframe-scaler"><button type="button" class="mfp-close">×</button><iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe></div>',
                        patterns: {
                            youtube: {
                                index: "youtube.com/",
                                id: "v=",
                                src: "//www.youtube.com/embed/%id%?autoplay=1&showinfo=0&" + o
                            }, vimeo: {index: "vimeo.com/", id: "/", src: "//player.vimeo.com/video/%id%?autoplay=1"}
                        }
                    }
                })
            }
        }), t(".sc-yt-trigger").length > 0 && (t.getScript("https://www.youtube.com/iframe_api"), t(".sc-yt-trigger").each(function () {
            var e = t(this), a = e.next();
            setTimeout(function t() {
                return "object" != typeof YT ? setTimeout(t, 1e3) : "function" != typeof YT.Player ? setTimeout(t, 1e3) : void new YT.Player(a.get(0), {
                    events: {
                        onReady: function (t) {
                            e.on("click", function (i) {
                                t.target.playVideo(), e.css("opacity", 0), a.show(), i.preventDefault()
                            })
                        }
                    }
                })
            }, 1e3)
        })), t(".sc-vm-trigger").length > 0 && t(".sc-vm-trigger").each(function () {
            var e = t(this), a = e.next(), i = "*";
            e.on("click", function (t) {
                !function (t, e) {
                    var o = {method: t};
                    e && (o.value = e);
                    var s = JSON.stringify(o);
                    a[0].contentWindow.postMessage(s, i)
                }("play"), e.css("opacity", 0), a.show(), t.preventDefault()
            })
        }))
    }

    function r() {
        t("body").on("click", ".wc-show-sidebar", function (e) {
            var a, i;
            t("body").toggleClass("slide-to-left"), t("html").addClass("no-scroll"), t("#shop-mobile-sidebar").before('<div class="mask-overlay"></div>'), t(this), a = "#shop-mobile-sidebar", i = function () {
                t("body").removeClass("slide-to-left"), t("html").removeClass("no-scroll"), t(".mask-overlay").remove()
            }, t(".wrapper-outer").on("mousedown vmousedown", function (e) {
                -1 == t(a).index(t(e.target).closest(a)) && (t("body").off("mousedown vmousedown"), i.call(e))
            })
        })
    }

    t.WR.Lightbox = function () {
        if (void 0 === t.fn.nivoLightbox) return setTimeout(function () {
            t.WR.Lightbox()
        }, 100);
        t('a[data-lightbox^="nivo"]').each(function () {
            t(this).data("nivo-lightbox-initialized") || (t(this).nivoLightbox({
                effect: "fall",
                keyboardNav: !0,
                clickOverlayToClose: !0
            }), t(this).data("nivo-lightbox-initialized", !0))
        })
    }, t.WR.Carousel = function () {
        if (void 0 === t.fn.owlCarousel) return setTimeout(t.WR.Carousel, 100);
        t(".wr-nitro-carousel").each(function () {
            var e = t(this);
            if (!e.data("owl-carousel-initialized")) {
                if (e.hasClass("exclude-carousel")) return;
                var a = e.data("owl-options");
                if (void 0 !== a) {
                    var i = "true" == a.autoplay, o = a.autoplayTimeout ? a.autoplayTimeout : "5000", s = a.items,
                        n = "true" == a.nav, r = "true" == a.dots, c = "true" == a.autoplayHoverPause, l = a.desktop,
                        d = a.tablet, u = a.mobile, p = a.sm_mobile, h = a.custom_responsive, m = "true" == a.rtl,
                        f = !a.loop || a.loop, v = "true" == a.autoHeight, b = a.animateIn ? a.animateIn : "",
                        g = a.animateOut ? a.animateOut : "", w = {
                            items: 1,
                            autoplay: i,
                            autoplayTimeout: o,
                            autoplayHoverPause: c,
                            nav: n,
                            dots: r,
                            loop: f,
                            autoHeight: v,
                            smartSpeed: 400,
                            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                            rtl: m
                        };
                    w.items = s, w.responsive = b || v || "1" == s || "true" == h ? {
                        0: {items: p},
                        376: {items: u},
                        601: {items: d},
                        769: {items: l},
                        993: {items: s}
                    } : {
                        0: {items: u},
                        584: {items: d},
                        784: {items: s}
                    }, b && (w.animateIn = b), g && (w.animateOut = g), e.owlCarousel(w)
                }
                e.data("owl-carousel-initialized", !0)
            }
        })
    }, t(document).ready(function () {
        var a, c, l, d, u, p, h, m, f, v, b, g, w;
        t("body").on("click", ".vc_tta-tab > a", function (t) {
            t.preventDefault()
        }), (void 0 == WR_Data_Js || 1 != WR_Data_Js.blogParallax) && 1 != WR_Data_Js.pageParallax && 1 != WR_Data_Js.bodyParallax || e() || window.skrollr && skrollr.init({forceHeight: !1}), t(".open-popup-link").length && t(".addition-product .open-popup-link, .addition-product .price br").remove(), t(".wr-mobile .hb-menu .has-children-mobile").click(function () {
            var e = t(this), a = e.closest(".item-link-outer"), i = e.closest("li").find(" > ul:first");
            a.hasClass("active-submenu") ? (i.stop(!0, !0).slideUp(function () {
                var a = e.closest(".site-navigator-inner"), i = e.closest(".site-navigator")[0].getBoundingClientRect(),
                    o = t(window).height() - i.top;
                i.height <= o && a.css("height", "")
            }), a.removeClass("active-submenu")) : (i.stop(!0, !0).slideDown(function () {
                var a = e.closest(".site-navigator-inner"), i = a[0].getBoundingClientRect(),
                    o = t(window).height() - i.top;
                i.height > o && a.height(o)
            }), a.addClass("active-submenu"))
        }), t(".wr-mobile .hb-menu .menu-icon-action").click(function () {
            var e = t(this), a = e.closest(".hb-menu").find(".site-navigator-inner");
            e.hasClass("active-menu") ? (a.stop(!0, !0).slideUp(), e.removeClass("active-menu")) : (s(e, ".hb-menu", function (t) {
                a.stop(!0, !0).slideUp(), e.removeClass("active-menu")
            }), a.stop(!0, !0).slideDown(function () {
                var e = a[0].getBoundingClientRect(), i = t(window).height() - e.top;
                e.height > i && t(this).height(i)
            }), e.addClass("active-menu"))
        }), t.function_rotate_device.menu_mobile = function () {
            t.each(t(".wr-mobile .hb-menu .menu-icon-action.active-menu"), function (e, a) {
                var i = t(a).closest(".hb-menu").find(".site-navigator-inner");
                i.css("height", "");
                var o = i[0].getBoundingClientRect(), s = t(window).height() - o.top;
                o.height > s ? i.height(s) : i.css("height", "")
            })
        }, o(), function () {
            function e() {
                switch (t("body > .hb-search-fs .close").attr("data-layout")) {
                    case"full-screen":
                        t("body > .hb-search-fs").fadeOut(300, function () {
                            t("html").removeClass("no-scroll"), t("body > .hb-search-fs").remove(), t("body > .wrapper-outer").removeAttr("style")
                        });
                        break;
                    case"topbar":
                        var e = t("#wpadminbar"), a = e.length ? e.height() : "0";
                        t("body > .hb-search-fs").animate({top: a - 80 + "px"}, function () {
                            t(this).remove()
                        }), t("body > .wrapper-outer").animate({top: "0px"}, function () {
                            t(this).removeAttr("style")
                        })
                }
                t(".header .hb-search").find(".open.active-topbar").removeClass("active-topbar")
            }

            t(".hb-search .open.show-full-screen").on("click", function () {
                var a = t(this).parents(".hb-search").find(".hb-search-fs")[0].outerHTML;
                t("body").append(a);
                var i = t(this).attr("data-background-style"), o = t(this).attr("data-layout"),
                    s = t("body > .hb-search-fs");
                if ("topbar" == o && t(this).hasClass("active-topbar")) e(); else {
                    switch (o) {
                        case"full-screen":
                            s.fadeIn(300), t("html").addClass("no-scroll");
                            break;
                        case"topbar":
                            var n = t("#wpadminbar"), r = n.length ? n.height() : "0";
                            t(this).addClass("active-topbar"), s.css({
                                display: "block",
                                top: r - 80 + "px"
                            }).animate({top: r + "px"}), t("body > .wrapper-outer").css({
                                position: "relative",
                                top: "0px"
                            }).animate({top: "80px"})
                    }
                    s.addClass(i + " " + o), s.find(".close").attr("data-layout", o), s.find("form input").focus()
                }
            }), t("body").on("click", ".hb-search-fs .close", function () {
                e()
            }), t(".header .hb-search.dropdown .open").click(function () {
                var e = t(this), a = e.closest(".hb-search"), i = a.find(".search-form:first"),
                    o = t(".header .hb-search.dropdown").index(a), n = a[0].getBoundingClientRect(),
                    r = parseInt(a.css("borderTopWidth")), c = parseInt(a.css("borderBottomWidth"));
                if (t(".header .hb-search.dropdown:not(:eq(" + o + "))").removeClass("active-dropdown"), a.hasClass("active-dropdown")) a.removeClass("active-dropdown"), i.removeClass("set-width"); else {
                    s(e, ".hb-search", function (t) {
                        a.removeClass("active-dropdown"), i.removeClass("set-width")
                    }), i.removeAttr("style");
                    var l = t(window).width();
                    i.width() > l - 10 && (i.css("width", l - 10), i.addClass("set-width")), l = t(window).width();
                    var d = i[0].getBoundingClientRect(),
                        u = (e[0].getBoundingClientRect(), l > 1024 ? parseInt(WR_Data_Js.offset) : 0);
                    if (l < d.right + 5) {
                        var p = d.right + 5 + u - l;
                        i.css("left", -p + "px")
                    } else d.left < 5 + u && i.css("left", "5px");
                    var h = "empty" == a.attr("data-margin-top") ? a.attr("data-margin-top") : parseInt(a.attr("data-margin-top"));
                    if (e.closest(".sticky-row-scroll").length || "empty" == h) {
                        var m = e.closest(e.closest(".sticky-row-scroll").length ? ".sticky-row" : ".hb-section-outer")[0].getBoundingClientRect(),
                            f = parseInt(m.bottom - n.bottom + (n.height - r));
                        i.css("top", f)
                    } else h > 0 && i.css("top", h + (n.height - (r + c)));
                    a.addClass("active-dropdown"), a.find(".wrls-form").length && a.find(".cate-search-outer").width(), setTimeout(function () {
                        a.find(".txt-search").focus()
                    }, 300)
                }
            }), t(".header .hb-search.expand-width .open").on("click", function (e) {
                var a = t(this), i = a.closest(".hb-search"), o = i.find(".search-form form"),
                    n = o[0].getBoundingClientRect(), r = n.width, c = a.closest(".header"),
                    l = c.hasClass("vertical-layout"), d = !0;
                if (i.hasClass("expan-width-active")) o.stop(!0, !0).css({overflow: "hidden"}).animate({width: "0px"}, 200, function () {
                    i.removeClass("expan-width-active"), o.removeAttr("style");
                    var e = a.closest(".container").find(".hide-expand-search");
                    e.css("visibility", "").animate({opacity: 1}, 200, function () {
                        e.removeClass("hide-expand-search"), t(this).css("opacity", "")
                    })
                }); else {
                    s(a, ".hb-search", function (e) {
                        o.stop(!0, !0).css({overflow: "hidden"}).animate({width: "0px"}, 200, function () {
                            i.removeClass("expan-width-active"), o.removeAttr("style");
                            var e = a.closest(".container").find(".hide-expand-search");
                            e.css("visibility", "").animate({opacity: 1}, 200, function () {
                                e.removeClass("hide-expand-search"), t(this).css("opacity", "")
                            })
                        })
                    });
                    var u = a[0].getBoundingClientRect(), p = u.left + u.width / 2, h = document.body.offsetWidth,
                        m = i.outerWidth();
                    if (l) {
                        var f = i[0].getBoundingClientRect(), v = c[0].getBoundingClientRect();
                        d = c.hasClass("left-position-vertical") ? !(f.left - v.left - 10 >= n.width) : v.right - f.right - 10 >= n.width
                    } else d = 2 * p < h;
                    if (d) {
                        var b = i.nextUntil();
                        if (b.length) {
                            var g = 0, w = function () {
                                o.stop(!0, !0).css({
                                    left: m + 5,
                                    width: 0,
                                    overflow: "hidden",
                                    visibility: "initial"
                                }).animate({width: r}, 200, function () {
                                    t(this).css("overflow", "")
                                })
                            };
                            l ? w() : (b.each(function (e, a) {
                                if (g < r && (t(a).animate({opacity: 0}, 200, function () {
                                    t(a).css("visibility", "hidden")
                                }), t(a).addClass("hide-expand-search")), (g += t(a).outerWidth(!0)) > r) return !1
                            }), setTimeout(w, 200))
                        } else o.stop(!0, !0).css({
                            left: m + 5,
                            width: 0,
                            overflow: "hidden",
                            visibility: "initial"
                        }).animate({width: r}, 200, function () {
                            t(this).css("overflow", "")
                        })
                    } else {
                        var _ = i.prevUntil();
                        if (_.length) {
                            var y = 0;
                            w = function () {
                                o.stop(!0, !0).css({
                                    right: m + 5,
                                    width: 0,
                                    overflow: "hidden",
                                    visibility: "initial"
                                }).animate({width: r}, 200, function () {
                                    t(this).css("overflow", "")
                                })
                            }, l ? w() : (_.each(function (e, a) {
                                if (y < r && (t(a).animate({opacity: 0}, 200, function () {
                                    t(a).css("visibility", "hidden")
                                }), t(a).addClass("hide-expand-search")), (y += t(a).outerWidth(!0)) > r) return !1
                            }), setTimeout(w, 200))
                        } else o.stop(!0, !0).css({
                            right: m + 5,
                            width: 0,
                            overflow: "hidden",
                            visibility: "initial"
                        }).animate({width: r}, 200, function () {
                            t(this).css("overflow", "")
                        })
                    }
                    i.addClass("expan-width-active"), setTimeout(function () {
                        i.find(".txt-search").focus()
                    }, 300)
                }
            }), t(".header .hb-search.boxed .open").on("click", function () {
                t(this).parents(".hb-search").find('input[type="submit"]').trigger("click")
            })
        }(), i(), t(".hb-sidebar .icon-sidebar").click(function () {
            t(this).closest(".hb-sidebar").addClass("active"), t("html").addClass("no-scroll")
        }), t(".hb-sidebar .content-sidebar > .overlay").click(function () {
            t(this).closest(".hb-sidebar").removeClass("active"), t("html").removeClass("no-scroll")
        }), function () {
            if (t("body.wr-desktop").on("click", ".hb-menu .menu-icon-action", function () {
                var e = t(this), a = e.parents(".hb-menu");
                e.find(".wr-burger-scale").addClass("wr-acitve-burger");
                var i = a.find(".site-navigator-outer")[0].outerHTML;
                t("body > .hb-menu-outer").length || t("body").append('<div class="hb-menu-outer"></div>'), t("body > .hb-menu-outer").html(i), setTimeout(function () {
                    t(".hb-menu-outer .navigator-column").height() < t(".hb-menu-outer .navigator-column-inner").height() && t(".hb-menu-outer").addClass("hb-menu-scroll")
                }, 500), t("body > .hb-overlay-menu").length || t("body").append('<div class="hb-overlay-menu"></div>');
                var o = e.attr("data-layout"), s = e.attr("data-effect"), n = e.attr("data-position"),
                    r = e.attr("data-animation"), c = t("body > .wrapper-outer"),
                    l = t("body > .hb-menu-outer .sidebar-style"),
                    d = (t("body > .hb-menu-outer"), t("body > .hb-menu-outer ul.site-navigator")),
                    u = t("body > .hb-overlay-menu"), p = t("body > .hb-menu-outer .fullscreen-style");
                if (t("html").addClass("no-scroll"), "fullscreen" == o) switch (s) {
                    case"none":
                        p.show();
                        break;
                    case"fade":
                        p.fadeIn(100);
                        break;
                    case"scale":
                        setTimeout(function () {
                            p.addClass("scale-active")
                        }, 100)
                } else if ("sidebar" == o) {
                    var h = l.innerWidth();
                    u.attr("data-position", n), u.attr("data-animation", r), u.fadeIn(), l.css("opacity", 1);
                    var m = t("#wpadminbar");
                    switch (m.length ? l.css("top", m.height() + "px") : l.css("top", "0px"), n) {
                        case"left":
                            switch (l.css({
                                visibility: "visible",
                                left: "-" + h + "px"
                            }).animate({left: "0px"}), "push" != r && "fall-down" != r && "fall-up" != r || c.css({
                                position: "relative",
                                left: "0px"
                            }).animate({left: h + "px"}), r) {
                                case"slide-in-on-top":
                                case"push":
                                    break;
                                case"fall-down":
                                    d.css({position: "relative", top: "-300px"}).animate({top: "0px"});
                                    break;
                                case"fall-up":
                                    d.css({position: "relative", top: "300px"}).animate({top: "0px"})
                            }
                            break;
                        case"right":
                            switch (l.css({
                                visibility: "visible",
                                right: "-" + h + "px"
                            }).animate({right: "0px"}), "push" != r && "fall-down" != r && "fall-up" != r || c.css({
                                position: "relative",
                                right: "0px"
                            }).animate({right: h + "px"}), r) {
                                case"slide-in-on-top":
                                case"push":
                                    break;
                                case"fall-down":
                                    d.css({position: "relative", top: "-300px"}).animate({top: "0px"});
                                    break;
                                case"fall-up":
                                    d.css({position: "relative", top: "300px"}).animate({top: "0px"})
                            }
                    }
                }
            }), t("body").on("click", ".fullscreen-style .close", function () {
                t(".wr-burger-scale").removeClass("wr-acitve-burger");
                var e = t(this), a = e.parents(".hb-menu-outer");
                switch (e.attr("data-effect")) {
                    case"none":
                        a.remove();
                        break;
                    case"fade":
                        a.find(".site-navigator-outer").fadeOut(300, function () {
                            a.remove()
                        });
                        break;
                    case"scale":
                        a.find(".site-navigator-outer").removeClass("scale-active"), setTimeout(function () {
                            a.remove()
                        }, 300)
                }
                t("html").removeClass("no-scroll"), t("body > .wrapper-outer").removeAttr("style")
            }), t("body").on("click", ".hb-overlay-menu", function () {
                t(".wr-burger-scale").removeClass("wr-acitve-burger");
                var e = t(this), a = e.attr("data-position"), i = e.attr("data-animation"),
                    o = t("body > .wrapper-outer"), s = t("body > .hb-menu-outer .sidebar-style"),
                    n = t("body > .hb-menu-outer ul.site-navigator"), r = s.innerWidth();
                switch (s.innerHeight(), e.fadeOut(), setTimeout(function () {
                    t("body > .hb-menu-outer").remove(), e.remove(), t("html").removeClass("no-scroll"), t("body > .wrapper-outer").removeAttr("style")
                }, 500), a) {
                    case"left":
                        switch (s.animate({left: "-" + r + "px"}), "push" != i && "fall-down" != i && "fall-up" != i || o.animate({left: "0px"}), i) {
                            case"slide-in-on-top":
                            case"push":
                                break;
                            case"fall-down":
                                n.animate({top: "-300px"});
                                break;
                            case"fall-up":
                                n.animate({top: "300px"})
                        }
                        break;
                    case"right":
                        switch (s.animate({right: "-" + r + "px"}), "push" != i && "fall-down" != i && "fall-up" != i || o.animate({right: "0px"}), i) {
                            case"slide-in-on-top":
                            case"push":
                                break;
                            case"fall-down":
                                n.animate({top: "-300px"});
                                break;
                            case"fall-up":
                                n.animate({top: "300px"})
                        }
                }
            }), t("body").on("click", ".header .menu-more .icon-more", function (e) {
                var a = t(this), i = a.closest(".site-navigator-inner"), o = a.closest(".menu-more"),
                    n = i.find(".site-navigator"), r = i.find(".nav-more"),
                    c = i.find(" > .site-navigator .item-hidden"), l = t(".header .menu-more").index(o),
                    d = a.closest(".element-item");
                if (t(".header .menu-more:not(:eq(" + l + "))").removeClass("active-more"), o.hasClass("active-more")) o.removeClass("active-more"); else {
                    s(a, ".hb-menu", function (t) {
                        o.removeClass("active-more")
                    }), r.html(""), r.removeAttr("style");
                    var u = t(window).width(), p = r[0].getBoundingClientRect(),
                        h = u > 1024 ? parseInt(WR_Data_Js.offset) : 0;
                    if (u < p.right + 5) {
                        var m = p.right + 5 + h - u;
                        r.css("left", -m + "px")
                    } else p.left < 5 + h && r.css("left", "5px");
                    var f = "empty" == d.attr("data-margin-top") ? d.attr("data-margin-top") : parseInt(d.attr("data-margin-top")),
                        v = o[0].getBoundingClientRect();
                    if (a.closest(".sticky-row-scroll").length || "empty" == f) {
                        var b = a.closest(a.closest(".sticky-row-scroll").length ? ".sticky-row" : ".hb-section-outer")[0].getBoundingClientRect(),
                            g = v.top + v.height, w = b.top + b.height, _ = parseInt(w - g), y = parseInt(_ + v.height);
                        r.css("top", y)
                    } else f > 0 && r.css("top", f + v.height);
                    if (c.length) {
                        var C = "";
                        t.each(c, function () {
                            C += t(this)[0].outerHTML
                        }), r.html('<ul class="animation-' + d.attr("data-animation") + " " + n.attr("class") + '">' + C + "</ul>")
                    }
                    setTimeout(function () {
                        o.addClass("active-more")
                    }, 10)
                }
            }), t.fn.hoverIntent) {
                var e = function (e) {
                    var a = "", i = e[0].getBoundingClientRect(),
                        o = (r = t(window).width()) > 1024 ? parseInt(WR_Data_Js.offset) : 0,
                        s = "empty" == e.closest(".hb-menu").attr("data-margin-top") ? e.closest(".hb-menu").attr("data-margin-top") : parseInt(e.closest(".hb-menu").attr("data-margin-top"));
                    if (e.hasClass("wrmm-item")) {
                        (y = e.find(" > .mm-container-outer")).attr("style", "display:block");
                        var n = e.closest(".container")[0].getBoundingClientRect(), r = t(window).width(), c = n.width,
                            l = n.right, d = 0, u = 0, p = y.attr("data-width");
                        "full" === p ? (d = c) + 10 + 2 * o >= r && (d = c - 10, l -= 5) : "full-width" === p ? (d = r - 10 - 2 * o, l = 5 + o) : (d = parseInt(p) ? parseInt(p) : c) + 10 + 2 * o >= r && (d = r - 10 - 2 * o, l -= 5), y.width(d);
                        var h = y[0].getBoundingClientRect();
                        if (u = "full-width" == p ? -(h.left - l) : "full" == p ? h.right - l > 0 ? -parseInt(h.right - l) : 0 : h.right > r - 5 - 2 * o ? -(h.right - (r - 5 - o)) : 0, a = {
                            display: "block",
                            left: u,
                            width: d
                        }, e.closest(".sticky-row-scroll").length || "empty" == s) {
                            var m = e.closest(e.closest(".sticky-row-scroll").length ? ".sticky-row" : ".hb-section-outer")[0].getBoundingClientRect(),
                                f = i.top + i.height, v = m.top + m.height, b = parseInt(v - f),
                                g = parseInt(b + i.height);
                            a.top = g, 0 == e.children(".hover-area").length && e.append('<span class="hover-area" style="height:' + (g - i.height) + 'px"></span>')
                        } else s > 0 && (a.top = s + i.height, 0 == e.children(".hover-area").length && e.append('<span class="hover-area" style="height:' + s + 'px"></span>'));
                        var w = y.find(".mm-container").width(), _ = 0;
                        t.each(y.find(".mm-container > .mm-col"), function () {
                            var e = t(this), a = e.outerWidth();
                            _ += a, e.removeClass("mm-last-row"), _ == w ? (e.addClass("mm-last-row"), _ = 0) : _ > w && (e.prev().addClass("mm-last-row"), _ = a)
                        })
                    } else {
                        var y;
                        if ((y = e.find(" > ul.sub-menu")).attr("style", "display:block"), 0 == y.length) return !1;
                        if (h = y[0].getBoundingClientRect(), r = t(window).width(), u = Math.round(h.right - r + o), e.hasClass("menu-default")) if (u = u > 0 ? -u - 5 : 0, e.closest(".sticky-row-scroll").length || "empty" == s) {
                            m = e.closest(e.closest(".sticky-row-scroll").length ? ".sticky-row" : ".hb-section-outer")[0].getBoundingClientRect(), f = i.top + i.height, v = m.top + m.height, b = parseInt(v - f);
                            var C = parseInt(b + i.height);
                            0 == e.children(".hover-area").length && e.append('<span class="hover-area" style="height:' + (C - i.height) + 'px"></span>')
                        } else s > 0 && (C = s + i.height, 0 == e.children(".hover-area").length && e.append('<span class="hover-area" style="height:' + s + 'px"></span>')); else {
                            var x = e.closest("ul");
                            if (parseInt(x.css("left")) < 0) {
                                var k = x[0].getBoundingClientRect();
                                u = h.width < k.left - o ? -h.width : h.width
                            } else u = 1 == WR_Data_Js.rtl && e.hasClass("menu-item-lv1") ? -h.width : u > 0 ? -h.width : h.width;
                            var R = t(window).height(),
                                I = t("#wpadminbar").length && "fixed" == t("#wpadminbar").css("position") ? t("#wpadminbar").height() : 0,
                                W = R - (h.top + h.height) - o;
                            W = h.height > R - 10 - I - o ? -(h.top - I - 5 - o) : W < 5 ? W - 5 : 0
                        }
                        a = {display: "block", left: u}, void 0 !== W && (a.top = W), void 0 !== C && (a.top = C)
                    }
                    switch (y.css(a), e.closest(".hb-menu").attr("data-animation")) {
                        case"none":
                            y.css({opacity: "1"});
                            break;
                        case"fade":
                            y.stop(!0, !0).css({pointerEvents: "none"}).animate({opacity: "1"}, 150, function () {
                                a.pointerEvents = "", y.css(a)
                            });
                            break;
                        case"left-to-right":
                            u = parseInt(y.css("left")), y.stop(!0, !0).css({
                                pointerEvents: "none",
                                left: u - 50 + "px"
                            }).animate({opacity: "1", left: u + "px"}, 300, function () {
                                a.pointerEvents = "", y.css(a)
                            });
                            break;
                        case"right-to-left":
                            u = parseInt(y.css("left")), y.stop(!0, !0).css({
                                pointerEvents: "none",
                                left: u + 50 + "px"
                            }).animate({opacity: "1", left: u + "px"}, 300, function () {
                                a.pointerEvents = "", y.css(a)
                            });
                            break;
                        case"bottom-to-top":
                            var T = parseInt(y.css("top"));
                            u = parseInt(y.css("left")), y.stop(!0, !0).css({
                                pointerEvents: "none",
                                left: u + "px",
                                top: T + 30 + "px"
                            }).animate({opacity: "1", top: T + "px"}, 300, function () {
                                a.pointerEvents = "", y.css(a)
                            });
                            break;
                        case"scale":
                            u = parseInt(y.css("left")), y.css({
                                pointerEvents: "none",
                                left: u + "px",
                                transform: "scale(0.8)"
                            }).animate({opacity: "1", transform: "scale(1)"}, 250, function () {
                                a.pointerEvents = "", y.css(a)
                            })
                    }
                    e.addClass("menu-hover")
                };
                setTimeout(function () {
                    t(".wr-desktop header.header.horizontal-layout .active-menu").each(function () {
                        e(t(this))
                    })
                }, 1e3), t(".wr-desktop header.header.horizontal-layout").hoverIntent({
                    over: function () {
                        var a = t(this);
                        a.hasClass("active-menu") || e(a)
                    }, out: function () {
                        var e = t(this);
                        if (!e.hasClass("active-menu")) {
                            if (e.children(".hover-area").remove(), e.hasClass("wrmm-item")) var a = e.find(" > .mm-container-outer"); else a = e.find("ul.sub-menu");
                            switch (e.find(" > .menu-item-link .hover-area").removeAttr("style"), e.closest(".hb-menu").attr("data-animation")) {
                                case"none":
                                    e.removeClass("menu-hover"), a.removeAttr("style");
                                    break;
                                case"fade":
                                    a.stop(!0, !0).animate({opacity: "0"}, 150, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"left-to-right":
                                    var i = parseInt(a.css("left")) - 50;
                                    a.stop(!0, !0).animate({opacity: "0", left: i + "px"}, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"right-to-left":
                                    i = parseInt(a.css("left")) + 50, a.stop(!0, !0).animate({
                                        opacity: "0",
                                        left: i + "px"
                                    }, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"bottom-to-top":
                                    var o = parseInt(a.css("top")) + 50;
                                    a.stop(!0, !0).animate({opacity: "0", top: o + "px"}, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"scale":
                                    a.stop(!0, !0).animate({opacity: "0", transform: "scale(0.8)"}, 250, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    })
                            }
                        }
                    }, timeout: 0, sensitivity: 1, interval: 0, selector: ".site-navigator li.menu-item"
                });
                var a = function (e) {
                    var a = "", i = t(window).width(), o = 0;
                    if (1 == e.closest(".menu-more").length) {
                        var s = e.closest(".menu-more")[0].getBoundingClientRect(), n = i - s.right;
                        s.left > n && (o = 1)
                    } else o = e.closest(".vertical-layout.right-position-vertical").length || e.closest(".sidebar-style.right-position").length;
                    var r = i > 1024 ? parseInt(WR_Data_Js.offset) : 0, c = e[0].getBoundingClientRect(),
                        l = t(window).height();
                    if (e.hasClass("wrmm-item")) {
                        (f = e.find(" > .mm-container-outer")).attr("style", "display:block");
                        var d = f.attr("data-width");
                        if (1 == o) {
                            var u = c.left - r;
                            ("full" == d || d > u) && (d = u - 5), f.width(d);
                            var p = f[0].getBoundingClientRect(),
                                h = t("#wpadminbar").length && "fixed" == t("#wpadminbar").css("position") ? t("#wpadminbar").height() : 0,
                                m = l - (p.top + p.height) - r;
                            m = p.height > l - 10 - h - r ? -(p.top - h - 5 - r) : m < 5 ? m - 5 : 0, a = {
                                display: "block",
                                width: d,
                                left: -d,
                                top: m
                            }
                        } else u = i - c.right - r, ("full" == d || d > u) && (d = u - 5), f.width(d), p = f[0].getBoundingClientRect(), h = t("#wpadminbar").length && "fixed" == t("#wpadminbar").css("position") ? t("#wpadminbar").height() : 0, m = l - (p.top + p.height) - r, m = p.height > l - 10 - h - r ? -(p.top - h - 5 - r) : m < 5 ? m - 5 : 0, a = {
                            display: "block",
                            width: d,
                            left: parseInt(c.width),
                            top: m
                        }
                    } else {
                        var f;
                        if (!(f = e.find(" > ul.sub-menu")).length) return !1;
                        if (f.attr("style", "display:block"), p = f[0].getBoundingClientRect(), e.hasClass("menu-default")) if (1 == o) var v = -parseInt(p.width); else v = parseInt(c.width); else {
                            var b = e.closest("ul"), g = b[0].getBoundingClientRect();
                            v = p.width > i - g.right - r - 5 ? -p.width : p.width, parseInt(b.css("left")) < 0 && (v = p.width < g.left - 5 - r ? -p.width : p.width)
                        }
                        h = t("#wpadminbar").length && "fixed" == t("#wpadminbar").css("position") ? t("#wpadminbar").height() : 0, m = l - (p.top + p.height) - r, m = p.height > l - 10 - h - r ? -(p.top - h - 5 - r) : m < 5 ? m - 5 : 0, a = {
                            display: "block",
                            left: v,
                            top: m
                        }
                    }
                    var w = 1 == e.closest(".menu-more").length ? e.closest(".element-item").attr("data-animation") : e.closest(".site-navigator-outer").attr("data-effect-vertical");
                    switch (e.closest(".hb-menu-scroll").length && e.hasClass("menu-item-lv0") && (p.height > l - 10 - 2 * r - h ? a.top = 5 : c.top + p.height > l - 5 - r ? (a.top = c.top - (c.top + p.height - l), a.top -= 2 * r + h + 5) : a.top = c.top - r - h), f.css(a), w) {
                        case"none":
                            f.css({visibility: "visible", opacity: "1"});
                            break;
                        case"fade":
                            f.stop(!0, !0).animate({opacity: "1"}, 300, function () {
                                f.css(a)
                            });
                            break;
                        case"left-to-right":
                            v = parseInt(f.css("left")), f.stop(!0, !0).css({left: v - 50 + "px"}).animate({
                                opacity: "1",
                                left: v + "px"
                            }, 300, function () {
                                f.css(a)
                            });
                            break;
                        case"right-to-left":
                            v = parseInt(f.css("left")), f.stop(!0, !0).css({left: v + 50 + "px"}).animate({
                                opacity: "1",
                                left: v + "px"
                            }, 300, function () {
                                f.css(a)
                            });
                            break;
                        case"bottom-to-top":
                            var _ = parseInt(f.css("top"));
                            v = parseInt(f.css("left")), f.stop(!0, !0).css({
                                left: v + "px",
                                top: _ + 50 + "px"
                            }).animate({opacity: "1", top: _ + "px"}, 300, function () {
                                f.css(a)
                            });
                            break;
                        case"scale":
                            f.css({left: v + "px", transform: "scale(0.8)"}).animate({
                                opacity: "1",
                                transform: "scale(1)"
                            }, 300, function () {
                                f.css(a)
                            })
                    }
                    e.addClass("menu-hover")
                };
                setTimeout(function () {
                    t(".vertical-layout .text-layout .animation-vertical-normal .active-menu").each(function () {
                        a(t(this))
                    })
                }, 1e3), t("body").hoverIntent({
                    over: function () {
                        var e = t(this);
                        e.hasClass("active-menu") || a(e)
                    },
                    out: function () {
                        var e = t(this);
                        if (!e.hasClass("active-menu")) {
                            if (e.hasClass("wrmm-item")) var a = e.find(" > .mm-container-outer"); else a = e.find("ul.sub-menu");
                            switch (1 == e.closest(".menu-more").length ? e.closest(".element-item").attr("data-animation") : e.closest(".site-navigator-outer").attr("data-effect-vertical")) {
                                case"none":
                                    e.removeClass("menu-hover"), a.removeAttr("style");
                                    break;
                                case"fade":
                                    a.stop(!0, !0).animate({opacity: "0"}, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"left-to-right":
                                    var i = parseInt(a.css("left")) - 50;
                                    a.stop(!0, !0).animate({opacity: "0", left: i + "px"}, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"right-to-left":
                                    i = parseInt(a.css("left")) + 50, a.stop(!0, !0).animate({
                                        opacity: "0",
                                        left: i + "px"
                                    }, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"bottom-to-top":
                                    var o = parseInt(a.css("top")) + 50;
                                    a.stop(!0, !0).animate({opacity: "0", top: o + "px"}, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    });
                                    break;
                                case"scale":
                                    a.stop(!0, !0).animate({opacity: "0", transform: "scale(0.8)"}, 300, function () {
                                        e.removeClass("menu-hover"), a.removeAttr("style")
                                    })
                            }
                        }
                    },
                    timeout: 1,
                    sensitivity: 6,
                    interval: 0,
                    selector: ".vertical-layout .text-layout .animation-vertical-normal .site-navigator li.menu-item, .hb-menu-outer .sidebar-style.animation-vertical-normal .site-navigator li.menu-item, .menu-more .nav-more .site-navigator li.menu-item"
                })
            }
            var i = {};

            function o(e) {
                var a = 0;
                return t.each(e, function () {
                    var e = t(this);
                    if (e.hasClass("hb-menu") && e.hasClass("text-layout")) {
                        var i = e.outerWidth(!0) - e.find(".site-navigator-outer").width() + 47;
                        a += i
                    } else i = e.outerWidth(!0), a += i
                }), a
            }

            function n(e, a, i) {
                var o = 0, s = 0;
                if (t.each(e, function () {
                    var e = t(this).outerWidth(!0);
                    o += e
                }), o < a && (s = a - o), s) {
                    var n = i.prevAll(".hb-flex");
                    if (n.length) {
                        var r = parseInt(s / n.length);
                        n.width(r), n.addClass("not-flex")
                    } else i.css("marginLeft", s + parseInt(i.css("marginLeft")))
                }
            }

            function r() {
                t.each(t(".horizontal-layout .hb-section-outer"), function () {
                    var e = t(this), a = e.find(".hb-menu.text-layout"), i = e.find(".element-item.center-element"),
                        s = e.find(".hb-flex");
                    if (a.length) if (a.find(".site-navigator > .menu-item").removeClass("item-hidden"), a.find(".menu-more").remove(), e.find(".center-element").removeAttr("style"), s.removeAttr("style"), s.removeClass("not-flex"), i.hasClass("hb-menu") && i.hasClass("text-layout")) {
                        var r = (f = e.find(".hb-section > .container"))[0].getBoundingClientRect().width - (parseInt(f.css("borderLeftWidth")) + parseInt(f.css("borderRightWidth")) + parseInt(f.css("paddingLeft")) + parseInt(f.css("paddingRight"))),
                            l = i.prevAll(':not(".hb-flex")'), d = i.nextAll(':not(".hb-flex")'), u = o(l), p = o(d),
                            h = r - 2 * (u > p ? u : p);
                        i.outerWidth(!0) >= h && c(i, h), c(l, m = parseInt((r - i.outerWidth(!0)) / 2)), c(d, m), n(l, m, i)
                    } else if (i.length) {
                        i.removeAttr("style"), r = (f = e.find(".hb-section > .container"))[0].getBoundingClientRect().width - (parseInt(f.css("borderLeftWidth")) + parseInt(f.css("borderRightWidth")) + parseInt(f.css("paddingLeft")) + parseInt(f.css("paddingRight")));
                        var m = parseInt((r - i.outerWidth(!0)) / 2);
                        l = i.prevAll(':not(".hb-flex")'), d = i.nextAll(':not(".hb-flex")'), c(l, m), c(d, m), n(l, m, i)
                    } else r = (f = e.find(".hb-section > .container"))[0].getBoundingClientRect().width - (parseInt(f.css("borderLeftWidth")) + parseInt(f.css("borderRightWidth")) + parseInt(f.css("paddingLeft")) + parseInt(f.css("paddingRight"))), c(e.find(".element-item:not(.hb-flex)"), r); else if (i.length) {
                        var f;
                        e.find(".center-element").removeAttr("style"), e.find(".hb-flex").removeAttr("style"), s.removeClass("not-flex"), r = (f = e.find(".hb-section > .container"))[0].getBoundingClientRect().width - (parseInt(f.css("borderLeftWidth")) + parseInt(f.css("borderRightWidth")) + parseInt(f.css("paddingLeft")) + parseInt(f.css("paddingRight"))), m = parseInt((r - i.outerWidth(!0)) / 2), n(l = i.prevAll(':not(".hb-flex")'), m, i)
                    }
                })
            }

            function c(e, a) {
                var i = [], o = [];
                t.each(e, function () {
                    var e = t(this);
                    e.hasClass("hb-menu") && e.hasClass("text-layout") ? i.push(e) : o.push(e)
                });
                var s = i.length;
                t.each(o, function () {
                    a -= t(this).outerWidth(!0)
                });
                var n = parseInt(a / s), r = 0, c = [], l = 0;
                t.each(i, function () {
                    var e = t(this).outerWidth(!0);
                    e < n ? r += n - e : c.push(l), l++
                }), n += parseInt(r / c.length), t.each(c, function (e, a) {
                    var o = t(i[a]), s = o.find(".site-navigator > .menu-item");
                    if (s.length) {
                        var r = o.outerWidth(!0), c = o.find(".site-navigator-outer").width(), l = n - (r - c + 52),
                            d = 0, u = !1;
                        t.each(s, function (e, a) {
                            (d += t(this).outerWidth(!0)) >= l && (t(this).addClass("item-hidden"), u = !0)
                        }), u && o.find(".site-navigator-inner").append('<div class="menu-more"><div class="icon-more"><span class="wr-burger-menu"></span><i class="fa fa-caret-down"></i></div><div class="nav-more"></div></div>')
                    }
                })
            }

            t("body").on("click", ".mm-container .mm-has-children", function (e) {
                e.preventDefault();
                var a = t(this), i = a.closest("ul"), o = a.closest("li"), s = a.closest(".mm-col"),
                    n = o.find(" > ul");
                i.addClass("slide-hide"), n.addClass("slide-show"), s.find(".prev-slide").length || s.find(" > li > ul.sub-menu").prepend('<li class="prev-slide"><i class="fa fa-angle-left"></i></li>');
                var r = a.closest(".mm-col").find(" > li > ul"), c = n.height() + r.find(".prev-slide").outerHeight();
                r.height() < c && r.height(c)
            }), t("body").on("click", ".mm-container .prev-slide", function (e) {
                var a = t(this), i = a.closest(".mm-col"),
                    o = (a.closest(".mm-container"), i.find(".slide-show:last").removeClass("slide-show"), i.find(".slide-hide:last"));
                1 == i.find(".slide-hide").length && (a.closest("ul").css("height", ""), a.remove()), o.removeClass("slide-hide")
            }), t("body").on("click", ".vertical-layout .hb-menu .animation-vertical-slide .icon-has-children, .hb-menu-outer .animation-vertical-slide .icon-has-children", function (e) {
                e.preventDefault();
                var a = t(this), o = a.closest(".site-navigator-outer"), s = a.closest("li"), n = s.find(" > ul > li"),
                    r = a.closest("ul").find(" > li "), c = Object.keys(i).length + 1,
                    l = a.closest("a").find(".menu_title").text(),
                    d = s.find(s.hasClass("wrmm-item") ? " .mm-container-outer " : " > ul ").height(),
                    u = o.find(".site-navigator"), p = "";
                if (d > u.height() && u.attr("style", "height:" + d + "px;"), s.addClass("active-slide").addClass("slide-level-" + c), s.find(" > ul > li.menu-item-has-children").length || s.find(" > ul ").addClass("not-padding-icon"), r.length) {
                    var h = r.length;
                    r.each(function (e, a) {
                        setTimeout(function () {
                            t(a).addClass("slide-left"), h == e + 1 && s.hasClass("wrmm-item") && s.addClass("slide-normal")
                        }, 100 * e)
                    })
                }
                if (n.length && !s.hasClass("wrmm-item") && setTimeout(function () {
                    n.each(function (e, a) {
                        setTimeout(function () {
                            t(a).addClass("slide-normal")
                        }, 100 * e)
                    })
                }, 100), i[c] = l, o.find(".menu-breadcrumbs-outer").addClass("show-breadcrumbs"), o.find(".item-breadcrumbs").remove(), Object.keys(i).length && t.each(i, function (t, e) {
                    p += '<div class="element-breadcrumbs item-breadcrumbs"><i class="fa fa-long-arrow-right"></i><span class="title-breadcrumbs" data-level="' + t + '">' + e + "</span></div>"
                }), o.find(".menu-breadcrumbs").append(p), o.hasClass("fullscreen-style")) {
                    var m = a.closest(".navigator-column-inner")[0].getBoundingClientRect(),
                        f = t(window).width() - m.left;
                    o.find(".menu-breadcrumbs-outer").css("width", parseInt(f)), a.closest(".navigator-column-inner").width(m.width)
                }
            }), t("body").on("click", ".vertical-layout .menu-breadcrumbs .element-breadcrumbs .title-breadcrumbs, .hb-menu-outer .animation-vertical-slide .menu-breadcrumbs .element-breadcrumbs .title-breadcrumbs", function () {
                var e = t(this), a = e.attr("data-level"), o = e.closest(".site-navigator-outer"),
                    s = Object.keys(i).length, n = e.closest(".menu-breadcrumbs");
                a != s && (o.find(".slide-level-" + s + ".wrmm-item").length ? o.find(".slide-level-" + s + ".wrmm-item").removeClass("slide-normal") : o.find(".slide-level-" + s + "> ul > li").each(function (e, a) {
                    setTimeout(function () {
                        t(a).removeClass("slide-normal")
                    }, 100 * e)
                }), "all" == a ? setTimeout(function () {
                    var a = o.find(".site-navigator > li").length;
                    o.find(".site-navigator > li").each(function (r, c) {
                        setTimeout(function () {
                            if (t(c).removeClass("slide-left"), a == r + 1) {
                                t(c).closest(".site-navigator").removeAttr("style"), o.find(".slide-normal").removeClass("slide-normal"), o.find(".slide-left").removeClass("slide-left");
                                for (var l = 1; l <= s; l++) o.find(".slide-level-" + l).removeClass("slide-level-" + l);
                                o.find(".active-slide").removeClass("active-slide"), e.closest(".menu-breadcrumbs-outer").removeClass("show-breadcrumbs"), setTimeout(function () {
                                    i = {}, n.find(".item-breadcrumbs").remove()
                                }, 300)
                            }
                        }, 100 * r)
                    })
                }, 100) : setTimeout(function () {
                    var e = o.find(".slide-level-" + a + " > ul > li").length;
                    o.find(".slide-level-" + a + " > ul > li").each(function (r, c) {
                        setTimeout(function () {
                            if (t(c).removeClass("slide-left"), e == r + 1) {
                                o.find(".slide-level-" + a + " ul ul .slide-normal").removeClass("slide-normal"), o.find(".slide-level-" + a + " ul ul .slide-left").removeClass("slide-left");
                                for (var l = a; l <= s; l++) l != a && o.find(".slide-level-" + l).removeClass("slide-level-" + l);
                                for (o.find(".slide-level-" + a + " .active-slide").removeClass("active-slide"), l = a; l <= s; l++) l != a && (delete i[l], n.find('.title-breadcrumbs[data-level="' + l + '"]').parent().remove())
                            }
                        }, 100 * r)
                    })
                }, 100))
            }), t("body").on("click", ".vertical-layout .hb-menu .animation-vertical-accordion .icon-has-children, .hb-menu-outer .animation-vertical-accordion .icon-has-children", function (e) {
                e.preventDefault();
                var a = t(this).closest("li");
                a.hasClass("active-accordion") ? (a.removeClass("active-accordion"), a.find(" > .mm-container-outer").length ? a.find(" > .mm-container-outer").stop(!0, !0).slideUp(300) : a.find(" > .sub-menu").stop(!0, !0).slideUp(300)) : (a.addClass("active-accordion"), a.find(" > .mm-container-outer").length ? a.find(" > .mm-container-outer").stop(!0, !0).slideDown(300) : a.find(" > .sub-menu").stop(!0, !0).slideDown(300))
            }), r(), t(window).resize(_.debounce(function () {
                r()
            }, 300))
        }(), t(".hb-currency .list .item").click(function () {
            var e = t(this), a = e.closest("form"), i = a.find(".currency-value"), o = e.attr("data-id");
            i.val(o), a.submit()
        }), function () {
            var e = [], a = t(".header .site-navigator > li"), i = t("#wpadminbar").outerHeight();
            t(".wr-scroll-animated, .wr-scroll-animated *, .menu-item-link").click(function () {
                if (location.pathname.replace(/^\//, "") == this.pathname.replace(/^\//, "") && location.hostname == this.hostname) {
                    var e = t(this.hash);
                    if (!this.hash.slice(1)) return;
                    var a = t(window).width() > 1024 ? parseInt(WR_Data_Js.offset) : 0;
                    if ((e = e.length ? e : t("[name=" + this.hash.slice(1) + "]")).length) {
                        var o = t(".header .sticky-row"), s = parseInt(o.attr("data-height"));
                        return s || (s = o.height()), t("html,body").stop().animate({scrollTop: e.offset().top - i - a + 1 - s + "px"}, 1200), !1
                    }
                }
            }), t.each(t(".header .site-navigator > li > a"), function () {
                var a = t(this).attr("href");
                if (void 0 != a && null != a.match(/^#/gi)) {
                    var i = t(a);
                    i.length && e.push(i)
                }
            });
            var o = _.debounce(function () {
                var o = t(this).scrollTop();
                t.each(e, function () {
                    var e = t(this), s = e.offset(), n = e.outerHeight(), r = e.attr("id"),
                        c = t(".header .sticky-normal.sticky-row-scroll").height();
                    if (o >= s.top - i - c && o <= s.top + n - i - c) {
                        var l = t('.header .site-navigator > li > a[href="#' + r + '"]').closest("li");
                        return a.removeClass("current-menu-ancestor").removeClass("current-menu-item"), void l.addClass("current-menu-item")
                    }
                })
            }, 10);
            e.length && t(window).scroll(function () {
                requestAnimationFrame(o)
            })
        }(), function () {
            var e = t(".primary-sidebar-sticky");
            if (e.length) {
                var a = t(window), i = t(".primary-sidebar-inner"), o = parseInt(e.css("marginTop")),
                    s = parseInt(i.css("marginTop")), n = t("#wpadminbar"), r = t(".header .sticky-row"),
                    c = r.attr("data-height"), l = n.length ? parseInt(n.height()) : 0,
                    d = (parseInt(e.find(".widget:last").css("marginBottom")), !1), u = !1;
                0 == s ? 0 == o && (e.addClass("fixed-margin"), o = 30, u = !0) : (o = 0, d = !0), i.width(i.width());
                var p = _.debounce(function () {
                    var n = a.width();
                    if (n <= 785) e.removeClass("fixed-bottom fixed-top"); else {
                        if (n <= 1008) var p = 0; else p = parseInt(WR_Data_Js.offset);
                        var h = i.height(), m = i.outerHeight(!0), f = e.height(), v = a.height(),
                            b = r.length ? 0 != c ? parseInt(c) : r.height() : 0, g = l + b + o + p;
                        if (h > v - g - p || m >= f) e.removeClass("fixed-bottom fixed-top"); else {
                            var w = t(this).scrollTop(), _ = e.offset(), y = w + v,
                                C = (f = e.height(), _.top + f + (v - h) - g), x = u ? g - o : g;
                            d && (C -= s), i.css("top", x), w > _.top - g && y < C ? (e.hasClass("fixed-bottom") && e.removeClass("fixed-bottom"), e.addClass("fixed-top"), e.addClass("fixing")) : y > C ? (e.hasClass("fixed-top") && e.removeClass("fixed-top"), e.addClass("fixed-bottom"), e.addClass("fixing")) : e.removeClass("fixed-bottom fixed-top fixing")
                        }
                    }
                }, 10), h = function () {
                    requestAnimationFrame(p)
                };
                t(window).scroll(h), h()
            }
        }(), t.WR.Lightbox(), t.WR.Carousel(), t(".wr-nitro-masonry").each(function (e, a) {
            var i = t(this).data("masonry");
            if (void 0 !== i) {
                var o = i.selector, s = i.columnWidth;
                t(this).WR_ImagesLoaded(function () {
                    t(a).isotope({percentPosition: !0, itemSelector: o, masonry: {columnWidth: s}})
                })
            }
        }), t(".wr-nitro-countdown").each(function (e, a) {
            var i = t(this).data("time");
            if (void 0 !== i) {
                var o = i.day, s = i.month + "/ " + o + "/ " + i.year + " 00:00:00";
                t(a).countdown({
                    date: s, render: function (e) {
                        t(this.el).html("<div class='pr'><span class='db color-primary'>" + this.leadingZeros(e.days, 2) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js.wr_countdown_days + "</span></div><div class='pr'><span class='db color-primary'>" + this.leadingZeros(e.hours, 2) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js.wr_countdown_hrs + "</span></div><div class='pr'><span class='db color-primary'>" + this.leadingZeros(e.min, 2) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js.wr_countdown_mins + "</span></div><div class='pr'><span class='db color-primary'>" + this.leadingZeros(e.sec, 2) + "</span><span class='db tu ls-1 color-dark'>" + WR_Data_Js.wr_countdown_secs + "</span></div>")
                    }
                })
            }
        }), a = t("#wr-back-top"), t(window).scroll(function () {
            0 != t(this).scrollTop() ? a.fadeIn() : a.fadeOut()
        }), a.click(function () {
            t("body, html").animate({scrollTop: 0}, 800)
        }), t(".search-results .search-item .entry-content p, .search-results .search-item .entry-title a").each(function (e, a) {
            var i = t(".search-results .result-list").attr("data-key"), o = t(a).text(), s = i.split(" ");
            t.each(s, function (e, i) {
                var s = new RegExp("(" + i + ")", "gi");
                o = o.replace(s, '<span class="highlight">$1</span>'), t(a).html(o)
            })
        }), function () {
            function e() {
                t.each(t.function_rotate_device, function (t, e) {
                    e.call()
                })
            }

            t(window).resize(function () {
                var a = t(window).height(), i = t(window).width();
                void 0 === window.is_vertical_mobile && (window.is_vertical_mobile = a < i), a < i && window.is_vertical_mobile ? (window.is_vertical_mobile = !1, e()) : a > i && !window.is_vertical_mobile && (window.is_vertical_mobile = !0, e())
            })
        }(), c = t(".gallery-cover"), l = t(".gallery-thumb"), d = !0, !c.length > 0 || (c.owlCarousel({
            items: 1,
            slideSpeed: 2e3,
            nav: !0,
            animateOut: "fadeOut",
            animateIn: "fadeIn",
            autoplay: !0,
            dots: !1,
            loop: !0,
            navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"]
        }).on("changed.owl.carousel", function (t) {
            var e = t.item.count - 1, a = Math.round(t.item.index - t.item.count / 2 - .5);
            a < 0 && (a = e), a > e && (a = 0), l.find(".owl-item").removeClass("current").eq(a).addClass("current");
            var i = l.find(".owl-item.active").length - 1, o = l.find(".owl-item.active").first().index(),
                s = l.find(".owl-item.active").last().index();
            a > s && l.data("owl.carousel").to(a, 100, !0), a < o && l.data("owl.carousel").to(a - i, 100, !0)
        }), l.on("initialized.owl.carousel", function () {
            l.find(".owl-item").eq(0).addClass("current")
        }).owlCarousel({
            items: 6,
            dots: !1,
            nav: !1,
            smartSpeed: 200,
            slideSpeed: 500,
            slideBy: 6,
            responsiveRefreshRate: 100
        }).on("changed.owl.carousel", function (t) {
            if (d) {
                var e = t.item.index;
                c.data("owl.carousel").to(e, 100, !0)
            }
        }), l.on("click", ".owl-item", function (e) {
            e.preventDefault();
            var a = t(this).index();
            c.data("owl.carousel").to(a, 300, !0)
        })), function () {
            if (t(".pagination[layout]").length > 0) {
                var e = parseInt(t(".pagination").find(".page-ajax.enable").attr("data-page")),
                    a = t(".pagination[layout]").attr("layout"),
                    i = t(".pagination[layout-style]").attr("layout-style"),
                    o = t("." + ("masonry" == i ? "wr-nitro-masonry" : "products")), s = t(".products .product").length,
                    n = t(".page-ajax"), r = t(n).find("a").attr("href"), c = ".products", l = ".page-ajax a",
                    d = (t(l).attr("href"), !1), u = 2, p = function () {
                        o.length && o.WR_ImagesLoaded(function () {
                            "masonry" == i ? o.isotope({
                                itemSelector: ".product",
                                masonry: {columnWidth: ".grid-sizer"}
                            }) : o.isotope({itemSelector: ".product", layoutMode: "fitRows"})
                        })
                    }, h = function () {
                        window.location.href.indexOf("?swoof=1&") > -1 && "function" == typeof woof_get_submit_link && (woof_ajax_page_num = u, r = woof_get_submit_link());
                        var a = new RegExp("(/page/" + u + "/|&paged=" + u + ")", "i");
                        r.match(a) || (r += "&paged=" + u), t.get(r, function (a) {
                            var i = t(c, a).wrapInner("").html(), n = t(c, a).find(".product");
                            if (s += n.length, t(".woocommerce-result-count span").html(s), t(i).WR_ImagesLoaded(function () {
                                t(l, a).attr("href"), o.append(n), o.data("isotope") ? o.isotope("appended", n) : p()
                            }), t(l).text("..."), e > u) {
                                if ("plain" == WR_Data_Js.permalink) var h = r.replace(/paged=+[0-9]+/gi, "paged=" + (u + 1)); else h = r.replace(/page\/+[0-9]+\//gi, "page/" + (u + 1) + "/");
                                t(l).attr("href", h)
                            } else t(l).removeAttr("href").addClass("disabled");
                            d = !1, u++
                        })
                    };
                if ("loadmore" == a) t(".page-ajax a").on("click", function (e) {
                    e.preventDefault(), n = t(".page-ajax"), r = t(n).find("a").attr("href"), c = ".products", t(l = ".page-ajax a").attr("href"), r && (t(l).html('<i class="fa fa-circle-o-notch fa-spin"></i>'), h())
                }); else if ("infinite" == a) {
                    var m = function () {
                        o = t("." + ("masonry" == i ? "wr-nitro-masonry" : "products")), n = t(".page-ajax"), r = t(n).find("a").attr("href"), c = ".products", t(l = ".page-ajax a").attr("href");
                        var e = o.offset().top + o.height() - t(window).scrollTop();
                        if (e < window.innerHeight && e > 0 && !d) {
                            if (!r) return;
                            d = !0, t(l).html('<i class="fa fa-circle-o-notch fa-spin"></i>'), h()
                        }
                    };
                    t(window).scroll(function () {
                        requestAnimationFrame(m)
                    })
                }
                p()
            }
        }(), "undefined" != typeof ScrollReveal && (window.sr = ScrollReveal().reveal(".wr-item-animation", {duration: 700})), t(".woof_auto_show").parent().addClass("woof_auto_show_outer"), t("body").on("click", ".yith-wcwl-add-button .add_to_wishlist", function (e) {
            e.preventDefault(), t(this).css("opacity", "0")
        }), t("body").on("click", ".wishlist-submit.add_to_wishlist", function (e) {
            e.preventDefault(), t(this).find(".ajax-loading").show(), t(this).find(".wishlist-icon").hide()
        }), t("body").on("click", ".yith-wcwl-remove-button a", function (e) {
            e.preventDefault();
            var a = t(this), i = a.closest(".yith-wcwl-add-to-wishlist"),
                o = i.find(".yith-wcwl-remove-button .ajax-loading"),
                s = i.find(".yith-wcwl-add-button .add_to_wishlist");
            a.css("opacity", "0"), o.css("visibility", "visible"), s.css("opacity", "1");
            var n = {
                action: "wr_remove_product_wishlish",
                _nonce: _nonce_wr_nitro,
                product_id: a.attr("data-product-id")
            };
            t.ajax({
                type: "POST", url: WRAjaxURL, data: n, success: function (t) {
                    "true" == t.status && (o.css("visibility", "hidden"), i.find(".yith-wcwl-remove-button").hide(), i.find(".yith-wcwl-add-button").show(), a.css("opacity", "1"))
                }
            })
        }), t(".single-product .product-type-booking").length && t(".woocommerce-message").show(), t("body").delegate(".product__compare .product__btn", "click", function () {
            return t(this).next().find(".compare").trigger("click"), !1
        }), u = function (t) {
            return parseInt(Number(t)) == t ? t : t.toFixed(2)
        }, t("body").on("click", ".quantity a.plus", function (e) {
            var a = t(this).parent().parent().find("input"), i = Number(a.attr("step")), o = Number(a.attr("max")),
                s = u(Number(a.val()) + i);
            0 != o && s > o && (s = o), a.val(s), a.trigger("change")
        }), t("body").on("click", ".quantity a.minus", function (e) {
            var a = t(this).parent().parent().find("input"), i = Number(a.attr("step")), o = u(Number(a.val()) - i);
            o < i && (o = i), a.val(o), a.trigger("change")
        }), t("body").on("click", ".btn-quickview", function (e) {
            var a = t(this);
            a.addClass("loading");
            var i = {action: "wr_quickview", product: a.attr("data-prod"), wr_view_image: "wr_quickview"};
            t.post(WRAjaxURL, i, function (e) {
                (e = t(e)).find(".wr-custom-attribute .has-image-gallery[data-value]").each(function () {
                    var e = t(this).attr("data-href") + "&wr_view_image=wr_quickview";
                    t(this).attr("data-href", e)
                }), void 0 !== t.fn.magnificPopup && t.magnificPopup.open({
                    items: {src: e},
                    mainClass: "mfp-fade mfp-quickview",
                    removalDelay: 300,
                    callbacks: {
                        open: function () {
                            "undefined" != typeof wc_add_to_cart_variation_params && t(".variations_form").each(function () {
                                t(this).wc_variation_form().find(".variations select:eq(0)").change()
                            })
                        }
                    }
                }), a.removeClass("loading"), setTimeout(function () {
                    t(".quickview-modal form").hasClass("variations_form") && t(".quickview-modal form.variations_form").wc_variation_form(), t(".wr-images-quickview").WR_ImagesLoaded(function () {
                        var e = t(".wr-images-quickview").outerHeight();
                        t(".quickview-modal .info").css({height: e, overflow: "auto"})
                    })
                }, 100)
            }), e.preventDefault(), e.stopPropagation()
        }), t("body").on("click", ".mfp-quickview .open-popup-link", function (e) {
            e.preventDefault(), e.stopPropagation(), t(".quickview-modal").addClass("active-sizeguide"), t(".quickview-modal-inner").hide()
        }), t("body").on("click", ".wr-sizeguide .sizeguide-close", function (e) {
            t(".quickview-modal").removeClass("active-sizeguide"), t(".quickview-modal-inner").show()
        }), t("body").on("click", ".product-type-simple .btn-buynow, .wr-buy-now .btn-buynow", function (e) {
            var a = t(this);
            a.addClass("loading");
            var i = {action: "wr_quickbuy", product_id: a.attr("data-product-id")};
            void 0 != a.attr("data-checkout") && void 0 != a.attr("data-payment-info") && (i.shortcode_checkout = a.attr("data-checkout"), i.shortcode_payment = a.attr("data-payment-info")), t.ajax({
                type: "POST",
                url: WRAjaxURL,
                data: i,
                success: function (e) {
                    "true" == e.status ? "redirect" == e.type ? window.location.href = e.checkout_url : "modal" == e.type && (-1 != e.checkout_url.indexOf("?") ? e.checkout_url = e.checkout_url + "&wr-buy-now=check-out" : e.checkout_url = e.checkout_url + "?wr-buy-now=check-out", void 0 !== t.fn.magnificPopup && t.magnificPopup.open({
                        items: {src: e.checkout_url},
                        type: "iframe",
                        mainClass: "mfp-fade wr-buy-now",
                        removalDelay: 300
                    })) : e.status, a.removeClass("loading")
                }
            }), e.preventDefault(), e.stopPropagation()
        }), t(".btn-newacc, .register .btn-backacc").on("click", function (e) {
            t(".form-container.login, .form-container.register").toggleClass("opened")
        }), t(".btn-lostpw, .lost-password .btn-backacc").on("click", function (e) {
            t(".form-container.login, .form-container.lost-password").toggleClass("opened")
        }), t("body").on("click", ".wc-switch a", function (e) {
            e.preventDefault();
            var a = t(this);
            if (!a.hasClass("active") && !a.hasClass("loading")) {
                a.closest(".wc-switch").find("a").removeClass("active"), a.addClass("active");
                var i = a.attr("data-layout"), o = function (t) {
                    var e = "";
                    if (-1 != t.indexOf("?")) {
                        var a = "list" == WR_Data_Js.wc_archive_style ? "grid" : WR_Data_Js.wc_archive_style;
                        e = -1 != t.indexOf("switch=" + a) ? t.replace("switch=" + a, "switch=" + i) : -1 != t.indexOf("switch=list") ? t.replace("switch=list", "switch=" + i) : -1 != t.indexOf("?switch=") || -1 != t.indexOf("&switch=") ? (e = t.replace("switch=", "")) + "&switch=" + i : t + "&switch=" + i
                    } else e = t + "?switch=" + i;
                    return e
                }, s = o(window.location.href);
                if (history.pushState({}, "", s), t("#shop-main .woocommerce-pagination ul li a.page-numbers").each(function (e, a) {
                    var i = t(this), s = i.attr("href");
                    i.attr("href", o(s))
                }), 1 == t("#shop-main .products").length) a.addClass("loading"), t.get(s, function (e) {
                    var i = t(".products", e);
                    if (i.length) {
                        i.addClass("products-ajax").hide();
                        var o = i[0].outerHTML, s = t("#shop-main .products");
                        s.after(o), s.fadeOut(200, function () {
                            t("#shop-main .products-ajax").show()
                        })
                    }
                    a.removeClass("loading")
                }); else {
                    var n = t("#shop-main .products." + i + "-layout");
                    t("#shop-main .products:not(." + i + "-layout)").hide(), n.show()
                }
            }
        }), t("#tab-description").show().closest(".description_tab").addClass("active"), t(".accordion-tabs .tab-heading").click(function (e) {
            e.preventDefault();
            var a = t(this), i = a.closest(".accordion_item"), o = a.closest(".accordion-tabs");
            i.hasClass("active") ? (i.removeClass("active"), i.find(".entry-content").stop(!0, !0).slideUp()) : (o.find(".accordion_item").removeClass("active"), i.addClass("active"), o.find(".entry-content").stop(!0, !0).slideUp(), i.find(".entry-content").stop(!0, !0).slideDown())
        }), 0 != t(".p-single-action .single_add_to_cart_button").length && t(window).load(function () {
            var e = t(".footer"), a = t(".p-single-action .single_add_to_cart_button"), i = a.offset().top + a.height(),
                o = e.height(), s = t(window).height(), n = t(document).height(),
                r = (t(window).width() > 1024 ? parseInt(WR_Data_Js.offset) : 0) + 10, c = function () {
                    var a = t(window).scrollTop(), c = t(".actions-fixed"), l = e.offset().top - c.height() - 15 - a;
                    a > i ? c.slideDown() : c.slideUp(), a + s < n - o ? c.css({
                        bottom: r + "px",
                        top: "auto"
                    }) : c.css({bottom: "auto", top: l})
                };
            t(window).scroll(function () {
                requestAnimationFrame(c)
            }), t(".wr_add_to_cart_button i").on("click", function () {
                if (t(this).parent().hasClass("wr-notice-tooltip")) {
                    var e = t("html, body"), a = t(".variations_form"),
                        i = t("#wpadminbar").length ? t("#wpadminbar").outerHeight() : "";
                    e.animate({scrollTop: a.offset().top - e.offset().top + e.scrollTop() - i - 20}, 800)
                }
            })
        }), t(".p-video").length > 0 && (t(".p-video-link").magnificPopup({type: "iframe"}), t(".p-video-file").magnificPopup({type: "inline"})), t(".wr-open-cf7").length > 0 && t(".wr-open-cf7").magnificPopup({
            type: "inline",
            removalDelay: 300,
            mainClass: "mfp-fade"
        }), r(), function () {
            if (t(".term-description").length > 0) {
                var e = t(".term-description");
                e.height() > 78 && (e.wrapInner('<div class="term-description-inner"></div>'), e.append('<a class="term-more dib mgt10 bg-primary color-white" href="#">' + WR_Data_Js.show_more + "</a>"), e.children(".term-description-inner").css({
                    height: 78,
                    overflow: "hidden"
                }), t("body").on("click", ".term-more", function () {
                    e.children(".term-description-inner").toggleClass("term-show-hide"), t(this).text() == WR_Data_Js.show_more ? t(this).text(WR_Data_Js.show_less) : t(this).text(WR_Data_Js.show_more)
                }))
            }
        }(), t(".flex-control-thumbs").length > 0 && t(".flex-control-thumbs li").length > 5 && t(".woocommerce-product-gallery__wrapper").WR_ImagesLoaded(function () {
            setTimeout(function () {
                t(".flex-control-thumbs").scrollbar()
            }, 50)
        }), t(".woocommerce-product-gallery--with-nav").length > 0 && t(".woocommerce-product-gallery--with-nav").flexslider({
            animation: "slide",
            controlNav: !1,
            animationLoop: !1,
            slideshow: !1,
            itemWidth: 90,
            itemMargin: 10,
            asNavFor: ".woocommerce-product-gallery--with-images"
        }), p = t(".nitro-member .member a"), h = t(".nitro-member .info > p").height(), t(".nitro-member.style-2 .info").css("bottom", -(h + 16)), p.mouseenter(function (e) {
            t(this).find(".name").fadeIn()
        }), p.mouseleave(function (e) {
            t(this).find(".name").hide()
        }), p.mousemove(function (e) {
            var a = e.pageX, i = e.pageY, o = t(this).offset(), s = a - o.left, n = i - o.top;
            t(this).find(".name").css({top: n + 20 + "px", left: s - 15 + "px"})
        }), p.on("click", function () {
            var e = t(this), a = e.closest(".member"), i = e.closest(".nitro-member"), o = i.find(".member"),
                s = o.index(a) + 1, n = o.length, r = i.width(), c = a[0].getBoundingClientRect(),
                l = parseInt(r / c.width), d = e.next();
            if (n <= l) var u = n; else if (s <= l) var u = l - s + s; else n < (u = (parseInt(s / l) + (s % l == 0 ? 0 : 1)) * l) && (u = n);
            var p = i.find(".member").get(u - 1);
            if (p = t(p).next(), a.hasClass("active-member")) a.removeClass("active-member"), t(".member-container").slideUp(500, function () {
                t(this).remove()
            }); else {
                o.removeClass("active-member"), a.addClass("active-member");
                var h = i.find(".member").get(u - 1);
                p.hasClass("member-container") ? t(".member-container").fadeOut(300, function () {
                    t(this).html(d.html()), t(this).fadeIn(300)
                }) : t(".member-container").length ? t(".member-container").slideUp(500, function () {
                    t(this).remove();
                    var e = i.find(".member").get(u - 1);
                    t(e).after('<div class="member-container clear">' + d.html() + "</div>"), t(".member-container").slideDown()
                }) : (t(".member-container").remove(), h = i.find(".member").get(u - 1), t(h).after('<div class="member-container clear">' + d.html() + "</div>"), t(".member-container").slideDown(500))
            }
        }), (m = t(".galleries .nitro-gallery-masonry")).length && m.each(function () {
            var e = t(this), a = e.attr("data-layout");
            e.WR_ImagesLoaded(function () {
                "masonry" == a ? e.isotope({
                    filter: "*",
                    percentPosition: !0,
                    masonry: {columnWidth: ".grid-sizer"}
                }) : e.isotope({filter: "*", percentPosition: !0, layoutMode: "fitRows"})
            })
        }), t(".gallery-cat a").click(function () {
            var e = t(this).attr("data-filter");
            t(this).closest(".galleries").find(".nitro-gallery-masonry").isotope({
                filter: e,
                transitionDuration: "0.3s"
            })
        }), t(".gallery-cat").find("a").click(function () {
            var e = t(this);
            if (e.hasClass("selected")) return !1;
            e.parents(".gallery-cat").find(".selected").removeClass("selected"), e.addClass("selected")
        }), window.innerWidth <= 769 && (t(".filter-on-mobile").on("click", function () {
            t(this).next().slideToggle()
        }), t(".gallery-cat a[data-filter]").on("click", function () {
            var e = t(this).text();
            t(this).parent().siblings(".filter-on-mobile").find("span").text(e), t(this).parent().slideToggle()
        })), f = t('.sc-cat-list[data-expand="true"]').children("a"), t('.sc-cat-list[data-expand="true"] ul').hide(), f.on("click", function () {
            t(this).next().slideToggle()
        }), t(".sc-cat-mobile").length > 0 && t(".sc-cat-mobile").on("click", function () {
            t(this).toggleClass("expanded").next().toggleClass("expanded")
        }), t(".wr-buy-now .btn-buynow").click(function () {
            t("body").hasClass("woocommerce-page") || t("body").addClass("woocommerce-page")
        }), t(".nitro-separator").each(function () {
            var e = t(this).find("span").width();
            switch (t(this).attr("data-align")) {
                case"left":
                    t("body").hasClass("rtl") ? t(this).find(".sep").css("margin-right", e + 20) : t(this).find(".sep").css("margin-left", e + 20);
                    break;
                case"right":
                    t("body").hasClass("rtl") ? t(this).find(".sep").css("margin-left", e + 20) : t(this).find(".sep").css("margin-right", e + 20);
                    break;
                case"center":
                    var a = (t(this).width() - e) / 2 - 20;
                    t(this).find(".sep-left, .sep-right").css("width", a)
            }
        }), n(), v = t(".nitro-timeline.style-2"), b = function () {
            v.removeClass("style-2").addClass("style-1"), t(window).width() < 568 ? v.removeClass("style-2").addClass("style-1") : v.removeClass("style-1").addClass("style-2")
        }, v.length > 0 && (b(), t(window).resize(function () {
            b()
        })), (g = function () {
            t.each(t(".list-blog.has-featured-img .has-post-thumbnail .entry-title"), function () {
                var e = t(this), a = e.closest(".has-post-thumbnail");
                a.removeClass("blog-res"), e.width() < 180 && a.addClass("blog-res")
            })
        })(), t(window).resize(function () {
            g()
        }), t(".product-categories .cat-parent > .children").before('<span class="fa fa-angle-down pa tc"></span>'), e() && t(".wr-mobile .widget_nav_menu .menu-item-has-children > .sub-menu").before('<span class="fa fa-angle-down pa tc"></span>'), t("body").on("click", ".product-categories .cat-parent .fa", function () {
            t(this).closest(".cat-parent").toggleClass("active").find("> .children").stop(!0, !1).slideToggle()
        }), t("body").on("click", ".widget_nav_menu .menu-item-has-children .fa", function () {
            t(this).closest(".menu-item-has-children").toggleClass("active").find("> .sub-menu").stop(!0, !1).slideToggle()
        }), t(".product-categories .count").each(function () {
            var e = t(this), a = e.text().replace("(", "").replace(")", "");
            e.text(a)
        }), (w = t(".pageloader")).length && (t(window).on("pageshow", function (t) {
            void 0 != t.originalEvent && t.originalEvent.persisted && (w.hide(), w.children().hide())
        }), t(window).on("beforeunload", function () {
            w.fadeIn(300, function () {
                w.children().fadeIn(300)
            })
        }), w.fadeOut(800), w.children().fadeOut("slow"))
    }), t(window).load(function () {
        !function () {
            var e = t(".wr-nitro-horizontal");
            if (e.length > 0) {
                var a = e.data("owl-options");
                if (void 0 !== a) {
                    var i = "true" == a.autoplay, o = "true" == a.dots, s = (a.loop, "true" == a.mousewheel);
                    e.owlCarousel({
                        items: 4,
                        loop: !0,
                        nav: !1,
                        autoplay: i,
                        dots: o,
                        autoWidth: !0
                    }), 1 == s && e.on("mousewheel", ".owl-stage", function (t) {
                        t.deltaY > 0 ? e.trigger("prev.owl") : e.trigger("next.owl"), t.preventDefault()
                    })
                }
            }
        }()
    })
}(jQuery);
