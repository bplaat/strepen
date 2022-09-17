# Simple build script to build the Strepen Windows Client app
# with MinGW (MSYS2 & pacman -S mingw-w64-x86_64-toolchain)
# and a minify-xml tool (npm install -g minify-xml)
# to build release use ./build.sh release

rm -rf .vscode
if [ "$1" = "clean" ]; then
    rm -rf build
    exit
fi

mkdir -p build
minify-xml res/app.manifest > build/app.min.manifest || exit
windres res/resource.rc -o build/resource.o || exit
cp WebView2/x64/WebView2Loader.dll build

if [ "$1" = "release" ]; then
    gcc -c -Os -IWebView2/include src/strepen.c -o build/strepen.o || exit
    ld -s --subsystem windows build/strepen.o build/resource.o -e _start \
        -L"C:\\Windows\\System32" -lkernel32 -luser32 -lgdi32 -lshell32 -lversion -o build/strepen.exe
    rm build/app.min.manifest build/resource.o build/strepen.o
    exit
fi

gcc -c -IWebView2/include src/strepen.c -o build/strepen.o || exit
ld build/strepen.o build/resource.o -e _start \
    -L"C:\\Windows\\System32" -lkernel32 -luser32 -lgdi32 -lshell32 -lversion -o build/strepen.exe
./build/strepen
