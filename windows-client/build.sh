# Simple build script to build the Windows application with MinGW
rm -rf build
mkdir build

# minify-xml res/app.manifest > res/app.min.manifest

windres res/resource.rc -o build/resource.o

gcc -c -Os -IWebView2/include src/strepen.c -o build/strepen.o

ld -s --subsystem windows build/strepen.o build/resource.o -e _start \
    -L"C:\\Windows\\System32" -lkernel32 -luser32 -lgdi32 -lshell32 -o build/strepen.exe

rm build/strepen.o build/resource.o

cp WebView2/x64/WebView2Loader.dll build

./build/strepen
