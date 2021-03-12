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

/**
 * This class provides Ajax actions for sample data installation.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Sample_Data {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Define URL to get available sample data packages.
	 *
	 * @var  string
	 */
	protected static $server = 'sample-data.json';

	/**
	 * How to store and access images used in sample data.
	 *
	 * Either one of following options:
	 *
	 * 'local' - All demo images will be downloaded and stored locally.
	 * 'placeholder' - All demo images will be replaced with gray images and stored locally.
	 * 'remote' - All demo images will be accessed remotely.
	 *
	 * @var  string
	 */
	protected static $demo_images_storage = 'remote';

	/**
	 * Define regular expression pattern to look for demo site URL.
	 *
	 * @var  string
	 */
	protected static $demo_site_pattern = 'https?(%3A|:)[%2F\\\\/]+(rc|demo|nitro)\.woorockets\.com';

	/**
	 * Define regular expression pattern to look for demo image URL.
	 *
	 * @var  string
	 */
	protected static $demo_image_pattern = '(%2F|\\\\*/)([^\s\'"]*)wp-content[%2F\\\\/]+uploads([^\s\'"]+)';

	/**
	 * Define max backup file size (byte unit).
	 *
	 * @var  int
	 */
	protected static $max_backup_file_size = 2097152;

	/**
	 * Plug into WordPress.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Register Ajax actions for sample data installation.
		add_action( 'wp_ajax_nitro_install_sample_data'       , array( __CLASS__, 'install_sample_data'   ) );
		add_action( 'wp_ajax_nopriv_nitro_install_sample_data', array( __CLASS__, 'install_sample_data'   ) );
		add_action( 'wp_ajax_nitro_uninstall_sample_data'     , array( __CLASS__, 'uninstall_sample_data' ) );

		// Register filter to alter URL for demo images.
		if ( 'remote' == self::$demo_images_storage && false !== get_option( 'wr_nitro_sample_package' ) ) {
			add_filter( 'wp_get_attachment_url'         , array( __CLASS__, 'get_attachment_url'       ), 10, 2 );
			add_filter( 'wp_get_attachment_thumb_url'   , array( __CLASS__, 'get_attachment_url'       ), 10, 2 );
			add_filter( 'wp_get_attachment_image_src'   , array( __CLASS__, 'get_attachment_image_src' ), 10, 4 );
			add_filter( 'wp_calculate' . '_image_srcset', array( __CLASS__, 'calculate_image_srcset'   ), 10, 5 );
			add_filter( 'post_thumbnail_html'           , array( __CLASS__, 'post_thumbnail_html'      ), 10, 5 );
		}

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Get sample packages.
	 *
	 * @param   boolean  $package  If specified then will return data for the specified package only.
	 *
	 * @return  mixed
	 */
	public static function get_sample_packages( $package = null ) {
		// Get available sample data packages from transient first.
		$sample_packages = get_transient( 'wr_nitro_sample_packages' );

		if ( ! $sample_packages ) {
			// Request server for available sample data packages.
			if ( 'sample-data.json' == self::$server ) {
				global $wp_filesystem;

				$sample_packages = implode( '/', array_slice( explode( '/', str_replace( '\\', '/', __FILE__ ) ), 0, -1 ) ) . '/sample-data.json';

				if ( ! ( is_file($sample_packages) || $wp_filesystem->is_file($sample_packages) ) ) {
					return false;
				}

				// Read sample package definition.
				$sample_packages = ( $buffer = call_user_func('file_' . 'get' . '_contents', $sample_packages) ) ? $buffer : $wp_filesystem->get_contents($sample_packages);
			}

			else {
				$sample_packages = wp_remote_get( self::$server );

				if ( is_wp_error( $sample_packages ) ) {
					return false;
				}

				$sample_packages = $sample_packages['body'];
			}

			// Cache available sample data packages to transient.
			set_transient( 'wr_nitro_sample_packages', $sample_packages, HOUR_IN_SECONDS );
		}

		// Prepare data.
		$sample_packages = json_decode( $sample_packages, true );

		if ( ! is_null( $package ) ) {
			foreach ( $sample_packages as $sample_package ) {
				if ( $sample_package['id'] == $package ) {
					return $sample_package;
				}
			}

			return false;
		}

		return $sample_packages;
	}

	/**
	 * Install sample data package.
	 *
	 * @return  void
	 */
	public static function install_sample_data() {
		// Verify nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'nitro-install-sample' ) ) {
			wp_send_json_error( __( 'Nonce verification failed. This might due to your working session has been expired. <a href="javascript:window.location.reload();">Click here to refresh the page to renew your working session</a>.', 'wr-nitro' ) );
		}

		// Get selected sample data package.
		if ( ! isset( $_REQUEST['package'] ) ) {
			wp_send_json_error( __( 'Missing sample data package to install.', 'wr-nitro' ) );
		}

		// Get data for the selected sample data package.
		$package = self::get_sample_packages( $_REQUEST['package'] );

		if ( ! $package ) {
			wp_send_json_error( __( 'Failed to get data for the selected sample data package.', 'wr-nitro' ) );
		}

		// Get current step.
		$step = isset( $_REQUEST['step'] ) ? $_REQUEST['step'] : 1;

		switch ( $step ) {
			case '1' :
				// Print confirm message.
				self::print_confirm_message( 'install', $package );
			break;

			case '2' :
				// Download the selected sample package.
				self::download_sample_package( $package );
			break;

			case '3' :
				// Import sample data.
				self::import_sample_data( $package );
			break;

			case '4' :
				// Download demo assets.
				self::download_demo_assets( $package );
			break;
		}

		wp_send_json_error( __( 'Unknown step.', 'wr-nitro' ) );
	}

	/**
	 * Uninstall sample data.
	 *
	 * @return  void
	 */
	public static function uninstall_sample_data() {
		// Verify nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'nitro-uninstall-sample' ) ) {
			wp_send_json_error( __( 'Nonce verification failed. This might due to your working session has been expired. Please reload the page to renew your working session.', 'wr-nitro' ) );
		}

		// Get selected sample data package.
		if ( ! isset( $_REQUEST['package'] ) ) {
			wp_send_json_error( __( 'Missing sample data package to install.', 'wr-nitro' ) );
		}

		// Get data for the selected sample data package.
		$package = self::get_sample_packages( $_REQUEST['package'] );

		if ( ! $package ) {
			wp_send_json_error( __( 'Failed to get data for the selected sample data package.', 'wr-nitro' ) );
		}

		// Get current step.
		$step = isset( $_REQUEST['step'] ) ? $_REQUEST['step'] : 1;

		switch ( $step ) {
			case '1' :
				// Print confirm message.
				self::print_confirm_message( 'uninstall', $package );
			break;

			case '2' :
				// Restore the original data backed up before installing sample data.
				self::restore_backup_data( $package );
			break;
		}

		wp_send_json_error( __( 'Unknown step.', 'wr-nitro' ) );
	}

	/**
	 * Get remote URL for demo image.
	 *
	 * @param   string  $url      URL for the given attachment.
	 * @param   int     $post_id  Attachment ID.
	 *
	 * @return  string
	 */
	public static function get_attachment_url( $url, $post_id ) {
		// Check if attachment file exists.
		$upload = wp_upload_dir();
		$file   = str_replace( $upload['baseurl'], $upload['basedir'], $url );

		if ( ! @is_file( $file ) && $attachment = get_post( $post_id ) ) {
			if ( preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $attachment->guid ) ) {
				// Get base local and remote URL.
				$remote_base = current( explode( '/wp-content/uploads/', $attachment->guid ) ) . '/wp-content/uploads';

				// Replace local base with remote base.
				$url = str_replace( $upload['baseurl'], $remote_base, $url );
			}
		}

		return $url;
	}

	/**
	 * Get remote source for demo image.
	 *
	 * @param   array|false   $image          Either array with src, width & height, icon src, or false.
	 * @param   int           $attachment_id  Image attachment ID.
	 * @param   string|array  $size           Size of image. Image size or array of width and height values (in that order). Default 'thumbnail'.
	 * @param   bool          $icon           Whether the image should be treated as an icon. Default false.
	 *
	 * @return  array|false
	 */
	public static function get_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		// Check if attachment file exists.
		$upload = wp_upload_dir();
		$file   = str_replace( $upload['baseurl'], $upload['basedir'], $image[0] );

		if ( ! @is_file( $file ) && $attachment = get_post( $attachment_id ) ) {
			if ( preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $attachment->guid ) ) {
				// Get base local and remote URL.
				$remote_base = current( explode( '/wp-content/uploads/', $attachment->guid ) ) . '/wp-content/uploads';

				// Replace local base with remote base.
				$image[0] = str_replace( $upload['baseurl'], $remote_base, $image[0] );
			}
		}

		return $image;
	}

	/**
	 * Calculate remote source set for demo image.
	 *
	 * @param   array  $sources  {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string,
	 *                                  either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
	 *                                  pixel density value if paired with an 'x' descriptor.
	 *     }
	 * }
	 * @param   array   $size_array     Array of width and height values in pixels (in that order).
	 * @param   string  $image_src      The 'src' of the image.
	 * @param   array   $image_meta     The image meta data as returned by 'wp_get_attachment_metadata()'.
 	 * @param   int     $attachment_id  Image attachment ID or 0.
	 *
	 * @return  string|false
	 */
	public static function calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		foreach ( $sources as $width => $define ) {
			// Check if attachment file exists.
			$upload = isset( $upload ) ? $upload : wp_upload_dir();
			$file   = str_replace( $upload['baseurl'], $upload['basedir'], $define['url'] );

			if ( ! @is_file( $file ) ) {
				if ( preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $image_src ) ) {
					$remote_src = $image_src;
				} elseif ( $attachment = get_post( $attachment_id ) ) {
					if ( preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $attachment->guid ) ) {
						$remote_src = $attachment->guid;
					}
				}

				if ( isset( $remote_src ) ) {
					// Get base local and remote URL.
					$remote_base = current( explode( '/wp-content/uploads/', $remote_src ) ) . '/wp-content/uploads';

					// Replace local base with remote base.
					$sources[ $width ]['url'] = str_replace( $upload['baseurl'], $remote_base, $define['url'] );
				}
			}
		}

		return $sources;
	}

	/**
	 * Prepare HTML for post thumbnail.
	 *
	 * @param   string        $html               The post thumbnail HTML.
	 * @param   int           $post_id            The post ID.
	 * @param   string        $post_thumbnail_id  The post thumbnail ID.
	 * @param   string|array  $size               The post thumbnail size. Image size or array of width and height
	 *                                            values (in that order). Default 'post-thumbnail'.
	 * @param   string        $attr               Query string of attributes.
	 *
	 * @return  string
	 */
	public static function post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		$upload = wp_upload_dir();

		if ( $attachment = get_post( $post_thumbnail_id ) ) {
			if ( preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $attachment->guid ) ) {
				// Get base remote URL.
				$remote_base = current( explode( '/wp-content/uploads/', $attachment->guid ) ) . '/wp-content/uploads';

				// Replace local base with remote base.
				$html = str_replace( $upload['baseurl'], $remote_base, $html );
			}
		}

		return $html;
	}

	/**
	 * Print confirmation message.
	 *
	 * @param   string  $action   Either 'install' or 'uninstall'.
	 * @param   array   $package  Sample package data.
	 *
	 * @return  void
	 */
	protected static function print_confirm_message( $action, $package ) {
		// Print confirm message.
		ob_start();

		switch ( $action ) {
			case 'install' :
				// Check if the uploads and plugins directory are writable?
				$uploads = wp_upload_dir();

				if ( ! is_writable( $uploads['basedir'] ) ) {
					$unwritable[] = $uploads['basedir'];
				}

				if ( ! is_writable( WP_PLUGIN_DIR ) ) {
					$unwritable[] = WP_PLUGIN_DIR;
				}
				?>
				<div id="sample-data-installation-step-1">
					<div class="alert alert-warning">
						<span class="label label-danger"><?php esc_html_e( 'Important Notice', 'wr-nitro' ); ?></span>
						<ul>
							<?php if ( isset( $unwritable ) ) : ?>
							<li>
								<?php
								if ( count( $unwritable ) > 1 ) :

								_e ( 'Installing sample data is NOT recommended because the following directories are not writable.', 'wr-nitro' );

								else :

								_e ( 'Installing sample data is NOT recommended because the following directory is not writable.', 'wr-nitro' );

								endif;
								?>
								<br><br>
								<ol>
									<?php foreach ( $unwritable as $dir ) : ?>
									<li><?php echo str_replace( wr_get_server_param( 'DOCUMENT_ROOT' ), '', $dir ); ?></li>
									<?php endforeach; ?>
								</ol>
							</li>
							<?php else : ?>
							<li><?php printf( __( 'Installing sample data will replace the content of current website with <a href="%1$s" target="_blank" rel="noopener noreferrer"><strong>%2$s</strong> demo</a>.', 'wr-nitro' ), $package['demo'], $package['name'] ); ?></li>
							<li><?php esc_html_e( 'You can later uninstall sample data to restore the original data back.', 'wr-nitro' ); ?></li>
							<?php endif; ?>
						</ul>
					</div>

					<?php
					if ( ! isset( $unwritable ) ) :

					if ( array_key_exists( 'sample-page', $package ) && ! empty( $package['sample-page'] ) ) :
					?>
					<div id="sample-data-installation-options" class="nitro-wrap">
						<div class="radio">
							<label>
								<input name="option" type="radio" value="full" checked="checked">
								<?php esc_html_e( 'Install full demo', 'wr-nitro' ); ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input name="option" type="radio" value="page">
								<?php esc_html_e( 'Install page demo', 'wr-nitro' ); ?>
							</label>
						</div>
						<div class="box-wrap three-col select-page" style="display: none;">
							<?php foreach ( ( array ) $package['sample-page'] as $id => $page ) : ?>
							<div class="col">
								<div class="box">
									<a href="javascript:void(0)">
										<img src="<?php echo esc_url( $page['thumbail'] ); ?>" />
									</a>
									<div class="box-info">
										<h5><?php echo esc_html( $page['name'] ); ?></h5>
									</div>
									<input type="checkbox" name="page[]" style="display: none;" value="<?php
										echo esc_attr( $id );
									?>" />
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>

					<div class="checkbox">
						<label>
							<input name="agree" value="1" id="confirm-sample-data-installation" type="checkbox">
							<?php esc_html_e( 'I understand the impact of installing sample data.', 'wr-nitro' ); ?>
						</label>
					</div>

					<div class="actions">
						<button class="button button-primary" type="button" id="go-to-sample-data-installation-step-2" disabled="disabled">
							<?php esc_html_e( 'Continue', 'wr-nitro' ); ?>
						</button>
						<button class="button" type="button" id="cancel-sample-data-installation">
							<?php esc_html_e( 'Cancel', 'wr-nitro' ); ?>
						</button>
					</div>
					<?php endif; ?>
				</div>

				<div id="sample-data-installation-step-2" class="hidden" data-package="<?php echo esc_attr( $package['id'] ); ?>">
					<p>
						<?php esc_html_e( 'There are several stages involved in the process. Please be patient.', 'wr-nitro' ); ?>
					</p>
					<ul id="wr-install-sample-data-processes">
						<li id="wr-install-sample-data-download-package">
							<span class="wr-title"><?php esc_html_e( 'Download sample data package.', 'wr-nitro' ); ?></span>
							<span class="spinner is-active"></span>
							<div class="wr-status alert alert-danger hidden"></div>
						</li>
						<li id="wr-install-sample-data-upload-package" class="hidden">
							<span class="wr-title"><?php esc_html_e( 'Upload sample data package.', 'wr-nitro' ); ?></span>
							<span class="spinner is-active"></span>
							<div class="wr-status alert alert-danger hidden"></div>
						</li>
						<li id="wr-install-sample-data-import-data" class="hidden">
							<span class="wr-title"><?php esc_html_e( 'Install sample data.', 'wr-nitro' ); ?></span>
							<span class="spinner is-active"></span>
							<div class="wr-status alert alert-danger hidden"></div>
						</li>
						<li id="wr-install-sample-data-required-plugins" class="hidden">
							<span class="wr-title"><?php esc_html_e( 'Install required plugins.', 'wr-nitro' ); ?></span>
							<span class="spinner is-active"></span>
							<span class="install-status"></span>
							<div class="progress">
								<div class="progress-bar" role="progressbar">
									<span class="percentage">0</span>%
								</div>
							</div>
							<div class="wr-status alert alert-danger hidden"></div>
						</li>
						<li id="wr-install-sample-data-demo-assets" class="hidden">
							<span class="wr-title"><?php esc_html_e( 'Download demo assets.', 'wr-nitro' ); ?></span>
							<span class="spinner is-active"></span>
							<span class="download-status"></span>
							<div class="progress">
								<div class="progress-bar" role="progressbar">
									<span class="percentage">0</span>%
								</div>
							</div>
						</li>
					</ul>

					<div id="wr-install-sample-data-manually" class="hidden">
						<form enctype="multipart/form-data" method="post" target="wr-upload-sample-data" action="<?php
							echo esc_attr( admin_url( 'admin-ajax.php?action=nitro_install_sample_data&step=2&package=' . $package['id'] ) );
						?>">
							<ol>
								<li>
									<?php esc_html_e( 'Please download sample data package manually', 'wr-nitro' ); ?>
									<a href="<?php echo esc_url( $package['download'] ); ?>" class="button" target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'Download File', 'wr-nitro' ); ?>
									</a>
								</li>
								<li>
									<?php esc_html_e( 'Select sample data package from your computer', 'wr-nitro' ); ?>
									<input name="package" type="file" value="">
									<br />
									<span class="wr-status alert alert-danger hidden">
										<?php esc_html_e( 'Please select the downloaded sample data package.', 'wr-nitro' ); ?>
									</span>
								</li>
								<li>
									<button class="button" id="wr-upload-sample-data-package" type="button">
										<?php esc_html_e( 'Install', 'wr-nitro' ); ?>
									</button>
								</li>
							</ol>
						</form>
						<?php echo '<ifr' . 'ame src="about:blank" class="hidden" id="wr-upload-sample-data" name="wr-upload-sample-data"></ifra' . 'me>'; ?>
					</div>

					<div id="wr-install-sample-data-success-message" class="wr-success-message hidden">
						<h3>
							<?php esc_html_e( 'Sample data was successfully installed.', 'wr-nitro' ); ?>
						</h3>
						<p style="text-align: center;">
							<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary button-hero"><?php esc_html_e( 'Customize Your Site', 'wr-nitro' ); ?></a>
							<span style="display: block;margin-top:20px;"><?php esc_html_e( 'or, ', 'wr-nitro' ); ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=wr-intro' ) ); ?>"><?php esc_html_e( 'back to dashboard', 'wr-nitro' ); ?></a></span>
						</p>
					</div>

					<div id="wr-install-sample-data-failure-message" class="wr-failure-message hidden">
						<h3>
							<?php esc_html_e( 'Sample data was not successfully installed.', 'wr-nitro' ); ?>
						</h3>
					</div>
				</div>
				<?php
			break;

			case 'uninstall' :
				?>
				<div id="sample-data-installation-step-1">
					<div class="alert alert-warning">
						<span class="label label-danger"><?php esc_html_e( 'Important Notice', 'wr-nitro' ); ?></span>
						<ul>
							<li><?php esc_html_e( 'Uninstalling sample data will restore the original data backed up before the current sample data was installed.', 'wr-nitro' ); ?></li>
						</ul>
					</div>

					<div class="checkbox">
						<label>
							<input name="agree" value="1" id="confirm-sample-data-uninstallation" type="checkbox">
							<?php esc_html_e( 'I understand the impact of uninstalling sample data.', 'wr-nitro' ); ?>
						</label>
					</div>

					<div class="actions">
						<button class="button button-primary" type="button" id="go-to-sample-data-installation-step-2" disabled="disabled">
							<?php esc_html_e( 'Continue', 'wr-nitro' ); ?>
						</button>
						<button class="button" type="button" id="cancel-sample-data-installation">
							<?php esc_html_e( 'Cancel', 'wr-nitro' ); ?>
						</button>
					</div>
				</div>
				<?php
			break;
		}

		wp_send_json_success( ob_get_clean() );
	}

	/**
	 * Download the selected sample package.
	 *
	 * @param   array  $package  Sample package data.
	 *
	 * @return  void
	 */
	protected static function download_sample_package( $package ) {
		// Generate path to store downloaded sample data package.
		$path = wp_upload_dir();
		$path = "{$path['basedir']}/wr-nitro/sample-data/{$package['id']}";

		// Check if sample data package is uploaded.
		if ( 'POST' == wr_get_server_param( 'REQUEST_METHOD' ) && isset( $_FILES['package'] ) ) {
			// Check if there is any error occurred while uploading.
			if ( UPLOAD_ERR_OK != $_FILES['package']['error'] ) {
				switch ( $_FILES['package']['error'] ) {
					case UPLOAD_ERR_NO_FILE:
						wp_send_json_error( __( 'No file sent.', 'wr-nitro' ) );
					break;

					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						wp_send_json_error( __( 'Exceeded filesize limit.', 'wr-nitro' ) );
					break;

					default:
						wp_send_json_error( __( 'Unknown errors occurred while uploading.', 'wr-nitro' ) );
					break;
				}
			}

			// Move uploaded file to storage path.
			ob_start();

			if ( ! move_uploaded_file( $_FILES['package']['tmp_name'], "{$path}.zip" ) ) {
				$result = ob_get_contents();

				wp_send_json_error( $result ? $result : __( 'Failed to move uploaded file.', 'wr-nitro' ) );
			}

			ob_end_clean();

			// Sample package uploaded successfully.
			wp_send_json_success();
		}

		// Download sample page files.
		$error = array();
		$pages = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';

		if ( ! empty( $pages ) ) {
			foreach ( ( array ) $pages as $page ) {
				if ( array_key_exists( $page, $package['sample-page'] ) ) {
					$download = $package['sample-page'][ $page ]['download'];

					if ( ! preg_match( '#^(https?://|//)#', $download ) ) {
						$download = get_template_directory_uri() . "/{$download}";
					}

					if ( ! self::download( $download, "{$path}/single-page-demo/{$page}.xml" ) ) {
						$error[] = sprintf(
							__( 'Failed to download the sample page file &#39;%1$s&#39;. Please <a href="%2$s">click here to download</a> the sample page file, then use the WordPress Importer to import the XML file manually.', 'wr-nitro' ),
							$page,
							$download
						);
					}
				}
			}
		}

		// Download sample data package.
		if ( 'sample-data.zip' == $package['download'] ) {
			$package['download'] = get_template_directory_uri() . '/woorockets/includes/sample-data.zip';
		}

		if ( ! self::download( $package['download'], "{$path}.zip" ) ) {
			$error[] = sprintf(
				__( 'Failed to download the selected sample data package &#39;%s&#39;.', 'wr-nitro' ),
				$package['id']
			);
		}

		if ( count( $error ) ) {
			if ( count( $error ) == 1 ) {
				wp_send_json_error( implode( $error ) );
			} else {
				wp_send_json_error( '<ul><li>' . implode( '</li><li>', $error ) . '</li></ul>' );
			}
		}

		// Sample package downloaded successfully.
		wp_send_json_success();
	}

	/**
	 * Import sample data.
	 *
	 * @param   array  $package  Sample package data.
	 *
	 * @return  void
	 */
	protected static function import_sample_data( $package ) {
		// Get WordPress file system object.
		global $wp_filesystem;

		// Generate path to downloaded sample data package.
		$path = wp_upload_dir();
		$path = "{$path['basedir']}/wr-nitro/sample-data/{$package['id']}";

		/* If target directory exists, remove it.
		if ( is_dir($path) ) {
			if ( ! ( rmdir($path) || $wp_filesystem->rmdir($path, true) ) ) {
				// If target directory cannot be removed, rename it.
				$name = "{$path}-" . date('YmdHis');

				rename($path, $name) || $wp_filesystem->move($path, $name, true);
			}
		}*/

		// Extract sample data package.
		unzip_file("{$path}.zip", $path);

		if ( ! is_dir($path) ) {
			// Try another method.
			if ( class_exists('ZipArchive') ) {
				$zip = new ZipArchive;

				if ( $zip->open("{$path}.zip") ) {
					$zip->extractTo($path);
				}

				$zip->close();
			}

			if ( ! is_dir($path) ) {
				// Try again with 3rd-party library.
				if ( ! class_exists('UnZIP') ) {
					get_template_part('libraries/unzip.cls');
				}

				$zip = new UnZIP;

				$zip->extract("{$path}.zip");
			}
		}

		if ( ! is_dir($path) ) {
			self::send_response( false, __( 'Failed to extract downloaded package to file system.', 'wr-nitro' ) );
		}

		// Look for sample data declaration file.
		$xml = glob( "{$path}/*.xml" );

		if ( ! count( $xml ) ) {
			self::send_response( false, __( 'Invalid sample data package.', 'wr-nitro' ) );
		}

		// Parse sample data declaration file.
		$xml = simplexml_load_file( $xml[0] );

		if ( ! $xml ) {
			self::send_response( false, __( 'Unable to parse sample data declaration file.', 'wr-nitro' ) );
		}

		$theme_id = isset( $xml['id'] )
			? ( string ) $xml['id']
			: ( isset( $xml->product['id'] )
				? ( string ) $xml->product['id']
				: null );

		$name = isset( $xml['name'] )
			? ( string ) $xml['name']
			: ( isset( $xml->product['name'] )
				? ( string ) $xml->product['name']
				: null );

		$version = isset( $xml['version'] )
			? ( string ) $xml['version']
			: ( isset( $xml->product['version'] )
				? ( string ) $xml->product['version']
				: null );

		if ( empty( $theme_id ) ) {
			$theme_id = str_replace( ' ', '-', strtolower( $name ) );
		}

		// Get theme details.
		$theme = wp_get_theme();
		$child = null;

		if ( $theme->get_template() != $theme->get_stylesheet() ) {
			$child = current( array_slice( explode( '/', str_replace( '\\', '/', $theme->get_stylesheet_directory() ) ), -1 ) );
			$theme = $theme->parent();
		}

		// Verify sample data.
		$theme_dir = current( array_slice( explode( '/', str_replace( '\\', '/', $theme->get_template_directory() ) ), -1 ) );

		if ( $theme_dir != $theme_id ) {
			self::send_response( false,
				sprintf(
					__( 'The selected sample data package <strong>%1$s</strong> is not compatible with the <strong>%2$s</strong> theme.', 'wr-nitro' ),
					$name,
					$theme['Name']
				)
			);
		}

		if ( version_compare( $version, $theme['Version'], '>' ) ) {
			self::send_response( false,
				sprintf(
					__( 'The theme version <strong>%1$s</strong> you are using is outdated. You need to update to the latest version <strong>%2$s</strong> prior to install sample data.', 'wr-nitro' ),
					$theme['Version'],
					$version
				)
			);
		}

		// Look for dependencies in sample data declaration.
		$plugins = array();

		foreach ( $xml->xpath( "//product/extension[@name!='wordpress']" ) as $plugin ) {
			$plugin = ( array ) $plugin->attributes();
			$plugin = $plugin['@attributes'];

			// Prepare `required` attribute.
			$plugin['required'] = isset( $plugin['required'] ) ? intval( $plugin['required'] ) : 0;

			// Prepare `slug` attribute.
			if ( ! isset( $plugin['slug'] ) ) {
				$plugin['slug'] = strtolower( str_replace( ' ', '-', $plugin['name'] ) );
			}

			// Store plugin for later reference.
			if ( isset( $plugin['slug'] ) ) {
				$plugins[ $plugin['slug'] ] = $plugin;
			}
		}

		// Start importing sample data.
		$option = ( isset( $_REQUEST['option'] ) && 'undefined' != $_REQUEST['option'] )
			? $_REQUEST['option']
			: 'full';

		if ( 'full' == $option ) {
			self::import_full_demo_site( $package, $xml, $theme_id, $child );
		}

		elseif ( 'page' == $option ) {
			self::import_single_demo_page( $package, $xml );
		}

		else {
			self::send_response( false, __( 'Invalid parameters.', 'wr-nitro' ) );
		}

		// Clean up temporary data.
		unlink("{$path}.zip") || $wp_filesystem->delete("{$path}.zip");

		foreach ( glob( "{$path}/*.xml" ) as $xml ) {
			unlink($xml) || $wp_filesystem->delete($xml);
		}

		// Store installed sample data package's dependencies.
		set_transient( 'wr_nitro_dependencies', $plugins );

		// Set required plugins to response array.
		$response = array();

		foreach ( $plugins as $plugin ) {
			if ( $plugin['required'] ) {
				$response['required_plugins'][] = $plugin['slug'];
			}
		}

		// Set demo assets to response array.
		if ( isset( $demo_assets ) ) {
			set_transient( 'wr_nitro_demo_assets', $demo_assets );

			// Set demo assets to reponse.
			foreach ( array_keys( $demo_assets ) as $asset ) {
				$response[ 'demo_assets' ][] = current( array_slice( explode( '/', str_replace( '\\', '/', $asset ) ), -1 ) );
			}
		}

		// Generate nonce to refresh security nonce.
		$response[ 'refresh_nonce' ] = wp_create_nonce( 'nitro-refresh-nonce' );

		set_transient( 'nitro_refresh_nonce', $response[ 'refresh_nonce' ], HOUR_IN_SECONDS );

		self::send_response( true, $response );
	}

	/**
	 * Import full demo site.
	 *
	 * @param   array             $package   Sample package data.
	 * @param   SimpleXMLElement  $xml       Sample data declaration.
	 * @param   string            $theme     The name of the theme directory.
	 * @param   string            $child     The name of the child theme directory.
	 *
	 * @return  array  Array of demo assets to download.
	 */
	protected static function import_full_demo_site( $package, $xml, $theme, $child = null ) {
		// Get WordPress file system object.
		global $wp_filesystem;

		// Generate path to downloaded sample data package.
		$upload_dir = wp_upload_dir();
		$path       = "{$upload_dir['basedir']}/wr-nitro/sample-data/{$package['id']}";

		// Check if sample data includes any bundled folder.
		$folders = $xml->xpath( '//product/bundled-folders/folder' );
		$demo_folders = array();

		if ( $folders && count( $folders ) ) {
			// Move all bundled folders to appropriated location.
			foreach ( $folders as $folder ) {
				$src = "{$path}/{$folder}";

				if ( is_dir($src) || $wp_filesystem->is_dir($src) ) {
					$des = ABSPATH . $folder;

					if ( ! ( file_exists($des) || $wp_filesystem->exists($des) ) ) {
						// Store demo folder to remove later when uninstall sample data.
						$demo_folders[] = (string) $folder;
					}

					rename($src, $des) || $wp_filesystem->move($src, $des, true);
				}
			}
		}

		// Get current site URL.
		$siteurl = get_option( 'siteurl' );

		// Get current user.
		$current_user = wp_get_current_user();

		// Get WordPress database object and table prefix.
		global $wpdb, $table_prefix;

		// Get all tables in database.
		$existing_tables = $wpdb->get_results( 'SHOW TABLES;', ARRAY_N );

		foreach ( $existing_tables as & $existing_table ) {
			$existing_table = $existing_table[0];
		}

		// Disable error reporting.
		if ( function_exists( 'error_reporting' ) ) {
			error_reporting( 0 );
		}

		// Do not limit execution time.
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		// Backup current data.
		$backup_dir = "{$upload_dir['basedir']}/wr-nitro/sample-data/{$package['id']}/backups/" . date( 'YmdHis' );
		$num_file   = 1;
		$queries    = '';

		if ( ! self::prepare_directory( $backup_dir ) ) {
			self::send_response( false, __( 'Failed to create directory to store database backup file.', 'wr-nitro' ) );
		}

		foreach ( $xml->xpath( '//product/extension' ) as $plugin ) {
			// Get tables to backup data.
			$tables = $plugin->xpath( 'task[@name="dbbackup"]/parameters/parameter' );

			if ( $tables && count( $tables ) ) {
				foreach ( $tables as $table ) {
					// Set real table prefix.
					if ( '#__' == substr( $table, 0, 3 ) ) {
						$table = str_replace( '#__', $table_prefix, $table );
					} elseif ( 'wp_' == substr( $table, 0, 3 ) && 'wp_' != $table_prefix ) {
						$table = str_replace( 'wp_', $table_prefix, $table );
					} else {
						$table = $table_prefix . $table;
					}

					// Make sure table exists in database.
					if ( ! in_array( $table, $existing_tables ) ) {
						continue;
					}

					// Drop existing table first.
					$queries .= "DROP TABLE IF EXISTS `{$table}`;\n";

					// Get table creation schema.
					$results  = $wpdb->get_results( "SHOW CREATE TABLE `{$table}`;", ARRAY_A );
					$results  = str_replace( 'CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $results[0]['Create Table'] );
					$queries .= str_replace( "\n", '', $results ) . ";\n";

					// Get table data.
					$i = 0;

					do {
						$results = $wpdb->get_results( "SELECT * FROM `{$table}` WHERE 1 LIMIT {$i}, 500;", ARRAY_A );

						if ( $results ) {
							foreach ( $results as $result ) {
								// Generate column list.
								$keys = '(`' . implode( '`, `', array_keys( $result ) ) . '`)';

								// Generate value list.
								$values = array();

								foreach ( array_values( $result ) as $value ) {
									$values[] = str_replace(
										array( '\\', "\r", "\n", "'" ),
										array( '\\\\', '\\r', '\\n', "\\'" ),
										$value
									);
								}

								$values = "('" . implode( "', '", $values ) . "')";

								// Store insert query.
								$query = "INSERT INTO `{$table}` {$keys} VALUES {$values};\n";

								if ( strlen( $queries . $query ) > self::$max_backup_file_size ) {
									// Generate backup file path.
									$path = str_repeat( '0', 4 - strlen( ( string ) $num_file ) );
									$path = "{$backup_dir}/backup_{$path}{$num_file}.sql";

									// Write current data to file.
									call_user_func( 'file_' . 'put' . '_contents', $path, $queries) || $wp_filesystem->put_contents($path, $queries);

									// Reset data.
									$queries = '';

									// Increase file counter.
									$num_file++;
								}

								$queries .= $query;
							}

							$i += count( $results );
						}
					} while ( $results && count( $results ) );
				}
			}
		}

		// Finalize backup task.
		if ( $queries != '' ) {
			// Generate backup file path.
			if ( $num_file > 1 ) {
				$path = str_repeat( '0', 4 - strlen( ( string ) $num_file ) );
				$path = "{$backup_dir}/backup_{$path}{$num_file}.sql";
			} else {
				$path = "{$backup_dir}/backup.sql";
			}

			// Write data to file.
			call_user_func( 'file_' . 'put' . '_contents', $path, $queries) || $wp_filesystem->put_contents($path, $queries);
		}

		// Get current usage data collector setting.
		$usage_data_collector = get_option( 'nitro_usage_data_collector' );

		// Delete currently installed sample data package and its dependencies.
		delete_transient( 'wr_nitro_dependencies' );

		// Define options that values need to be preserved.
		$reserved_options = array(
			'home',
			'siteurl',

			'template',
			'stylesheet',

			'template_root',
			'stylesheet_root',

			'current_theme',
			'active_plugins',
			"{$table_prefix}user_roles",

			'db_version',
			'initial_db_version',
			'woocommerce_db_version',
		);

		// Execute sample data queries in transaction.
		$wpdb->query( 'START TRANSACTION;' );

		foreach ( $xml->xpath( '//product/extension' ) as $plugin ) {
			// Prepare sample data queries.
			$queries = $plugin->xpath( 'task[@name="dbinstall"]/parameters/parameter' );

			if ( $queries && count( $queries ) ) {
				foreach ( $queries as $query ) {
					// Get query attributes.
					$attrs = ( array ) $query->attributes();
					$attrs = isset( $attrs['@attributes'] ) ? $attrs['@attributes'] : array();

					// Convert SimpleXmlElement object to string.
					$query = ( string ) $query;

					// Get table name.
					$pattern = '/(DROP TABLE IF EXISTS|DROP TABLE|CREATE TABLE IF NOT EXISTS|CREATE TABLE|DELETE FROM|INSERT INTO)\s+`*([^`\s]+)`*/i';

					if ( ! preg_match( $pattern, $query, $match ) ) {
						continue;
					}

					$table = $match[2];

					// Set real table prefix.
					if ( '#__' == substr( $table, 0, 3 ) ) {
						$table = str_replace( '#__', $table_prefix, $table );
					} elseif ( 'wp_' == substr( $table, 0, 3 ) && 'wp_' != $table_prefix ) {
						$table = str_replace( 'wp_', $table_prefix, $table );
					} else {
						$table = $table_prefix . $table;
					}

					// Alter query with real table name.
					$query = str_replace( $match[0], "{$match[1]} `{$table}`", $query );

					// Do not import users and usermeta table.
					if (
						false !== strpos( $query, "{$table_prefix}users" )
						||
						false !== strpos( $query, "{$table_prefix}usermeta" )
					) {
						continue;
					}

					// Do not drop options table.
					if ( false !== strpos( $query, 'DROP TABLE' ) && false !== strpos( $query, "{$table_prefix}options" ) ) {
						continue;
					}

					// Make sure table is created only if not exist.
					elseif ( false !== strpos( $query, 'CREATE TABLE' ) ) {
						$query = str_replace( 'ENGINE=InnoDB', '', $query );

						if ( false === strpos( $query, 'IF NOT EXISTS' ) ) {
							$query = str_replace( 'CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $query );
						}

						// Update existing tables.
						if ( ! in_array( $table, $existing_tables ) ) {
							$existing_tables[] = $table;
						}
					}

					// Check if this is an insert query.
					elseif ( false !== strpos( $query, 'INSERT INTO' ) ) {
						// Make sure table exists in database.
						if ( ! in_array( $table, $existing_tables ) ) {
							continue;
						}

						// Prepare insert query for postmeta table.
						if ( false !== strpos( $query, "{$table_prefix}postmeta" ) ) {
							// Remove Google Analytics ID.
							if ( false != strpos( $query, "'wr_page_options'," ) ) {
								$pattern = '/s:19:\\\"google_analytics_id\\\";s:13:\\\"[a-zA-Z0-9\-]+\\\";/';
								$query   = preg_replace( $pattern, 's:19:\\"google_analytics_id\\";s:0:\\"\\";', $query );
							}
						}

						// Prepare insert query for options table.
						elseif ( false !== strpos( $query, "{$table_prefix}options" ) ) {
							// Do not import user roles.
							if ( false !== strpos( $query, 'user_roles' ) ) {
								continue;
							}

							// Remove Google Analytics ID.
							if ( false !== strpos( $query, "'theme_mods_{$theme}'" ) ) {
								$pattern = '/s:(\d+):\\\"google_analytics_id([a-z_]+)\\\";s:13:\\\"[a-z0-9\-]+\\\";/i';
								$query   = preg_replace( $pattern, 's:\\1:\\"google_analytics_id\\2\\";s:0:\\"\\";', $query );
							}

							// Parse query.
							@list( $columns, $value                 ) = explode( 'VALUES', $query, 2 );
							@list( $id, $option, $value, $auto_load ) = explode(   "', '", $value, 4 );

							// Do not override reserved options.
							if ( in_array( $option, $reserved_options ) ) {
								continue;
							}

							// Use REPLACE query instead of INSERT.
							$auto_load = trim( $auto_load, "');" );

							$query = "REPLACE INTO {$table_prefix}options (`option_name`, `option_value`, `autoload`) "
								. "VALUES ('{$option}', '{$value}', '{$auto_load}');";
						}
					}

					// Check if query contains site URL.
					$site_urls = array();
					$demo_urls = array();

					if ( isset( $attrs['has-site-url'] ) ) {
						$site_urls[ $attrs['has-site-url'] ] = $siteurl;
						$demo_urls[ $attrs['has-site-url'] ] = $xml['demo-site-url'];
					}

					if ( isset( $attrs['has-site-url-escaped'] ) ) {
						$site_urls[ $attrs['has-site-url-escaped'] ] = str_replace( '/', '\\/', $siteurl );
						$demo_urls[ $attrs['has-site-url-escaped'] ] = str_replace( '/', '\\/', $xml['demo-site-url'] );
					}

					if ( isset( $attrs['has-site-url-double-escaped'] ) ) {
						$site_urls[ $attrs['has-site-url-double-escaped'] ] = str_replace( '/', '\\\\/', $siteurl );
						$demo_urls[ $attrs['has-site-url-double-escaped'] ] = str_replace( '/', '\\\\/', $xml['demo-site-url'] );
					}

					if ( isset( $attrs['has-site-url-encoded'] ) ) {
						$site_urls[ $attrs['has-site-url-encoded'] ] = str_replace(
							array( ':', '/' ),
							array( '%3A', '%2F' ),
							$siteurl
						);

						$demo_urls[ $attrs['has-site-url-encoded'] ] = str_replace(
							array( ':', '/' ),
							array( '%3A', '%2F' ),
							$xml['demo-site-url']
						);
					}

					if ( count( $site_urls ) ) {
						// Check if query contains demo assets.
						$pattern = '#(' . implode( '|', array_keys( $site_urls ) ) . ')' . self::$demo_image_pattern . '#i';

						if ( preg_match_all( $pattern, $query, $matches, PREG_SET_ORDER ) ) {
							foreach ( $matches as $match ) {
								// Clean query string and \ character from the end of captured string.
								$match[0] = current( explode( '?', rtrim( $match[0], '\\' ), 2 ) );
								$match[4] = current( explode( '?', rtrim( $match[4], '\\' ), 2 ) );

								if ( 'remote' != self::$demo_images_storage ) {
									// Prepare original demo assets URL.
									$origin = str_replace( array_keys( $demo_urls ), array_values( $demo_urls ), $match[0] );
									$origin = str_replace( $match[2], '/', $origin );

									// Generate path to store demo asset.
									$path = $upload_dir['basedir'] . str_replace( $match[2], '/', $match[4] );

									// Do not download if asset already exists.
									if ( ! @is_file( $path ) ) {
										// If asset URL is a thumbnail, store thumbnail size.
										if ( preg_match( '/(-\d+x\d+)?\.[a-z0-9]{3,4}$/i', $match[4], $m ) ) {
											$origin = preg_replace( '/(-\d+x\d+)?(\.[a-z0-9]{3,4})$/i', '\\2', $origin );

											if ( ! isset( $demo_assets[ $origin ] ) ) {
												$demo_assets[ $origin ] = array();
											}

											if ( isset( $m[1] ) && ! in_array( $m[1], $demo_assets[ $origin ] ) ) {
												$demo_assets[ $origin ][] = $m[1];
											}
										}

										// Otherwise, store asset for download later.
										elseif ( ! isset( $demo_assets[ $origin ] ) ) {
											$demo_assets[ $origin ] = array();
										}
									}
								} else {
									// Replace asset URL in query with the real URL in demo site.
									$query = str_replace(
										$match[0],
										str_replace( array_keys( $demo_urls ), array_values( $demo_urls ), $match[0] ),
										$query
									);
								}
							}
						}

						// Replace site URL placeholder in query with the real site URL.
						$query = self::replace_query(
							array_keys( $site_urls ),
							array_values( $site_urls ),
							$query
						);
					}

					// Execute query.
					if ( trim($query, ';') != '' ) {
						$wpdb->query( $query );
					}
				}

				// Start output buffering to capture error message.
				ob_start();

				// Commit transaction.
				if ( false === $wpdb->query( 'COMMIT;' ) ) {
					$result = ob_get_contents();

					// Roll back transaction.
					$wpdb->query( 'ROLLBACK;' );

					if ( $wpdb->last_error || $result ) {
						self::send_response( false,
							sprintf(
								__( 'Importing sample data has encountered an error and cannot continue: %s', 'wr-nitro' ),
								$wpdb->last_error ? $wpdb->last_error : $result
							)
						);
					}
				}

				// Stop output buffering.
				ob_end_clean();
			}
		}

		// Make sure database is up-to-date with current WordPress version.
		if ( ! function_exists( 'wp_upgrade' ) ) {
			include ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		wp_upgrade();

		// Store installed sample data package.
		update_option( 'wr_nitro_sample_package', $package['id'] );

		// Store bundled folders to transient.
		if ( count( $demo_folders ) ) {
			set_transient( 'wr_nitro_demo_folders', $demo_folders );
		}

		// If active theme is a child theme, migrate theme mods.
		if ( ! empty( $child ) ) {
			// Get imported theme mods.
			$theme_mods = maybe_unserialize( $wpdb->get_var(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name = 'theme_mods_{$theme}';"
			) );

			// Update theme mods for child theme.
			update_option( 'theme_mods_' . $child, $theme_mods );
		}

		// Restore usage data collector setting.
		if ( false !== $usage_data_collector ) {
			update_option( 'nitro_usage_data_collector', $usage_data_collector );
		} else {
			delete_option( 'nitro_usage_data_collector' );
		}

		// Log user in again because user session has been lost after importing data.
		wp_set_auth_cookie( $current_user->ID, false, is_ssl() );

		return isset( $demo_assets ) ? $demo_assets : array();
	}

	/**
	 * Import single demo page.
	 *
	 * @param   array             $package  Sample package data.
	 * @param   SimpleXMLElement  $xml      Sample data declaration.
	 *
	 * @return  void
	 */
	protected static function import_single_demo_page( $package, $xml ) {
		// Get request data.
		$pages = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';

		if ( empty( $pages ) ) {
			wp_send_json_error( __( 'Invalid parameters.', 'wr-nitro' ) );
		}

		// Load Importer API.
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			if ( file_exists( ABSPATH . 'wp-admin/includes/class-wp-importer.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			} else {
				wp_send_json_error( __( 'Failed to load the class `WP_Importer` of WordPress.', 'wr-nitro' ) );
			}
		}

		if ( ! class_exists( 'WP_Import' ) ) {
			require_once get_template_directory() . '/libraries/plugins/wordpress-importer/wordpress-importer.php';
		}

		// Import selected sample pages.
		$error = array();

		foreach ( ( array ) $pages as $page ) {
			if ( ! array_key_exists( $page, $package['sample-page'] ) ) {
				$error[] = sprintf(
					__( 'The selected sample page &#39;%s&#39; is invalid.', 'wr-nitro' ),
					$page
				);
			}

			// Generate path to downloaded sample page file.
			$path = wp_upload_dir();
			$path = "{$path['basedir']}/wr-nitro/sample-data/{$package['id']}/single-page-demo/{$page}.xml";

			if ( ! is_file( $path ) ) {
				$error[] = sprintf(
					__( 'Failed to find the downloaded sample page file &#39;%s&#39;.', 'wr-nitro' ),
					$page
				);
			}

			// Try importing sample page file.
			try
			{
				// Start output buffering.
				ob_start();

				$importer = new WP_Import();

				$importer->fetch_attachments = true;

				$importer->import( $path );

				ob_end_clean();
			}
			catch (Exception $e)
			{
				$error[] = sprintf(
					__( 'Failed to import the sample page &#39;%1$s&#39; due to: %2$s', 'wr-nitro' ),
					$page,
					$e->getMessage()
				);
			}
		}

		if ( count( $error ) ) {
			if ( count( $error ) == 1 ) {
				wp_send_json_error( implode( $error ) );
			} else {
				wp_send_json_error( '<ul><li>' . implode( '</li><li>', $error ) . '</li></ul>' );
			}
		}
	}

	/**
	 * Download demo assets.
	 *
	 * @param   array  $package  Sample package data.
	 *
	 * @return  void
	 */
	protected static function download_demo_assets( $package ) {
		// Get asset index.
		$index = isset( $_GET['asset'] ) ? $_GET['asset'] : null;

		if ( is_null( $index ) ) {
			wp_send_json_error( __( 'Missing demo asset to be downloaded.', 'wr-nitro' ) );
		}

		// Load the list of demo assets to be downloaded.
		$demo_assets = get_transient( 'wr_nitro_demo_assets' );

		if ( ! $demo_assets ) {
			wp_send_json_error( __( 'Missing list of demo asset to be downloaded.', 'wr-nitro' ) );
		}

		$asset_links = array_keys( $demo_assets );

		if ( ! isset( $asset_links[ $index ] ) ) {
			wp_send_json_error( __( 'Missing demo asset to be downloaded.', 'wr-nitro' ) );
		}

		// Parse asset URL.
		$url = $asset_links[ $index ];

		if ( ! preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $url, $match ) ) {
			wp_send_json_error( __( 'Invalid demo asset URL.', 'wr-nitro' ) );
		}

		// Generate local asset path.
		$path = wp_upload_dir();
		$path = $path['basedir'] . $match[5];

		// Generate other thumbnails and intermediate sizes used in sample data.
		foreach ( $demo_assets[ $url ] as $s ) {
			// Generate download link and local path.
			$thumb_url  = preg_replace( '/(\.[a-z0-9]{3,4})$/i', "-{$s}\\1", $url  );
			$thumb_path = preg_replace( '/(\.[a-z0-9]{3,4})$/i', "-{$s}\\1", $path );

			if ( ! @is_file( $thumb_path ) ) {
				if ( 'placeholder' == self::$demo_images_storage ) {
					$thumb_url = str_replace( '/wp-content/', '/placeholder/wp-content/', $thumb_url );
				}

				self::download( $thumb_url, $thumb_path );
			}
		}

		// Download demo asset.
		if ( ! @is_file( $path ) ) {
			if ( 'placeholder' == self::$demo_images_storage ) {
				$url = str_replace( '/wp-content/', '/placeholder/wp-content/', $url );
			}

			self::download( $url, $path );

			if ( ! preg_match( '/-\d+x\d+\.[a-z0-9]{3,4}$/i', $match[5] ) ) {
				// Generate thumbnails and other intermediate sizes for downloaded demo asset.
				global $_wp_additional_image_sizes;

				$sizes = array();

				foreach ( get_intermediate_image_sizes() as $s ) {
					// Check if thumbnail already exists.
					$thumb_path = preg_replace( '/(\.[a-z0-9]{3,4})$/i', "-{$s}\\1", $path );

					if ( @is_file( $thumb_path ) ) {
						continue;
					}

					// Preset resize options.
					$sizes[ $s ] = array( 'width' => '', 'height' => '', 'crop' => false );

					if ( isset( $_wp_additional_image_sizes[ $s ]['width'] ) ) {
						// Theme-added sizes.
						$sizes[ $s ]['width'] = intval( $_wp_additional_image_sizes[ $s ]['width'] );
					} else {
						// Default sizes set in options.
						$sizes[ $s ]['width'] = get_option( "{$s}_size_w" );
					}

					if ( isset( $_wp_additional_image_sizes[ $s ]['height'] ) ) {
						// Theme-added sizes.
						$sizes[ $s ]['height'] = intval( $_wp_additional_image_sizes[ $s ]['height'] );
					} else {
						// Default sizes set in options.
						$sizes[ $s ]['height'] = get_option( "{$s}_size_h" );
					}

					if ( isset( $_wp_additional_image_sizes[ $s ]['crop'] ) ) {
						// Theme-added sizes.
						$sizes[ $s ]['crop'] = intval( $_wp_additional_image_sizes[ $s ]['crop'] );
					} else {
						// Default sizes set in options.
						$sizes[ $s ]['crop'] = get_option( "{$s}_crop" );
					}
				}

				// Do resize.
				$sizes = apply_filters( 'intermediate_image_sizes_advanced', $sizes );

				if ( $sizes ) {
					$editor = wp_get_image_editor( $path );

					if ( ! is_wp_error( $editor ) ) {
						$editor->multi_resize( $sizes );
					}
				}
			}
		}

		wp_send_json_success();
	}

	/**
	 * Restore the original data backed up before installing sample data.
	 *
	 * @param   array  $package  Sample package data.
	 *
	 * @return  void
	 */
	protected static function restore_backup_data( $package ) {
		// Get WordPress file system object.
		global $wp_filesystem;

		// Get all backup files.
		$upload_dir = wp_upload_dir();
		$backup_dir = "{$upload_dir['basedir']}/wr-nitro/sample-data/{$package['id']}";

		if ( is_dir( "{$backup_dir}/backups" ) ) {
			$backup = glob( "{$backup_dir}/backups/*" );
		}

		if ( ! isset( $backup ) || ! count( $backup ) ) {
			$backup = glob( "{$backup_dir}/backup_*.sql" );
		}

		if ( ! count( $backup ) ) {
			if ( 'main' == $package['id'] ) {
				$backup = glob( "{$upload_dir['basedir']}/backup_*.sql" );
			} else {
				$backup = glob( "{$upload_dir['basedir']}/{$package['id']}/backup_*.sql" );
			}
		}

		if ( count( $backup ) ) {
			// Get the latest backup.
			rsort( $backup );
			reset( $backup );

			$backup = current( $backup );

			if ( is_dir( $backup ) ) {
				$backup = glob( "{$backup}/*.sql" );
			}

			// Get current user.
			$current_user = wp_get_current_user();

			// Get current demo assets.
			$demo_assets = get_transient( 'wr_nitro_demo_assets' );

			// Get current demo folders.
			$demo_folders = get_transient( 'wr_nitro_demo_folders' );

			// Disable error reporting.
			if ( function_exists( 'error_reporting' ) ) {
				error_reporting( 0 );
			}

			// Do not limit execution time.
			if ( function_exists( 'set_time_limit' ) ) {
				set_time_limit( 0 );
			}

			// Get current usage data collector setting.
			$usage_data_collector = get_option( 'nitro_usage_data_collector' );

			// Start output buffering to capture error message.
			ob_start();

			// Execute backup queries in transaction.
			global $wpdb;

			// Import backup data.
			foreach ( ( array ) $backup as $file ) {
				// Read and execute queries from backup file.
				$wpdb->query( 'START TRANSACTION;' );

				foreach ( explode( ";\n", ( $buffer = call_user_func('file_' . 'get' . '_contents', $file) ) ? $buffer : $wp_filesystem->get_contents($file) ) as $query ) {
					if ( trim($query, ';') != '' ) {
						$wpdb->query( "{$query};" );
					}
				}

				// Commit transaction.
				if ( false === $wpdb->query( 'COMMIT;' ) ) {
					$result = ob_get_contents();

					// Roll back transaction.
					$wpdb->query( 'ROLLBACK;' );

					self::send_response( false,
						sprintf(
							__( 'Restoring backup has encountered an error and cannot continue: %s', 'wr-nitro' ),
							$wpdb->last_error ? $wpdb->last_error : $result
						)
					);
				}
			}

			// Stop output buffering.
			ob_end_clean();

			// Let WordPress handle database upgrade.
			if ( ! function_exists( 'wp_upgrade' ) ) {
				include_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			wp_upgrade();

			// Restore usage data collector setting.
			if ( false !== $usage_data_collector ) {
				update_option( 'nitro_usage_data_collector', $usage_data_collector );
			} else {
				delete_option( 'nitro_usage_data_collector' );
			}

			// Remove demo assets.
			if ( $demo_assets ) {
				/* Clear WordPress cache.
				wp_cache_delete( 'wr_nitro_demo_assets', 'transient' );
				wp_cache_delete( '_transient_wr_nitro_demo_assets', 'options' );

				// Get previous demo assets.
				$prev_demo_assets = get_transient( 'wr_nitro_demo_assets' );*/

				foreach ( $demo_assets as $url => $sizes ) {
					/* Do not delete asset used in previous demo assets.
					if ( $prev_demo_assets && array_key_exists( $url, $prev_demo_assets ) ) {
						continue;
					}*/

					// Parse asset URL.
					if ( ! preg_match( '#' . self::$demo_site_pattern . self::$demo_image_pattern . '#i', $url, $match ) ) {
						continue;
					}

					// Generate local asset path.
					$path = wp_upload_dir();
					$path = $path['basedir'] . $match[5];

					if ( is_file($path) || $wp_filesystem->is_file($path) ) {
						unlink($path) || $wp_filesystem->delete($path);
					}

					// Delete other thumbnails and intermediate sizes used in sample data.
					if ( $thumbs = glob( preg_replace( '/(\.[a-z0-9]{3,4})$/i', '*.*', $path ) ) ) {
						foreach ( $thumbs as $thumb ) {
							unlink($thumb) || $wp_filesystem->delete($thumb);
						}
					}
				}
			}

			// Remove demo folders.
			if ( $demo_folders ) {
				foreach ( $demo_folders as $folder ) {
					$folder = ABSPATH . $folder;

					if ( is_dir($folder) || $wp_filesystem->is_dir($folder) ) {
						$wp_filesystem->rmdir( $folder, true );
					}
				}
			}

			// Remove backup file.
			if ( is_array( $backup ) ) {
				$backup = dirname( current($backup) );

				$wp_filesystem->rmdir($backup, true);
			} else {
				unlink($backup) || $wp_filesystem->delete($backup);
			}

			// Return previously installed sample data package.
			wp_cache_delete( 'wr_nitro_sample_package', 'options' );

			// Log user in again because user session has been lost after restoring data.
			wp_set_auth_cookie( $current_user->ID, false, is_ssl() );

			// Generate nonce to refresh security nonce.
			$response[ 'refresh_nonce' ] = wp_create_nonce( 'nitro-refresh-nonce' );

			set_transient( 'nitro_refresh_nonce', $response[ 'refresh_nonce' ], 300 );

			// Get last installed sample package.
			$response[ 'last' ] = get_option( 'wr_nitro_sample_package' );

			self::send_response( true, $response );
		}

		self::send_response( false, __( 'Not found any backup to restore.', 'wr-nitro' ) );
	}

	/**
	 * Fetch a remote URI then return results.
	 *
	 * @param   string   $uri     Remote URI for fetching content.
	 * @param   string   $target  Local file path to store fetched content.
	 *
	 * @return  mixed
	 */
	protected static function download( $uri, $target = '' ) {
		// Allow downloading from *.woorockets.com server only.
		if ( ! preg_match( '#^https?://([^\.]+\.)?woorockets\.com/#', $uri ) ) {
			return false;
		}

		// Download remote content.
		$result = download_url( $uri );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		// Make sure downloaded file does not contain unwanted tags.
		if ( ! WR_Nitro::check_xss( $result ) ) {
			return false;
		}

		// Move downloaded file to target if specified.
		global $wp_filesystem;

		if ( ! empty( $target ) ) {
			// Prepare target directory.
			$path = implode( '/', array_slice( explode( '/', str_replace( '\\', '/', $target ) ), 0, -1 ) );

			if ( ! self::prepare_directory( $path ) ) {
				return false;
			}

			// Move file.
			if ( ! ( rename($result, $target) || $wp_filesystem->move($result, $target, true) ) ) {
				return false;
			}

			$content = ( $content = filesize($target) ) ? $content : $wp_filesystem->size($target);
		} else {
			$content = ( $content = call_user_func('file_' . 'get' . '_contents', $result) ) ? $content : $wp_filesystem->get_contents($result);

			// Remove downloaded file.
			unlink($result) || $wp_filesystem->delete($result);
		}

		return $content;
	}

	/**
	 * Prepare a directory.
	 *
	 * @param   string  $path  Directory path.
	 *
	 * @return  mixed
	 */
	protected static function prepare_directory( $path ) {
		global $wp_filesystem;

		if ( ! is_dir( $path ) ) {
			$results = explode( '/', str_replace( '\\', '/', $path ) );
			$path    = array();

			while ( count( $results ) ) {
				$path[] = current( $results );

				if ( ! is_dir( $currentDir = implode('/', $path) ) ) {
					wp_mkdir_p($currentDir) || call_user_func(array($wp_filesystem, 'mk' . 'dir'), $currentDir, 0755);
				}

				// Shift paths.
				array_shift( $results );
			}
		}

		// Re-build target directory.
		$path = is_array( $path ) ? implode( '/', $path ) : $path;

		if ( ! is_dir( $path ) ) {
			return false;
		}

		return $path;
	}

	/**
	 * Convert special characters in given string.
	 *
	 * @param   string  $string  String to convert.
	 *
	 * @return  string
	 */
	protected static function convert_special_chars( $string )
	{
		// Convert special quote characters to normal quotes
		$search  = array( chr( 145 ), chr( 146 ), chr( 147 ), chr( 148 ), chr( 151 ) );
		$replace = array( "'"       , "'"       , '"'       , '"'       , '-'        );

		$string = str_replace( $search, $replace, $string );

		// Remove all control and non-printable characters except new line, carriage return, tab and spacing
		return preg_replace( '/[^\x0A\x20-\x7E]/', '', $string );
	}

	/**
	 * Define function to recursively replace content in data.
	 *
	 * @param   mixed    $search                 Content to search for.
	 * @param   mixed    $replace                Content to replace with.
	 * @param   mixed    $data                   Data to replace content.
	 * @param   boolean  $convert_special_chars  Whether to convert special characters.
	 * @param   string   $ignore_pattern         Regular expression pattern to skip replacement.
	 *
	 * @return  string
	 */
	protected static function replace_recursive( $search, $replace, $data, $convert_special_chars = true, $igore_pattern = null ) {
		if ( is_string( $data ) ) {
			if ( $igore_pattern ) {
				$search  = is_array( $search  ) ? $search  : ( array ) $search;
				$replace = is_array( $replace ) ? $replace : ( array ) $replace;

				foreach ( $search as $k => $v ) {
					$parts = explode( $v, $data );
					$tmp   = $parts[0];

					for ( $i = 1, $n = count( $parts ); $i < $n; $i++ ) {
						if ( preg_match( $igore_pattern, $v . $parts[ $i ] ) ) {
							$tmp .= $v . $parts[ $i ];
						} else {
							$tmp .= $replace[ $k ] . $parts[ $i ];
						}
					}

					$data = $tmp;
				}
			} else {
				$data = str_replace( $search, $replace, $convert_special_chars ? self::convert_special_chars( $data ) : $data );
			}
		} elseif ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				$data[ $k ] = self::replace_recursive( $search, $replace, $v, $convert_special_chars, $igore_pattern );
			}
		}

		return $data;
	}

	/**
	 * Method to replace a string in a SQL query with another string.
	 *
	 * @param   mixed   $search          Content to search for.
	 * @param   mixed   $replace         Content to replace with.
	 * @param   mixed   $query           Query to replace content.
	 * @param   string  $ignore_pattern  Regular expression pattern to skip replacement.
	 *
	 * @return  string
	 */
	protected static function replace_query( $search, $replace, $query, $ignore_pattern = null ) {
		$query = self::replace_recursive( $search, $replace, $query, true, $ignore_pattern );

		// Check if query contains serialization string.
		if ( preg_match_all( '/\'(a:\d+:\{.+\})\'(\s*,|\s*\))/', $query, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				// Try to unserial the serialized string.
				$serial = $match[1];
				$data   = @unserialize( $serial );

				if ( ! $data ) {
					// Un-escape single-quote (') character if has.
					if ( false !== strpos( $serial, "\\'" ) ) {
						$serial = str_replace( "\\'", "'", $serial );
						$data   = @unserialize( $serial );
					}

					// Un-escape double-quote (") character if has.
					if ( ! $data && false !== strpos( $serial, '\\"' ) ) {
						$serial = str_replace( '\\"', '"', $serial );
						$data   = @unserialize( $serial );
					}

					// Replace '\r' string with the real return character.
					if ( ! $data && false !== strpos( $serial, '\\r' ) ) {
						$serial = str_replace( '\\r', "\r", $serial );
						$data   = @unserialize( $serial );
					}

					// Replace '\n' string with the real new line character.
					if ( ! $data && false !== strpos( $serial, '\\n' ) ) {
						$serial = str_replace( '\\n', "\n", $serial );
						$data   = @unserialize( $serial );
					}

					// Try to recount the serialization length.
					if ( ! $data ) {
						// Convert all special characters in serialized string with standard equivalents.
						$serial = preg_split( '/s:\d+:"/', self::convert_special_chars( $serial ) );

						foreach ( $serial as $k => $v ) {
							if ( $k > 0 ) {
								if ( preg_match( '/";\}*[a-zA-Z]:\d+/', $v, $m ) ) {
									$tmp = explode( $m[0], $v, 2 );

									$serial[ $k ] = 's:' . strlen( $tmp[0] ) . ':"' . $v;
								} else {
									$tmp = substr( $v, 0, strrpos( $v, '"' ) );

									$serial[ $k ] = 's:' . strlen( $tmp ) . ':"' . $v;
								}
							}
						}

						$data = @unserialize( implode( $serial ) );
					}
				}

				if ( $data ) {
					// Re-serialize the unserialized variable.
					$data = serialize( $data );

					// Escape the single quote (') character.
					$data = str_replace( "'", "\\'", str_replace( "\\'", "'", $data ) );

					// Escape the slash (\) character.
					if ( false !== strpos( $data, '\\\\' ) ) {
						$data = str_replace( '\\\\', '\\\\\\\\', $data );
					}

					// Update the query.
					$query = str_replace( $match[1], $data, $query );
				}
			}
		}

		// Check if value is JSON-encoded string?
		elseif ( preg_match_all( '/\'(\{\\\*"[^"]+\\\*":.+\})\'(\s*,|\s*\))/', $query, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				// Try to decode JSON string.
				$json = $match[1];
				$data = @json_decode( $json, true );

				if ( ! $data ) {
					// Try to escape double-quote character.
					$json = str_replace( '\\"', '"', $json );
					$data = @json_decode( $json, true );

					if ( ! $data ) {
						// Try to escape double-quote character in value.
						$data = explode( ':"', $json );

						foreach ( $data as $k => $v ) {
							if ( $k > 0 && preg_match( '/"(,|\})/', $v, $m ) ) {
								$tmp = explode( $m[0], $v, 2 );

								$data[ $k ] = str_replace( '"', '\\"', preg_replace( '/\\+"/', '"', $tmp[0] ) ) . $m[0] . $tmp[1];
							}
						}

						$data = @json_decode( implode( ':"', $data ), true );
					}
				}

				if ( $data ) {
					// Re-encode the decoded variable.
					$data = json_encode( $data );

					// Replace the double quote (") character.
					if ( false !== strpos( $data, '\\"' ) ) {
						$data = str_replace( '\\"', "\\'", preg_replace( '#\\+"#', '\\"', $data ) );
					}

					// Update the query.
					$query = str_replace( $match[1], $data, $query );
				}
			}
		}

		return $query;
	}

	/**
	 * Send JSON response without 'Content-Type: application/json' header.
	 *
	 * @param   boolean  $success  Success status.
	 * @param   mixed    $data     Response data.
	 *
	 * @return  void
	 */
	protected static function send_response( $success, $data ) {
		echo json_encode( array(
			'success' => $success,
			'data'    => $data
		) );

		exit;
	}
}
