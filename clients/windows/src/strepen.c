#define UNICODE
#include <windows.h>
#include <shlobj.h>
#include <objbase.h>
#define COBJMACROS
#include <wincodec.h>
#include "WebView2.h"
#include "../res/resource.h"

#define ID_MENU_CLEAR_DATA 1
#define ID_MENU_ABOUT 2
#define WINDOW_STYLE WS_OVERLAPPEDWINDOW
HINSTANCE instance;
HWND window_hwnd;
UINT window_dpi;
ICoreWebView2 *webview2 = NULL;
ICoreWebView2Controller *controller = NULL;

#define ABOUT_WINDOW_STYLE (WS_OVERLAPPEDWINDOW ^ WS_THICKFRAME ^ WS_MAXIMIZEBOX)
UINT about_window_dpi;
HBITMAP about_image;

// Standard C Library wrapper functions
void *malloc(size_t size) {
    return HeapAlloc(GetProcessHeap(), 0, size);
}

void free(void *ptr) {
    HeapFree(GetProcessHeap(), 0, ptr);
}

size_t wcslen(const wchar_t *string) {
    wchar_t *c = (wchar_t *)string;
    while (*c != '\0') c++;
    return c - string;
}

wchar_t *wcscpy(wchar_t *dest, const wchar_t *src) {
    wchar_t *start = dest;
    while ((*dest++ = *src++) != '\0');
    return start;
}

wchar_t *wcscat(wchar_t *dest, const wchar_t *src) {
    wchar_t *start = dest;
    while (*dest != '\0') dest++;
    wcscpy(dest, src);
    return start;
}

// Helper functions
#define DWMWA_USE_IMMERSIVE_DARK_MODE_BEFORE_20H1 19
#define DWMWA_USE_IMMERSIVE_DARK_MODE 20
typedef HRESULT (STDMETHODCALLTYPE *_DwmSetWindowAttribute)(HWND hwnd, DWORD dwAttribute, LPCVOID pvAttribute, DWORD cbAttribute);

typedef HRESULT (STDMETHODCALLTYPE *_CreateCoreWebView2EnvironmentWithOptions)(PCWSTR browserExecutableFolder, PCWSTR userDataFolder, ICoreWebView2EnvironmentOptions *environmentOptions, ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *environmentCreatedHandler);

UINT GetPrimaryDesktopDpi(void) {
    HDC hdc = GetDC(HWND_DESKTOP);
    UINT dpi = GetDeviceCaps(hdc, LOGPIXELSY);
    ReleaseDC(HWND_DESKTOP, hdc);
    return dpi;
}

typedef BOOL (STDMETHODCALLTYPE *_AdjustWindowRectExForDpi)(RECT *lpRect, DWORD dwStyle, BOOL bMenu, DWORD dwExStyle, UINT dpi);

BOOL AdjustWindowRectExForDpi(RECT *lpRect, DWORD dwStyle, BOOL bMenu, DWORD dwExStyle, UINT dpi) {
    HMODULE huser32 = LoadLibrary(L"user32.dll");
    _AdjustWindowRectExForDpi AdjustWindowRectExForDpi = (_AdjustWindowRectExForDpi)GetProcAddress(huser32, "AdjustWindowRectExForDpi");
    if (AdjustWindowRectExForDpi) {
        return AdjustWindowRectExForDpi(lpRect, dwStyle, bMenu, dwExStyle, dpi);
    }
    return AdjustWindowRectEx(lpRect, dwStyle, bMenu, dwExStyle);
}

WINDOWPLACEMENT previousPlacement = { sizeof(previousPlacement) };

