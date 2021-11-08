@echo off

cls

cd /d %~d0
cd %~dp0

call constants.bat

cd ..

%php_home%php think clear
%php_home%php think optimize:autoload
%php_home%php think optimize:schema
%php_home%php think optimize:route
%php_home%php think optimize:config
%php_home%php think optimize:config admin
%php_home%php think optimize:config api
%php_home%php think optimize:config callback
%php_home%php think optimize:config upload
%php_home%php think optimize:config home
%php_home%php think optimize:config index
%php_home%php think optimize:config member
%php_home%php think optimize:config report
%php_home%php think optimize:config tool
%php_home%php think optimize:config mobile
