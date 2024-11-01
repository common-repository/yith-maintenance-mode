<?php
/**
 * Maintenance Mode page - Skin 3
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH Maintenance Mode
 * @version 1.0.1
 *
 * @var $custom
 * @var $logo
 * @var $message
 * @var $newsletter
 * @var $p_font
 * @var $socials
 * @var $title
 * @var $title_font
 * @var $stylesheet_url
 */

$background_role = array();
if ( ! empty( $background['color'] ) ) {
	$background_role[] = "background-color: {$background['color']};";
}
if ( ! empty( $background['image'] ) ) {
	$background_role[] = "background-image: url('{$background['image']}');";
}
if ( ! empty( $background['repeat'] ) ) {
	$background_role[] = "background-repeat: {$background['repeat']};";
}
if ( ! empty( $background['position'] ) ) {
	$background_role[] = "background-position: {$background['position']};";
}
if ( ! empty( $background['attachment'] ) ) {
	$background_role[] = "background-attachment: {$background['attachment']};";
}

?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<!-- START HEAD -->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<link rel="Shortcut Icon" type="image/x-icon" href="<?php echo esc_url( home_url() ); ?>/favicon.ico" />
	<link rel="stylesheet" href="<?php echo esc_url( yith_google_fonts_url() ); //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo esc_url( $stylesheet_url ); //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>" type="text/css" />
	<style type="text/css">
		body {
		<?php echo wp_kses( implode( "\n", $background_role ), 'entities' ); ?>
		}

		.logo .tagline {
		<?php echo wp_kses( $logo['tagline_font'], 'entities' ); ?>
		}

		h1, h2, h3, h4, h5, h6 {
		<?php echo wp_kses( $title_font, 'entities' ); ?>
		}

		p, li {
		<?php echo wp_kses( $p_font, 'entities' ); ?>
		}

		form.newsletter input.text-field {
		<?php echo wp_kses( $newsletter['email_font'], 'entities' ); ?>
		}

		form.newsletter input.submit-field {
			background: <?php echo wp_kses( $newsletter['submit']['color'], 'entities' ); ?>;
		<?php echo wp_kses( $newsletter['submit']['font'], 'entities' ); ?>
		}

		form.newsletter .submit:hover input.submit-field {
			background: <?php echo wp_kses( $newsletter['submit']['hover'], 'entities' ); ?>;
		}

		<?php echo wp_kses( $custom, 'entities' ); ?>
	</style>
</head>
<!-- END HEAD -->
<!-- START BODY -->
<body <?php body_class(); ?>>

<div class="container">
	<a class="logo" href="<?php echo esc_url( site_url() ); ?>">
		<img src="<?php echo esc_url( $logo['image'] ); ?>" alt="Logo" />
		<?php if ( ! empty( $logo['tagline'] ) ) : ?>
			<p class="tagline"><?php echo wp_kses_post( $logo['tagline'] ); ?></p>
		<?php endif; ?>
	</a>

	<div class="yit-box">

		<div class="message">
			<?php echo wp_kses_post( $message ); ?>
		</div>

		<?php if ( $newsletter['enabled'] ) : ?>

			<?php if ( $title ) : ?>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
			<?php endif ?>

			<form method="<?php echo esc_attr( $newsletter['form_method'] ); ?>" action="<?php echo esc_url( $newsletter['form_action'] ); ?>" class="newsletter">
				<fieldset>
					<input type="text" name="<?php echo esc_attr( $newsletter['email_name'] ); ?>" id="<?php echo esc_attr( $newsletter['email_name'] ); ?>" class="email-field text-field" placeholder="<?php echo esc_attr( $newsletter['email_label'] ); ?>" />
					<div class="submit"><input type="submit" value="<?php echo esc_attr( $newsletter['submit']['label'] ); ?>" class="submit-field" /></div>
					<?php foreach ( $newsletter['hidden_fields'] as $field_name => $field_value ) : ?>
						<input type="hidden" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>" />
					<?php endforeach; ?>
				</fieldset>
			</form>
		<?php endif; ?>
	</div>

	<div class="socials">
		<?php foreach ( $socials as $social => $url ) : ?>
			<?php
			if ( empty( $url ) ) {
				continue;
			}

			if ( 'email' === $social ) {
				$url = 'mailto:' . $url;
			}
			if ( 'skype' === $social ) {
				$url = 'https://myskype.info/' . str_replace( '@', '', $url );
			}
			?>
			<a class="social <?php echo esc_attr( $social ); ?>" href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( ucfirst( $social ) ); ?></a>
		<?php endforeach; ?>
	</div>

</div>

<?php wp_footer(); ?>
</body>
</html>
