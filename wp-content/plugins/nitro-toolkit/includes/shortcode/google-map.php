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
 * Nitro Google Map shortcode.
 */
class Nitro_Toolkit_Shortcode_Google_Map extends Nitro_Toolkit_Shortcode {
	/**
	 * Shortcode name.
	 *
	 * @var  string
	 */
	public $shortcode = 'google_map';

	/**
	 * Generate HTML code based on shortcode parameters.
	 *
	 * @param   array   $atts     Shortcode parameters.
	 * @param   string  $content  Current content.
	 *
	 * @return  string
	 */
	public function generate_html( $atts, $content = null ) {
		$html = $css = '';

		// Extract shortcode parameters.
		extract(
			shortcode_atts(
				array(
					'lat'               => '0',
					'lon'               => '0',
					'api'               => '',
					'z'                 => 14,
					'w'                 => '600',
					'h'                 => '400',
					'maptype'           => 'ROADMAP',
					'mapstype'          => '',
					'address'           => '',
					'marker'            => '',
					'markerimage'       => '',
					'traffic'           => '',
					'draggable'         => '',
					'infowindow'        => '',
					'infowindowdefault' => '',
					'hidecontrols'      => '',
					'scrollwheel'       => '',
				),
				$atts
			)
		);

		$classes = array( 'nitro-gmap' );

		// Generate an unique ID.
		$id = uniqid( 'map_' );

		// Generate HTML code.
		$w     = is_numeric( $w ) ? 'width:'. esc_attr( $w ) .'px;' : 'width:'. esc_attr( $w ) .';';
		$h     = is_numeric( $h ) ? 'height:'. esc_attr( $h ) .'px;' : 'height:'. esc_attr( $h ) .';';
		$html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" id="nitro_' . esc_attr( $id ) . '" style="' . esc_attr( $w ) . esc_attr( $h ) . '"></div>';

		$html .= '
<scr' . 'ipt>
	(function($) {
		$(document).ready(function() {
			var options = {
				zoom: ' . $z . ',
				center: {lat: ' . esc_js( $lat ) . ', lng: ' . esc_js( $lon ) . '},
				mapTypeId: google.maps.MapTypeId.' . esc_js( $maptype ) . ',';

		if ( $scrollwheel == 'true' ) {
			$html .= '
				scrollwheel: true,';
		} else {
			$html .= '
				scrollwheel: false,';
		}

		if ( ! empty( $hidecontrols ) ) {
			$html .= '
				disableDefaultUI: "' . esc_js( $hidecontrols ) . '",';
		}

		switch ( $mapstype ) {
			case 'grayscale' :
				$html .= '
				styles: [
					{"featureType": "landscape","stylers": [{"saturation": -100},{"lightness": 65},{"visibility": "on"}]},
					{"featureType": "poi","stylers": [{"saturation": -100},{"lightness": 51},{"visibility": "simplified"}]},
					{"featureType": "road.highway","stylers": [{"saturation": -100},{"visibility": "simplified"}]},
					{"featureType": "road.arterial","stylers": [{"saturation": -100},{"lightness": 30},{"visibility": "on"}]},
					{"featureType": "road.local","stylers": [{"saturation": -100},{"lightness": 40},{"visibility": "on"}]},
					{"featureType": "transit","stylers": [{"saturation": -100},{"visibility": "simplified"}]},
					{"featureType": "administrative.province","stylers": [{"visibility": "off"}]},
					{"featureType": "water","elementType": "labels","stylers": [{"visibility": "on"},{"lightness": -25},{"saturation": -100}]},
					{"featureType": "water","elementType": "geometry","stylers": [{"hue": "#ffff00"},{"lightness": -25},{"saturation": -97}]}
				]';
			break;

			case 'blue_water' :
				$html .= '
				styles: [
					{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},
					{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},
					{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},
					{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},
					{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},
					{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},
					{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},
					{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}
				]';
			break;

			case 'pale_dawn' :
				$html .= '
				styles: [
					{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"},{"lightness":33}]},
					{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2e5d4"}]},
					{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#c5dac6"}]},
					{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},
					{"featureType":"road","elementType":"all","stylers":[{"lightness":20}]},
					{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#c5c6c6"}]},
					{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#e4d7c6"}]},
					{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#fbfaf7"}]},
					{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#acbcc9"}]}
				]';
			break;

			case 'shades_of_grey' :
				$html .= '
				styles: [
					{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},
					{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},
					{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},
					{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},
					{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},
					{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},
					{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},
					{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},
					{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},
					{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},
					{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},
					{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},
					{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}
				]';
			break;
		}

		$html .= '
			};

			var ' . esc_js( $id ) . ' = new google.maps.Map(document.getElementById("nitro_' . esc_js( $id ) . '"), options);';

		// Traffic
		if ( $traffic == 'true' ) {
			$html .= '
			var trafficLayer = new google.maps.TrafficLayer();
			trafficLayer.setMap(' . esc_js( $id ) . ');';
		}

		// Address
		if ( ! empty( $address ) ) {
			$html .= '
			var geocoder_' . esc_js( $id ) . ' = new google.maps.Geocoder();
			var address = \'' . esc_js( $address ) . '\';
			geocoder_' . esc_js( $id ) . '.geocode({\'address\': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					' . esc_js( $id ) . '.setCenter(results[0].geometry.location);';

			if ( $marker == 'true' ) {
				// Add custom image
				if ( ! empty( $markerimage ) && is_numeric( $markerimage ) ){
					$html .= '
					var image = "' . wp_get_attachment_url( $markerimage ) . '";';
				} elseif ( ! empty( $markerimage ) ) {
					$html .= '
					var image = "' . esc_js( $markerimage ) . '";';
				}

				$html .= '
					var marker = new google.maps.Marker({
						map: ' . esc_js( $id ) . ',';
				if ( $draggable == 'true' ) {
					$html .= '
						draggable: true,';
				}

				if ( ! empty( $markerimage ) ) {
					$html .= '
						icon: image,';
				}

				$html .= '
						position: ' . esc_js( $id ) . '.getCenter()
					});';

				// Info window
				if ( ! empty( $infowindow ) ) {
					// First convert and decode html chars
					$thiscontent = htmlspecialchars_decode( $infowindow );
					$html .= '
					var contentString = \'' . $thiscontent . '\';
					var infowindow = new google.maps.InfoWindow({
						content: contentString
					});
					google.maps.event.addListener(marker, \'click\', function() {
						infowindow.open(' . esc_js( $id ) . ',marker);
					});';

					// Show info window by default
					if ( $infowindowdefault == 'true' ) {
						$html .= '
					infowindow.open(' . esc_js( $id ) . ',marker);';
					}
				}
			}

			$html .= '
					} else {
					alert("Rendering address failed with following reason: " + status);
				}
			});';
		}

		// Marker: show if address is not specified
		if ( $marker == 'true' && empty( $address ) ) {
			// Add custom image
			if ( ! empty( $markerimage ) && is_numeric( $markerimage ) ){
				$html .= '
			var image = "'. wp_get_attachment_url( $markerimage ) .'";';
			} elseif ( ! empty( $markerimage ) ){
				$html .= '
			var image = "'. esc_js( $markerimage ) .'";';
			}

			$html .= '
			var marker = new google.maps.Marker({
				map: ' . esc_js( $id ) . ',';

			if ( $draggable == 'true' ) {
				$html .= '
				draggable: true,';
			}

			if ( ! empty ( $markerimage ) ) {
				$html .= '
				icon: image,';
			}

			$html .= '
				position: ' . esc_js( $id ) . '.getCenter()
			});';

			// Info window
			if ( ! empty( $infowindow ) ) {
				$html .= '
			var contentString = \'' . esc_js( $infowindow ) . '\';
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});
			google.maps.event.addListener(marker, \'click\', function() {
			  infowindow.open(' . esc_js( $id ) . ',marker);
			});';

				// Show info window by default
				if ( $infowindowdefault == 'true' ) {
					$html .= '
			infowindow.open(' . esc_js( $id ) . ',marker);';
				}
			}
		}

		$html .= '
		});
	})(jQuery);
</scr' . 'ipt>';

		$subfix = ! empty( $api ) ? '?key=' . $api : '';

		wp_enqueue_script( 'nitro-google-map', 'https://maps.googleapis.com/maps/api/js' . $subfix );

		return apply_filters( 'nitro_toolkit_shortcode_google_map', force_balance_tags( $html ) );
	}

}
