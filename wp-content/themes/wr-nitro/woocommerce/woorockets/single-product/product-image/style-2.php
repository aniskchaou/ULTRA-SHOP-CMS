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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

$wr_nitro_options = WR_Nitro::get_options();

// Get sale price dates
$countdown = get_post_meta( get_the_ID(), '_show_countdown', true );
$start     = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end       = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now       = date( 'd-m-y' );

$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
$image_title       = get_post_field( 'post_excerpt', $post_thumbnail_id );
$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'images',
	'pr'
) );

// Get thumbnail position
$thumb_position = $wr_nitro_options['wc_single_thumb_position'];
if ( $thumb_position != 'bottom' ) {
	$wrapper_classes[] = 'vertical';
	$wrapper_classes[] = 'vertical--' . $thumb_position;
}

// Embed video to product thumbnail
$video_source = get_post_meta( $post->ID, 'wc_product_video', true );
$video_link   = get_post_meta( $post->ID, 'wc_product_video_url', true );
$video_file   = get_post_meta( $post->ID, 'wc_product_video_file', true );
if ( ! empty( $video_link ) || ! empty( $video_file ) ) {
	$wrapper_classes[] = 'has-video';
}

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<?php if ( 'yes' == $countdown && $end && date( 'd-m-y', strtotime( $start ) ) <= $now ) : ?>
		<div class="product__countdown pa bgw">
			<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
		</div><!-- .product__countdown -->
	<?php endif; ?>

	<div class="woocommerce-product-gallery__wrapper">
		<?php
		$attributes = array(
			'title'                   => $image_title,
			'data-src'                => $full_size_image[0],
			'data-large_image'        => $full_size_image[0],
			'data-large_image_width'  => $full_size_image[1],
			'data-large_image_height' => $full_size_image[2],
		);

		if ( has_post_thumbnail() ) {
			$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" data-type="image-gallery-highlights" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
			$html .= get_the_post_thumbnail( $post->ID, 'shop_single', $attributes );
			$html .= '</a></div>';
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</div>
	<?php
		if ( $video_source == 'url' && ! empty( $video_link ) ) {
			echo '<div class="p-video pa">';
				echo '<a class="p-video-link db" href="' . esc_url( apply_filters('wr_nitro_refine_video_url', $video_link) ) . '"><i class="fa fa-play"></i></a>';
			echo '</div>';
		} elseif ( ! empty( $video_file ) ) {
			echo '<div class="p-video pa">';
				echo '<a class="p-video-file db" href="#wr-p-video"><i class="fa fa-play"></i></a>';
				echo '<div id="wr-p-video" class="mfp-hide">' . do_shortcode( '[video src="' . wp_get_attachment_url( $video_file ) . '" width="640" height="320"]' ) . '</div>';
			echo '</div>';
		}
	?>
</div>
<?php if ( $thumb_position == 'bottom' ) : ?>
	<div class="woocommerce-product-gallery--with-nav pr">
		<ul class="slides">
			<?php
			$attributes = array(
				'title'                   => $image_title,
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);

			if ( has_post_thumbnail() ) {
				$html  = '<li>';
					$html .= get_the_post_thumbnail( $post->ID, 'shop_thumbnail', $attributes );
				$html .= '</li>';
			}

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

			$attachment_ids = $product->get_gallery_image_ids();

			if ( $attachment_ids && has_post_thumbnail() ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
					$attributes      = array(
						'title'                   => get_post_field( 'post_title', $attachment_id ),
						'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
						'data-src'                => $full_size_image[0],
						'data-large_image'        => $full_size_image[0],
						'data-large_image_width'  => $full_size_image[1],
						'data-large_image_height' => $full_size_image[2],
					);

					$html  = '<li>';
						$html .= wp_get_attachment_image( $attachment_id, 'shop_thumbnail', false, $attributes );
			 		$html .= '</li>';

					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
				}
			}
			?>
		</ul>
	</div>
<?php endif; ?>
