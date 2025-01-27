<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

$yith_google_fonts = new Google_Font();

/**
 * Google font management
 */
class Google_Font {

	/**
	 * The google fonts to include in the page
	 *
	 * @var array
	 */
	public $google_fonts = array();

	/**
	 * Generate the url for the google fonts
	 */
	public function google_fonts_url() {
		$base_url = 'https://fonts.googleapis.com/css?family=';
		$fonts    = array();

		if ( empty( $this->google_fonts ) ) {
			return '';
		}

		foreach ( $this->google_fonts as $font => $variants ) {
			$fonts[] = rawurlencode( $font . ':' . implode( ',', $variants ) );
		}

		return $base_url . implode( '|', $fonts );
	}

	/**
	 * Add a new google font in queue
	 *
	 * @param string $font     The name of google font.
	 * @param array  $variants The variatns for the google font to add.
	 */
	public function add_google_font( $font, $variants = array() ) {
		if ( ! is_array( $variants ) ) {
			$variants = array( $variants );
		}

		foreach ( $variants as $variant ) {
			if ( ! isset( $this->google_fonts[ $font ] ) ) {
				$this->google_fonts[ $font ] = array( 300, 400 );
			}
			if ( ! in_array( $variant, $this->google_fonts[ $font ], true ) ) {
				$this->google_fonts[ $font ][] = $variant;
			}
		}
	}
}

if ( ! function_exists( 'yith_add_google_font' ) ) {
	/**
	 * Add a new google font in queue
	 *
	 * @param string $font    The name of google font.
	 * @param array  $variant The variatns for the google font to add.
	 */
	function yith_add_google_font( $font, $variant = array() ) {
		global $yith_google_fonts;
		$yith_google_fonts->add_google_font( $font, $variant );
	}
}

if ( ! function_exists( 'yith_google_fonts_url' ) ) {
	/**
	 * The url with the google fonts to load
	 *
	 * @return string
	 */
	function yith_google_fonts_url() {
		global $yith_google_fonts;

		return $yith_google_fonts->google_fonts_url();
	}
}
