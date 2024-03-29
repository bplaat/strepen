#import <Cocoa/Cocoa.h>
#import <WebKit/WebKit.h>

#define LocalizedString(key) NSLocalizedString(key, nil)

NSApplication *application;
NSWindow *window;
WKWebView *webview;
NSString *appVersion;

@interface WindowDelegate : NSObject <NSWindowDelegate>
@end

@implementation WindowDelegate
- (void)windowDidResize:(NSNotification *)notification {
    webview.frame = [window.contentView bounds];
}
@end

@interface AppDelegate : NSObject <NSApplicationDelegate>
@end

@implementation AppDelegate
- (void)applicationDidFinishLaunching:(NSNotification *)aNotification {
    // Get app version
    appVersion = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleShortVersionString"];

    // Create menu
    NSMenu *menubar = [[NSMenu alloc] init];
    application.mainMenu = menubar;

    NSMenuItem *menuBarItem = [[NSMenuItem alloc] init];
    [menubar addItem:menuBarItem];

    NSMenu *appMenu = [[NSMenu alloc] init];
    menuBarItem.submenu = appMenu;

    NSMenuItem* aboutMenuItem = [[NSMenuItem alloc] initWithTitle:LocalizedString(@"menu_about")
        action:@selector(openAboutAlert:) keyEquivalent:@""];
    [appMenu addItem:aboutMenuItem];

    [appMenu addItem:[NSMenuItem separatorItem]];

    NSMenuItem* quitMenuItem = [[NSMenuItem alloc] initWithTitle:LocalizedString(@"menu_quit")
        action:@selector(terminate:) keyEquivalent:@"q"];
    [appMenu addItem:quitMenuItem];

    // Create window
    window = [[NSWindow alloc] initWithContentRect:NSMakeRect(0, 0, 1280, 720)
        styleMask:NSWindowStyleMaskTitled | NSWindowStyleMaskClosable | NSWindowStyleMaskMiniaturizable | NSWindowStyleMaskResizable
        backing:NSBackingStoreBuffered
        defer:NO];
    window.title = LocalizedString(@"app_name");
    window.titlebarAppearsTransparent = YES;
    CGFloat windowX = (NSWidth(window.screen.frame) - NSWidth(window.frame)) / 2;
    CGFloat windowY = (NSHeight(window.screen.frame) - NSHeight(window.frame)) / 2;
    [window setFrame:NSMakeRect(windowX, windowY, NSWidth(window.frame), NSHeight(window.frame)) display:YES];
    window.minSize = NSMakeSize(640, 480);
    window.backgroundColor = [NSColor colorWithRed:(0x0a / 255.f) green:(0x0a / 255.f) blue:(0x0a / 255.f) alpha:1];
    window.frameAutosaveName = @"window";
    window.delegate = [[WindowDelegate alloc] init];

    // Create webview
    webview = [[WKWebView alloc] initWithFrame:[window.contentView bounds]];
    [webview setValue:@NO forKey:@"drawsBackground"];
    NSURL *url = [NSURL URLWithString:LocalizedString(@"webview_url")];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    [webview loadRequest:request];
    [window.contentView addSubview:webview];

    [window makeKeyAndOrderFront:nil];
}

- (BOOL)applicationShouldTerminateAfterLastWindowClosed:(NSApplication *)sender {
    return YES;
}

- (void)applicationWillTerminate:(NSNotification *)aNotification {}

- (void)openAboutAlert:(NSNotification *)aNotification {
    NSAlert *alert = [[NSAlert alloc] init];
    alert.messageText = LocalizedString(@"about_title");
    alert.informativeText = [[NSString alloc] initWithFormat:LocalizedString(@"about_text"), appVersion];
    [alert runModal];
}
@end

int main(void) {
    application = [NSApplication sharedApplication];
    application.delegate = [[AppDelegate alloc] init];
    [application run];
    return EXIT_SUCCESS;
}
