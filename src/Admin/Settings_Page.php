<?php
/**
 * Minimal admin settings page.
 *
 * @package PhoenixWP\GmDhlMcFix
 */

declare(strict_types=1);

namespace PhoenixWP\GmDhlMcFix\Admin;

use PhoenixWP\GmDhlMcFix\Install;
use PhoenixWP\GmDhlMcFix\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Settings under WooCommerce or PhoenixWP Core menu.
 */
final class Settings_Page {

	private static ?self $instance = null;

	public const CORE_MENU_SLUG = 'phoenix-wp-core';

	public const PAGE_SLUG = 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce';

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {}

	/**
	 * Registers admin hooks.
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 25 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds submenu: under PhoenixWP Core when present, otherwise under WooCommerce.
	 */
	public function register_menu(): void {
		$page_title = __( 'GM DHL WCML Fix', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' );
		$menu_title = $page_title;
		$parent     = phoenix_gm_dhl_mc_fix_is_core_active() ? self::CORE_MENU_SLUG : 'woocommerce';

		add_submenu_page(
			$parent,
			$page_title,
			$menu_title,
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Registers option group.
	 */
	public function register_settings(): void {
		register_setting(
			'phoenix_gm_dhl_mc_fix',
			Settings::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => Settings::defaults(),
			)
		);
	}

	/**
	 * Sanitizes settings array.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, bool>
	 */
	public function sanitize_settings( $input ): array {
		$defaults = Settings::defaults();
		$output   = Settings::defaults();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		foreach ( array_keys( $defaults ) as $key ) {
			$output[ $key ] = ! empty( $input[ $key ] );
		}

		return $output;
	}

	/**
	 * Renders settings page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings = Settings::get();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'PhoenixWP Fix — German Market DHL & WCML', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?></h1>
			<p class="description">
				<strong><?php esc_html_e( 'WCML Multi-Currency Compatibility Fix', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?></strong>
				<?php esc_html_e( '— DHL free-shipping thresholds and shipping costs.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
				<br />
				<strong><?php esc_html_e( 'German Market DHL International Address Fix', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?></strong>
				<?php esc_html_e( '— street/house-number parsing for DHL labels.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
			</p>
			<p class="description">
				<?php esc_html_e( 'Requires German Market (DHL add-on) and WCML multi-currency — not included.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
			</p>

			<?php $this->render_dependency_notices(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'phoenix_gm_dhl_mc_fix' );
				?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'DHL thresholds', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( Settings::OPTION_KEY ); ?>[convert_dhl_thresholds]" value="1" <?php checked( $settings['convert_dhl_thresholds'] ); ?> />
								<?php esc_html_e( 'Convert free/minimum amounts (EUR) with WCML before comparing cart totals', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Targets dhl_home_delivery, dhl_packstation, dhl_parcelshops.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'DHL shipping costs', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( Settings::OPTION_KEY ); ?>[convert_dhl_shipping_costs]" value="1" <?php checked( $settings['convert_dhl_shipping_costs'] ); ?> />
								<?php esc_html_e( 'Convert configured DHL shipping costs (EUR) with WCML on checkout', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Converts flat rates such as 5,00 EUR / 15,00 EUR to the active currency (WCML does not convert custom GM DHL methods automatically).', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'International address', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( Settings::OPTION_KEY ); ?>[fix_address_parsing]" value="1" <?php checked( $settings['fix_address_parsing'] ); ?> />
								<?php esc_html_e( 'Fix number-first addresses (e.g. France) for DHL checkout and labels', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Outputs dependency warnings.
	 */
	private function render_dependency_notices(): void {
		if ( ! phoenix_gm_dhl_mc_fix_is_german_market_active() ) {
			echo '<div class="notice notice-warning inline"><p>';
			esc_html_e( 'German Market is not active. DHL integrations will not run.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' );
			echo '</p></div>';
		}

		if ( ! phoenix_gm_dhl_mc_fix_is_wcml_active() ) {
			echo '<div class="notice notice-warning inline"><p>';
			esc_html_e( 'WCML multi-currency functions were not detected. Threshold conversion requires WooCommerce Multilingual.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' );
			echo '</p></div>';
		}

		if ( Install::integrations_available() ) {
			echo '<div class="notice notice-success inline"><p>';
			esc_html_e( 'Dependencies OK — compatibility fixes are active.', 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce' );
			echo '</p></div>';
		}
	}
}
