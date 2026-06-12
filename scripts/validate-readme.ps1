# Local readme.txt checks before wp.org submission (offline).
# Usage: .\scripts\validate-readme.ps1

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$readmePath = Join-Path $root 'readme.txt'

if (-not (Test-Path $readmePath)) {
	throw "readme.txt not found: $readmePath"
}

$readme = Get-Content $readmePath -Raw
$lines = Get-Content $readmePath
$errors = @()
$warnings = @()

if ($readme -notmatch '(?m)^=== .+ ===\s*$') {
	$errors += 'Missing === Plugin Name === header.'
}

$headerBlock = ($lines | Select-Object -First 20) -join "`n"
foreach ($field in @('Contributors:', 'Requires at least:', 'Tested up to:', 'Requires PHP:', 'Stable tag:', 'License:')) {
	if ($headerBlock -notmatch [regex]::Escape($field)) {
		$errors += "Missing header field: $field"
	}
}

if ($headerBlock -match 'Stable tag:\s*([0-9.]+)') {
	$stableTag = $Matches[1]
	$mainFile = Join-Path $root 'phoenix-wp-bridge-german-market-wcml.php'
	$mainContent = Get-Content $mainFile -Raw
	if ($mainContent -notmatch "Version:\s*$stableTag") {
		$errors += "Stable tag ($stableTag) does not match plugin header Version."
	}
}

$tagLine = $lines | Where-Object { $_ -match '^Tags:' } | Select-Object -First 1
if ($tagLine) {
	$tags = ($tagLine -replace '^Tags:\s*', '').Split(',') | ForEach-Object { $_.Trim() } | Where-Object { $_ }
	if ($tags.Count -gt 5) {
		$errors += "Too many readme Tags ($($tags.Count)). wp.org allows max 5."
	}
	if ($tags.Count -eq 0) {
		$warnings += 'No Tags: line found in header block.'
	}
}

$requiredSections = @('Description', 'Installation', 'Frequently Asked Questions', 'Changelog')
foreach ($section in $requiredSections) {
	if ($readme -notmatch "== $section ==") {
		$errors += "Missing section: == $section =="
	}
}

if ($readme -match '== Upgrade Notice ==') {
	$warnings += 'Upgrade Notice section present (optional for first release).'
}

if ($readme.Length -gt 150000) {
	$warnings += 'readme.txt is very large; consider trimming for wp.org.'
}

Write-Host "readme.txt validation: $readmePath"
Write-Host ''

if ($errors.Count -eq 0) {
	Write-Host 'PASS - no blocking issues found.' -ForegroundColor Green
} else {
	Write-Host 'FAIL — fix before submit:' -ForegroundColor Red
	$errors | ForEach-Object { Write-Host "  - $_" -ForegroundColor Red }
}

if ($warnings.Count -gt 0) {
	Write-Host ''
	Write-Host 'Warnings:'
	$warnings | ForEach-Object { Write-Host "  - $_" -ForegroundColor Yellow }
}

Write-Host ''
Write-Host 'Also run: https://wordpress.org/plugins/developers/readme-validator/'

if ($errors.Count -gt 0) {
	exit 1
}
