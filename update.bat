@echo off
set /p Input=Nama Perubahan: 
git add .
git commit -m "%Input%"
git push