void SetWindowFullscreen(HWND hwnd, BOOL enabled) {
    DWORD dwStyle = GetWindowLong(hwnd, GWL_STYLE);
    if (enabled) {
        MONITORINFO monitorInfo = { sizeof(monitorInfo) };
        if (
            GetWindowPlacement(hwnd, &previousPlacement) &&
            GetMonitorInfo(MonitorFromWindow(hwnd, MONITOR_DEFAULTTOPRIMARY), &monitorInfo)
        ) {
            SetWindowLong(hwnd, GWL_STYLE, dwStyle & ~WS_OVERLAPPEDWINDOW);
            SetWindowPos(hwnd, HWND_TOP, monitorInfo.rcMonitor.left, monitorInfo.rcMonitor.top,
                monitorInfo.rcMonitor.right - monitorInfo.rcMonitor.left,
                monitorInfo.rcMonitor.bottom - monitorInfo.rcMonitor.top,
                SWP_NOOWNERZORDER | SWP_FRAMECHANGED);
        }
    } else {
        SetWindowLong(hwnd, GWL_STYLE, dwStyle | WS_OVERLAPPEDWINDOW);
        SetWindowPlacement(hwnd, &previousPlacement);
        SetWindowPos(hwnd, NULL, 0, 0, 0, 0, SWP_NOMOVE | SWP_NOSIZE | SWP_NOZORDER | SWP_NOOWNERZORDER | SWP_FRAMECHANGED);
    }
}

wchar_t *GetString(UINT id) {
    wchar_t *string;
    LoadString(instance, id, (wchar_t *)&string, 0);
    return string;
}

HBITMAP LoadPNGFromResource(wchar_t *type, wchar_t *name) {
    HRSRC hsrc = FindResourceW(NULL, name, type);

    CLSID CLSID_WICImagingFactory = { 0xcacaf262, 0x9370, 0x4615, { 0xa1, 0x3b, 0x9f, 0x55, 0x39, 0xda, 0x4c, 0x0a } };
    IID IID_IWICImagingFactory = { 0xec5ec8a9, 0xc395, 0x4314, { 0x9c, 0x77, 0x54, 0xd7, 0xa9, 0x35, 0xff, 0x70 } };
    IWICImagingFactory *wicFactory;
    CoCreateInstance(&CLSID_WICImagingFactory, NULL, CLSCTX_INPROC_SERVER, &IID_IWICImagingFactory, (void **)&wicFactory);

    IWICStream *wicStream;
    IWICImagingFactory_CreateStream(wicFactory, &wicStream);
    IWICStream_InitializeFromMemory(wicStream, LockResource(LoadResource(NULL, hsrc)), SizeofResource(NULL, hsrc));

    IWICBitmapDecoder *wicDecoder;
    IWICImagingFactory_CreateDecoderFromStream(wicFactory, (IStream *)wicStream, NULL, WICDecodeMetadataCacheOnDemand, &wicDecoder);

    IWICBitmapFrameDecode *wicFrame;
    IWICBitmapDecoder_GetFrame(wicDecoder, 0, &wicFrame);
    UINT width, height;
    IWICBitmapSource_GetSize(wicFrame, &width, &height);

    IWICFormatConverter *wicConverter;
    IWICImagingFactory_CreateFormatConverter(wicFactory, &wicConverter);
    GUID GUID_WICPixelFormat24bppBGR = { 0x6fddc324, 0x4e03, 0x4bfe, { 0xb1, 0x85, 0x3d, 0x77, 0x76, 0x8d, 0xc9, 0x0c } };
    IWICFormatConverter_Initialize(wicConverter, (IWICBitmapSource *)wicFrame, &GUID_WICPixelFormat24bppBGR, WICBitmapDitherTypeNone, NULL, 0, WICBitmapPaletteTypeCustom);

    IID IID_IWICBitmapSource = { 0x00000120, 0xa8f2, 0x4877, { 0xba, 0x0a, 0xfd, 0x2b, 0x66, 0x45, 0xfb, 0x94 } };
    IWICBitmapSource *wicConvertedSource;
    IWICFormatConverter_QueryInterface(wicConverter, &IID_IWICBitmapSource, (void **)&wicConvertedSource);

    HDC hdc = GetDC(HWND_DESKTOP);
    BITMAPINFO bitmapInfo = {0};
    bitmapInfo.bmiHeader.biSize = sizeof(BITMAPINFOHEADER);
    bitmapInfo.bmiHeader.biWidth = width;
    bitmapInfo.bmiHeader.biHeight = -height;
    bitmapInfo.bmiHeader.biPlanes = 1;
    bitmapInfo.bmiHeader.biBitCount = 24;
    bitmapInfo.bmiHeader.biCompression = BI_RGB;
    BYTE *bitmapBuffer = NULL;
    HBITMAP bitmap = CreateDIBSection(hdc, &bitmapInfo, DIB_RGB_COLORS, (void **)&bitmapBuffer, NULL, 0);
    IWICBitmapSource_CopyPixels(wicConvertedSource, NULL, width * 3, width * 3 * height, bitmapBuffer);
    ReleaseDC(HWND_DESKTOP, hdc);

    IWICBitmapSource_Release(wicConvertedSource);
    IWICFormatConverter_Release(wicConverter);
    IWICBitmapFrameDecode_Release(wicFrame);
    IWICBitmapFrameDecode_Release(wicDecoder);
    IWICStream_Release(wicStream);
    IWICImagingFactory_Release(wicFactory);
    return bitmap;
}

