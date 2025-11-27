@echo off
echo Clearing Laravel cache...
cd /d E:\mostaql-project\wave-app

echo.
echo [1/6] Clearing route cache...
php artisan route:clear

echo.
echo [2/6] Clearing config cache...
php artisan config:clear

echo.
echo [3/6] Clearing view cache...
php artisan view:clear

echo.
echo [4/6] Clearing application cache...
php artisan cache:clear

echo.
echo [5/6] Clearing compiled classes...
php artisan clear-compiled

echo.
echo [6/6] Optimizing application...
php artisan optimize:clear

echo.
echo ========================================
echo All cache cleared successfully!
echo ========================================
echo.
echo Now you can test the registration at:
echo https://start.al-investor.com/register
echo.
pause
