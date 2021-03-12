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

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

// Get footer layout
$wr_layout = $wr_nitro_options['footer_layout'];

// Get sidebar
$wr_sidebar_1 = $wr_nitro_options['footer_sidebar_1'];
$wr_sidebar_2 = $wr_nitro_options['footer_sidebar_2'];
$wr_sidebar_3 = $wr_nitro_options['footer_sidebar_3'];
$wr_sidebar_4 = $wr_nitro_options['footer_sidebar_4'];
$wr_sidebar_5 = $wr_nitro_options['footer_sidebar_5'];
?>
		<?php do_action( 'wr_nitro_before_footer' ); ?>

		<footer id="footer" class="<?php
			if ( is_customize_preview() )
				echo 'customizable customize-section-footer ';
 		?>footer" <?php WR_Nitro_Helper::schema_metadata( array( 'context' => 'footer' ) ); ?>>

 			<?php if ( ! empty( $wr_nitro_options['sidebar_before_footer_widget'] ) && is_active_sidebar( $wr_nitro_options['sidebar_before_footer_widget'] ) ) : ?>
				<div class="sidebar-before-footer">
					<div class="container">
						<?php dynamic_sidebar( $wr_nitro_options['sidebar_before_footer_widget'] ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( $wr_sidebar_1 ) || is_active_sidebar( $wr_sidebar_2 ) || is_active_sidebar( $wr_sidebar_3 ) || is_active_sidebar( $wr_sidebar_4 ) || is_active_sidebar( $wr_sidebar_5 ) ) : ?>
			<div class="top">
				<div class="top-inner">
					<div class="row">
						<?php
							switch( $wr_layout ) :
								case 'layout-1' :
									echo '<div class="cm-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									break;

								case 'layout-2' :
									echo '<div class="cm-9 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-3 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									break;

								case 'layout-3' :
									echo '<div class="cm-6 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-3 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-3 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									break;

								case 'layout-4' :
									echo '<div class="cm-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									break;

								case 'layout-5' :
									echo '<div class="cm-3 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-3 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-6 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									break;

								case 'layout-6' :
									echo '<div class="cm-3 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-9 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									break;

								case 'layout-8' :
									echo '<div class="cm-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									break;

								case 'layout-9' :
									echo '<div class="cm-4 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-8 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									break;

								case 'layout-10' :
									echo '<div class="cm-3 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-6 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-3 w800-4 cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									break;
								case 'layout-11' :
									echo '<div class="cm-4 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_4 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_5 );
									echo '</div>';
									break;
								case 'layout-12' :
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									echo '<div class="cm-2 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_4 );
									echo '</div>';
									echo '<div class="cm-4 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_5 );
									echo '</div>';
									break;
								default :
									echo '<div class="cm-3 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_1 );
									echo '</div>';
									echo '<div class="cm-3 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_2 );
									echo '</div>';
									echo '<div class="cm-3 w800-6 w800-clear cxs-12">';
										dynamic_sidebar( $wr_sidebar_3 );
									echo '</div>';
									echo '<div class="cm-3 w800-6 cxs-12">';
										dynamic_sidebar( $wr_sidebar_4 );
									echo '</div>';
									break;
							endswitch;
						?>
					</div><!-- .row -->
				</div><!-- .top-inner -->
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $wr_nitro_options['sidebar_after_footer_widget'] ) && is_active_sidebar( $wr_nitro_options['sidebar_after_footer_widget'] ) ) : ?>
				<div class="sidebar-after-footer">
					<div class="container">
						<?php dynamic_sidebar( $wr_nitro_options['sidebar_after_footer_widget'] ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="bot">
				<div class="info">
					<?php echo do_shortcode( wp_kses_post( $wr_nitro_options['footer_bot_text'] ) ); ?>
				</div>
			</div>
			<?php // Back to top button
				if ( $wr_nitro_options['back_top'] ) :
					$btn_classes = array( 'heading-color hover-bg-primary dib' );
					if ( 'light' == $wr_nitro_options['back_top_style'] ) {
						$btn_classes[] = 'overlay_bg nitro-line';
					} else {
						$btn_classes[] = 'heading-bg';
					}
				?>
					<div id="wr-back-top" >
						<a href="javascript:void(0);" class="<?php echo esc_attr( implode(' ', $btn_classes ) ); ?>"  title="<?php esc_attr_e( 'Back to top', 'wr-nitro' ); ?>"><i class="fa fa-angle-up"></i></a>
					</div>
			<?php endif; ?>
		</footer><!-- .footer -->

		<?php do_action( 'wr_nitro_after_footer' ); ?>
	</div></div><!-- .wrapper -->
<?php wp_footer(); ?>

</body>
</html>
