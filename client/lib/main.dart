import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'services/storage_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  SharedPreferences storage = await StorageService.getInstance();
  runApp(MaterialApp(
    title: 'Strepen',
    debugShowCheckedModeBanner: false,
    theme: ThemeData(
      primarySwatch: Colors.pink
    ),
    initialRoute: storage.getString('token') != null ? '/' : '/login',
    routes: {
      '/': (context) => HomeScreen(),
      '/login': (context) => LoginScreen()
    }
  ));
}
