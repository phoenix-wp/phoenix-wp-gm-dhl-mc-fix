<?php
/**
 * Checkout address normalization for DHL (P1 stub).
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix\Address;

use PhoenixWP\GmDhlMcFix\Settings;
use PhoenixWP\GmDhlMcFix\Shipping\Dhl_Method_Registry;

defined( 'ABSPATH' ) || exit;

/**
 * Normalizes checkout addresses before GM DHL house-number validation.
 */
final class Checkout_Address_Fix {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {}

	/**
	 * Registers checkout and DHL label hooks.
	 */
	public function init(): void {
		if ( ! Settings::is_enabled( 'fix_address_parsing' ) ) {
			return;
		}

		add_filter( 'woocommerce_checkout_posted_data', array( $this, 'normalize_posted_addresses' ), 20 );
		add_filter( 'wgm_shipping_dhl_build_address_from_order', array( $this, 'fix_dhl_consignee_street' ), 10, 2 );
	}

	/**
	 * Normalizes billing/shipping address_1 when a DHL method is selected.
	 *
	 * @param array<string, mixed> $data Posted checkout data.
	 * @return array<string, mixed>
	 */
	public function normalize_posted_addresses( array $data ): array {
		if ( empty( $data['shipping_method'] ) || ! is_array( $data['shipping_method'] ) ) {
			return $data;
		}

		$chosen = (string) ( $data['shipping_method'][0] ?? '' );
		if ( ! $this->is_dhl_rate_id( $chosen ) ) {
			return $data;
		}

		if ( ! empty( $data['billing_address_1'] ) ) {
			$data['billing_address_1'] = Street_Parser::normalize_for_german_market( (string) $data['billing_address_1'] );
		}

		if ( ! empty( $data['shipping_address_1'] ) ) {
			$data['shipping_address_1'] = Street_Parser::normalize_for_german_market( (string) $data['shipping_address_1'] );
		}

		return $data;
	}

	/**
	 * Ensures DHL consignee addressStreet is set for EU cross-border orders.
	 *
	 * German Market treats EU-to-EU shipments as domestic and splits address_1 with a
	 * DE-only regex. Number-first lines (e.g. FR "56 Bd Jean Mermoz") then yield an
	 * empty addressStreet at label creation.
	 *
	 * @param array<string, mixed> $address DHL consignee payload.
	 * @param \WC_Order             $order   Order.
	 * @return array<string, mixed>
	 */
	public function fix_dhl_consignee_street( array $address, \WC_Order $order ): array {
		$current_street = trim( (string) ( $address['addressStreet'] ?? '' ) );
		if ( '' !== $current_street ) {
			return $address;
		}

		$line = trim( $order->get_shipping_address_1() );
		if ( '' === $line ) {
			return $address;
		}

		$address['addressStreet'] = Street_Parser::to_street_with_number( $line );

		return $address;
	}

	/**
	 * Checks whether a rate ID belongs to GM DHL.
	 *
	 * @param string $rate_id e.g. dhl_home_delivery:3
	 */
	private function is_dhl_rate_id( string $rate_id ): bool {
		foreach ( Dhl_Method_Registry::method_ids() as $method_id ) {
			if ( str_starts_with( $rate_id, $method_id ) ) {
				return true;
			}
		}

		return false;
	}
}
