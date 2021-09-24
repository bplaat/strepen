import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../models/product.dart';
import '../config.dart';

Future<bool> login(String email, String password) async {
  var response = await http.post(Uri.parse(API_URL + '/auth/login'), body: {
    'api_key': API_KEY,
    'email': email,
    'password': password
  });
  var data = json.decode(response.body);
  if (!data.containsKey('token')) {
    return false;
  }

  SharedPreferences prefs = await SharedPreferences.getInstance();
  await prefs.setString('token', data['token']);
  await prefs.setInt('user_id', data['user_id']);
  return true;
}

Future logout() async {
  SharedPreferences prefs = await SharedPreferences.getInstance();
  await http.get(Uri.parse(API_URL + '/auth/logout?api_key=' + API_KEY), headers: {
    'Authorization': 'Bearer ' + prefs.getString('token')!
  });

  await prefs.remove('token');
  await prefs.remove('user_id');
}
