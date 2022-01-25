# Simple build script to build the Windows application with MinGW
rm -rf build
mkdir build

# minify-xml res/app.manifest > res/app.min.manifest

windres res/resource.rc -o build/resource.o

gcc -s -Os src/strepen.c build/resource.o -IWebView2/include -LWebView2/x64 -lWebView2Loader \
    -lgdi32 -ldwmapi -Wl,--subsystem,windows -o build/strepen.exe

rm build/resource.o

cp WebView2/x64/WebView2Loader.dll build

./build/strepen
