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
 * Pluggable initialization class.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Welcome {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Initialize welcome functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Add action to enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// Register Ajax action.
		add_action( 'wp_ajax_nitro_refresh_nonce'       , array( __CLASS__, 'refresh_nonce' ) );
		add_action( 'wp_ajax_nopriv_nitro_refresh_nonce', array( __CLASS__, 'refresh_nonce' ) );
	}

	/**
	 * Render custom style.
	 *
	 * @return  void
	 */
	public static function enqueue_scripts() {
		global $pagenow;

		if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'wr-intro' ) {
			// Load ThickBox.
			add_thickbox();

			// Enqueue assets for Nitro welcome page.
			wp_enqueue_script( 'magnific-popup'  , get_template_directory_uri() . '/assets/3rd-party/magnific-popup/jquery-magnific-popup.min.js', array(), false, true );
			wp_enqueue_style( 'wr-nitro-admin', get_template_directory_uri() . '/assets/woorockets/css/admin/admin.css' );
			wp_enqueue_script( 'wr-nitro-admin', get_template_directory_uri() . '/assets/woorockets/js/admin/admin.js', array( 'jquery' ), false, true );

			// Localize text translation.
			wp_localize_script( 'wr-nitro-admin', 'wr_nitro_admin', array(
				'install_plugin_url'       => admin_url( 'admin-ajax.php?action=nitro_install_plugin' ),
				'install_plugin_nonce'     => wp_create_nonce( 'nitro-install-plugin' ),
				'uninstall_plugin_url'     => admin_url( 'admin-ajax.php?action=nitro_uninstall_plugin' ),
				'uninstall_plugin_nonce'   => wp_create_nonce( 'nitro-uninstall-plugin' ),
				'install_sample_url'       => admin_url( 'admin-ajax.php?action=nitro_install_sample_data' ),
				'install_sample_nonce'     => wp_create_nonce( 'nitro-install-sample' ),
				'uninstall_sample_url'     => admin_url( 'admin-ajax.php?action=nitro_uninstall_sample_data' ),
				'uninstall_sample_nonce'   => wp_create_nonce( 'nitro-uninstall-sample' ),
				'refresh_nonce_url'        => admin_url( 'admin-ajax.php?action=nitro_refresh_nonce' ),
				'install'                  => __( 'Install', 'wr-nitro' ),
				'uninstall'                => __( 'Uninstall', 'wr-nitro' ),
				'confirm_install_plugin'   => __( 'Install %PLUGIN% plugin?', 'wr-nitro' ),
				'confirm_uninstall_plugin' => __( 'Uninstall %PLUGIN% plugin?', 'wr-nitro' ),
				'confirm_uninstall_sample' => __( 'Uninstalling sample data will remove all sample content and revert database to the original state. All installed plugins will remain in the system, but deactivated. Are you sure you want to proceed?', 'wr-nitro' ),
				'unknown_error'            => __( 'An unknown error was occurred. Please try again later.', 'wr-nitro' ),
				'close_prevented'          => __( 'Closing sample data installation modal might damage your site. Please be patient!', 'wr-nitro' ),
				'install_plugin_failed'    => __( 'Failed to install the plugin %s. This might due to corrupted internet connection.', 'wr-nitro' ),
				'session_expired'          => __( 'Your working session has been expired.', 'wr-nitro' ),
				'login_link'               => wp_login_url( '', true ),
				'login_again'              => __( 'Click here to login again.', 'wr-nitro' ),
			) );
		}
	}

	/**
	 * Refresh security nonce for installing / uninstalling plugins / sample data.
	 *
	 * @return  void
	 */
	public static function refresh_nonce() {
		// Verify nonce.
		if ( ! isset( $_REQUEST['nonce'] ) || $_REQUEST['nonce'] != get_transient( 'nitro_refresh_nonce' ) ) {
			wp_send_json_error( __( 'Nonce verification failed.', 'wr-nitro' ) );
		}

		wp_send_json_success( array(
			'install_plugin_nonce'   => wp_create_nonce( 'nitro-install-plugin'   ),
			'uninstall_plugin_nonce' => wp_create_nonce( 'nitro-uninstall-plugin' ),
			'install_sample_nonce'   => wp_create_nonce( 'nitro-install-sample'   ),
			'uninstall_sample_nonce' => wp_create_nonce( 'nitro-uninstall-sample' ),
		) );
	}

	/**
	 * Render HTML of intro tab.
	 *
	 * @return  string
	 */
	public static function html() {
		$subscribe  = 'http://bit.ly/nitro-theme-subscribe';
		$twitter    = 'http://twitter.com/woorockets';
		$dribbble   = 'https://dribbble.com/woorockets-team';
		$facebook   = 'https://www.facebook.com/woorockets';
		$support    = 'http://nitro.woorockets.com/support';
		$docs       = 'http://nitro.woorockets.com/docs';
		$changelog  = 'http://nitro.woorockets.com/changelog';
		$registered = WR_Nitro_Update::get_download_token();
		?>
		<div class="wrap nitro-wrap">
			<h1 class="intro-title">
				<?php esc_html_e( 'Nitro', 'wr-nitro' ); ?>
				<a target="_blank" rel="noopener noreferrer" title="Click to see changelog" href="<?php echo esc_url( $changelog ); ?>">
					<sup><?php echo WR_Nitro::VERSION; ?></sup>
				</a>
			</h1>

			<div class="welcome-panel">
				<div class="welcome-panel-content">
					<h2><?php esc_html_e( 'Welcome to Nitro!', 'wr-nitro' ); ?></h2>
					<p class="about-description"><?php esc_html_e( 'We\'ve assembled some links to get you started', 'wr-nitro' ); ?></p>
					<div class="welcome-panel-column-container">
						<div class="welcome-panel-column">
							<h3><?php esc_html_e( 'Get Started', 'wr-nitro' ); ?></h3>
							<a href="#demos" target="_blank" rel="noopener noreferrer" class="wr-scroll-animated button button-primary button-hero trigger-tab"><?php esc_html_e( 'Install Sample Data', 'wr-nitro' ); ?></a>
							<p class="small-text"><?php esc_html_e( 'or', 'wr-nitro' ); ?>, <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize your site', 'wr-nitro' ); ?></a></p>
						</div>
						<div class="welcome-panel-column">
							<h3><?php esc_html_e( 'Next Steps', 'wr-nitro' ); ?></h3>
							<ul>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( admin_url( 'edit.php?post_type=header_builder' ) ); ?>" class="welcome-icon welcome-widgets-menus"><?php esc_html_e( 'View Header Builder', 'wr-nitro' ); ?></a></li>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $docs ); ?>" class="welcome-icon dashicons-media-document"><?php esc_html_e( 'Read Documentation', 'wr-nitro' ); ?></a></li>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $support ); ?>" class="welcome-icon dashicons-editor-help"><?php esc_html_e( 'Request Support', 'wr-nitro' ); ?></a></li>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $changelog ); ?>" class="welcome-icon dashicons-backup"><?php esc_html_e( 'View Changelog Details', 'wr-nitro' ); ?></a></li>
							</ul>
						</div>
						<div class="welcome-panel-column">
							<h3><?php esc_html_e( 'Keep in Touch', 'wr-nitro' ); ?></h3>
							<ul>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $subscribe ); ?>" class="welcome-icon dashicons-email-alt"><?php esc_html_e( 'Newsletter', 'wr-nitro' ); ?></a></li>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $twitter ); ?>" class="welcome-icon dashicons-twitter"><?php esc_html_e( 'Twitter', 'wr-nitro' ); ?></a></li>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $dribbble ); ?>" class="welcome-icon dashicons-dribbble"><?php esc_html_e( 'Dribbble', 'wr-nitro' ); ?></a></li>
								<li><a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $facebook ); ?>" class="welcome-icon dashicons-facebook"><?php esc_html_e( 'Facebook', 'wr-nitro' ); ?></a></li>
							</ul>
						</div>
					</div>
				</div><!-- .welcome-panel-content -->
			</div><!-- .welcome-panel -->
			<div id="tabs-container" role="tabpanel">
				<h2 class="nav-tab-wrapper">
					<?php if ( $registered ) : ?>
					<a class="nav-tab nitro-nav" href="#demos"><?php esc_html_e( 'Sample Data', 'wr-nitro' ); ?></a>
					<a class="nav-tab nitro-nav" href="#plugins"><?php esc_html_e( 'Plugins', 'wr-nitro' ); ?></a>
					<a class="nav-tab nitro-nav" href="#registration"><?php esc_html_e( 'Product Registration', 'wr-nitro' ); ?></a>
					<a class="nav-tab nitro-nav" href="#support"><?php esc_html_e( 'Support', 'wr-nitro' ); ?></a>
					<?php else : ?>
					<a class="nav-tab nitro-nav" href="#registration"><?php esc_html_e( 'Product Registration', 'wr-nitro' ); ?></a>
					<a class="nav-tab nav-disabled" data-title="<?php esc_html_e( 'In order to install plugins and sample data, you need to validate Nitro purchase code', 'wr-nitro' ); ?>" href="#"><?php esc_html_e( 'Sample Data', 'wr-nitro' ); ?></a>
					<a class="nav-tab nav-disabled" data-title="<?php esc_html_e( 'In order to install plugins and sample data, you need to validate Nitro purchase code', 'wr-nitro' ); ?>" href="#"><?php esc_html_e( 'Plugins', 'wr-nitro' ); ?></a>
					<a class="nav-tab nitro-nav" href="#support"><?php esc_html_e( 'Support', 'wr-nitro' ); ?></a>
					<?php endif; ?>
				</h2>
				<div class="tab-content">
					<?php
					if ( $registered ) {
						self::demos_html( $registered );
						self::plugins_html( $registered );
						self::registration_html();
						self::support_html();
					} else {
						self::registration_html();
						self::demos_html( $registered );
						self::plugins_html( $registered );
						self::support_html();
					}
					?>
				</div><!-- .tab-content -->
			</div>
		</div><!-- .nitro-wrap -->
		<?php
	}

	/**
	 * Render HTML of sample data tab.
	 *
	 * @param   mixed  $registered  Whether Nitro is registered or not?
	 *
	 * @return  string
	 */
	protected static function demos_html( $registered ) {
		// Get all sample data packages.
		$packages = WR_Nitro_Sample_Data::get_sample_packages();
		$current  = get_option( 'wr_nitro_sample_package' );

		if ( $current === '' ) {
			$current = 'main';
		}
		?>
		<div id="demos" class="tab-pane" role="tabpanel">
			<?php
			// Start output buffering to temporary hold outputed content for counting tags.
			ob_start();

			if ( @count( $packages ) ) :

			foreach ( $packages as $package ) :

			if ( ! $package['main'] ) :

			$package['tags'] = isset( $package['tags'] ) ? ( array ) $package['tags'] : array();

			foreach ( $package['tags'] as $tag ) :

				$tags[ $tag ] = ( isset( $tags ) && isset( $tags[ $tag ] ) ) ? ( $tags[ $tag ] + 1 ) : 1;
				$total_count  = isset( $total_count ) ? ( $total_count + 1 ) : 1;

			endforeach;
			?>
			<div class="col demo-<?php echo esc_attr( str_replace( ' & ', '-', implode( ',', $package['tags'] ) ) ); ?>">
				<div class="box <?php
					if ( in_array( 'hot', $package['tags'] ) )
						echo 'badge hot';
					elseif ( in_array( 'new', $package['tags'] ) )
						echo 'badge new';
				?>">
					<?php if ( isset( $package['thumbail'] ) && ! empty( $package['thumbail'] ) ) : ?>
					<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $package['demo'] ); ?>"><img src="<?php echo esc_url( $package['thumbail'] ); ?>"></a>
					<?php endif; ?>
					<div class="box-info">
						<h5><?php echo esc_html( $package['name'] ); ?></h5>
						<div>
							<?php if ( isset( $package['id'] ) ) : ?>
								<a href="#TB_inline?inlineId=" class="button button-primary install-sample<?php
									if ( $current === $package['id'] )
										echo ' hidden';
									elseif ( ! $registered )
										echo ' disabled';
								?>" data-package="<?php
									echo esc_attr( $package['id'] );
								?>" title="<?php
									esc_attr_e( 'Install sample data', 'wr-nitro' );
								?>" data-width="50%" data-height="345"<?php
									if ( $registered )
										echo ' target="thickbox"';
									else
										echo ' disabled="disabled"'
 								?>>
									<?php esc_html_e( 'Install', 'wr-nitro' ); ?>
								</a>
								<a href="#TB_inline?inlineId=" class="button button-primary uninstall-sample <?php
									if ( $current !== $package['id'] )
										echo 'hidden';
								?>" data-package="<?php
									echo esc_attr( $package['id'] );
								?>" title="<?php
									esc_attr_e( 'Uninstall sample data', 'wr-nitro' );
								?>" target="thickbox" data-width="50%" data-height="265">
									<?php esc_html_e( 'Uninstall', 'wr-nitro' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
			endif;

			endforeach;

			else :
			?>
			<div class="error"><p><?php
				_e( 'Failed to get available sample data packages.', 'wr-nitro' );
			?></p></div>
			<?php
			endif;

			// Get outputed content then stop output buffering.
			$html = ob_get_contents();

			ob_end_clean();
			?>
			<div class="welcome-panel panel-small welcome-main">
				<h3><?php esc_html_e( 'We have prepared plenty of sample data for you to start', 'wr-nitro' ); ?></h3>
				<ul>
					<li><?php esc_html_e( 'All sample data are similar to real shops, so you can use them right out of the box.', 'wr-nitro' ); ?></li>
					<li><?php esc_html_e( 'The original website data will be backed up and can be restored at anytime.', 'wr-nitro' ); ?></li>
					<li><?php esc_html_e( 'You can install sample data multiple times to choose the one you like without trashing database.', 'wr-nitro' ); ?></li>
				</ul>
			</div>

			<h3><?php esc_html_e( 'Explore the power behind Nitro', 'wr-nitro' ); ?></h3>
			<div class="main-demo">
				<?php foreach ( $packages as $package ) { ?>
					<?php if ( $package['main'] ) { ?>
						<div>
							<img src="<?php echo esc_url( $package['thumbail'] ); ?>">
						</div>
						<div class="main-demo-content">
							<h2><?php echo esc_attr( $package['name'] ); ?></h2>
							<p><?php esc_html_e( 'The main demo is packed with all design layouts (home, product category, product detail, as well as other eCommerce pages) and element examples. Moreover, it is an overview of how all eCommerce features and customization options work on your live shop.', 'wr-nitro' ); ?></p>
							<div class="btn-install-demo">
								<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $package['demo'] ); ?>" class="button button-preview"><?php esc_html_e( 'Preview', 'wr-nitro' ); ?></a>

								<?php if ( isset( $package['id'] ) ) : ?>
									<a href="#TB_inline?inlineId=" class="button button-primary install-sample<?php
										if ( $current === $package['id'] )
											echo ' hidden';
										elseif ( ! $registered )
											echo ' disabled';
									?>" data-package="<?php
										echo esc_attr( $package['id'] );
									?>" title="<?php
										esc_attr_e( 'Install sample data', 'wr-nitro' );
									?>" data-width="50%" data-height="345"<?php
										if ( $registered )
											echo ' target="thickbox"';
										else
											echo ' disabled="disabled"'
	 								?>>
										<?php esc_html_e( 'Install', 'wr-nitro' ); ?>
									</a>
									<a href="#TB_inline?inlineId=" class="button button-primary uninstall-sample <?php
										if ( $current !== $package['id'] )
											echo 'hidden';
									?>" data-package="<?php
										echo esc_attr( $package['id'] );
									?>" title="<?php
										esc_attr_e( 'Uninstall sample data', 'wr-nitro' );
									?>" target="thickbox" data-width="50%" data-height="265">
										<?php esc_html_e( 'Uninstall', 'wr-nitro' ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>

			<h3><?php esc_html_e( 'Get started with pre-made samples', 'wr-nitro' ); ?></h3>
			<!--<div class="filter-by-tag clear">
				<a class="current" data-tag="*" href="#">
					<?php //_e( 'All', 'wr-nitro' ); ?>
					<span class="item-count"><?php //echo (int) $total_count; ?></span>
				</a>
				<?php //foreach ( $tags as $tag => $total_count ) : ?>
				<a data-tag=".demo-<?php //echo esc_attr( str_replace( ' & ', '-', $tag ) ); ?>" href="#">
					<?php //echo esc_attr( ucfirst( $tag ) ); ?>
					<span class="item-count"><?php //echo (int) $total_count; ?></span>
				</a>
				<?php //endforeach; ?>
			</div>-->
			<div class="box-wrap demos three-col">
				<?php echo '' . $html; ?>
			</div>
			<?php echo '<ifr' . 'ame'; ?> id="nitro-manipulate-sample-data" name="nitro-manipulate-sample-data" src="about:blank" class="hidden"></iframe>

			<!--<br />
			<hr />-->

			<!--<h3><?php esc_html_e( 'You also can import pre-made single page template...', 'wr-nitro' ); ?></h3>-->
			<?php
			// Add filter to register all available page templates.
			// add_filter( 'wr-content-migration-presets', array( __CLASS__, 'register_page_templates' ) );

			// // Print the list of page templates available to import.
			// do_action( 'wr_content_migration_render_form', 'full' );
			?>
		</div>
		<?php
	}

	/**
	 * Render HTML of plugins tab.
	 *
	 * @param   mixed  $registered  Whether Nitro is registered or not?
	 *
	 * @return  string
	 */
	protected static function plugins_html( $registered ) {
		// Get TGM Plugin Activation instance.
		$tgmpa = TGM_Plugin_Activation::get_instance();
		?>
		<div id="plugins" class="tab-pane" role="tabpanel">
			<?php if ( ! $tgmpa->is_tgmpa_complete() ) : ?>
			<div class="welcome-panel panel-small">
				<p><?php echo wp_kses_post( 'Below you can see the list of plugins custom made especially for <b>Nitro</b> theme and some from 3rd party developers. Please be mind that plugins with "<b>required</b>" badge are essential to make Nitro work smoothly. By default when the new version of plugins is released we update the Nitro theme too.', 'wr-nitro' ); ?></p>
				<?php
					if ( $registered ) {
						echo sprintf( '<a target="_blank" rel="noopener noreferrer" class="button button-primary install-all-plugin" href="%s">' . __( 'Install All Recommended Plugins', 'wr-nitro' ) . '</a>', admin_url( 'admin.php?page=tgmpa-install-plugins&plugin_status=install' ) );
					}
				?>
			</div>
			<?php endif; ?>

			<div class="box-wrap plugins three-col">
				<?php foreach ( WR_Nitro_Pluggable::$plugins as $slug => $plugin ) : ?>
				<div class="col">
					<div class="box <?php if ( isset( $plugin['required'] ) && $plugin['required'] ) echo 'badge required'; ?>">
						<?php if ( isset( $plugin['thumb'] ) && ! empty( $plugin['thumb'] ) ) : ?>
							<?php if ( isset( $plugin['link'] ) && ! empty( $plugin['link'] ) ) : echo '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $plugin['link'] ) . '">'; endif; ?>
								<img src="<?php echo esc_url( $plugin['thumb'] ); ?>">
							<?php if ( isset( $plugin['link'] ) && ! empty( $plugin['link'] ) ) : echo '</a>'; endif; ?>
						<?php endif; ?>
						<div class="box-info">
							<h5>
								<span title="<?php echo esc_attr( $plugin['name'] ); ?>">
									<?php
									echo esc_attr( $plugin['name'] );

									if ( isset( $plugin['version'] ) && ! empty( $plugin['version'] ) ) :
									?>
								</span>
								<sup class="version"><?php echo esc_attr( $plugin['version'] ); ?></sup>
								<?php endif; ?>
							</h5>
							<?php if ( WR_Nitro_Pluggable::plugin_activated( $slug ) ) : ?>
							<a href="#" class="button button-primary uninstall-plugin" data-plugin="<?php
								echo esc_attr( $slug );
							?>">
								<?php esc_html_e( 'Uninstall', 'wr-nitro' ); ?>
							</a>
							<?php else : ?>
							<a href="#" class="button button-primary install-plugin<?php
								if ( ! $registered && $plugin['source_type'] != 'repo' )
									echo ' disabled';
							?>" data-plugin="<?php
								echo esc_attr( $slug );
							?>"<?php
								if ( ! $registered && $plugin['source_type'] != 'repo' )
									echo ' disabled="disabled"'
							?>>
								<?php esc_html_e( 'Install', 'wr-nitro' ); ?>
							</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div><!-- #plugins -->
		<?php
	}

	/**
	 * Render HTML of registration tab.
	 *
	 * @return  string
	 */
	protected static function registration_html() {
		// Get customer info.
		$nitro_customer = wp_parse_args(
			get_option( 'nitro_customer' ),
			array(
				'name'          => 'nullmaster',
				'email'         => 'nullmaster@babiato.org',
				'purchase_code' => 'nullmasterinbabiato',
			)
		);
		?>
		<div id="registration" class="tab-pane" role="tabpanel">
			<?php 
			$_GET['error'] = null;
			
			
			if ( isset( $_GET['error'] ) ) : ?>
			<div class="notice notice-error">
				<p><?php echo urldecode( $_GET['error'] ); ?></p>
			</div>
			<?php elseif ( isset( $_GET['message'] ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo urldecode( $_GET['message'] ); ?></p>
			</div>
			<?php endif; ?>
			<div class="welcome-panel panel-small envato-validate">
				<p><?php echo sprintf( __( '<b>In order to install plugins and sample data</b>, you need to validate Nitro purchase code and validate Nitro today for premium support, latest updates, eCommerce guides, and much more. %s', 'wr-nitro' ), '<a class="open-purchase-code" href="http://www.woorockets.com/wp-content/plugins/cloudwork-verifi/media/images/purchasecode.jpg" target="_blank" rel="noopener noreferrer">How to get envato purchase code.</a>' ); ?></p>
				<form id="wr_registration_form" method="post" action="options.php">
					<?php settings_fields( 'nitro_automatic_update' ); ?>
					<input class="required-field" id="nitro_customer_name" type="text" name="nitro_customer[name]" value="<?php
						echo esc_attr( $nitro_customer['name'] );
					?>" placeholder="<?php
						echo esc_attr( 'Customer Name', 'wr-nitro' );
					?>">
					<input class="required-field" id="nitro_customer_email" type="text" name="nitro_customer[email]" value="<?php
						echo esc_attr( $nitro_customer['email'] );
					?>" placeholder="<?php
						echo esc_attr( 'Customer Email *', 'wr-nitro' );
					?>">
					<input class="required-field last" id="nitro_customer_purchase_code" type="text" name="nitro_customer[purchase_code]" <?php //echo '' . ( $nitro_customer['purchase_code'] ? 'disabled' : '' ); ?> value="<?php
						echo esc_attr( $nitro_customer['purchase_code'] );
					?>" placeholder="<?php
						echo esc_attr( 'Purchase Code *', 'wr-nitro' );
					?>">
					<div class="clear"></div>
					<input id="nitro_customer_timezone" type="hidden" name="nitro_customer[timezone]" value="">
					<button id="nitro_customer_submit" class="button button-primary button-hero" disabled="disabled">
						<?php esc_html_e( 'Validate', 'wr-nitro' ); ?>
					</button>
				</form>
				<script type="text/javascript">
					(function($) {
						// Get customer timezone.
						$('#nitro_customer_timezone').val((new Date()).getTimezoneOffset());

						// Setup registration form.
						$('#wr_registration_form').submit(function(event) {
							// Validate email.
							$('#nitro_customer_email').trigger('keyup');

							if ($('#nitro_customer_submit').attr('disabled')) {
								event.preventDefault();

								return false;
							}
						}).on('keyup', 'input', function() {
							var email = $('#nitro_customer_email').val();

							if (email != '' && email.length > 2 && email.indexOf('@') > 0) {
								// Validate email.
								var parts = email.split('@');

								if ( parts.length == 2) {
									// Validate local part.
									if (parts[0].match(/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/)) {
										// Validate domain part.
										if (
											parts[1].indexOf('.') > 0
											&&
											!parts[1].match(/^[\s\t\r\n\0\x0B\.]+/)
											&&
											!parts[1].match(/[\s\t\r\n\0\x0B\.]+$/)
										) {
											// Validate subs.
											var subs = parts[1].split('.');

											if (subs.length > 0) {
												var valid = true;

												for (var i = 0; i < subs.length; i++) {
													if (!subs[i].match(/^[a-z0-9\-]+$/)) {
														valid = false;

														break;
													}
												}

												if (valid && $('#nitro_customer_purchase_code').val() != '') {
													return $('#nitro_customer_submit').removeAttr('disabled');
												}
											}
										}
									}
								}
							}

							$('#nitro_customer_submit').attr('disabled', 'disabled');
						});
					})(jQuery);
				</script>
			</div>
		</div>
		<?php
	}

	/**
	 * Render HTML of support tab.
	 *
	 * @return  string
	 */
	protected static function support_html() {
		?>
		<div id="support" class="tab-pane" role="tabpanel">
			<div class="three-col">
				<div class="col">
					<h3><?php esc_html_e( 'Documentation', 'wr-nitro' ); ?></h3>
					<p><?php esc_html_e( 'Here is our user guide for Nitro, including basic setup steps, as well as Nitro features and elements for your reference.', 'wr-nitro' ); ?></p>
					<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/docs" class="button button-primary"><?php esc_html_e( 'Read Documentation', 'wr-nitro' ); ?></a>
				</div>
				<div class="col closed">
					<h3><?php esc_html_e( 'Video Tutorials', 'wr-nitro' ); ?></h3>
					<p class="coming-soon"><?php esc_html_e( 'Video tutorials is the great way to show you how to setup Nitro theme, make sure that the feature works as it\'s designed.', 'wr-nitro' ); ?></p>
					<a href="#" class="button button-primary disabled"><?php esc_html_e( 'See Video', 'wr-nitro' ); ?></a>
				</div>
				<div class="col">
					<h3><?php esc_html_e( 'Forum', 'wr-nitro' ); ?></h3>
					<p><?php esc_html_e( 'Can\'t find the solution on documentation? We\'re here to help, even on weekend. Just click here to start 1on1 chatting with us!', 'wr-nitro' ); ?></p>
					<a target="_blank" rel="noopener noreferrer" href="http://nitro.woorockets.com/support" class="button button-primary"><?php esc_html_e( 'Request Support', 'wr-nitro' ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display a help tip.
	 *
	 * @param  string $tooltip        Help tip text
	 * @param  bool   $allow_html Allow sanitized HTML if true or escape
	 * @return string
	 */
	protected static function tooltip( $tooltip, $allow_html = false ) {
		if ( $allow_html ) {
			$tooltip = htmlspecialchars(
				wp_kses(
					html_entity_decode( $tooltip ),
					array(
						'br'     => array(),
						'em'     => array(),
						'strong' => array(),
						'small'  => array(),
						'span'   => array(),
						'ul'     => array(),
						'li'     => array(),
						'ol'     => array(),
						'p'      => array(),
					)
				)
			);
		} else {
			$tooltip = esc_attr( $tooltip );
		}

		return '<div class="help"><span class="dashicons dashicons-editor-help"></span><span class="tooltip">' . $tooltip . '</span></div>';
	}

	/**
	 * Register all available page templates with WR Content Migration plugin.
	 *
	 * @param   array  $templates  Current page templates.$this
	 *
	 * @return  array
	 */
	public static function register_page_templates( $templates ) {
		$templates['just-for-test'] = array(
			'thumb'  => '',
			'source' => '',
		);

		return $templates;
	}
}
