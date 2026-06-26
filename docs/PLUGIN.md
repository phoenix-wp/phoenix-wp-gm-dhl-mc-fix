# PhoenixWP Fix — German Market DHL & WCML — Technical Reference

> **Display name:** PhoenixWP Fix — German Market DHL & WCML  
> Kanonische Spec: `phoenix-wp-core/docs/plugins/PHOENIX-WP-GM-DHL-MC-FIX.md`

## Architecture

```
src/
  Integration/German_Market_Dhl.php   — boots hooks on woocommerce_init
  Shipping/
    Dhl_Method_Registry.php           — method IDs + option key patterns
    Threshold_Comparator.php          — cart vs EUR threshold (WCML)
    Shipping_Cost_Converter.php         — WCML conversion for DHL rate costs
    Shipping_Rate_Filters.php         — GM DHL filter hooks
  Currency/Multicurrency_Price_Converter.php — WCML price conversion wrapper
  Address/
    Street_Parser.php                 — bidirectional street/house no.
    Checkout_Address_Fix.php          — normalize before GM validation
  Admin/Settings_Page.php
  Plugin.php, Install.php, Settings.php
```

## Hooks used

| Hook | Purpose |
|------|---------|
| `woocommerce_dhl_*_shipping_rate_cost` | Zero cost when converted free threshold met |
| `woocommerce_shipping_dhl_*_is_available` | Fix minimum/free availability |
| `woocommerce_package_rates` | Fallback rate cost adjustment |
| `woocommerce_checkout_posted_data` | Address normalize before GM validation |
| `wgm_shipping_dhl_build_address_from_order` | Fix empty consignee street on DHL labels (EU cross-border) |
| `woocommerce_package_rates` (priority 16) | WCML threshold fix + EUR shipping cost conversion for GM DHL |

## Extension hooks

- `phoenix_gm_dhl_mc_fix_loaded`
- `phoenix_gm_dhl_mc_fix_integrations_loaded`

## Version 1.0.0

wp.org-ready free compatibility fix. **UI: English only** (Loco-ready, no bundled translations). Marketing: `docs/DESCRIPTION-en.md` + `docs/DESCRIPTION-de.md`.
