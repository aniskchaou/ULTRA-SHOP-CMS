void function ( exports, $, _, Backbone ) {

	exports.NITRO_HB_TYPE_HORZ = 1;
	exports.NITRO_HB_TYPE_VERT = 2;

	exports.getNumber = function(str){
		str.replace(/%|px|em/, '');
		return str;
	}

	var styleModel = B.Model({
		fontWeightIsBold: B.Compute({
			deps: ['fontWeight'],
			get: function(value) {
				return value == 'bold' ? true : false;
			},
			set: function( value ) {
				this.set('fontWeight', value == true ? 'bold' : 'normal')
			}
		}),
		textDecorationIsUnderline: B.Compute({
			deps: ['textDecoration'],
			get: function(value) {
				return value == 'underline' ? true : false;
			},
			set: function( value ) {
				this.set('textDecoration', value == true ? 'underline' : 'none')
			}
		}),
		fontStyleIsItalic: B.Compute({
			deps: ['fontStyle'],
			get: function(value) {
				return value == 'italic' ? true : false;
			},
			set: function( value ) {
				this.set('fontStyle', value == true ? 'italic' : 'normal')
			}
		}),
		backgroundImageNotEmpty: B.Compute(['backgroundImage'], function(arg) {
			return typeof arg !== 'undefined' && arg != '' && arg != ' ' ? true : false;
		}),
	});

	var ItemModel = B.Model({
		style: B.Model(styleModel, {
		}),
	});
	var columsCollection = B.Collection(
		B.Model(
			$.extend( {}, wr_hb_data_allow['cols'],{
				"items": B.Collection({
					"search": B.Model( ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['search'], {
							style: B.Model( styleModel, $.extend( true, {}, wr_hb_data_allow['items']['search']['style'] ) ),
							dropdownLayout: B.Compute(['layout'], function(arg) {
								return arg == 'dropdown' ? true : false;
							}),
							showLiveSearch: B.Compute(['liveSearch'], function(arg) {
								return arg.toJSON().active ? true : false;
							}),
							showButton: B.Compute(['layout'], function(arg) {
								return arg == 'boxed' ? true : false;
							}),
							showTextButton: B.Compute(['buttonType'], function(arg) {
								return arg == 'text' ? true : false;
							}),
							showIconButton: B.Compute(['buttonType'], function(arg) {
								return arg == 'icon' ? true : false;
							}),
							liveSearch: B.Model(
								styleModel, $.extend( {}, wr_hb_data_allow['items']['search']['liveSearch'], {
									searchIn: B.Model( styleModel, $.extend( true, {}, wr_hb_data_allow['items']['search']['liveSearch']['searchIn'] ) ),
								} ) 
							),
						})
					),
					"menu": B.Model( ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['menu'], {
							background: B.Model( styleModel, wr_hb_data_allow['items']['menu']['background'] ),

							spacing: B.Model( styleModel, wr_hb_data_allow['items']['menu']['spacing'] ),

							textSettings: B.Model( styleModel, wr_hb_data_allow['items']['menu']['textSettings'] ),

							subMenu: B.Model(
								$.extend( {}, wr_hb_data_allow['items']['menu']['subMenu'], {
									style: B.Model( styleModel, wr_hb_data_allow['items']['menu']['subMenu']['style'] ),
									link: B.Model({
										style: B.Model( styleModel, wr_hb_data_allow['items']['menu']['subMenu']['link']['style'] )
									}),
								})
							),
							link: B.Model(
								$.extend( {}, wr_hb_data_allow['items']['menu']['link'], {
									style: B.Model( styleModel, wr_hb_data_allow['items']['menu']['link']['style'] ),
								})
							),
							layoutStyleText       : B.Compute(['layoutStyle'], function(arg) {
								return arg == 'text' ? true : false;
							}),
							submenuNormalVertical : B.Compute(['subMenu'], function(arg) {
								return arg.toJSON().animationVertical == 'normal' ? true : false;
							}),
							layoutStyleIcon       : B.Compute(['layoutStyle'], function(arg) {
								return arg == 'icon' ? true : false;
							}),
							menuStyleFullscreen   : B.Compute(['menuStyle'], function(arg) {
								return arg == 'fullscreen' ? true : false;
							}),
							menuStyleSidebar      : B.Compute(['menuStyle'], function(arg) {
								return arg == 'sidebar' ? true : false;
							}),
							defaultHover          : B.Compute(['hoverStyle'], function(arg) {
								return ( arg == 'default' ) ? true : false;
							}),
							underlineHover        : B.Compute(['hoverStyle'], function(arg) {
								return ( arg == 'underline' ) ? true : false;
							}),
							backgroundHover       : B.Compute(['hoverStyle'], function(arg) {
								return ( arg == 'background' ) ? true : false;
							}),
							oulineHover           : B.Compute(['hoverStyle'], function(arg) {
								return ( arg == 'ouline' ) ? true : false;
							}),
							layoutStyleMobileIcon : B.Compute(['layoutStyleMobile'], function(arg) {
								return arg == 'icon' ? true : false;
							}),
						})
					),
					"sidebar": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['sidebar'], {
							frontCSS: B.Model( {
								style:B.Model( styleModel, wr_hb_data_allow['items']['sidebar']['frontCSS']['style'] ),

								spacing: B.Model( styleModel, wr_hb_data_allow['items']['sidebar']['frontCSS']['spacing'] ),
							}),
							heightShow: B.Compute(['position'], function(arg) {
								return ( arg == 'top' || arg == 'bottom' ) ? true : false;
							}),
							widthShow: B.Compute(['position'], function(arg) {
								return ( arg == 'left' || arg == 'right' ) ? true : false;
							}),
						})
					),
					"text": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['text'], {
							style: B.Model( styleModel, wr_hb_data_allow['items']['text']['style'] ),
						})
					),
					"logo": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['logo'], {
							logoImage: (typeof wr_site_data.theme_url !== 'undefined' ? wr_site_data.theme_url : '') + '/assets/woorockets/images/logo.png',
							isLogoText: B.Compute(['logoType'], function(arg){
								return arg == 'text' ? true : false;
							}),
							isLogoImage: B.Compute(['logoType'], function(arg){
								return arg == 'image' ? true : false;
							}),
							style: B.Model( styleModel, wr_hb_data_allow['items']['logo']['style'] ),
						})
					),
					"social": B.Model( ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['social'], {
							socialList      : B.Model({}),
							customStyle: B.Compute(['iconStyle'], function(arg) {
								return arg == 'custom' ? true : false;
							}),
							style: B.Model( styleModel, wr_hb_data_allow['items']['social']['style'] ),
						})
					),
					"shopping-cart": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['shopping-cart'], {
							styleIcon      : B.Model(styleModel, wr_hb_data_allow['items']['shopping-cart']['styleIcon'] ),
							typeDropdown   : B.Compute(['type'], function(type) {
								return type == 'dropdown' ? true : false;
							}),
							typeSidebar    : B.Compute(['type'], function(type) {
								return type == 'sidebar' ? true : false;
							}),
							showColorPrice : B.Compute(['showCartInfo'], function(type) {
								return ( type == 'total_price' || type == 'number_price' ) ? true : false;
							}),
							style          : B.Model( styleModel, wr_hb_data_allow['items']['shopping-cart']['style'] ),
						})
					),
					"wpml": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['wpml'], {
							style: B.Model( styleModel, wr_hb_data_allow['items']['wpml']['style'] ),
						})
					),
					"wishlist": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['wishlist'], {
							style: B.Model( styleModel, wr_hb_data_allow['items']['wishlist']['style'] ),
						})
					),
					"currency": B.Model(ItemModel,
						$.extend( {}, wr_hb_data_allow['items']['currency'], {
							style: B.Model( styleModel, wr_hb_data_allow['items']['currency']['style'] ),
						})
					),
					"flex": B.Model(ItemModel, {
					}),
				},{
					initialize: function(){
						this.on( 'change:index', _.debounce( this.sort ));
					}
				}),
				style: B.Model( styleModel, wr_hb_data_allow['cols']['style'] )
			})
		)
	)
	exports.WRNitro_HeaderBuilder_Model = B.Model({
		rows : B.Collection(
			B.Model(
				$.extend( {}, wr_hb_data_allow['rows'], {
					"cols" : columsCollection,
					style  : B.Model( styleModel, wr_hb_data_allow['rows']['style'] ),
					stickyShow            : B.Compute(['sticky'], function( type ) {
						return type;
					}),
				})
			),{
				initialize: function(){
					this.on( 'change:index', _.debounce( this.sort ));
				}
			}
		),
		settings: B.Model(
			$.extend( {}, wr_hb_data_allow['settings'], {
				fixedList        : B.Model( {
					miscellaneous             : B.Model({}),
					custom_post_type_archives : B.Model({}),
					taxonomies                : B.Model({}),
					single                    : B.Model({}),
					pages                     : B.Model({}),
				}),
				style: B.Model(styleModel, wr_hb_data_allow['settings']['style'] ),
				isVertical    : B.Compute(['type'], function(arg){
					return arg == 'vertical' ? true : false;
				}),
				isHorizontal  : B.Compute(['type'], function(arg){
					return arg == 'horizontal' ? true : false;
				}),
				hideShowFixed : B.Compute(['position'], function(arg){
					return arg == 'fixed' ? true : false;
				})
			})
		)
	});

	exports.WRNitro_HeaderBuilder_AppModel = B.Model({
		desktop       : B.Model( WRNitro_HeaderBuilder_Model, {
			type: 'desktop'
		}),
		mobile        : B.Model( WRNitro_HeaderBuilder_Model, {
			type: 'mobile'
		}),
		layout        : 'desktop',
		hbLayout      : 'horizontal',
		switchLayout  : false,
		creatFirst    : false,
		isDesktopView : B.Compute(['layout'], function(arg){
			return arg == 'desktop' ? true : false;
		}),
		isMobileView  : B.Compute(['layout'], function(arg){
			return arg == 'mobile' ? true : false;
		})
	});

}( this, jQuery, _, Backbone );