void FatalError(wchar_t *message) {
    MessageBox(HWND_DESKTOP, message, L"Strepen Error", MB_OK | MB_ICONSTOP);
    ExitProcess(1);
}

void GetAppVersion(UINT *version) {
    wchar_t file_name[MAX_PATH];
    GetModuleFileName(NULL, file_name, sizeof(file_name) / sizeof(wchar_t));
    DWORD version_info_size = GetFileVersionInfoSize(file_name, NULL);
    BYTE *version_info = malloc(version_info_size);
    GetFileVersionInfo(file_name, 0, version_info_size, version_info);

    VS_FIXEDFILEINFO *file_info;
    UINT file_info_size;
    VerQueryValue(version_info, L"\\", (LPVOID *)&file_info, &file_info_size);

    version[0] = HIWORD(file_info->dwProductVersionMS);
    version[1] = LOWORD(file_info->dwProductVersionMS);
    version[2] = HIWORD(file_info->dwProductVersionLS);
    version[3] = LOWORD(file_info->dwProductVersionLS);

    free(version_info);
}

// Browser functionality
void ResizeBrowser(HWND hwnd) {
    if (controller == NULL) return;
    RECT window_rect;
    GetClientRect(hwnd, &window_rect);
    ICoreWebView2Controller_put_Bounds(controller, window_rect);
}

BOOL HandleKeyDown(UINT key) {
    if (key == VK_ESCAPE) {
        if (!(GetWindowLong(window_hwnd, GWL_STYLE) & WS_OVERLAPPEDWINDOW)) {
            SetWindowFullscreen(window_hwnd, FALSE);
        }
        return TRUE;
    }
    if (key == VK_F11) {
        SetWindowFullscreen(window_hwnd, GetWindowLong(window_hwnd, GWL_STYLE) & WS_OVERLAPPEDWINDOW);
        return TRUE;
    }
    return FALSE;
}

// Default IUnknown method wrappers
HRESULT STDMETHODCALLTYPE Unknown_QueryInterface(IUnknown *This, REFIID riid, void **ppvObject) {
    return E_NOINTERFACE;
}

ULONG STDMETHODCALLTYPE Unknown_AddRef(IUnknown *This) {
    return E_NOTIMPL;
}

ULONG STDMETHODCALLTYPE Unknown_Release(IUnknown *This) {
    return E_NOTIMPL;
}

// Forward interface reference
ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandlerVtbl EnvironmentCompletedHandlerVtbl;
ICoreWebView2NewWindowRequestedEventHandlerVtbl NewWindowRequestedHandlerVtbl;
ICoreWebView2CreateCoreWebView2ControllerCompletedHandlerVtbl ControllerCompletedHandlerVtbl;

// ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler
HRESULT STDMETHODCALLTYPE EnvironmentCompletedHandler_Invoke(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This, HRESULT result, ICoreWebView2Environment *created_environment) {
    if (FAILED(result)) {
        FatalError(L"Failed to create ICoreWebView2Environment");
    }
    ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *controllerCompletedHandler = malloc(sizeof(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler));
    controllerCompletedHandler->lpVtbl = &ControllerCompletedHandlerVtbl;
    ICoreWebView2Environment_CreateCoreWebView2Controller(created_environment, window_hwnd, controllerCompletedHandler);
    return S_OK;
}

ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandlerVtbl EnvironmentCompletedHandlerVtbl = {
    (HRESULT (STDMETHODCALLTYPE *)(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This, REFIID riid, void **ppvObject))Unknown_QueryInterface,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This))Unknown_AddRef,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *This))Unknown_Release,
    EnvironmentCompletedHandler_Invoke
};

