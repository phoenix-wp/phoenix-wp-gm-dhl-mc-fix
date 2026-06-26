<?php
/**
 * Boots German Market DHL integrations.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix\Integration;

use PhoenixWP\GmDhlMcFix\Address\Checkout_Address_Fix;
use PhoenixWP\GmDhlMcFix\Shipping\Shipping_Rate_Filters;

defined( 'ABSPATH' ) || exit;

/**
 * Registers shipping and address hooks when dependencies are present.
 */
final class German_Market_Dhl {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {}

	/**
	 * Initializes integrations on woocommerce_init.
	 */
	public function init(): void {
		Checkout_Address_Fix::instance()->init();

		if ( phoenix_gm_dhl_mc_fix_is_wcml_active() ) {
			Shipping_Rate_Filters::instance()->init();
		}

		/**
		 * Fires when GM DHL bridge integrations are registered.
		 */
		do_action( 'phoenix_gm_dhl_mc_fix_integrations_loaded' );
	}
}
