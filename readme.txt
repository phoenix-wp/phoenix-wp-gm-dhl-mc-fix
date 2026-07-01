=== Phoenix German Market DHL Multi-Currency Fix for WooCommerce ===
Contributors: phoenixwp
Donate link: https://phoenixwp.com/support/
Tags: woocommerce, german-market, wpml, wcml, shipping
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 8.2
WC requires at least: 8.0
WC tested up to: 10.9.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free compatibility fix for German Market DHL + WCML multi-currency: correct thresholds, shipping rates, and EU address parsing.

== Description ==

**Not a replacement for German Market or WCML.** This free bridge plugin fixes two real-world issues when you run **German Market DHL** (Home Delivery, Packstation, Parcelshops) together with **WCML multi-currency**.

= WCML multi-currency fix =

German Market stores DHL free-shipping and minimum-order thresholds in your **shop base currency** (for example EUR). WCML converts product prices, but **not** custom German Market DHL shipping methods. Without this fix, a cart of 75 PLN can incorrectly unlock free shipping when your threshold is 75 EUR.

* **Free-shipping thresholds** — converts `free_min_amount` and `minimum_amount` with WCML before comparing cart totals
* **Flat DHL rates** — converts zone costs (for example 5.00 EUR / 15.00 EUR) to the active storefront currency
* **Any WCML currency** and **any shipping zone** where the supported DHL methods are configured

= International address fix =

German Market splits address line 1 using a Germany-style pattern (street before number). For EU cross-border DHL labels, number-first addresses (for example France: `12 Rue Example`) can produce an empty street in the DHL API.

* **Bidirectional parsing** — number-first and street-first formats for checkout and DHL label creation

= Requirements (not included) =

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)
* [German Market](https://marketpress.de/shop/plugins/woocommerce/woocommerce-german-market/) with the **DHL shipping add-on**
* [WCML](https://wpml.org/documentation/related-projects/woocommerce-multilingual/) **multi-currency** enabled

= Settings =

**WooCommerce → GM DHL WCML Fix** — enable threshold conversion, shipping-cost conversion, and address parsing independently.

= Supported DHL method IDs =

`dhl_home_delivery`, `dhl_packstation`, `dhl_parcelshops`

Configure thresholds and costs in German Market in your **shop base currency** as usual — this plugin converts them on the storefront. Does **not** add DHL or multi-currency functionality. Does **not** support DPD or other German Market carriers.

More on [phoenixwp.com](https://phoenixwp.com/phoenix-wp-gm-dhl-wcml-fix/) · Support: [phoenixwp.com/support](https://phoenixwp.com/support/)

== Installation ==

1. Install and activate **WooCommerce**, **German Market** (with DHL), and **WCML** multi-currency first.
2. Upload this plugin to `/wp-content/plugins/phoenix-german-market-dhl-multi-currency-fix-for-woocommerce/` or install from the WordPress plugin directory.
3. Activate through the **Plugins** menu.
4. Open **WooCommerce → GM DHL WCML Fix** and confirm the feature toggles are enabled.
5. Test checkout in a secondary currency (for example PLN or CHF) with a cart below and above your free-shipping threshold.

== Frequently Asked Questions ==

= Is this a standalone DHL or multi-currency plugin? =

No. It is a **compatibility fix** for shops that already use **German Market DHL** and **WCML multi-currency**. Neither plugin is included.

= Why is free shipping wrong without this plugin? =

German Market compares EUR thresholds directly to the cart total in the active currency (for example 75 PLN treated as 75 EUR). This plugin converts the threshold with WCML first.

= Why are shipping costs shown as 5.00 in PLN instead of a converted amount? =

WCML auto-converts WooCommerce core methods (Flat Rate, Free Shipping) but not German Market DHL. Enable **Convert DHL shipping costs** in the plugin settings.

= Does it work with any currency WCML supports? =

Yes — any currency configured in WCML with exchange rates. The shop base currency is read from WooCommerce settings.

= Does it work with multiple shipping zones? =

Yes. Each zone instance keeps its own DHL cost and thresholds; the fix applies per rate at checkout.

= Why do French or other EU addresses fail on DHL labels? =

German Market expects street-before-number formatting. Number-first addresses can leave the street field empty in the DHL API. The address fix normalizes both formats for label creation.

= Is there a Pro version? =

No. This plugin is **100% free** (GPL). Download on [WordPress.org](https://wordpress.org/plugins/phoenix-german-market-dhl-multi-currency-fix-for-woocommerce/) or from our [plugin directory](https://phoenixwp.com/plugins/).

= Is the admin interface translated? =

The plugin UI is **English by default**. A **German (de_DE)** language pack is bundled. Other locales: [Loco Translate](https://wordpress.org/plugins/loco-translate/) or GlotPress.

== Screenshots ==

1. Settings — toggle WCML threshold, shipping cost, and address fixes
2. Checkout with converted DHL shipping cost in a secondary currency

== Changelog ==

= 1.0.0 =
* Initial wordpress.org release.
* WCML conversion for DHL free-shipping and minimum thresholds (German Market DHL).
* WCML conversion for DHL flat shipping costs.
* International address parsing fix for DHL label API (checkout + `wgm_shipping_dhl_build_address_from_order`).
* Bundled German (de_DE) admin translations for DACH shops.
* HPOS compatible. Tested with WordPress 7.0 and WooCommerce 10.9.1 (German Market DHL 3.58.x).

== Upgrade Notice ==

= 1.0.0 =
Initial release on WordPress.org.
