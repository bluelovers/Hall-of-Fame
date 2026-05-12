# HOF Log Cleanup Tool
# 解決日誌文件持續增長的問題

param(
    [Parameter(Position=0)]
    [ValidateSet("clear", "slim", "help")]
    [string]$Action = "slim",

    [Parameter(Position=1)]
    [int]$Lines = 500,

    [Parameter(ValueFromRemainingArguments=$true)]
    [string[]]$Files
)

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$OutputEncoding = [System.Text.Encoding]::UTF8

# Default list of log files to process
$DefaultFiles = @(
    (Join-Path $PSScriptRoot "../../server_error.log"),
    (Join-Path $PSScriptRoot "../../server_output.log"),
    (Join-Path $PSScriptRoot "../test/coverage-report/log/xdebug.log"),
    (Join-Path $PSScriptRoot "../test/test/coverage-report/log/php_errors.log")
    (Join-Path $PSScriptRoot "../test/coverage-report/log/php_errors.log")
)

# Show help message
function Show-Help {
    Write-Host "HOF Log Cleanup Tool" -ForegroundColor Green
    Write-Host "========================================"
    Write-Host "Usage:"
    Write-Host "  log-cleanup.bat [Action] [Lines] [Files...]"
    Write-Host ""
    Write-Host "Actions:"
    Write-Host "  clear  - Empty specified log files"
    Write-Host "  slim   - Keep last N lines (default 500)"
    Write-Host "  help   - Show this help"
    Write-Host ""
    Write-Host "Parameters:"
    Write-Host "  Lines  - Lines to keep when using slim"
    Write-Host "  Files  - File paths to process (optional)"
    Write-Host ""
    Write-Host "Default files:"
    foreach ($f in $DefaultFiles) {
        Write-Host "  - $f"
    }
}

# Process a single file
function Process-File {
    param($FilePath, $Action, $Lines)

    $Items = @()
    try {
        # Detect if contains wildcards
        if ($FilePath -match '[\*\?]') {
            $Items = Get-ChildItem -Path $FilePath -File -ErrorAction SilentlyContinue
        } elseif (Test-Path $FilePath) {
            $Items = @(Get-Item -Path $FilePath)
        }
    } catch {
        Write-Host "  -> Error accessing path: $FilePath" -ForegroundColor Red
        return
    }

    if ($null -eq $Items -or $Items.Count -eq 0) {
        return
    }

    foreach ($Item in $Items) {
        $Path = $Item.FullName
        Write-Host "Processing: $Path" -ForegroundColor Cyan
        try {
            if ($Action -eq "clear") {
                # Use [System.IO.File]::WriteAllText to empty file to avoid locking issues
                [System.IO.File]::WriteAllText($Path, "")
                # Write-Host "  -> Emptied" -ForegroundColor Green
            } elseif ($Action -eq "slim") {
                # Read last N lines
                $content = Get-Content -Path $Path -Tail $Lines -ErrorAction SilentlyContinue
                if ($null -ne $content) {
                    # Write back to file with UTF8 encoding
                    $content | Set-Content -Path $Path -Encoding UTF8
                    # Write-Host "  -> Slimmed to last $Lines lines" -ForegroundColor Green
                } else {
                    # Write-Host "  -> File empty or no processing needed" -ForegroundColor Yellow
                }
            }
            Write-Host "  -> Completed" -ForegroundColor Green
        } catch {
            Write-Host "  -> Error: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

# Main execution logic
if ($Action -eq "help") {
    Show-Help
    exit 0
}

$TargetFiles = $Files
if ($null -eq $TargetFiles -or $TargetFiles.Count -eq 0) {
    $TargetFiles = $DefaultFiles
}

foreach ($File in $TargetFiles) {
    Process-File -FilePath $File -Action $Action -Lines $Lines
}
