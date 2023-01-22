find . -name ".DS_Store" -delete
mkdir -p Strepen.app/Contents/MacOS Strepen.app/Contents/Resources
if [[ $1 = "release" ]]; then
    clang -x objective-c --target=arm64-macos -Os strepen.m -framework Cocoa -framework WebKit -o Strepen-arm64 || exit 1
    clang -x objective-c --target=x86_64-macos -Os strepen.m -framework Cocoa -framework WebKit -o Strepen-x86_64 || exit 1
    strip Strepen-arm64 Strepen-x86_64
    lipo Strepen-arm64 Strepen-x86_64 -create -output Strepen.app/Contents/MacOS/Strepen
    rm Strepen-arm64 Strepen-x86_64
else
    clang -x objective-c strepen.m -framework Cocoa -framework WebKit -o Strepen.app/Contents/MacOS/Strepen || exit 1
fi
cp -r Resources Strepen.app/Contents
cp Info.plist Strepen.app/Contents
if [[ $1 = "release" ]]; then
    zip -r Strepen.app.zip Strepen.app
fi
open Strepen.app
