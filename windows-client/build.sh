# Simple build script to build the Strepen Windows Client app
# with MinGW (MSYS2 & pacman -S mingw-w64-x86_64-toolchain)
# and a minify-xml tool (npm install -g minify-xml)
# to build release use ./build.sh release
rm -rf build
mkdir build
if minify-xml res/app.manifest > res/app.min.manifest && windres res/resource.rc -o build/resource.o; then
    if gcc -c $([ "$1" == "release" ] && echo "-Os") -IWebView2/include src/strepen.c -o build/strepen.o; then
        if ld -s $([ "$1" == "release" ] && echo "--subsystem windows") build/strepen.o build/resource.o -e _start \
            -L"C:\\Windows\\System32" -lkernel32 -luser32 -lgdi32 -lshell32 -o build/strepen.exe
        then
            rm build/resource.o build/strepen.o
            cp WebView2/x64/WebView2Loader.dll build
            if [ "$1" != "release" ]; then
                ./build/strepen
            fi
        fi
    fi
fi
