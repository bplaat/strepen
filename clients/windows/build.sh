# Simple build script to build the Strepen Windows Client app
# with MinGW (MSYS2 & pacman -S mingw-w64-x86_64-toolchain),
# 7-zip and a minify-xml tool (npm install -g minify-xml)
# to build release use ./build.sh release

rm -rf ../.vscode
if [ "$1" = "clean" ]; then
    rm -rf build
    exit
fi

rm -rf build
mkdir build
minify-xml res/app.manifest > build/app.min.manifest || exit
windres res/resource.rc -o build/resource.o || exit

if [ "$1" = "release" ]; then
    mkdir build/strepen-win64
    gcc -c -Os -IWebView2/include src/strepen.c -o build/strepen.o || exit
    ld -s --subsystem windows build/strepen.o build/resource.o -e _start \
        -L"C:\\Windows\\System32" -lkernel32 -luser32 -lgdi32 -lshell32 -lole32 -lversion -o build/strepen-win64/strepen.exe
    cp WebView2/x64/WebView2Loader.dll build/strepen-win64

    cd build
    7z a strepen-win64.zip strepen-win64 > /dev/null
    exit
fi

gcc -c -IWebView2/include src/strepen.c -o build/strepen.o || exit
ld build/strepen.o build/resource.o -e _start \
    -L"C:\\Windows\\System32" -lkernel32 -luser32 -lgdi32 -lshell32 -lole32 -lversion -o build/strepen.exe
cp WebView2/x64/WebView2Loader.dll build
./build/strepen
