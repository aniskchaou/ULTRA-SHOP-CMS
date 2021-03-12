<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Custom functions for WooCommerce.
 */

/**
 * Editor custom control for WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Control_Editor extends WP_Customize_Control {
	public $type = 'editor';

	/**
	 * Editor mode.
	 *
	 * @var  string
	 */
	protected $mode = 'css';

	/**
	 * Placeholder text.
	 *
	 * @var  string
	 */
	protected $placeholder = "/**\n * Write your code here.\n */";

	/**
	 * Button text.
	 *
	 * @var  string
	 */
	protected $button_text = 'Set Code';

	/**
	 * Message to ask if Cancel button is clicked when change has been made.
	 *
	 * @var  string
	 */
	protected $confirm_message = 'Change has been made. Are you sure you want to cancel?';

	/**
	 * Constructor.
	 *
	 * @param   WP_Customize_Manager  $manager  WordPress's Customize Manager object.
	 * @param   string                $id       Control ID.
	 * @param   array                 $args     Control arguments.
	 *
	 * @return  void
	 */
	public function __construct( $manager, $id, $args ) {
		parent::__construct( $manager, $id, $args );

		if ( isset( $args['mode'] ) && in_array( $args['mode'], array( 'css', 'javascript', 'htmlmixed' ) ) ) {
			$this->mode = $args['mode'];
		}

		if ( isset( $args['placeholder'] ) ) {
			$this->placeholder = $args['placeholder'];
		}

		if ( isset( $args['button_text'] ) ) {
			$this->button_text = $args['button_text'];
		}

		if ( isset( $args['confirm_message'] ) ) {
			$this->confirm_message = $args['confirm_message'];
		}
	}

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return  void
	 */
	public function enqueue() {
		static $enqueued;

		if ( ! isset( $enqueued ) ) {
			// Load ThickBox library.
			add_thickbox();

			// Enqueue CodeMirror library for creating CSS / JS editor.
			wp_enqueue_style( 'codemirror', get_template_directory_uri() . '/assets/3rd-party/codemirror/lib/codemirror.css', array(), '5.15.2' );
			wp_enqueue_script( 'codemirror', get_template_directory_uri() . '/assets/3rd-party/codemirror/lib/codemirror.js', array(), '5.15.2', true );
			wp_enqueue_script( 'codemirror-css-mode', get_template_directory_uri() . '/assets/3rd-party/codemirror/mode/css/css.js', array( 'codemirror' ), '5.15.2', true );
			wp_enqueue_script( 'codemirror-js-mode', get_template_directory_uri() . '/assets/3rd-party/codemirror/mode/javascript/javascript.js', array( 'codemirror' ), '5.15.2', true );
			wp_enqueue_script( 'codemirror-html-mode', get_template_directory_uri() . '/assets/3rd-party/codemirror/mode/htmlmixed/htmlmixed.js', array( 'codemirror' ), '5.15.2', true );

			// Enqueue custom control script.
			wp_enqueue_script( 'wr-nitro-customize-editor', get_template_directory_uri() . '/assets/woorockets/js/admin/customize/control/editor.js', array( 'codemirror' ), '1.0.0', true );

			wp_localize_script( 'wr-nitro-customize-editor', 'wr_nitro_customize_editor', array(
				'type' => $this->type,
			) );

			// Register action to print HTML template for creating editor modal.
			add_action( 'customize_controls_print_footer_scripts', array( &$this, 'print_editor_template' ) );

			$enqueued = true;
		}
	}

	/**
	 * Print HTML template for creating editor modal.
	 *
	 * @return  void
	 */
	public function print_editor_template() {
		static $printed;

		if ( ! isset( $printed ) ) {
			?>
			<?php echo '<scr' . 'ipt type="text/html" id="nitro_customize_control_editor_template">'; ?>
				<div class="customize-control-editor">
					<div class="editor"></div>
					<hr>
					<div class="actions" style="text-align:center">
						<button type="button" class="button button-primary save"><?php esc_html_e( 'Save', 'wr-nitro' ); ?></button>
						<button type="button" class="button cancel"><?php esc_html_e( 'Cancel', 'wr-nitro' ); ?></button>
					</div>
				</div>
			<?php
			echo '</scr' . 'ipt>';
			$printed = true;
		}
	}

	/**
	 * Render the control's content.
	 *
	 * @return  void
	 */
	public function render_content() {
		if ( $this->label ) {
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php
		}

		if ( $this->description ) {
			?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php
		}
		?>
		<div class="customize-control-content" id="wr-<?php echo esc_attr( $this->type ); ?>-<?php echo esc_attr( $this->id ); ?>" style="text-align:center">
			<a class="button upload-button" title="<?php
				echo esc_attr( $this->label ? $this->label : $this->button_text );
			?>" href="#TB_inline?inlineId=" target="thickbox" data-width="50%" data-height="285px">
				<?php echo esc_html( $this->button_text ); ?>
			</a>
		</div>
		<?php echo '<scr' . 'ipt type="text/javascript">'; ?>
			new jQuery.WR_Editor_Control( {
				id: '<?php echo esc_js( $this->id ); ?>',
				mode: '<?php echo esc_js( $this->mode ); ?>',
				placeholder: '<?php echo esc_js( $this->placeholder ); ?>',
				button_text: '<?php echo esc_js( $this->button_text ); ?>',
				confirm_message: '<?php echo esc_js( $this->confirm_message ); ?>',
			} );
		<?php
		echo '</scr' . 'ipt>';
	}
}
