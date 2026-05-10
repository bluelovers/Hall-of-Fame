@echo off
chcp 65001 >nul

rem ========================================
rem Hall of Fame - PHPUnit Test Runner
rem
rem This script runs the PHPUnit test suite for the project
rem ========================================

rem Change to test directory (relative to script location)
cd /d "%~dp0..\test"

rem https://xdebug.org/download/historical
rem https://downloads.php.net/~windows/pecl/releases/xdebug/2.2.7/
set XDEBUG_OPTS=
for %%a in (%*) do (
    if "%%a"=="--coverage-html" (
        set XDEBUG_OPTS=-d zend_extension="%~dp0php_xdebug-2.2.7-5.6-nts-vc11-x64.dll" -d xdebug.coverage_enable=1
    )
)

rem Display test execution information header
echo ========================================
echo  Hall of Fame - PHPUnit Test Runner
echo  %DATE% %TIME%
echo ----------------------------------------
echo  CWD: %cd%
echo  ARGV: %*
echo  XDEBUG_OPTS: %XDEBUG_OPTS%
echo ========================================
echo  PHP Version:
call "%~dp0php.bat" -v
echo.

rem Execute PHPUnit tests
rem Uses php.bat wrapper to invoke PHP 5.6.32 runtime
rem Parameters:
rem   %~dp0php.bat           - Project PHP CLI wrapper path
rem   %~dp0phpunit-5.7.27.phar - PHPUnit 5.7.27 PHAR file
rem   -c "%~dp0..\test\phpunit.xml" - PHPUnit config file path
rem   %*                     - Pass through all additional parameters
"%~dp0php.bat" %XDEBUG_OPTS% "%~dp0phpunit-5.7.27.phar" -c "%~dp0..\test\phpunit.xml" %*

rem Check test execution result and display appropriate message
echo.
if %ERRORLEVEL% EQU 0 (
    echo ========================================
    echo  All tests passed successfully!
    echo ========================================
) else (
    echo ========================================
    echo  Some tests failed (exit code: %ERRORLEVEL%).
    echo ========================================
)
echo.

rem Pause execution, wait for user keypress to continue
rem Standard batch file practice to prevent window from closing immediately
pause
