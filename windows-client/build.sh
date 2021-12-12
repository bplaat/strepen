# Simple build script to build the Windows application with MinGW and ResourceHacker
rm -rf build
mkdir build

windres res/resource.rc -o build/resource.res

gcc -s -Os src/strepen.c -IWebView/include -LWebView/x64 -lWebView2Loader -lgdi32 -ldwmapi -Wl,--subsystem,windows -o build/strepen.exe

ResourceHacker -open build/strepen.exe -save build/strepen.exe -action delete -log NUL
ResourceHacker -open build/strepen.exe -save build/strepen.exe -action addoverwrite -res build/resource.res -log NUL

rm build/resource.res

cp WebView/x64/WebView2Loader.dll build

./build/strepen
