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
?>
<div id="nitro-usage-data-collector" class="<?php
	if ( isset( $force ) && $force ) {
		echo 'nitro-system-status';
	} else {
		echo 'notice notice-success is-dismissible';
	} ?>" style="display: block !important">
	<?php
	echo wp_kses_post( __( '
<p>We&#39;d like to ask you the permission to share the following data which can help us improve Nitro theme:</p>
<ol>
	<li>URL that Nitro is installed (Why? Use for showcase purpose & connecting Nitro users)</li>
	<li>Niche demo is used (Why? Get to know which demo is the most popular in Nitro and how we can improve it more)</li>
	<li>List of plugins on the website (Why? Use for making the Nitro compatible with those plugins and your website works stably)</li>
	<li>Hosting parameters (Why? Make the Nitro compatible with your website environment)</li>
</ol>
	', 'wr-nitro' ) );
	?>
	<p class="submit">
		<input type="button" class="allow button button-primary" value="<?php _e( 'Yes', 'wr-nitro' ); ?>">
		<input type="button" class="disallow button" value="<?php _e( 'No', 'wr-nitro' ); ?>">
	</p>
	<?php if ( ! isset( $force ) || ! $force ) : ?>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text"><?php _e( 'Dismiss this message.', 'wr-nitro' ); ?></span>
	</button>
	<?php endif; ?>
</div>
<script type="text/javascript">
	jQuery( function( $ ) {
		$( document ).ready( function() {
			$( '#nitro-usage-data-collector' ).on( 'click', '.allow, .disallow', function() {
				$( this ).parent().children().attr( 'disabled', 'disabled' );

				$.ajax( {
				    url: '<?php echo admin_url( "admin-ajax.php?action=nitro_enable_collector" ); ?>',
				    data: {
					    enable: $( this ).hasClass( 'allow' ) ? 1 : 0,
				    },
				    context: this,
				    complete: function() {
					    $( this ).closest( '#nitro-usage-data-collector' ).fadeOut();
				    },
				} );
			} );
		} );
	} );
</script>