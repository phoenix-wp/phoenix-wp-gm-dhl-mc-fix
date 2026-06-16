<?php

/**

 * Hooks into German Market DHL shipping rate and availability filters.

 *

 * @package PhoenixWP\BridgeGermanMarketWcml

 */



declare(strict_types=1);



namespace PhoenixWP\BridgeGermanMarketWcml\Shipping;



use PhoenixWP\BridgeGermanMarketWcml\Settings;



defined( 'ABSPATH' ) || exit;



/**

 * Applies WCML-converted thresholds and shipping costs to GM DHL rates.

 */

final class Shipping_Rate_Filters {



	private static ?self $instance = null;



	public static function instance(): self {

		if ( null === self::$instance ) {

			self::$instance = new self();

		}



		return self::$instance;

	}



	private function __construct() {}



	/**

	 * Registers WooCommerce filters.

	 */

	public function init(): void {

		$convert_thresholds = Settings::is_enabled( 'convert_dhl_thresholds' );

		$convert_costs      = Settings::is_enabled( 'convert_dhl_shipping_costs' );



		if ( ! $convert_thresholds && ! $convert_costs ) {

			return;

		}



		if ( $convert_thresholds ) {

			foreach ( Dhl_Method_Registry::methods() as $method_id => $config ) {

				add_filter( $config['rate_cost_hook'], array( $this, 'filter_rate_cost' ), 999, 3 );

				add_filter( $config['is_available_hook'], array( $this, 'filter_is_available' ), 20, 1 );

			}

		}



		// After GM include_free_shipping_methods (priority 15).

		add_filter( 'woocommerce_package_rates', array( $this, 'filter_package_rates' ), 16, 2 );

	}



	/**

	 * Zeroes shipping cost when WCML-adjusted free threshold is met.

	 *

	 * @param float|int|string $cost          Shipping cost.

	 * @param bool             $free_shipping GM free-shipping flag (may be wrong for FX).

	 * @param object           $method        WC_Shipping_Method instance.

	 * @return float|int|string

	 */

	public function filter_rate_cost( $cost, bool $free_shipping, object $method ) {

		unset( $free_shipping );



		$free_min = Threshold_Comparator::read_method_threshold( $method, Dhl_Method_Registry::FIELD_FREE_MIN );



		if ( $free_min <= 0 ) {

			return $cost;

		}



		if ( Threshold_Comparator::cart_meets_eur_threshold( $free_min ) ) {

			return 0;

		}



		if ( (float) $cost <= 0 ) {

			return $this->get_method_base_cost( $method );

		}



		return $cost;

	}



	/**

	 * Corrects method availability when minimum / free thresholds apply.

	 *

	 * @param bool $is_available Whether GM thinks the method is available.

	 */

	public function filter_is_available( bool $is_available ): bool {

		$method = $this->get_current_filter_method();

		if ( null === $method ) {

			return $is_available;

		}



		$minimum  = Threshold_Comparator::read_method_threshold( $method, Dhl_Method_Registry::FIELD_MINIMUM );

		$free_min = Threshold_Comparator::read_method_threshold( $method, Dhl_Method_Registry::FIELD_FREE_MIN );

		$cost     = isset( $method->cost ) ? (float) $method->cost : 0.0;



		if ( $cost <= 0 && $minimum > 0 ) {

			return Threshold_Comparator::cart_meets_eur_threshold( $minimum );

		}



		if ( $cost > 0 && $free_min > 0 ) {

			return Threshold_Comparator::cart_meets_eur_threshold( $free_min );

		}



		return $is_available;

	}



	/**

	 * Ensures package rates reflect converted thresholds and EUR shipping costs.

	 *

	 * @param array<string, \WC_Shipping_Rate> $rates   Package rates.

	 * @param array<string, mixed>             $package Package.

	 * @return array<string, \WC_Shipping_Rate>

	 */

	public function filter_package_rates( array $rates, array $package ): array {

		foreach ( $rates as $rate_id => $rate ) {

			if ( ! $rate instanceof \WC_Shipping_Rate ) {

				continue;

			}



			if ( ! Dhl_Method_Registry::is_dhl_method( (string) $rate->get_method_id() ) ) {

				continue;

			}



			$method = $this->resolve_shipping_method( (string) $rate->get_method_id(), $rate->get_instance_id() );

			$rates[ $rate_id ] = $this->process_dhl_rate( $rate, $method );

		}



		if ( Settings::is_enabled( 'convert_dhl_thresholds' ) ) {

			$rates = $this->restore_missing_paid_dhl_rates( $rates, $package );

		}



		return $rates;

	}



	/**

	 * Applies threshold correction and WCML cost conversion to one rate.

	 *

	 * @param \WC_Shipping_Rate   $rate   Shipping rate.

	 * @param object|null         $method WC_Shipping_Method instance.

	 */

	private function process_dhl_rate( \WC_Shipping_Rate $rate, ?object $method ): \WC_Shipping_Rate {

		if ( Settings::is_enabled( 'convert_dhl_thresholds' ) && null !== $method ) {

			$rate = $this->apply_threshold_to_rate( $rate, $method );

		}



		if ( Settings::is_enabled( 'convert_dhl_shipping_costs' ) ) {

			$rate = Shipping_Cost_Converter::convert_rate( $rate );

		}



		return $rate;

	}