// ICoreWebView2AcceleratorKeyPressedEventHandler
HRESULT STDMETHODCALLTYPE AcceleratorKeyPressedHandler_Invoke(ICoreWebView2AcceleratorKeyPressedEventHandler *This, ICoreWebView2Controller *sender, ICoreWebView2AcceleratorKeyPressedEventArgs *args) {
    COREWEBVIEW2_KEY_EVENT_KIND state;
    ICoreWebView2AcceleratorKeyPressedEventArgs_get_KeyEventKind(args, &state);
    UINT key;
    ICoreWebView2AcceleratorKeyPressedEventArgs_get_VirtualKey(args, &key);
    if (state == COREWEBVIEW2_KEY_EVENT_KIND_KEY_DOWN && HandleKeyDown(key)) {
        ICoreWebView2AcceleratorKeyPressedEventArgs_put_Handled(args, TRUE);
    }
    return S_OK;
}

ICoreWebView2AcceleratorKeyPressedEventHandlerVtbl AcceleratorKeyPressedHandlerVtbl = {
    (HRESULT (STDMETHODCALLTYPE *)(ICoreWebView2AcceleratorKeyPressedEventHandler *This, REFIID riid, void **ppvObject))Unknown_QueryInterface,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2AcceleratorKeyPressedEventHandler *This))Unknown_AddRef,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2AcceleratorKeyPressedEventHandler *This))Unknown_Release,
    AcceleratorKeyPressedHandler_Invoke
};

// ICoreWebView2NewWindowRequestedEventHandler
HRESULT STDMETHODCALLTYPE NewWindowRequestedHandler_Invoke(ICoreWebView2NewWindowRequestedEventHandler *This, ICoreWebView2 *sender, ICoreWebView2NewWindowRequestedEventArgs *args) {
    ICoreWebView2NewWindowRequestedEventArgs_put_Handled(args, TRUE);
    wchar_t *url;
    ICoreWebView2NewWindowRequestedEventArgs_get_Uri(args, &url);
    ShellExecute(window_hwnd, L"OPEN", url, NULL, NULL, SW_SHOWNORMAL);
    return S_OK;
}

ICoreWebView2NewWindowRequestedEventHandlerVtbl NewWindowRequestedHandlerVtbl = {
    (HRESULT (STDMETHODCALLTYPE *)(ICoreWebView2NewWindowRequestedEventHandler *This, REFIID riid, void **ppvObject))Unknown_QueryInterface,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2NewWindowRequestedEventHandler *This))Unknown_AddRef,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2NewWindowRequestedEventHandler *This))Unknown_Release,
    NewWindowRequestedHandler_Invoke
};

