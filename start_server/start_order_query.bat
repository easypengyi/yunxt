@echo off

cls

cd /d %~d0
cd %~dp0

call constants.bat

cd ..

%php_home%php think order:query
