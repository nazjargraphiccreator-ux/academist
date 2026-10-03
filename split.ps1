
$lines = Get-Content "functions.php"
$outlines = @()
$incLines = @()
$inSection = $false
$sectionName = ""

foreach ($line in $lines) {
    if ($line -match "^/\* ==+") {
        # Check the next line to see if it starts with a number and a dot
    }
}
# This is getting too complicated for PowerShell.