// ICoreWebView2CreateCoreWebView2ControllerCompletedHandler
HRESULT STDMETHODCALLTYPE ControllerCompletedHandler_Invoke(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This, HRESULT result, ICoreWebView2Controller *new_controller) {
    if (FAILED(result)) {
        FatalError(L"Failed to create ICoreWebView2Controller");
    }
    controller = new_controller;
    ICoreWebView2Controller_AddRef(controller);
    ICoreWebView2Controller_get_CoreWebView2(controller, &webview2);

    ICoreWebView2AcceleratorKeyPressedEventHandler *newAcceleratorKeyPressedHandler = malloc(sizeof(ICoreWebView2AcceleratorKeyPressedEventHandler));
    newAcceleratorKeyPressedHandler->lpVtbl = &AcceleratorKeyPressedHandlerVtbl;
    ICoreWebView2Controller_add_AcceleratorKeyPressed(controller, newAcceleratorKeyPressedHandler, NULL);

    ICoreWebView2_13 *webview2_13;
    ICoreWebView2_QueryInterface(webview2, &IID_ICoreWebView2_13, (void **)&webview2_13);
    ICoreWebView2Profile *profile;
    ICoreWebView2_13_get_Profile(webview2_13, &profile);
    ICoreWebView2_13_Release(webview2_13);
    ICoreWebView2Profile_put_PreferredColorScheme(profile, COREWEBVIEW2_PREFERRED_COLOR_SCHEME_DARK);
    ICoreWebView2Profile_Release(profile);

    ICoreWebView2Settings *settings;
    ICoreWebView2_get_Settings(webview2, &settings);
    ICoreWebView2Settings_put_AreDefaultContextMenusEnabled(settings, FALSE);
    ICoreWebView2Settings_put_IsStatusBarEnabled(settings, FALSE);
    ICoreWebView2Settings_Release(settings);

    ICoreWebView2NewWindowRequestedEventHandler *newWindowRequestedHandler = malloc(sizeof(ICoreWebView2NewWindowRequestedEventHandler));
    newWindowRequestedHandler->lpVtbl = &NewWindowRequestedHandlerVtbl;
    ICoreWebView2_add_NewWindowRequested(webview2, newWindowRequestedHandler, NULL);

    ICoreWebView2_Navigate(webview2, GetString(ID_STRING_WEBVIEW_URL));
    ResizeBrowser(window_hwnd);
    return S_OK;
}

ICoreWebView2CreateCoreWebView2ControllerCompletedHandlerVtbl ControllerCompletedHandlerVtbl = {
    (HRESULT (STDMETHODCALLTYPE *)(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This, REFIID riid, void **ppvObject))Unknown_QueryInterface,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This))Unknown_AddRef,
    (ULONG (STDMETHODCALLTYPE *)(ICoreWebView2CreateCoreWebView2ControllerCompletedHandler *This))Unknown_Release,
    ControllerCompletedHandler_Invoke,
};

