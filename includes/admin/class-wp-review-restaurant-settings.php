<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Review_Restaurant_Settings class.
 */
class WP_Review_Restaurant_Settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->settings_group = 'review_restaurant';
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * init_settings function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function init_settings() {
		// Prepare roles option
		$roles         = get_editable_roles();
		$account_roles = array();

		foreach ( $roles as $key => $role ) {
			if ( $key == 'administrator' ) {
				continue;
			}
			$account_roles[ $key ] = $role['name'];
		}

		$this->settings = apply_filters( 'review_restaurant_settings',
			array(
				'restaurant_listings' => array(
					__( 'Restaurant Listings', 'wp-review-restaurant' ),
					array(
						array(
							'name'        => 'review_restaurant_per_page',
							'std'         => '10',
							'placeholder' => '',
							'label'       => __( 'Restaurants per page', 'wp-review-restaurant' ),
							'desc'        => __( 'How many restaurants should be shown per page by default?', 'wp-review-restaurant' ),
							'attributes'  => array()
						),
						array(
							'name'       => 'review_restaurant_hide_filled_positions',
							'std'        => '0',
							'label'      => __( 'Filled positions', 'wp-review-restaurant' ),
							'cb_label'   => __( 'Hide filled positions', 'wp-review-restaurant' ),
							'desc'       => __( 'If enabled, filled positions will be hidden from the restaurant list.', 'wp-review-restaurant' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'review_restaurant_enable_categories',
							'std'        => '0',
							'label'      => __( 'Restaurants categories', 'wp-review-restaurant' ),
							'cb_label'   => __( 'Enable restaurant categories', 'wp-review-restaurant' ),
							'desc'       => __( 'Choose whether to enable restaurant categories. Categories must be setup by an admin for users to choose during restaurant submission.', 'wp-review-restaurant' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
					),
				),
				'restaurant_submission' => array(
					__( 'Restaurant Submission', 'wp-review-restaurant' ),
					array(
						array(
							'name'       => 'review_restaurant_enable_registration',
							'std'        => '1',
							'label'      => __( 'Account creation', 'wp-review-restaurant' ),
							'cb_label'   => __( 'Allow account creation', 'wp-review-restaurant' ),
							'desc'       => __( 'If enabled, non-logged in users will be able to create an account by entering their email address on the restaurant submission form.', 'wp-review-restaurant' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'review_restaurant_registration_role',
							'std'        => 'restaurant_administrator',
							'label'      => __( 'Account Role', 'wp-review-restaurant' ),
							'desc'       => __( 'If you enable registration on your restaurant submission form, choose a role for the new user.', 'wp-review-restaurant' ),
							'type'       => 'select',
							'options'    => $account_roles
						),
						array(
							'name'       => 'review_restaurant_user_requires_account',
							'std'        => '1',
							'label'      => __( 'Account required', 'wp-review-restaurant' ),
							'cb_label'   => __( 'Restaurant submission requires an account', 'wp-review-restaurant' ),
							'desc'       => __( 'If disabled, non-logged in users will be able to submit restaurant listings without creating an account.', 'wp-review-restaurant' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'review_restaurant_submission_requires_approval',
							'std'        => '1',
							'label'      => __( 'Approval Required', 'wp-review-restaurant' ),
							'cb_label'   => __( 'New submissions require admin approval', 'wp-review-restaurant' ),
							'desc'       => __( 'If enabled, new submissions will be inactive, pending admin approval.', 'wp-review-restaurant' ),
							'type'       => 'checkbox',
							'attributes' => array()
						),
						array(
							'name'       => 'review_restaurant_submission_duration',
							'std'        => '30',
							'label'      => __( 'Listing duration', 'wp-review-restaurant' ),
							'desc'       => __( 'How many <strong>days</strong> listings are live before expiring. Can be left blank to never expire.', 'wp-review-restaurant' ),
							'attributes' => array()
						),
						array(
							'name'       => 'review_restaurant_allowed_application_method',
							'std'        => '',
							'label'      => __( 'Application method', 'wp-review-restaurant' ),
							'desc'       => __( 'Choose what restaurant administrators can use for their restaurant application method.', 'wp-review-restaurant' ),
							'type'       => 'select',
							'options'    => array(
								''      => __( 'Email address or website URL', 'wp-review-restaurant' ),
								'email' => __( 'Email addresses only', 'wp-review-restaurant' ),
								'url'   => __( 'Website URLs only', 'wp-review-restaurant' ),
							)
						),
						array(
							'name' 		=> 'review_restaurant_submit_page_slug',
							'std' 		=> '',
							'label' 	=> __( 'Submit Page Slug', 'wp-review-restaurant' ),
							'desc'		=> __( 'Enter the slug of the page where you have placed the [submit_restaurant_form] shortcode. This lets the plugin know where the form is located.', 'wp-review-restaurant' ),
							'type'      => 'input'
						)
					)
				),
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {
		$this->init_settings();

		foreach ( $this->settings as $section ) {
			foreach ( $section[1] as $option ) {
				if ( isset( $option['std'] ) )
					add_option( $option['name'], $option['std'] );
				register_setting( $this->settings_group, $option['name'] );
			}
		}
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */
	public function output() {
		$this->init_settings();
		?>
		<div class="wrap review-restaurant-settings-wrap">
			<form method="post" action="options.php">

				<?php settings_fields( $this->settings_group ); ?>

			    <h2 class="nav-tab-wrapper">
			    	<?php
			    		foreach ( $this->settings as $key => $section ) {
			    			echo '<a href="#settings-' . sanitize_title( $key ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
			    		}
			    	?>
			    </h2>

				<?php
					if ( ! empty( $_GET['settings-updated'] ) ) {
						flush_rewrite_rules();
						echo '<div class="updated fade review-restaurant-updated"><p>' . __( 'Settings successfully saved', 'wp-review-restaurant' ) . '</p></div>';
					}

					foreach ( $this->settings as $key => $section ) {

						echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';

						echo '<table class="form-table">';

						foreach ( $section[1] as $option ) {

							$placeholder    = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . $option['placeholder'] . '"' : '';
							$class          = ! empty( $option['class'] ) ? $option['class'] : '';
							$value          = get_option( $option['name'] );
							$option['type'] = ! empty( $option['type'] ) ? $option['type'] : '';
							$attributes     = array();

							if ( ! empty( $option['attributes'] ) && is_array( $option['attributes'] ) )
								foreach ( $option['attributes'] as $attribute_name => $attribute_value )
									$attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';

							echo '<tr valign="top" class="' . $class . '"><th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th><td>';

							switch ( $option['type'] ) {

								case "checkbox" :

									?><label><input id="setting-<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php echo implode( ' ', $attributes ); ?> <?php checked( '1', $value ); ?> /> <?php echo $option['cb_label']; ?></label><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								case "textarea" :

									?><textarea id="setting-<?php echo $option['name']; ?>" class="large-text" cols="50" rows="3" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?>><?php echo esc_textarea( $value ); ?></textarea><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								case "select" :

									?><select id="setting-<?php echo $option['name']; ?>" class="regular-text" name="<?php echo $option['name']; ?>" <?php echo implode( ' ', $attributes ); ?>><?php
										foreach( $option['options'] as $key => $name )
											echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';
									?></select><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								case "password" :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="password" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								default :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

							}

							echo '</td></tr>';
						}

						echo '</table></div>';

					}
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-review-restaurant' ); ?>" />
				</p>
		    </form>
		</div>
		<script type="text/javascript">
			jQuery('.nav-tab-wrapper a').click(function() {
				jQuery('.settings_panel').hide();
				jQuery('.nav-tab-active').removeClass('nav-tab-active');
				jQuery( jQuery(this).attr('href') ).show();
				jQuery(this).addClass('nav-tab-active');
				return false;
			});

			jQuery('.nav-tab-wrapper a:first').click();
		</script>
		<?php
	}
}