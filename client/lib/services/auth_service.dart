import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../models/user.dart';
import '../models/product.dart';
import '../config.dart';
import '../services/storage_service.dart';

class AuthService {
  static AuthService? _instance;

  User? _user;

  Future<User> user() async {
    if (_user == null) {
      StorageService storage = await StorageService.getInstance();
      var response = await http.get(Uri.parse(API_URL + '/users/' + storage.prefs.getInt('user_id').toString() + '?api_key=' + API_KEY), headers: {
          'Authorization': 'Bearer ' + storage.prefs.getString('token')!
      });
      _user = User.fromJson(json.decode(response.body));
    }
    return _user!;
  }

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

    StorageService storage = await StorageService.getInstance();
    await storage.prefs.setString('token', data['token']);
    await storage.prefs.setInt('user_id', data['user_id']);
    return true;
  }

  Future logout() async {
    StorageService storage = await StorageService.getInstance();
    await http.get(Uri.parse(API_URL + '/auth/logout?api_key=' + API_KEY), headers: {
      'Authorization': 'Bearer ' + storage.prefs.getString('token')!
    });

    await storage.prefs.remove('token');
    await storage.prefs.remove('user_id');
  }

  static AuthService getInstance() {
    if (_instance == null) {
      _instance = AuthService();
    }
    return _instance!;
  }
}
