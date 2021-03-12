<?php
/**
 * @version    1.0
 * @package    Nitro_Toolkit
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

/**
 * Nitro Masonry Builder shortcode.
 */
class Nitro_Toolkit_Shortcode_Masonry extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'masonry';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '_wr_shortcode_masonry_custom_css';

	/**
	 * Enqueue custom scripts / stylesheets.
	 *
	 * @return  void
	 */
	public function enqueue_scripts() {
		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
				// Enqueue required assets.
				wp_enqueue_script( 'isotope' );
			}
		}

		// Let parent class load default scripts.
		parent::enqueue_scripts();
	}

	/**
	 * Generate custom CSS.
	 *
	 * @param   array  $atts  Shortcode parameters.
	 *
	 * @return  string
	 */
	public function generate_css( $atts ) {
		$css = array();

		// Extract shortcode parameters.
		extract( shortcode_atts(
			array(
				'column'               => '3',
				'gutter_width'         => '',
				'extra_class'          => '',
				'border'               => '',
				'border_color'         => '',
				'wr_masonry_custom_id' => ''
			), $atts )
		);

		// Generate custom ID
		$id = $wr_masonry_custom_id;
		$css[] = '
			#' . $id . ' {
				margin: -' . esc_attr( $gutter_width / 2 ) . 'px;';
				$css[] .= '
			}
			#' . $id . ' .item {
				padding: ' . esc_attr( $gutter_width / 2 ) . 'px;';
				$css[] .= '
			}
		';

		if ( $border ) {
			$css[] = '
				#' . $id . ',
				#' . $id . ' .item {
					border-style: solid;
					border-color: ' . esc_attr( $border_color ) . ';
				}
				#' . $id . ' {
					border-width: 1px 0 0 1px;
				}
				#' . $id . ' .item {
					border-width: 0 1px 1px 0;
				}
			';
		}

		return implode( '', $css );
	}

	/**
	 * Generate HTML code based on shortcode parameters.
	 *
	 * @param   array   $atts     Shortcode parameters.
	 * @param   string  $content  Current content.
	 *
	 * @return  string
	 */
	public function generate_html( $atts, $content = null ) {
		$html = $script = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'column'               => '3',
					'gutter_width'         => '',
					'extra_class'          => '',
					'wr_masonry_custom_id' => ''
				),
				$atts
			)
		);

		// Generate custom ID
		$id = $wr_masonry_custom_id;

		// Generate HTML code.
		$html .= '<div id="' . esc_attr( $id ) . '" class="wr-masonry-wrap columns-' . esc_attr( $column ) . '">';
			$html .= do_shortcode( $content );
		$html .= '</div>';

		$html .= '
			<script>
				(function($) {
				"use strict";

					function masonryBuilder() {
						// Check if Isotope plugin for jQuery is loaded before setting up masonry layout.
						if (typeof $.fn.isotope == "undefined") {
							return setTimeout(masonryBuilder, 100);
						}

						var container    = $( "#' . esc_js( $id ) . '.wr-masonry-wrap" ),
							LargeSquare  = container.find( ".large-square" ),
							SmallSquare  = container.find( ".small-square" ),
							recLandscape = container.find( ".rectangle-landscape" ),
							recPortrait  = container.find( ".rectangle-portrait" ),
							wrapWidth    = container.width();

						LargeSquare.css({
							"width": wrapWidth*2/' . esc_js( $column ) . ',
							"height": wrapWidth*2/' . esc_js( $column ) . ',
						});
						SmallSquare.css({
							"width": (wrapWidth*1/' . esc_js( $column ) . '),
							"height": wrapWidth*1/' . esc_js( $column ) . ',
						});
						recLandscape.css({
							"width": wrapWidth*2/' . esc_js( $column ) . ',
							"height": wrapWidth*1/' . esc_js( $column ) . ',
						});
						recPortrait.css({
							"width": wrapWidth*1/' . esc_js( $column ) . ',
							"height": wrapWidth*2/' . esc_js( $column ) . ',
						});
						setTimeout( function() {
							container.isotope({
								itemSelector: ".item",
								masonry: {
									columnWidth: wrapWidth / ' . esc_js( $column ) . ',
								}
							});
						}, 100)
					}
					$(window).load(function() {
						setTimeout(function(){
							masonryBuilder();
						}, 100);
					});
					$(window).resize(function() {
						if ( window.innerWidth > 1024 ) {
							setTimeout(function(){
								masonryBuilder();
							}, 10);
						}
					});
				})( jQuery );
			</script>
		';

		return apply_filters( 'nitro_toolkit_shortcode_masonry', force_balance_tags( $html ) );
	}
}
