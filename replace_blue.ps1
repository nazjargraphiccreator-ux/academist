$targetColor = "#102d56"
$targetRgba = "rgba(16, 45, 86,"

$hexPattern = "(?i)#(4189f0|1b5cb8|007bff|5b95f9|1e40af|0056b3|4FC3F7|2b6cc8|519aff|4a84e8|0D47A1|2563eb|93c5fd|00c6ff|1a42a8|003d82|38bdf8|bfdbfe|bae6fd|0277BD|0056d2|7aaef5|2196F3|3396f3)"
$rgbaPattern = "(?i)rgba\(\s*(65\s*,\s*137\s*,\s*240|27\s*,\s*92\s*,\s*184|0\s*,\s*86\s*,\s*179|0\s*,\s*86\s*,\s*210|0\s*,\s*123\s*,\s*255|91\s*,\s*149\s*,\s*249|13\s*,\s*71\s*,\s*161|33\s*,\s*150\s*,\s*243)\s*,"

Get-ChildItem -Path "d:\anaclean-redesign\web\login-acc-checkout\academic\academist-child-NEW\academist-child" -Recurse -Include *.css,*.php,*.js | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $newContent = $content -replace $hexPattern, $targetColor
    $newContent = $newContent -replace $rgbaPattern, $targetRgba
    if ($content -cne $newContent) {
        Set-Content -Path $_.FullName -Value $newContent -NoNewline
        Write-Output "Updated: $($_.FullName)"
    }
}
