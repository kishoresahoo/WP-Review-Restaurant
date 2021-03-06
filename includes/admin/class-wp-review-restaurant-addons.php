<?php
/**
 * Addons Page
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WP_Review_Restaurant_Addons' ) ) :

/**
 * WP_Review_Restaurant_Addons Class
 */
class WP_Review_Restaurant_Addons {

	/**
	 * Handles output of the reports page in admin.
	 */
	public function output() {

		if ( false === ( $addons = get_transient( 'wp_review_restaurant_addons_html' ) ) ) {

			$raw_addons = wp_remote_get( 
				'http://www.woothemes.com/product-category/woocommerce-extensions/',
				array(
					'timeout'     => 10,
					'redirection' => 5,
					'sslverify'   => false
				) 
			);

			if ( ! is_wp_error( $raw_addons ) ) {

				$raw_addons = wp_remote_retrieve_body( $raw_addons );

				// Get Products
				$dom = new DOMDocument();
				libxml_use_internal_errors(true);
				$dom->loadHTML( $raw_addons );

				$xpath  = new DOMXPath( $dom );
				$tags   = $xpath->query('//ul[@class="products"]');
				foreach ( $tags as $tag ) {
					$addons = $tag->ownerDocument->saveXML( $tag );
					break;
				}

				$addons = wp_kses_post( $addons );

				if ( $addons ) {
					set_transient( 'wp_review_restaurant_addons_html', $addons, 60*60*24*7 ); // Cached for a week
				}
			}
		}

		?>
		<div class="wrap wp_review_restaurant wp_review_restaurant_addons_wrap">
			<h2><?php _e( 'WP Review Restaurant Add-ons', 'wp-review-restaurant' ); ?></h2>
			<div id="notice" class="updated below-h2"><p><?php printf( __( 'Buying multiple add-ons? <a href="%s">Check out the core add-on bundle &rarr;</a>', 'wp-review-restaurant' ), 'http://opentuteplus.com/bundle/' ); ?></p></div>
			<p></p>
			<?php echo $addons; ?>
		</div>
		<?php
	}
}

endif;

return new WP_Review_Restaurant_Addons();