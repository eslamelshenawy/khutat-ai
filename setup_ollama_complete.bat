@echo off
echo ========================================
echo Ollama Setup - Complete Installation
echo ========================================
echo.

REM Step 1: Check if Ollama is installed
echo [1/5] Checking Ollama installation...
where ollama >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Ollama not found in PATH!
    echo Please restart your terminal/computer after installation.
    pause
    exit /b 1
)
echo SUCCESS: Ollama is installed!
echo.

REM Step 2: Check Ollama version
echo [2/5] Checking Ollama version...
ollama --version
echo.

REM Step 3: Start Ollama serve in background
echo [3/5] Starting Ollama server...
echo This will run in the background...
start /B ollama serve
timeout /t 5 /nobreak >nul
echo.

REM Step 4: Pull gemma:2b model
echo [4/5] Downloading gemma:2b model (1.6 GB)...
echo This may take 5-10 minutes...
ollama pull gemma:2b
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to download model!
    pause
    exit /b 1
)
echo SUCCESS: Model downloaded!
echo.

REM Step 5: Test generation
echo [5/5] Testing AI generation...
ollama run gemma:2b "Write a short business plan summary in 20 words" --verbose
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Ollama is now running on: http://localhost:11434
echo.
echo Next steps:
echo 1. Open: http://127.0.0.1:8000/wizard/start
echo 2. Create a business plan
echo 3. Click "توليد بالذكاء الاصطناعي"
echo.
echo NOTE: Keep this window open to keep Ollama running!
echo.
pause
