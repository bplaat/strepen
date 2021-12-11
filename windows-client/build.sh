# Simple build script to build the Windows application with MinGW and ResourceHacker
rm -rf build
mkdir build

windres res/resource.rc -o build/resource.res

g++ -Os src/main.cpp src/event.cpp -IWebView/include -LWebView/x64 \
    -static-libgcc -static-libstdc++ -Wl,-Bstatic,--whole-archive -lwinpthread -Wl,--no-whole-archive \
    -Wl,-Bdynamic -lWebView2Loader -Wl,--subsystem,windows -o build/strepen.exe

ResourceHacker -open build/strepen.exe -save build/strepen.exe -action addoverwrite -res build/resource.res -log NUL

rm build/resource.res

cp WebView/x64/WebView2Loader.dll build

./build/strepen
