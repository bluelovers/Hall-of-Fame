Param(
    # 如果執行時加上 -NoKill，則不會自動停止
    # 支援 -NoKill, --NoKill, -no-kill, --no-kill
    [Alias("no-kill")]
    [switch]$NoKill,

    # Port 變數名本身就已經支援 -Port 或 --Port --port 了，不需要再設為 "port"
    # 如果你想支援更短的參數，可以設為 "p"
    # 支援 -Port, --port, -p, --p
    [Alias("p")]
    [int]$Port = 8085,

    # Timeout 同理，變數名已支援 --Timeout，不需要額外設為 "timeout"
    # 可以設為 "t" 或是保留空白
    # 支援 -Timeout, --timeout, -t, --t
    [Alias("t")]
    [int]$Timeout = 10,

    # 新增 Xdebug 開關偵測
    # 支援 -Xdebug 或由使用者手動傳入包含 coverage 的參數
    [Alias("x")]
    [switch]$Xdebug
)

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$OutputEncoding = [System.Text.Encoding]::UTF8

function Get-WebPageSnapshot {
    param(
        [Parameter(Mandatory=$true)]
        [string]$Uri,

        [string]$SavePath = $null, # 預設為空，不儲存

        [int]$ShowLines = 10       # 預設顯示前 10 行
    )

    try {
        # 顯示預覽
        # Write-Host "--------------------------------------------------" -ForegroundColor Gray
        Write-Host "正在擷取內容: $Uri ..." -ForegroundColor Gray
        Write-Host "網頁內容 (前 $ShowLines 行)：" -ForegroundColor Yellow
        Write-Host "--------------------------------------------------" -ForegroundColor Gray

        $response = Invoke-WebRequest -Uri $Uri -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop

        # 處理 Byte 轉字串
        $rawHtml = if ($response.Content -is [System.Array]) {
            [System.Text.Encoding]::UTF8.GetString($response.Content)
        } else {
            $response.Content
        }

        # 如果有指定路徑，則儲存檔案
        if ($SavePath) {
            $rawHtml | Out-File -FilePath $SavePath -Encoding utf8
        }

        $lines = $rawHtml -split "`r`n|`n" | Where-Object { $_.Trim() -ne "" }
        $lines | Select-Object -First $ShowLines | ForEach-Object { Write-Host $_ }

        if ($SavePath) {
            Write-Host "..." -ForegroundColor Gray
            Write-Host "... ( ${Uri} 完整內容已儲存至: $SavePath )" -ForegroundColor Gray
            # Write-Host "--------------------------------------------------" -ForegroundColor Gray
        }

        Write-Host "--------------------------------------------------" -ForegroundColor Gray
    }
    catch {
        # 使用 ${Uri} 確保冒號不會被當作變數名稱的一部分
        Write-Host "[提示] 無法擷取 ${Uri} 內容: $($_.Exception.Message)" -ForegroundColor DarkYellow
    }
}

# 1. 設定變數
$SERVER_PORT = $Port
$PROJECT_DIR = $PSScriptRoot

$PHP_PATH_DIR = "D:\Users\WebstormProjects\php\bin\php-5.6.32-nts-Win32-VC11-x64"

$PHP_PATH = Join-Path $PHP_PATH_DIR "php.exe"
$PHP_EXT_DIR = Join-Path $PHP_PATH_DIR "ext"
$XDEBUG_DLL = Join-Path $PSScriptRoot "trust_path/bin/php_xdebug-2.2.7-5.6-nts-vc11-x64.dll"

$LOG_FILE = Join-Path $PROJECT_DIR "server_output.log"
$ERR_FILE = Join-Path $PROJECT_DIR "server_error.log"

# 額外增加一個存放原始碼的變數
$HTML_FILE = Join-Path $PROJECT_DIR "server_index.html"
$HTML_FILE2 = Join-Path $PROJECT_DIR "server_file2.html"

Write-Host "==============================================" -ForegroundColor Gray
Write-Host "  Hall of Fame - PHP Development Server" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Gray

# --- 容錯處理 ---
if (-not (Test-Path $PHP_PATH)) {
    Write-Error "錯誤：找不到 PHP 執行檔於路徑：$PHP_PATH"
    exit
}

