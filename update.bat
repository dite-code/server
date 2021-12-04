@echo off
git status
set /p txt=Nama Perubahan: 
git add .
git commit -m "%txt%"
git push