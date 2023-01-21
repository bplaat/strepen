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
cp AppIcon.icns Strepen.app/Contents/Resources
cp Info.plist Strepen.app/Contents
open Strepen.app
