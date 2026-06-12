# Build a distributable ZIP for GitHub Release / wordpress.org trunk.
# Usage: .\scripts\build-release.ps1 [-Version 1.0.0] [-Deploy]

param(
	[string]$Version = '',
	[switch]$Deploy
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$pluginSlug = 'phoenix-wp-bridge-german-market-wcml'

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

$excludeNames = @(
	'.git', '.github', 'dist', 'scripts', 'wp-org-assets',
	'composer.lock', 'composer.phar', 'vendor', 'node_modules', '.svn-wp-org',
	'.gitignore', '.DS_Store', 'Thumbs.db'
)

if ($Deploy) {
	$excludeNames += @('docs', 'README.md')
	$zipPath = Join-Path $distDir "$pluginSlug-$Version-deploy.zip"
}

if (Test-Path $stageDir) {
	Remove-Item -Recurse -Force $stageDir -ErrorAction SilentlyContinue
}
New-Item -ItemType Directory -Path $stageDir -Force | Out-Null
if (-not (Test-Path $distDir)) {
	New-Item -ItemType Directory -Path $distDir -Force | Out-Null
}

Get-ChildItem -Path $root -Force | Where-Object {
	$_.Name -notin $excludeNames
} | ForEach-Object {
	Copy-Item -Path $_.FullName -Destination $stageDir -Recurse -Force
}

if (Test-Path $zipPath) {
	Remove-Item -Force $zipPath -ErrorAction SilentlyContinue
}

Push-Location $env:TEMP
try {
	tar -a -c -f $zipPath $pluginSlug
} finally {
	Pop-Location
}

Remove-Item -Recurse -Force $stageDir -ErrorAction SilentlyContinue

$suffix = if ($Deploy) { ' (deploy)' } else { '' }
Write-Host "Built $zipPath$suffix"
