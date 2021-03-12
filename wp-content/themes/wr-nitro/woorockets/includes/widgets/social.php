<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

class WR_Nitro_Widgets_Social extends WP_Widget {
	function __construct() {
		$widget_ops  = array(
			'description' => __( 'A list of your social network', 'wr-nitro' )
		);

		$control_ops = array(
			'width'  => 'auto',
			'height' => 'auto',
		);

		parent::__construct( 'nitro_social', __( 'Nitro - Social', 'wr-nitro' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title         = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$list_social   = empty( $instance['list_social'] ) ? array() : unserialize( $instance['list_social'] );
		$style         = empty( $instance['style'] ) ? 'style-default' : $instance['style'];
		$multicolor    = empty( $instance['multicolor'] ) ? 0 : $instance['multicolor'];
		$icon_color    = empty( $instance['icon_color'] ) ? '' : $instance['icon_color'];
		$background_color = empty( $instance['background_color'] ) ? '' : $instance['background_color'];
		$border_width  = empty( $instance['border_width'] ) ? 0 : $instance['border_width'];
		$border_color  = empty( $instance['border_color'] ) ? 'transparent' : $instance['border_color'];
		$icon_size     = empty( $instance['icon_size'] ) ? '' : $instance['icon_size'];
		$icon_spacing  = empty( $instance['icon_spacing'] ) ? '' : $instance['icon_spacing'];
		$border_radius = empty( $instance['border_radius'] ) ? '' : $instance['border_radius'];
		$el_class      = empty( $instance['el_class'] ) ? '' : $instance['el_class'];

		echo '' . $before_widget;

		if ( ! empty( $title ) ) {
			echo '' . $before_title . $title . $after_title;
		}

		// Unique ID
		$id = uniqid( 'socials-' );

		$classes = array( 'social-bar' );
		// Get extra class
		if ( ! empty( $el_class ) ) {
			$classes[] = $el_class;
		}
		// Get icon style
		if ( $style ) {
			$classes[] = $style;
		}
		// Get Multi colors
		if ( $multicolor == 1 ) {
			$classes[] = 'multicolor';

			switch ( $style ) {
				case 'style-custom':
						if ( empty( $icon_color ) ) {
							$classes[] = 'outline';
						} else if ( empty( $icon_color ) && empty( $background_color ) ) {
							$classes[] = 'solid';
						}
					break;
				default:
						if ( empty( $icon_color ) ) {
							$classes[] = 'outline';
						}
					break;
			}
		}

		// Get Icon size
		if ( $icon_size ) {
			$classes[] = $icon_size;
		}

		$wr_nitro_options = WR_Nitro::get_options();
		$wr_channels = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'dribbble', 'behance', 'flickr', 'google-plus', 'medium', 'skype',  'slack', 'tumblr', 'vimeo', 'yahoo', 'youtube', 'rss', 'vk' );

		// Render html
		$output = '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		foreach ( $wr_channels as $key => $value ) {
			if ( isset( $wr_nitro_options[ $value ] ) && $wr_nitro_options[ $value ] ) {

				$value_insert = str_replace( '-', '_', esc_attr( $value ) );

				if ( in_array( $value_insert , $list_social ) ) {
					$output .='<a class="' . esc_attr( $value ) . ' dib pr tc" target="_blank" rel="noopener noreferrer" href="' . $wr_nitro_options[ $value ] . '"><i class="fa fa-' . esc_attr( $value ) . '"></i><span class="tooltip ab ts-03">' . str_replace( '-', ' ', esc_html( $value ) ) . '</span></a>';
				}
			}
		}
		$output .= '</div>';

		// Render CSS
		$output .= '<style>';
			$output .= '
				.widget #' . esc_attr( $id ) . '.social-bar a {';

					if ( ! empty( $icon_color ) ) {
						$output .= '
					color: ' . esc_attr( $icon_color ) . ' !important;';
					}

					if ( 'style-custom' == $style ) {

						if ( ! empty( $background_color ) ) {
							$output .= '
						background-color: ' . esc_attr( $background_color ) . ' !important;';
						}
						if ( ! empty( $border_color ) && ! empty( $border_width ) ) {
							$output .= '
						border-color: ' . esc_attr( $border_color ) . ' !important;';
						}
						if ( ! empty( $border_color ) && ! empty( $border_width ) ) {
							$output .= '
						border-radius: ' . esc_attr( $border_radius ) . 'px;';
						}
							$output .= '
						border-width: ' . ( $border_width ? esc_attr( $border_width ) . 'px' : 0 ) . ';';
					}

					if ( $border_width > 1 && $icon_size == 'large' ) {
						$output .= '
					line-height: ' . ( 64 - esc_attr( $border_width ) ) . 'px;';
					} elseif ( $border_width > 1 && $icon_size == 'normal' ) {
						$output .= '
					line-height: ' . ( 44 - esc_attr( $border_width ) ) . 'px;';
					} elseif ( $border_width > 1 && $icon_size == 'small' ) {
						$output .= '
					line-height: ' . ( 32 - esc_attr( $border_width ) ) . 'px;';
					}

					if ( ! empty( $icon_spacing ) ) {
						$output .= '
					margin: ' . esc_attr( $icon_spacing ) / 2 . 'px;';
					}

					$output .= '
				}';
				if ( ! empty( $icon_spacing) ) {
					$output .= '
						.widget #' . esc_attr( $id ) . '.social-bar {
							margin: 0 -' . esc_attr( $icon_spacing ) / 2 . 'px;
						}
					';
				}


		$output .= '</style>';

		echo '' . $output . $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['list_social']      = ( is_array( $new_instance['list_social'] ) ) ? serialize( $new_instance['list_social'] ) : '';
		$instance['multicolor']       = ! empty( $new_instance['multicolor'] ) ? 1 : 0;
		$instance['icon_color']       = esc_attr( $new_instance['icon_color'] );
		$instance['background_color'] = esc_attr( $new_instance['background_color'] );
		$instance['border_width']     = intval( $new_instance['border_width'] );
		$instance['border_color']     = esc_attr( $new_instance['border_color'] );
		$instance['icon_size']        = esc_attr( $new_instance['icon_size'] );
		$instance['icon_spacing']     = intval( $new_instance['icon_spacing'] );
		$instance['border_radius']    = intval( $new_instance['border_radius'] );
		$instance['el_class']         = esc_attr( $new_instance['el_class'] );
		if ( in_array( $new_instance['style'], array( 'style-default', 'style-custom' ) ) ) {
			$instance['style'] = $new_instance['style'];
		} else {
			$instance['style'] = 'style-default';
		}

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'            => '',
			'list_social'      => '',
			'style'            => 'style-default',
			'icon_color'       => '',
			'multicolor'       => '',
			'background_color' => '',
			'border_width'     => '',
			'border_color'     => '',
			'icon_size'        => '',
			'icon_spacing'     => '',
			'border_radius'    => '',
			'el_class'         => '',
		) );
		$title            = strip_tags( $instance['title'] );
		$links            = $instance['list_social'] ? unserialize( $instance['list_social'] ) : array();
		$style            = esc_attr( $instance['style'] );
		$multicolor       = esc_attr( $instance['multicolor'] );
		$icon_color       = esc_attr( $instance['icon_color'] );
		$background_color = esc_attr( $instance['background_color'] );
		$border_width     = intval( $instance['border_width'] );
		$border_color     = esc_attr( $instance['border_color'] );
		$icon_size        = esc_attr( $instance['icon_size'] );
		$icon_spacing     = intval( $instance['icon_spacing'] );
		$border_radius    = intval( $instance['border_radius'] );
	?>
		<style type="text/css">
			.wr-list-social {
				display: flex;
    			flex-wrap: wrap;
			}
			.wr-list-social .chb-item {
				width: 50%;
			    padding: 5px;
			    box-sizing: border-box;
			    text-transform: capitalize;
			}
			.wr-note {
				color: #999;
			}
		</style>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wr-nitro' ); ?></label><br />
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<div>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php printf( __( 'Select link to show (<a href="%s" target="_blank" rel="noopener noreferrer">Change your social links</a>)', 'wr-nitro' ), admin_url() . 'customize.php?autofocus[section]=social' ); ?></label>
			<div class="wr-list-social">
				<?php
					// Get theme options.
					$wr_nitro_options = WR_Nitro::get_options();
					$wr_channels = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'dribbble', 'behance', 'flickr', 'google-plus', 'medium', 'skype',  'slack', 'tumblr', 'vimeo', 'yahoo', 'youtube', 'rss', 'vk' );
					foreach ( $wr_channels as $value ) {
						if ( isset( $wr_nitro_options[ $value ] ) && $wr_nitro_options[ $value ] ) {

							$value_insert = str_replace( '-', '_', esc_attr( $value ) );

							echo '
							<label class="chb-item">
								<input ' . ( in_array( $value_insert, $links ) ? 'checked="checked"' : NULL ) . ' name="' . esc_attr( $this->get_field_name( "list_social" ) ) . '[]" type="checkbox" class="chb" value="' . $value_insert . '">
								<span>' . str_replace( '-', ' ', esc_attr( $value ) ) . '</span>
							</label>';
						}
					}
				?>
			</div>
		</div>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_html_e( 'Select style:', 'wr-nitro' ); ?></label><br />
			<select class="widefat wr-style" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" >
				<option value="style-default" <?php echo ( ( $style == 'style-default' ) ? 'selected="selected"' : NULL ); ?>><?php esc_html_e( 'None', 'wr-nitro' ); ?></option>
				<option value="style-custom" <?php echo ( ( $style == 'style-custom' ) ? 'selected="selected"' : NULL ); ?>><?php esc_html_e( 'Custom', 'wr-nitro' ); ?></option>
			</select>
		</p>
		<p>
			<label class="chb-item">
				<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'multicolor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'multicolor' ) ); ?>"<?php checked( $multicolor ); ?> />
				<span><?php esc_html_e( 'Multi Colors', 'wr-nitro' ); ?></span>
			</label>
		</p>
		<p><small class="wr-note"><?php esc_html_e( 'When this option is checked, color or background is auto selected by its default branding accordingly. Color or background must be empty value.', 'wr-nitro' ); ?></small></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>"><?php esc_html_e( 'Icon color:', 'wr-nitro' ); ?></label><br />
			<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_color' ) ); ?>" value="<?php echo esc_attr( $icon_color ); ?>" /><br />
		</p>

		<div class="wr-style-custom" <?php echo ( ( $style != 'style-custom' ) ? 'style="display: none"' : NULL ); ?>>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'background_color' ) ); ?>"><?php esc_html_e( 'Background color:', 'wr-nitro' ); ?></label><br />
				<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'background_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'background_color' ) ); ?>" value="<?php echo esc_attr( $background_color ); ?>" /><br />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>"><?php esc_html_e( 'Border color:', 'wr-nitro' ); ?></label><br />
				<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" value="<?php echo esc_attr( $border_color ); ?>" /><br />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_width' ) ); ?>"><?php esc_html_e( 'Border width:', 'wr-nitro' ); ?></label>
				<input class="tiny-text" type="number" id="<?php echo esc_attr( $this->get_field_id( 'border_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_width' ) ); ?>" value="<?php echo esc_attr( $border_width ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>"><?php esc_html_e( 'Border radius:', 'wr-nitro' ); ?></label>
				<input class="tiny-text" type="number" id="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_radius' ) ); ?>" value="<?php echo esc_attr( $border_radius ); ?>" />
			</p>
		</div>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_spacing' ) ); ?>"><?php esc_html_e( 'Icon spacing:', 'wr-nitro' ); ?></label>
			<input class="tiny-text" type="number" id="<?php echo esc_attr( $this->get_field_id( 'icon_spacing' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_spacing' ) ); ?>" value="<?php echo esc_attr( $icon_spacing ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_size' ) ); ?>"><?php esc_html_e( 'Icon size:', 'wr-nitro' ); ?></label><br />
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_size' ) ); ?>" >
				<option <?php echo ( ( $icon_size == 'small' ? 'selected="selected"' : NULL ) ); ?> value="small"><?php esc_html_e( 'Small', 'wr-nitro' ); ?></option>
				<option <?php echo ( ( $icon_size == 'normal' ? 'selected="selected"' : NULL ) ); ?> value="normal"><?php esc_html_e( 'Normal', 'wr-nitro' ); ?></option>
				<option <?php echo ( ( $icon_size == 'large' ? 'selected="selected"' : NULL ) ); ?> value="large"><?php esc_html_e( 'Large', 'wr-nitro' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'el_class' ) ); ?>"><?php esc_html_e( 'Extra Class', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'el_class' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'el_class' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'el_class' ] ); ?>" />
		</p>

		<script type='text/javascript'>
			jQuery( document ).ready( function($) {
				// Init color picker
				$( 'input.widget-color-picker' ).each( function(){
					var _this = $(this);

					if( _this.closest('.widget-liquid-left').length != 1 ) {
						_this.wpColorPicker();
					}
				} );

				// Dependency
				$( 'body' ).off( 'change', '.wr-style' ).on( 'change', '.wr-style', function() {
					var _this = $(this);
					var val = _this.val();
					var parent = _this.closest( '.widget-content' );

					if ( val == 'style-custom' ) {
						parent.find( '.wr-style-custom' ).show();
					} else {
						parent.find( '.wr-style-custom' ).hide();
					}
				} )
			});
		</script>

	<?php
	}
}
