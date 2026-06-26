<?php
/**
 * Uninstall cleanup.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'phoenix_gm_dhl_mc_fix_settings' );
delete_option( 'phoenix_wp_bridge_gm_wcml_settings' );
