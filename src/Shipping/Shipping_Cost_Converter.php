<?php

/**

 * Converts GM DHL shipping rate costs from EUR to the active WCML currency.

 *

 * @package PhoenixWP\GmDhlMcFix

 */



declare(strict_types=1);



namespace PhoenixWP\GmDhlMcFix\Shipping;



use PhoenixWP\GmDhlMcFix\Currency\Multicurrency_Price_Converter;



defined( 'ABSPATH' ) || exit;



/**

 * Converts WooCommerce shipping rate costs (including proportional taxes).

 */

final class Shipping_Cost_Converter {



	/**

	 * Converts a flat EUR amount to the active storefront currency.

	 *

	 * @param float $amount_eur Amount in shop base currency.

	 */

	public static function convert_amount( float $amount_eur ): float {

		if ( $amount_eur <= 0 ) {

			return 0.0;

		}



		if ( ! self::should_convert() ) {

			return $amount_eur;

		}



		return Threshold_Comparator::threshold_in_active_currency( $amount_eur );

	}



	/**

	 * Converts a shipping rate cost (and taxes) stored in EUR.

	 *

	 * @param \WC_Shipping_Rate $rate Shipping rate.

	 */

	public static function convert_rate( \WC_Shipping_Rate $rate ): \WC_Shipping_Rate {

		if ( ! self::should_convert() ) {

			return $rate;

		}



		$original_cost = (float) $rate->get_cost();



		if ( $original_cost <= 0 ) {

			return $rate;

		}



		$converted_cost = self::convert_amount( $original_cost );

		$rate->set_cost( $converted_cost );



		$taxes = $rate->get_taxes();



		if ( empty( $taxes ) || $original_cost <= 0 ) {

			return $rate;

		}



		$ratio     = $converted_cost / $original_cost;

		$new_taxes = array();



		foreach ( $taxes as $key => $tax ) {

			$new_taxes[ $key ] = (float) $tax * $ratio;

		}



		$rate->set_taxes( $new_taxes );



		return $rate;

	}



	/**

	 * Whether storefront currency differs from the shop base currency.

	 */

	private static function should_convert(): bool {

		return Multicurrency_Price_Converter::client_currency() !== strtoupper( phoenix_gm_dhl_mc_fix_base_currency() );

	}

}


