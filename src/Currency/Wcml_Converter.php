<?php

/**

 * Converts EUR shop amounts into the active WCML currency.

 *

 * @package PhoenixWP\BridgeGermanMarketWcml

 */



declare(strict_types=1);



namespace PhoenixWP\BridgeGermanMarketWcml\Currency;



defined( 'ABSPATH' ) || exit;



/**

 * WCML price conversion wrapper.

 */

final class Wcml_Converter {



	/**

	 * Returns the active storefront currency code (WCML-aware).

	 */

	public static function client_currency(): string {

		$currency = apply_filters( 'wcml_price_currency', null );

		if ( is_string( $currency ) && strlen( $currency ) === 3 ) {

			return strtoupper( $currency );

		}



		global $woocommerce_wpml;



		if ( is_object( $woocommerce_wpml ) && isset( $woocommerce_wpml->multi_currency ) ) {

			$client = $woocommerce_wpml->multi_currency->get_client_currency();

			if ( is_string( $client ) && strlen( $client ) === 3 ) {

				return strtoupper( $client );

			}

		}



		if ( function_exists( 'get_woocommerce_currency' ) ) {

			return strtoupper( (string) get_woocommerce_currency() );

		}



		return strtoupper( phoenix_wp_bridge_gm_wcml_base_currency() );

	}



	/**

	 * Converts an amount stored in shop base currency to the active currency.

	 *

	 * @param float  $amount_eur Amount in base currency (EUR).

	 * @param string $to_currency Target currency code (defaults to active).

	 */

	public static function to_active_currency( float $amount_eur, string $to_currency = '' ): float {

		if ( $amount_eur <= 0 ) {

			return 0.0;

		}



		$base = strtoupper( phoenix_wp_bridge_gm_wcml_base_currency() );

		$to   = '' !== $to_currency ? strtoupper( $to_currency ) : self::client_currency();



		if ( $to === $base ) {

			return $amount_eur;

		}



		if ( function_exists( 'wcml_convert_price' ) ) {

			$converted = wcml_convert_price( $amount_eur, $to );



			if ( is_numeric( $converted ) && (float) $converted > 0 ) {

				return (float) $converted;

			}

		}



		/**

		 * Fallback for older WCML versions.

		 *

		 * @param float  $amount_eur    Base currency amount.

		 * @param string $to_currency Target currency.

		 */

		$converted = apply_filters( 'wcml_raw_price_amount', $amount_eur, $to );



		return is_numeric( $converted ) ? (float) $converted : $amount_eur;

	}

}


