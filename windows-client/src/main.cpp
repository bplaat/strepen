#define UNICODE
#define CINTERFACE
#include "WebView2.h"
#include "event.h"
#define WIN32_LEAN_AND_MEAN
#include <windows.h>
#include <shlobj.h>

#define ID_ICON 1

#define WINDOW_WIDTH 1024
#define WINDOW_HEIGHT 768
#define WINDOW_MIN_WIDTH 640
#define WINDOW_MIN_HEIGHT 480
#define WINDOW_STYLE WS_OVERLAPPEDWINDOW

LPCTSTR window_class_name = TEXT("strepen");
LPCTSTR window_title = TEXT("Strepen");
ICoreWebView2 *webview2 = nullptr;
ICoreWebView2Controller *controller = nullptr;

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

LRESULT WINAPI WndProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam) {
    if (msg == WM_SIZE) {
        ResizeBrowser(hwnd);
        return 0;
    }
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
    wc.hCursor = LoadCursor(nullptr, IDC_ARROW);
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
    AdjustWindowRectEx(&window_rect, WINDOW_STYLE, false, 0);

    HWND hwnd = CreateWindowEx(0, window_class_name, window_title,
        WINDOW_STYLE, window_rect.left, window_rect.top,
        window_rect.right - window_rect.left, window_rect.bottom - window_rect.top,
        HWND_DESKTOP, nullptr, hInstance, nullptr);
    ShowWindow(hwnd, nCmdShow);
    UpdateWindow(hwnd);

    // Set up event handlers
    EventHandler handler{};
    handler.EnvironmentCompleted = [&](HRESULT result, ICoreWebView2Environment* created_environment) {
        if (FAILED(result)) {
            FatalError(TEXT("Failed to create ICoreWebView2Environment"));
        }
        created_environment->lpVtbl->CreateCoreWebView2Controller(created_environment, hwnd, &handler);
        return S_OK;
    };
    handler.ControllerCompleted = [&](HRESULT result, ICoreWebView2Controller* new_controller) {
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
    };

    // Create webview2 stuff
    wchar_t appDataPath[MAX_PATH];
    SHGetFolderPath(NULL, CSIDL_LOCAL_APPDATA, NULL, 0, appDataPath);
    wcscat(appDataPath, L"\\strepen");
    HRESULT result = CreateCoreWebView2EnvironmentWithOptions(nullptr, appDataPath, nullptr, &handler);
    if (FAILED(result)) {
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
