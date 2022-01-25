#define UNICODE
#define WIN32_LEAN_AND_MEAN
#include <windows.h>
#include <shlobj.h>
#include <dwmapi.h>
#define COBJMACROS
#include "WebView2.h"

#ifndef DWMWA_USE_IMMERSIVE_DARK_MODE_BEFORE_20H1
    #define DWMWA_USE_IMMERSIVE_DARK_MODE_BEFORE_20H1 19
#endif
#ifndef DWMWA_USE_IMMERSIVE_DARK_MODE
    #define DWMWA_USE_IMMERSIVE_DARK_MODE 20
#endif

#define ID_ICON 1
#define ID_MENU_ABOUT 1001

#define WINDOW_WIDTH 1280
#define WINDOW_HEIGHT 720
#define WINDOW_MIN_WIDTH 640
#define WINDOW_MIN_HEIGHT 480
#define WINDOW_STYLE WS_OVERLAPPEDWINDOW

LPCTSTR window_class_name = TEXT("strepen");
LPCTSTR window_title = TEXT("Strepen");

HWND hwnd;
int window_dpi;
ICoreWebView2 *webview2 = NULL;
ICoreWebView2Controller *controller = NULL;

// Helper functions
int GetPrimaryDesktopDpi(void) {
    HDC hdc = GetDC(HWND_DESKTOP);
    int dpi = GetDeviceCaps(hdc, LOGPIXELSY);
    ReleaseDC(HWND_DESKTOP, hdc);
    return dpi;
}

typedef BOOL (STDMETHODCALLTYPE *_AdjustWindowRectExForDpi)(RECT *lpRect, DWORD dwStyle, BOOL bMenu, DWORD dwExStyle, UINT dpi);

BOOL AdjustWindowRectExForDpi(RECT *lpRect, DWORD dwStyle, BOOL bMenu, DWORD dwExStyle, UINT dpi) {
    HMODULE huser32 = LoadLibrary(TEXT("user32.dll"));
    _AdjustWindowRectExForDpi AdjustWindowRectExForDpi = (_AdjustWindowRectExForDpi)GetProcAddress(huser32, "AdjustWindowRectExForDpi");
    if (AdjustWindowRectExForDpi) {
        return AdjustWindowRectExForDpi(lpRect, dwStyle, bMenu, dwExStyle, dpi);
    }
    return AdjustWindowRectEx(lpRect, dwStyle, bMenu, dwExStyle);
}

void FatalError(LPCTSTR message) {
    MessageBox(HWND_DESKTOP, message, TEXT("Strepen Error"), MB_OK | MB_ICONSTOP);
    ExitProcess(1);
}

void ResizeBrowser(HWND hwnd) {
    if (!controller) return;
    RECT window_rect;
    GetClientRect(hwnd, &window_rect);
    ICoreWebView2Controller_put_Bounds(controller, window_rect);
}

// Forward interface reference
ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandlerVtbl EnvironmentCompletedHandlerVtbl;
ICoreWebView2CreateCoreWebView2ControllerCompletedHandlerVtbl ControllerCompletedHandlerVtbl;

// ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler
HRESULT STDMETHODCALLTYPE EnvironmentCompletedHandler_QueryInterface(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This, REFIID riid, void **ppvObject) {
    return E_NOINTERFACE;
}

ULONG STDMETHODCALLTYPE EnvironmentCompletedHandler_AddRef(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This) {
    return S_FALSE;
}

ULONG STDMETHODCALLTYPE EnvironmentCompletedHandler_Release(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This) {
    return S_FALSE;
}

