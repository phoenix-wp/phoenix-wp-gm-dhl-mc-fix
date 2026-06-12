# wordpress.org submission — PhoenixWP Fix (German Market DHL & WCML)

> **Free plugin · no Pro tier**  
> **Suggested SVN slug:** `phoenix-wp-bridge-german-market-wcml`  
> **Display name:** PhoenixWP Fix — German Market DHL & WCML

Canonical spec: `phoenix-wp-core/docs/plugins/PHOENIX-WP-BRIDGE-GERMAN-MARKET-WCML.md`

---

## Positioning (important for review)

This plugin is a **compatibility fix / extension**, not a standalone product:

- Requires **German Market** (commercial) with DHL add-on
- Requires **WCML** multi-currency
- Does **not** ship DHL, labels, or currency conversion on its own

Use **Fix** in the directory title and two feature subtitles in `readme.txt`:

1. **WCML Multi-Currency Compatibility Fix**
2. **German Market DHL International Address Fix**

---

## Pre-submit checklist

### Code & header

- [x] `phoenix-wp-bridge-german-market-wcml.php` — Plugin Name includes **Fix**
- [x] `Requires Plugins: woocommerce` (GM + WCML = soft deps + admin notices)
- [x] HPOS compatibility declared
- [x] GPL-2.0-or-later
- [x] `uninstall.php` removes settings option
- [x] No telemetry / external calls without user action

### readme.txt

- [x] English only (wp.org standard)
- [x] FAQ: Loco-ready, English admin UI
- [x] FAQ: German description in `docs/DESCRIPTION-de.md`
- [x] Validate locally: `.\scripts\validate-readme.ps1`
- [ ] Validate online: https://wordpress.org/plugins/developers/readme-validator/

### i18n

- [x] All user strings: `__()` / `esc_html_e()` with text domain
- [x] `load_plugin_textdomain()` in bootstrap
- [x] `loco.xml` for Loco Translate
- [x] **No** `de_DE` (or other) files shipped in v1.0.0
- [ ] GlotPress community packs optional after listing

### Build

```powershell
cd phoenix-wp-bridge-german-market-wcml
.\scripts\build-release.ps1
# → dist/phoenix-wp-bridge-german-market-wcml-1.0.0.zip
```

- [ ] ZIP installs on clean WP + WC + GM + WCML
- [ ] No `.git`, `dist/`, `scripts/`, `wp-org-assets/` in ZIP

### Assets (SVN `assets/` only)

- [x] Generated (PhoenixWP brand v3): `phoenix-wp-core/scripts/generate-brand-assets.ps1 -Plugin gm-dhl-wcml`
- [x] Copied to `wp-org-assets/` (4 PNGs + README)

| File | Size |
|------|------|
| `icon-256x256.png` | 256×256 |
| `icon-128x128.png` | 128×128 |
| `banner-772x250.png` | 772×250 |
| `banner-1544x500.png` | 1544×500 |

Banner: **PhoenixWP Fix** · German Market DHL + WCML · Multi-Currency - DHL Address Fix

---

## Plugin add form (wordpress.org/plugins/developers/add/)

Use when the wp.org account is approved (same account as Gift):

| Field | Value |
|-------|-------|
| **Plugin name** | PhoenixWP Fix — German Market DHL & WCML |
| **Plugin slug** | `phoenix-wp-bridge-german-market-wcml` |
| **Short description** | WCML multi-currency compatibility fix and German Market DHL international address fix. Requires German Market DHL + WCML (not included). |
| **Plugin URL** | https://github.com/phoenix-wp/phoenix-wp-bridge-german-market-wcml |
| **Notes for reviewer** | Compatibility fix only — not a fork of German Market or WCML. Requires both commercial plugins. No external calls, no telemetry. Tested with GM DHL 3.58.x, WCML multi-currency, WooCommerce HPOS. |

After approval email: run `.\scripts\wp-org-svn-deploy.ps1 -Version 1.0.0`, review, commit.

---

## SVN workflow

1. Reserve slug at https://wordpress.org/plugins/developers/add/
2. After approval: `.\scripts\wp-org-svn-deploy.ps1 -Version 1.0.0` (prepares trunk, assets, tag)
3. Review `svn status` in `.svn-wp-org/`, then `svn commit -m "Release 1.0.0"`
4. Answer plugins@wordpress.org review questions

Manual alternative:

1. `svn co https://plugins.svn.wordpress.org/phoenix-wp-bridge-german-market-wcml`
2. Copy release ZIP contents → `trunk/`
3. Copy PNGs from `wp-org-assets/` → `assets/`
4. Tag `tags/1.0.0/` from trunk

---

## Test plan (before submit)

| # | Test | Expected |
|---|------|----------|
| 1 | PLN cart below EUR threshold | DHL shows converted cost, not free |
| 2 | PLN cart above threshold | DHL free |
| 3 | FR address, DHL label | No empty street error |
| 4 | EUR checkout | Unchanged behaviour |
| 5 | GM or WCML inactive | Admin warning, no fatal errors |
| 6 | Plugin deactivated + deleted | Settings option removed |

---

## Tags (max 5 for readme.txt)

`woocommerce`, `german-market`, `wpml`, `wcml`, `shipping`

(DHL is implied; WP.org tag limit is 5.)
