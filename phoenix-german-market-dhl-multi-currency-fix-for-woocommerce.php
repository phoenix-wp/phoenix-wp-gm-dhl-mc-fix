<?php
/**
 * Plugin Name:       Phoenix German Market DHL Multi-Currency Fix for WooCommerce
 * Plugin URI:        https://phoenixwp.com/support/
 * Description:       WCML multi-currency compatibility fix and German Market DHL international address fix. Requires German Market DHL + WCML (not included).
 * Version:           1.0.0
 * Requires at least: 6.7
 * Tested up to:      7.0
 * Requires PHP:      8.2
 * Requires Plugins:  woocommerce
 * WC requires at least: 8.0
 * WC tested up to:   10.9.1
 * Author:            PhoenixWP
 * Author URI:        https://phoenixwp.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       phoenix-german-market-dhl-multi-currency-fix-for-woocommerce
 * Domain Path:       /languages
 *
 * @package PhoenixWP\GmDhlMcFix
 */

defined( 'ABSPATH' ) || exit;

define( 'PHOENIX_GM_DHL_MC_FIX_VERSION', '1.0.0' );
define( 'PHOENIX_GM_DHL_MC_FIX_FILE', __FILE__ );
define( 'PHOENIX_GM_DHL_MC_FIX_PATH', plugin_dir_path( __FILE__ ) );
define( 'PHOENIX_GM_DHL_MC_FIX_URL', plugin_dir_url( __FILE__ ) );
define( 'PHOENIX_GM_DHL_MC_FIX_BASENAME', plugin_basename( __FILE__ ) );

$phoenix_gm_dhl_mc_fix_autoload = PHOENIX_GM_DHL_MC_FIX_PATH . 'vendor/autoload.php';

if ( is_readable( $phoenix_gm_dhl_mc_fix_autoload ) ) {
	require_once $phoenix_gm_dhl_mc_fix_autoload;
} else {
	require_once PHOENIX_GM_DHL_MC_FIX_PATH . 'includes/autoload-fallback.php';
	phoenix_gm_dhl_mc_fix_register_autoload_fallback();
}

\PhoenixWP\GmDhlMcFix\Install::register_hooks();

\PhoenixWP\GmDhlMcFix\Plugin::instance();
