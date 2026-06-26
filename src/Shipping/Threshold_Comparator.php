<?php
/**
 * Compares cart totals with WCML-converted DHL thresholds.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix\Shipping;

use PhoenixWP\GmDhlMcFix\Currency\Multicurrency_Price_Converter;

defined( 'ABSPATH' ) || exit;

/**
 * Threshold comparison helpers for German Market DHL methods.
 */
final class Threshold_Comparator {

	/**
	 * Cart subtotal used for GM free-shipping checks (matches GM DHL logic).
	 */
	public static function cart_subtotal_for_threshold(): float {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return 0.0;
		}

		$total = (float) WC()->cart->get_displayed_subtotal();

		if ( WC()->cart->display_prices_including_tax() ) {
			$total = (float) round(
				$total - ( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() ),
				wc_get_price_decimals()
			);
		} else {
			$total = (float) round( $total - WC()->cart->get_discount_total(), wc_get_price_decimals() );
		}

		return max( 0.0, $total );
	}

	/**
	 * Whether the cart meets a EUR threshold in the active currency.
	 *
	 * @param float $threshold_eur Threshold configured in EUR in German Market.
	 */
	public static function cart_meets_eur_threshold( float $threshold_eur ): bool {
		if ( $threshold_eur <= 0 ) {
			return false;
		}

		$converted_threshold = self::threshold_in_active_currency( $threshold_eur );
		$cart_total          = self::cart_subtotal_for_threshold();

		return $cart_total >= $converted_threshold;
	}

	/**
	 * Converts a GM threshold (stored in EUR) to the active storefront currency.
	 *
	 * @param float $threshold_eur Threshold in shop base currency.
	 */
	public static function threshold_in_active_currency( float $threshold_eur ): float {
		return Multicurrency_Price_Converter::to_active_currency( $threshold_eur );
	}

	/**
	 * Reads threshold from a live WC_Shipping_Method instance (preferred).
	 *
	 * @param object $method   WC_Shipping_Method or GM DHL method.
	 * @param string $property minimum_amount|free_min_amount.
	 */
	public static function read_method_threshold( object $method, string $property ): float {
		if ( isset( $method->$property ) ) {
			$value = (string) $method->$property;

			if ( '' !== $value ) {
				return (float) wc_format_decimal( $value );
			}
		}

		$method_id = isset( $method->id ) ? (string) $method->id : '';

		if ( '' !== $method_id ) {
			$flat = Dhl_Method_Registry::read_flat_option_threshold( $method_id, $property );
			if ( null !== $flat ) {
				return $flat;
			}
		}

		return 0.0;
	}
}
