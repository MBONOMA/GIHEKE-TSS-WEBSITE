# GIHEKE TSS - Automatic Startup Script
# Starts MySQL (XAMPP), installs deps, seeds DB, launches app

param(
    [switch]$NoInstall,
    [switch]$NoSeed
)

$ErrorActionPreference = "Stop"
$RootDir = $PSScriptRoot

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  GIHEKE TSS - System Launcher" -ForegroundColor Cyan
Write-Host "  MySQL Edition (via XAMPP)" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# ──────────────────────────────────────────────
# 1. START MYSQL from XAMPP
# ──────────────────────────────────────────────
Write-Host "🔍 Checking MySQL (XAMPP)..." -ForegroundColor Yellow
$mysqlBin = "C:\xampp\mysql\bin\mysqld.exe"
$mysqlClient = "C:\xampp\mysql\bin\mysql.exe"

if (-not (Test-Path $mysqlBin)) {
    Write-Host "❌ XAMPP MySQL not found at $mysqlBin" -ForegroundColor Red
    Write-Host "   Install XAMPP from https://www.apachefriends.org/" -ForegroundColor Yellow
    exit 1
}

# Check if MySQL is already running
$mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
if (-not $mysqlProcess) {
    Write-Host "🚀 Starting MySQL Server..." -ForegroundColor Yellow
    $dataDir = "C:\xampp\mysql\data"
    Start-Process -NoNewWindow -FilePath $mysqlBin -ArgumentList "--datadir=`"$dataDir`" --port=3306 --explicit_defaults_for_timestamp"
    Start-Sleep -Seconds 4
    $mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if (-not $mysqlProcess) {
        Write-Host "❌ Failed to start MySQL. Check XAMPP installation." -ForegroundColor Red
        exit 1
    }
    Write-Host "✅ MySQL Server started (PID: $($mysqlProcess.Id))" -ForegroundColor Green
} else {
    Write-Host "✅ MySQL Server already running (PID: $($mysqlProcess.Id))" -ForegroundColor Green
}

# Wait for MySQL to accept connections
$maxRetries = 10
for ($i = 1; $i -le $maxRetries; $i++) {
    try {
        & $mysqlClient -u root -e "SELECT 1" 2>&1 | Out-Null
        Write-Host "✅ MySQL accepting connections" -ForegroundColor Green
        break
    } catch {
        if ($i -eq $maxRetries) {
            Write-Host "❌ MySQL failed to accept connections after $maxRetries attempts" -ForegroundColor Red
            exit 1
        }
        Write-Host "   Waiting for MySQL... ($i/$maxRetries)" -ForegroundColor Gray
        Start-Sleep -Seconds 2
    }
}

# ──────────────────────────────────────────────
# 2. CREATE DATABASE
# ──────────────────────────────────────────────
Write-Host ""
Write-Host "🗄️  Setting up database..." -ForegroundColor Yellow
& $mysqlClient -u root -e "
    CREATE DATABASE IF NOT EXISTS giheke_tss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS 'giheke_admin'@'localhost' IDENTIFIED BY 'Giheke2024!';
    GRANT ALL PRIVILEGES ON giheke_tss.* TO 'giheke_admin'@'localhost';
    FLUSH PRIVILEGES;
" 2>&1 | Out-Null
Write-Host "✅ Database 'giheke_tss' ready" -ForegroundColor Green

# ──────────────────────────────────────────────
# 3. INSTALL DEPENDENCIES
# ──────────────────────────────────────────────
if (-not $NoInstall) {
    Write-Host ""
    Write-Host "📦 Installing backend dependencies..." -ForegroundColor Yellow
    Set-Location "$RootDir\backend"
    npm install 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0 -and $LASTEXITCODE -ne $null) {
        Write-Host "❌ Backend install failed" -ForegroundColor Red
        exit 1
    }
    Write-Host "✅ Backend dependencies installed" -ForegroundColor Green

    Write-Host ""
    Write-Host "📦 Installing frontend dependencies..." -ForegroundColor Yellow
    Set-Location "$RootDir\frontend"
    npm install 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0 -and $LASTEXITCODE -ne $null) {
        Write-Host "❌ Frontend install failed" -ForegroundColor Red
        exit 1
    }
    Write-Host "✅ Frontend dependencies installed" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "⏭️  Skipping npm install (--NoInstall flag)" -ForegroundColor Gray
}

# ──────────────────────────────────────────────
# 4. SEED DATABASE
# ──────────────────────────────────────────────
if (-not $NoSeed) {
    Write-Host ""
    Write-Host "🌱 Seeding database..." -ForegroundColor Yellow
    Set-Location "$RootDir\backend"
    npm run seed 2>&1
    if ($LASTEXITCODE -ne 0 -and $LASTEXITCODE -ne $null) {
        Write-Host "⚠️  Seed had issues, continuing anyway..." -ForegroundColor Yellow
    } else {
        Write-Host "✅ Database seeded" -ForegroundColor Green
    }
} else {
    Write-Host ""
    Write-Host "⏭️  Skipping seed (--NoSeed flag)" -ForegroundColor Gray
}

# ──────────────────────────────────────────────
# 5. START BACKEND & FRONTEND
# ──────────────────────────────────────────────
Write-Host ""
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  🚀 LAUNCHING APPLICATIONS" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan

$BackendPort = 4000
$FrontendPort = 3000

# Start Backend
Set-Location "$RootDir\backend"
$backendJob = Start-Job -Name "giheke-backend" -ScriptBlock {
    param($dir, $port)
    Set-Location $dir
    $env:PORT = $port
    npm run start:dev
} -ArgumentList "$RootDir\backend", $BackendPort

Start-Sleep -Seconds 3

# Start Frontend
Set-Location "$RootDir\frontend"
$frontendJob = Start-Job -Name "giheke-frontend" -ScriptBlock {
    param($dir, $port)
    Set-Location $dir
    npm run dev
} -ArgumentList "$RootDir\frontend", $FrontendPort

Start-Sleep -Seconds 5

Write-Host ""
Write-Host "✅ SYSTEM IS RUNNING!" -ForegroundColor Green
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host "  Frontend: http://localhost:$FrontendPort" -ForegroundColor White
Write-Host "  Backend:  http://localhost:$BackendPort" -ForegroundColor White
Write-Host "  API:      http://localhost:$BackendPort/api/v1" -ForegroundColor White
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Default Login:" -ForegroundColor Cyan
Write-Host "    Email:    admin@giheketss.com" -ForegroundColor White
Write-Host "    Password: Admin123!" -ForegroundColor White
Write-Host ""
Write-Host "  Press Q + Enter to stop all services" -ForegroundColor Gray
Write-Host ""

# Monitor for quit
while ($true) {
    $key = if ($Host.UI.RawUI.KeyAvailable) { $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyUp").Character } else { $null }
    if ($key -eq 'q' -or $key -eq 'Q') {
        Write-Host ""
        Write-Host "🛑 Stopping services..." -ForegroundColor Yellow
        Stop-Job $backendJob -ErrorAction SilentlyContinue
        Stop-Job $frontendJob -ErrorAction SilentlyContinue
        Remove-Job $backendJob -ErrorAction SilentlyContinue
        Remove-Job $frontendJob -ErrorAction SilentlyContinue
        Write-Host "✅ Services stopped" -ForegroundColor Green
        break
    }
    Start-Sleep -Seconds 1
}
