import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'screens/loading_screen.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/settings_screen.dart';

class StrepenApp extends StatelessWidget {
  const StrepenApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Strepen',
      debugShowCheckedModeBanner: false,
      localizationsDelegates: AppLocalizations.localizationsDelegates,
      supportedLocales: AppLocalizations.supportedLocales,
      theme: ThemeData(
          colorScheme: ColorScheme.fromSwatch(primarySwatch: Colors.pink)
              .copyWith(secondary: Colors.pink)),
      darkTheme: ThemeData(
        colorScheme: ColorScheme.fromSwatch(primarySwatch: Colors.pink)
            .copyWith(secondary: Colors.pink, brightness: Brightness.dark),
        appBarTheme: const AppBarTheme(
            backgroundColor: Colors.pink, foregroundColor: Colors.white),
      ),
      initialRoute: '/loading',
      routes: {
        '/loading': (context) => const LoadingScreen(),
        '/home': (context) => const HomeScreen(),
        '/login': (context) => const LoginScreen(),
        '/settings': (context) => const SettingsScreen(),
      },
    );
  }
}

void main() {
  runApp(const StrepenApp());
}
