import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../models/product.dart';
import '../config.dart';
import '../services/storage_service.dart';

Future<bool> login({
  required String email,
  required String password
}) async {
  var response = await http.post(Uri.parse(API_URL + '/auth/login'), body: {
    'api_key': API_KEY,
    'email': email,
    'password': password
  });
  var data = json.decode(response.body);
  if (!data.containsKey('token')) {
    return false;
  }

  SharedPreferences storage = await StorageService.getInstance();
  await storage.setString('token', data['token']);
  await storage.setInt('user_id', data['user_id']);
  return true;
}

Future logout() async {
  SharedPreferences storage = await StorageService.getInstance();
  await http.get(Uri.parse(API_URL + '/auth/logout?api_key=' + API_KEY), headers: {
    'Authorization': 'Bearer ' + storage.getString('token')!
  });

  await storage.remove('token');
  await storage.remove('user_id');
}
