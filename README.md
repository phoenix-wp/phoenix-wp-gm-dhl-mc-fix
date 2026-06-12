# PhoenixWP Fix — German Market DHL & WCML

Free **compatibility fix** for [German Market](https://marketpress.de/shop/plugins/woocommerce/woocommerce-german-market/) DHL + [WCML](https://wpml.org/documentation/related-projects/woocommerce-multilingual/) multi-currency.

**WCML Multi-Currency Compatibility Fix** — DHL free-shipping thresholds and shipping costs.

**German Market DHL International Address Fix** — street/house-number parsing for DHL labels.

Requires German Market (DHL add-on) and WCML — **not included**.

## Descriptions (EN / DE)

| Language | File |
|----------|------|
| English | [`docs/DESCRIPTION-en.md`](docs/DESCRIPTION-en.md) · wp.org `readme.txt` |
| Deutsch | [`docs/DESCRIPTION-de.md`](docs/DESCRIPTION-de.md) |

**Admin UI:** English source strings only. Loco-ready via `loco.xml` — no bundled translations.

## Fixes

| Feature | Problem without fix |
|---------|---------------------|
| Free-shipping thresholds | 75 PLN unlocks 75 EUR threshold |
| Shipping costs | 5.00 EUR shown as 5.00 PLN |
| DHL labels (EU addresses) | Empty street for `56 Rue Example` |

## Requirements

- WordPress 6.7+
- PHP 8.2+
- WooCommerce 8.0+
- German Market + DHL shipping methods
- WCML multi-currency

## Development

```powershell
cd phoenix-wp-bridge-german-market-wcml
composer install   # optional; includes/autoload-fallback.php works without vendor/
.\scripts\build-release.ps1              # wp.org / GitHub (mit docs/)
.\scripts\build-release.ps1 -Deploy      # Live-Shop — ohne docs/
```

## wp.org

| Item | Value |
|------|--------|
| Suggested SVN slug | `phoenix-wp-bridge-german-market-wcml` |
| Display name | PhoenixWP Fix — German Market DHL & WCML |
| Assets | `wp-org-assets/` → SVN `assets/` only |
| Checklist | `docs/WP-ORG-SUBMISSION.md` |

## Spec

`phoenix-wp-core/docs/plugins/PHOENIX-WP-BRIDGE-GERMAN-MARKET-WCML.md`

## License

GPL-2.0-or-later
