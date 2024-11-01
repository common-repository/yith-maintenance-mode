<?php
/**
 * Your Inspiration Themes common functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @version 0.0.1
 * @package YITH/MaintenanceMode
 */

! defined( 'YITH_FUNCTIONS' ) && define( 'YITH_FUNCTIONS', true );

/* === Include Common Framework File === */
require_once 'google_fonts.php';
require_once 'yith-panel.php';

if ( ! function_exists( 'yit_wp_roles' ) ) {
	/**
	 * Returns the roles of the site.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function yit_wp_roles() {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}

		$roles = array();
		foreach ( $wp_roles->roles as $k => $role ) {
			$roles[ $k ] = $role['name'];
		}

		return $roles;
	}
}

if ( ! function_exists( 'yit_user_roles' ) ) {
	/**
	 * Returns the roles of the user
	 *
	 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function yit_user_roles( $user_id = null ) {
		if ( is_numeric( $user_id ) ) {
			$user = get_userdata( $user_id );
		} else {
			$user = wp_get_current_user();
		}

		if ( empty( $user ) ) {
			return array();
		}

		return (array) $user->roles;
	}
}

if ( ! function_exists( 'yit_extract' ) ) {
	/**
	 * Extract array variables
	 *
	 * Usage example:
	 * ```
	 * list ( $type, $class, $value ) = yit_extract( $field, 'type', 'class', 'value' );
	 * ```
	 *
	 * @param array  $array   The array.
	 * @param string ...$keys The keys.
	 *
	 * @return array
	 * @since 3.5
	 */
	function yit_extract( $array, ...$keys ) {
		return array_map(
			function ( $key ) use ( $array ) {
				return isset( $array[ $key ] ) ? $array[ $key ] : null;
			},
			$keys
		);
	}
}

if ( ! function_exists( 'yit_typo_option_to_css' ) ) {
	/**
	 * Change the typography option saved in database to attributes for css
	 *
	 * @param array $option The option as saved in the database.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function yit_typo_option_to_css( $option ) {
		$attrs   = array();
		$variant = array();

		list( $color, $size, $unit, $family, $style ) = yit_extract( $option, 'color', 'size', 'unit', 'family', 'style' );

		$attrs[] = "color: $color;";
		$attrs[] = "font-size: $size $unit;";
		$attrs[] = "font-family: '$family';";
		switch ( $style ) {
			case 'regular':
				$attrs[] = 'font-weight: 400;';
				$attrs[] = 'font-style: normal;';
				$variant = 400;
				break;
			case 'bold':
				$attrs[] = 'font-weight: 700;';
				$attrs[] = 'font-style: normal;';
				$variant = 700;
				break;
			case 'extra-bold':
				$attrs[] = 'font-weight: 800;';
				$attrs[] = 'font-style: normal;';
				$variant = 800;
				break;
			case 'italic':
				$attrs[] = 'font-weight: 400;';
				$attrs[] = 'font-style: italic;';
				$variant = 400;
				break;
			case 'bold-italic':
				$attrs[] = 'font-weight: 700;';
				$attrs[] = 'font-style: italic;';
				$variant = 700;
				break;
		}

		yith_add_google_font( $family, $variant );

		return implode( "\n", $attrs ) . "\n";
	}
}

if( ! function_exists( 'yith_show_gfont_gdpr_disclaimer' ) ){
	/**
	 * Get the Google Fonts GDPR disclaimer  box in plugin panel options
	 *
	 * @since 1.6.0
	 * @author YITH <plugins@yithemes.com>
	 * @return void
	 */
	function yith_show_gfont_gdpr_disclaimer( $show = true ){
		$gfont_disclaimer_title = esc_html__( 'Google Fonts and GDPR', 'yith-maintenance-mode' );
		$gfont_faq_url         = 'https://developers.google.com/fonts/faq#what_does_using_the_google_fonts_api_mean_for_the_privacy_of_my_users';
		$gfont_disclaimer_text  = wp_kses_post(
			sprintf( __( 'As you can see in %1$sGoogle FAQ%2$s:%3$sThe Google Fonts API is designed to limit the collection, storage, and use of end-user data to what is needed to serve fonts efficiently.[…] Google Fonts logs records of the CSS and the font file requests, and access to this data is kept secure. […] We use data from Google’s web crawler to detect which websites use Google fonts.%4$sIn other words, when someone visits your website, Google will be able to access the IP address they used to access it. As a result of using Google Fonts, you implicitly accept their terms and conditions, and you must inform people visiting your site of this in accordance with the current GDPR law in Europe.', 'yith-maintenance-mode' ),
				'<a target="_blank" href="' . $gfont_faq_url . '" rel="noopener">',
				'</a>',
				'<blockquote class="yith-gfont-quote-disclamer">',
				'</blockquote>'
			));
		printf( '<div id="yith-gfont-disclamer" class="notice update-nag"><h4>%s</h4>%s</div>', $gfont_disclaimer_title, $gfont_disclaimer_text );
	}

	add_action( 'yith_panel_before_panel', 'yith_show_gfont_gdpr_disclaimer' );
}

