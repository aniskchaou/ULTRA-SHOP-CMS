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
 * Class Toolkit Render Functions
 *
 * @since    1.0
 */
class Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = '';

	/**
	 * Shortcode style.
	 *
	 * @var  string
	 */
	protected $style = '';

	/**
	 * Meta key.
	 *
	 * @var  string
	 */
	protected $metakey = '';

	/**
	 * Attribute of the currently rendering shortcode.
	 *
	 * @var  array
	 */
	private static $_attrs;

	/**
	 * Register shortcode with WordPress.
	 *
	 * @return  void
	 */
	public function __construct() {
		if ( ! empty( $this->shortcode ) ) {
			// Add shortcode.
			add_shortcode( "nitro_{$this->shortcode}", array( &$this, 'generate_html' ) );

			// Hook into post saving.
			add_action( 'save_post', array( &$this, 'update_post' ) );

			// Add filters / actions to enqueue scripts / stylesheets and print inline CSS.
			add_action( 'wp_enqueue_scripts'        , array(                    &$this, 'enqueue_scripts'  ), 99998 ); // Priority less than WR_Nitro_Render::enqueue_scripts

			add_action( 'template_redirect'        , array(                    &$this, 'add_google_fonts'  ) );

			add_filter( 'nitro-shortcode-inline-css', array(                    &$this, 'add_inline_css'   )         );
			add_action( 'wp_head'                   , array( 'Nitro_Toolkit_Shortcode', 'print_inline_css' ), 99999 );
		}
	}

	/**
	 * Replace and save custom css to post meta.
	 *
	 * @param   int  $post_id
	 *
	 * @return  void
	 */
	public function update_post( $post_id ) {
		if ( ! isset( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Set and replace content.
		$post = $this->replace_post( $post_id );

		if ( $post ) {
			// Generate custom CSS.
			$css = $this->get_css( $post->post_content );

			// Update post and save CSS to post meta.
			$this->save_post( $post );
			$this->save_postmeta( $post_id, $css );
		} else {
			$this->save_postmeta( $post_id, '' );
		}
	}

	/**
	 * Replace shortcode used in a post with real content.
	 *
	 * @param   int  $post_id  Post ID.
	 *
	 * @return  WP_Post object or null.
	 */
	public function replace_post( $post_id ) {
		// Get post.
		$post = get_post( $post_id );

		if ( $post ) {
			if ( has_shortcode( $post->post_content, "nitro_{$this->shortcode}" ) ) {
				if ( ! function_exists( 'wr_nitro_toolkit_shortcode_replace_post_callback' ) ) {
					function wr_nitro_toolkit_shortcode_replace_post_callback( $matches ) {
						// Generate a random string to use as element ID.
						$id = 'nitro_custom_css_' . mt_rand();

						return $matches[1] . '="' . $id . '"';
					}
				}

				$post->post_content = preg_replace_callback(
					'/(' . $this->shortcode . '_custom_id)="[^"]+"/',
					'wr_nitro_toolkit_shortcode_replace_post_callback',
					$post->post_content
				);
			}
		}

		return $post;
	}

	/**
	 * Parse shortcode custom css string.
	 *
	 * @param   string  $content
	 * @param   string  $shortcode
	 *
	 * @return  string
	 */
	public function get_css( $content ) {
		$css = '';

		if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {
			foreach ( $shortcodes[2] as $index => $tag ) {
				if ( $tag == "nitro_{$this->shortcode}" ) {
					$atts = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
					$css .= $this->generate_css( $atts );
				}
			}

			foreach ( $shortcodes[5] as $shortcode_content ) {
				$css .= $this->get_css( $shortcode_content );
			}
		}

		return $css;
	}

	/**
	 * Update post data content.
	 *
	 * @param   int  $post  WP_Post object.
	 *
	 * @return  void
	 */
	public function save_post( $post ) {
		// Sanitize post data for inserting into database.
		$data = sanitize_post( $post, 'db' );

		// Update post content.
		global $wpdb;

		$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . esc_sql( $data->post_content ) . "' WHERE ID = {$data->ID};" );

		// Update post cache.
		$data = sanitize_post( $post, 'raw' );

		wp_cache_replace( $data->ID, $data, 'posts' );
	}

	/**
	 * Update extra post meta.
	 *
	 * @param   int     $post_id  Post ID.
	 * @param   string  $css      Custom CSS.
	 *
	 * @return  void
	 */
	public function save_postmeta( $post_id, $css ) {
		if ( $post_id && $this->metakey ) {
			if ( empty( $css ) ) {
				delete_post_meta( $post_id, $this->metakey );
			} else {
				update_post_meta( $post_id, $this->metakey, preg_replace( '/[\t\r\n]/', '', $css ) );
			}
		}
	}

	/**
	 * Generate custom CSS.
	 *
	 * @param   array  $atts  Shortcode parameters.
	 *
	 * @return  string
	 */
	public function generate_css( $atts ) {
		return '';
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
		return '';
	}

	/**
	 * Enqueue custom scripts / stylesheets.
	 *
	 * @return  void
	 */
	public function enqueue_scripts() {}

	/**
	 * Add google font to link base in Nitro theme.
	 *
	 * @return  void
	 */
	public function add_google_fonts() {}

	/**
	 * Add custom inline CSS.
	 *
	 * @param   array  $inline_css  Array of inline CSS.
	 *
	 * @return  array
	 */
	public function add_inline_css( $inline_css ) {
		if ( is_singular() && ! empty( $this->metakey ) && $post_id = get_the_ID() ) {
			$post_custom_css = get_post_meta( $post_id, $this->metakey, true );

			$inline_css[] = $post_custom_css;
		}

		return $inline_css;
	}

	/**
	 * Print custom inline CSS.
	 *
	 * @return  void
	 */
	public static function print_inline_css() {
		// Get all custom inline CSS.
		$inline_css = apply_filters( 'nitro-shortcode-inline-css', array() );

		if ( count( $inline_css ) ) {
			echo '<style id="wr-nitro-toolkit-inline" type="text/css">' . trim( implode( ' ', $inline_css ) ) . "</style>\n";
		}
	}

	/**
	 * Get shortcode attributes from outside function add_shortcode().
	 *
	 * @return  void
	 */
	public function get_attr( $content, &$atts = array() ) {
		if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {

			foreach ( $shortcodes[2] as $index => $tag ) {
				if ( $tag == "nitro_{$this->shortcode}" ) {
					$atts[] = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
				}
			}

			foreach ( $shortcodes[5] as $shortcode_content ) {
				$atts[] = $this->get_attr( $shortcode_content );
			}

			return $atts;
		}
	}

	/**
	 * Recursive get fonts
	 *
	 * @param   array  $atts  Attribute of shortcode
	 *
	 * @param   array  $attr_fonts 	Attribute of fonts
	 *
	 * @return  void
	 */
	public static function recursive_font( $atts, $attr_fonts ) {
		foreach ( $atts as $key => $val ) {
			if ( ! $val ) continue;

			if ( is_array( $val[ key( $val ) ] ) ) {
				self::recursive_font( $val, $attr_fonts );
			} else {
				$fonts_shortcode = array();

				if ( $attr_fonts ) {
					foreach( $attr_fonts as $font_name => $font_weight ) {
						if ( isset( $val[ $font_name ] ) ) {
							$font_weight                           = array( ( isset( $val[ $font_weight ] ) && $val[ $font_weight ] ) ? $val[ $font_weight ] : '400' );
							$fonts_shortcode[ $val[ $font_name ] ] = isset( $fonts_shortcode[ $val[ $font_name ] ] ) ? array_unique( array_merge( $fonts_shortcode[ $val[ $font_name ] ], $font_weight ) ) : $font_weight;
						}
					}
				}

				if ( $fonts_shortcode && class_exists( 'WR_Nitro_Helper' ) ) {
					WR_Nitro_Helper::add_google_font( $fonts_shortcode );
				}
			}
		}
	}

	/**
	 * Recursive check isset active slider.
	 *
	 * @param   array  $atts  Attribute of shortcode
	 *
	 * @param   bool  $check_once  Return true if isset slider attribute
	 *
	 * @return  bool
	 */
	public static function recursive_slider( $atts, &$check_once = false ) {
		if( ! $check_once ) {
			foreach ( $atts as $key => $val ) {
				if( ! $val ) continue;

				if( isset( $val['slider'] ) && $val['slider'] ) {
					$check_once = true;
					break;
				} elseif( is_array( $val[ key( $val ) ] ) ) {
					self::recursive_slider( $val, $check_once );
				}
			}

			if( $check_once )
				return true;
		} else {
			return true;
		}
	}

	/**
	 * Set shortcode attributes.
	 *
	 * @param   array  $attrs  Atrributes array.
	 *
	 * @return  void
	 */
	public static function set_attrs( $attrs ) {
		self::$_attrs = $attrs;
	}

	/**
	 * Get shortcode attributes.
	 *
	 * @return  void
	 */
	public static function get_attrs() {
		return self::$_attrs;
	}
}