// About window code
LRESULT WINAPI AboutWndProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam) {
    // Load icon image PNG
    if (msg == WM_CREATE) {
        about_image = LoadPNGFromResource(L"IMAGE", (wchar_t *)ID_IMAGE_ICON);
        return 0;
    }

    // Handle dpi changes
    if (msg == WM_DPICHANGED) {
        about_window_dpi = HIWORD(wParam);
        RECT *window_rect = (RECT *)lParam;
        SetWindowPos(hwnd, NULL, window_rect->left, window_rect->top, window_rect->right - window_rect->left,
            window_rect->bottom - window_rect->top, SWP_NOZORDER | SWP_NOACTIVATE);
        return 0;
    }

    // Paint something nice
    if (msg == WM_ERASEBKGND) {
        return TRUE;
    }
    if (msg == WM_PAINT) {
        PAINTSTRUCT ps;
        HDC hdc = BeginPaint(hwnd, &ps);

        RECT clientRect;
        GetClientRect(hwnd, &clientRect);

        // Create back buffer
        HDC hdcBuffer = CreateCompatibleDC(hdc);
        SetBkMode(hdcBuffer, TRANSPARENT);
        HBITMAP bitmapBuffer = CreateCompatibleBitmap(hdc, clientRect.right, clientRect.bottom);
        SelectObject(hdcBuffer, bitmapBuffer);

        // Draw background color
        HBRUSH brush = CreateSolidBrush(0x0a0a0a);
        RECT rect = { 0, 0, clientRect.right, clientRect.bottom };
        FillRect(hdcBuffer, &rect, brush);
        DeleteObject(brush);

        // Draw about icon image
        HDC hdcImage = CreateCompatibleDC(hdcBuffer);
        SelectObject(hdcImage, about_image);
        SetStretchBltMode(hdcBuffer, STRETCH_HALFTONE);
        StretchBlt(hdcBuffer, MulDiv(16, about_window_dpi, 96), MulDiv(16 + 16, about_window_dpi, 96),
            MulDiv(128, about_window_dpi, 96), MulDiv(128, about_window_dpi, 96), hdcImage, 0, 0, 256, 256, SRCCOPY);
        DeleteDC(hdcImage);

        // Draw about title
        HFONT titleFont = CreateFont(MulDiv(32, about_window_dpi, 96), 0, 0, 0, FW_NORMAL, FALSE, FALSE, FALSE, ANSI_CHARSET,
            OUT_DEFAULT_PRECIS, CLIP_DEFAULT_PRECIS, CLEARTYPE_QUALITY, DEFAULT_PITCH | FF_DONTCARE, L"Segoe UI");
        SelectObject(hdcBuffer, titleFont);
        SetTextColor(hdcBuffer, 0xffffff);
        TextOutW(hdcBuffer, MulDiv(16 + 128 + 24, about_window_dpi, 96), MulDiv(32, about_window_dpi, 96), GetString(ID_STRING_ABOUT_TITLE), wcslen(GetString(ID_STRING_ABOUT_TITLE)));
        DeleteObject(titleFont);

        // Draw about text
        UINT app_version[4];
        GetAppVersion(app_version);
        wchar_t about_text[512];
        wsprintf(about_text, GetString(ID_STRING_ABOUT_TEXT_FORMAT), app_version[0], app_version[1], app_version[2], app_version[3]);

        HFONT textFont = CreateFont(MulDiv(20, about_window_dpi, 96), 0, 0, 0, FW_NORMAL, FALSE, FALSE, FALSE, ANSI_CHARSET,
            OUT_DEFAULT_PRECIS, CLIP_DEFAULT_PRECIS, CLEARTYPE_QUALITY, DEFAULT_PITCH | FF_DONTCARE, L"Segoe UI");
        SelectObject(hdcBuffer, textFont);
        int y =32 + 32 + 16;
        wchar_t *c = about_text;
        for (;;) {
            wchar_t *lineStart = c;
            while (*c != '\n' && *c != '\0') c++;
            TextOutW(hdcBuffer, MulDiv(16 + 128 + 24, about_window_dpi, 96), MulDiv(y, about_window_dpi, 96), lineStart, c - lineStart);
            if (*c == '\0') break;
            c++;
            y += 20 + 8;
        }
        DeleteObject(textFont);

        // Draw and delete back buffer
        BitBlt(hdc, 0, 0, clientRect.right, clientRect.bottom, hdcBuffer, 0, 0, SRCCOPY);
        DeleteObject(bitmapBuffer);
        DeleteDC(hdcBuffer);

        EndPaint(hwnd, &ps);
        return 0;
    }

    // Clean up
    if (msg == WM_DESTROY) {
        DeleteObject(about_image);
        return 0;
    }

    return DefWindowProc(hwnd, msg, wParam, lParam);
}

void OpenAboutWindow(void) {
    // Register window class
    WNDCLASSEX wc = {0};
    wc.cbSize = sizeof(WNDCLASSEX);
    wc.style = CS_HREDRAW | CS_VREDRAW;
    wc.lpfnWndProc = AboutWndProc;
    wc.hInstance = instance;
    wc.hIcon = (HICON)LoadImage(instance, MAKEINTRESOURCE(ID_ICON_APP), IMAGE_ICON, 0, 0, LR_DEFAULTSIZE | LR_DEFAULTCOLOR | LR_SHARED);
    wc.hCursor = LoadCursor(NULL, IDC_ARROW);
    wc.lpszClassName = L"strepen-about";
    wc.hIconSm = (HICON)LoadImage(instance, MAKEINTRESOURCE(ID_ICON_APP), IMAGE_ICON, GetSystemMetrics(SM_CXSMICON), GetSystemMetrics(SM_CYSMICON), LR_DEFAULTCOLOR | LR_SHARED);
    RegisterClassEx(&wc);

    // Create centered window
    about_window_dpi = GetPrimaryDesktopDpi();
    UINT window_width = MulDiv(500, about_window_dpi, 96);
    UINT window_height = MulDiv(196, about_window_dpi, 96);
    RECT window_rect;
    window_rect.left = (GetSystemMetrics(SM_CXSCREEN) - window_width) / 2;
    window_rect.top = (GetSystemMetrics(SM_CYSCREEN) - window_height) / 2;
    window_rect.right = window_rect.left + window_width;
    window_rect.bottom = window_rect.top + window_height;
    AdjustWindowRectExForDpi(&window_rect, ABOUT_WINDOW_STYLE, FALSE, 0, about_window_dpi);
    HWND hwnd = CreateWindowEx(0, wc.lpszClassName, GetString(ID_STRING_ABOUT_TITLE),
        ABOUT_WINDOW_STYLE, window_rect.left, window_rect.top,
        window_rect.right - window_rect.left, window_rect.bottom - window_rect.top,
        HWND_DESKTOP, NULL, instance, NULL);

    // Enable dark window decoration
    HMODULE hdwmapi = LoadLibrary(L"dwmapi.dll");
    _DwmSetWindowAttribute DwmSetWindowAttribute = (_DwmSetWindowAttribute)GetProcAddress(hdwmapi, "DwmSetWindowAttribute");
    if (DwmSetWindowAttribute != NULL) {
        BOOL enabled = TRUE;
        if (FAILED(DwmSetWindowAttribute(hwnd, DWMWA_USE_IMMERSIVE_DARK_MODE, &enabled, sizeof(BOOL)))) {
            DwmSetWindowAttribute(hwnd, DWMWA_USE_IMMERSIVE_DARK_MODE_BEFORE_20H1, &enabled, sizeof(BOOL));
        }
    }

    // Show window
    ShowWindow(hwnd, SW_SHOWDEFAULT);
    UpdateWindow(hwnd);
}

