import 'package:shared_preferences/shared_preferences.dart';

class StorageService {
  static StorageService? _instance;

  late SharedPreferences _prefs;

  static Future<StorageService> getInstance() async {
    if (_instance == null) {
      _instance = StorageService();
      _instance!._prefs = await SharedPreferences.getInstance();
    }
    return _instance!;
  }

  String? get token {
    return _prefs.getString('token');
  }

  Future setToken(String? token) async {
    if (token != null) {
      await _prefs.setString('token', token);
    } else {
      await _prefs.remove('token');
    }
  }

  int? get userId {
    return _prefs.getInt('user_id');
  }

  Future setUserId(int? userId) async {
    if (userId != null) {
      await _prefs.setInt('user_id', userId);
    } else {
      await _prefs.remove('user_id');
    }
  }
}