/**
 * Enqueue stylesheets and scripts in admin.
 *
 * @return  void
 */
function nitro_toolkit_enqueue_admin_scripts() {
	if ( ! function_exists( 'vc_editor_post_types' ) ) {
		return;
	}

	// Get post type enabled for editing with Visual Composer.
	$types = vc_editor_post_types();

	// Check if current post type is enabled
	global $post;

	if ( isset( $post->post_type ) && in_array( $post->post_type, $types ) ) {
		wp_enqueue_style( 'wr-nitro-toolkit-admin', NITRO_TOOLKIT_URL . 'assets/css/admin.css' );
		wp_enqueue_script( 'wr-nitro-toolkit-admin', NITRO_TOOLKIT_URL . 'assets/js/admin.js', array(), '', true );
	}

}
add_action( 'admin_footer', 'nitro_toolkit_enqueue_admin_scripts', 10001 );

/**
 * Register all supported shortcodes.
 */
// Check plugin wc is activate
$active_plugin_wc = is_plugin_active( 'woocommerce/woocommerce.php' );
$shortcodes = array(
	'banner',
	'blog_list',
	'blog_single',
	'button',
	'carousel',
	'counter',
	'countdown',
	'dropcaps',
	'google_map',
	'heading',
	'member',
	'pricing_single',
	'pricing',
	'social',
	'buy_now',
	'separator',
	'services',
	'social_network',
	'lists',
	'quote',
	'subscribe_form',
	'testimonial',
	'masonry',
	'masonry_element',
	'video',
	'spotlight',
	'timeline'
);

if ( $active_plugin_wc ) {
	$shortcode_product = array(
		'product-attribute',
		'product_categories',
		'product_category',
		'product_package',
		'product_button',
		'product_menu',
		'product',
		'products'
	);
	$shortcodes = array_merge( $shortcodes, $shortcode_product );
}

foreach ( $shortcodes as $shortcode ) {
	// Include shortcode class declaration file.
	$shortcode = str_replace( '_', '-', $shortcode );

	if ( is_file( NITRO_TOOLKIT_PATH . '/includes/shortcode/' . $shortcode . '.php' ) ) {
		include_once NITRO_TOOLKIT_PATH . '/includes/shortcode/' . $shortcode . '.php';
	}

	// Generate shortcode class name.
	$class = 'Nitro_Toolkit_Shortcode_' . implode( '_', array_map( 'ucfirst', explode( '-', $shortcode ) ) );

	if ( class_exists( $class ) ) {
		$shortcode = new $class();
	}
}