// Window code
LRESULT WINAPI WndProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam) {
    // When window is created
    if (msg == WM_CREATE) {
        HMENU sysMenu = GetSystemMenu(hwnd, FALSE);
        InsertMenu(sysMenu, 5, MF_BYPOSITION | MF_SEPARATOR, 0, NULL);
        InsertMenu(sysMenu, 6, MF_BYPOSITION, ID_MENU_CLEAR_DATA, GetString(ID_STRING_CLEAR_DATA_MENU));
        InsertMenu(sysMenu, 7, MF_BYPOSITION, ID_MENU_ABOUT, GetString(ID_STRING_ABOUT_MENU));
        return 0;
    }

    // Menu commands
    if (msg == WM_SYSCOMMAND) {
        UINT id = LOWORD(wParam);

        if (id == ID_MENU_CLEAR_DATA) {
            if (webview2 != NULL) {
                ICoreWebView2_13 *webview2_13;
                ICoreWebView2_QueryInterface(webview2, &IID_ICoreWebView2_13, (void **)&webview2_13);

                ICoreWebView2Profile *profile;
                ICoreWebView2_13_get_Profile(webview2_13, &profile);
                ICoreWebView2_13_Release(webview2_13);

                ICoreWebView2Profile2 *profile2;
                ICoreWebView2Profile2_QueryInterface(profile, &IID_ICoreWebView2Profile2, (void **)&profile2);
                ICoreWebView2Profile_Release(profile);
                ICoreWebView2Profile2_ClearBrowsingDataAll(profile2, NULL);
                ICoreWebView2Profile2_Release(profile2);

                ICoreWebView2_2_Reload(webview2);
            }
            return 0;
        }

        if (id == ID_MENU_ABOUT) {
            OpenAboutWindow();
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

    // Handle keydown messages
    if (msg == WM_KEYDOWN) {
        HandleKeyDown(wParam);
        return 0;
    }

    // Set window min size
    if (msg == WM_GETMINMAXINFO) {
        RECT window_rect = { 0, 0, MulDiv(640, window_dpi, 96), MulDiv(480, window_dpi, 96) };
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

void _start(void) {
    // Get instance
    instance = GetModuleHandle(NULL);

    // Register window class
    WNDCLASSEX wc = {0};
    wc.cbSize = sizeof(WNDCLASSEX);
    wc.lpfnWndProc = WndProc;
    wc.hInstance = instance;
    wc.hIcon = (HICON)LoadImage(instance, MAKEINTRESOURCE(ID_ICON_APP), IMAGE_ICON, 0, 0, LR_DEFAULTSIZE | LR_DEFAULTCOLOR | LR_SHARED);
    wc.hCursor = LoadCursor(NULL, IDC_ARROW);
    wc.hbrBackground = CreateSolidBrush(0x0a0a0a);
    wc.lpszClassName = L"strepen";
    wc.hIconSm = (HICON)LoadImage(instance, MAKEINTRESOURCE(ID_ICON_APP), IMAGE_ICON, GetSystemMetrics(SM_CXSMICON), GetSystemMetrics(SM_CYSMICON), LR_DEFAULTCOLOR | LR_SHARED);
    RegisterClassEx(&wc);

    // Create centered window
    window_dpi = GetPrimaryDesktopDpi();
    UINT window_width = MulDiv(1280, window_dpi, 96);
    UINT window_height = MulDiv(720, window_dpi, 96);
    RECT window_rect;
    window_rect.left = (GetSystemMetrics(SM_CXSCREEN) - window_width) / 2;
    window_rect.top = (GetSystemMetrics(SM_CYSCREEN) - window_height) / 2;
    window_rect.right = window_rect.left + window_width;
    window_rect.bottom = window_rect.top + window_height;
    AdjustWindowRectExForDpi(&window_rect, WINDOW_STYLE, FALSE, 0, window_dpi);
    window_hwnd = CreateWindowEx(0, wc.lpszClassName, GetString(ID_STRING_APP_NAME),
        WINDOW_STYLE, window_rect.left, window_rect.top,
        window_rect.right - window_rect.left, window_rect.bottom - window_rect.top,
        HWND_DESKTOP, NULL, instance, NULL);

    // Enable dark window decoration
    HMODULE hdwmapi = LoadLibrary(L"dwmapi.dll");
    _DwmSetWindowAttribute DwmSetWindowAttribute = (_DwmSetWindowAttribute)GetProcAddress(hdwmapi, "DwmSetWindowAttribute");
    if (DwmSetWindowAttribute != NULL) {
        BOOL enabled = TRUE;
        if (FAILED(DwmSetWindowAttribute(window_hwnd, DWMWA_USE_IMMERSIVE_DARK_MODE, &enabled, sizeof(BOOL)))) {
            DwmSetWindowAttribute(window_hwnd, DWMWA_USE_IMMERSIVE_DARK_MODE_BEFORE_20H1, &enabled, sizeof(BOOL));
        }
    }

    // Show window
    ShowWindow(window_hwnd, window_width >= GetSystemMetrics(SM_CXSCREEN) ? SW_SHOWMAXIMIZED : SW_SHOWDEFAULT);
    UpdateWindow(window_hwnd);

    // Load webview2 laoder
    HMODULE hWebview2Loader = LoadLibrary(L"WebView2Loader.dll");
    _CreateCoreWebView2EnvironmentWithOptions __CreateCoreWebView2EnvironmentWithOptions =
        (_CreateCoreWebView2EnvironmentWithOptions)GetProcAddress(hWebview2Loader, "CreateCoreWebView2EnvironmentWithOptions");
    if (__CreateCoreWebView2EnvironmentWithOptions != NULL) {
        // Find app data path
        wchar_t appDataPath[MAX_PATH];
        SHGetFolderPath(NULL, CSIDL_LOCAL_APPDATA, NULL, 0, appDataPath);
        wcscat(appDataPath, L"\\strepen");

        // Init webview2 stuff
        SetEnvironmentVariable(L"WEBVIEW2_DEFAULT_BACKGROUND_COLOR", L"0a0a0a");
        ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler *environmentCompletedHandler = malloc(sizeof(ICoreWebView2CreateCoreWebView2EnvironmentCompletedHandler));
        environmentCompletedHandler->lpVtbl = &EnvironmentCompletedHandlerVtbl;
        if (FAILED(__CreateCoreWebView2EnvironmentWithOptions(NULL, appDataPath, NULL, environmentCompletedHandler))) {
            FatalError(L"Failed to call CreateCoreWebView2EnvironmentWithOptions");
        }
    } else {
        FatalError(L"Failed to load WebView2Loader.dll");
    }

    // Main window event loop
    MSG message;
    while (GetMessage(&message, NULL, 0, 0) > 0) {
        TranslateMessage(&message);
        DispatchMessage(&message);
    }
    ExitProcess(message.wParam);
}
