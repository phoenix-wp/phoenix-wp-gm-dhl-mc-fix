# Build a distributable ZIP for GitHub Release / wordpress.org trunk.
# Usage: .\scripts\build-release.ps1 [-Version 1.0.0] [-Deploy]

param(
	[string]$Version = '',
	[switch]$Deploy
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$pluginSlug = 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce'
$coreHelpers = Join-Path (Split-Path -Parent $root) 'phoenix-wp-core\scripts\wp-org-release-helpers.ps1'
if (-not (Test-Path $coreHelpers)) {
	throw "Missing shared helpers: $coreHelpers"
}
. $coreHelpers

if ($Version -eq '') {
	$mainFile = Join-Path $root "$pluginSlug.php"
	$content = Get-Content $mainFile -Raw
	if ($content -match "Version:\s*([0-9.]+)") {
		$Version = $Matches[1]
	} else {
		throw "Could not detect plugin version in $mainFile"
	}
}

$distDir = Join-Path $root 'dist'
$stageDir = Join-Path $env:TEMP $pluginSlug
$zipPath = Join-Path $distDir "$pluginSlug-$Version.zip"

$extraExcludes = @('vendor', '.svn-wp-org')
if ($Deploy) {
	$extraExcludes += @('docs', 'README.md')
	$zipPath = Join-Path $distDir "$pluginSlug-$Version-deploy.zip"
}

if (-not (Test-Path $distDir)) {
	New-Item -ItemType Directory -Path $distDir -Force | Out-Null
}

Copy-PhoenixPluginToStage -Root $root -StageDir $stageDir -ExcludeNames (Get-PhoenixWpOrgStageExcludeNames -Extra $extraExcludes)
Remove-PhoenixWpOrgStageArtifacts -StageDir $stageDir
$languagesReadme = Join-Path $stageDir 'languages\README.md'
if (Test-Path $languagesReadme) {
	Remove-Item -Force $languagesReadme
}
New-PhoenixPluginReleaseZip -StageDir $stageDir -PluginSlug $pluginSlug -ZipPath $zipPath
Test-PhoenixPluginReleaseZip -ZipPath $zipPath -PluginSlug $pluginSlug -RequireDistinctUris
Test-PhoenixWpOrgZipUnexpectedArtifacts -ZipPath $zipPath -PluginSlug $pluginSlug
Test-PhoenixWpOrgZipPhpNoBom -ZipPath $zipPath -PluginSlug $pluginSlug

$suffix = if ($Deploy) { ' (deploy)' } else { '' }
Write-Host "Built $zipPath$suffix (tar paths for wp.org)"
