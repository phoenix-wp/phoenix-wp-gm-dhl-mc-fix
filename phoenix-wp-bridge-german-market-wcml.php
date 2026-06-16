<?php

/**

 * Plugin Name:       PhoenixWP Fix — German Market DHL & WCML

 * Plugin URI:        https://wordpress.org/plugins/phoenix-wp-bridge-german-market-wcml/

 * Description:       WCML multi-currency compatibility fix and German Market DHL international address fix. Requires German Market DHL + WCML (not included).

 * Version:           1.0.0

 * Requires at least: 6.7

 * Requires PHP:      8.2

 * Requires Plugins:  woocommerce

 * Author:            PhoenixWP

 * Author URI:        https://phoenixwp.com

 * License:           GPL-2.0-or-later

 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html

 * Text Domain:       phoenix-wp-bridge-german-market-wcml

 * Domain Path:       /languages

 *

 * @package PhoenixWP\BridgeGermanMarketWcml

 */



defined( 'ABSPATH' ) || exit;



define( 'PHOENIX_WP_BRIDGE_GM_WCML_VERSION', '1.0.0' );

define( 'PHOENIX_WP_BRIDGE_GM_WCML_FILE', __FILE__ );

define( 'PHOENIX_WP_BRIDGE_GM_WCML_PATH', plugin_dir_path( __FILE__ ) );

define( 'PHOENIX_WP_BRIDGE_GM_WCML_URL', plugin_dir_url( __FILE__ ) );

define( 'PHOENIX_WP_BRIDGE_GM_WCML_BASENAME', plugin_basename( __FILE__ ) );



$autoload = PHOENIX_WP_BRIDGE_GM_WCML_PATH . 'vendor/autoload.php';



if ( is_readable( $autoload ) ) {

	require_once $autoload;

} else {

	require_once PHOENIX_WP_BRIDGE_GM_WCML_PATH . 'includes/autoload-fallback.php';

	phoenix_wp_bridge_gm_wcml_register_autoload_fallback();

}



\PhoenixWP\BridgeGermanMarketWcml\Install::register_hooks();



\PhoenixWP\BridgeGermanMarketWcml\Plugin::instance();


