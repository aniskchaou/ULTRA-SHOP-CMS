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

get_header();
?>
	<div class="maintenance-content">

		<?php
			echo '<div class="maintenance-title">' . $wr_nitro_options['under_construction_title'] . '</div>';
			echo '<p class="maintenance-message pr mgb50">' . $wr_nitro_options['under_construction_message'] . '</p>';
		?>

		<div class="wr-countdown"></div>

	</div><!-- .maintenance-content -->
	<?php echo '<scr' . 'ipt>' ?>
	(function($) {
		"use strict";

		$(document).ready(function() {
			function count_down() {
				if (typeof $.fn.countdown == 'undefined') {
					return setTimeout(count_down, 100);
				}

				var endDate = '<?php echo esc_js( $wr_nitro_options['under_construction_timer'] ); ?>';

				$('.wr-countdown').countdown({
					date: endDate,
					render: function(data) {
						$(this.el).html('<div><div>' + this.leadingZeros(data.days, 2) + ' <span><?php esc_html_e( 'Days', 'wr-nitro' ); ?></span></div></div><div><div>' + this.leadingZeros(data.hours, 2) + ' <span><?php esc_html_e( 'Hours', 'wr-nitro' ); ?></span></div></div><div><div>' + this.leadingZeros(data.min, 2) + ' <span><?php esc_html_e( 'Minutes', 'wr-nitro' ); ?></span></div></div><div><div>' + this.leadingZeros(data.sec, 2) + ' <span><?php esc_html_e( 'Seconds', 'wr-nitro' ); ?></span></div></div>' );
					}
				});

				if ($('body').hasClass('boxed')) {
					$('body').removeClass('boxed');
				}
			}

			count_down();
		});
	})(jQuery);
	<?php echo '</scr' . 'ipt>' ?>

<?php wp_footer();
