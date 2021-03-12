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
 * Nitro Product Categories shortcode.
 */
class Nitro_Toolkit_Shortcode_Product_Package extends Nitro_Toolkit_Shortcode {
    /**
     * Shortcode name.
     *
     * @var  string
     */
    public $shortcode = 'product_package';

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
                wp_enqueue_style( 'wr-nitro-woocommerce'     );
                wp_enqueue_script( 'magnific-popup' );
            }
        }

        // Let parent class load default scripts.
        parent::enqueue_scripts();
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
        global $post;

        $html = '';

        // Extract shortcode parameters.
        $atts = shortcode_atts(
            array(
                'ids'         => '',
                'extra_class' => '',
                'list_style'  => 'grid',
                'style'       => 1,
            ),
            $atts
        );
        extract( $atts );

        $classes = array( 'sc-product-package ' . $extra_class );

        // Filter product post type
        $args = array(
            'post_type' => 'product',
            'post__in'  => explode( ',', $ids )
        );
        $the_query = new WP_Query( $args );

        // Make shortcode attributes accessible from outside.
        Nitro_Toolkit_Shortcode::set_attrs( $atts );

        $html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
        $html .= '<ul>';
        while ( $the_query->have_posts() ) {
            $the_query->the_post();

            $html .= '<li class="pr mgb20 pdb20 pdl40 clearfix">';
            $html .= '<div class="p-package-info fl">';
            $html .= '<div class="p-package-head">';
            $html .= '<h4 class="mg0 pdr20 fl product-title">';
            $html .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            $html .= '</h4>';
            ob_start();
            woocommerce_template_single_price();
            $html .= ob_get_clean();
            $html .= '</div>';
            // Get all product terms
            $terms = get_the_terms( $post->ID, 'product_cat' );
            $html .= '<div class="p-package-cat">';
            foreach ( $terms as $term ) {
                $terms_link = get_term_link( $term->slug, 'product_cat' );
                $html .= '<a href="' . $terms_link . '">' . $term->name . '</a>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="p-package-cart fr">';
            $html .= do_shortcode( '[nitro_product_button id="' . get_the_ID() . '"]' );
            $html .= '</div>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        wp_reset_postdata();

        // Reset globally accessible shortcode attributes.
        Nitro_Toolkit_Shortcode::set_attrs( null );

        return apply_filters( 'nitro_toolkit_shortcode_product_package', force_balance_tags( $html ) );
    }
}
