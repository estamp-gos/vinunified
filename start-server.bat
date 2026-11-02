@echo off
echo Starting VinUnified Local Development Server...
echo Open your browser at: http://localhost:8000
echo.
"%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" -S localhost:8000 router.php
pause
