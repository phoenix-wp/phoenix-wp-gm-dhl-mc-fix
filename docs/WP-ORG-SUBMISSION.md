# wordpress.org submission — PhoenixWP Fix (German Market DHL & WCML)

> **Free plugin · no Pro tier**  
> **SVN slug:** `phoenix-german-market-dhl-multi-currency-fix-for-woocommerce`  
> **Display name:** PhoenixWP Fix — German Market DHL & WCML  
> **Stand:** 2026-06-26 — **Einreichung jetzt** (PCP + ReadMe + Smoke ✅)

Canonical spec: `phoenix-wp-core/docs/plugins/PHOENIX-WP-GM-DHL-MC-FIX.md`

---

## Positioning (important for review)

This plugin is a **compatibility fix / extension**, not a standalone product:

- Requires **German Market** (commercial) with DHL add-on
- Requires **WCML** multi-currency
- Does **not** ship DHL, labels, or currency conversion on its own
- **100 % GPL** — no Freemius, no Pro tier, no upsell gates

Use **Fix** in the directory title and two feature subtitles in `readme.txt`:

1. **WCML Multi-Currency Compatibility Fix**
2. **German Market DHL International Address Fix**

---

## Review audit (2026-06-26)

| Area | Result |
|------|--------|
| Trialware / Pro gates | ✅ None — single free codebase |
| Freemius / premium paths | ✅ None |
| External HTTP / telemetry | ✅ None |
| HPOS | ✅ Declared |
| `uninstall.php` | ✅ Removes `phoenix_gm_dhl_mc_fix_settings` (and legacy key) |
| PhoenixWP Core | ✅ **Optional** — `register_module` + Admin unter **PhoenixWP** (wie Gift / Price Labels); ohne Core unter **WooCommerce** |
| Security (nonces, caps) | ✅ Settings via `options.php` + `manage_woocommerce` |
| i18n | ✅ JIT via wp.org (`languages/de_DE` bundled) |
| wp.org ZIP artifacts | ✅ No `docs/`, `loco.xml`, root `README.md`, `scripts/` |
| BOM / line endings | ✅ Build guards green |
| WC tested | ✅ **10.9.1** |
| readme ↔ header version | ✅ **1.0.0** |

**Note:** SVN slug is **not live yet** — first step is the [plugin add form](https://wordpress.org/plugins/developers/add/) (upload ZIP). SVN deploy **after** approval email.

### wp.org review fixes (2026-06-25)

| Review issue | Fix |
|--------------|-----|
| Text domain ≠ slug | Aligned to `phoenix-german-market-dhl-multi-currency-fix-for-woocommerce` (header, `__()`, `languages/*`) |
| Generic prefixes | Unified prefix `phoenix_gm_dhl_mc_fix_*` (functions, hooks, options, constants `PHOENIX_GM_DHL_MC_FIX_*`) |
| `wcml` class prefix | Renamed `Wcml_Converter` → `Multicurrency_Price_Converter` |
| `$woocommerce_wpml` global | Read via `$GLOBALS['woocommerce_wpml']` into `$pgmdhlmc_wcml` (WPML core global, not a plugin symbol) |
| Namespace | `PhoenixWP\GmDhlMcFix` |
| Legacy option | `phoenix_wp_bridge_gm_wcml_settings` migrated on load/activate; removed on uninstall |

Resubmit ZIP: `dist/phoenix-german-market-dhl-multi-currency-fix-for-woocommerce-1.0.0.zip`

---

## Submit package (use this)

| Item | Path |
|------|------|
| **Upload ZIP** | `dist/phoenix-german-market-dhl-multi-currency-fix-for-woocommerce-1.0.0.zip` |
| **Assets (SVN later)** | `wp-org-assets/` — 4 PNGs |

Build:

```powershell
cd phoenix-wp-gm-dhl-mc-fix
.\scripts\validate-readme.ps1
.\scripts\build-release.ps1
```

---

## Pre-submit checklist

### Code & header

- [x] Plugin Name includes **Fix**
- [x] `Requires Plugins: woocommerce` (GM + WCML = soft deps + admin notices)
- [x] HPOS compatibility declared
- [x] GPL-2.0-or-later
- [x] No hard dependency on PhoenixWP Core

### readme.txt

- [x] English (wp.org standard)
- [x] No references to `docs/` files excluded from ZIP
- [x] Donate link + support URL
- [x] `.\scripts\validate-readme.ps1` — PASS
- [x] **User:** readme validator — PASS (2026-06-26)
- [x] **User:** Plugin Check (PCP) — PASS (2026-06-26)

### Build

- [x] ZIP builds with artifact + BOM guards
- [x] 30 files, ~40 KB — no vendor tree (autoload-fallback only)
- [x] **User:** smoke test — PASS (2026-06-26)

### Assets (SVN `assets/` only — after approval)

- [x] `icon-256x256.png`, `icon-128x128.png`
- [x] `banner-772x250.png`, `banner-1544x500.png`

---

## Plugin add form

https://wordpress.org/plugins/developers/add/

| Field | Value |
|-------|-------|
| **Plugin name** | PhoenixWP Fix — German Market DHL & WCML |
| **Plugin slug** | `phoenix-german-market-dhl-multi-currency-fix-for-woocommerce` |
| **Short description** | WCML multi-currency compatibility fix and German Market DHL international address fix. Requires German Market DHL + WCML (not included). |
| **Plugin URL** | https://phoenixwp.com/support/ |
| **Upload** | `dist/phoenix-german-market-dhl-multi-currency-fix-for-woocommerce-1.0.0.zip` |

### Notes for reviewer (paste into submission / reply mail)

```
Compatibility fix only — not a fork of German Market or WCML.

Requires both commercial plugins (German Market with DHL add-on + WCML multi-currency).
Neither is bundled. Without them, the plugin shows admin notices and does not alter checkout.

Features:
1) Converts German Market DHL free-shipping thresholds and flat rates using WCML before comparing cart totals.
2) Normalizes international address line 1 (e.g. France number-first) for DHL checkout/labels.

No external API calls, no telemetry, no license gates. HPOS compatible.
Tested with WooCommerce 10.9.1, WordPress 7.0, German Market DHL ~3.58.x on a live DACH shop (Vitalstoffversand).
```

---

## SVN workflow (after approval email)

```powershell
cd phoenix-wp-gm-dhl-mc-fix
.\scripts\wp-org-svn-deploy.ps1 -Version 1.0.0
# Review svn status in .svn-wp-org/, then:
cd .svn-wp-org
svn commit -m "Initial release 1.0.0 — German Market DHL WCML fix."
```

Or use prepared working copy under `phoenix-wp-core/../dev/svn-gm-dhl-wcml` after approval (same steps as Gift / Price Labels).

---

## Test plan (before / during review)

| # | Test | Expected |
|---|------|----------|
| 1 | PLN cart below EUR threshold | DHL shows converted cost, not free |
| 2 | PLN cart above threshold | DHL free |
| 3 | FR address, DHL label | No empty street error |
| 4 | EUR checkout | Unchanged behaviour |
| 5 | GM or WCML inactive | Admin warning, no fatal errors |
| 6 | Plugin deactivated + deleted | Settings option removed |
| 7 | WP `de_DE` | Admin UI German |

---

## Tags (readme — max 5)

`woocommerce`, `german-market`, `wpml`, `wcml`, `shipping`
