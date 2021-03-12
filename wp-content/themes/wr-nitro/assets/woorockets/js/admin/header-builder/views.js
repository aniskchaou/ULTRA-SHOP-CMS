void function (exports, $, _, Backbone) {

	// exports.log = console.log.bind(console)
	exports.log = function(){};

	exports.WRNitro_Fonts = [];

	exports.WRNitro_View = B.View.extend({
		constructor: function () {
			Object.defineProperty(this, 'baseView', {
				get: function () {
					var root = this;
					var parent = this.superView;
					while (parent) {
						root = parent;
						if (parent instanceof WRNitro_HeaderBuilder_Base_View) {
							break;
						}
						parent = parent.superView;
					}
					return root;
				},
			})
			B.View.apply(this, arguments);
		},
		loadFont: function (fontName) {
			if (fontName) {
				if (WRNitro_Fonts.indexOf(fontName) < 0) {
					WRNitro_Fonts.push(fontName);
					$('head').append('<link href="//fonts.googleapis.com/css?family=' + fontName.replace(/\s+/g, '+') + '" data-noprefix rel="stylesheet" type="text/css">');
				}
			}
		},
		resize_menu: _.debounce( function(){
			var view = this;

			// Each rows
			$.each( $( '.hb-horizontal .hb-desktop-view .hb-row .hb-items, .hb-horizontal .hb-mobile-view .hb-row .hb-items, .hb-vertical .hb-mobile-view .hb-row .hb-items' ), function() {
				var row = $(this);

				var menu_row = row.find( '.header-item.item-menu[data-vertical-layout-style="text"]' );
				var center_element = row.find( '.header-item.element-center' );
				var list_flex = row.find( '.item-flex' );

				/* Reset */
				menu_row.find( '.menu > .menu-item' ).removeClass( 'item-hidden' );
				menu_row.find( '.menu-more' ).remove();
				row.find( '.element-center' ).removeAttr( 'style' );
				list_flex.removeAttr( 'style' );
				list_flex.removeClass( 'not-flex' );

				// Set center element and menu more has menu element in row
				if ( menu_row.length ) {

					// Menu element is center element
					if ( center_element.hasClass( 'item-menu' ) && center_element.attr( 'data-vertical-layout-style' ) == 'text' ) {
						var width_parent = row.width();

						var prev_menu = center_element.prevAll( ':not(".item-flex"):not([data-mobile-layout="text"])' );
						var next_menu = center_element.nextAll( ':not(".item-flex"):not([data-mobile-layout="text"])' );
						var width_prev_menu = view.get_width_menu_center( prev_menu );
						var width_next_menu = view.get_width_menu_center( next_menu );
						var width_spacing_center = ( width_prev_menu > width_next_menu ) ? width_prev_menu : width_next_menu;
						var width_menu_center = center_element.outerWidth( true );
						var width_calc_center = width_parent - ( width_spacing_center * 2 );

						if( width_menu_center >= width_calc_center ) {
							view.resize_menu_list( center_element, width_calc_center );
						}

						var spacing_average = parseInt( ( width_parent - center_element.outerWidth( true ) ) / 2 );

						view.resize_menu_list( prev_menu, spacing_average );
						view.resize_menu_list( next_menu, spacing_average );

						// Set margin left for element center 
						view.calc_element_center( prev_menu, spacing_average, center_element );

					// Menu element isn't center element but has center element
					} else if ( center_element.length ) {
						/* Reset */
						center_element.removeAttr( 'style' );

						var width_parent = row.width();
						var spacing_average = parseInt( ( width_parent - center_element.outerWidth( true ) ) / 2 );
						var prev_menu = center_element.prevAll( ':not(".item-flex"):not([data-mobile-layout="text"])' );
						var next_menu = center_element.nextAll( ':not(".item-flex"):not([data-mobile-layout="text"])' );

						view.resize_menu_list( prev_menu, spacing_average );
						view.resize_menu_list( next_menu, spacing_average );

						// Set margin left for element center 
						view.calc_element_center( prev_menu, spacing_average, center_element );

					// Haven't center element
					} else {
						var width_parent = row.width();
						view.resize_menu_list( row.find( '.header-item:not(.item-flex):not([data-mobile-layout="text"])' ), width_parent );
					}

				// Set center element not menu element in row
				} else if ( center_element.length ) {
					var width_parent = row.width();
					var spacing_average = parseInt( ( width_parent - center_element.outerWidth( true ) ) / 2 );
					var prev_menu = center_element.prevAll( ':not(".item-flex"):not([data-mobile-layout="text"])' );

					// Set margin left for element center 
					view.calc_element_center( prev_menu, spacing_average, center_element );
				}
			} );
		}, 400 ),
		calc_element_center: function( el_prev, spacing_average, center_element ){
			var width_all_el = 0;
			var margin_left = 0;

			$.each( el_prev, function(){
				var _this = $(this);
				var width = _this.outerWidth( true );
				width_all_el += width;
			} );

			if( width_all_el < spacing_average ) {
				margin_left = spacing_average - width_all_el;
			}

			if( margin_left ) {
				var lits_flex = center_element.prevAll( '.item-flex' );

				if( lits_flex.length ) {
					var width_flex = parseInt( margin_left/lits_flex.length )
					lits_flex.width( width_flex );
					lits_flex.addClass( 'not-flex' );
				} else {
					center_element.css( 'paddingLeft', margin_left );
				}
			}
		},
		resize_menu_list: function ( list_element, width_parent ) {
			var list_menu = [];
			var el_not_menu_flex = [];

			$.each( list_element, function() {
				var _this = $( this );
				if ( _this.hasClass( 'item-menu' ) && _this.attr( 'data-vertical-layout-style' ) == 'text' ) {
					list_menu.push( _this );
				} else {
					el_not_menu_flex.push( _this );
				}
			} )

			var count_menu = list_menu.length;

			$.each( el_not_menu_flex, function() {
				width_parent -= $( this ).outerWidth( true );
			} );

			var width_rest = parseInt( width_parent / count_menu );
			var width_rest_long = 0;
			var is_plus_rest_long = false;
			var menus_more = [];

			// Plus for width rest if menu not exceeds
			var i = 0;
			$.each( list_menu, function() {
				var width_menu = $( this ).outerWidth( true );
				if ( width_menu < width_rest ) {
					width_rest_long += width_rest - width_menu;
				} else {
					menus_more.push( i );
				}
				i++;
			} );

			width_rest += parseInt( width_rest_long / menus_more.length );

			$.each( menus_more, function( key, val ) {
				var _this = $( list_menu[ val ] );
				var menu_items = _this.find( '.menu > .menu-item' );

				if ( ! menu_items.length ) {
					return;
				}

				var width_this = _this.outerWidth( true );
				var width_outer = _this.find( '.item-content' ).width();
				var width_rest_item = width_rest - ( ( width_this - width_outer ) + 52 );
				var width_menu_items = 0;
				var show_menu_more = false;

				$.each( menu_items, function( key, val ) {
					width_menu_items += $( this ).outerWidth( true );
					if ( width_menu_items >= width_rest_item ) {
						$( this ).addClass( 'item-hidden' );
						show_menu_more = true;
					}
					;
				} );

				if ( show_menu_more ) {
					_this.find( '.item-content' ).append( '<div class="menu-more" style="' + ( ( _this.attr( 'data-color-icon-more' ) !== undefined && _this.attr( 'data-color-icon-more' ) ) ? ( 'color:' + _this.attr( 'data-color-icon-more' ) ) : '' ) + '"><div class="icon-more"><i class="fa fa-bars"></i><i class="fa fa-caret-down"></i></div><div class="nav-more"></div></div>' );
				}
			} );
		},
		get_width_menu_center: function ( element ){
			var width_all = 0;

			$.each( element, function(){
				var _this = $(this);

				if( _this.hasClass( 'item-menu' ) && _this.attr( 'data-vertical-layout-style' ) == 'text' ) {
					var width = ( _this.outerWidth( true ) - _this.find( '.item-content' ).width() ) + 47;
					width_all += width;
				} else {
					var width = _this.outerWidth( true );
					width_all += width;
				}
			} );

			return width_all;
		},
	});
	exports.WRNitro_HeaderBuilder_ItemView = WRNitro_View.extend({
		init: function () {
			var view = this;
			this.listenTo(this.model.get('style'), 'change', this.updateCSS);
			this.listenTo(this.model.get('style'), 'change:fontFamily', this.loadFont(this.model.get('style.fontFamily')));
			this.listenTo(this.model, 'delete:item', this.deleteItem);
			this.listenTo(this.model, 'change:ID', this.updateID);
			this.listenTo(this.model, 'change:className', this.updateClass);
			this.listenTo(this.model, 'change:selected', this.changeSelected);
			this.listenTo(this.model, 'checkCenterElment', this.checkCenterElment);

			this.listenTo( this.model, 'change:centerElement',  function(){
				if( this.model.get( 'centerElement' ) ) {
					this.$el.siblings().removeClass( 'element-center' );
					this.$el.addClass( 'element-center' );

					this.model.collection.invoke('set', 'centerElement', false, { silent: true } );
					this.model.set( 'centerElement', true, { silent: true } );
				} else {
					this.$el.removeAttr( 'style' );
					this.$el.removeClass( 'element-center' );

					this.model.set( 'centerElement', false, { silent: true } );
					//this.resize_menu();
				}
			} );

			this.updateCSS();
			this.$el.on('mousedown', function(){
				$('.hb-items').trigger('deselect:items');
			});

			this.setElementCenter();
		},
		ajaxURL: wr_site_data.ajax_url,
		checkCenterElment: function( collection ){
			collection.countEnableCenter = collection.countEnableCenter || 0;
			collection.countEnableCenter += this.model.get('centerElement') ? 1 : 0;
		},
		setElementCenter: function(){
			if( this.model.get('centerElement') ) {
				this.$el.addClass( 'element-center' );
			} else {
				this.$el.removeClass( 'element-center' );
			}
		},
		changeSelected: function () {
			if( this.model.get('selected') ) {
				this.$el.addClass( 'active-element' );
			} else {
				this.$el.removeClass( 'active-element' );
			}
		},
		updateCSS: function () {
			this.$( '.header-item-inner' ).css(this.model.get('style').toJSON());
			if (this.model.get('style.backgroundImage') !== 'undefined' && this.model.get('style.backgroundImage') != ' ' && this.model.get('style.backgroundImage')) {
				this.$( '.header-item-inner' ).css({backgroundImage: 'url(' + this.model.get('style.backgroundImage') + ')'});
			}
		},
		updateID: function () {
			var id = ( typeof this.model.get('ID') != 'undefined' ) ? this.model.get('ID').replace(/\s+/, '') : null;
			if (!id) {
				this.$el.removeAttr('id');
			}
			else {
				this.$el.attr('id', id);
			}
		},
		updateClass: function () {
			var className = ( typeof this.model.get('className') != 'undefined' ) ? this.model.get('className').replace(/\s+/, '') : '';
			this.$el.attr('class', 'header-item item-' + this.model.get('_rel') + ' ' + className);
		},
		updateIndex: function (e) {
			var index = this.$el.index();
			this.model.set('index', index);
		},
		deleteItem: function(){
			this.remove();
			this.model.destroy();
		},
		deleteModel: function (e) {
			e.stopPropagation();
			var view           = this;
			var delete_confirm = confirm( 'Do you really want to delete this item?' );
			if( delete_confirm ) {
				this.rootView.closeInspector();
				view.remove();
				view.model.destroy();
			}
		},
		sortToNewRow: function (e, data) {
			if (typeof data.from !== 'undefined' && typeof data.to !== 'undefined') {
				var model = this.model.toJSON();
				model.index = data.index;
				$(data.to).trigger('addItem', {model: model, index: data.index});
				this.deleteItem();
				// this.model.destroy();
				// _.delay(function(){
				// 	$(data.to).trigger('resetCollection');
				// }, 11)
			}
		}
	});

	exports.WRNitro_HeaderBuilder_ItemsView = B.CollectionView.extend({
		events: {
			'addItem': 'addItem',
			'resetCollection': 'resetCollection',
			'delete:selected:item': 'deleteItems',
			'deselect:items': 'deselectItems',
			'centerElementTo': function(e, item){
				_.delay(_.bind(function(){
					this.countCenterElement();

					var view = this;
					if ( this.countEnableCenter > 1 ) {
						view.$( '.element-center:first' ).removeClass( 'element-center' );

						this.collection.invoke('set', 'centerElement', false, { silent: true } );
						_.delay(function(){
							//sorting item's model
							// log(view)
							var interval = setInterval(function(){
								if(view.lastItem) {
									log(view.lastItem)
									view.lastItem.set('centerElement', true, { silent: true } );
									clearInterval(interval);
								}
							}, 10)
						}, 10)
					}
					this.countEnableCenter = 0;
				}, this), 10)
			},
		},
		resetCollection: function(){
			this.$el.html('');
			this.reset();
		},
		countCenterElement: function(){
			this.collection.invoke( 'trigger', 'checkCenterElment', this );
		},
		ready: function () {
			var view = this;
			this.sortable = new Sortable(this.el, {
				group: 'items', animation: 200,

				onEnd: function (evt) {
					var item = evt.item;
					var from = evt.from;
					var to = $(item).parents('.hb-items').get(0);
					//$(item).trigger('removeItem', {to: to, index: 1});
					if (from == to) {
						view.$el.find('div.header-item').trigger('updateIndex');
					}
					else {
						$(from).find('div.header-item').trigger('updateIndex');
						$(to).find('div.header-item').trigger('updateIndex');

						var index = $(item).index();
						$(item).trigger('sortToNewRow', {to: to, index: index, from: from});

					};

					_.defer(function(){
						$(to).trigger( 'centerElementTo', item )
					} );

					log( 'End drog item' );
				}
			});
		},
		addItem: function (e, data) {
			var view = this;
			$(data.el).parents('.hb-items').find('div.header-item').trigger('updateIndex');
			_.delay(function () {
				view.lastItem = view.collection.add( JSON.parse(JSON.stringify(data.model)) );
				
				$(data.el).remove();
			}, 10);
		},
		deleteItems: function () {
			_.each( this.collection.where({selected: true}), function(model){
				model.trigger('delete:item')
			})
		},
		deselectItems: function () {
			_.invoke(this.collection.where({selected: true}), 'set', {selected: false});
		},
		itemViews: {
			"search": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.searchInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.searchInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
					this.listenTo(this.model, 'change:ID', this.updateID);
					this.listenTo(this.model, 'change:searchStyle', this.backgroundStyle);
					this.listenTo(this.model, 'change:layout', this.updateLayout);
					this.listenTo(this.model, 'change:iconColor', this.updateIconCSS);
					this.listenTo(this.model, 'change:iconFontSize', this.updateIconCSS);
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.listenTo(this.model, 'change:liveSearch.active', this.show_category);
					this.listenTo(this.model, 'change:liveSearch.show_category', this.show_category);
					this.listenTo(this.model, 'change:placeholder', this.updatePlaceholder);
					this.listenTo(this.model, 'change:widthInput', this.updateWidthInput );
					this.listenTo(this.model, 'change:buttonType', this.updateTypeButton );
					this.listenTo(this.model, 'change:textButton', this.updateStyleButton );
					this.listenTo(this.model, 'change:textColorButton', this.updateStyleButton );
					this.listenTo(this.model, 'change:bgColorButton', this.updateStyleButton );
				},
				ready: function () {
					this.updateLayout();
					this.updateIconCSS();
					this.backgroundStyle();
					this.updateAlignVertical();
					this.show_category();
					this.updatePlaceholder();
					this.updateWidthInput();
                    this.init();
                    this.updateTypeButton();
                    this.updateStyleButton();
				},
				updateWidthInput: function () {
					var widthInput = this.model.get('widthInput');
					this.$( '.text-search' ).width( widthInput );
				},
				updateTypeButton: function () {
					var buttonType = this.model.get('buttonType');

					if( buttonType == 'text' ) {
						this.$( '.icon-button' ).hide();
						this.$( '.text-button' ).show();
					} else {
						this.$( '.icon-button' ).show();
						this.$( '.text-button' ).hide();
					}
				},
				updateStyleButton: function () {
					var textButton = this.model.get('textButton');
					var textColorButton = this.model.get('textColorButton');
					var bgColorButton = this.model.get('bgColorButton');

					this.$( '.text-button' ).css( { color: textColorButton, backgroundColor : bgColorButton } ).text( textButton );

				},
				updatePlaceholder: function () {
					var placeholder = this.model.get('placeholder');
					this.$( '.text-search' ).attr( 'placeholder', placeholder );
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				backgroundStyle: function () {
					var style = this.model.get('searchStyle');
					this.$('.item-content').attr( 'data-background-style', style );
				},
				updateLayout: function () {
					var layout = this.model.get('layout');
					this.$('.item-content').attr( 'data-layout', layout );

					if( layout != 'boxed' ) {
						this.model.set( 'buttonType', 'icon' );
					}

					this.show_category();
				},
				updateIconCSS: function () {
					this.$('.icon-button').css( { color : this.model.get('iconColor'), fontSize : this.model.get('iconFontSize') + 'px' } );
				},
				show_category: function () {
					if( window.active_wrls == 1 ) {
						var active_wrls = this.model.get('showLiveSearch');
						if( active_wrls ) {
							var show_category = this.model.get('liveSearch.show_category');

							if( show_category ) {
								var layout = this.model.get('layout');

								if( layout == 'boxed' ) {
									this.$( '.header-item-inner' ).addClass( 'has-category' );
									this.$('.all-category').show();
									return;
								}
							}
						}
					}

					this.$( '.header-item-inner' ).removeClass( 'has-category' );
					this.$('.all-category').hide();
				}
			}),
			"menu": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': 'showInspector',
					'click a': 'showInspector',
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				showInspector: function (e) {
					this.rootView.menuInspectorView.inspect(this.model);
					this.rootView.closeInspector();
					this.rootView.menuInspectorView.open(this.el);
					e.stopPropagation();
					e.preventDefault();
				},
				initialize: function () {
					this.listenTo(this.model.get('link.style'), 'change',                 this.updateLinkCSS);
					this.listenTo(this.model.get('style'), 'change:backgroundColorHover', this.updateBackgroundHover);
					this.listenTo(this.model, 'change:className',                         this.updateClass);
					this.listenTo(this.model, 'change:menuID',                            this.renderMenuItem);
					this.listenTo(this.model, 'change:alignVertical',                     this.updateAlignVertical);
					this.listenTo(this.model, 'change:layoutStyle',                       this.updateLayoutStyle);
					this.listenTo(this.model, 'change:menuStyle',                         this.checkMenuStyle);
					this.listenTo(this.model, 'change:textAlign',                         this.updateTextAlign);
					this.listenTo(this.model, 'change:unitWidthSidebar',                  this.updateUnitWidthSidebar);
					this.listenTo(this.model, 'change:itemSpacing',                       this.updateItemSpacing);
					this.listenTo(this.model, 'change:layoutStyleMobile',                 this.updateLayoutStyleMobile);
					this.listenTo(this.model, 'change:layoutStyleMobile',                 this.updateLayoutStyleMobileData);
				},
				ready: function () {
					this.listenTo(this.rootView.model, 'change:layout',  this.renderMenuItem);
					this.listenTo(this.model, 'change:layoutStyle',      this.renderMenuItem);
					this.listenTo(this.model, 'change:textSettings',     this.updateTextCSS);
					this.listenTo(this.model, 'change:spacing',          this.updateSpacing);
					this.listenTo( this.model, 'change:iconColor',       this.updateColorIcon );
					this.listenTo( this.model, 'change:iconColorMobile', this.updateColorIcon );
					this.init();

					if ( this.model.get('textSettings.fontFamily') ) {
						this.loadFont( this.model.get('textSettings.fontFamily') + ':' + this.model.get('textSettings.fontWeight') );
					}

					this.updateLayoutStyleMobile();
					this.checkMenuStyle();
					this.renderMenuItem();
					this.updateTextCSS();
					this.updateSpacing();
					this.updateAlignVertical();
					this.updateLayoutStyle();
					this.updateTextAlign();
				},
				updateItemSpacing: function () {
					var item_spacing = this.model.get('itemSpacing');

					if( item_spacing === '' ) {
						item_spacing = 30;
					}

					if( this.rootView.model.get('desktop.settings.type') == 'vertical' ) {
						this.$( '.menu .menu-item' ).css( { 'paddingTop': item_spacing/2, 'paddingBottom': item_spacing/2 } );
					} else {
						this.$( '.menu .menu-item' ).css( { 'paddingRight': item_spacing/2, 'paddingLeft': item_spacing/2 } );    
					}
				},
				updateUnitWidthSidebar: function () {
					var width = this.model.get('widthSidebar');
					var unit = this.model.get('unitWidthSidebar');
					if( unit == '%' && width > 100 ) {
						this.model.set( 'widthSidebar', '100' );
					};
				},
				updateTextAlign: function () {
					var text_align = this.model.get('textAlign');
					this.$('.item-content').attr( 'data-text-align', text_align );
				},
				updateLayoutStyleMobile: function () {
					var style = this.model.get('layoutStyleMobile');
					var layout = this.rootView.model.get( 'layout' );

					if( layout == 'mobile' && style == 'text' ) {
						this.$el.closest( '.hb-items' ).addClass( 'fxnwr' );
						this.$el.attr( 'data-mobile-layout', style );
					} else {
						this.$el.closest( '.hb-items' ).removeClass( 'fxnwr' );
					}
				},
				updateLayoutStyleMobileData: function () {
					this.renderMenuItem();
				},
				checkMenuStyle: function () {
					var menuStyle = this.model.get('menuStyle');

					if( menuStyle == 'fullscreen' ) {
						this.model.set( 'subMenu.animationVertical', 'slide' );
					}
				},
				updateLayoutStyle: function () {
					var layoutStyle = this.model.get('layoutStyle');
					var menuStyle = this.model.get('menuStyle');

					if( layoutStyle == 'icon' && menuStyle == 'fullscreen' ) {
						this.model.set( 'subMenu.animationVertical', 'slide' );
					}

					this.$('.item-content').attr( 'data-vertical-layout-style', layoutStyle );
					this.$el.attr( 'data-vertical-layout-style', layoutStyle );

				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				updateColorIcon: function () {
					var layout = this.rootView.model.get( 'layout' );
					var color  = ( layout == 'mobile' ) ? this.model.get( 'iconColorMobile' ) : this.model.get( 'iconColor' );

					if( color != undefined && color != '' ) {
						this.$el.find( '.wr-burger-scale span' ).attr( 'style', 'background:' + color + ';' );
					} else {
						this.$el.find( '.wr-burger-scale span' ).removeAttr( 'style' );
					}
				},
				updateCSS: function () {
					if (typeof this.model.get('layoutStyle') !== 'undefined' && this.model.get('layoutStyle') == 'text' ) {
						this.$( '.header-item-inner' ).css(this.model.get('style').toJSON());
						if (this.model.get('style.backgroundImage') !== 'undefined' && this.model.get('style.backgroundImage') != ' ' && this.model.get('style.backgroundImage')) {
							this.$( '.header-item-inner' ).css({backgroundImage: 'url(' + this.model.get('style.backgroundImage') + ')'});
						}
					} else {
						this.$( '.header-item-inner' ).removeAttr('style');
						//this.el.style.cssText = null;
					}
					this.updateSpacing();
				},
				updateTextCSS: function () {
					var layoutStyle = this.model.get('layoutStyle');
					if (typeof layoutStyle !== 'undefined' && layoutStyle == 'text') {
						var textSettings = this.model.get('textSettings').toJSON();
						
						if( typeof textSettings['lineHeight'] !== 'undefined' )
							textSettings['lineHeight'] = textSettings['lineHeight'] + 'px';
						
						this.$( '.header-item-inner' ).css(textSettings);
					}
				},
				updateBackgroundHover: function () {
					var backgroundColorHover = this.model.get('style.backgroundColorHover');
					if (typeof backgroundColorHover !== 'undefined' && typeof this.model.get('layoutStyle') !== 'undefined' && this.model.get('layoutStyle') == 'text') {
						this.$('ul>li').hover(function () {
							$(this).css('backgroundColor', backgroundColorHover);
						}, function () {
							$(this).css('backgroundColor', '');
						})
					}
				},
				updateLinkCSS: function () {
					var linkOpt = this.model.get('link.style').toJSON();
					if (typeof this.model.get('layoutStyle') !== 'undefined' && this.model.get('layoutStyle') == 'text') {
						this.$el.find('a').css(linkOpt);

						if ( typeof this.model.get('link.style.color') !== 'undefined' && this.model.get('link.style.color') != ' ' && this.model.get('link.style.color') ) {
							this.$el.find( '.menu-more' ).css( { color : this.model.get('link.style.color') } );
							this.$el.find( '.item-content' ).attr( 'data-color-icon-more', this.model.get('link.style.color') );
						}
					}
				},
				renderMenuItem: function () {
					var layout         = this.rootView.model.get('layout');
					var menuID         = parseInt( this.model.get('menuID') );
					var layoutMobile   = this.model.get('layoutStyleMobile');
					var html           = '';
					var view           = this;
					var menu_container = view.$('.menu-main-menu-container');

					if ( menuID > 0 ) {
						if(layout == 'mobile' ) {
							if( layoutMobile == 'icon' ) {
								menu_container.html('<div class="wr-burger-scale"><span class="wr-burger-top"></span><span class="wr-burger-middle"></span><span class="wr-burger-bottom"></span></div>');
								view.$( '.menu-more' ).remove();
								this.updateColorIcon();
							} else if( layoutMobile == 'text' ){
								$.ajax({
									url: view.ajaxURL,
									type: 'POST',
									data: {menu_id: menuID, action: "get_menu_html"},
									complete: function (data) {
										html = data.responseText;
										menu_container.html(html);

										view.updateLayoutStyleMobile();

										view.updateTextCSS();
										view.updateLinkCSS();
										view.updateItemSpacing();
										view.resize_menu();
									}
								});
							}
						} else if (layout == 'desktop' ) {
							var layoutStyle = this.model.get('layoutStyle');
							if (typeof layoutStyle !== 'undefined' && layoutStyle == 'icon') {
								menu_container.html('<div class="wr-burger-scale"><span class="wr-burger-top"></span><span class="wr-burger-middle"></span><span class="wr-burger-bottom"></span></div>');
								view.$( '.menu-more' ).remove();
								this.updateCSS();
								this.updateColorIcon();
							} else {
								$.ajax({
									url: view.ajaxURL,
									type: 'POST',
									data: {menu_id: menuID, action: "get_menu_html"},
									complete: function (data) {
										var layoutStyle = view.model.get('layoutStyle');
										var layout = view.rootView.model.get('layout');
										if (typeof layoutStyle !== 'undefined' && layoutStyle != 'icon' && typeof layout !== 'undefined' && layout == 'desktop') {
											html = data.responseText;
											menu_container.html(html);

											view.updateTextCSS();
											view.updateLinkCSS();
											view.updateItemSpacing();
											view.resize_menu();
										}
									}
								});
							}
						}
					} else {
						menu_container.html( 'Please choose the menu to show.' );
					}
				},
				updateSpacing: function () {
					var spacing = this.model.get('spacing').toJSON();

					this.$( '.header-item-inner' ).css( spacing );

					if ( spacing['backgroundImage'] !== 'undefined' && spacing['backgroundImage'] != ' ' && spacing['backgroundImage'] ) { log( 1234543 );
						this.$( '.header-item-inner' ).css({backgroundImage: 'url(' + spacing['backgroundImage'] + ')'});
					}
				}
			}),
			"sidebar": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.sidebarInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.sidebarInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.listenTo(this.model, 'change:unit', this.updateUnit);
				},
				ready: function () {
					this.init();
					this.listenTo( this.model.get('frontCSS.spacing'), 'change', this.updateSpacing );
					this.listenTo( this.model, 'change:icon', this.updateIcon );
					this.listenTo( this.model, 'change:iconColor', this.updateColorIcon );
					this.listenTo( this.model, 'change:iconSize', this.updateIconSize );
					this.listenTo( this.model, 'change:sidebarID', this.check_choose_sidebar );
					this.check_choose_sidebar();
					this.updateIcon();
					this.updateSpacing();
					this.updateColorIcon();
					this.updateIconSize();
					this.updateAlignVertical();
				},
				updateUnit: function () {
					var width = this.model.get('frontCSS.style.width');
					var height = this.model.get('frontCSS.style.height');
					var unit = this.model.get('unit');
					if( unit == '%' && ( width > 100 || height > 100 ) ) {
						this.model.set( 'frontCSS.style.width', '100' );
						this.model.set( 'frontCSS.style.height', '100' );
					};
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				updateSpacing: function () {
					var spacing = this.model.get('frontCSS.spacing').toJSON();
					this.$( '.header-item-inner' ).css(spacing)
				},
				updateIcon: function () {
					var icon = this.model.get('icon');
					this.$el.find( '.icon i' ).attr( 'class', icon );
				},
				updateColorIcon: function () {
					var color = this.model.get('iconColor');

					if( color != undefined && color != '' ) {
						this.$el.find( '.icon' ).css( 'color', color );
					} else {
						this.$el.find( '.icon' ).css( 'color', '' );
					}
				},
				updateIconSize: function () {
					var iconSize = this.model.get('iconSize');

					if( iconSize != undefined && iconSize != '' ) {
						this.$el.find( '.icon' ).css( 'fontSize', iconSize );
					} else {
						this.$el.find( '.icon' ).css( 'fontSize', '' );
					}
				},
				check_choose_sidebar: function(){
					var sidebar_id = this.model.get('sidebarID');

					if( sidebar_id && sidebar_id != 0 ) {
						this.$( '.item-content .icon' ).show();
						this.$( '.item-content .sidebar-notice' ).hide();
					} else {
						this.$( '.item-content .icon' ).hide();
						this.$( '.item-content .sidebar-notice' ).show();
					}

				}
			}),
			"text": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.textInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.textInspectorView.open(this.el);
						e.stopPropagation();
					},
					'click a': 'showInspector',
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.listenTo(this.model.get('style'), 'change', this.updateStyleCSS);
				},
				ready: function () {
                    this.init();
					this.updateAlignVertical();
					this.updateStyleCSS();
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				updateStyleCSS: function () {
					var style = this.model.get('style').toJSON();
					if( typeof style['lineHeight'] !== 'undefined' )
						style['lineHeight'] = style['lineHeight'] + 'px';
					
					this.$( '.header-item-inner' ).css(style);
				},
			}),
			"logo": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.logoInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.logoInspectorView.open(this.el);

						// Show select sticky if enable
						if( this.superView.superView.superView.superView.model.get('sticky') ) {
							this.rootView.logoInspectorView.$( '.logo-sticky' ).show();
						} else {
							this.rootView.logoInspectorView.$( '.logo-sticky' ).hide();
						}

						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
				},
				ready: function () {
					this.listenTo(this.model, 'change:logoType', this.renderLogo);
					this.listenTo(this.model, 'change:logoImage', this.renderLogo);
					this.listenTo(this.model, 'change:content', this.renderLogo);
					this.renderLogo();
					this.updateAlignVertical();
					this.init();

					if (this.model.get('style.fontFamily'))
						this.loadFont( this.model.get( 'style.fontFamily' ) + ':' + this.model.get('style.fontWeight') );
				},
				renderLogo: function () {
					var logoType = this.model.get('logoType'), html = '';
					if (typeof logoType !== 'undefined' && logoType == 'image') {
						var url = '';
						if (typeof this.model.get('logoImage') !== 'undefined') {
							url = this.model.get('logoImage');
						}
						html += '<img src="' + url + '">';
					}
					else {
						html += this.model.get('content');
					}
					this.$('.item-content').html(html);
					this.updateCSS();
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				updateCSS: function () {
					var style = _.omit(this.model.get('style').toJSON(), ['maxWidth']);
					var width = this.model.get('style.maxWidth');

					if( typeof style['lineHeight'] !== 'undefined' )
						style['lineHeight'] = style['lineHeight'] + 'px';

					this.$( '.header-item-inner' ).css( style );

					this.$('img').css( 'width', width );
					if (this.model.get('style.backgroundImage') !== 'undefined' && this.model.get('style.backgroundImage') != ' ' && this.model.get('style.backgroundImage')) {
						this.$( '.header-item-inner' ).css({backgroundImage: 'url(' + this.model.get('style.backgroundImage') + ')'});
					}
				},
			}),
			"social": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.socialInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.socialInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
					this.listenTo(this.model, 'change:socialList', this.renderSocial);
					this.listenTo(this.model, 'change:iconColor', this.renderSocial);
					this.listenTo(this.model, 'change:backgroundColor', this.renderSocial);
					this.listenTo(this.model, 'change:borderColor', this.renderSocial);
					this.listenTo(this.model, 'change:borderStyle', this.renderSocial);
					this.listenTo(this.model, 'change:borderWidth', this.renderSocial);
					this.listenTo(this.model, 'change:iconStyle', this.renderSocial);
					this.listenTo(this.model, 'change:iconSpacing', this.renderSocial);
					this.listenTo(this.model, 'change:borderRadius', this.renderSocial);
					this.listenTo(this.model, 'change:iconSize', this.renderIconSize);
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.renderSocial();
				},
				ready: function () {
                    this.init();
					this.updateAlignVertical();
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				renderSocial: function () {
					var view = this;
					var list = this.model.get('socialList').toJSON();

					var is_background = '';

					var iconColor       = this.model.get('iconColor') != undefined ? this.model.get('iconColor') : '';
					var backgroundColor = this.model.get('backgroundColor') != undefined ? this.model.get('backgroundColor') : '';
					var borderColor     = this.model.get('borderColor') != undefined ? this.model.get('borderColor') : '';
					var borderStyle     = this.model.get('borderStyle') != undefined ? this.model.get('borderStyle') : '';
					var borderWidth     = this.model.get('borderWidth') != undefined ? this.model.get('borderWidth') : '';
					var borderRadius     = this.model.get('borderRadius') != undefined ? this.model.get('borderRadius') : '';
					var iconSpacing     = this.model.get('iconSpacing') != undefined ? this.model.get('iconSpacing') : '';
					var iconStyle       = this.model.get('iconStyle') != undefined ? this.model.get('iconStyle')     : '';

					if ( iconColor === '' ) {
						is_background = 'color';
					} else if( iconStyle === 'custom' && backgroundColor === '' ) {
						is_background = 'background';
					}

					var iconSize = this.model.get('iconSize');
					if (typeof iconSize !== 'undefined')
						this.$('.item-content').attr('data-size', iconSize);

					if (typeof iconStyle !== 'undefined')
						this.$('.item-content').attr( 'class', 'item-content ' + iconStyle );

					if (!_.isEmpty(list)) {
						this.$('.item-content').html('');

						var style = '';

						if( iconStyle === 'none' ) {
							style += ( iconColor !== '' ) ? ( 'color: ' + iconColor + ';' ) : '';
							style += ( iconSpacing !== '' ) ?       ( 'margin-right: ' + iconSpacing + 'px;' ) : '';
						} else if( iconStyle === 'custom' ) {
							style += ( iconColor !== '' ) ?         ( 'color: ' + iconColor + ';' ) : '';
							style += ( backgroundColor !== '' ) ?   ( 'background-color: ' + backgroundColor + ';' ) : '';
							style += ( borderColor !== '' ) ?       ( 'border-color: ' + borderColor + ';' ) : '';
							style += ( borderStyle !== '' ) ?       ( 'border-style: ' + borderStyle + ';' ) : '';
							style += ( borderWidth !== '' ) ?       ( 'border-width: ' + borderWidth + 'px;' ) : '';
							style += ( borderRadius !== '' ) ?      ( 'border-radius: ' + borderRadius + 'px;' ) : '';
							style += ( iconSpacing !== '' ) ?       ( 'margin-right: ' + iconSpacing + 'px;' ) : '';
						}

						_.each( list, function ( value, key ) {
							key = key.replace('_', '-');
							if ( value && list_social.indexOf( key ) != '-1' )
								view.$( '.item-content' ).append( '<span style="' + style + '" class="wr-' + is_background + '-' + key + '"><i class="fa fa-' + key + '"></i></span>' );
						})
					}

				},
				renderIconSize: function () {
					this.$('.item-content').attr('data-size', this.model.get('iconSize'));
				}
			}),
			"shopping-cart": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.shoppingcartInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.shoppingcartInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
				},
				ready: function () {
					this.listenTo(this.model, 'change:showCartInfo', this.renderCart);
					this.listenTo(this.model, 'change:iconName', this.renderIconCart);
					this.listenTo(this.model, 'change:styleIcon', this.updateStyleIcon);
					this.listenTo(this.model, 'change:titleText', this.updateTitleText);
					this.listenTo(this.model, 'change:colorTitle', this.updateColorTitle);
					this.listenTo(this.model, 'change:colorPrice', this.updateColorPrice);

					this.updateAlignVertical();
					this.init();
					this.renderIconCart();
					this.renderCart();
					this.updateStyleIcon();
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				renderCart: function () {
					var showCartInfo = this.model.get('showCartInfo');
					var iconName     = this.model.get('iconName');
					var colorPrice   = this.model.get('colorPrice');
					var colorTitle   = this.model.get('colorTitle');
					var titleText    = this.model.get('titleText');
					if( typeof titleText != 'undefined' ) {
						titleText = titleText.trim();
					}
					if( typeof colorTitle != 'undefined' ) {
						colorTitle = colorTitle.trim();
					}

					var title        = '<div class="title-text ' + ( titleText === '' ? 'hidden' : '' ) + '" style="color: ' + colorTitle + '">' + titleText + '</div>';
					var price        = '<div style="color: ' + colorPrice + '" class="price-cart">$686</div>';
					var quantity     = '<div class="number-cart">8</div>';

					switch (showCartInfo) {
						case 'none':
							this.$('.item-content').html( title + '<i class="' + iconName + '"></i>' );
							break;
						case 'item_number':
							this.$('.item-content').html( title + '<i class="' + iconName + '">' + quantity + '</i>' );
							break;
						case 'total_price':
							this.$('.item-content').html( title + '<i class="' + iconName + '"></i>' + price );
							break;
						case 'number_price':
							this.$('.item-content').html( title + '<i class="' + iconName + '">' + quantity + '</i>' + price );
							break;
					}
				},
				renderIconCart: function () {
					var iconName = this.model.get('iconName');
					this.$('.item-content i').attr('class', iconName);
				},
				updateStyleIcon: function () {
					var styleIcon = this.model.get('styleIcon').toJSON();
					this.$('.item-content').css(styleIcon);
				},
				updateTitleText: function () {
					var titleText = this.model.get('titleText');
					if( typeof titleText != 'undefined' ) {
						titleText = titleText.trim();
					}

					this.$('.title-text').html( titleText );

					if( titleText === '' ) {
						this.$('.title-text').addClass( 'hidden' );
					} else {
						this.$('.title-text').removeClass( 'hidden' );
					}
				},
				updateColorTitle: function () {
					var colorTitle = this.model.get('colorTitle');
					if( typeof colorTitle != 'undefined' ) {
						colorTitle = colorTitle.trim();
					}

					this.$('.title-text').css( { color: colorTitle } );
				},
				updateColorPrice: function () {
					var colorPrice = this.model.get('colorPrice');
					if( typeof colorPrice != 'undefined' ) {
						colorPrice = colorPrice.trim();
						this.$('.price-cart').css( { color: colorPrice } );
					}
				},
			}),
			"wpml": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.wpmlInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.wpmlInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
				},
				ready: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.init();
					this.removeEventDefault();
					this.updateAlignVertical();
				},
				removeEventDefault: function () {
					this.$( '#lang_sel_click' ).removeAttr( 'onclick id' );
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				}
			}),
			"wishlist": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.wishlistInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.wishlistInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
				},
				ready: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.listenTo(this.model, 'change:textLabel', this.updateLabel);
					this.listenTo(this.model, 'change:colorLabel', this.updateLabel);
					this.listenTo(this.model, 'change:colorIcon', this.updateLabel);
					this.listenTo(this.model, 'change:iconSize', this.updateLabel);
					this.listenTo(this.model, 'change:labelSize', this.updateLabel);
					this.listenTo(this.model, 'change:labelPosition', this.updateLabelPosition);
					this.init();
					this.updateAlignVertical();
					this.updateLabel();
					this.updateLabelPosition();
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				updateLabelPosition: function () {
					var position = this.model.get('labelPosition');
					this.$el.attr( 'data-position', position );
				},
				updateLabel: function(){
					var text_label  = this.model.get('textLabel');
					var color_label = this.model.get('colorLabel');
					var size_label  = this.model.get('labelSize');
					var size_icon   = this.model.get('iconSize');
					var color_icon  = this.model.get('colorIcon');

					this.$( '.icon' ).css( { color: color_icon, fontSize: size_icon } );
					this.$( '.text' ).css( { color: color_label, fontSize: size_label } ).html( text_label );

					if( text_label === '' ) {
						this.$( '.text' ).addClass( 'hidden' );
					} else {
						this.$( '.text' ).removeClass( 'hidden' );
					};
				},
			}),
			"currency": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						this.rootView.currencyInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.currencyInspectorView.open(this.el);
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
				initialize: function () {
				},
				ready: function () {
					this.listenTo(this.model, 'change:alignVertical', this.updateAlignVertical);
					this.listenTo(this.model, 'change:textLabel', this.updateLabel);
					this.listenTo(this.model, 'change:colorLabel', this.updateLabel);
					this.listenTo(this.model, 'change:colorIcon', this.updateLabel);
					this.listenTo(this.model, 'change:iconSize', this.updateLabel);
					this.listenTo(this.model, 'change:labelSize', this.updateLabel);
					this.listenTo(this.model, 'change:labelPosition', this.updateLabelPosition);
					this.init();
					this.updateAlignVertical();
					this.updateLabel();
					this.updateLabelPosition();
				},
				updateAlignVertical: function () {
					var align = this.model.get('alignVertical');
					this.$el.attr( 'data-align-vertical', align );
				},
				updateLabelPosition: function () {
					var position = this.model.get('labelPosition');
					this.$el.attr( 'data-position', position );
				},
				updateLabel: function(){
					var text_label  = this.model.get('textLabel');
					var color_label = this.model.get('colorLabel');
					var size_label  = this.model.get('labelSize');
					var size_icon   = this.model.get('iconSize');
					var color_icon  = this.model.get('colorIcon');

					this.$( '.icon' ).css( { color: color_icon, fontSize: size_icon } );
					this.$( '.text' ).css( { color: color_label, fontSize: size_label } ).html( text_label );

					if( text_label === '' ) {
						this.$( '.text' ).addClass( 'hidden' );
					} else {
						this.$( '.text' ).removeClass( 'hidden' );
					};
				},
			}),
			"flex": WRNitro_HeaderBuilder_ItemView.extend({
				events: {
					'click': function (e) {
						e.stopPropagation();
					},
					'updateIndex': 'updateIndex',
					'mousedown .delete-item': 'deleteModel',
					'sortToNewRow': 'sortToNewRow'
				},
			}),
		}
	});

	exports.WRNitro_HeaderBuilder_ColumnsView = B.CollectionView.extend({
		itemView: WRNitro_View.extend({
			views: {
				"itemsView collection:items > .hb-items": WRNitro_HeaderBuilder_ItemsView
			},
			initialize: function () {
				this.listenTo(this.model.get('style'), 'change', this.updateCSS);
				this.listenTo(this.model, 'change:unit', this.changeUnit);
				this.updateCSS();
			},
			updateCSS: function () {
				var unit    = this.model.get('unit');
				var width   = this.model.get('style.maxWidth');

				this.$el.css( this.model.get('style').toJSON() );

				//set width without data unit
				this.$el.css({'max-width': width + unit});
				if (this.model.get('style.backgroundImage') !== 'undefined' && this.model.get('style.backgroundImage') != ' ' && this.model.get('style.backgroundImage')) {
					this.$el.css({backgroundImage: 'url(' + this.model.get('style.backgroundImage') + ')'});
				}
			},
			changeUnit: function(){
				var unit    = this.model.get('unit');
				var width   = this.model.get('style.maxWidth');

				if( unit == '%' && width > 100 ) {
					this.model.set( 'style.maxWidth', '100' );
					this.$el.css( { 'max-width': 100 + unit } );
				} else {
					this.$el.css( { 'max-width': width + unit } );
				}
			},
		})
	});

	exports.WRNitro_HeaderBuilder_RowsView = B.CollectionView.extend({
		itemView: WRNitro_View.extend({
			events: {
				'click': function (e) {
					var vertical = this.rootView.model.get('desktop.settings.type');
					var layout   = this.rootView.model.get('layout');

					if ( ! ( vertical == 'vertical' && layout == 'desktop' ) ) {
						this.rootView.rowInspectorView.inspect(this.model);
						this.rootView.closeInspector();
						this.rootView.rowInspectorView.open(this.el);
						e.stopPropagation();
					}

				},
				'mousedown .delete-item': 'deleteModel',
				'updateIndex': 'updateIndex',

			},
			initialize: function () {
				this.listenTo(this.model.get('style'), 'change', this.updateCSS);
				this.listenTo(this.model, 'destroy', this.remove);
				this.listenTo(this.model, 'change:className', this.updateClass);
				this.listenTo(this.model, 'change:ID', this.updateID);
				this.updateCSS();
			},
			views: {
				"columnsView collection:cols > .hb-columns": WRNitro_HeaderBuilder_ColumnsView
			},
			updateCSS: function () {
				var hb_columns = this.$( '.hb-columns' );

				hb_columns.css(this.model.get('style').toJSON());

				if (this.model.get('style.backgroundImage') !== 'undefined' && this.model.get('style.backgroundImage') != ' ' && this.model.get('style.backgroundImage')) {
					hb_columns.css({backgroundImage: 'url(' + this.model.get('style.backgroundImage') + ')'});
				}
			},
			deleteModel: function (e) {
				e.stopPropagation();
				var view           = this;
				var delete_confirm = confirm( 'Do you really want to delete this item?' );
				if( delete_confirm ) {
					this.rootView.closeInspector();
					this.rootView.closeInspector();
					view.model.destroy();

					var layout = this.rootView.model.get( 'layout' );

					if ( layout == 'desktop' && this.rootView.model.get( 'desktop.rows' ).length == 0 ) {
						$( '#hb-app' ).addClass( 'row-empty' );
					}
				}
			},
			updateIndex: function (e) {
				var index = this.$el.index();
				this.model.set('index', index);
			},
			updateID: function () {
				var id = ( typeof this.model.get('ID') != 'undefined' ) ? this.model.get('ID').replace(/\s+/, '') : null;
				if (!id) {
					this.$el.removeAttr('id');
				}
				else {
					this.$el.attr('id', id);
				}
			},
			updateClass: function () {
				var className = ( typeof this.model.get('className') != 'undefined' ) ? this.model.get('className').replace(/\s+/, '') : '';
				this.$el.attr('class', 'hb-row ' + className);
			},
		})
	});

	exports.WRNitro_HeaderBuilder_InspectorView = WRNitro_View.extend({
		events: {
			'click .nav-settings li': 'clickTabNav',
			'click .wr-checkbox-btn': 'clickCheckboxBTN',
			'mousedown .file-image .select-image': function(e){
				e.stopPropagation();
			},
			'mousedown .media-modal': function (e) {
				e.stopPropagation();
			},
			'click .file-image .select-image': function (e) {
				e.stopPropagation();
				this.openMediaBox(e);
			},
			'click .wr-radio-group>.wr-radio-btn'           : 'showRadioItemActive',
			'click .file-image .remove-image'               : 'removeImage',
			'click .wr-image-selected'                      : 'showListFont',
			'click .wr-list-font li'                        : 'setFontName',
			'click .select-icon .list-icon li'              : 'setFontAwesome',
			'keyup .wr-customize-font .txt-sfont'           : 'searchFont',
			'search .wr-customize-font .txt-sfont'          : 'searchFont',
			'keyup .select-icon .txt-sfont'                 : 'searchFontAwesome',
			'search .select-icon .txt-sfont'                : 'searchFontAwesome',
			'change .slt-font-weight'                       : 'setFontWeight',
			'click .close-setting'                          : 'close',
			'change .search-in .chb input[type="checkbox"]' : 'validateSearchIn',
			'change .el-show-width-submmenu'                : 'showWidthSubMenu',
			'click .show-hide-fixed .sh-fixed-title'        : 'accordion',
			'click .hb-text-editor'                         : 'updateContentModeTextEditor',
			'blur .hb-text-editor'                          : 'updateContentModeTextEditor',
			'mouseover .color-theme.hb-disabled'            : 'tooltipDescriptionShow',
			'mouseout .color-theme.hb-disabled'             : 'tooltipDescriptionHide',
			'change .chb-use-theme'                         : 'addClassParamUseTheme',
		},
		initialize: function () {
			this.visibility = false;
			this.$('input, textarea').on('keyup', function(e){
				$(e.currentTarget).trigger('change');
			});
		},
		ready: function () {
			this.$el.hide();
			this.$el.draggable({
				containment : '#hb-app',
				handle      : '.title-setting'
			});
		},
		addClassParamUseTheme: function(){
			var model_current  = this.model.toJSON();

			if( model_current.themeColor ) {
				this.$( '.color-theme' ).addClass( 'hb-disabled' );
			} else {
				this.$( '.color-theme' ).removeClass( 'hb-disabled' );
			}
		},
		tooltipDescriptionShow : function( e ){
			var _this = $( e.currentTarget );
			_this.find( '.title-field' ).append( '<div class="des-color">Not change, because row use theme default color.</div>' );
		},
		tooltipDescriptionHide : function( e ){
			var _this = $( e.currentTarget );
			_this.find( '.title-field .des-color' ).remove();
		},
		updateContentModeTextEditor: function ( e ) {
			var _this      = $( e.currentTarget );
			var content    = window.switchEditors._wp_Autop ( _this.val() );     // Changes double line-breaks in the text into HTML paragraphs (<p>...</p>).
			var parent     = _this.closest( '.hb-editor' );
			var input_hide = parent.find( '.hb-editor-hidden' );

			input_hide.val( content ).trigger('change');
		},
		showWidthSubMenu: function () {
			var layout_current = this.rootView.model.get('hbLayout');
			var model_current  = this.model.toJSON();
			if( ( model_current['layoutStyle'] == 'text' && ( layout_current == 'horizontal' || ( layout_current == 'vertical' && model_current['subMenu']['animationVertical'] == 'normal' ) ) ) || ( model_current['layoutStyle'] == 'icon' && model_current['menuStyle'] == 'sidebar' && model_current['subMenu']['animationVertical'] == 'normal' ) ) {
				this.$('.show-width-submmenu').show();
			} else {
				this.$('.show-width-submmenu').hide();
			}
		},
		accordion: function( e ){
			var _this = $(e.currentTarget);
			var parent = _this.closest( '.sh-fixed-box' );

			if( _this.hasClass( 'active' ) ) {
				parent.find( '.sh-fixed-list' ).stop( true, true ).slideDown();
				_this.removeClass( 'active' )
			} else {
				parent.find( '.sh-fixed-list' ).stop( true, true ).slideUp();
				_this.addClass( 'active' );
			}
		},
		validateSearchIn: function(){
			this.$( '.search-in .chb.search-disable' ).removeClass( 'search-disable' );

			var search_selected = this.$('.search-in input[type="checkbox"]:checked');

			if( search_selected.length == 1 )
				search_selected.closest( '.chb' ).addClass( 'search-disable' );
		},
		checkVisible: function(){
			var hb_layout = this.rootView.model.get('hbLayout');
			var layout    = this.rootView.model.get('layout');

			// Show settings for horizontal layout
			if ( hb_layout == 'horizontal' ) {
				this.$('.visible-horizontal-layout').show();
				this.$('.visible-vertical-layout').hide();
			} else {
				this.$('.visible-horizontal-layout').hide();
				this.$('.visible-vertical-layout').show();
			}

			// Show setting for without desktop and vertical
			if( layout == 'desktop' && hb_layout == 'vertical' ) {
				this.$( '.visible-without-vertical-desktop' ).hide();
			} else {
				this.$( '.visible-without-vertical-desktop' ).show();
			}

			// Show setting for without desktop horizontal
			if( layout == 'desktop' && hb_layout == 'horizontal' ) {
				this.$( '.visible-horizontal-desktop' ).show();
			} else {
				this.$( '.visible-horizontal-desktop' ).hide();
			}

			// Show setting for without mobile horizontal
			if( layout == 'mobile' && hb_layout == 'horizontal' ) {
				this.$( '.visible-horizontal-mobile' ).show();
			} else {
				this.$( '.visible-horizontal-mobile' ).hide();
			}

			// Show setting for without desktop vertical
			if( layout == 'desktop' && hb_layout == 'vertical' ) {
				this.$( '.visible-vertical-desktop' ).show();
			} else {
				this.$( '.visible-vertical-desktop' ).hide();
			}

			// Show setting for without mobile vertical
			if( layout == 'mobile' && hb_layout == 'vertical' ) {
				this.$( '.visible-vertical-mobile' ).show();
			} else {
				this.$( '.visible-vertical-mobile' ).hide();
			}

			// Show setting for without desktop vertical
			if( layout == 'desktop' && hb_layout == 'vertical' ) {
				this.$( '.hide-desktop-vertical' ).hide();
				this.$( '.visible-desktop-vertical' ).show();
			} else {
				this.$( '.hide-desktop-vertical' ).show();
				this.$( '.visible-desktop-vertical' ).hide();
			}

			// Show setting for desktop
			if( layout == 'mobile' ) {
				this.$( '.visible-mobile-layout' ).show();
			} else {
				this.$( '.visible-mobile-layout' ).hide();
			}

			// Show setting for mobile
			if( layout == 'desktop' ) {
				this.$( '.visible-desktop-layout' ).show();
			} else {
				this.$( '.visible-desktop-layout' ).hide();
			}
		},
		initFonts: function () {
			var view = this;
			this.$('.wr-customize-font').each(function (i, value) {
				var fontName = $(value).find('select.slt.hidden').val();
				view.setActiveFont( $(value ), fontName);
				view.setActiveFontWeight( $(value), fontName );
			});
		},
		initFontsAwesome: function () {

			if( ! window.wr_fontawesome ) {
				return;
			}

			var view = this;

			this.$( '.select-icon' ).each(function (i, value) {
				var _this    = $(value),
					fontName = _this.find('.hidden').val(),
					list_icon = _this.find('.list-icon ul');

				$.each( window.wr_fontawesome, function( val, key ){
					var active = '';
					if( val == fontName ) {
						active = 'class="active-font"';
					}

					list_icon.append( '<li ' + active + ' data-value="' + val + '"><i class="' + val +'"></i></li>' )
				} );

				_this.find( '.icon-selected i' ).attr( 'class', fontName );
			});
		},
		showListFont: function (e) {
			var parent      = $(e.currentTarget).parents('.wr-customize-font');
			var listFont    = $(e.currentTarget).siblings('.wr-select-image-container');

			if( parent.hasClass( 'active-drop' ) ) {
				listFont.hide();
				parent.removeClass( 'active-drop' );
			} else {
				parent.addClass( 'active-drop' );

				var currentFont = $(e.currentTarget).parent().find('select.slt.hidden').val();
				var listFont    = $(e.currentTarget).siblings('.wr-select-image-container');

				this.setActiveFont( parent, currentFont );

				listFont.show();

				parent.clickOutside(_.bind(function () {
					listFont.hide();
					parent.removeClass( 'active-drop' );
				}, this));

				parent.find( '.txt-sfont' ).focus();
			}
		},
		setFontName: function (e) {
			var selector = e.currentTarget;
			var fontName = $(selector).attr( 'data-value' );
			if ( fontName ) {
				var parent     = $(selector).parents('.wr-customize-font');
				var fontWeigth = $(selector).attr( 'data-weigth' );

				$(selector).closest( '.wr-select-image-container' ).hide();
				$(selector).closest( '.wr-customize-font' ).removeClass( 'active-drop' );

				this.setActiveFont(parent, fontName);
				this.setListFontWeigth( parent, fontWeigth, fontName );
			}
		},
		setFontAwesome: function (e) {
			var selector = e.currentTarget;
			var _this = $(selector);

			if( _this.hasClass( 'active-font' ) ) {
				return;
			}

			var parent = _this.closest( '.select-icon' );
			parent.find( '.list-icon li' ).removeClass( 'active-font' );
			_this.addClass( 'active-font' );
			var fontName = _this.find( 'i' ).attr( 'class' );

			// Update data
			parent.find( '.hidden' ).val( fontName ).trigger('change');

			parent.find( '.icon-selected i' ).attr( 'class', fontName );
		},
		searchFont: function (e) {
			var selector    = e.currentTarget;
			var keyword     = $(selector).val();
			var list_fonts  = $(selector).closest( '.wr-select-image-container' ).find( 'li' );

			if( keyword ) {
				if( window.keyword_font_old == undefined || window.keyword_font_old != keyword || e.keyCode == 13 || e.keyCode == 86 ) {
					list_fonts.hide();
					list_fonts.each( function () {
						var textField = ( $(this).attr( 'data-value' ) != undefined ) ? $(this).attr( 'data-value' ).toLowerCase() : '' ;
						var keyword_lowercase = keyword.toLowerCase().trim();
						if( textField.indexOf( keyword_lowercase ) == -1 ) {
							$(this).hide();
						} else {
							$(this).fadeIn( 200 );
						}
					} );

					window.keyword_font_old = keyword; 
				}
			} else {
				list_fonts.show();
			}
		},
		searchFontAwesome: function (e) {
			var selector    = e.currentTarget;
			var keyword     = $(selector).val();
			var list_fonts  = $(selector).closest( '.list-icon-wrap' ).find( 'li' );

			if( keyword ) {
				if( window.keyword_font_old == undefined || window.keyword_font_old != keyword || e.keyCode == 13 || e.keyCode == 86 ) {
					list_fonts.hide();
					list_fonts.each( function () {
						var textField = ( $(this).attr( 'data-value' ) != undefined ) ? $(this).attr( 'data-value' ).toLowerCase() : '' ;
						var keyword_lowercase = keyword.toLowerCase().trim();
						if( textField.indexOf( keyword_lowercase ) == -1 ) {
							$(this).hide();
						} else {
							$(this).fadeIn( 200 );
						}
					} );

					window.keyword_font_old = keyword; 
				}
			} else {
				list_fonts.show();
			}
		},
		setActiveFont: function ( parent, fontName ) {
			if (fontName) {
				this.loadFont( fontName );

				var newName = fontName.replace(/\s+/g, '-').toLowerCase();
				this.$('.wr-select-image-container li.selected').removeClass('selected');
				this.$('.wr-select-image-container li.' + newName).addClass('selected');

				parent.find('.wr-image-selected').attr('data-font', fontName);
				parent.find('.wr-image-selected').attr('class', 'wr-image-selected ' + newName);
				parent.find('.wr-image-selected').text( fontName );

				// Update data
				parent.find('select.slt.hidden').val(fontName).trigger('change');

				if( parent.find('.wr-list-font li.' + newName).attr( 'data-weigth' ) != undefined && ( parent.find('.wr-list-font li.' + newName).attr( 'data-weigth' ).search( ',' ) == -1 ) ) {
					this.$( '.slt-font-weight[data-link-font-weight="' + parent.attr( 'data-link-font-weight' ) + '"]' ).prop( 'disabled', true );
				} else {
					this.$( '.slt-font-weight[data-link-font-weight="' + parent.attr( 'data-link-font-weight' ) + '"]' ).prop( 'disabled', false );
				}
			}
		},
		setFontWeight: function ( e ) {
			var selector = e.currentTarget;
			var font_name = $( selector ).closest( '.hb-settings-box' ).find( '.wr-customize-font[data-link-font-weight="' + $( selector ).attr( 'data-link-font-weight' ) + '"] select.slt.hidden' ).val();

			this.loadFont( font_name + ':' + $( selector ).val() );
		},
		setActiveFontWeight: function ( parent, fontName ) {
			if( !parent || !fontName )
				return;

			var font_weight_select  = parent.closest( '.hb-settings-box' ).find( '.slt-font-weight[data-link-font-weight="' + parent.attr( 'data-link-font-weight' ) + '"]' );
			var list_font_weight    = font_weight_select.find( 'option' );
			var fontWeigth          = parent.find( '.wr-list-font li[data-value="' + fontName + '"]' ).attr( 'data-weigth' );
			var fontWeigthArray     = ( fontWeigth != undefined ) ? fontWeigth.split( ',' ) : '';

			$.each( list_font_weight, function() {
				if( fontWeigthArray.indexOf( $(this).val() ) != -1 ) {
					$(this).show();
				} else {
					$(this).hide();
				}
			} );

			this.loadFont( fontName + ':' + font_weight_select.val() );
		},
		setListFontWeigth: function ( parent, fontWeigth, fontName ) {
			if( !parent || !fontWeigth || !fontName )
				return;

			var font_weight_select  = parent.closest( '.hb-settings-box' ).find( '.slt-font-weight[data-link-font-weight="' + parent.attr( 'data-link-font-weight' ) + '"]' );
			var list_font_weight    = font_weight_select.find( 'option' );
			var fontWeigthArray     = ( fontWeigth != undefined ) ? fontWeigth.split( ',' ) : '';

			$.each( list_font_weight, function() {
				if( fontWeigthArray.indexOf( $(this).val() ) != -1 ) {
					$(this).show();
				} else {
					$(this).hide();
				}
			} );

			if( fontWeigthArray.indexOf( font_weight_select.val() ) == -1 ) {
				font_weight_select.val( fontWeigthArray[0] ).trigger('change');
				this.loadFont( fontName + ':' + fontWeigthArray[0] );
			}

			if( fontWeigthArray.length == 1 ) {
				font_weight_select.prop( 'disabled', true );
			} else {
				font_weight_select.prop( 'disabled', false );
			}
		},
		removeImage: function (e) {
			var selector = e.currentTarget;
			var input = $(selector).parents('.file-image').find('input.input-file');
			if (input.length > 0) {
				input.get(0).value = '';
				input.trigger('change');
			}
		},
		setContentEditor: function () {
			var view = this;
			view.$( '.hb-editor' ).each( function ( key, value ) {
				var _this           = $( value );
				var content         = _this.find('.hb-editor-hidden').val();
				var tinymce_name    = _this.attr( 'data-editor' );
				var tinymce_box     = tinymce.get( tinymce_name );
				var content_removep = window.switchEditors._wp_Nop ( content );

				// Set content for tab visual
				if( tinymce_box )
					tinymce_box.setContent( content );

				// Set content for tab text
				_this.find( '.wp-editor-area' ).val( content_removep );

			} );
		},
		inspect: function ( model ) {

			log( model.toJSON() );

			this.trigger('before:inspect');
			var view = this;
			this.setModel(model);
			model.set('selected', true);
			this.__dataBinding.updateView();
			this.$('ul.nav-settings li[class="active"]').click();
			this.fillDataToDom();

			this.$('.wr-color').spectrum( {
				color: '',
				showInput: true,
				showInitial: true,
				allowEmpty: true,
				showAlpha: true,
				clickoutFiresChange: false,
				showButtons: true,
				cancelText: 'Cancel',
				chooseText: 'Choose',
				preferredFormat: 'rgb',
				show: function ( color ) {
					$( '.sp-button-container .sp-default' ).remove();
					$( '.sp-button-container' ).prepend( '<a class="sp-default" href="#">Default</a>' );

					var input           = $(this);
					var color_default   = color ? ( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() ) : '';
					var container       = input.parents('.wr-hb-colors-control');

					$('.sp-default').off('click').on('click', function( event ) {;
						event.preventDefault();
						input.spectrum( 'set', color_default );
						container.find('.font-color').text( color_default );
						input.val( color_default ).trigger('change');
						$('.sp-container:visible').find('.sp-input').val( color_default );
					});

					$('.sp-clear').off('click').on('click', function( event ) {;
						event.preventDefault();
						input.spectrum( 'set', '' );
						container.find('.font-color').text( '' );
						input.val( '' ).trigger('change');
						$('.sp-container:visible').find('.sp-input').val( '' );
					});

					$('.sp-container:visible').find('.sp-input').val( color_default );
				},
				move: function ( color ) {
					if( ! color )
						return;

					var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
					$(this).siblings('.font-color').text(val);
					$(this).val(val).trigger('change');
					$('.sp-container:visible').find('.sp-input').val(val);
				},
				change: function( color ) {
					if( ! color )
						return;

					var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
					$(this).siblings('.font-color').text(val);
					$(this).val(val).trigger('change');
					$('.sp-container:visible').find('.sp-input').val(val);
				},
				hide: function( color ) {
					if( ! color ) {
						$(this).siblings('.font-color').text('');
						$(this).val('').trigger('change');
					} else {
						var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
						$(this).siblings('.font-color').text(val);
						$(this).val(val).trigger('change');
						$('.sp-container:visible').find('.sp-input').val(val);
					}
				}
			});
			this.initFonts();
			this.initFontsAwesome();
			this.setClickOutSide();
			this.validateSearchIn();
			this.setContentEditor();

			switch ( model.toJSON()._rel ) {
				case 'search' :

					break;

				case 'menu' : 

					this.showWidthSubMenu();

					break;

				case 'sidebar' : 

					break;

				case 'text' :

					break;

				case 'logo' : 

					break;

				case 'social' : 

					break;

				case 'shopping-cart' : 

					var layout = this.rootView.model.get('layout');

					if ( layout == 'mobile' ) {
						this.model.set( 'type', 'sidebar' );
					}
					break;

				case 'wpml' : 

					break;

				case 'wishlist' : 

					break;

				case 'currency' : 

					break;
			}

			if( model.collection != undefined && model.collection.parent != undefined && model.collection.parent.collection != undefined ) {
				if( model.collection.parent.collection.parent.get( 'themeColor' ) ) {
					this.$( '.color-theme' ).addClass( 'hb-disabled' );
				} else {
					this.$( '.color-theme.hb-disabled' ).removeClass( 'hb-disabled' );
				}
			};
		},
		open: function (target, options) {
			this.$el.show().addClass('visible').position({
				my        : 'top+10',
				at        : 'bottom',
				of        : target,
				collision : 'fit none',
				within    : '#hb-app',
				using: function(pos, ui) {
					if( pos['left'] <= 25 ) {
						pos['left'] = 25;
					} else {
						var container_info    = $( '#hb-app' )[0].getBoundingClientRect();
						var right_container   = container_info['left'] + container_info['width'];
						var right_box_setting = ui['element']['width'] + ui['element']['left'];

						if( ( right_container - right_box_setting ) <= 25 )
							pos['left'] -= 25;
					}

					ui.element.element.css( pos );
				}
			});

			this.$el.clickOutside(_.bind(function () {
				this.$el.hide();
			}, this));
			this.checkVisible();
		},
		close: function () {
			this.$el.hide().removeClass('visible');
		},
		clickTabNav: function (e) {
			this.$('ul.nav-settings li').removeClass('active');
			$(e.currentTarget).addClass('active');
			var dataNav = $(e.currentTarget).attr('data-nav');
			this.$('.option-settings .item-option').hide();
			this.$('.option-settings .item-option[data-option="' + dataNav + '"]').show();
		},
		setClickOutSide: function () {
			this.$el.clickOutside(_.bind(function (e) {

				// Check click wp editor
				var check_editor = ( $( e.target ).closest( '.mce-container' ).length || $( e.target ).closest( '.mce-tooltip' ).length || $( e.target ).closest( '#wp-link-wrap' ).length || $( '#wp-link-backdrop' ).is( e.target ) ) ? true : false;

				if( ! check_editor ) {
					this.model.set('selected', false);
					this.close();
				} else {
					this.setClickOutSide();
				}
			}, this ) );
		},
		openMediaBox: function (e) {
			var view = this;
			var selector = e.currentTarget;
			e.preventDefault();
			// Store clicked element for later reference
			var $btn = $(selector), frame = $btn.data('wr_media_selector'), $input = $(selector).parent().find('input.input-file');
			if ( ! frame ) {
				log('Not has frame');

				// Create the media frame
				frame = wp.media({
					button: {
						text: 'Select',
					},
					states: [
						new wp.media.controller.Library({
							title: 'Select media',
							library: wp.media.query({type: 'image'}),
							multiple: false,
							date: false,
						})
					]
				});

				// When an image is selected, run a callback
				frame.on('select', function () {
					// Grab the selected attachment
					var attachment = frame.state().get('selection').first();
					// Update the field value
					$input.val(attachment.attributes.url).trigger('change');
					//$input.val(attachment.attributes.url).trigger('wr:change');
					view.$el.show();
				});

				// Store media selector object for later reference
				$btn.data('wr_media_selector', frame);
			}
			frame.open();
		},
		clickCheckboxBTN: function (e) {
			var selector = $(e.currentTarget);
			var input    = selector.find('input[type="checkbox"]');
			selector.toggleClass('active');
			if (input.length > 0) {
				var checked = selector.hasClass('active');

				input.prop('checked', checked);
				input.trigger('change');
			}
		},
		fillDataToDom: function () {
			var listCheckBox = this.$el.find('.wr-checkbox-btn, .wr-radio-item');
			_.each(listCheckBox, function (el) {
				var input = $(el).find('input');
				if (input.length > 0) {
					if (input.prop('checked')) {
						$(el).addClass('active');
					}
					else {
						$(el).removeClass('active');
					}
				}
			});

			var radioItem = this.$('.wr-radio-group .wr-radio-btn');
			var input     = this.$('.wr-radio-group input[type="hidden"]');
			if (input.length > 0) {
				var val = input.get(0).value;
				log(input)
				if (radioItem.length > 0 && val.length > 0) {
					_.each(radioItem, function (el) {
						var value = $(el).attr('data');
						if (value == val) {
							$(el).addClass('active');
						}
						else {
							$(el).removeClass('active');
						}
					});
				}
			}
		},
		showRadioItemActive: function (e) {
			var selector  = $(e.currentTarget);
			var parent    = selector.parents('.wr-radio-group');
			var radioItem = parent.find('.wr-radio-btn');
			var val       = selector.attr('data');
			log(val)
			parent.find('input[type="hidden"]').val(val).trigger('change');
			radioItem.removeClass('active');
			selector.addClass('active');
		},
	});

	exports.WRNitro_HeaderBuilder_Base_View = WRNitro_View.extend({
		events: {
			'click .add-row': 'addRow',
		},
		init: function () {
			this.listenTo(this.model, 'change:settings.style', this.updateCSS);
			this.updateCSS();
		},
		addRow: function () {
			var layout     = this.rootView.model.get( 'layout' );
			var row        = this.model.get( 'rows' ).add({});
			var col        = row.get( 'cols' ).add({});
			var length_row = this.model.get( 'rows' ).length
			row.get( 'cols[0].items' ).add( { _rel: "flex" } );
			row.set( 'index', length_row - 1 );

			if( layout == 'mobile' ) {
				col.set( 'style.maxWidth', 94 );
				col.set( 'unit', '%' );
			} else {
				$( '#hb-app' ).removeClass( 'row-empty' );
			}
		},
		initReady: function () {
			var view = this;
			this.sortable = new Sortable(this.$('.hb-rows').get(0), {
				handle: '.hb-row-drag-handle',
				group: 'rows', animation: 200,
				onEnd: function (evt) {
					view.$('.hb-row').trigger('updateIndex');
				}
			});
		},
		views: {
			"rowsView collection:rows > .hb-rows": WRNitro_HeaderBuilder_RowsView.extend({
				initialize: function () {
					this.listenTo(this.collection, "change:sticky", this.changeSticky, this);
				},
				changeSticky: function(item, collection, options){
					if(item.get('sticky')) {
						this.collection.invoke('set', 'sticky', false, {silent: true});
						item.set({sticky: true}, {silent: true})
					}
				}
			}),
		},
		updateCSS: function () {
			var style = this.model.get('settings.style').toJSON();
			this.$('.hb-rows.list-row').css(style);
			if (typeof style.backgroundImage !== 'undefined' && style.backgroundImage.length > 5) {
				this.$('.hb-rows.list-row').css('backgroundImage', 'url(' + style.backgroundImage + ')');
			}
		},
	});

	exports.WRNitro_HeaderBuilder_Desktop_View = WRNitro_HeaderBuilder_Base_View.extend({
		initialize: function () {
			this.init();
			this.listenTo(this.model.get('settings.style'), 'change:width', this.changeWidth);
			this.listenTo(this.model.get('settings'), 'change:unit', this.changeUnit);
			this.changeWidth();
		},
		changeWidth: function(){
			if( this.model.get('settings.type') == 'vertical' ) {
				var width = this.model.get('settings.style.width');
				var unit = this.model.get('settings.unit');
				this.$('.list-row-outer').css('width', width + unit);
			}
		},
		changeUnit: function(){
			if( this.model.get('settings.type') == 'vertical' ) {
				var width = this.model.get('settings.style.width');
				var unit  = this.model.get('settings.unit');

				if( unit == '%' && width > 100 ) {
					this.model.set( 'settings.style.width', '100' );
					this.$('.list-row-outer').css('width', 100 + unit);
				} else {
					this.$('.list-row-outer').css('width', width + unit);
				}
			}
		},
		updateCSS: function () {
			var style = this.model.get('settings.style').toJSON();
			delete style.width;

			this.$('.hb-rows.list-row').css( style );
			if (typeof style.backgroundImage !== 'undefined' && style.backgroundImage.length > 5) {
				this.$('.hb-rows.list-row').css('backgroundImage', 'url(' + style.backgroundImage + ')');
			}

			if( this.model.get('settings.type') == 'vertical' ) {
				this.changeWidth();
			}
		},
		ready: function(){
			if( ! ( window.headerData.desktop != undefined && window.headerData.desktop.rows != undefined && window.headerData.desktop.rows.length ) ) {
				$( '#hb-app' ).addClass( 'row-empty' );
			}

			if( this.model.get('settings.type') != 'vertical') {
				this.initReady();
			} else {
				if ( typeof headerData.desktop === 'undefined') {
					this.$('.add-row').hide();
					this.model.set('rows[0].vertical', false);
				}
			}
		}
	});

	exports.WRNitro_HeaderBuilder_Mobile_View = WRNitro_HeaderBuilder_Base_View.extend({
		initialize: function () {
			this.init();
		},
		ready: function(){
			this.initReady();
		},
		updateCSS: function () {
			var style = this.model.get('settings.style').toJSON();
			delete style.width;
			this.$('.hb-rows.list-row').css(style);
			
			if (typeof style.backgroundImage !== 'undefined' && style.backgroundImage.length > 5) {
				this.$('.hb-rows.list-row').css('backgroundImage', 'url(' + style.backgroundImage + ')');
			}
		},
	});

	exports.WRNitro_HeaderBuilder_View = WRNitro_View.extend({
		events: {
			'change .wr-import-input'                      : 'importDataFromLocal',
			'click .setting-item.import, #load-template .des-import span' : 'openChoseFile',
			'click .setting-item.mobile'                   : 'switchMobileLayout',
			'click .setting-item.desktop'                  : 'switchDesktopLayout',
			'mouseover #export_data'                       : 'createBlobJSON',
			'click #export_data'                           : 'saveFile',
			'click #load-template .action .add-row'        : 'triggerAddRow',
			'click #load-template .action .add-template'   : 'modalTemplate',
			'click .setting-item.load-template'           : 'modalTemplate',
			'click .settings-library .setting-item.setting': function (e) {
				var layout      = this.model.get('layout');
				this.layoutView = eval('this.' + layout + 'View');
				log( this.layoutView.model.toJSON() );
				
				this.settingInspectorView.inspect(this.layoutView.model.get('settings'));
				this.closeInspector();
				this.settingInspectorView.open(e.currentTarget);
				e.stopPropagation();
			},
		},
		views: {
			"desktopView model:desktop > .hb-desktop-view" : WRNitro_HeaderBuilder_Desktop_View,
			"mobileView model:mobile > .hb-mobile-view"    : WRNitro_HeaderBuilder_Mobile_View,
			"settingInspectorView > .hb-setting-inspector" : WRNitro_HeaderBuilder_InspectorView.extend(),
			"rowInspectorView > .hb-row-inspector"         : WRNitro_HeaderBuilder_InspectorView.extend(),
			"searchInspectorView > .hb-search-inspector"   : WRNitro_HeaderBuilder_InspectorView.extend(),
			"menuInspectorView > .hb-menu-inspector"       : WRNitro_HeaderBuilder_InspectorView.extend(),
			"sidebarInspectorView > .hb-sidebar-inspector" : WRNitro_HeaderBuilder_InspectorView.extend(),
			"textInspectorView > .hb-text-inspector"       : WRNitro_HeaderBuilder_InspectorView.extend(),
			"logoInspectorView > .hb-logo-inspector"       : WRNitro_HeaderBuilder_InspectorView.extend(),
			"socialInspectorView > .hb-social-inspector"   : WRNitro_HeaderBuilder_InspectorView.extend({
				initialize: function () {
					this.visibility = false;
					this.on('before:inspect', this.beforeInspect);
				},
				beforeInspect: function () {
					this.$('.list-chb.social input[type="checkbox"]').prop('checked', false);
				}
			}),
			"shoppingcartInspectorView > .hb-shopping-cart-inspector": WRNitro_HeaderBuilder_InspectorView.extend(),
			"wpmlInspectorView > .hb-wpml-inspector": WRNitro_HeaderBuilder_InspectorView.extend(),
			"wishlistInspectorView > .hb-wishlist-inspector": WRNitro_HeaderBuilder_InspectorView.extend(),
			"currencyInspectorView > .hb-currency-inspector": WRNitro_HeaderBuilder_InspectorView.extend(),
		},
		initialize: function () {
			// this.listenTo(this.model, 'change', this.createBlobJSON);
			this.listenTo(this.model, 'change:layout', this.layoutChange);
			
			$(window).on('keydown', _.bind(function(e){
				this.keyDownHandle(e, this)
			}, this));

			$('body').on('mousedown', '.sp-clear, .media-modal', function (e) {
				e.stopPropagation();
			});

			if( this.$el.hasClass('hb-vertical') ){
				this.model.set('desktop.settings.type', 'vertical');
				this.model.set('mobile.settings.type', 'vertical');
				this.model.set('hbLayout', 'vertical');
				log('set vertical')
			} else {
				this.model.set('desktop.settings.type', 'horizontal');
				this.model.set('mobile.settings.type', 'horizontal');
				this.model.set('hbLayout', 'horizontal');
			}

			$('body').on('mousedown', '.sp-container,.sp-picker-container',  function(e){
				log('mousedown')
				e.stopPropagation();
			});


			$( 'body' ).on( 'click', '#hb-modal .modal-overlay', function(e){
				e.stopPropagation();
				$( '#hb-modal' ).removeClass( 'active' );
				$( 'body' ).addClass( 'no-scoll' );
			});

			var view = this;
			var layout = view.model.get( 'hbLayout' );

			var name_json = ( layout == 'horizontal' ) ? 'hoz' : 'ver';

			$( 'body' ).on( 'click', '#hb-modal .install-inner', function(e){
				e.stopPropagation();
				var data_id = $(this).attr( 'data-id' );

				$.getJSON( wr_site_data.theme_url + '/woorockets/includes/header-builder/data-json/' + name_json + '-' + data_id + '.json', function( response ) {
					if( response ) {
						view.model.clear();
						view.model.set( response );
						
						$( '#hb-modal' ).removeClass( 'active' );
						$( 'body' ).removeClass( 'no-scoll' );
						$( '#hb-app' ).removeClass( 'row-empty' );
					}
				});
			});

			$( window ).resize(function() {
				view.resize_menu();
			} );
		},
		ready: function () {
			this.$el.show();
			this.$('.hb-settings-box').hide();
			this.sortable = new Sortable(this.$('.hb-list-element').get(0), {
				group: {
					name: 'items',
					pull: 'clone',
					put: false
				},
				animation: 200,
				sort: false,
				onEnd: function (evt) {
					var el = evt.item;
					var itemType = $(el).attr('data-item');
					var index = $(el).index();
					var col = $(el).parents('.hb-items');
					col.trigger('addItem', {model: {_rel: itemType, index: index}, el: el});
					$( '.hb-wrapper .hb-content .list-row .hb-row .hb-items' ).removeClass( 'active-drag' );

				},
				onMove: function(evt){
					$( '.hb-wrapper .hb-content .list-row .hb-row .hb-items' ).removeClass( 'active-drag' );
					var el = evt.to;
					$(el).addClass( 'active-drag' );
				},
			});
			var layout = this.model.get('layout');
			if (layout == 'mobile') {
				this.$el.addClass('mobile');
				this.$el.removeClass('desktop');
			} else {
				this.$el.addClass('desktop');
				this.$el.removeClass('mobile');
			}
			this.layoutView = eval('this.' + layout + 'View');
			
			this.listenTo(this.model, 'change', this.saveChange);
			_.delay(function(){
				this.already = true;
				// $( '#btn-save-header' ).addClass( 'disabled' );
			},100);

			this.addDataDefault();

			// Feature copy data of WPML plugin
			this.copyDataWPML();
		},
		triggerAddRow : function ( ) {
			$( '.hb-desktop-view .add-row' ).trigger( 'click' );
		},
		modalTemplate : function ( ) {
			if( ! $( 'div#hb-modal' ).length ) {
				$( 'body' ).append( $( 'script#hb-list-template' ).html() );
			}

			$( 'body' ).addClass( 'no-scoll' );

			setTimeout( function(){
				$( '#hb-modal' ).addClass( 'active' );
			}, 100 );
		},
		addDataDefault : function ( ) {
			log( this.model.toJSON() );

			if ( typeof headerData.creatFirst === 'undefined' || headerData.creatFirst == false ) {
				var row = this.model.get('mobile.rows').add({});
				var col = row.get('cols').add({});
				row.get('cols[0].items').add( { _rel: "logo" } );
				row.get('cols[0].items').add( { _rel: "flex" } );
				row.get('cols[0].items').add( { _rel: "menu" } );

				col.set( 'style.maxWidth', 94 );
				col.set( 'unit', '%' );
				row.set( 'index', 0 );

				this.model.set( { creatFirst: true } );
			}
		},
		saveChange: function () {
			this.resize_menu();
			
			// $( '#btn-save-header' ).removeClass( 'disabled' );
		},
		layoutChange: function () {
			var layout = this.model.get('layout');
			if (layout == 'mobile') {
				this.$el.addClass('mobile');
				this.$el.removeClass('desktop');
			} else {
				this.$el.addClass('desktop');
				this.$el.removeClass('mobile');
			}
		},
		importDataFromLocal: function (e) {
			var selector = e.currentTarget;
			var file = selector.files[0];
			var view = this;
			var contents = '';
			if (file) {
				if ( this.checkFileAPI ) {
					var read = new FileReader();
					read.onload = function (e) {
						contents = e.target.result;
					};
					read.onloadend = function () {
						if (/^[\],:{}\s]*$/.test(contents.replace(/\\["\\\/bfnrtu]/g, '@').
								replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
								replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

							//The json is ok
							var data   = JSON.parse(contents);
							var layout = view.model.get('hbLayout');

							if( data['hbLayout'] == layout ) {
								view.model.clear();
								view.model.set(data); 
								$( '#hb-app.row-empty' ).removeClass( 'row-empty' );
							} else {
								if( layout == 'vertical' ){
									alert( 'Data was imported does not appropriate with vertical layout' );
								} else {
									alert( 'Data was imported does not appropriate with horizontal layout' );
								}
							}

							log( data )
							log( view )
						} else {
							alert('This file is not JSON');
						}
					};
					read.readAsText(file);
				}
			} else {
				alert('Failed to load this file');
			}

			//Reset files array
			selector.value = '';
		},
		copyDataWPML: function (e) {
			var view = this;
			$( document ).ajaxComplete( function( event, xhr, settings ) {
				var data_request = ( typeof settings.data != 'undefined' ) ? settings.data : '';

				if ( data_request.search( 'icl_ajx_action=copy_from_original' ) != -1 ) {
					var data = JSON.parse( xhr.responseJSON.customfields.header_data );
					$( '#hb-app' ).removeClass( 'row-empty' );
					view.model.clear();
					view.model.set( data );
				}
			} );
		},
		stripslashes: function ( str ) {
			return (str + '').replace(/\\(.?)/g, function (s, n1) {
				switch (n1) {
					case '\\':
						return '\\'
					case '0':
						return '\u0000'
					case '':
						return ''
					default:
						return n1
				}
			})
		},
		checkFileAPI: function () {
			if (window.File && window.FileReader && window.FileList && window.Blob) {
				return true;
			} else {
				alert('The File APIs are not fully supported by your browser. Fallback required.');
				return false;
			}
		},
		createBlobJSON: function () {
			var data = JSON.stringify(this.model.toJSON());
			var blob = new Blob([data], {type: "application/json"});
			var url = URL.createObjectURL(blob);

			var currentdate = new Date();
			var datetime = currentdate.getDate() + "/"
				+ (currentdate.getMonth() + 1) + "/"
				+ currentdate.getFullYear() + "-"
				+ currentdate.getHours() + ":"
				+ currentdate.getMinutes();

			var a = this.$('#export_data').get(0);
			a.href = url;
			a.download = 'woorockets-header-' + datetime + ".json";
		},
		switchMobileLayout: function () {
			this.layoutView  = this.mobileView;
			this.model.set({'layout': 'mobile', switchLayout: true});
		},
		copyToMobileData: function () {
			var desktopData = this.model.get('desktop').toJSON();
			if( typeof desktopData.settings.width !== 'undefined' ) {
				log(desktopData.settings.style.width)
				delete desktopData.settings.style.width;
			}
			if( typeof desktopData.settings.style.unit !== 'undefined' ) {
				delete desktopData.settings.style.unit;
			}
			log(desktopData)
			this.model.set('mobile', desktopData);
		},
		switchDesktopLayout: function () {
			this.layoutView  = this.desktopView;
			this.model.set({'layout': 'desktop', switchLayout: true});
		},
		saveFile: function ( e ) {
			if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
				e.preventDefault();

				var data = JSON.stringify( this.model.toJSON() );

				$.ajax( {
					type : 'POST',
					url  : wr_site_data.ajax_url,
					data : {
						action : 'save_data',
						data   : data,
						_nonce : wr_site_data._nonce
					},
					success: function( val ) { 
						if( val == 'true' ) {
							var url = wr_site_data.ajax_url + '?action=save_file&_nonce=' + wr_site_data._nonce;
							/*console.log( url );
							window.open( url );*/

							window.location.href = url;
						}
					}
				});
			}
		},
		openChoseFile: function (e) {
			log(this.$('.wr-import-input'))
			this.$('.wr-import-input').trigger('click');
		},
		closeInspector: function () {
			this.rowInspectorView.close();
			this.settingInspectorView.close();
			this.searchInspectorView.close();
			this.menuInspectorView.close();
			this.sidebarInspectorView.close();
			this.textInspectorView.close();
			this.logoInspectorView.close();
			this.socialInspectorView.close();
			this.shoppingcartInspectorView.close();
			this.wpmlInspectorView.close();
			this.wishlistInspectorView.close();
			this.currencyInspectorView.close();
		},
		keyDownHandle: function (e, view) {
			if ($(e.target).is('input,select,textarea') && !e.metaKey && !e.ctrlKey)
				return;
			switch (e.keyCode) {
				case 27: // ESC key
					e.preventDefault();
					this.closeInspector();
					break;
			}
		},
		keyUpHandle: function (e) {
			if ($(e.target).is('input,select,textarea') && !e.metaKey && !e.ctrlKey) {
				$(e.target).trigger('change');
			}
		},
	});

}(this, jQuery, _, Backbone);
//Backbone.noConflict();