# --- Xdebug 動態配置 (參考 php-test 邏輯) ---
$XDEBUG_OPTS = ""
# 只要有下 -Xdebug 開關，或者使用者參數中有 coverage 關鍵字就啟動
if ($Xdebug -or ($MyInvocation.BoundParameters.Values -match "coverage")) {
    if (Test-Path $XDEBUG_DLL) {
        $XDEBUG_OPTS = "-d zend_extension=`"$XDEBUG_DLL`" -d xdebug.coverage_enable=1"
        Write-Host "[啟動] Xdebug 已掛載" -ForegroundColor Yellow
    } else {
        Write-Host "[警告] 找不到 Xdebug DLL: $XDEBUG_DLL" -ForegroundColor Red
    }
}

# 清空日誌
try { $null | Out-File -FilePath $LOG_FILE -Confirm:$false -ErrorAction SilentlyContinue } catch {}
try { $null | Out-File -FilePath $ERR_FILE -Confirm:$false -ErrorAction SilentlyContinue } catch {}

# 2. 啟動 PHP 伺服器
try {
    # 將 $Port 變數帶入啟動參數，使用 -c 參數指定 php.ini 目錄
    # 將 $XDEBUG_OPTS 加入啟動參數
    $argString = "$XDEBUG_OPTS -c `"$PROJECT_DIR`" -S 0.0.0.0:$Port -t `"$PROJECT_DIR`" `"$PROJECT_DIR\server_router.php`""

    # 輸出完整的啟動指令方便除錯 (Debug 用)
    Write-Host "執行參數: $argString" -ForegroundColor Gray

    $phpProc = Start-Process -FilePath $PHP_PATH `
        -ArgumentList $argString `
        -RedirectStandardOutput $LOG_FILE `
        -RedirectStandardError $ERR_FILE `
        -PassThru `
        -WindowStyle Hidden -ErrorAction Stop
}
catch {
    Write-Error "啟動失敗：$($_.Exception.Message)"
    exit
}

Start-Sleep -Milliseconds 800 # 稍微延長等待時間確保 Port 已監聽
if ($null -eq $phpProc -or $phpProc.HasExited) {
    $rawError = Get-Content $ERR_FILE -Raw
    # Write-Host "--------------------------------------------------" -ForegroundColor Red
    Write-Error "伺服器啟動後立即中斷。請檢查 Port $Port 是否被佔用。"
    if ($rawError) { Write-Host "PHP 錯誤訊息：`n$rawError" -ForegroundColor Red }
    exit
}

$targetPid = $phpProc.Id

# --- 抓取網頁原始碼並處理 Byte 轉字串 ---
Get-WebPageSnapshot -Uri "http://localhost:$Port" -SavePath $HTML_FILE
# Get-WebPageSnapshot -Uri "http://localhost:$Port/manual/tutorial" -SavePath $HTML_FILE2

# 3. 輸出狀態報告
# Write-Host "--------------------------------------------------" -ForegroundColor Gray
Write-Host "伺服器已啟動於 Port: $Port" -ForegroundColor Cyan
Write-Host "PHP PID: $targetPid (PowerShell PID: $PID)" -ForegroundColor Cyan

if ($NoKill) {
    Write-Host "[模式] 持續執行 (Ctrl+C 關閉)" -ForegroundColor Magenta
} else {
    Write-Host "[模式] 自動停止 (倒數 $Timeout 秒)" -ForegroundColor Cyan
}
Write-Host "--------------------------------------------------"

# 4. 背景監控
$job = Start-Job -ScriptBlock {
    param($log, $err)
    Get-Content -Path $log, $err -Wait -Tail 0
} -ArgumentList $LOG_FILE, $ERR_FILE

# 5. 主迴圈
$timer = [System.Diagnostics.Stopwatch]::StartNew()
try {
    while ($true) {
        if ($phpProc.HasExited) {
            Write-Host "`n[警告] PHP 伺服器已提前中斷。" -ForegroundColor Red
            break
        }

        # 使用傳入的 $Timeout 參數
        if (-not $NoKill -and $timer.Elapsed.TotalSeconds -ge $Timeout) {
            Write-Host "`n時間到 ($Timeout 秒)..." -ForegroundColor Yellow
            break
        }

        Receive-Job -Job $job
        Start-Sleep -Milliseconds 200
    }
}
finally {
    if ($job) { Stop-Job -Job $job; Remove-Job -Job $job }
    if ($null -ne $targetPid) {
        if (Get-Process -Id $targetPid -ErrorAction SilentlyContinue) {
            Stop-Process -Id $targetPid -Force
            Write-Host "`nPID $targetPid 已終止。" -ForegroundColor Green
        }
    }
    $timer.Stop()
    Write-Host "任務結束。" -ForegroundColor Gray
}

