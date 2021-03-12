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

class WR_Nitro_Widgets_Subscription extends WP_Widget {
	function __construct() {
		$widget_ops  = array(
			'description' => __( 'Display a Mailchimp subscription form', 'wr-nitro' )
		);

		$control_ops = array(
			'width'  => 'auto',
			'height' => 'auto',
		);

		parent::__construct( 'nitro_subscription', __( 'Nitro - Subscription Form', 'wr-nitro' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title         = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$link          = empty( $instance['link'] ) ? '' : $instance['link'];
		$placeholder   = empty( $instance['placeholder'] ) ? 'Enter your email' : $instance['placeholder'];
		$button_type   = empty( $instance['button_type'] ) ? 'button-submit' : $instance['button_type'];
		$button_text   = empty( $instance['button_text'] ) ? 'Subscribe' : $instance['button_text'];
		$icon_color    = empty( $instance['icon_color'] ) ? '#333333' : $instance['icon_color'];
		$input_bg      = empty( $instance['input_bg'] ) ? '#f9f9f9' : $instance['input_bg'];
		$border_color  = empty( $instance['border_color'] ) ? '#ebebeb' : $instance['border_color'];
		$bg_color      = empty( $instance['bg_color'] ) ? '#333333' : $instance['bg_color'];
		$text_color    = empty( $instance['text_color'] ) ? '#ffffff' : $instance['text_color'];
		$width         = empty( $instance['width'] ) ? '300px' : $instance['width'];
		$height        = empty( $instance['height'] ) ? '50' : $instance['height'];
		$icon_size     = empty( $instance['icon_size'] ) ? '16' : $instance['icon_size'];
		$text_size     = empty( $instance['text_size'] ) ? '14' : $instance['text_size'];
		$icon_position = empty( $instance['icon_position'] ) ? 'inside' : $instance['icon_position'];
		$border_width  = empty( $instance['border_width'] ) ? '0' : $instance['border_width'];
		$border_radius = empty( $instance['border_radius'] ) ? '' : $instance['border_radius'];
		$el_class      = empty( $instance['el_class'] ) ? '' : $instance['el_class'];

		$classes = array( 'sc-subscribe-form pr' );

		if ( ! empty( $el_class ) ) {
			$classes[] = $el_class;
		}
		if ( ! empty( $button_type ) ) {
			$classes[] = $button_type;
		}
		if ( 'icon-submit' == $button_type ) {
			$classes[] = $icon_position;
		}

		echo '' . $before_widget;

		if ( ! empty( $title ) ) {
			echo '' . $before_title . $title . $after_title;
		}

		// Unique ID
		$id = uniqid( 'subscribe-' );

		// Render html
		$output = '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode(' ', $classes ) ) . '">';
			$output .= '<form target="_blank" rel="noopener noreferrer" class="validate" name="mc-embedded-subscribe-form" method="post" action="' . esc_url( $link ) . '">';
				$output .= '<div class="mc-field-group fc">';
					$output .= '<input type="email" required="" placeholder="' . esc_attr( $placeholder ) . '" class="newsletter-email extenal-bdcl" name="EMAIL">';
				if ( 'button-submit' ==  $button_type ) {
					$output .= '<input type="submit" value="' . esc_attr( $button_text ) . '" class="newsletter-submit">';
				} else {
					$output .= '<button type="submit" class="newsletter-submit pa"><i class="fa fa-envelope-o"></i></button>';
				}
				$output .= '</div>';
			$output .= '</form>';
		$output .= '</div>';

		// Render CSS
		$output .= '<style>';

			if ( ! empty( $width ) ) {
				$output .= '
					#' . esc_attr( $id ) . '.sc-subscribe-form form {
						max-width: ' . ( ! empty( $width ) ? esc_attr( $width ) : '100%' ) . ';
						width: 100%;
					}
				';
			}
			if ( ! empty( $input_bg ) ) {
				$output .= '
					#' . esc_attr( $id ) . '.sc-subscribe-form input[type="email"] {
						background: ' . ( ! empty( $input_bg ) ? esc_attr( $input_bg ) : '#ebebeb' ) . ';
					}
				';
			}
			$output .= '
				#' . esc_attr( $id ) . '.sc-subscribe-form .mc-field-group > * {
					height: ' . ( ! empty( $height ) ? esc_attr( $height ) . 'px' : '45px' ) . ';
					line-height: ' . ( ! empty( $height ) ? esc_attr( $height ) . 'px' : '45px' ) . ';
					border-width: ' . ( ! empty( $border_width ) ? esc_attr( $border_width ) : '1px' ) . ';
					border-color: ' . ( ! empty( $border_color ) ? esc_attr( $border_color ) : '#ebebeb' ) . ';
					border-radius: ' . ( ! empty( $border_radius ) ? esc_attr( $border_radius ) . 'px' : '0px' ) . ';
				}
			';
				if ( 'button-submit' == $button_type && ! empty( $border_radius ) ) {
					$output .= '
				#' . esc_attr( $id ) . '.sc-subscribe-form input {
						margin-right: 10px;
					}';
				}

				if ( 'button-submit' == $button_type ) {
						$output .= '
					#' . esc_attr( $id ) . '.sc-subscribe-form input[type="submit"] {
						background: ' . esc_attr( $bg_color ) . ';
						color: ' . esc_attr( $text_color ) . ';
						font-size: ' . ( ! empty( $text_size ) ? esc_attr( $text_size ) . 'px' : '14px' ) . ';
					}';
				} else {
					$output .= '
					#' . esc_attr( $id ) . '.sc-subscribe-form button i {
						color: ' . esc_attr( $icon_color ) . ';
						font-size: ' . ( ! empty( $icon_size ) ? esc_attr( $icon_size ) . 'px' : '16px' ) . ';
					}';
					$output .= '
					#' . esc_attr( $id ) . '.sc-subscribe-form button {
						width: ' . esc_attr( $height ) . 'px;
					}';
				}
				if ( 'icon-submit' == $button_type && 'outside' == $icon_position ) {
						$output .= '
					#' . esc_attr( $id ) . '.sc-subscribe-form input[type="email"] {
						width: calc(100% - ' . esc_attr( $height ) . 'px);
					}
					#' . esc_attr( $id ) . '.sc-subscribe-form button {
						right: 0;
					}';
				}
		$output .= '</style>';

		echo '' . $output . $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['link']         = $new_instance['link'];
		$instance['placeholder']  = $new_instance['placeholder'];
		$instance['button_text']  = $new_instance['button_text'];
		$instance['width']        = $new_instance['width'];
		$instance['height']       = $new_instance['height'];
		$instance['icon_size']    = $new_instance['icon_size'];
		$instance['icon_color']   = $new_instance['icon_color'];
		$instance['text_size']    = $new_instance['text_size'];
		$instance['bg_color']     = $new_instance['bg_color'];
		$instance['text_color']   = $new_instance['text_color'];
		$instance['border_width'] = $new_instance['border_width'];
		$instance['border_radius'] = $new_instance['border_radius'];
		$instance['border_color'] = $new_instance['border_color'];
		$instance['input_bg'    ] = $new_instance['input_bg'];
		$instance['el_class']     = $new_instance['el_class'];

		if ( in_array( $new_instance['button_type'], array( 'button-submit', 'icon-submit' ) ) ) {
			$instance['button_type'] = $new_instance['button_type'];
		} else {
			$instance['button_type'] = 'button-submit';
		}
		if ( in_array( $new_instance['icon_position'], array( 'inside', 'outside' ) ) ) {
			$instance['icon_position'] = $new_instance['icon_position'];
		} else {
			$instance['icon_position'] = 'inside';
		}

		return $instance;
	}

	function form( $instance ) {
			$defaults = array(
				'title'         => '',
				'link'          => '',
				'placeholder'   => esc_html__( 'Enter your email', 'wr-nitro' ),
				'width'         => '100%',
				'height'        => '50',
				'button_type'   => 'button-submit',
				'icon_size'     => 16,
				'icon_position' => 'inside',
				'icon_color'    => '#333333',
				'button_text'   => esc_html__( 'Subscribe', 'wr-nitro' ),
				'text_size'     => 14,
				'bg_color'      => '#333',
				'text_color'    => '#fff',
				'border_width'  => '1px',
				'border_radius' => 0,
				'border_color'  => '#ebebeb',
				'input_bg'      => '#f9f9f9',
				'el_class'      => ''
			);
			// Merge the arguments with the defaults
			$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'title' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link to action', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'link' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'placeholder' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_html_e( 'Form Width (px or %)', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'width' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_html_e( 'Input Height (Unit: px)', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="number" value="<?php echo esc_attr( $instance[ 'height' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'input_bg' ) ); ?>"><?php esc_html_e( 'Input Background', 'wr-nitro' ); ?></label><br>
			<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'input_bg' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_bg' ) ); ?>" value="<?php echo esc_attr( $instance[ 'input_bg' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_type' ) ); ?>"><?php esc_html_e( 'Button Type:', 'wr-nitro' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'button_type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'button_type' ) ); ?>" class="widefat wr_button_type">
				<option value="button-submit"<?php selected( $instance['button_type'], 'button-submit' ); ?>><?php esc_html_e( 'Button', 'wr-nitro' ); ?></option>
				<option value="icon-submit"<?php selected( $instance['button_type'], 'icon-submit' ); ?>><?php esc_html_e( 'Icon', 'wr-nitro' ); ?></option>
			</select>
		</p>

		<div class="icons-group" <?php echo ( 'icon-submit' == $instance['button_type'] ) ? 'style="display: block"' : 'style="display: none"'; ?>>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icon_size' ) ); ?>"><?php esc_html_e( 'Icon Size (Unit: px)', 'wr-nitro' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_size' ) ); ?>" type="number" value="<?php echo esc_attr( $instance[ 'icon_size' ] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icon_position' ) ); ?>"><?php esc_html_e( 'Icon Position:', 'wr-nitro' ); ?></label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'icon_position' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'icon_position' ) ); ?>" class="widefat">
					<option value="insidet"<?php selected( $instance['icon_position'], 'inside' ); ?>><?php esc_html_e( 'Inside Form', 'wr-nitro' ); ?></option>
					<option value="outside"<?php selected( $instance['icon_position'], 'outside' ); ?>><?php esc_html_e( 'Outside Form', 'wr-nitro' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>"><?php esc_html_e( 'Icon Color:', 'wr-nitro' ); ?></label><br>
				<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_color' ) ); ?>" value="<?php echo esc_attr( $instance[ 'icon_color' ] ); ?>" />
			</p>
		</div>
		<div class="btn-group" <?php echo ( 'button-submit' == $instance['button_type'] ) ? 'style="display: block"' : 'style="display: none"'; ?>>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Button Text', 'wr-nitro' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'button_text' ]	 ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'text_size' ) ); ?>"><?php esc_html_e( 'Text Size (Unit: px)', 'wr-nitro' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_size' ) ); ?>" type="number" value="<?php echo esc_attr( $instance[ 'text_size' ] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>"><?php esc_html_e( 'Button Background:', 'wr-nitro' ); ?></label><br>
				<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_color' ) ); ?>" value="<?php echo esc_attr( $instance[ 'bg_color' ] ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'text_color' ) ); ?>"><?php esc_html_e( 'Button Text Color:', 'wr-nitro' ); ?></label><br>
				<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'text_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_color' ) ); ?>" value="<?php echo esc_attr( $instance[ 'text_color' ] ); ?>" />
			</p>
		</div>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'border_width' ) ); ?>"><?php esc_html_e( 'Border Width (eg: 1px or 2px 1px 0 3px)', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'border_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_width' ) ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'border_width' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>"><?php esc_html_e( 'Border Radius (Unit: px)', 'wr-nitro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'border_radius' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_radius' ) ); ?>" type="number" value="<?php echo esc_attr( $instance[ 'border_radius' ] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>"><?php esc_html_e( 'Border Color:', 'wr-nitro' ); ?></label><br>
			<input class="widget-color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" value="<?php echo esc_attr( $instance[ 'border_color' ] ); ?>" />
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
				$( 'body' ).off( 'change', '.wr_button_type' ).on( 'change', '.wr_button_type', function() {
					var _this = $(this);
					var val = _this.val();
					var parent = _this.closest( '.widget-content' );

					if ( val == 'icon-submit' ) {
						parent.find( '.icons-group' ).show();
						parent.find( '.btn-group' ).hide();
					} else {
						parent.find( '.icons-group' ).hide();
						parent.find( '.btn-group' ).show();
					}
				} )
			});
		</script>

		<?php
	}
}
