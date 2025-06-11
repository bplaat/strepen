/*
 * Copyright (c) 2023-2025 Bastiaan van der Plaat
 *
 * SPDX-License-Identifier: MIT
 */

#import <Cocoa/Cocoa.h>
#import <WebKit/WebKit.h>

#define LocalizedString(key) NSLocalizedString(key, nil)

@interface AppDelegate : NSObject <NSApplicationDelegate, NSWindowDelegate>
    @property (strong, nonatomic) NSWindow *window;
    @property (strong, nonatomic) WKWebView *webview;
@end

@implementation AppDelegate
- (void)applicationDidFinishLaunching:(NSNotification *)aNotification {
    // Create menu
    NSMenu *menubar = [NSMenu new];
    NSApp.mainMenu = menubar;

    NSMenuItem *menuBarItem = [NSMenuItem new];
    [menubar addItem:menuBarItem];

    NSMenu *appMenu = [NSMenu new];
    menuBarItem.submenu = appMenu;

    NSMenuItem* aboutMenuItem = [[NSMenuItem alloc] initWithTitle:LocalizedString(@"menu_about")
        action:@selector(openAbout:) keyEquivalent:@""];
    [appMenu addItem:aboutMenuItem];

    [appMenu addItem:[NSMenuItem separatorItem]];

    NSMenuItem* quitMenuItem = [[NSMenuItem alloc] initWithTitle:LocalizedString(@"menu_quit")
        action:@selector(terminate:) keyEquivalent:@"q"];
    [appMenu addItem:quitMenuItem];

    // Create window
    self.window = [[NSWindow alloc] initWithContentRect:NSMakeRect(0, 0, 1280, 720)
        styleMask:NSWindowStyleMaskTitled | NSWindowStyleMaskClosable | NSWindowStyleMaskMiniaturizable | NSWindowStyleMaskResizable
        backing:NSBackingStoreBuffered
        defer:NO];
    self.window.title = LocalizedString(@"app_name");
    self.window.titlebarAppearsTransparent = YES;
    self.window.appearance = [NSAppearance appearanceNamed:NSAppearanceNameDarkAqua];
    CGFloat windowX = (NSWidth(self.window.screen.frame) - NSWidth(self.window.frame)) / 2;
    CGFloat windowY = (NSHeight(self.window.screen.frame) - NSHeight(self.window.frame)) / 2;
    [self.window setFrame:NSMakeRect(windowX, windowY, NSWidth(self.window.frame), NSHeight(self.window.frame)) display:YES];
    self.window.minSize = NSMakeSize(640, 480);
    self.window.backgroundColor = [NSColor colorWithRed:(0x0a / 255.f) green:(0x0a / 255.f) blue:(0x0a / 255.f) alpha:1];
    self.window.frameAutosaveName = @"window";
    self.window.delegate = self;

    // Create webview
    self.webview = [[WKWebView alloc] initWithFrame:[self.window.contentView bounds]];
    [self.webview setValue:@NO forKey:@"drawsBackground"];
    NSURL *url = [NSURL URLWithString:LocalizedString(@"webview_url")];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    [self.webview loadRequest:request];
    [self.window.contentView addSubview:self.webview];

    // Show window
    NSApp.activationPolicy = NSApplicationActivationPolicyRegular;
    [NSApp activateIgnoringOtherApps:YES];
    [self.window makeKeyAndOrderFront:nil];
}

- (BOOL)applicationShouldTerminateAfterLastWindowClosed:(NSApplication *)sender {
    return YES;
}

- (void)applicationWillTerminate:(NSNotification *)aNotification {}

- (void)windowDidResize:(NSNotification *)notification {
    self.webview.frame = [self.window.contentView bounds];
}

- (void)openAbout:(NSNotification *)aNotification {
    [NSApp orderFrontStandardAboutPanel:nil];
}

@end

int main(int argc, const char **argv) {
    NSApplication *app = [NSApplication sharedApplication];
    app.delegate = [AppDelegate new];
    return NSApplicationMain(argc, argv);
}
