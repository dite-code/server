@echo off
set /p txt=Nama Perubahan: 
git add .
git commit -m "%txt%"
git push