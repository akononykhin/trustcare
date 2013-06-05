@echo off

set BIRT_HOME_WITH_PREFIX=%~dp0
set BIRT_HOME=%BIRT_HOME_WITH_PREFIX:~0,-1%

setlocal enabledelayedexpansion

rem Necessary to change directory to BIRT_HOME to be able to use relative paths (full paths are too long for 8Kb environment variable)
cd %BIRT_HOME%
for %%i in (%BIRT_HOME%\lib\*.jar) do set CLASSPATH=lib\%%~ni.jar;!CLASSPATH!

java  -DBIRT_HOME="%BIRT_HOME%" %*
exit /b %errorlevel%

:end
