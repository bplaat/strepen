import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/home.dart';
import 'screens/login.dart';

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
      '/': (context) => Home(),
      '/login': (context) => Login()
    }
  ));
}
