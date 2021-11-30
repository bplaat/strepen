import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/settings_screen.dart';
import 'services/storage_service.dart';
import 'services/auth_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  StorageService storage = await StorageService.getInstance();
  if (storage.token != null && await AuthService.getInstance().user() == null) {
    await storage.setToken(null);
    await storage.setUserId(null);
  }

  runApp(MaterialApp(
    title: 'Strepen',
    debugShowCheckedModeBanner: false,

    localizationsDelegates: AppLocalizations.localizationsDelegates,
    supportedLocales: AppLocalizations.supportedLocales,

    theme: ThemeData(
      brightness: Brightness.light,
      primarySwatch: Colors.pink,
      accentColor: Colors.pink
    ),
    darkTheme: ThemeData(
      brightness: Brightness.dark,
      primarySwatch: Colors.pink,
      accentColor: Colors.pink
    ),

    initialRoute: storage.token != null ? '/home' : '/login',
    routes: {
      '/home': (context) => HomeScreen(),
      '/login': (context) => LoginScreen(),
      '/settings': (context) => SettingsScreen(),
    }
  ));
}
