<?php
/**
 * German Market DHL method IDs and WooCommerce option key patterns.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix\Shipping;

defined( 'ABSPATH' ) || exit;

/**
 * Registry of GM DHL shipping methods and threshold field names.
 *
 * WooCommerce stores instance settings as serialized arrays under
 * woocommerce_{method_id}_{instance_id}_settings. Vitalstoffversand also
 * exposes flat option keys (legacy / admin UI) such as:
 * woocommerce_dhl_home_delivery_minimum_amount
 */
final class Dhl_Method_Registry {

	public const METHOD_HOME       = 'dhl_home_delivery';
	public const METHOD_PACKSTATION = 'dhl_packstation';
	public const METHOD_PARCELS    = 'dhl_parcelshops';

	public const FIELD_MINIMUM     = 'minimum_amount';
	public const FIELD_FREE_MIN    = 'free_min_amount';

	/**
	 * Known GM DHL methods and their rate-cost filter hooks.
	 *
	 * @return array<string, array{title: string, rate_cost_hook: string, is_available_hook: string, example_option_keys: string[]}>
	 */
	public static function methods(): array {
		return array(
			self::METHOD_HOME => array(
				'title'              => 'DHL Home Delivery',
				'rate_cost_hook'     => 'woocommerce_dhl_home_delivery_shipping_rate_cost',
				'is_available_hook'  => 'woocommerce_shipping_dhl_home_delivery_is_available',
				'example_option_keys' => array(
					'woocommerce_dhl_home_delivery_minimum_amount',
					'woocommerce_dhl_home_delivery_free_min_amount',
				),
			),
			self::METHOD_PACKSTATION => array(
				'title'              => 'DHL Packstation',
				'rate_cost_hook'     => 'woocommerce_dhl_packstation_shipping_rate_cost',
				'is_available_hook'  => 'woocommerce_shipping_dhl_packstation_is_available',
				'example_option_keys' => array(
					'woocommerce_dhl_packstation_minimum_amount',
					'woocommerce_dhl_packstation_free_min_amount',
				),
			),
			self::METHOD_PARCELS => array(
				'title'              => 'DHL Parcelshops',
				'rate_cost_hook'     => 'woocommerce_dhl_parcelshops_shipping_rate_cost',
				'is_available_hook'  => 'woocommerce_shipping_dhl_parcelshops_is_available',
				'example_option_keys' => array(
					'woocommerce_dhl_parcelshops_minimum_amount',
					'woocommerce_dhl_parcelshops_free_min_amount',
				),
			),
		);
	}

	/**
	 * All registered method IDs.
	 *
	 * @return string[]
	 */
	public static function method_ids(): array {
		return array_keys( self::methods() );
	}

	/**
	 * Whether a shipping method belongs to GM DHL.
	 *
	 * @param string $method_id WooCommerce shipping method ID.
	 */
	public static function is_dhl_method( string $method_id ): bool {
		return in_array( $method_id, self::method_ids(), true );
	}

	/**
	 * Reads a flat wp_option threshold (EUR) if present.
	 *
	 * @param string $method_id Method slug.
	 * @param string $field     minimum_amount|free_min_amount.
	 */
	public static function read_flat_option_threshold( string $method_id, string $field ): ?float {
		$config = self::methods()[ $method_id ] ?? null;
		if ( null === $config ) {
			return null;
		}

		foreach ( $config['example_option_keys'] as $option_key ) {
			if ( ! str_contains( $option_key, $field ) ) {
				continue;
			}

			$value = get_option( $option_key, '' );
			if ( '' === $value || null === $value ) {
				continue;
			}

			return (float) wc_format_decimal( (string) $value );
		}

		return null;
	}
}
