Param(
    # 支援 -Port, --port, -p, --p
    [Alias("p")]
    [int]$Port = 8085
)

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "--------------------------------------------------" -ForegroundColor Gray
Write-Host "正在檢查並清理 Port ${Port} 上的舊 PHP 進程..." -ForegroundColor Cyan

# 執行清理指令
# 我們將 $Port 變數帶入 CommandLine 的篩選條件中
Get-CimInstance Win32_Process -Filter "name = 'php.exe'" | Where-Object {
    $_.CommandLine -like "*$Port*"
} | ForEach-Object {
    $targetId = $_.ProcessId
    Write-Host "偵測到殘留進程 PID: $targetId，正在終止..." -ForegroundColor Yellow
    Stop-Process -Id $targetId -Force
    Write-Host "PID $targetId 已成功終止。" -ForegroundColor Green
}

Write-Host "清理完成。" -ForegroundColor Gray
Write-Host "--------------------------------------------------"
