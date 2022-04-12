<?php
/**
 * Plugin Name: Charitable - Currencies per Campaign
 * Plugin URI:
 * Description: Accept donations in different currencies on a per-campaign basis.
 * Version: 1.0.0
 * Author: WP Charitable
 * Author URI: https://www.wpcharitable.com
 * Requires at least: 5.5
 * Tested up to: 5.9.3
 *
 * Text Domain: charitable-campaign-currencies
 * Domain Path: /languages/
 *
 * @package Charitable Currencies per Campaign
 * @author  WP Charitable
 */

namespace Charitable\Pro\CurrenciesPerCampaign;

use Charitable\Extensions\Activation\Activation;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load plugin, but only if Charitable is found and activated.
 *
 * @return false|\Charitable\Pro\CurrenciesPerCampaign\Domain\Bootstrap Whether the class was loaded.
 */
add_action(
	'plugins_loaded',
	function() {
		/* Load Composer packages. */
		require_once( 'vendor/autoload.php' );

		$activation = new Activation( '1.6.53' );

		if ( $activation->ok() ) {
			spl_autoload_register( '\Charitable\Pro\CurrenciesPerCampaign\autoloader' );

			return new \Charitable\Pro\CurrenciesPerCampaign\CampaignCurrencies;
		}

		/* translators: %s: link to activate Charitable */
		$activation->activation_notice = __( 'Charitable Currencies per Campaign requires Charitable! Please <a href="%s">activate it</a> to continue.', 'charitable-campaign-currencies' );

		/* translators: %s: link to install Charitable */
		$activation->installation_notice = __( 'Charitable Currencies per Campaign requires Charitable! Please <a href="%s">install it</a> to continue.', 'charitable-campaign-currencies' );

		/* translators: %s: link to update Charitable */
		$activation->update_notice = __( 'Charitable Currencies per Campaign requires Charitable 1.6.53+! Please <a href="%s">update Charitable</a> to continue.', 'charitable-campaign-currencies' );

		$activation->run();

		return false;
	}
);

/**
 * Set up the plugin autoloader.
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Charitable\Pro\CurrenciesPerCampaign\Foo class
 * from src/Foo.php:
 *
 *      new \Charitable\Pro\CurrenciesPerCampaign\Foo;
 *
 * @since  1.0.0
 *
 * @param  string $class The fully-qualified class name.
 * @return void
 */
function autoloader( $class ) {
	/* Plugin namespace prefix. */
	$prefix = 'Charitable\\Pro\\CurrenciesPerCampaign\\';

	/* Check if the class name uses the namespace prefix. */
	$len = strlen( $prefix );

	if ( 0 !== strncmp( $prefix, $class, $len ) ) {
		return;
	}

	/* Get the relative class name. */
	$relative_class = substr( $class, $len );

	/* Get the file path. */
	$file = __DIR__ . '/src/' . str_replace( '\\', '/', $relative_class ) . '.php';

	/* Bail out if the file doesn't exist. */
	if ( ! file_exists( $file ) ) {
		return;
	}

	/* Finally, require the file. */
	require $file;
}
