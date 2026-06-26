# Deploy release to wordpress.org SVN (after slug is approved).
# Requires: Subversion CLI, wp.org credentials, approved plugin slug.
#
# Usage:
#   .\scripts\wp-org-svn-deploy.ps1 -Version 1.0.0
#   .\scripts\wp-org-svn-deploy.ps1 -Version 1.0.0 -DryRun

param(
	[string]$Version = '1.0.0',
	[string]$Slug = 'phoenix-german-market-dhl-multi-currency-fix-for-woocommerce',
	[switch]$DryRun
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$buildScript = Join-Path $PSScriptRoot 'build-release.ps1'
$validateScript = Join-Path $PSScriptRoot 'validate-readme.ps1'
$zipPath = Join-Path $root "dist/$Slug-$Version.zip"
$svnUrl = "https://plugins.svn.wordpress.org/$Slug"
$workDir = Join-Path $root ".svn-wp-org"

Write-Host "PhoenixWP Fix - wp.org SVN deploy ($Slug $Version)"

& $validateScript

if ($DryRun -and (Test-Path $zipPath)) {
	Write-Host "[DryRun] Using existing ZIP: $zipPath"
} else {
	& $buildScript -Version $Version
}

if (-not (Test-Path $zipPath)) {
	throw "Release ZIP not found: $zipPath"
}

$requiredAssets = @(
	'icon-256x256.png',
	'icon-128x128.png',
	'banner-772x250.png',
	'banner-1544x500.png'
)
foreach ($asset in $requiredAssets) {
	$assetPath = Join-Path $root "wp-org-assets/$asset"
	if (-not (Test-Path $assetPath)) {
		throw "Missing wp-org asset: $assetPath"
	}
}

if ($DryRun) {
	Write-Host '[DryRun] Would checkout, copy trunk, assets, tag, and svn commit.'
	Write-Host "  SVN: $svnUrl"
	Write-Host "  ZIP: $zipPath"
	exit 0
}

if (-not (Get-Command svn -ErrorAction SilentlyContinue)) {
	throw 'Subversion (svn) not found in PATH. Install TortoiseSVN CLI or svn package.'
}

if (Test-Path $workDir) {
	Remove-Item -Recurse -Force $workDir
}
New-Item -ItemType Directory -Path $workDir | Out-Null

Push-Location $workDir
try {
	svn co $svnUrl .
} catch {
	throw "SVN checkout failed. Is slug '$Slug' approved on wordpress.org?`n$($_.Exception.Message)"
}

$trunkDir = Join-Path $workDir 'trunk'
$assetsDir = Join-Path $workDir 'assets'
$tagDir = Join-Path $workDir "tags/$Version"

if (-not (Test-Path $trunkDir)) { New-Item -ItemType Directory -Path $trunkDir | Out-Null }
if (-not (Test-Path $assetsDir)) { New-Item -ItemType Directory -Path $assetsDir | Out-Null }
if (-not (Test-Path (Split-Path $tagDir))) { New-Item -ItemType Directory -Path (Split-Path $tagDir) | Out-Null }

Get-ChildItem $trunkDir -Force | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue

Add-Type -AssemblyName System.IO.Compression.FileSystem
$tempExtract = Join-Path $workDir 'zip-extract'
if (Test-Path $tempExtract) { Remove-Item -Recurse -Force $tempExtract }
[System.IO.Compression.ZipFile]::ExtractToDirectory((Resolve-Path $zipPath), $tempExtract)
$extractedRoot = Join-Path $tempExtract $Slug
Copy-Item -Path (Join-Path $extractedRoot '*') -Destination $trunkDir -Recurse -Force

foreach ($asset in $requiredAssets) {
	Copy-Item -Path (Join-Path $root "wp-org-assets/$asset") -Destination (Join-Path $assetsDir $asset) -Force
}

if (Test-Path $tagDir) {
	Get-ChildItem $tagDir -Force | Remove-Item -Recurse -Force
} else {
	New-Item -ItemType Directory -Path $tagDir | Out-Null
}
Copy-Item -Path (Join-Path $trunkDir '*') -Destination $tagDir -Recurse -Force

svn add --force trunk assets "tags/$Version" 2>$null
svn status

Write-Host ''
Write-Host 'Review svn status above, then commit:'
Write-Host "  svn commit -m `"Release $Version`""
Write-Host ''
Write-Host 'Or run from this folder after review:'
Write-Host "  cd `"$workDir`""
Write-Host "  svn commit -m `"Release $Version`""

Pop-Location