HRESULT STDMETHODCALLTYPE EnvironmentCompletedHandler_Invoke(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This, HRESULT result, ICoreWebView2Environment *created_environment) {
    if (FAILED(result)) {
        FatalError(TEXT("Failed to create ICoreWebView2Environment"));
    }
    ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *controllerCompletedHandler = malloc(sizeof(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler));
    controllerCompletedHandler->lpVtbl = &ControllerCompletedHandlerVtbl;
    ICoreWebView2Environment_CreateCoreWebView2Controller(created_environment, hwnd, controllerCompletedHandler);
    return S_OK;
}

ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandlerVtbl EnvironmentCompletedHandlerVtbl = {
    EnvironmentCompletedHandler_QueryInterface,
    EnvironmentCompletedHandler_AddRef,
    EnvironmentCompletedHandler_Release,
    EnvironmentCompletedHandler_Invoke
};

// ICoreWebView2CreateCoreWebView2ControllerCompletedHandler
HRESULT STDMETHODCALLTYPE ControllerCompletedHandler_QueryInterface(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This, REFIID riid, void **ppvObject) {
    return E_NOINTERFACE;
}

ULONG STDMETHODCALLTYPE ControllerCompletedHandler_AddRef(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This) {
    return S_FALSE;
}

ULONG STDMETHODCALLTYPE ControllerCompletedHandler_Release(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This) {
    return S_FALSE;
}

HRESULT STDMETHODCALLTYPE ControllerCompletedHandler_Invoke(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This, HRESULT result, ICoreWebView2Controller *new_controller) {
    if (FAILED(result)) {
        FatalError(TEXT("Failed to create ICoreWebView2Controller"));
    }
    controller = new_controller;
    ICoreWebView2Controller_AddRef(controller);
    ICoreWebView2Controller_get_CoreWebView2(controller, &webview2);
    ICoreWebView2_AddRef(webview2);
    ICoreWebView2_Navigate(webview2, L"https://stam.diekantankys.nl/");
    ResizeBrowser(hwnd);
    return S_OK;
}

ICoreWebView2CreateCoreWebView2ControllerCompletedHandlerVtbl ControllerCompletedHandlerVtbl = {
    ControllerCompletedHandler_QueryInterface,
    ControllerCompletedHandler_AddRef,
    ControllerCompletedHandler_Release,
    ControllerCompletedHandler_Invoke,
};

// Window code
LRESULT WINAPI WndProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam) {
    // When window is created
    if (msg == WM_CREATE) {
        HMENU sysMenu = GetSystemMenu(hwnd, FALSE);
        InsertMenu(sysMenu, 5, MF_BYPOSITION | MF_SEPARATOR, 0, NULL);
        InsertMenu(sysMenu, 6, MF_BYPOSITION, ID_MENU_ABOUT, TEXT("About"));
        return 0;
    }

    // Menu commands
    if (msg == WM_SYSCOMMAND) {
        int id = LOWORD(wParam);
        if (id == ID_MENU_ABOUT) {
            MessageBox(hwnd, TEXT("Made by Bastiaan van der Plaat\nCopyright (c) 2021 PlaatSoft"), TEXT("About Strepen Windows App"), MB_OK | MB_ICONINFORMATION);
            return 0;
        }
    }

    // Handle dpi changes
    if (msg == WM_DPICHANGED) {
        window_dpi = HIWORD(wParam);
        RECT *window_rect = (RECT *)lParam;
        SetWindowPos(hwnd, NULL, window_rect->left, window_rect->top, window_rect->right - window_rect->left,
            window_rect->bottom - window_rect->top, SWP_NOZORDER | SWP_NOACTIVATE);
        return 0;
    }

    // Resize browser
    if (msg == WM_SIZE) {
        ResizeBrowser(hwnd);
        return 0;
    }

    // Set window min size
    if (msg == WM_GETMINMAXINFO) {
        RECT window_rect = { 0, 0, MulDiv(WINDOW_MIN_WIDTH, window_dpi, 96), MulDiv(WINDOW_MIN_HEIGHT, window_dpi, 96) };
        AdjustWindowRectExForDpi(&window_rect, WINDOW_STYLE, FALSE, 0, window_dpi);
        MINMAXINFO *minMaxInfo = (MINMAXINFO *)lParam;
        minMaxInfo->ptMinTrackSize.x = window_rect.right - window_rect.left;
        minMaxInfo->ptMinTrackSize.y = window_rect.bottom - window_rect.top;
        return 0;
    }

    // Quit application
    if (msg == WM_DESTROY) {
        PostQuitMessage(0);
        return 0;
    }

    return DefWindowProc(hwnd, msg, wParam, lParam);
}

