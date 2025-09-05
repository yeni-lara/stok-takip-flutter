@echo off
chcp 65001 >nul
echo ==========================================
echo 🚀 Laravel Proje Sıkıştırıcı v2.0
echo ==========================================
echo.

REM 7-Zip kurulu mu kontrol et
if not exist "C:\Program Files\7-Zip\7z.exe" (
    echo ❌ 7-Zip bulunamadı! Lütfen 7-Zip'i yükleyin.
    echo    İndirme: https://www.7-zip.org/
    pause
    exit /b 1
)

REM Proje adını otomatik al (klasör adından)
for %%I in (.) do set PROJECT_NAME=%%~nxI

REM Tarih ve saat ekle
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "YY=%dt:~2,2%" & set "YYYY=%dt:~0,4%" & set "MM=%dt:~4,2%" & set "DD=%dt:~6,2%"
set "HH=%dt:~8,2%" & set "Min=%dt:~10,2%"

set ARCHIVE_NAME=%PROJECT_NAME%-%DD%-%MM%-%YY%-%HH%%Min%.zip

echo 📦 Proje: %PROJECT_NAME%
echo 📅 Tarih: %DD%/%MM%/%YYYY% %HH%:%Min%
echo 🗜️  Arşiv: %ARCHIVE_NAME%
echo.

echo ⏳ Sıkıştırma başlıyor...
echo.

"C:\Program Files\7-Zip\7z.exe" a -tzip -mx=1 -mmt=on "%ARCHIVE_NAME%" ^
app ^
bootstrap ^
config ^
database ^
public ^
resources ^
routes ^
storage ^
vendor ^
composer.json ^
composer.lock ^
package.json ^
package-lock.json ^
artisan ^
vite.config.js ^
postcss.config.js ^
tailwind.config.js ^
.env ^
-x!node_modules ^
-x!.git ^
-x!"storage\logs\*" ^
-x!"storage\framework\cache\*" ^
-x!"storage\framework\sessions\*" ^
-x!"storage\framework\views\*" ^
-x!tests ^
-x!phpunit.xml ^
-x!.editorconfig ^
-x!.gitattributes ^
-x!README.md ^
-x!mobile-app ^
-x!"*.log"

echo.
if %errorlevel% equ 0 (
    echo ✅ Sıkıştırma başarıyla tamamlandı!
    echo.
    echo 📊 Dosya Bilgileri:
    for %%A in ("%ARCHIVE_NAME%") do (
        set /a SIZE_MB=%%~zA/1024/1024
        echo    Boyut: !SIZE_MB! MB
        echo    Dosya: %%~nxA
    )
    echo.
    echo 💡 Bu ZIP dosyasını canlı sunucuya yükleyebilirsiniz.
) else (
    echo ❌ Sıkıştırma sırasında hata oluştu!
)

echo.
echo ==========================================
pause 