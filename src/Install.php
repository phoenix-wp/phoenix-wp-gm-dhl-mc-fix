<?php
/**
 * Plugin activation lifecycle.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix;

defined( 'ABSPATH' ) || exit;

/**
 * Handles activation and deactivation.
 */
final class Install {

	public const MIN_WP  = '6.7';
	public const MIN_PHP = '8.2';

	private const LEGACY_OPTION_KEY = 'phoenix_wp_bridge_gm_wcml_settings';

	/**
	 * Registers lifecycle hooks.
	 */
	public static function register_hooks(): void {
		register_activation_hook( PHOENIX_GM_DHL_MC_FIX_FILE, array( self::class, 'activate' ) );
		register_deactivation_hook( PHOENIX_GM_DHL_MC_FIX_FILE, array( self::class, 'deactivate' ) );
	}

	/**
	 * Runs on activation.
	 */
	public static function activate(): void {
		if ( ! self::requirements_met() ) {
			deactivate_plugins( PHOENIX_GM_DHL_MC_FIX_BASENAME );
			wp_die(
				esc_html__( 'PhoenixWP Fix — German Market DHL & WCML requires WordPress 6.7+, PHP 8.2+, and WooCommerce.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ),
				esc_html__( 'Plugin Activation Error', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ),
				array( 'back_link' => true )
			);
		}

		self::migrate_legacy_settings();

		if ( ! get_option( Settings::OPTION_KEY ) ) {
			add_option( Settings::OPTION_KEY, Settings::defaults() );
		}

		flush_rewrite_rules();
	}

	/**
	 * Runs on deactivation.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	/**
	 * Whether PHP, WordPress, and WooCommerce are available.
	 */
	public static function requirements_met(): bool {
		global $wp_version;

		if ( version_compare( PHP_VERSION, self::MIN_PHP, '<' ) ) {
			return false;
		}

		if ( isset( $wp_version ) && version_compare( $wp_version, self::MIN_WP, '<' ) ) {
			return false;
		}

		if ( ! phoenix_gm_dhl_mc_fix_is_woocommerce_active() ) {
			return false;
		}

		return true;
	}

	/**
	 * Whether runtime integrations can load (soft deps for admin notice only).
	 */
	public static function integrations_available(): bool {
		return phoenix_gm_dhl_mc_fix_is_german_market_active() && phoenix_gm_dhl_mc_fix_is_wcml_active();
	}

	/**
	 * Copies settings from the pre-review option key when present.
	 */
	private static function migrate_legacy_settings(): void {
		$legacy = get_option( self::LEGACY_OPTION_KEY, null );

		if ( null === $legacy || false === $legacy ) {
			return;
		}

		if ( false === get_option( Settings::OPTION_KEY, false ) ) {
			update_option( Settings::OPTION_KEY, $legacy );
		}

		delete_option( self::LEGACY_OPTION_KEY );
	}
}
