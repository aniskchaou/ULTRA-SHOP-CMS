<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $el_class
 * @var $collapsible
 * @var $disable_keyboard
 * @var $active_tab
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Accordion
 */
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'jquery-ui-accordion' );
$el_class = $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion wpb_content_element ' . $el_class . ' not-column-inherit', $this->settings['base'], $atts );

$output .= "\n\t" . '<div class="' . esc_attr( $css_class ) . '" data-collapsible="' . esc_attr( $collapsible ) . '" data-vc-disable-keydown="' . ( esc_attr( ( 'yes' == $disable_keyboard ? 'true' : 'false' ) ) ) . '" data-active-tab="' . $active_tab . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper wpb_accordion_wrapper ui-accordion ' . esc_attr( $style ) . ' ">';
$output .= wpb_widget_title( array( 'title' => $title, 'extraclass' => 'wpb_accordion_heading' ) );
$output .= "\n\t\t\t" . wpb_js_remove_wpautop( $content );
$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( $this->getShortcode() );

echo apply_filters( 'wr_vc_accordion', $output );