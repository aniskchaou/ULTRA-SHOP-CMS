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

// Get option show search form
$wr_show_searchform = $wr_nitro_options['page_404_show_searchform'];

get_header();
?>
	<div class="container<?php echo ( is_customize_preview() ? ' customizable customize-section-page_404' : '' ); ?>">

		<div class="content-404 tc">
			<div class="heading-404"><h2>404</h2></div>
			<div class="content-inner">
				<?php
					// Render 404 content
					echo wp_kses_post( $wr_nitro_options['page_404_content'] );
				?>
			</div>
			<?php
				// Render search form
				if ( $wr_show_searchform == '1' ) {
					get_search_form();
				}
			?>
		</div><!-- .content-404 -->

		<?php do_action( 'wr_nitro_404_content' ); ?>

	</div><!-- .container -->

<?php get_footer(); ?>
