#define UNICODE
#define WIN32_LEAN_AND_MEAN
#include <windows.h>
#include <shlobj.h>
#include "WebView2.h"

#define ID_ICON 1

#define WINDOW_WIDTH 1280
#define WINDOW_HEIGHT 720
#define WINDOW_MIN_WIDTH 640
#define WINDOW_MIN_HEIGHT 480
#define WINDOW_STYLE WS_OVERLAPPEDWINDOW

LPCTSTR window_class_name = TEXT("strepen");
LPCTSTR window_title = TEXT("Strepen");

HWND hwnd;
ICoreWebView2 *webview2 = NULL;
ICoreWebView2Controller *controller = NULL;

// Helper functions
void FatalError(LPCTSTR message) {
    MessageBox(HWND_DESKTOP, message, TEXT("Strepen Error"), MB_OK | MB_ICONSTOP);
    ExitProcess(1);
}

void ResizeBrowser(HWND hwnd) {
    if (!controller) return;
    RECT window_rect;
    GetClientRect(hwnd, &window_rect);
    controller->lpVtbl->put_Bounds(controller, window_rect);
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
    created_environment->lpVtbl->CreateCoreWebView2Controller(created_environment, hwnd, controllerCompletedHandler);
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
    controller->lpVtbl->AddRef(controller);
    controller->lpVtbl->get_CoreWebView2(controller, &webview2);
    webview2->lpVtbl->AddRef(webview2);
    webview2->lpVtbl->Navigate(webview2, L"https://stam.diekantankys.nl/");
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
    // Resize browser
    if (msg == WM_SIZE) {
        ResizeBrowser(hwnd);
        return 0;
    }

    // Set window min size
    if (msg == WM_GETMINMAXINFO) {
        RECT window_rect = { 0, 0, WINDOW_MIN_WIDTH, WINDOW_MIN_HEIGHT };
        AdjustWindowRectEx(&window_rect, WINDOW_STYLE, FALSE, 0);
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
    wc.hbrBackground = (HBRUSH)(COLOR_WINDOW + 1);
    wc.lpszClassName = window_class_name;
    wc.hIconSm = (HICON)LoadImage(hInstance, MAKEINTRESOURCE(ID_ICON), IMAGE_ICON, GetSystemMetrics(SM_CXSMICON), GetSystemMetrics(SM_CYSMICON), LR_DEFAULTCOLOR | LR_SHARED);
    RegisterClassEx(&wc);

    // Create centered window
    RECT window_rect;
    window_rect.left = (GetSystemMetrics(SM_CXSCREEN) - WINDOW_WIDTH) / 2;
    window_rect.top = (GetSystemMetrics(SM_CYSCREEN) - WINDOW_HEIGHT) / 2;
    window_rect.right = window_rect.left + WINDOW_WIDTH;
    window_rect.bottom = window_rect.top + WINDOW_HEIGHT;
    AdjustWindowRectEx(&window_rect, WINDOW_STYLE, FALSE, 0);

    hwnd = CreateWindowEx(0, window_class_name, window_title,
        WINDOW_STYLE, window_rect.left, window_rect.top,
        window_rect.right - window_rect.left, window_rect.bottom - window_rect.top,
        HWND_DESKTOP, NULL, hInstance, NULL);
    ShowWindow(hwnd, nCmdShow);
    UpdateWindow(hwnd);

    // Find app data path
    wchar_t appDataPath[MAX_PATH];
    SHGetFolderPath(NULL, CSIDL_LOCAL_APPDATA, NULL, 0, appDataPath);
    wcscat(appDataPath, L"\\strepen");

    // Init webview2 stuff
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