	/**

	 * Applies WCML threshold logic to a single shipping rate.

	 *

	 * @param \WC_Shipping_Rate $rate   Shipping rate.

	 * @param object            $method WC_Shipping_Method instance.

	 */

	private function apply_threshold_to_rate( \WC_Shipping_Rate $rate, object $method ): \WC_Shipping_Rate {

		$free_min = Threshold_Comparator::read_method_threshold( $method, Dhl_Method_Registry::FIELD_FREE_MIN );



		if ( $free_min <= 0 ) {

			return $rate;

		}



		if ( Threshold_Comparator::cart_meets_eur_threshold( $free_min ) ) {

			$rate->set_cost( 0 );

			$rate->set_taxes( array() );

		} elseif ( (float) $rate->get_cost() <= 0 ) {

			$rate->set_cost( $this->get_method_base_cost( $method ) );

		}



		return $rate;

	}



	/**

	 * Re-adds DHL rates removed by GM when free shipping was wrongly detected.

	 *

	 * @param array<string, \WC_Shipping_Rate> $rates   Package rates.

	 * @param array<string, mixed>             $package Package.

	 * @return array<string, \WC_Shipping_Rate>

	 */

	private function restore_missing_paid_dhl_rates( array $rates, array $package ): array {

		if ( ! class_exists( \WC_Shipping_Zones::class ) ) {

			return $rates;

		}



		$zone = \WC_Shipping_Zones::get_zone_matching_package( $package );



		foreach ( $zone->get_shipping_methods( true ) as $method ) {

			if ( ! $method instanceof \WC_Shipping_Method || ! Dhl_Method_Registry::is_dhl_method( $method->id ) ) {

				continue;

			}



			$free_min = Threshold_Comparator::read_method_threshold( $method, Dhl_Method_Registry::FIELD_FREE_MIN );

			if ( $free_min <= 0 || Threshold_Comparator::cart_meets_eur_threshold( $free_min ) ) {

				continue;

			}



			if ( $this->package_has_rate_for_method( $rates, $method->id, (int) $method->instance_id ) ) {

				continue;

			}



			foreach ( $method->get_rates_for_package( $package ) as $rate ) {

				if ( ! $rate instanceof \WC_Shipping_Rate ) {

					continue;

				}



				$rates[ $rate->get_id() ] = $this->process_dhl_rate( $rate, $method );

			}

		}



		return $rates;

	}



	/**

	 * Checks whether a package already contains a rate for a method instance.

	 *

	 * @param array<string, \WC_Shipping_Rate> $rates       Package rates.

	 * @param string                           $method_id   Method slug.

	 * @param int                              $instance_id Instance ID.

	 */

	private function package_has_rate_for_method( array $rates, string $method_id, int $instance_id ): bool {

		foreach ( $rates as $rate ) {

			if ( ! $rate instanceof \WC_Shipping_Rate ) {

				continue;

			}



			if ( $rate->get_method_id() === $method_id && (int) $rate->get_instance_id() === $instance_id ) {

				return true;

			}

		}



		return false;

	}



	/**

	 * Reads the flat shipping cost configured on a GM DHL method instance (EUR).

	 *

	 * @param object $method WC_Shipping_Method instance.

	 */

	private function get_method_base_cost( object $method ): float {

		if ( method_exists( $method, 'get_option' ) ) {

			return (float) wc_format_decimal( (string) $method->get_option( 'cost', '0' ) );

		}



		if ( isset( $method->cost ) ) {

			return (float) wc_format_decimal( (string) $method->cost );

		}



		return 0.0;

	}



	/**

	 * Resolves WC_Shipping_Method from current filter hook name.

	 */

	private function get_current_filter_method(): ?object {

		$hook = current_filter();



		foreach ( Dhl_Method_Registry::methods() as $method_id => $config ) {

			if ( $config['is_available_hook'] !== $hook ) {

				continue;

			}



			return $this->resolve_shipping_method( $method_id );

		}



		return null;

	}



	/**

	 * Loads a shipping method instance from WC_Shipping.

	 *

	 * @param string   $method_id   Method slug.

	 * @param int|null $instance_id Instance ID.

	 */

	private function resolve_shipping_method( string $method_id, ?int $instance_id = null ): ?object {

		if ( null !== $instance_id && $instance_id > 0 && class_exists( \WC_Shipping_Zones::class ) ) {

			$instance = \WC_Shipping_Zones::get_shipping_method( $instance_id );



			if ( $instance instanceof \WC_Shipping_Method && $instance->id === $method_id ) {

				return $instance;

			}

		}



		if ( ! function_exists( 'WC' ) || ! WC()->shipping() ) {

			return null;

		}



		$methods = WC()->shipping()->get_shipping_methods();



		if ( ! isset( $methods[ $method_id ] ) ) {

			return null;

		}



		return $methods[ $method_id ];

	}

}