INT WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, INT nCmdShow) {
    // Register window class
    WNDCLASSEX wc = {0};
    wc.cbSize = sizeof(WNDCLASSEX);
    wc.lpfnWndProc = WndProc;
    wc.hInstance = hInstance;
    wc.hIcon = (HICON)LoadImage(hInstance, MAKEINTRESOURCE(ID_ICON), IMAGE_ICON, 0, 0, LR_DEFAULTSIZE | LR_DEFAULTCOLOR | LR_SHARED);
    wc.hCursor = LoadCursor(NULL, IDC_ARROW);
    wc.hbrBackground = CreateSolidBrush(0x00a0a0a0a);
    wc.lpszClassName = window_class_name;
    wc.hIconSm = (HICON)LoadImage(hInstance, MAKEINTRESOURCE(ID_ICON), IMAGE_ICON, GetSystemMetrics(SM_CXSMICON), GetSystemMetrics(SM_CYSMICON), LR_DEFAULTCOLOR | LR_SHARED);
    RegisterClassEx(&wc);

    // Create centered window
    window_dpi = GetPrimaryDesktopDpi();
    int window_width = MulDiv(WINDOW_WIDTH, window_dpi, 96);
    int window_height = MulDiv(WINDOW_HEIGHT, window_dpi, 96);
    RECT window_rect;
    window_rect.left = (GetSystemMetrics(SM_CXSCREEN) - window_width) / 2;
    window_rect.top = (GetSystemMetrics(SM_CYSCREEN) - window_height) / 2;
    window_rect.right = window_rect.left + window_width;
    window_rect.bottom = window_rect.top + window_height;
    AdjustWindowRectExForDpi(&window_rect, WINDOW_STYLE, FALSE, 0, window_dpi);
    hwnd = CreateWindowEx(0, window_class_name, window_title,
        WINDOW_STYLE, window_rect.left, window_rect.top,
        window_rect.right - window_rect.left, window_rect.bottom - window_rect.top,
        HWND_DESKTOP, NULL, hInstance, NULL);

    // Enable dark window decoration
    BOOL useImmersiveDarkMode = TRUE;
    if (FAILED(DwmSetWindowAttribute(hwnd, DWMWA_USE_IMMERSIVE_DARK_MODE, &useImmersiveDarkMode, sizeof(BOOL)))) {
        DwmSetWindowAttribute(hwnd, DWMWA_USE_IMMERSIVE_DARK_MODE_BEFORE_20H1, &useImmersiveDarkMode, sizeof(BOOL));
    }

    // Show window
    ShowWindow(hwnd, nCmdShow);
    UpdateWindow(hwnd);

    // Find app data path
    wchar_t appDataPath[MAX_PATH];
    SHGetFolderPath(NULL, CSIDL_LOCAL_APPDATA, NULL, 0, appDataPath);
    wcscat(appDataPath, L"\\strepen");

    // Init webview2 stuff
    SetEnvironmentVariable(TEXT("WEBVIEW2_DEFAULT_BACKGROUND_COLOR"), TEXT("0a0a0a"));
    ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *environmentCompletedHandler = malloc(sizeof(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler));
    environmentCompletedHandler->lpVtbl = &EnvironmentCompletedHandlerVtbl;
    if (FAILED(CreateCoreWebView2EnvironmentWithOptions(NULL, appDataPath, NULL, environmentCompletedHandler))) {
        FatalError(TEXT("Failed to call CreateCoreWebView2EnvironmentWithOptions"));
    }

    // Main window event loop
    MSG message;
    while (GetMessage(&message, NULL, 0, 0) > 0) {
        TranslateMessage(&message);
        DispatchMessage(&message);
    }
    return message.wParam;
}
