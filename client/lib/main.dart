import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'services/storage_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  StorageService storage = await StorageService.getInstance();
  runApp(MaterialApp(
    title: 'Strepen',
    debugShowCheckedModeBanner: false,
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
    initialRoute: storage.prefs.getString('token') != null ? '/home' : '/login',
    routes: {
      '/home': (context) => HomeScreen(),
      '/login': (context) => LoginScreen()
    }
  ));
}
