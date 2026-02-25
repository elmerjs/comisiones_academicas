@echo off
set source_folder="C:\xampp\htdocs\comisiones_academicas"
set destination_folder="C:\Users\J2C4YS3\Desktop\backup\backup_c_academicas"

for /f "tokens=1-4 delims=/ " %%a in ('powershell Get-Date -Format yyyy-MM-dd') do (
    set "formatted_date=%%a%%b%%c"
)

set backup_folder=%destination_folder%\backup_%formatted_date%

if not exist %backup_folder% (
    mkdir %backup_folder%
)

xcopy /e /i /y %source_folder%\* %backup_folder%

exit
