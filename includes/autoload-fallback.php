<?php
/**
 * PSR-4 fallback autoloader when Composer vendor/ is not present.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the extension PSR-4 autoloader.
 */
function phoenix_gm_dhl_mc_fix_register_autoload_fallback(): void {
	spl_autoload_register(
		static function ( string $class ): void {
			$prefix = 'PhoenixWP\\GmDhlMcFix\\';

			if ( ! str_starts_with( $class, $prefix ) ) {
				return;
			}

			$relative = substr( $class, strlen( $prefix ) );
			$file     = PHOENIX_GM_DHL_MC_FIX_PATH . 'src/' . str_replace( '\\', '/', $relative ) . '.php';

			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}
	);

	require_once PHOENIX_GM_DHL_MC_FIX_PATH . 'src/functions.php';
}
