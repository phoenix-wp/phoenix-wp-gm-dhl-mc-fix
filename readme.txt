=== Phoenix German Market DHL Multi-Currency Fix for WooCommerce ===
Contributors: phoenixwp
Donate link: https://phoenixwp.com/preise/
Tags: woocommerce, german-market, wpml, wcml, shipping
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 8.2
WC requires at least: 8.0
WC tested up to: 10.9.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WCML multi-currency compatibility fix and German Market DHL international address fix. Requires German Market DHL + WCML (not included).

== Description ==

**This is not a replacement for German Market or WCML.** Free compatibility fixes when both are used with **German Market DHL** (Home Delivery, Packstation, Parcelshops).

= WCML Multi-Currency Compatibility Fix =

German Market stores DHL thresholds and flat shipping rates in your **shop base currency** (for example EUR). WCML converts product prices automatically, but **not** custom shipping methods like German Market DHL. Without this fix, a cart of 75 PLN can incorrectly unlock free shipping when your threshold is 75 EUR.

* **Free-shipping thresholds** — `free_min_amount` and `minimum_amount` converted with WCML before comparing cart totals
* **Shipping costs** — flat DHL rates (for example 5.00 EUR / 15.00 EUR per zone) converted to the active storefront currency

= German Market DHL International Address Fix =

German Market splits address line 1 using a Germany-style pattern (street before number). For EU cross-border DHL labels, number-first addresses (for example France: `56 Bd Example`) can produce an empty street in the DHL API.

* **Bidirectional parsing** — number-first and street-first formats for checkout and DHL label creation

= Requirements =

* [WooCommerce](https://wordpress.org/plugins/woocommerce/)
* [German Market](https://marketpress.de/shop/plugins/woocommerce/woocommerce-german-market/) with the **DHL shipping add-on**
* [WCML](https://wpml.org/documentation/related-projects/woocommerce-multilingual/) **multi-currency** enabled

= Supported DHL methods =

* `dhl_home_delivery`
* `dhl_packstation`
* `dhl_parcelshops`

Works with **any WCML currency** and **any shipping zone** where these methods are configured. Languages are not relevant — only currency and address format matter.

**German description:** [phoenixwp.com/support](https://phoenixwp.com/support/) (DE product copy).

= Settings =

**WooCommerce → GM DHL WCML Fix** (or **PhoenixWP → GM DHL WCML Fix** when PhoenixWP Core is installed)

Toggle each fix independently: thresholds, shipping costs, address parsing.

= Important =

* Configure DHL thresholds and costs in German Market in your **shop base currency** as usual — this plugin converts them on the storefront.
* Does **not** support DPD or other German Market carriers.
* Does **not** add DHL or multi-currency functionality — both must already be set up.

Documentation: https://phoenixwp.com/support/

== Installation ==

1. Install and activate **WooCommerce**, **German Market** (with DHL), and **WCML** multi-currency first.
2. Upload this plugin to `/wp-content/plugins/phoenix-german-market-dhl-multi-currency-fix-for-woocommerce/` or install from the WordPress plugin directory.
3. Activate through the **Plugins** menu.
4. Open **WooCommerce → GM DHL WCML Fix** and confirm the feature toggles are enabled.
5. Test checkout in a secondary currency (for example PLN or CHF) with a cart below and above your free-shipping threshold.

== Frequently Asked Questions ==

= Is this a standalone DHL or multi-currency plugin? =

No. It is a **compatibility fix** for shops that already use **German Market DHL** and **WCML multi-currency**. Neither plugin is included.

= Why is free shipping still wrong without this plugin? =

German Market compares EUR thresholds directly to the cart total in the active currency (for example 75 PLN treated as 75 EUR). This plugin converts the threshold with WCML first.

= Why are shipping costs shown as 5.00 in PLN instead of a converted amount? =

WCML auto-converts WooCommerce core methods (Flat Rate, Free Shipping) but not German Market DHL. Enable **Convert DHL shipping costs** in the plugin settings.

= Does it work with any currency WCML supports? =

Yes. Any currency configured in WCML with exchange rates. The shop base currency is read from WooCommerce settings.

= Does it work with multiple shipping zones? =

Yes. Each zone instance keeps its own DHL cost and thresholds; the fix applies per rate at checkout.

= Why do French addresses fail on DHL labels? =

German Market splits address line 1 using a Germany-style pattern (street before number). EU cross-border orders can end up with an empty street in the DHL API. The address fix normalizes number-first formats for label creation.

= Is there a Pro version? =

No. This plugin is **100% free** (GPL).

= Is the admin interface translated? =

The plugin UI is **English by default**. A **German (de_DE)** language pack is included for the DACH market. Other locales: use [Loco Translate](https://wordpress.org/plugins/loco-translate/) (`loco.xml`) or [GlotPress](https://translate.wordpress.org) after listing.

= Is there a German description? =

Yes — on [phoenixwp.com/support](https://phoenixwp.com/support/). With WordPress set to Deutsch, the admin settings page uses the bundled de_DE translations.

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
