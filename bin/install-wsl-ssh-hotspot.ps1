# Run this file from Windows PowerShell as Administrator.
# It keeps Termux connecting to the same address:
#   ssh -p 2222 black@192.168.137.1

$ErrorActionPreference = "Stop"

$ListenAddress = "192.168.137.1"
$ListenPort = 2222
$ConnectPort = 2222
$DistroName = "kali-linux"
$TaskName = "Keep WSL SSH Hotspot Forward"
$ScriptDir = Join-Path $env:ProgramData "WSL-SSH-Hotspot"
$ScriptPath = Join-Path $ScriptDir "Update-WslSshForward.ps1"

function Assert-Admin {
    $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = [Security.Principal.WindowsPrincipal]::new($identity)
    if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
        throw "Open PowerShell as Administrator, then run this script again."
    }
}

function Write-ManagedScript {
    New-Item -ItemType Directory -Force -Path $ScriptDir | Out-Null

    $content = @'
$ErrorActionPreference = "Stop"

$ListenAddress = "192.168.137.1"
$ListenPort = 2222
$ConnectPort = 2222
$DistroName = "kali-linux"

function Get-WslIp {
    $ip = (& wsl.exe -d $DistroName -u root sh -lc "systemctl start ssh >/dev/null 2>&1 || service ssh start >/dev/null 2>&1 || true; ip -4 -o addr show eth0 | sed -E 's#.* inet ([0-9.]+)/.*#\1#'").Trim()
    if (-not $ip) {
        throw "Could not read WSL eth0 IPv4 address."
    }
    return $ip
}

$wslIp = Get-WslIp

& netsh interface portproxy delete v4tov4 listenaddress=$ListenAddress listenport=$ListenPort 2>$null | Out-Null
& netsh interface portproxy add v4tov4 listenaddress=$ListenAddress listenport=$ListenPort connectaddress=$wslIp connectport=$ConnectPort | Out-Null

$existingRule = Get-NetFirewallRule -DisplayName "WSL SSH 2222 from hotspot" -ErrorAction SilentlyContinue
if (-not $existingRule) {
    New-NetFirewallRule -DisplayName "WSL SSH 2222 from hotspot" -Direction Inbound -Action Allow -Protocol TCP -LocalPort $ListenPort | Out-Null
}

"$(Get-Date -Format s) forwarding ${ListenAddress}:${ListenPort} -> ${wslIp}:${ConnectPort}" |
    Out-File -FilePath "$env:ProgramData\WSL-SSH-Hotspot\last-run.log" -Encoding utf8
'@

    Set-Content -Path $ScriptPath -Value $content -Encoding UTF8
}

function Install-ScheduledTask {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent().Name
    $action = New-ScheduledTaskAction `
        -Execute "powershell.exe" `
        -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$ScriptPath`""

    $logonTrigger = New-ScheduledTaskTrigger -AtLogOn -User $currentUser
    $repeatTrigger = New-ScheduledTaskTrigger `
        -Once `
        -At (Get-Date).Date `
        -RepetitionInterval (New-TimeSpan -Minutes 5) `
        -RepetitionDuration (New-TimeSpan -Days 3650)

    $principal = New-ScheduledTaskPrincipal `
        -UserId $currentUser `
        -RunLevel Highest `
        -LogonType Interactive

    $settings = New-ScheduledTaskSettingsSet `
        -AllowStartIfOnBatteries `
        -DontStopIfGoingOnBatteries `
        -StartWhenAvailable `
        -MultipleInstances IgnoreNew `
        -RestartCount 3 `
        -RestartInterval (New-TimeSpan -Minutes 1)

    Register-ScheduledTask `
        -TaskName $TaskName `
        -Action $action `
        -Trigger @($logonTrigger, $repeatTrigger) `
        -Principal $principal `
        -Settings $settings `
        -Force | Out-Null
}

Assert-Admin
Write-ManagedScript
Install-ScheduledTask

& powershell.exe -NoProfile -ExecutionPolicy Bypass -File $ScriptPath

Write-Host ""
Write-Host "Done. From Termux, use:"
Write-Host "ssh -p 2222 black@$ListenAddress"
Write-Host ""
Write-Host "Current Windows port forward:"
& netsh interface portproxy show v4tov4
Write-Host ""
Write-Host "Last updater run:"
Get-Content "$env:ProgramData\WSL-SSH-Hotspot\last-run.log"
