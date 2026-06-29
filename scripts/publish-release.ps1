# Build release ZIP(s) for GitHub / live deploy.
#
# Usage:
#   .\scripts\publish-release.ps1              # wp.org ZIP (with docs)
#   .\scripts\publish-release.ps1 -Deploy      # slim live-shop ZIP
#   .\scripts\publish-release.ps1 -SkipGitHub

param(
	[string]$Version = '',
	[switch]$Deploy,
	[switch]$SkipGitHub
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$pluginSlug = 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce'
$buildScript = Join-Path $PSScriptRoot 'build-release.ps1'

if ($Deploy) {
	& $buildScript -Version $Version -Deploy
} else {
	& $buildScript -Version $Version
}

if ($Version -eq '') {
	$mainFile = Join-Path $root "$pluginSlug.php"
	$content = Get-Content $mainFile -Raw
	if ($content -match "Version:\s*([0-9.]+)") {
		$Version = $Matches[1]
	}
}

$suffix = if ($Deploy) { '-deploy' } else { '' }
$zipPath = Join-Path $root "dist/$pluginSlug-$Version$suffix.zip"

if (-not (Test-Path $zipPath)) {
	throw "ZIP not found: $zipPath"
}

$firstEntry = (tar -tf $zipPath | Select-Object -First 1).Trim()
$expected = "$pluginSlug/"
if ($firstEntry -ne $expected) {
	throw "Invalid ZIP root folder '$firstEntry' (expected '$expected')"
}

Write-Host "ZIP verified: $zipPath"

if ($SkipGitHub -or $Deploy) {
	if ($Deploy) {
		Write-Host 'Deploy ZIP ready for WordPress plugin upload (live shop).'
	}
	exit 0
}

if (-not (Get-Command gh -ErrorAction SilentlyContinue)) {
	throw 'GitHub CLI (gh) not found.'
}

$ghRepo = 'phoenix-wp/phoenix-wp-gm-dhl-mc-fix'

gh release view $Version --repo $ghRepo 2>$null
if ($LASTEXITCODE -ne 0) {
	throw "GitHub release tag $Version does not exist. Create it first: gh release create $Version"
}

gh release upload $Version $zipPath --repo $ghRepo --clobber
Write-Host "GitHub release asset updated: https://github.com/phoenix-wp/phoenix-wp-gm-dhl-mc-fix/releases/tag/$Version"
