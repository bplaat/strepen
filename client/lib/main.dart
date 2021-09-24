import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  SharedPreferences prefs = await SharedPreferences.getInstance();
  runApp(MaterialApp(
    title: 'Strepen',
    debugShowCheckedModeBanner: false,
    theme: ThemeData(
      primarySwatch: Colors.pink
    ),
    initialRoute: prefs.getString('token') != null ? '/' : '/login',
    routes: {
      '/': (context) => HomeScreen(),
      '/login': (context) => LoginScreen()
    }
  ));
}
