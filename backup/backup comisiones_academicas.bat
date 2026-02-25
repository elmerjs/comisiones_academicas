@echo off
for /f "tokens=1-4 delims=/ " %%a in ('powershell Get-Date -Format yyyy-MM-dd') do (
    set "formatted_date=%%a%%b%%c"
)
C:\xampp\mysql\bin\mysqldump.exe -hlocalhost -uroot --skip-password comisiones_academicas > C:\xampp\htdocs\comisiones_academicas\backup\c_a_Backup_%formatted_date%.sql
exit