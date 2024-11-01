<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Your Inspiration Themes Panel
 *
 * @author  YITH <plugins@yithemes.com>
 * @version 0.1.0
 * @package YITH/MaintenanceMode
 */

if ( ! class_exists( 'YITH_Panel' ) ) {
	/**
	 * The class manages the theme options for the Plugin
	 */
	class YITH_Panel {

		/**
		 * Class version
		 *
		 * @var string
		 */
		public $version = '0.1.0';

		/**
		 * Parameters for add_submenu_page
		 *
		 * USE: add_submenu_page(
		 *      'themes.php',        // The file name of a standard WordPress admin page
		 *      'Theme Options',    // The text to be displayed in the title tags of the page when the menu is selected
		 *      'Theme Options',    // The text to be used for the menu
		 *      'administrator',    // The capability (or role) required for this menu to be displayed to the user.
		 *      'theme-options',    // The slug name to refer to this menu by (should be unique for this menu).
		 *      'theme_options_display_page' // The function to be called to output the content for this page.
		 *  );
		 *
		 * @access protected
		 * @var array
		 */
		protected $submenu = array();

		/**
		 * Initial Options definition:
		 *   'tab' => array(
		 *      'label',
		 *      'sections' => array(
		 *          'fields' => array(
		 *             'option1',
		 *             'option2',
		 *              ...
		 *          )
		 *      )
		 *   )
		 *
		 * @var array
		 * @access public
		 */
		public $options = array();

		/**
		 * Options group name
		 *
		 * @var string
		 * @access public
		 */
		public $option_group = 'panel_group';

		/**
		 * Option name
		 *
		 * @var string
		 * @access public
		 */
		public $option_name = 'panel_options';

		/**
		 * Banner links
		 *
		 * @var string
		 * @access public
		 */
		public $banner_url = 'https://yithemes.com/?ap_id=plugin';

		/**
		 * Banner links
		 *
		 * @var string
		 * @access public
		 */
		public $banner_img = '';

		/**
		 * Constructor
		 *
		 * @param array          $submenu      Parameters for add_submenu_page.
		 * @param array          $options      Array of plugin options.
		 * @param array          $banner       Array of banner options.
		 * @param boolean|string $option_group The name of the option group.
		 * @param boolean|string $option_name  The option name.
		 *
		 * @return void
		 */
		public function __construct( $submenu, $options, $banner = array(), $option_group = false, $option_name = false ) {
			$this->submenu = apply_filters( 'yith_panel_submenu', $submenu );
			$this->options = apply_filters( 'yith_panel_options', $options );

			if ( ! empty( $banner ) ) {
				$this->banner_url = $banner['url'];
				$this->banner_img = $banner['img'];
			}

			if ( $option_group ) {
				$this->option_group = $option_group;
			}

			if ( $option_name ) {
				$this->option_name = $option_name;
			}

			// add new menu item.
			// register new settings option group.
			// include js and css files.
			// print browser.
			add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
			add_action( 'admin_init', array( $this, 'panel_register_setting' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'panel_enqueue' ) );

			// add the typography javascript vars.
			add_action( 'yith_panel_after_panel', array( $this, 'js_typo_vars' ) );
		}

		/**
		 * Create new submenu page
		 *
		 * @return void
		 * @access public
		 * @link   http://codex.wordpress.org/Function_Reference/add_submenu_page
		 */
		public function add_submenu_page() {
			$submenu = $this->submenu;
			add_submenu_page(
				$submenu[0],
				$submenu[1],
				$submenu[2],
				$submenu[3],
				$submenu[4],
				array( $this, isset( $submenu[5] ) ? $submenu[5] : 'display_panel_page' )
			);
		}

		/**
		 * Print the Panel page
		 *
		 * @return void
		 * @access public
		 */
		public function display_panel_page() {
			// Create a header in the default WordPress 'wrap' container.
			$page = $this->get_tab();
			?>
			<div id="icon-themes" class="icon32"><br /></div>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $this->options as $k => $tab ) : ?>
					<a class="nav-tab <?php echo ( $page === $k ) ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'panel_page', $k ) ); ?>"><?php echo esc_html( $tab['label'] ); ?></a>
				<?php endforeach ?>
				<?php do_action( 'yith_panel_after_tabs' ); ?>
			</h2>

			<div class="wrap">
				<?php do_action( 'yith_panel_before_panel' ); ?>
				<form action="options.php" method="post">

					<?php do_settings_sections( $this->option_name ); ?>
					<?php settings_fields( $this->option_group ); ?>

					<p class="submit">
						<input type="hidden" name="panel_page" value="<?php echo esc_attr( $page ); ?>" />
						<input class="button-primary" type="submit" name="save_options" value="<?php _e(' Save Options', 'yith-maintenance-mode' ); ?>" />
					</p>
				</form>
				<?php do_action( 'yith_panel_after_panel' ); ?>
			</div>
			<?php
		}

		/**
		 * Add the vars for the typography options
		 */
		public function js_typo_vars() {
			global $yith_panel_if_typography;
			if ( ! isset( $yith_panel_if_typography ) || ! $yith_panel_if_typography ) {
				return;
			}

			$web_fonts = array(
				'Arial',
				'Arial Black',
				'Comic Sans MS',
				'Courier New',
				'Georgia',
				'Impact',
				'Lucida Console',
				'Lucida Sans Unicode',
				'Thaoma',
				'Trebuchet MS',
				'Verdana',
			);

			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$google_fonts = $wp_filesystem->get_contents( dirname( __FILE__ ) . '/assets/js/google_fonts.json' );
			?>
			<script type="text/javascript">
				var yit_google_fonts  = '<?php echo wp_kses( $google_fonts, 'entities' ); ?>',
					yit_web_fonts     = '{"items":<?php echo wp_json_encode( $web_fonts ); ?>}',
					yit_family_string = '';
			</script>
			<?php
		}

		/**
		 * Register a new settings option group
		 *
		 * @return void
		 * @access public
		 * @link   http://codex.wordpress.org/Function_Reference/register_setting
		 * @link   http://codex.wordpress.org/Function_Reference/add_settings_section
		 * @link   http://codex.wordpress.org/Function_Reference/add_settings_field
		 */
		public function panel_register_setting() {
			$page = $this->get_tab();
			$tab  = isset( $this->options[ $page ] ) ? $this->options[ $page ] : array();

			if ( ! empty( $tab['sections'] ) ) {
				// add sections and fields.
				foreach ( $tab['sections'] as $section_name => $section ) {
					// add the section.
					add_settings_section(
						$section_name,
						$section['title'],
						array( $this, 'panel_section_content' ),
						$this->option_name
					);

					// add the fields.
					foreach ( $section['fields'] as $option_name => $option ) {
						$option['id']        = $option_name;
						$option['label_for'] = $option_name;

						// register settings group.
						register_setting(
							$this->option_group,
							$option_name,
							array( $this, 'panel_sanitize' )
						);

						add_settings_field(
							$option_name,
							$option['title'],
							array( $this, 'panel_field_content' ),
							$this->option_name,
							$section_name,
							$option
						);
					}
				}
			}
		}

		/**
		 * Display sections content
		 *
		 * @param array $section The section options.
		 *
		 * @return void
		 * @access public
		 */
		public function panel_section_content( $section ) {
			$page = $this->get_tab();
			if ( isset( $this->options[ $page ]['sections'][ $section['id'] ]['description'] ) ) {
				echo '<p class="section-description">' . esc_html( $this->options[ $page ]['sections'][ $section['id'] ]['description'] ) . '</p>';
			}
		}

		/**
		 * Sanitize the option's value
		 *
		 * @param array $input The input to sanitize.
		 *
		 * @return array
		 * @access public
		 */
		public function panel_sanitize( $input ) {
			return apply_filters( 'yith_panel_sanitize', $input );
		}

		/**
		 * Get the active tab. If the page isn't provided, the function
		 * will return the first tab name
		 *
		 * @return string
		 * @access protected
		 */
		public function get_tab() {
			$panel_page = ! empty( $_REQUEST['panel_page'] ) ? sanitize_title_for_query( wp_unslash( $_REQUEST['panel_page'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tabs       = array_keys( $this->options );

			return ! empty( $panel_page ) ? $panel_page : $tabs[0];
		}

		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 * @access public
		 */
		public function panel_enqueue() {
			global $pagenow;

			if ( $pagenow === $this->submenu[0] && isset( $_GET['page'] ) && $_GET['page'] === $this->submenu[4] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$plugin_dir_url = plugin_dir_url( __FILE__ );

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'jquery-ui' );

				wp_enqueue_style( 'yith-panel-css', $plugin_dir_url . 'assets/css/yith-panel.css', array( 'wp-color-picker' ), $this->version );
				wp_enqueue_script( 'yith-panel-js', $plugin_dir_url . 'assets/js/yith-panel.js', array( 'jquery', 'wp-color-picker' ), $this->version, true );

				wp_enqueue_media();

				do_action( 'yith_panel_enqueue' );
			}
		}

		/**
		 * Display field content
		 *
		 * @param array $field The field options.
		 *
		 * @return void
		 * @access public
		 */
		public function panel_field_content( $field ) {
			$value = get_option( $field['id'], isset( $field['std'] ) ? $field['std'] : '' );
			$id    = $field['id'];
			$name  = $field['id'];

			switch ( $field['type'] ) {
				case 'text':
					?>
					<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $value ); ?>" class="regular-text code" />
					<?php
					break;

				case 'textarea':
					?>
					<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class='large-text code' rows='10' cols='50'><?php echo esc_html( $value ); ?></textarea>
					<?php
					break;

				case 'checkbox':
					?>
					<label><input type="checkbox" id="<?php echo esc_attr( $id ); ?> " name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( $value, true ); ?> /><span class='description'><?php echo esc_html( $field['description'] ); ?></span></label><br />
					<?php
					break;

				case 'select':
					?>
					<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
						<?php foreach ( $field['options'] as $v => $label ) : ?>
							<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $value, $v ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
					break;

				case 'skin':
					?>
					<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="skin" data-path="<?php echo esc_attr( $field['path'] ); ?>">
						<?php foreach ( $field['options'] as $v => $label ) : ?>
							<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $value, $v ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
					break;

				case 'colorpicker':
					$std = isset( $field['std'] ) ? $field['std'] : '';
					?>
					<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $value ); ?>" class="medium-text code panel-colorpicker" data-default-color="<?php echo esc_attr( $std ); ?>" />
					<?php
					break;

				case 'upload':
					?>
					<div class="uploader">
						<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $value ); ?>" class="regular-text code" />
						<input type="button" name="" id="<?php echo esc_attr( $id ); ?>_button" class="button" value=" <?php esc_html_e( 'Upload', 'yith-maintenance-mode' ); ?>" />
					</div>
					<?php
					break;

				case 'checkboxes':
					$value = ! ! $value && is_array( $value ) ? $value : array();
					?>
					<div class="checkboxes">
						<?php foreach ( $field['options'] as $check_value => $check_label ) : ?>
							<label><input type="checkbox" id="<?php echo esc_attr( $id . '_' . $check_value ); ?> " name="<?php echo esc_attr( $name ); ?>[]" value="<?php echo esc_attr( $check_value ); ?>" <?php checked( in_array( $check_value, $value, true ), true ); ?> /><?php echo esc_html( $check_label ); ?></label><br />
						<?php endforeach; ?>
					</div>
					<?php
					break;

				case 'typography':
					$value = wp_parse_args( $value, $field['std'] );
					?>
					<div class="typography_container typography">
						<div class="option">
							<!-- Size -->
							<div class="spinner_container">
								<input class="typography_size number small-text" type="number" name="<?php echo esc_attr( $name ); ?>[size]" id="<?php echo esc_attr( $id ); ?>-size" value="<?php echo esc_attr( $value['size'] ); ?>" data-min="<?php echo( isset( $field['min'] ) ? esc_attr( $field['min'] ) : '' ); ?>" data-max="<?php echo( isset( $field['max'] ) ? esc_attr( $field['max'] ) : '' ); ?>" />
							</div>

							<!-- Unit -->
							<div class="select-wrapper font-unit">
								<select class="typography_unit" name="<?php echo esc_attr( $name ); ?>[unit]" id="<?php echo esc_attr( $id ); ?>-unit">
									<option value="px" <?php selected( $value['unit'], 'px' ); ?>><?php esc_html_e( 'px', 'yith-maintenance-mode' ); ?></option>
									<option value="em" <?php selected( $value['unit'], 'em' ); ?>><?php esc_html_e( 'em', 'yith-maintenance-mode' ); ?></option>
									<option value="pt" <?php selected( $value['unit'], 'pt' ); ?>><?php esc_html_e( 'pt', 'yith-maintenance-mode' ); ?></option>
									<option value="rem" <?php selected( $value['unit'], 'rem' ); ?>><?php esc_html_e( 'rem', 'yith-maintenance-mode' ); ?></option>
								</select>
							</div>

							<!-- Family -->
							<div class="select-wrapper font-family">
								<select class="typography_family" name="<?php echo esc_attr( $name ); ?>[family]" id="<?php echo esc_attr( $id ); ?>-family" data-instance="false">
									<?php if ( $value['family'] ) : ?>
										<option value="<?php echo esc_attr( stripslashes( $value['family'] ) ); ?>"><?php echo esc_html( $value['family'] ); ?></option>
									<?php else : ?>
										<option value=""><?php esc_html_e( 'Select a font family', 'yith-maintenance-mode' ); ?></option>
									<?php endif ?>
								</select>
							</div>

							<!-- Style -->
							<div class="select-wrapper font-style">
								<select class="typography_style" name="<?php echo esc_attr( $name ); ?>[style]" id="<?php echo esc_attr( $id ); ?>-style">
									<option value="regular" <?php selected( $value['style'], 'regular' ); ?>><?php esc_html_e( 'Regular', 'yith-maintenance-mode' ); ?></option>
									<option value="bold" <?php selected( $value['style'], 'bold' ); ?>><?php esc_html_e( 'Bold', 'yith-maintenance-mode' ); ?></option>
									<option value="extra-bold" <?php selected( $value['style'], 'extra-bold' ); ?>><?php esc_html_e( 'Extra bold', 'yith-maintenance-mode' ); ?></option>
									<option value="italic" <?php selected( $value['style'], 'italic' ); ?>><?php esc_html_e( 'Italic', 'yith-maintenance-mode' ); ?></option>
									<option value="bold-italic" <?php selected( $value['style'], 'bold-italic' ); ?>><?php esc_html_e( 'Italic bold', 'yith-maintenance-mode' ); ?></option>
								</select>
							</div>

							<!-- Color -->
							<input type='text' id='<?php echo esc_attr( $id ); ?>-color' name='<?php echo esc_attr( $name ); ?>[color]' value='<?php echo esc_attr( $value['color'] ); ?>' class='medium-text code panel-colorpicker typography_color' data-default-color='<?php echo esc_attr( $field['std']['color'] ); ?>' />

						</div>
						<div class="clear"></div>
						<div class="font-preview">
							<p>The quick brown fox jumps over the lazy dog</p>
							<!-- Refresh -->
							<div class="refresh_container">
								<button class="refresh"><?php esc_html_e( 'Click to preview', 'yith-maintenance-mode' ); ?></button>
							</div>
						</div>
					</div>
					<?php
					global $yith_panel_if_typography;
					$yith_panel_if_typography = true;
					break;

				default:
					do_action( 'yith_panel_field_' . $field['type'] );
					break;
			}

			if ( isset( $field['description'] ) && '' !== $field['description'] && 'checkbox' !== $field['type'] ) {
				?>
				<p class='description'><?php echo esc_html( $field['description'] ); ?></p>
				<?php
				if ( 'skin' === $field['type'] ) {
					?>
					<div class="skin-preview"></div>
					<?php
				}
			}

		}

	}
}
