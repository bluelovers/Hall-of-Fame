@echo off
powershell -ExecutionPolicy Bypass -File "%~dp0taskill-port.ps1" %*
